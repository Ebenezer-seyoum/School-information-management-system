<?php
include('directorHeader.php');

if (getRoleNameById(getUserByID($_SESSION["uid"])["user_type"]) !== "Director") {
    echo "Not authorized."; exit;
}

$filters = [
    'academic_year' => $_GET['academic_year'] ?? '',
    'class_id'      => $_GET['class_id'] ?? '',
    'gender'        => $_GET['gender'] ?? '',
];

// Build SQL
$sql = "SELECT s.sid, s.first_name, s.father_name, s.gender, 
               sec.section_name, sec.class_type, 
               a.academic_year
        FROM students s
        JOIN assign_student a ON s.sid=a.student_id
        JOIN sections sec ON a.section_id=sec.cid
        WHERE 1=1";

if ($filters['academic_year']) {
    $year = mysqli_real_escape_string($conn,$filters['academic_year']);
    $sql .= " AND a.academic_year='$year'";
}
if ($filters['section_id']) {
    $class = mysqli_real_escape_string($conn,$filters['section_id']);
    $sql .= " AND a.section_id='$class'";
}
if ($filters['gender']) {
    $gender = mysqli_real_escape_string($conn,$filters['gender']);
    $sql .= " AND s.gender='$gender'";
}

$res = mysqli_query($conn,$sql);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Report Generator</title>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
</head>
<body>
<div class="container">
  <h3 class="fw-bold mb-3">School Report Generator</h3>

  <!-- Filter Form -->
  <form method="get" class="row g-3 mb-4">
    <div class="col-md-3">
      <label>Academic Year</label>
      <select name="academic_year" class="form-control">
        <option value="">All</option>
        <?php
        $yrs=mysqli_query($conn,"SELECT DISTINCT academic_year FROM assign_student ORDER BY academic_year DESC");
        while($y=mysqli_fetch_assoc($yrs)){
            $sel = $filters['academic_year']==$y['academic_year']?"selected":"";
            echo "<option $sel>{$y['academic_year']}</option>";
        }
        ?>
      </select>
    </div>
    <div class="col-md-3">
      <label>Class</label>
      <select name="class_id" class="form-control">
        <option value="">All</option>
        <?php
        $cls=mysqli_query($conn,"SELECT * FROM sections");
        while($c=mysqli_fetch_assoc($cls)){
            $sel = $filters['class_id']==$c['cid']?"selected":"";
            echo "<option value='{$c['cid']}' $sel>{$c['section_name']} ({$c['class_type']})</option>";
        }
        ?>
      </select>
    </div>
    <div class="col-md-3">
      <label>Gender</label>
      <select name="gender" class="form-control">
        <option value="">All</option>
        <option value="Male" <?= $filters['gender']=="Male"?"selected":"" ?>>Male</option>
        <option value="Female" <?= $filters['gender']=="Female"?"selected":"" ?>>Female</option>
      </select>
    </div>
    <div class="col-md-3 align-self-end">
      <button type="submit" class="btn btn-primary">Generate</button>
    </div>
  </form>

  <!-- Report Table -->
  <table id="reportTable" class="display nowrap" style="width:100%">
    <thead>
      <tr>
        <th>SID</th>
        <th>Name</th>
        <th>Gender</th>
        <th>Class</th>
        <th>Academic Year</th>
      </tr>
    </thead>
    <tbody>
      <?php while($r=mysqli_fetch_assoc($res)){ ?>
      <tr>
        <td><?= $r['sid'] ?></td>
        <td><?= $r['first_name']." ".$r['father_name'] ?></td>
        <td><?= $r['gender'] ?></td>
        <td><?= $r['section_name']." (".$r['class_type'].")" ?></td>
        <td><?= $r['academic_year'] ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<script>
$(document).ready(function(){
  $('#reportTable').DataTable({
    dom: 'Bfrtip',
    buttons: ['copy','csv','excel','pdf','print'],
    responsive: true
  });
});
</script>
</body>
</html>
