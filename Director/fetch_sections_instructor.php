<?php
include('../connection/connection.php');

$selected_class = $_POST['class_type'] ?? '';  // e.g., 9, 10, 11S, 11N, 12S, 12N
$academic_year  = $_POST['academic_year'] ?? '';

$response = [];

if($selected_class && $academic_year){
    $year_safe = mysqli_real_escape_string($conn, $academic_year);

    $grade = substr($selected_class, 0, 2);   // '9', '10', '11', '12'
    $type  = substr($selected_class, 2);      // '', 'S', 'N'

    // For 11/12 social/natural, filter by class_type column OR section_name pattern
    if($grade == '11' || $grade == '12'){
        if($type == 'S'){
            $where = "(s.class_type='social' OR s.section_name LIKE '".$grade."S%')";
        } elseif($type == 'N'){
            $where = "(s.class_type='natural' OR s.section_name LIKE '".$grade."N%')";
        } else {
            $where = "s.section_name LIKE '".$grade."%'";
        }
    } else {
        // For 9/10 general
        $where = "s.class_type='general' AND s.section_name LIKE '".$grade."%'";
    }

    $sections_q = mysqli_query($conn, "
        SELECT s.cid, s.section_name, ai.instructor_id AS assigned_instructor
        FROM sections s
        LEFT JOIN assign_instructor ai 
            ON ai.section_id = s.cid AND ai.academic_year = '$year_safe'
        WHERE $where
        ORDER BY s.section_name ASC
    ");

    while($sec = mysqli_fetch_assoc($sections_q)){
        $response[] = $sec;
    }
}

echo json_encode($response);
