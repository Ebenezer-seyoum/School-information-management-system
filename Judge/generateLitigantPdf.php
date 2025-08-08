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
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            width: 95%;
            margin: 20px auto;
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        .header h2 {
            margin: 0 auto;
            padding: 12px 25px;
            background-color: #2c3e50;
            color: white;
            border-radius: 6px;
            display: inline-block;
            font-size: 20px;
            font-weight: 600;
        }
        .case-id {
            font-size: 16px;
            color: #3498db;
            margin-top: 8px;
            font-weight: bold;
        }
        .litigant-grid {
            width: 100%;
            margin: 0 auto;
            border-collapse: separate;
            border-spacing: 12px;
        }
        .litigant-card {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            vertical-align: top;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .litigant-details {
            line-height: 1.6;
            font-size: 12px;
        }
        .detail-label {
            font-weight: 600;
            color: #2c3e50;
            min-width: 110px;
            display: inline-block;
        }
        .section-title {
            font-size: 14px;
            color: #2c3e50;
            margin: 20px 0 12px 0;
            padding-bottom: 6px;
            border-bottom: 1px dashed #ddd;
        }
        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
            font-size: 11px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Advanced Customer Detail Information Form</h2>
            <div class="case-id">Case ID: '.htmlspecialchars($case_id).'</div>
        </div>';

$counter = 0;
$html .= '<table class="litigant-grid" align="center">';

foreach ($caseRows as $case) {
    $region = getRegionById($case["region"]);
    $zone = getZoneById($case["zone"]);
    $woreda = getWoredaById($case["woreda"]);

    if ($counter % 3 == 0) {
        $html .= '<tr>';
    }

    $html .= '<td width="33%" class="litigant-card">
        <div class="litigant-details">
            <div class="section-title">Litigant Information</div>
            <div><span class="detail-label">Full Name:</span> '.htmlspecialchars($case['first_name']).' '.htmlspecialchars($case['father_name']).' '.htmlspecialchars($case['grandfather_name']).'</div>
            <div><span class="detail-label">Gender:</span> '.htmlspecialchars($case['gender']).'</div>
            <div><span class="detail-label">Contact:</span> '.htmlspecialchars($case['phone']).'</div>
            <div><span class="detail-label">Email:</span> '.htmlspecialchars($case['email']).'</div>
            
            <div class="section-title">Location Details</div>
            <div><span class="detail-label">Region:</span> '.htmlspecialchars($region).'</div>
            <div><span class="detail-label">Zone:</span> '.htmlspecialchars($zone).'</div>
            <div><span class="detail-label">Woreda:</span> '.htmlspecialchars($woreda).'</div>
            <div><span class="detail-label">Wogen Type:</span> '.htmlspecialchars($case['wogen']).'</div>
            
            <div class="section-title">Case Details</div>
            <div><span class="detail-label">Litigant Type:</span> '.htmlspecialchars($case['litigant_type']).'</div>
            <div><span class="detail-label">Argument Money:</span> '.htmlspecialchars($case['argument_money']).' ETB</div>
            <div><span class="detail-label">Judgement Money:</span> '.htmlspecialchars($case['judgement_money']).' ETB</div>
        </div>
    </td>';

    $counter++;
    if ($counter % 3 == 0) {
        $html .= '</tr>';
    }
}

// Complete the last row if needed
if ($counter % 3 != 0) {
    $html .= str_repeat('<td></td>', 3 - ($counter % 3));
    $html .= '</tr>';
}

$html .= '</table>';

$html .= '
        <div class="footer">
            <p>Generated on '.date('F j, Y').' | Court Management System</p>
        </div>
    </div>
</body>
</html>';




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
