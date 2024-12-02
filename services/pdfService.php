<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload fra Composer

use setasign\Fpdi\Fpdi;

class PDFService {
    public function generateBookingPDF($data, $type) {
        // Opprett FPDI-objektet
        $pdf = new Fpdi(); // FPDI utvider FPDF-klassen

        // Legg til en ny side
        $pdf->AddPage();

        // Last inn PDF-mal
        $templatePath = __DIR__ . '/../uploads/templates/bookingmal.pdf'; // Bruk absolutt sti
        if (!file_exists($templatePath)) {
            die("Error: Template file not found at path: $templatePath");
        }

        // Sett malen til PDF-en
        $pdf->setSourceFile($templatePath);
        $templateId = $pdf->importPage(1);
        if (!$templateId) {
            die("Error: Unable to import page from template.");
        }
        $pdf->useTemplate($templateId);

        // Sett font og tekstfarge
        $pdf->SetFont('Helvetica', '', 12); // Set font til Helvetica
        $pdf->SetTextColor(0, 0, 0); // Set farge til svart

        // Start y verdier
        $y = 68;
        $diff = 11.8;

        // Definer nøklene for feltene
        $keys = [
            'bookingNumber',
            'reservationDate',
            'customerName',
            'phone',
            'email',
            'checkInDate',
            'checkOutDate',
            'roomType',
            'guestCount',
            'pricePerNight',
            'nights',
            'mva',
            'totalPrice',
            'status'
        ];

        // Kombiner dataene med status
        $data['status'] = 'Bekreftet';

        // Fyll ut feltene i PDF-en med en løkke
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                // Beregn bredden på teksten
                $text = $data[$key];
                // Legg til "NOK" for spesifikke felt som representerer penger
                if (in_array($key, ['pricePerNight', 'mva', 'totalPrice'])) {
                    $text .= ' NOK';
                }

                // Beregn posisjon for å sentrere teksten
                $pageWidth = $pdf->GetPageWidth();
                $textWidth = $pdf->GetStringWidth($text);
                $x = (($pageWidth - $textWidth) / 2)-2; // Sentraliser teksten på siden
                
                // Plasser og skriv teksten
                $pdf->SetXY($x, $y);
                $pdf->Write(0, $text);
                $y += $diff;
            }
        }

        // Lagre den genererte PDF-en
        $outputPath = __DIR__ . '/../uploads/' . $type . '_' . $data['bookingNumber'] . '.pdf'; // Bruk absolutt sti

        // Prøv å generere PDF-en
        try {
            $pdf->Output('F', $outputPath);
            echo "PDF generert: <a href='../uploads/" . $type . '_' . $data['bookingNumber'] . ".pdf'>Klikk her for å åpne PDF</a>";
        } catch (Exception $e) {
            die("Error: Unable to generate PDF. " . $e->getMessage());
        }
    }
}

// Test funksjonaliteten
$data = [
    'bookingNumber' => '123456',
    'reservationDate' => '01-12-2024',
    'customerName' => 'Ola Nordmann',
    'phone' => '99999999',
    'email' => 'ola@nordmann.no',
    'checkInDate' => '01-12-2024',
    'checkOutDate' => '03-12-2024',
    'roomType' => 'Dobbeltrom',
    'guestCount' => '2',
    'pricePerNight' => '1000',
    'nights' => '2',
    'mva' => '500',
    'totalPrice' => '2500'
];

$pdfService = new PDFService();
$pdfService->generateBookingPDF($data, 'ordrebekreftelse');
?>
