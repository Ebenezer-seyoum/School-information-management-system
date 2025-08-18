<?php
include('../connection/connection.php');

$year = $_GET['year'] ?? '';
if(!$year) { echo json_encode([]); exit; }

$year = mysqli_real_escape_string($conn,$year);

$sql = "SELECT DISTINCT s.cid, s.section_name, s.class_type
        FROM assign_student ast
        JOIN sections s ON ast.section_id = s.cid
        WHERE ast.academic_year='$year'
        ORDER BY s.class_type, s.section_name";

$res = mysqli_query($conn,$sql);
$classes = [];
while($row = mysqli_fetch_assoc($res)) {
    $classes[] = $row;
}

echo json_encode($classes);
