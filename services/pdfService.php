<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload fra Composer

use setasign\Fpdi\Fpdi;

class PDFService {
    public function generateBookingPDF($data, $type) {
        $pdf = new Fpdi();
        $pdf->AddPage();
        $templatePath = __DIR__ . '/../uploads/templates/bookingmal.pdf';


        // Last inn PDF-mal
        $templatePath = __DIR__ . '/../uploads/templates/bookingmal.pdf'; // Bruk absolutt sti
        
        // Last inn PDF-mal
        $templatePath = __DIR__ . '/../uploads/templates/bookingmal.pdf'; // Bruk absolutt sti
        if (!file_exists($templatePath)) {
            die("Error: Template file not found at path: $templatePath");
        }

        $pdf->setSourceFile($templatePath);
        $templateId = $pdf->importPage(1);
        $pdf->useTemplate($templateId);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->SetTextColor(0, 0, 0);

        $y = 68;
        $diff = 11.8;

        // Data som skal skrives ut
        $pdfData = [
            'bookingNumber' => $data['bookingNumber'],
            'reservationDate' => date('d-m-Y'),
            'customerName' => $data['customerName'],
            'phone' => '99999999', // Default phone
            'email' => $data['email'],
            'checkInDate' => $data['checkInDate'],
            'checkOutDate' => $data['checkOutDate'],
            'roomType' => $data['roomType'],
            'guestCount' => $data['guestCount'],
            'pricePerNight' => $data['pricePerNight'],
            'nights' => $data['nights'],
            'mva' => $data['mva'],
            'totalPrice' => $data['totalPrice'],
            'status' => 'Bekreftet'
        ];

        // Skriv ut data til PDF
        foreach ($pdfData as $key => $value) {
            $text = $value;
            if (in_array($key, ['pricePerNight', 'mva', 'totalPrice'])) {
                $text .= ' NOK';
            }
            
            $pageWidth = $pdf->GetPageWidth();
            $textWidth = $pdf->GetStringWidth($text);
            $x = (($pageWidth - $textWidth) / 2)-2;
            
            $pdf->SetXY($x, $y);
            $pdf->Write(0, $text);
            $y += $diff;
        }

        // Lagre den genererte PDF-en
        $outputPath = __DIR__ . '/../uploads/' . $type . '_' . $data['bookingNumber'] . '.pdf'; // Bruk absolutt sti

        try {
            $pdf->Output('F', $outputPath);
            return true;
        } catch (Exception $e) {
            return false;
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
