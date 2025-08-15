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


// Declare variables for all inputs and their corresponding error messages
$student_id = $student_photo = $firstName = $father_full_name = $gFatherName = $gender = $email = "";
$region = $zone = $woreda = $kebele = $nationality = "";
$password = $confirmPassword = $username = $phone = $role_type = $success = "";
$dob = $birth_place = $emergency_contact_name = $emergency_contact_phone = "";
$fatherName = $mother_name = $father_contact = $mother_contact = $father_occupation = $mother_occupation = "";
$blood_group = $medical_condition = $other_condition = $disabilities = "";
$previous_school = $previous_documents = "";

$student_id_err = $firstName_err = $father__full_name_err = $gFatherName_err = "";
$region_err = $zone_err = $woreda_err = $kebele_err = $nationality_err = "";
$gender_err = $email_err = $password_err = $confirmPassword_err = $username_err = "";
$student_photo_err = $phone_err = $role_type_err = $allErr = "";
$dob_err = $birth_place_err = $emergency_contact_name_err = $emergency_contact_phone_err = "";
$fatherName_err = $mother_name_err = $father_contact_err = $mother_contact_err = "";
$father_occupation_err = $mother_occupation_err = $blood_group_err = $medical_condition_err = "";
$other_condition_err = $disabilities_err = $previous_school_err = $previous_documents_err = "";
$test = true;
$generatedId = getNextIdNumber();

if (isset($_POST["update"]) && ($_SERVER["REQUEST_METHOD"] == "POST")) {
    // Validate student ID
    if (empty($_POST["student_id"])) {
        $student_id_err = "Please enter a student ID";
        $test = false;
    } else {
        $student_id = trim($_POST["student_id"]);
    }

    // Validate profile picture
    if (empty($_FILES["profile_picture"]["name"])) {
        $student_photo_err = "Please select your profile picture";
        $test = false;
    } else if ($_FILES["profile_picture"]["error"] !== UPLOAD_ERR_OK) {
        $student_photo_err = "Error uploading file. Error code: " . $_FILES["profile_picture"]["error"];
        $test = false;
    } else if (validateProfilePicture($_FILES["profile_picture"]) !== true) {
        $student_photo_err = validateProfilePicture($_FILES["profile_picture"]);
        $test = false;
    } else {
        $uploadDir = '../assets/img/';
        $uploadFile = $uploadDir . basename($_FILES["profile_picture"]["name"]);
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $uploadFile)) {
            $student_photo = $uploadFile;
        } else {
            $student_photo_err = "Failed to upload the profile picture.";
            $test = false;
        }
    }

    // Validate first name
    if (empty($_POST["first_name"])) {
        $firstName_err = "Please enter a first name";
        $test = false;
    } elseif (validateName($_POST["first_name"]) == 0) {
        $firstName_err = "Invalid first name";
        $test = false;
    } else {
        $firstName = trim($_POST["first_name"]);
    }

    // Validate father name
    if (empty($_POST["father_name"])) {
        $fatherName_err = "Please enter a father name";
        $test = false;
    } elseif (validateName($_POST["father_name"]) == 0) {
        $fatherName_err = "Invalid father name";
        $test = false;
    } else {
        $fatherName = trim($_POST["father_name"]);
    }

    // Validate grand father name
    if (empty($_POST["grand_father_name"])) {
        $gFatherName_err = "Please enter a grand father name";
        $test = false;
    } elseif (validateName($_POST["grand_father_name"]) == 0) {
        $gFatherName_err = "Invalid grand father name";
        $test = false;
    } else {
        $gFatherName = trim($_POST["grand_father_name"]);
    }

    // Validate gender
    if (empty($_POST["gender"])) {
        $gender_err = "Please select a gender";
        $test = false;
    } elseif (validateGender($_POST["gender"]) == 0) {
        $gender_err = "Invalid gender selection";
        $test = false;
    } else {
        $gender = $_POST["gender"];
    }

    // Validate email
    if (empty($_POST["email"])) {
        $email_err = "Please enter your email";
        $test = false;
    } else {
        $email = trim($_POST["email"]);
        if (!validateEmail($email)) {
            $email_err = "Please enter a valid email address (example: user@domain.com)";
            $test = false;
        }
    }

    // Validate nationality
    if (empty($_POST["nationality"])) {
        $nationality_err = "Please enter a nationality";
        $test = false;
    } elseif (validateName($_POST["nationality"]) == 0) {
        $nationality_err = "Invalid nationality";
        $test = false;
    } else {
        $nationality = trim($_POST["nationality"]);
    }

    // Validate region
    if (empty($_POST["region"])) {
        $region_err = "Please select a region";
        $test = false;
    } else {
        $region = trim($_POST["region"]);
    }

    // Validate zone
    if (empty($_POST["zone"])) {
        $zone_err = "Please select a zone";
        $test = false;
    } else {
        $zone = trim($_POST["zone"]);
    }

    // Validate woreda
    if (empty($_POST["woreda"])) {
        $woreda_err = "Please select a woreda";
        $test = false;
    } else {
        $woreda = trim($_POST["woreda"]);
    }

    // Validate kebele
    if (empty($_POST["kebele"])) {
        $kebele_err = "Please select a kebele";
        $test = false;
    } else {
        $kebele = trim($_POST["kebele"]);
    }

    // Validate Date of Birth
    if (empty($_POST["dob"])) {
        $dob_err = "Please enter a date of birth";
        $test = false;
    } elseif (checkDateOfBirth($_POST["dob"]) == 0) {
        $dob_err = "Invalid date format";
        $test = false;
    } else {
        $dob = $_POST["dob"];
    }

    // Validate Place of Birth
    if (empty($_POST["birth_place"])) {
        $birth_place_err = "Please enter a place of birth";
        $test = false;
    } elseif (validateName($_POST["birth_place"]) == 0) {
        $birth_place_err = "Invalid place of birth";
        $test = false;
    } else {
        $birth_place = trim($_POST["birth_place"]);
    }

    // Validate Emergency Contact Name
    if (empty($_POST["emergency_contact_name"])) {
        $emergency_contact_name_err = "Please enter an emergency contact name";
        $test = false;
    } elseif (validateName($_POST["emergency_contact_name"]) == 0) {
        $emergency_contact_name_err = "Invalid emergency contact name";
        $test = false;
    } else {
        $emergency_contact_name = trim($_POST["emergency_contact_name"]);
    }

    // Validate Emergency Contact Phone
    if (empty($_POST["emergency_contact_phone"])) {
        $emergency_contact_phone_err = "Please enter an emergency contact phone number";
        $test = false;
    } elseif (!validatePhoneNumber($_POST["emergency_contact_phone"])) {
        $emergency_contact_phone_err = "Invalid emergency contact phone number";
        $test = false;
    } else {
        $emergency_contact_phone = trim($_POST["emergency_contact_phone"]);
    }

    // Validate username
    if (empty($_POST["username"])) {
        $username_err = "Please enter a username";
        $test = false;
    } elseif (!validateName($_POST["username"])) {
        $username_err = "Invalid username";
        $test = false;
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate password
    if (empty($_POST["password"])) {
        $password_err = "Please enter your new password";
        $test = false;
    } else if (validatePassword($_POST["password"]) == 0) {
        $password_err = "Please enter a valid password (no invalid symbols)";
        $test = false;
    } else {
        $password = $_POST["password"];
        $strongPassword = isStrongPassword($password);
        if ($strongPassword !== true) {
            $password_err = $strongPassword;
            $test = false;
        }
    }

    // Validate password confirmation
    if (empty($_POST["confirm_password"])) {
        $confirmPassword_err = "Please enter your new password";
        $test = false;
    } else if (validatePassword($_POST["confirm_password"]) == 0) {
        $confirmPassword_err = "Please enter valid password";
        $test = false;
    } else if (comparePasswords($_POST["password"], $_POST["confirm_password"]) == 0) {
        $confirmPassword_err = "Password did not match";
        $test = false;
    } else {
        $confirmPassword = $_POST["confirm_password"];
    }

    // Validate phone
    if (empty($_POST["phone"])) {
        $phone_err = "Please enter a phone number";
        $test = false;
    } elseif (!validatePhoneNumber($_POST["phone"])) {
        $phone_err = "Invalid phone number format";
        $test = false;
    } else {
        $phone = trim($_POST["phone"]);
    }

    // Validate father's full name
    if (empty($_POST["father_full_name"])) {
        $father__full_name_err = "Please enter father's full name";
        $test = false;
    } else if (validateName($_POST["father_full_name"]) == 0) {
        $father_full_name_err = "Invalid father's full name";
        $test = false;
    } else {
        $father_full_name = trim($_POST["father_full_name"]);
    }

    // Validate mother's full name
    if (empty($_POST["mother_name"])) {
        $mother_name_err = "Please enter mother's full name";
        $test = false;
    } else if (validateName($_POST["mother_name"]) == 0) {
        $mother_name_err = "Invalid mother's full name";
        $test = false;
    } else {
        $mother_name = trim($_POST["mother_name"]);
    }

    // Validate father's contact number
    if (empty($_POST["father_contact"])) {
        $father_contact_err = "Please enter father's contact number";
        $test = false;
    } elseif (validatePhoneNumber($_POST["father_contact"]) == 0) {
        $father_contact_err = "Invalid father's contact number";
        $test = false;
    } else {
        $father_contact = trim($_POST["father_contact"]);
    }

    // Validate mother's contact number
    if (empty($_POST["mother_contact"])) {
        $mother_contact_err = "Please enter mother's contact number";
        $test = false;
    } elseif (validatePhoneNumber($_POST["mother_contact"]) == 0) {
        $mother_contact_err = "Invalid mother's contact number";
        $test = false;
    } else {
        $mother_contact = trim($_POST["mother_contact"]);
    }

    // Validate father's occupation
    if (empty($_POST["father_occupation"])) {
        $father_occupation_err = "Please enter father's occupation";
        $test = false;
    } else if (validateName($_POST["father_occupation"]) == 0) {
        $father_occupation_err = "Invalid father's occupation";
        $test = false;
    } else {
        $father_occupation = trim($_POST["father_occupation"]);
    }

    // Validate mother's occupation
    if (empty($_POST["mother_occupation"])) {
        $mother_occupation_err = "Please enter mother's occupation";
        $test = false;
    } else if (validateName($_POST["mother_occupation"]) == 0) {
        $mother_occupation_err = "Invalid mother's occupation";
        $test = false;
    } else {
        $mother_occupation = trim($_POST["mother_occupation"]);
    }

    // Validate blood group
    if (empty($_POST["blood_group"])) {
        $blood_group_err = "Please enter blood group";
        $test = false;
    } else if (validateBloodGroup($_POST["blood_group"]) == 0) {
        $blood_group_err = "Invalid blood group";
        $test = false;
    } else {
        $blood_group = trim($_POST["blood_group"]);
    }

    // Validate medical condition
    if (!empty($_POST["medical_condition"]) && $_POST["medical_condition"] === "Other" && empty($_POST["other_condition"])) {
        $other_condition_err = "Please specify the medical condition";
        $test = false;
    } else {
        $medical_condition = trim($_POST["medical_condition"]);
        $other_condition = trim($_POST["other_condition"]);
    }

    // Validate disabilities
    if (!empty($_POST["disabilities"]) && !in_array($_POST["disabilities"], ["Yes", "No"])) {
        $disabilities_err = "Invalid selection for disabilities";
        $test = false;
    } else {
        $disabilities = trim($_POST["disabilities"]);
    }

    // Validate previous school
    if (empty($_POST["previous_school"])) {
        $previous_school_err = "Please enter previous school name";
        $test = false;
    } else {
        $previous_school = trim($_POST["previous_school"]);
    }

    // Validate previous documents
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        $allowed = ["pdf" => "application/pdf", "doc" => "application/msword", "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document"];
        $file_name = $_FILES["file"]["name"];
        $file_type = $_FILES["file"]["type"];
        $file_size = $_FILES["file"]["size"];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!array_key_exists($ext, $allowed) || $file_size > 5 * 1024 * 1024 || !in_array($file_type, $allowed)) {
            $previous_documents_err = "Invalid file. Allowed: PDF, DOC, DOCX under 5MB.";
            $test = false;
        } else {
            $destination = "../assets/case_files/" . $file_name;
            if (!move_uploaded_file($_FILES["file"]["tmp_name"], $destination)) {
                $previous_documents_err = "Failed to upload the file.";
                $test = false;
            } else {
                $previous_documents = $destination;
            }
        }
    } else {
        $previous_documents_err = "Please upload a file.";
        $test = false;
    }

    // Check if all validations passed
    if ($test == true) {
        if (studentExist($student_id) == 1) {
            $encryptedPassword = encryptPassword($password);
            // Update student-specific function
            if (updateStudent(
                $student_id, $student_photo, $firstName, $fatherName, $gFatherName, $gender,
                $email, $nationality, $region, $zone, $woreda, $kebele, $dob, $birth_place,
                $emergency_contact_name, $emergency_contact_phone, $username, $encryptedPassword,
                $phone, $father_full_name, $mother_name, $father_contact, $mother_contact,
                $father_occupation, $mother_occupation, $blood_group,
                $other_condition, $disabilities, $previous_school, $previous_documents
            ) == 1) {
                $success = "Student account successfully updated";
                header('refresh:2');
                $Notif_msg = "Student account details updated.";
                $sql_Notif = "INSERT INTO notifications (user_id, message) VALUES ('$student_id', '$Notif_msg')";
                mysqli_query($conn, $sql_Notif);
            } else {
                $allErr = "Something went wrong while updating the student account";
            }
        } else {
            $allErr = "No student found with the provided information";
        }
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
            <div class="col-md-6">
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


    <!-- Buttons -->
    <div class="text-center mt-4">
      <button type="update" class="btn btn-primary">Update Students</button>
      <a href="view_studentForUpdate.php" class="btn btn-secondary">Back</a>
    </div>
    </form>
  </section>
</div>

<?php include('../Admin/footer.php'); ?>