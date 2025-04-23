<?php
require('fpdf/fpdf.php');

header('Content-Type: application/pdf');

// Get results from POST request
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['results']) || !isset($input['target'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No results or target provided']);
    exit;
}

class SecurityReport extends FPDF {
    function Header() {
        // Page header
        $this->SetFont('Arial', 'B', 24);
        $this->Cell(0, 20, 'Synthèse de l\'audit de sécurité', 0, 1, 'C');
        $this->Ln(10);
    }
    
    function Footer() {
        // Page footer
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    function ChapterTitle($title) {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, $title, 0, 1, 'L');
        $this->Ln(5);
    }
    
    function ChapterBody($body) {
        $this->SetFont('Courier', '', 11);
        $lines = explode("\n", $body);
        foreach($lines as $line) {
            $this->MultiCell(0, 5, $line);
        }
        $this->Ln(10);
    }
}

// Create PDF document
$pdf = new SecurityReport();
$pdf->AliasNbPages();
$pdf->AddPage();

// Add cover page
$pdf->SetFont('Arial', 'B', 24);
$pdf->Cell(0, 20, 'Synthèse de l\'audit de sécurité', 0, 1, 'C');
$pdf->Ln(20);
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(0, 10, 'Cible : ' . $input['target'], 0, 1, 'C');
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Date : ' . date('Y-m-d H:i:s'), 0, 1, 'C');
$pdf->Ln(20);
$pdf->AddPage();

// Parse results and add to PDF
$results = explode("\n\n", $input['results']);
$current_tool = '';

foreach ($results as $result) {
    if (preg_match('/=== (.*?) Results ===/', $result, $matches)) {
        $current_tool = $matches[1];
        $pdf->ChapterTitle($current_tool . ' Results');
        $content = trim(str_replace("=== {$current_tool} Results ===", '', $result));
        $pdf->ChapterBody($content);
    }
}

// Output PDF
$pdf->Output('D', 'security_audit_report.pdf'); 