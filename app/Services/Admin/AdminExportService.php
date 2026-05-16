<?php

namespace App\Services\Admin;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class AdminExportService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function downloadPdf(string $view, array $data, string $filename): Response
    {
        $pdf = Pdf::loadView($view, $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }
}
