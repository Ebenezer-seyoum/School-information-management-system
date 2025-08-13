<?php
include('studentHeader.php');

if (isset($_GET["student_id"])) {
    $student_id = basics($_GET["student_id"]);
    $userProfile = getStudentByID($student_id);
    if (!$userProfile) {
        echo "<p>No student found.</p>";
        include('footer.php');
        exit;
    }
} else {
    echo "<p>User ID not provided.</p>";
    include('footer.php');
    exit;
}
?>

<div class="container">
 <div class="page-inner">
   <div class="page-header">
     <h3 class="fw-bold mb-3">View Account</h3>
     <ul class="breadcrumbs mb-3">
       <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
       <li class="separator"><i class="icon-arrow-right"></i></li>
       <li class="nav-item"><a href="#">Users</a></li>
       <li class="separator"><i class="icon-arrow-right"></i></li>
       <li class="nav-item"><a href="#">View Account</a></li>
     </ul>
  </div>

<div class="main-content">
  <section class="section">
    <!-- Personal Information -->
    <div class="card mb-3">
      <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Personal Information</h4>
      </div>
      <div class="card-body">
        <div class="row">
          <!-- Photo -->
          <div class="col-lg-4 mb-4 text-center">
            <img src="<?php echo !empty($userProfile['student_photo']) ? htmlspecialchars($userProfile['student_photo']) : '../assets/img/no.png'; ?>" 
                 alt="Profile Picture" width="120" height="120">
          </div>
          <div class="col-lg-8">
            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($userProfile['student_id']); ?></p>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($userProfile['first_name']); ?></p>
            <p><strong>Father Name:</strong> <?php echo htmlspecialchars($userProfile['father_name']); ?></p>
            <p><strong>Grand Father Name:</strong> <?php echo htmlspecialchars($userProfile['grand_father_name']); ?></p>
            <p><strong>Gender:</strong> <?php echo $userProfile['gender'] == 'M' ? 'Male' : 'Female'; ?></p>
            <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($userProfile['dob']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($userProfile['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($userProfile['phone']); ?></p>
            <p><strong>Place of Birth:</strong> <?php echo htmlspecialchars($userProfile['birth_place']); ?></p>
            <p><strong>Nationality:</strong> <?php echo htmlspecialchars($userProfile['nationality']); ?></p>
            <p><strong>Region:</strong> <?php echo htmlspecialchars($userProfile['region']); ?></p>
            <p><strong>Zone:</strong> <?php echo htmlspecialchars($userProfile['zone']); ?></p>
            <p><strong>Woreda:</strong> <?php echo htmlspecialchars($userProfile['woreda']); ?></p>
            <p><strong>Kebele:</strong> <?php echo htmlspecialchars($userProfile['kebele']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($userProfile['username']); ?></p>
            <p><strong>Class Type:</strong> <?php echo htmlspecialchars($userProfile['role_type']); ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Parent / Guardian Information -->
    <div class="card mb-3">
      <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Parent / Guardian Information</h4>
      </div>
      <div class="card-body">
        <p><strong>Father’s Full Name:</strong> <?php echo htmlspecialchars($userProfile['father_full_name']); ?></p>
        <p><strong>Mother’s Full Name:</strong> <?php echo htmlspecialchars($userProfile['mother_name']); ?></p>
        <p><strong>Father’s Contact:</strong> <?php echo htmlspecialchars($userProfile['father_contact']); ?></p>
        <p><strong>Mother’s Contact:</strong> <?php echo htmlspecialchars($userProfile['mother_contact']); ?></p>
        <p><strong>Father’s Occupation:</strong> <?php echo htmlspecialchars($userProfile['father_occupation']); ?></p>
        <p><strong>Mother’s Occupation:</strong> <?php echo htmlspecialchars($userProfile['mother_occupation']); ?></p>
      </div>
    </div>

    <!-- Emergency Information -->
    <div class="card mb-3">
      <div class="card-header bg-warning text-white">
        <h4 class="mb-0">Emergency Information</h4>
      </div>
      <div class="card-body">
        <p><strong>Contact Name:</strong> <?php echo htmlspecialchars($userProfile['emergency_contact_name']); ?></p>
        <p><strong>Contact Phone:</strong> <?php echo htmlspecialchars($userProfile['emergency_contact_phone']); ?></p>
      </div>
    </div>

    <!-- Health Information -->
    <div class="card mb-3">
      <div class="card-header bg-danger text-white">
        <h4 class="mb-0">Health Information</h4>
      </div>
      <div class="card-body">
        <p><strong>Blood Group:</strong> <?php echo htmlspecialchars($userProfile['blood_group']); ?></p>
        <p><strong>Medical Condition:</strong> <?php echo htmlspecialchars($userProfile['medical_condition']); ?></p>
        <?php if (!empty($userProfile['other_condition'])): ?>
            <p><strong>Other Condition:</strong> <?php echo htmlspecialchars($userProfile['other_condition']); ?></p>
        <?php endif; ?>
        <p><strong>Disabilities:</strong> <?php echo htmlspecialchars($userProfile['disabilities']); ?></p>
      </div>
    </div>

    <!-- Academic Information -->
    <div class="card mb-3">
      <div class="card-header bg-success text-white">
        <h4 class="mb-0">Academic Information</h4>
      </div>
      <div class="card-body">
        <p><strong>Previous School:</strong> <?php echo htmlspecialchars($userProfile['previous_school']); ?></p>
        <p><strong>Academic Status Before Joining:</strong> <?php echo htmlspecialchars($userProfile['academic_status']); ?></p>
        <p><strong>Previous Documents:</strong> 
          <?php if (!empty($userProfile['previous_documents'])): ?>
            <a href="<?php echo htmlspecialchars($userProfile['previous_documents']); ?>" target="_blank">View Document</a>
          <?php else: ?>
            None
          <?php endif; ?>
        </p>
      </div>
    </div>

  </section>
</div>

<?php include('footer.php'); ?>
