<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class RequestDetailsPdfExporter
{
    public function export(Transaction $transaction): string
    {
        $html = View::make('landlord.partials.request-details-pdf', [
            'transaction' => $transaction,
        ])->render();

        $tempDir = storage_path('app/mpdf-temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 14,
            'margin_right' => 14,
            'margin_top' => 14,
            'margin_bottom' => 14,
            'tempDir' => $tempDir,
            'default_font' => 'dejavusans',
        ]);

        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->WriteHTML($html);

        $binary = $mpdf->Output('', Destination::STRING_RETURN);

        if (!is_string($binary) || !str_starts_with($binary, '%PDF')) {
            throw new \RuntimeException('PDF generation failed: invalid output.');
        }

        $path = tempnam(sys_get_temp_dir(), 'ghorfa_request_');
        if ($path === false) {
            throw new \RuntimeException('Could not create temporary file for export.');
        }

        $pdfPath = $path . '.pdf';
        @unlink($path);

        if (file_put_contents($pdfPath, $binary) === false) {
            throw new \RuntimeException('Could not write PDF file.');
        }

        return $pdfPath;
    }
}
