<?php
include('../connection/connection.php');

$selected_class = $_POST['class_type'] ?? '';  // e.g., 9, 10, 11S, 11N, 12S, 12N
$academic_year  = $_POST['academic_year'] ?? '';

$response = [];

if($selected_class && $academic_year){
    $year_safe = mysqli_real_escape_string($conn, $academic_year);

    $grade = substr($selected_class, 0, 2);   // '9', '10', '11', '12'
    $type  = substr($selected_class, 2);      // '', 'S', 'N'

    // Build strict filters: match grade by section_name prefix and class_type precisely
    $grade_only = mysqli_real_escape_string($conn, $grade);
    $grade_like = $grade_only . '%';
    $whereParts = ["s.section_name LIKE '".$grade_like."'"];

    if ($grade === '11' || $grade === '12') {
        if ($type === 'S') {
            $whereParts[] = "UPPER(s.class_type) = 'SOCIAL'";
        } elseif ($type === 'N') {
            $whereParts[] = "UPPER(s.class_type) = 'NATURAL'";
        }
        // if no S/N provided, show both SOCIAL and NATURAL of that grade
    } else {
        // For 9/10, restrict to GENERAL
        $whereParts[] = "UPPER(s.class_type) = 'GENERAL'";
    }

    $where = implode(' AND ', $whereParts);

    $sections_q = mysqli_query($conn, "
        SELECT s.cid, s.section_name, s.class_type, ai.instructor_id AS assigned_instructor
        FROM sections s
        LEFT JOIN assign_instructor ai 
            ON ai.section_id = s.cid AND ai.academic_year = '$year_safe'
        WHERE $where
        ORDER BY s.section_name ASC
    ");

    while($sec = mysqli_fetch_assoc($sections_q)){
        // Normalize class_type for UI display
        $sec['class_type'] = ucfirst(strtolower($sec['class_type']));
        $response[] = $sec;
    }
}

echo json_encode($response);
