<?php
// Ensure consistent JSON responses
header('Content-Type: application/json; charset=utf-8');
include('../connection/connection.php');

// Accept both 'sid' and 'student_id' to be tolerant with callers
$student_id_raw = $_POST['sid'] ?? ($_POST['student_id'] ?? null);
$new_section_raw = $_POST['new_section'] ?? null;
$academic_year = isset($_POST['academic_year']) ? trim($_POST['academic_year']) : null;

// Basic validation and normalization
$student_id = $student_id_raw !== null ? (int)$student_id_raw : 0;
$new_section = $new_section_raw !== null ? (int)$new_section_raw : 0;

if($student_id <= 0 || $new_section <= 0 || !$academic_year){
    echo json_encode(["status"=>"error","message"=>"Missing or invalid parameters."]);
    exit;
}

// --- Get old assignment if exists ---
$academic_year_esc = mysqli_real_escape_string($conn, $academic_year);
$check_sql = "
    SELECT a.asid, a.student_id, a.section_id, a.academic_year, s.section_name AS old_section_name
    FROM assign_student a
    LEFT JOIN sections s ON a.section_id = s.cid
    WHERE a.student_id = {$student_id} AND a.academic_year = '{$academic_year_esc}'
    LIMIT 1
";
$check = mysqli_query($conn, $check_sql);

if($check && mysqli_num_rows($check) > 0){
    $row = mysqli_fetch_assoc($check);
    $assign_id      = (int)$row['asid'];
    $old_section_id = (int)$row['section_id'];
    $old_section    = $row['old_section_name'];

    // Update existing assignment
    $update = mysqli_query($conn, "UPDATE assign_student SET section_id = {$new_section} WHERE asid = {$assign_id}");

    if($update){
        // get new section name
        $newSecRes = mysqli_query($conn, "SELECT section_name FROM sections WHERE cid = {$new_section} LIMIT 1");
        $newSecRow = $newSecRes ? mysqli_fetch_assoc($newSecRes) : null;

        echo json_encode([
            "status"        => "success",
            "message"       => "Student successfully transferred.",
            "student_id"    => $student_id,
            "academic_year" => $academic_year,
            "old_section_id"=> $old_section_id,
            "old_section"   => $old_section,
            "new_section_id"=> $new_section,
            "new_section"   => $newSecRow['section_name'] ?? null
        ]);
    } else {
        echo json_encode(["status"=>"error","message"=>"Could not update section."]);
    }

} else {
    // No record found → Insert new assignment
    $insert_sql = "INSERT INTO assign_student (student_id, section_id, academic_year) VALUES ({$student_id}, {$new_section}, '{$academic_year_esc}')";
    $insert = mysqli_query($conn, $insert_sql);

    if($insert){
        $newSecRes = mysqli_query($conn, "SELECT section_name FROM sections WHERE cid = {$new_section} LIMIT 1");
        $newSecRow = $newSecRes ? mysqli_fetch_assoc($newSecRes) : null;

        echo json_encode([
            "status"        => "success",
            "message"       => "Student assigned to section for this academic year.",
            "student_id"    => $student_id,
            "academic_year" => $academic_year,
            "old_section_id"=> null,
            "old_section"   => null,
            "new_section_id"=> $new_section,
            "new_section"   => $newSecRow['section_name'] ?? null
        ]);
    } else {
        echo json_encode(["status"=>"error","message"=>"Could not insert record."]);
    }
}
?>
