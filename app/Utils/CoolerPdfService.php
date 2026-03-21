<?php

namespace App\Utils;

use App\CoolerAgreement;
use App\CoolerRetrieval;

class CoolerPdfService
{
    /**
     * Generate the loan agreement PDF HTML.
     */
    public function generateAgreementHtml(CoolerAgreement $agreement): string
    {
        $dealer = $agreement->dealer;
        $cooler = $agreement->cooler;

        // Embed passport photo if available
        $photoDoc = $dealer->documents()->where('document_type', 'passport_photo')->first();
        $photoHtml = '';
        if ($photoDoc && file_exists(public_path($photoDoc->file_path))) {
            $photoData = base64_encode(file_get_contents(public_path($photoDoc->file_path)));
            $photoHtml = "<img src=\"data:{$photoDoc->mime_type};base64,{$photoData}\" style=\"width:100px;height:100px;object-fit:cover;\" />";
        }

        // Signature image helper
        $sigImg = function (?string $path, string $label) {
            if ($path && file_exists(public_path($path))) {
                $data = base64_encode(file_get_contents(public_path($path)));
                return "<img src=\"data:image/png;base64,{$data}\" style=\"max-width:200px;max-height:80px;\" /><br><small>{$label}</small>";
            }
            return "<div style=\"border-bottom:1px solid #000;width:200px;height:60px;\"></div><small>{$label}</small>";
        };

        ob_start(); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #000; }
  h1 { text-align:center; font-size:14px; }
  h2 { font-size:12px; text-transform:uppercase; border-bottom:2px solid #000; margin-top:20px; }
  table { width:100%; border-collapse:collapse; margin:10px 0; }
  table td, table th { border:1px solid #999; padding:5px 8px; vertical-align:top; }
  table th { background:#eee; font-weight:bold; }
  .sig-row td { border:none; text-align:center; width:25%; }
  .header-logo { text-align:center; margin-bottom:10px; }
  .clause { margin:8px 0; }
  .clause-num { font-weight:bold; }
</style>
</head>
<body>

<div class="header-logo">
  <h1>SBC KENYA LIMITED<br>COOLER LOAN AGREEMENT</h1>
  <p>This agreement is made on <strong><?= $agreement->agreement_date->format('d F Y') ?></strong></p>
</div>

<h2>Schedule 1 — Dealer Details</h2>
<table>
  <tr><th style="width:30%">Field</th><th>Details</th><th style="width:120px">Photo</th></tr>
  <tr>
    <td>Full Name</td>
    <td><?= e($dealer->name) ?></td>
    <td rowspan="8" style="text-align:center"><?= $photoHtml ?: '&nbsp;' ?></td>
  </tr>
  <tr><td>ID Number</td><td><?= e($dealer->id_number) ?></td></tr>
  <tr><td>KRA PIN</td><td><?= e($dealer->kra_pin) ?></td></tr>
  <tr><td>Phone</td><td><?= e($dealer->phone) ?></td></tr>
  <tr><td>Postal Address</td><td><?= e($dealer->postal_address) ?></td></tr>
  <tr><td>Outlet Name</td><td><?= e($dealer->outlet_name) ?></td></tr>
  <tr><td>Channel</td><td><?= ucfirst($dealer->channel) ?></td></tr>
  <tr><td>Location</td><td><?= e($dealer->full_address) ?></td></tr>
</table>

<h2>Cooler Asset Details</h2>
<table>
  <tr><th>Asset Type</th><th>Asset Number</th><th>Serial Number</th><th>Cooler Tag</th><th>Replacement Value</th></tr>
  <tr>
    <td><?= e($cooler->asset_type) ?></td>
    <td><?= e($cooler->asset_number) ?></td>
    <td><?= e($cooler->serial_number) ?></td>
    <td><?= e($cooler->cooler_tag) ?></td>
    <td>KES <?= number_format($cooler->replacement_value, 2) ?></td>
  </tr>
</table>

<h2>Agreement Terms</h2>

<?php
        $clauses = [
            '1' => 'SBC Kenya Limited ("the Company") agrees to place the refrigerator cooler described in Schedule 1 above with the dealer named herein ("the Dealer") on a free loan basis.',
            '4' => 'The Dealer shall stock the cooler with SBC Kenya Limited products only (Exclusivity Clause). No competitor products shall be placed in the cooler at any time.',
            '5' => 'The cooler shall be placed in a prominent position within the Dealer\'s premises, visible to customers. The Company\'s trademarks and branding shall not be covered, defaced, or obscured in any way.',
            '8' => 'The Dealer agrees to maintain an average monthly sales volume of KES ' . number_format($agreement->sales_volume_target ?? 0, 2) . ' as agreed with the Company.',
            '14'=> 'The Dealer shall maintain the cooler stocked to 100% of its capacity at all times.',
            '15'=> 'The Dealer shall maintain minimum stock levels as advised by the Company\'s sales representative at all times.',
            '16'=> 'The Company reserves the right to retrieve the cooler at any time without prior notice if any of these terms are breached, or at the Company\'s sole discretion.',
            '17'=> 'The Dealer is fully liable for the replacement value of the cooler (as stated above) in case of theft, loss, or damage caused by the Dealer\'s negligence.',
            '18'=> 'This agreement shall not be transferred to a third party. Any change in business ownership automatically terminates this agreement and requires the Dealer to notify the Company immediately.',
        ];

        foreach ($clauses as $num => $text): ?>
<div class="clause"><span class="clause-num"><?= $num ?>.</span> <?= e($text) ?></div>
<?php endforeach; ?>

<h2>Signatures</h2>
<table>
  <tr class="sig-row">
    <td><?= $sigImg($agreement->company_signature_path, "Company Legal Team<br>" . e($agreement->company_signatory_name ?? '')) ?></td>
    <td><?= $sigImg($agreement->rsm_tsm_signature_path, "RSM/TSM<br>" . e($agreement->rsm_tsm_signatory_name ?? '')) ?></td>
    <td><?= $sigImg($agreement->dealer_signature_path, "Dealer<br>" . e($agreement->dealer_signatory_name ?? '')) ?></td>
    <td><?= $sigImg($agreement->distributor_signature_path, "Distributor/Stockist<br>" . e($agreement->distributor_signatory_name ?? '')) ?></td>
  </tr>
</table>

<p style="text-align:center;margin-top:20px;font-size:10px;">
  Generated by FlowState POS — SBC Kenya Limited &copy; <?= date('Y') ?>
</p>
</body>
</html>
        <?php
        return ob_get_clean();
    }

    /**
     * Generate retrieval letter HTML.
     */
    public function generateRetrievalLetterHtml(CoolerRetrieval $retrieval): string
    {
        $dealer = $retrieval->dealer;
        $cooler = $retrieval->cooler;

        ob_start(); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size:11px; color:#000; }
  h1 { text-align:center; font-size:13px; }
  table { width:100%; border-collapse:collapse; margin:10px 0; }
  table td, table th { border:1px solid #999; padding:5px 8px; }
  table th { background:#eee; }
  .sig-row td { border:none; text-align:center; }
  .ref { text-align:right; font-size:10px; }
</style>
</head>
<body>

<div class="ref">Ref No: CMR-<?= str_pad($retrieval->id, 5, '0', STR_PAD_LEFT) ?><br>Date: <?= $retrieval->retrieval_date->format('d/m/Y') ?></div>

<h1>SBC KENYA LIMITED<br>COOLER RETRIEVAL NOTICE</h1>

<p>To: <strong><?= e($dealer->name) ?></strong><br>
Outlet: <strong><?= e($dealer->outlet_name) ?></strong><br>
Location: <strong><?= e($dealer->full_address) ?></strong><br>
Phone: <strong><?= e($dealer->phone) ?></strong></p>

<p>Dear <?= e($dealer->name) ?>,</p>

<p>
This is to notify you that SBC Kenya Limited is retrieving the following cooler asset from your premises as of the date above.
</p>

<h2 style="font-size:12px;border-bottom:1px solid #000;">Cooler Details</h2>
<table>
  <tr><th>Serial Number</th><th>Asset Number</th><th>Asset Type</th><th>Cooler Tag</th></tr>
  <tr>
    <td><?= e($cooler->serial_number) ?></td>
    <td><?= e($cooler->asset_number) ?></td>
    <td><?= e($cooler->asset_type) ?></td>
    <td><?= e($cooler->cooler_tag) ?></td>
  </tr>
</table>

<h2 style="font-size:12px;border-bottom:1px solid #000;">Reason for Retrieval</h2>
<p>&#9745; <?= e($retrieval->reason_label) ?><?= $retrieval->reason_notes ? ': ' . e($retrieval->reason_notes) : '' ?></p>

<p>
The Company reserves the right to retrieve the cooler at any time as per the terms of the loan agreement signed by you.
Please ensure full cooperation with the retrieval team.
</p>

<h2 style="font-size:12px;border-bottom:1px solid #000;">Authorized Staff</h2>
<table>
  <tr><th>Name</th><th>ID Number</th><th>Contact</th></tr>
  <tr>
    <td><?= e($retrieval->authorized_staff_name) ?></td>
    <td><?= e($retrieval->authorized_staff_id_no) ?></td>
    <td><?= e($retrieval->authorized_staff_tel) ?></td>
  </tr>
</table>

<h2 style="font-size:12px;border-bottom:1px solid #000;">Acknowledgement</h2>
<p>The dealer acknowledges receipt of this notice and confirmation of cooler retrieval.</p>

<table>
  <tr class="sig-row">
    <td style="width:50%;padding-top:40px;">
      <?php if ($retrieval->customer_signature_path && file_exists(public_path($retrieval->customer_signature_path))): ?>
        <?php $data = base64_encode(file_get_contents(public_path($retrieval->customer_signature_path))); ?>
        <img src="data:image/png;base64,<?= $data ?>" style="max-width:180px;max-height:70px;" /><br>
      <?php else: ?>
        <div style="border-bottom:1px solid #000;height:50px;"></div>
      <?php endif; ?>
      Dealer Signature &amp; Date
    </td>
    <td style="width:50%;padding-top:40px;">
      <div style="border-bottom:1px solid #000;height:50px;"></div>
      Company Representative Signature &amp; Date
    </td>
  </tr>
</table>

<p style="text-align:center;margin-top:20px;font-size:10px;">
  SBC Kenya Limited — FlowState POS &copy; <?= date('Y') ?>
</p>
</body>
</html>
        <?php
        return ob_get_clean();
    }

    /**
     * Save HTML to PDF using dompdf (if installed) or write HTML file as fallback.
     *
     * @return string  public-relative path to the saved PDF
     */
    public function savePdf(string $html, string $outputPath): string
    {
        $fullPath = public_path($outputPath);
        $dir      = dirname($fullPath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (class_exists(\Dompdf\Dompdf::class)) {
            $dompdf = new \Dompdf\Dompdf(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true]);
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            file_put_contents($fullPath, $dompdf->output());
        } else {
            // Fallback: save as HTML (still downloadable/printable)
            $htmlPath = str_replace('.pdf', '.html', $fullPath);
            file_put_contents($htmlPath, $html);
            return str_replace('.pdf', '.html', $outputPath);
        }

        return $outputPath;
    }
}
