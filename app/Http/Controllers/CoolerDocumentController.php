<?php

namespace App\Http\Controllers;

use App\CoolerDocument;
use App\Utils\CoolerDocumentService;
use Illuminate\Http\Request;

class CoolerDocumentController extends Controller
{
    protected CoolerDocumentService $docService;

    public function __construct(CoolerDocumentService $docService)
    {
        $this->docService = $docService;
    }

    /**
     * Download a document (with access log).
     */
    public function download(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.document.download')) {
            abort(403, 'Unauthorized action.');
        }

        $document = CoolerDocument::findOrFail($id);
        $path     = public_path($document->file_path);

        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }

        \Log::info("Document {$id} downloaded by user " . auth()->id());

        return response()->download($path, $document->file_name);
    }

    /**
     * Serve a document inline (for viewer).
     */
    public function view(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.document.view')) {
            abort(403, 'Unauthorized action.');
        }

        $document = CoolerDocument::findOrFail($id);
        $path     = public_path($document->file_path);

        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }

        return response()->file($path, [
            'Content-Type'        => $document->mime_type,
            'Content-Disposition' => 'inline; filename="' . $document->file_name . '"',
        ]);
    }

    /**
     * Verify a document.
     */
    public function verify(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.dealer.verify_docs')) {
            abort(403, 'Unauthorized action.');
        }

        $document = CoolerDocument::findOrFail($id);
        $this->docService->verify($document, $request->notes);

        return response()->json(['success' => true, 'status' => 'verified']);
    }

    /**
     * Reject a document.
     */
    public function reject(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.dealer.verify_docs')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate(['notes' => 'required|string']);

        $document = CoolerDocument::findOrFail($id);
        $this->docService->reject($document, $request->notes);

        return response()->json(['success' => true, 'status' => 'rejected']);
    }

    /**
     * Delete a document.
     */
    public function destroy(Request $request, int $id)
    {
        if (!auth()->user()->can('cooler.document.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $document = CoolerDocument::findOrFail($id);
        $this->docService->delete($document);

        return response()->json(['success' => true]);
    }

    /**
     * Get documents expiring soon (API for dashboard widget).
     */
    public function expiringSoon(Request $request)
    {
        if (!auth()->user()->can('cooler.compliance.view')) {
            abort(403, 'Unauthorized action.');
        }

        $docs = CoolerDocument::expiringSoon(30)
            ->with('documentable')
            ->get(['id', 'documentable_type', 'documentable_id', 'document_type', 'expires_at', 'status']);

        return response()->json($docs);
    }
}
