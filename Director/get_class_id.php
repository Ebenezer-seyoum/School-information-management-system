<?php
include('../connection/connection.php');
header('Content-Type: application/json');

$section_id = isset($_POST['section_id']) ? (int)$_POST['section_id'] : 0;
$academic_year = isset($_POST['academic_year']) ? trim($_POST['academic_year']) : '';

if (!$section_id || $academic_year === '') {
    echo json_encode(['success' => false, 'message' => 'Missing section or year']);
    exit;
}

$yearEsc = mysqli_real_escape_string($conn, $academic_year);
$sql = "SELECT ai.hid, s.section_name, s.class_type
        FROM assign_instructor ai
        JOIN sections s ON s.cid = ai.section_id
        WHERE ai.section_id = $section_id AND ai.academic_year = '$yearEsc'
        LIMIT 1";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);

if ($row) {
    echo json_encode(['success' => true, 'class_id' => (int)$row['hid'], 'section_name' => $row['section_name'], 'class_type' => $row['class_type']]);
} else {
    echo json_encode(['success' => false, 'message' => 'No instructor assignment found for this section and year']);
}
?>