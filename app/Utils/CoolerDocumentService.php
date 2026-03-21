<?php

namespace App\Utils;

use App\CoolerDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CoolerDocumentService
{
    /**
     * Upload and store a cooler document.
     *
     * @param  UploadedFile  $file
     * @param  mixed         $documentable  CoolerDealer|CoolerAgreement|CoolerRetrieval
     * @param  string        $documentType
     * @param  array         $options       expires_at, metadata, etc.
     */
    public function upload(UploadedFile $file, $documentable, string $documentType, array $options = []): CoolerDocument
    {
        $this->validateFile($file, $documentType);

        $subPath      = $this->buildStoragePath($documentable, $documentType);
        $fileName     = $this->sanitizeFilename($file->getClientOriginalName());
        $storedName   = time() . '_' . $fileName;
        $fullDir      = public_path($subPath);

        if (!is_dir($fullDir)) {
            mkdir($fullDir, 0755, true);
        }

        $mimeType = $file->getMimeType();

        // Process image: auto-rotate, compress
        if (str_starts_with($mimeType, 'image/') && $mimeType !== 'image/heic') {
            $this->processAndSaveImage($file, $fullDir . '/' . $storedName);
        } else {
            $file->move($fullDir, $storedName);
        }

        $filePath      = $subPath . '/' . $storedName;
        $thumbnailPath = null;

        // Generate thumbnail for images and PDFs
        if (str_starts_with($mimeType, 'image/')) {
            $thumbnailPath = $this->generateThumbnail($fullDir . '/' . $storedName, $subPath);
        }

        return CoolerDocument::create([
            'documentable_type' => get_class($documentable),
            'documentable_id'   => $documentable->id,
            'document_type'     => $documentType,
            'file_path'         => $filePath,
            'file_name'         => $fileName,
            'file_size'         => filesize(public_path($filePath)),
            'mime_type'         => $mimeType,
            'thumbnail_path'    => $thumbnailPath,
            'uploaded_by'       => auth()->id(),
            'expires_at'        => $options['expires_at'] ?? null,
            'status'            => 'pending',
            'metadata'          => $options['metadata'] ?? null,
        ]);
    }

    /**
     * Validate uploaded file against per-type rules.
     */
    protected function validateFile(UploadedFile $file, string $documentType): void
    {
        $maxSizes = [
            'passport_photo'         => 2048,  // 2MB
            'retrieval_photo_before' => 10240,
            'retrieval_photo_during' => 10240,
            'retrieval_photo_after'  => 10240,
        ];

        $maxKb    = $maxSizes[$documentType] ?? 5120; // default 5MB
        $maxBytes = $maxKb * 1024;

        if ($file->getSize() > $maxBytes) {
            throw new \InvalidArgumentException("File exceeds maximum size of " . ($maxKb / 1024) . "MB. Please compress and try again.");
        }

        $photoTypes = ['retrieval_photo_before', 'retrieval_photo_during', 'retrieval_photo_after', 'passport_photo'];
        $imageOnly  = in_array($documentType, $photoTypes) && $documentType !== 'acknowledgement_signature';

        $allowedMimes = $imageOnly
            ? ['image/jpeg', 'image/png', 'image/heic', 'image/webp']
            : ['image/jpeg', 'image/png', 'image/heic', 'application/pdf'];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException("Invalid file format. Only PDF, JPG, and PNG files are accepted for this document.");
        }
    }

    /**
     * Build the storage sub-path based on the documentable type.
     */
    protected function buildStoragePath($documentable, string $documentType): string
    {
        $class = class_basename($documentable);

        $map = [
            'CoolerDealer'    => "uploads/cooler/dealers/{$documentable->id}",
            'CoolerAgreement' => "uploads/cooler/agreements/{$documentable->id}",
            'CoolerRetrieval' => "uploads/cooler/retrievals/{$documentable->id}/photos",
        ];

        return $map[$class] ?? "uploads/cooler/misc/{$documentable->id}";
    }

    /**
     * Process image: auto-rotate from EXIF, compress if large.
     */
    protected function processAndSaveImage(UploadedFile $file, string $destination): void
    {
        if (!extension_loaded('gd')) {
            $file->move(dirname($destination), basename($destination));
            return;
        }

        $mimeType = $file->getMimeType();
        $source   = $file->getPathname();

        $image = match (true) {
            str_contains($mimeType, 'jpeg') => imagecreatefromjpeg($source),
            str_contains($mimeType, 'png')  => imagecreatefrompng($source),
            default                          => null,
        };

        if (!$image) {
            $file->move(dirname($destination), basename($destination));
            return;
        }

        // Auto-rotate from EXIF
        if (function_exists('exif_read_data') && str_contains($mimeType, 'jpeg')) {
            $exif        = @exif_read_data($source);
            $orientation = $exif['Orientation'] ?? 1;
            $image       = match ($orientation) {
                3       => imagerotate($image, 180, 0),
                6       => imagerotate($image, -90, 0),
                8       => imagerotate($image, 90, 0),
                default => $image,
            };
        }

        imagejpeg($image, $destination, 85);
        imagedestroy($image);
    }

    /**
     * Generate a thumbnail (max 300px wide) for an image.
     */
    protected function generateThumbnail(string $imagePath, string $subPath): ?string
    {
        if (!extension_loaded('gd') || !file_exists($imagePath)) {
            return null;
        }

        $mimeType = mime_content_type($imagePath);
        $src      = match (true) {
            str_contains($mimeType, 'jpeg') => imagecreatefromjpeg($imagePath),
            str_contains($mimeType, 'png')  => imagecreatefrompng($imagePath),
            default                          => null,
        };

        if (!$src) {
            return null;
        }

        [$origW, $origH] = getimagesize($imagePath);
        $maxW  = 300;
        $ratio = $origW > $maxW ? $maxW / $origW : 1;
        $newW  = (int) ($origW * $ratio);
        $newH  = (int) ($origH * $ratio);

        $thumb    = imagecreatetruecolor($newW, $newH);
        imagecopyresampled($thumb, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        $thumbDir  = public_path($subPath . '/thumbnails');
        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0755, true);
        }

        $thumbName = 'thumb_' . basename($imagePath);
        imagejpeg($thumb, $thumbDir . '/' . $thumbName, 75);
        imagedestroy($src);
        imagedestroy($thumb);

        return $subPath . '/thumbnails/' . $thumbName;
    }

    /**
     * Verify a document.
     */
    public function verify(CoolerDocument $document, ?string $notes = null): void
    {
        $document->update([
            'status'             => 'verified',
            'verified_at'        => now(),
            'verified_by'        => auth()->id(),
            'verification_notes' => $notes,
        ]);
    }

    /**
     * Reject a document.
     */
    public function reject(CoolerDocument $document, string $notes): void
    {
        $document->update([
            'status'             => 'rejected',
            'verified_at'        => now(),
            'verified_by'        => auth()->id(),
            'verification_notes' => $notes,
        ]);
    }

    /**
     * Delete a document and its files.
     */
    public function delete(CoolerDocument $document): void
    {
        $filePath      = public_path($document->file_path);
        $thumbnailPath = $document->thumbnail_path ? public_path($document->thumbnail_path) : null;

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        if ($thumbnailPath && file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }

        $document->delete();
    }

    protected function sanitizeFilename(string $name): string
    {
        $name = pathinfo($name, PATHINFO_FILENAME);
        $ext  = pathinfo($name, PATHINFO_EXTENSION) ?: '';
        return Str::slug($name) . ($ext ? ".{$ext}" : '');
    }
}
