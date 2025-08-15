<?php
include('directorHeader.php');

if (isset($_GET["sid"])) {
    $sid = basics($_GET["sid"]);
    $userProfile = getStudentSidByID($sid);
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $first_name = basics($_POST['first_name']);
    $father_name = basics($_POST['father_name']);
    $grand_father_name = basics($_POST['grand_father_name']);
    $gender = basics($_POST['gender']);
    $dob = basics($_POST['dob']);
    $email = basics($_POST['email']);
    $phone = basics($_POST['phone']);
    $birth_place = basics($_POST['birth_place']);
    $nationality = basics($_POST['nationality']);
    $region = basics($_POST['region']);
    $zone = basics($_POST['zone']);
    $woreda = basics($_POST['woreda']);
    $kebele = basics($_POST['kebele']);
    $username = basics($_POST['username']);

    $mother_name = basics($_POST['mother_name']);
    $father_contact = basics($_POST['father_contact']);
    $mother_contact = basics($_POST['mother_contact']);
    $father_occupation = basics($_POST['father_occupation']);
    $mother_occupation = basics($_POST['mother_occupation']);
    $emergency_contact_name = basics($_POST['emergency_contact_name']);
    $emergency_contact_phone = basics($_POST['emergency_contact_phone']);
    $blood_group = basics($_POST['blood_group']);
    $medical_condition = basics($_POST['medical_condition']);
    $other_condition = basics($_POST['other_condition']);
    $disabilities = basics($_POST['disabilities']);
    $previous_school = basics($_POST['previous_school']);


    // Update student information in the database (assuming a function exists)
    $updateResult = updateStudentProfile($sid, [
        'first_name' => $first_name,
        'father_name' => $father_name,
        'grand_father_name' => $grand_father_name,
        'gender' => $gender,
        'dob' => $dob,
        'email' => $email,
        'phone' => $phone,
        'birth_place' => $birth_place,
        'nationality' => $nationality,
        'region' => $region,
        'zone' => $zone,
        'woreda' => $woreda,
        'kebele' => $kebele,
        'username' => $username,
        'mother_name' => $mother_name,
        'father_contact' => $father_contact,
        'mother_contact' => $mother_contact,
        'father_occupation' => $father_occupation,
        'mother_occupation' => $mother_occupation,
        'emergency_contact_name' => $emergency_contact_name,
        'emergency_contact_phone' => $emergency_contact_phone,
        'blood_group' => $blood_group,
        'medical_condition' => $medical_condition,
        'other_condition' => $other_condition,
        'disabilities' => $disabilities,
        'previous_school' => $previous_school
   
    ]);

    if ($updateResult) {
        echo "<div class='alert alert-success'>Student information updated successfully.</div>";
        // Refresh userProfile data
        $userProfile = getStudentSidByID($sid);
    } else {
        echo "<div class='alert alert-danger'>Failed to update student information.</div>";
    }
}
?>

<div class="container">
 <div class="page-inner">
   <div class="page-header">
     <h3 class="fw-bold mb-3">Update Student</h3>
     <ul class="breadcrumbs mb-3">
       <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
       <li class="separator"><i class="icon-arrow-right"></i></li>
       <li class="nav-item"><a href="#">Manage Student</a></li>
       <li class="separator"><i class="icon-arrow-right"></i></li>
       <li class="nav-item"><a href="#">Update Student</a></li>
     </ul>
  </div>

  <div class="main-content">
  <section class="section">
    <form method="POST" enctype="multipart/form-data">
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
              <input type="file" name="student_photo" class="form-control" accept="image/*">
            </div>
            <div class="col-lg-8">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Student ID</label>
                  <input type="text" class="form-control" name="student_id" value="<?php echo htmlspecialchars($userProfile['student_id']); ?>" disabled>
                </div>
                <div class="col-md-6">
                  <label class="form-label">First Name</label>
                  <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($userProfile['first_name']); ?>" required>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Father Name</label>
                  <input type="text" class="form-control" name="father_name" value="<?php echo htmlspecialchars($userProfile['father_name']); ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Grand Father Name</label>
                  <input type="text" class="form-control" name="grand_father_name" value="<?php echo htmlspecialchars($userProfile['grand_father_name']); ?>">
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Gender</label>
                  <select class="form-control" name="gender" required>
                    <option value="M" <?php echo $userProfile['gender'] == 'M' ? 'selected' : ''; ?>>Male</option>
                    <option value="F" <?php echo $userProfile['gender'] == 'F' ? 'selected' : ''; ?>>Female</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Date of Birth</label>
                  <input type="date" class="form-control" name="dob" value="<?php echo htmlspecialchars($userProfile['dob']); ?>" required>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($userProfile['email']); ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Phone</label>
                  <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($userProfile['phone']); ?>">
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Place of Birth</label>
                  <input type="text" class="form-control" name="birth_place" value="<?php echo htmlspecialchars($userProfile['birth_place']); ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Nationality</label>
                  <input type="text" class="form-control" name="nationality" value="<?php echo htmlspecialchars($userProfile['nationality']); ?>">
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Region</label>
                  <input type="text" class="form-control" name="region" value="<?php echo htmlspecialchars($userProfile['region']); ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Zone</label>
                  <input type="text" class="form-control" name="zone" value="<?php echo htmlspecialchars($userProfile['zone']); ?>">
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Woreda</label>
                  <input type="text" class="form-control" name="woreda" value="<?php echo htmlspecialchars($userProfile['woreda']); ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Kebele</label>
                  <input type="text" class="form-control" name="kebele" value="<?php echo htmlspecialchars($userProfile['kebele']); ?>">
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Username</label>
                  <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($userProfile['username']); ?>" required>
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
              <input type="text" class="form-control" name="father_name" value="<?php echo htmlspecialchars($userProfile['father_name']); ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Mother’s Full Name</label>
              <input type="text" class="form-control" name="mother_name" value="<?php echo htmlspecialchars($userProfile['mother_name']); ?>">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Father’s Contact</label>
              <input type="text" class="form-control" name="father_contact" value="<?php echo htmlspecialchars($userProfile['father_contact']); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Mother’s Contact</label>
              <input type="text" class="form-control" name="mother_contact" value="<?php echo htmlspecialchars($userProfile['mother_contact']); ?>">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Father’s Occupation</label>
              <input type="text" class="form-control" name="father_occupation" value="<?php echo htmlspecialchars($userProfile['father_occupation']); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Mother’s Occupation</label>
              <input type="text" class="form-control" name="mother_occupation" value="<?php echo htmlspecialchars($userProfile['mother_occupation']); ?>">
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
              <input type="text" class="form-control" name="emergency_contact_name" value="<?php echo htmlspecialchars($userProfile['emergency_contact_name']); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Phone</label>
              <input type="text" class="form-control" name="emergency_contact_phone" value="<?php echo htmlspecialchars($userProfile['emergency_contact_phone']); ?>">
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
              <input type="text" class="form-control" name="blood_group" value="<?php echo htmlspecialchars($userProfile['blood_group']); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Medical Condition</label>
              <input type="text" class="form-control" name="medical_condition" value="<?php echo htmlspecialchars($userProfile['medical_condition']); ?>">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Other Condition</label>
              <input type="text" class="form-control" name="other_condition" value="<?php echo htmlspecialchars($userProfile['other_condition']); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Disabilities</label>
              <input type="text" class="form-control" name="disabilities" value="<?php echo htmlspecialchars($userProfile['disabilities']); ?>">
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
              <input type="text" class="form-control" name="previous_school" value="<?php echo htmlspecialchars($userProfile['previous_school']); ?>">
            </div>
          
          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label">Previous Documents</label>
              <?php if (!empty($userProfile['previous_documents'])): ?>
                <a href="<?php echo htmlspecialchars($userProfile['previous_documents']); ?>" target="_blank" class="form-control d-block">View Document</a>
                <input type="file" name="previous_documents" class="form-control" accept=".pdf">
              <?php else: ?>
                <input type="file" name="previous_documents" class="form-control" accept=".pdf">
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Buttons -->
    <div class="text-center mt-4">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="view_studentForUpdate.php" class="btn btn-secondary">Back</a>
    </div>
    </form>
  </section>
</div>

<?php include('../Admin/footer.php'); ?>