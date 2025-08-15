<?php
session_start();
include('../connection/connection.php');
include('../connection/function.php');

$uid = $_SESSION['uid'] ?? 0;
$profile = getUserByID($uid);
if(strtolower(getRoleNameById($profile['user_type']))!=='teacher'){
    echo "<div class='alert alert-danger'>Not authorized</div>";
    exit;
}

$section_id = (int)($_GET['section_id'] ?? 0);
$year       = mysqli_real_escape_string($conn, $_GET['year'] ?? '');
$semester   = (int)($_GET['semester'] ?? 1);

if(!$section_id || !$year){
    echo "<div class='alert alert-warning'>Invalid parameters</div>";
    exit;
}

$sql = "SELECT DISTINCT st.sid, st.student_id, CONCAT(st.first_name, ' ', st.father_name) AS full_name
        FROM marks m
        INNER JOIN students st ON m.student_id = st.sid
        WHERE m.section_id = $section_id
          AND m.academic_year = '$year'
          AND m.semester = $semester
        ORDER BY full_name ASC";
$res = mysqli_query($conn, $sql);

echo '<table class="table table-bordered table-striped">
<thead><tr><th>#</th><th>SID</th><th>Name</th><th>Action</th></tr></thead><tbody>';

$no = 1;
while($r = mysqli_fetch_assoc($res)){
    echo "<tr>
      <td>{$no}</td>
      <td>".htmlspecialchars($r['student_id'])."</td>
      <td>".htmlspecialchars($r['full_name'])."</td>
      <td><button class='btn btn-sm btn-primary view-details-btn' data-sid='{$r['sid']}'>Details</button></td>
    </tr>";
    $no++;
}
if($no === 1){
    echo "<tr><td colspan='4' class='text-center'>No records found</td></tr>";
}
echo '</tbody></table>';
?>
