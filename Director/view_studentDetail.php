<?php
include('directorHeader.php');

if (isset($_GET["sid"])) {
    $sid = basics($_GET["sid"]);
    $userProfile = getStudentSidByID($sid);
  // Decrypt password for display
  $decrypted_password = '';
  if ($userProfile && !empty($userProfile['password'])) {
    $decrypted_password = decryptPassword($userProfile['password']) ?: '';
  }
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
     <h3 class="fw-bold mb-3">View Detail</h3>
     <ul class="breadcrumbs mb-3">
       <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
       <li class="separator"><i class="icon-arrow-right"></i></li>
       <li class="nav-item"><a href="#">Student Management</a></li>
       <li class="separator"><i class="icon-arrow-right"></i></li>
       <li class="nav-item"><a href="#">View Detail</a></li>
     </ul>
  </div>

  <div class="main-content">
  <section class="section">

    <!-- Personal Information -->
    <div class="card mb-3">
      <div class="card-header bg-primary text-white" data-bs-toggle="collapse" href="#personalInfo" style="cursor:pointer;">
        <h4 class="mb-0">
          Personal Information
          <span class="float-end collapse-arrow">&#9660;</span>
        </h4>
      </div>
      <div class="collapse show" id="personalInfo">
        <div class="card-body">
          <div class="row">
            <div class="col-lg-4 mb-4 text-center">
              <img src="<?php echo !empty($userProfile['student_photo']) ? htmlspecialchars($userProfile['student_photo']) : '../assets/img/no.png'; ?>" 
                   alt="Profile Picture" width="120" height="120" class="mb-2">
            </div>
            <div class="col-lg-8">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Student ID</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['student_id']); ?>" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">First Name</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['first_name']); ?>" disabled>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Father Name</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['father_name']); ?>" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Grand Father Name</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['grand_father_name']); ?>" disabled>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Gender</label>
                  <input type="text" class="form-control" value="<?php echo $userProfile['gender'] == 'M' ? 'Male' : 'Female'; ?>" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Date of Birth</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['dob']); ?>" disabled>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['email']); ?>" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Phone</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['phone']); ?>" disabled>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Place of Birth</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['birth_place']); ?>" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Nationality</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['nationality']); ?>" disabled>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Region</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['region']); ?>" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Zone</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['zone']); ?>" disabled>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Woreda</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['woreda']); ?>" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Kebele</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['kebele']); ?>" disabled>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Username</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['username']); ?>" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Password</label>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($decrypted_password); ?>" disabled>
                </div>
            
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Parent / Guardian Information -->
    <div class="card mb-3">
      <div class="card-header bg-primary text-white" data-bs-toggle="collapse" href="#parentInfo" style="cursor:pointer;">
        <h4 class="mb-0">
          Parent / Guardian Information
          <span class="float-end collapse-arrow">&#9660;</span>
        </h4>
      </div>
      <div class="collapse" id="parentInfo">
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Father’s Full Name</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['father_name']); ?>" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Mother’s Full Name</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['mother_name']); ?>" disabled>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Father’s Contact</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['father_contact']); ?>" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Mother’s Contact</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['mother_contact']); ?>" disabled>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Father’s Occupation</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['father_occupation']); ?>" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Mother’s Occupation</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['mother_occupation']); ?>" disabled>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Emergency Information -->
    <div class="card mb-3">
      <div class="card-header bg-warning text-white" data-bs-toggle="collapse" href="#emergencyInfo" style="cursor:pointer;">
        <h4 class="mb-0">
          Emergency Information
          <span class="float-end collapse-arrow">&#9660;</span>
        </h4>
      </div>
      <div class="collapse" id="emergencyInfo">
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Contact Name</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['emergency_contact_name']); ?>" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Phone</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['emergency_contact_phone']); ?>" disabled>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Health Information -->
    <div class="card mb-3">
      <div class="card-header bg-danger text-white" data-bs-toggle="collapse" href="#healthInfo" style="cursor:pointer;">
        <h4 class="mb-0">
          Health Information
          <span class="float-end collapse-arrow">&#9660;</span>
        </h4>
      </div>
      <div class="collapse" id="healthInfo">
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Blood Group</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['blood_group']); ?>" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Medical Condition</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['medical_condition']); ?>" disabled>
            </div>
          </div>
          <?php if (!empty($userProfile['other_condition'])): ?>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Other Condition</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['other_condition']); ?>" disabled>
            </div>
          </div>
          <?php endif; ?>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Disabilities</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['disabilities']); ?>" disabled>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Academic Information -->
    <div class="card mb-3">
      <div class="card-header bg-success text-white" data-bs-toggle="collapse" href="#academicInfo" style="cursor:pointer;">
        <h4 class="mb-0">
          Academic Information
          <span class="float-end collapse-arrow">&#9660;</span>
        </h4>
      </div>
      <div class="collapse" id="academicInfo">
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Previous School</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['previous_school']); ?>" disabled>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Previous Documents</label>
              <?php if (!empty($userProfile['previous_documents'])): ?>
                <a href="<?php echo htmlspecialchars($userProfile['previous_documents']); ?>" target="_blank" class="form-control d-block">View Document</a>
              <?php else: ?>
                <input type="text" class="form-control" value="None" disabled>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
<!-- Back Button -->
<div class="text-center mt-4">
    <a href="view_studentForUpdate.php" class="btn btn-secondary">Back</a>
</div>
  </section>
</div>

<?php include('../Admin/footer.php'); ?>
