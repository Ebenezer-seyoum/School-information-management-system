<?php
include('teacherHeader.php'); // adjust path

// --- Check teacher login ---
$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);
if(!isset($_SESSION["uid"]) || $roleName != "Teacher"){
    echo "You are not authorized to view this page.";
    exit;
}


// --- Main Logic ---
$atid = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
if($atid <= 0){
    echo "<div class='alert alert-danger'>Invalid class selected.</div>";
    exit;
}

$classInfo = getClassInfo($conn, $atid);
if(!$classInfo){
    echo "<div class='alert alert-danger'>Class not found.</div>";
    exit;
}

$students = getStudentsBySection($conn, $classInfo['section_id'], $classInfo['academic_year']);

?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">
        Students in Class: <?= htmlspecialchars($classInfo['section_name'] . ' - ' . $classInfo['class_type']) ?> 
        (<?= $classInfo['academic_year'] ?>)
      </h3>
    </div>

    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-hover text-center align-middle">
          <thead class="table-secondary">
            <tr>
              <th>#</th>
              <th>First Name</th>
              <th>Father Name</th>
              <th>Gender</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if(count($students) > 0): $no=1; ?>
              <?php foreach($students as $s): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= htmlspecialchars($s['first_name']) ?></td>
                  <td><?= htmlspecialchars($s['father_name']) ?></td>
                  <td><?= htmlspecialchars($s['gender']) ?></td>
                  <td>
                    <a href="student_profile.php?sid=<?= $s['sid'] ?>" class="btn btn-info btn-sm">View Profile</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-danger">No students assigned to this class and year.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include('../Admin/footer.php'); ?>
