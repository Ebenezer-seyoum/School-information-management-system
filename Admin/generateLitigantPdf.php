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
    $pdf->SetTitle('Litigant ID Cards');
    $pdf->SetMargins(10, 10, 10); 
    $pdf->AddPage();
    $pdf->SetFont('times', '', 10); 

    $html = "<h2 style='text-align:center; font-family:Arial, sans-serif; margin-bottom:20px;'>Litigant Personal Information Cards (Case ID: $case_id)</h2>";

    $html .= '<table cellspacing="20" cellpadding="0" style="width:100%; margin:0 auto;">';
    $counter = 0;

    foreach ($caseRows as $case) {
        $region = getRegionById($case["region"]);
        $zone = getZoneById($case["zone"]);
        $woreda = getWoredaById($case["woreda"]);

        if ($counter % 2 == 0) {
            $html .= "<tr>";
        }

        $html .= "
        <td width='50%' style='vertical-align:top;'>
            <div style='
                background-color: #e6f2ff;
                border-radius: 10px;
                padding: 20px;
                font-family: Arial, sans-serif;
                height: 320px;
                position: relative;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                margin: 0 auto;
                width: 90%;
            '>
                <div style='
                    background-color: #0056b3;
                    color: white;
                    padding: 10px;
                    text-align: center;
                    border-radius: 5px;
                    margin-bottom: 15px;
                    font-weight: bold;
                    font-size: 16px;
                '>
                    Personal Information Card
                </div>
                
                <div style='
                    font-size: 13px;
                    line-height: 2;
                    padding: 0 10px;
                '>
                    <div style='display: flex; margin-bottom: 5px;'>
                        <div style='width: 40%; font-weight: bold;'>First Name:</div>
                        <div style='width: 60%; border-bottom: 1px solid #ccc;'>" . htmlspecialchars($case['first_name']) . "</div>
                    </div>
                    <div style='display: flex; margin-bottom: 5px;'>
                        <div style='width: 40%; font-weight: bold;'>Father Name:</div>
                        <div style='width: 60%; border-bottom: 1px solid #ccc;'>" . htmlspecialchars($case['father_name']) . "</div>
                    </div>
                    <div style='display: flex; margin-bottom: 5px;'>
                        <div style='width: 40%; font-weight: bold;'>Grandfather Name:</div>
                        <div style='width: 60%; border-bottom: 1px solid #ccc;'>" . htmlspecialchars($case['grandfather_name']) . "</div>
                    </div>
                    <div style='display: flex; margin-bottom: 5px;'>
                        <div style='width: 40%; font-weight: bold;'>Address:</div>
                        <div style='width: 60%; border-bottom: 1px solid #ccc;'>" . htmlspecialchars($region . ', ' . $zone . ', ' . $woreda) . "</div>
                    </div>
                    <div style='display: flex; margin-bottom: 5px;'>
                        <div style='width: 40%; font-weight: bold;'>Phone:</div>
                        <div style='width: 60%; border-bottom: 1px solid #ccc;'>" . htmlspecialchars($case['phone']) . "</div>
                    </div>
                    <div style='display: flex; margin-bottom: 5px;'>
                        <div style='width: 40%; font-weight: bold;'>Email:</div>
                        <div style='width: 60%; border-bottom: 1px solid #ccc;'>" . htmlspecialchars($case['gmail']) . "</div>
                    </div>
                    <div style='display: flex; margin-bottom: 5px;'>
                        <div style='width: 40%; font-weight: bold;'>Litigant Type:</div>
                        <div style='width: 60%; border-bottom: 1px solid #ccc;'>" . htmlspecialchars($case['litigant_type']) . "</div>
                    </div>
                    <div style='display: flex; margin-bottom: 5px;'>
                        <div style='width: 40%; font-weight: bold;'>Gender:</div>
                        <div style='width: 60%; border-bottom: 1px solid #ccc;'>" . htmlspecialchars($case['gender']) . "</div>
                    </div>
                </div>
                
                <div style='
                    position: absolute;
                    bottom: 20px;
                    right: 20px;
                    font-family: Arial, sans-serif;
                    font-size: 14px;
                    font-weight: bold;
                    text-align: right;
                    color: #d9534f;
                '>
                    IN CASE OF<br>EMERGENCY
                </div>
            </div>
        </td>";

        $counter++;

        if ($counter % 2 == 0) {
            $html .= "</tr>";
        }
    }

    if ($counter % 2 != 0) {
        $html .= "<td></td></tr>";
    }

    $html .= "</table>";

    ob_end_clean(); 
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output("Litigant_Case_$case_id.pdf", 'I');
    exit;
} else {
    echo "Invalid request.";
}
?>