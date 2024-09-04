<?php
// app/Services/CarteFideliteService.php
namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class CarteFideliteService
{
    public function generateCarteFidelite($user)
    {
        // Configure Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Load HTML content
        $html = view('emails.carte_fidelite', compact('user'))->render();

        // Load HTML into Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the PDF (first pass)
        $dompdf->render();

        // Output the generated PDF (second pass)
        $output = $dompdf->output();
        $filePath = storage_path('app/public/carte_fidelite_' . $user->id . '.pdf');
        file_put_contents($filePath, $output);

        return $filePath;
    }
}
