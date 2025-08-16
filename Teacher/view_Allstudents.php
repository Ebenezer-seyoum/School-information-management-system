<?php
session_start();
include('../connection/connection.php');
include('../connection/function.php');

// --- Auth check ---
if (!isset($_SESSION["uid"])) {
    exit("<div class='alert alert-danger'>Not authorized</div>");
}

$profile  = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);

if ($roleName !== "Teacher") {
    exit("<div class='alert alert-danger'>Not authorized</div>");
}

// --- Inputs ---
$atid = (int)($_GET['class_id'] ?? 0);
if ($atid <= 0) {
    exit("<div class='alert alert-danger'>Invalid class</div>");
}

// --- Fetch Class Info ---
$classInfo = getClassInfo($conn, $atid);
if (!$classInfo) {
    exit("<div class='alert alert-danger'>Class not found</div>");
}

// --- Fetch Students ---
$students = getStudentsBySection($conn, $classInfo['section_id'], $classInfo['academic_year']);
?>

<table class="table table-bordered table-striped align-middle" id="studentsTable">
  <thead class="table-dark text-center">
    <tr>
      <th>#</th>
      <th>Profile</th>
      <th>Student ID</th>
      <th>First Name</th>
      <th>Father Name</th>
      <th>Gender</th>
    </tr>
  </thead>
  <tbody class="text-center">
    <?php if ($students && count($students) > 0): $no=1; ?>
      <?php foreach ($students as $s): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td>
            <a href="student_profile.php?sid=<?= urlencode($s['sid']) ?>" target="_blank">
              <img src="<?= htmlspecialchars($s['student_photo']) ?>" 
                   alt="Profile" 
                   class="rounded-circle border border-dark shadow-sm"
                   style="width: 80px; height: 80px; object-fit: cover;">
            </a>
          </td>
          <td><strong><?= htmlspecialchars($s['student_id']) ?></strong></td>
          <td><?= htmlspecialchars($s['first_name']) ?></td>
          <td><?= htmlspecialchars($s['father_name']) ?></td>
          <td><?= htmlspecialchars($s['gender']) ?></td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="6" class="text-danger">No students assigned to this class and year.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
