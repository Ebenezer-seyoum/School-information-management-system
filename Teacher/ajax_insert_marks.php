<?php
session_start();
include('../connection/connection.php');
include('../connection/function.php');

$uid = $_SESSION['uid'] ?? 0;
$profile = getUserByID($uid);
if(getRoleNameById($profile['user_type'])!=='Teacher'){ echo "Not authorized"; exit; }
if($_SERVER['REQUEST_METHOD']!=='POST'){ echo "Invalid request"; exit; }

$atid = (int)($_POST['atid'] ?? 0);
$academic_year = mysqli_real_escape_string($conn,$_POST['academic_year'] ?? '');
$section_id = (int)($_POST['section_id'] ?? 0);
$subject_id = (int)($_POST['subject_id'] ?? 0);
$semester = (int)($_POST['semester'] ?? 1);
$student_ids = $_POST['student_id'] ?? [];
$marks = $_POST['mark'] ?? [];

if(!$atid || !$academic_year || !$section_id || !$subject_id){ echo "Missing data"; exit; }

for($i=0;$i<count($student_ids);$i++){
    $sid = (int)$student_ids[$i];
    $markVal = trim($marks[$i]);
    $mark = ($markVal==='') ? 0 : (int)$markVal;

    // Check if a mark already exists for this student + semester
    $check = mysqli_query($conn,"SELECT mid,mark_status FROM marks
        WHERE student_id=$sid AND section_id=$section_id AND subject_id=$subject_id
          AND academic_year='$academic_year' AND semester=$semester LIMIT 1");

    if(mysqli_num_rows($check) > 0){
        $row = mysqli_fetch_assoc($check);
        $mid = (int)$row['mid'];
        $status = (int)$row['mark_status'];

        if($status == 2){
            echo "Mark status 2: Please contact director for student ID $sid"; exit;
        }

        mysqli_query($conn,"UPDATE marks 
            SET result=$mark, mark_status=2, updated_at=NOW() 
            WHERE mid=$mid");
    } else {
        mysqli_query($conn,"INSERT INTO marks(student_id,section_id,subject_id,result,semester,academic_year,mark_status,created_at,updated_at)
            VALUES($sid,$section_id,$subject_id,$mark,$semester,'$academic_year',2,NOW(),NOW())");
    }
}

echo 'success';
