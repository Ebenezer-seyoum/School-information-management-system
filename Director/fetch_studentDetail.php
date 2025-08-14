<?php
include('../connection/connection.php');

if (!isset($_GET['sid']) || !is_numeric($_GET['sid'])) {
    echo '<div class="text-danger">Invalid request.</div>';
    exit;
}

$sid = intval($_GET['sid']);
$query = "SELECT * FROM students WHERE sid='$sid'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    echo '<div class="text-danger">Student not found.</div>';
    exit;
}

$student = mysqli_fetch_assoc($result);

// Generate unique suffix for collapse IDs to avoid conflicts if multiple modals open
$uid = uniqid();
?>

<div class="container-fluid p-2">
  <!-- Personal Information -->
  <div class="card mb-3">
    <div class="card-header bg-primary text-white" data-bs-toggle="collapse" href="#personalInfo<?= $uid; ?>" style="cursor:pointer;">
      <h5 class="mb-0">Personal Information</h5>
    </div>
    <div class="collapse show" id="personalInfo<?= $uid; ?>">
      <div class="card-body row">
        <div class="col-lg-4 text-center">
          <img src="<?= !empty($student['student_photo']) ? htmlspecialchars($student['student_photo']) : '../assets/img/no.png'; ?>" 
               alt="Profile Picture" width="120" height="120" class="mb-2 rounded border">
        </div>
        <div class="col-lg-8">
          <div class="row mb-2">
            <div class="col-md-6"><label class="form-label">Student ID</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($student['student_id']); ?>" disabled>
            </div>
            <div class="col-md-6"><label class="form-label">Full Name</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($student['first_name'] . ' ' . $student['father_name'] . ' ' . $student['grand_father_name']); ?>" disabled>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-md-6"><label class="form-label">Gender</label>
              <input type="text" class="form-control" value="<?= $student['gender']=='M' ? 'Male' : 'Female'; ?>" disabled>
            </div>
            <div class="col-md-6"><label class="form-label">Date of Birth</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($student['dob']); ?>" disabled>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-md-6"><label class="form-label">Email</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($student['email']); ?>" disabled>
            </div>
            <div class="col-md-6"><label class="form-label">Phone</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($student['phone']); ?>" disabled>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Parent / Guardian Information -->
  <div class="card mb-3">
    <div class="card-header bg-primary text-white" data-bs-toggle="collapse" href="#parentInfo<?= $uid; ?>" style="cursor:pointer;">
      <h5 class="mb-0">Parent / Guardian Information</h5>
    </div>
    <div class="collapse" id="parentInfo<?= $uid; ?>">
      <div class="card-body row">
        <div class="col-md-6"><label class="form-label">Father Name</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($student['father_name']); ?>" disabled>
        </div>
        <div class="col-md-6"><label class="form-label">Mother Name</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($student['mother_name']); ?>" disabled>
        </div>
        <div class="col-md-6"><label class="form-label">Father Contact</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($student['father_contact']); ?>" disabled>
        </div>
        <div class="col-md-6"><label class="form-label">Mother Contact</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($student['mother_contact']); ?>" disabled>
        </div>
      </div>
    </div>
  </div>

  <!-- Emergency & Health Information -->
  <div class="card mb-3">
    <div class="card-header bg-warning text-white" data-bs-toggle="collapse" href="#healthInfo<?= $uid; ?>" style="cursor:pointer;">
      <h5 class="mb-0">Emergency & Health</h5>
    </div>
    <div class="collapse" id="healthInfo<?= $uid; ?>">
      <div class="card-body row">
        <div class="col-md-6"><label class="form-label">Emergency Contact</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($student['emergency_contact_name']); ?>" disabled>
        </div>
        <div class="col-md-6"><label class="form-label">Emergency Phone</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($student['emergency_contact_phone']); ?>" disabled>
        </div>
        <div class="col-md-6"><label class="form-label">Blood Group</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($student['blood_group']); ?>" disabled>
        </div>
        <div class="col-md-6"><label class="form-label">Medical Condition</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($student['medical_condition']); ?>" disabled>
        </div>
        <?php if(!empty($student['other_condition'])): ?>
        <div class="col-md-6"><label class="form-label">Other Condition</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($student['other_condition']); ?>" disabled>
        </div>
        <?php endif; ?>
        <div class="col-md-6"><label class="form-label">Disabilities</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($student['disabilities']); ?>" disabled>
        </div>
      </div>
    </div>
  </div>

  <!-- Academic Information -->
  <div class="card mb-3">
    <div class="card-header bg-success text-white" data-bs-toggle="collapse" href="#academicInfo<?= $uid; ?>" style="cursor:pointer;">
      <h5 class="mb-0">Academic Information</h5>
    </div>
    <div class="collapse" id="academicInfo<?= $uid; ?>">
      <div class="card-body row">
        <div class="col-md-6"><label class="form-label">Previous School</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($student['previous_school']); ?>" disabled>
        </div>
        <div class="col-md-6"><label class="form-label">Academic Status Before Joining</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($student['academic_status']); ?>" disabled>
        </div>
        <div class="col-md-12"><label class="form-label">Previous Documents</label>
          <?php if(!empty($student['previous_documents'])): ?>
            <a href="<?= htmlspecialchars($student['previous_documents']); ?>" target="_blank" class="form-control d-block text-primary">View Document</a>
          <?php else: ?>
            <input type="text" class="form-control" value="None" disabled>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
