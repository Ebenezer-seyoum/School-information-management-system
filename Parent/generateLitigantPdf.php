<?php
ob_start();
require_once('../tcpdf/tcpdf.php');
include '../connection/connection.php';
include '../connection/function.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kid'])) {
    $case_id = $_POST['case_id']; 
    $kid = $_POST['kid'];
    $caseRows = getAllCasesByCid($kid); 
    if (!$caseRows) {
        die("Invalid case data.");
    }
   // PDF setup
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Court System');
$pdf->SetTitle('Litigant Case Information');
$pdf->SetMargins(10, 10, 10); 
$pdf->AddPage();
$pdf->SetFont('times', '', 11); 
// Start HTML content
$html = "<h2 style='text-align:center;'>Litigant Information (Case ID: $case_id)</h2><br><br>";
$counter = 0;
$html .= '<table cellspacing="8" cellpadding="0" style="width:100%;">'; 
foreach ($caseRows as $case) {
    $region = getRegionById($case["region"]);
    $zone = getZoneById($case["zone"]);
    $woreda = getWoredaById($case["woreda"]);
    // Start a new row every 4 cards
    if ($counter % 4 == 0) {
        $html .= '<tr>';
    }
    // Card HTML
    $html .= '<td width="25%" style="border: 1px solid #000; padding: 10px; border-radius: 6px;">
        <div style="line-height: 1.4;">
            <strong>First Name:</strong> ' . htmlspecialchars($case['first_name']) . '<br>
            <strong>Father Name:</strong> ' . htmlspecialchars($case['father_name']) . '<br>
            <strong>Grandfather:</strong> ' . htmlspecialchars($case['grandfather_name']) . '<br>
            <strong>Gender:</strong> ' . htmlspecialchars($case['gender']) . '<br>
            <strong>Gmail:</strong> ' . htmlspecialchars($case['gmail']) . '<br>
            <strong>Phone:</strong> ' . htmlspecialchars($case['phone']) . '<br>
            <strong>Region:</strong> ' . htmlspecialchars($region) . '<br>
            <strong>Zone:</strong> ' . htmlspecialchars($zone) . '<br>
            <strong>Woreda:</strong> ' . htmlspecialchars($woreda) . '<br>
            <strong>Wogen:</strong> ' . htmlspecialchars($case['wogen']) . '<br>
            <strong>Argument Money:</strong> ' . htmlspecialchars($case['argument_money']) . '<br>
            <strong>Judgement Money:</strong> ' . htmlspecialchars($case['judgement_money']) . '<br>
            <strong>Litigant_Type:</strong> ' . htmlspecialchars($case['litigant_type']) . '
        </div>
    </td>';
    $counter++;
    // End row after 4 cards
    if ($counter % 4 == 0) {
        $html .= '</tr>';
    }
}
// Fill remaining columns if the last row is incomplete
if ($counter % 4 != 0) {
    $html .= str_repeat('<td></td>', 4 - ($counter % 4));
    $html .= '</tr>';
}
$html .= '</table>';
    // Clean output buffer BEFORE sending PDF
    ob_end_clean();
    // Write to PDF
    $pdf->writeHTML($html, true, false, true, false, '');
    // Output PDF
    $pdf->Output("Litigant_Case_$case_id.pdf", 'I');
    exit;

} else {
    echo "Invalid request.";
}
