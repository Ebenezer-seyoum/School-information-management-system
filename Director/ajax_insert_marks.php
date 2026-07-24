<?php
session_start();
include('../connection/connection.php');
include('../connection/function.php');

$uid = $_SESSION['uid'] ?? 0;
$profile = getUserByID($uid);
if(getRoleNameById($profile['user_type'])!=='Director'){ 
    echo "Not authorized"; exit;
}

if($_SERVER['REQUEST_METHOD']!=='POST'){ 
    echo "Invalid request"; exit;
}

// --- POST data ---
$academic_year = mysqli_real_escape_string($conn,$_POST['academic_year'] ?? '');
$section_id    = (int)($_POST['section_id'] ?? 0);
$subject_id    = (int)($_POST['subject_id'] ?? 0);
$semester      = (int)($_POST['semester'] ?? 1);
$marks_post    = $_POST['mark'] ?? [];

if(!$section_id || !$subject_id || !$academic_year){
    echo "Missing essential data"; exit;
}

// Check teacher assignment
$teacher_check = mysqli_query($conn,"SELECT teacher_id FROM assign_teacher 
    WHERE section_id=$section_id AND subject_id=$subject_id AND academic_year='$academic_year' LIMIT 1");
if(mysqli_num_rows($teacher_check)==0){
    echo "No teacher assigned for this subject"; exit;
}
$teacher_id = (int)mysqli_fetch_assoc($teacher_check)['teacher_id'];

// Fetch all students in section
$student_res = mysqli_query($conn,"SELECT student_id FROM assign_student 
    WHERE section_id=$section_id AND academic_year='$academic_year'");
if(mysqli_num_rows($student_res)==0){
    echo "No students in this section"; exit;
}

$errors = [];
while($row = mysqli_fetch_assoc($student_res)){
    $sid = (int)$row['student_id'];
    $markVal = $marks_post[$sid] ?? null;

    if($markVal===null || trim($markVal)==='') continue;
    $mark = (int)trim($markVal);
    if($mark < 0 || $mark > 100){
        $errors[] = "Invalid mark for student $sid. Marks must be between 0 and 100.";
        continue;
    }

    // Check if mark exists
    $check_sql = "SELECT mid FROM marks
                  WHERE student_id=$sid AND section_id=$section_id 
                  AND subject_id=$subject_id AND academic_year='$academic_year' 
                  AND semester=$semester LIMIT 1";
    $check = mysqli_query($conn,$check_sql);
    if(!$check){ $errors[] = mysqli_error($conn); continue; }

    if(mysqli_num_rows($check)>0){
        $mid = (int)mysqli_fetch_assoc($check)['mid'];
        $update_sql = "UPDATE marks 
                       SET result=$mark, mark_status=2, teacher_id=$uid, action_by='director', updated_at=NOW()
                       WHERE mid=$mid";
        if(!mysqli_query($conn,$update_sql)) $errors[] = mysqli_error($conn);
    } else {
        $insert_sql = "INSERT INTO marks(student_id,section_id,subject_id,teacher_id,result,semester,academic_year,mark_status,action_by,created_at,updated_at)
                       VALUES($sid,$section_id,$subject_id,$uid,$mark,$semester,'$academic_year',2,'director',NOW(),NOW())";
        if(!mysqli_query($conn,$insert_sql)) $errors[] = mysqli_error($conn);
    }
}

echo empty($errors)?'success':implode("\n",$errors);
