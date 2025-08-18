<?php
session_start();
include('../connection/connection.php');

$student_school_id = $_SESSION['sid'] ?? ''; // school ID string
$academic_year     = mysqli_real_escape_string($conn, $_POST['academic_year'] ?? '');
$section_id        = (int)($_POST['section_id'] ?? 0);
$semester          = (int)($_POST['semester'] ?? 1);

$response = [];

// --- 1. Fetch classes assigned to this student ---
if($student_school_id && $academic_year && !$section_id){
    $q = mysqli_query($conn, "
        SELECT s.cid, s.section_name, s.class_type, c.sid AS internal_sid
        FROM assign_student ast
        JOIN sections s ON ast.section_id = s.cid
        JOIN students c ON ast.student_id = c.sid
        WHERE c.student_id = '$student_school_id'
          AND ast.academic_year = '$academic_year'
        ORDER BY s.class_type, s.section_name ASC
    ") or die(mysqli_error($conn));

    while($row = mysqli_fetch_assoc($q)){
        $response[] = [
            'cid'          => $row['cid'],
            'section_name' => $row['section_name'],
            'sid'          => $student_school_id,  // school ID
            'class_type'   => $row['class_type'],
            'internal_sid' => $row['internal_sid'] // internal PK for marks query
        ];
    }
}

// --- 2. Fetch subjects & marks for a specific class ---
if($section_id && $academic_year){
    // First, get internal student ID if not provided
    $student_internal_id = 0;
    $q_stu = mysqli_query($conn, "SELECT sid FROM students WHERE student_id = '$student_school_id'");
    if($q_stu && mysqli_num_rows($q_stu) > 0){
        $row = mysqli_fetch_assoc($q_stu);
        $student_internal_id = $row['sid'];
    }

    if($student_internal_id){
        $sql = "SELECT sub.suid, sub.subject_name,
                       COALESCE(m.result, 'N/A') AS marks
                FROM curriculum_subjects cs
                JOIN subjects sub ON cs.subject_id = sub.suid
                LEFT JOIN marks m 
                  ON m.subject_id = sub.suid
                 AND m.section_id = cs.class_id
                 AND m.academic_year = '$academic_year'
                 AND m.semester = '$semester'
                 AND m.student_id = '$student_internal_id'
                WHERE cs.class_id = $section_id
                ORDER BY sub.subject_name ASC";

        $q = mysqli_query($conn, $sql) or die(mysqli_error($conn));

        while($row = mysqli_fetch_assoc($q)){
            $response[] = [
                'subject_id'   => $row['suid'],
                'subject_name' => $row['subject_name'],
                'marks'        => $row['marks'],
            ];
        }
    }
}

// Return JSON
header('Content-Type: application/json');
echo json_encode($response);
