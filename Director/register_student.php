<?php
// Include database connection
include('../connection/connection.php'); // Adjust path to your connection file
include('directorHeader.php');

// Initialize MySQL connection
if (!isset($mysql) || !($mysql instanceof mysqli)) {
    $mysql = mysqli_connect("localhost", "root", "", "sims"); // Replace with your DB credentials
    if (!$mysql) {
        die("Connection failed: " . mysqli_connect_error());
    }
}

// Declare variables for all inputs and their corresponding error messages
$student_id = $student_photo = $firstName = $father_full_name = $gFatherName = $gender = $email = "";
$region = $zone = $woreda = $kebele = $nationality = "";
$password = $confirmPassword = $username = $phone = $success = "";
$dob = $birth_place = $emergency_contact_name = $emergency_contact_phone = "";
$fatherName = $mother_name = $father_contact = $mother_contact = $father_occupation = $mother_occupation = "";
$blood_group = $medical_condition = $other_condition = $disabilities = "";
$previous_school = $previous_documents = "";

$student_id_err = $firstName_err = $father_full_name_err = $gFatherName_err = "";
$region_err = $zone_err = $woreda_err = $kebele_err = $nationality_err = "";
$gender_err = $email_err = $password_err = $confirmPassword_err = $username_err = "";
$student_photo_err = $phone_err = $allErr = "";
$dob_err = $birth_place_err = $emergency_contact_name_err = $emergency_contact_phone_err = "";
$fatherName_err = $mother_name_err = $father_contact_err = $mother_contact_err = "";
$father_occupation_err = $mother_occupation_err = $blood_group_err = $medical_condition_err = "";
$other_condition_err = $disabilities_err = $previous_school_err = $previous_documents_err = "";
$test = true;
$generatedId = getNextIdNumber();

if (isset($_POST["register"]) && ($_SERVER["REQUEST_METHOD"] == "POST")) {
    // Validate student ID
    if (empty($_POST["student_id"])) {
        $student_id_err = "Please enter a student ID";
        $test = false;
    } else if (validateIdNumber($_POST["student_id"]) == 0) {
        $student_id_err = "Please enter valid student ID";
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
        $firstName_err = "Please enter your first name";
        $test = false;
    } else if (validateName($_POST["first_name"]) == 0) {
        $firstName_err = "Please enter valid first name";
        $test = false;
    } else {
        $firstName = trim($_POST["first_name"]);
    }

    // Validate father name
    if (empty($_POST["father_name"])) {
        $fatherName_err = "Please enter your father name";
        $test = false;
    } else if (validateName($_POST["father_name"]) == 0) {
        $fatherName_err = "Please enter valid father name";
        $test = false;
    } else {
        $fatherName = trim($_POST["father_name"]);
    }

    // Validate grand father name
    if (empty($_POST["grand_father_name"])) {
        $gFatherName_err = "Please enter your grand father name";
        $test = false;
    } else if (validateName($_POST["grand_father_name"]) == 0) {
        $gFatherName_err = "Please enter valid grand father name";
        $test = false;
    } else {
        $gFatherName = trim($_POST["grand_father_name"]);
    }

    // Validate gender
    if (empty($_POST["gender"])) {
        $gender_err = "Please select your gender";
        $test = false;
    } else if (validateGender($_POST["gender"]) == 0) {
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
        $nationality_err = "Please enter your nationality";
        $test = false;
    } else if (validateName($_POST["nationality"]) == 0) {
        $nationality_err = "Please enter valid nationality";
        $test = false;
    } else {
        $nationality = trim($_POST["nationality"]);
    }

    // Validate region
    if (empty($_POST["region"])) {
        $region_err = "Please select your region";
        $test = false;
    } else {
        $region = trim($_POST["region"]);
    }

    // Validate zone
    if (empty($_POST["zone"])) {
        $zone_err = "Please select your zone";
        $test = false;
    } else {
        $zone = trim($_POST["zone"]);
    }

    // Validate woreda
    if (empty($_POST["woreda"])) {
        $woreda_err = "Please select your woreda";
        $test = false;
    } else {
        $woreda = trim($_POST["woreda"]);
    }

    // Validate kebele
    if (empty($_POST["kebele"])) {
        $kebele_err = "Please select your kebele";
        $test = false;
    } else {
        $kebele = trim($_POST["kebele"]);
    }

    // Validate Date of Birth
    if (empty($_POST["dob"])) {
        $dob_err = "Please enter your date of birth";
        $test = false;
    } else if (checkDateOfBirth($_POST["dob"]) == 0) {
        $dob_err = "Invalid date format";
        $test = false;
    } else {
        $dob = $_POST["dob"];
    }

    // Validate Place of Birth
    if (empty($_POST["birth_place"])) {
        $birth_place_err = "Please enter your place of birth";
        $test = false;
    } else if (validateName($_POST["birth_place"]) == 0) {
        $birth_place_err = "Please enter valid place of birth";
        $test = false;
    } else {
        $birth_place = trim($_POST["birth_place"]);
    }

    // Validate Emergency Contact Name
    if (empty($_POST["emergency_contact_name"])) {
        $emergency_contact_name_err = "Please enter your emergency contact name";
        $test = false;
    } else if (validateName($_POST["emergency_contact_name"]) == 0) {
        $emergency_contact_name_err = "Please enter valid emergency contact name";
        $test = false;
    } else {
        $emergency_contact_name = trim($_POST["emergency_contact_name"]);
    }

    // Validate Emergency Contact Phone
    if (empty($_POST["emergency_contact_phone"])) {
        $emergency_contact_phone_err = "Please enter your emergency contact phone number";
        $test = false;
    } else if (!validatePhoneNumber($_POST["emergency_contact_phone"])) {
        $emergency_contact_phone_err = "Invalid emergency contact phone number";
        $test = false;
    } else {
        $emergency_contact_phone = trim($_POST["emergency_contact_phone"]);
    }

    // Validate username
    if (empty($_POST["username"])) {
        $username_err = "Please enter your username";
        $test = false;
    } else if (!validateName($_POST["username"])) {
        $username_err = "Please enter valid username";
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
        $phone_err = "Please enter your phone number";
        $test = false;
    } else if (!validatePhoneNumber($_POST["phone"])) {
        $phone_err = "Invalid phone number format";
        $test = false;
    } else {
        $phone = trim($_POST["phone"]);
    }

    // Validate father's full name
    if (empty($_POST["father_full_name"])) {
        $father_full_name_err = "Please enter your father's full name";
        $test = false;
    } else if (validateName($_POST["father_full_name"]) == 0) {
        $father_full_name_err = "Please enter valid father's full name";
        $test = false;
    } else {
        $father_full_name = trim($_POST["father_full_name"]);
    }

    // Validate mother's full name
    if (empty($_POST["mother_name"])) {
        $mother_name_err = "Please enter your mother's full name";
        $test = false;
    } else if (validateName($_POST["mother_name"]) == 0) {
        $mother_name_err = "Please enter valid mother's full name";
        $test = false;
    } else {
        $mother_name = trim($_POST["mother_name"]);
    }

    // Validate father's contact number
    if (empty($_POST["father_contact"])) {
        $father_contact_err = "Please enter your father's contact number";
        $test = false;
    } else if (!validatePhoneNumber($_POST["father_contact"])) {
        $father_contact_err = "Invalid father's contact number";
        $test = false;
    } else {
        $father_contact = trim($_POST["father_contact"]);
    }

    // Validate mother's contact number
    if (empty($_POST["mother_contact"])) {
        $mother_contact_err = "Please enter your mother's contact number";
        $test = false;
    } else if (!validatePhoneNumber($_POST["mother_contact"])) {
        $mother_contact_err = "Invalid mother's contact number";
        $test = false;
    } else {
        $mother_contact = trim($_POST["mother_contact"]);
    }

    // Validate father's occupation
    if (empty($_POST["father_occupation"])) {
        $father_occupation_err = "Please enter your father's occupation";
        $test = false;
    } else if (validateName($_POST["father_occupation"]) == 0) {
        $father_occupation_err = "Please enter valid father's occupation";
        $test = false;
    } else {
        $father_occupation = trim($_POST["father_occupation"]);
    }

    // Validate mother's occupation
    if (empty($_POST["mother_occupation"])) {
        $mother_occupation_err = "Please enter your mother's occupation";
        $test = false;
    } else if (validateName($_POST["mother_occupation"]) == 0) {
        $mother_occupation_err = "Please enter valid mother's occupation";
        $test = false;
    } else {
        $mother_occupation = trim($_POST["mother_occupation"]);
    }

    // Validate blood group
    if (empty($_POST["blood_group"])) {
        $blood_group_err = "Please enter your blood group";
        $test = false;
    } else if (validateBloodGroup($_POST["blood_group"]) == 0) {
        $blood_group_err = "Please enter valid blood group";
        $test = false;
    } else {
        $blood_group = trim($_POST["blood_group"]);
    }

    // Validate medical condition
    if (!empty($_POST["medical_condition"]) && $_POST["medical_condition"] === "Other" && empty($_POST["other_condition"])) {
        $other_condition_err = "Please specify the medical condition";
        $test = false;
    } else {
        $medical_condition = trim($_POST["medical_condition"] ?? '');
        $other_condition = trim($_POST["other_condition"] ?? '');
    }

    // Validate disabilities
    if (!empty($_POST["disabilities"]) && !in_array($_POST["disabilities"], ["Yes", "No"])) {
        $disabilities_err = "Invalid selection for disabilities";
        $test = false;
    } else {
        $disabilities = trim($_POST["disabilities"] ?? '');
    }

    // Validate previous school
    if (empty($_POST["previous_school"])) {
        $previous_school_err = "Please enter your previous school name";
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

    // Process form if all validations pass
    if ($test) {
        if (!studentExist($student_id)) {
            $encryptedPassword = encryptPassword($password);
            // Call registerStudent with all required and optional fields
            $result = registerStudent(
                $mysql,
                $student_photo,
                $student_id,
                $firstName,
                $fatherName,
                $gFatherName,
                $gender,
                $dob,
                $email,
                $phone,
                $birth_place,
                $nationality,
                $region,
                $zone,
                $woreda,
                $kebele,
                $username,
                $encryptedPassword,
                $father_full_name,
                $mother_name,
                $father_contact,
                $mother_contact,
                $father_occupation,
                $mother_occupation,
                $emergency_contact_name,
                $emergency_contact_phone,
                $blood_group,
                $medical_condition,
                $other_condition,
                $disabilities,
                $previous_school,
                $previous_documents
            );
            if ($result == 1) {
                $success = "Student successfully registered";
                header('refresh:2');
            } else {
                $allErr = "Error occurred during registration: " . mysqli_error($mysql);
            }
        } else {
            $allErr = "Student with this ID already exists";
        }
    }
}
?>

<!-- Page content start -->
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Register Students</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Manage Students</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Register Student</a></li>
            </ul>
        </div>
        <!-- End page header -->
        <div class="main-content">
            <section class="section">
                <form action="" method="POST" enctype="multipart/form-data" id="studentForm"> 
                    <div class="row">
                        <div class="col-12 col-lg-4 mb-4">
                            <label class="form-label text-primary">Photo<span class="text-danger">*</span></label>
                            <div class="avatar-upload">
                                <div class="avatar-preview">
                                    <div class="user-img">
                                        <img class="profile-images" src="<?php echo !empty($student_photo) ? htmlspecialchars($student_photo) : '../assets/img/no.png'; ?>" alt="Profile Picture" width="100" height="100">
                                    </div>
                                </div>
                                <div class="change-btn mt-2 mb-lg-0 mb-3">
                                    <input type="file" class="form-control d-none" id="imageUpload" name="profile_picture" accept="image/jpeg,image/png,image/gif">
                                    <label for="imageUpload" class="dlab-upload mb-0 btn btn-primary btn-sm">Choose File</label>
                                    <button type="button" id="removeImage" class="btn btn-danger light remove-img ms-2 btn-sm">Remove</button><br>
                                    <span class="text-danger"><?php echo htmlspecialchars($student_photo_err); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-8 col-sm-8 col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Register Student</h4>
                                </div>
                                <?php if (!empty($success)) { ?>
                                    <div class="form-control bg-success"><?php echo htmlspecialchars($success); ?></div>
                                <?php } ?>
                                <?php if (!empty($allErr)) { ?>
                                    <div class="form-control bg-danger"><?php echo htmlspecialchars($allErr); ?></div>
                                <?php } ?>
                                <div class="card-body">
                                    <!-- Student ID / First Name -->
                                    <div class="row">
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="student_id">Student ID<span class="text-danger">*</span></label>
                                            <input id="student_id" type="text" class="form-control" name="student_id" value="<?php echo htmlspecialchars($generatedId); ?>" readonly/>
                                            <span class="text-danger"><?php echo htmlspecialchars($student_id_err); ?></span>
                                        </div>
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="first_name">First Name<span class="text-danger">*</span></label>
                                            <input id="first_name" type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($firstName); ?>"/>
                                            <span class="text-danger"><?php echo htmlspecialchars($firstName_err); ?></span>
                                        </div>
                                    </div>
                                    <!-- Father / Grand Father -->
                                    <div class="row">
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="father_name">Father Name<span class="text-danger">*</span></label>
                                            <input id="father_name" type="text" class="form-control" name="father_name" value="<?php echo htmlspecialchars($fatherName); ?>"/>
                                            <span class="text-danger"><?php echo htmlspecialchars($fatherName_err); ?></span>
                                        </div>
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="grand_father_name">Grand Father Name<span class="text-danger">*</span></label>
                                            <input id="grand_father_name" type="text" class="form-control" name="grand_father_name" value="<?php echo htmlspecialchars($gFatherName); ?>"/>
                                            <span class="text-danger"><?php echo htmlspecialchars($gFatherName_err); ?></span>
                                        </div>       
                                    </div>
                                    <!-- Gender / Date of Birth -->
                                    <div class="row">
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="gender">Gender<span class="text-danger">*</span></label>
                                            <select name="gender" id="gender" class="form-control">
                                                <option value="">Select Gender</option>
                                                <option value="M" <?php echo $gender == 'M' ? 'selected' : ''; ?>>Male</option>
                                                <option value="F" <?php echo $gender == 'F' ? 'selected' : ''; ?>>Female</option>
                                            </select>
                                            <span class="text-danger"><?php echo htmlspecialchars($gender_err); ?></span>
                                        </div> 
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="dob">Date of Birth<span class="text-danger">*</span></label>
                                            <input type="date" name="dob" id="dob" class="form-control" value="<?php echo htmlspecialchars($dob); ?>">
                                            <span class="text-danger"><?php echo htmlspecialchars($dob_err); ?></span>
                                        </div>
                                    </div>
                                    <!-- Email / Phone -->
                                    <div class="row">
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="email">Email<span class="text-danger">*</span></label>
                                            <input id="email" type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>" />
                                            <span class="text-danger"><?php echo htmlspecialchars($email_err); ?></span>
                                        </div>
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="phone">Phone Number<span class="text-danger">*</span></label>
                                            <input id="phone" type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($phone); ?>" />
                                            <span class="text-danger"><?php echo htmlspecialchars($phone_err); ?></span>
                                        </div>
                                    </div>
                                    <!-- Place of Birth / Nationality -->
                                    <div class="row">
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="birth_place">Place of Birth<span class="text-danger">*</span></label>
                                            <input type="text" name="birth_place" id="birth_place" class="form-control" value="<?php echo htmlspecialchars($birth_place); ?>">
                                            <span class="text-danger"><?php echo htmlspecialchars($birth_place_err); ?></span>
                                        </div>
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="nationality">Nationality<span class="text-danger">*</span></label>
                                            <input type="text" name="nationality" id="nationality" class="form-control" value="<?php echo htmlspecialchars($nationality ?? ''); ?>">
                                            <span class="text-danger"><?php echo htmlspecialchars($nationality_err ?? ''); ?></span>
                                        </div>
                                    </div>
                                    <!-- Region / Zone -->
                                    <div class="row">
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label>Region<span class="text-danger">*</span></label>
                                            <select class="form-control" name="region" id="region">
                                                <option value="">Select Region</option>
                                                <?php foreach (getAllRegions() as $regionItem): ?>
                                                    <option value="<?php echo htmlspecialchars($regionItem['id']); ?>" <?php echo $region == $regionItem['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($regionItem['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <span class="text-danger"><?php echo htmlspecialchars($region_err); ?></span>
                                        </div>
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label>Zone<span class="text-danger">*</span></label>
                                            <select class="form-control" name="zone" id="zone">
                                                <option value="">Select Zone</option>
                                            </select>
                                            <span class="text-danger"><?php echo htmlspecialchars($zone_err); ?></span>
                                        </div>
                                    </div>
                                    <!-- Woreda / Kebele -->
                                    <div class="row">
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label>Woreda<span class="text-danger">*</span></label>
                                            <select class="form-control" name="woreda" id="woreda">
                                                <option value="">Select Woreda</option>
                                            </select>
                                            <span class="text-danger"><?php echo htmlspecialchars($woreda_err); ?></span>
                                        </div>
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label>Kebele<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="kebele" id="kebele" value="<?php echo htmlspecialchars($kebele); ?>" />
                                            <span class="text-danger"><?php echo htmlspecialchars($kebele_err); ?></span>
                                        </div>
                                    </div>
                                    <!-- Username / Password -->
                                    <div class="row">
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="username">Username<span class="text-danger">*</span></label>
                                            <input id="username" type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($username); ?>" />
                                            <span class="text-danger"><?php echo htmlspecialchars($username_err); ?></span>
                                        </div>
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="password" class="d-block">Password<span class="text-danger">*</span></label>
                                            <input type="password" id="password" name="password" class="form-control" onkeyup="checkADDPassword()" />
                                            <ul id="password-checklist" style="list-style: none; padding: 0; display: none;">
                                                <li id="lower" style="color: red;">❌ One lowercase letter</li>
                                                <li id="upper" style="color: red;">❌ One uppercase letter</li>
                                                <li id="special" style="color: red;">❌ One special character (@#$%^&+=!)</li>
                                                <li id="length" style="color: red;">❌ At least 8 characters</li>
                                            </ul>
                                            <span class="text-danger"><?php echo htmlspecialchars($password_err); ?></span>
                                        </div>
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="confirm_password" class="d-block">Confirm Password<span class="text-danger">*</span></label>
                                            <input id="confirm_password" type="password" class="form-control" name="confirm_password" />
                                            <span class="text-danger"><?php echo htmlspecialchars($confirmPassword_err); ?></span>
                                        </div>
                                    </div>
                                    <!-- Parent / Guardian Information -->
                                    <div class="row">
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="father_full_name">Father’s Full Name<span class="text-danger">*</span></label>
                                            <input type="text" name="father_full_name" id="father_full_name" class="form-control" value="<?php echo htmlspecialchars($father_full_name); ?>">
                                            <span class="text-danger"><?php echo htmlspecialchars($father_full_name_err); ?></span>
                                        </div>
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="mother_name">Mother’s Full Name<span class="text-danger">*</span></label>
                                            <input type="text" name="mother_name" id="mother_name" class="form-control" value="<?php echo htmlspecialchars($mother_name); ?>">
                                            <span class="text-danger"><?php echo htmlspecialchars($mother_name_err); ?></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="father_contact">Father’s Contact Number<span class="text-danger">*</span></label>
                                            <input type="text" name="father_contact" id="father_contact" class="form-control" value="<?php echo htmlspecialchars($father_contact); ?>">
                                            <span class="text-danger"><?php echo htmlspecialchars($father_contact_err); ?></span>
                                        </div>
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="mother_contact">Mother’s Contact Number<span class="text-danger">*</span></label>
                                            <input type="text" name="mother_contact" id="mother_contact" class="form-control" value="<?php echo htmlspecialchars($mother_contact); ?>">
                                            <span class="text-danger"><?php echo htmlspecialchars($mother_contact_err); ?></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="father_occupation">Father’s Occupation<span class="text-danger">*</span></label>
                                            <input type="text" name="father_occupation" id="father_occupation" class="form-control" value="<?php echo htmlspecialchars($father_occupation); ?>">
                                            <span class="text-danger"><?php echo htmlspecialchars($father_occupation_err); ?></span>
                                        </div>
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="mother_occupation">Mother’s Occupation<span class="text-danger">*</span></label>
                                            <input type="text" name="mother_occupation" id="mother_occupation" class="form-control" value="<?php echo htmlspecialchars($mother_occupation); ?>">
                                            <span class="text-danger"><?php echo htmlspecialchars($mother_occupation_err); ?></span>
                                        </div>
                                    </div>
                                    <!-- Emergency Information -->
                                    <div class="row">
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="emergency_contact_name">Emergency Contact Name<span class="text-danger">*</span></label>
                                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control" value="<?php echo htmlspecialchars($emergency_contact_name); ?>">
                                            <span class="text-danger"><?php echo htmlspecialchars($emergency_contact_name_err); ?></span>
                                        </div>
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="emergency_contact_phone">Emergency Contact Phone<span class="text-danger">*</span></label>
                                            <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="form-control" value="<?php echo htmlspecialchars($emergency_contact_phone); ?>">
                                            <span class="text-danger"><?php echo htmlspecialchars($emergency_contact_phone_err); ?></span>
                                        </div>
                                    </div>
                                    <!-- Health Information -->
                                    <div class="row">
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="blood_group">Blood Group <small class="text-danger">(optional)</small></label>
                                            <input type="text" name="blood_group" id="blood_group" class="form-control" value="<?php echo htmlspecialchars($blood_group); ?>">
                                            <span class="text-danger"><?php echo htmlspecialchars($blood_group_err); ?></span>
                                        </div>
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="medical_condition">Medical Condition <small class="text-danger">(optional)</small></label>
                                            <select name="medical_condition" id="medical_condition" class="form-control">
                                                <option value="">-- Select --</option>
                                                <option value="Asthma" <?php echo $medical_condition == 'Asthma' ? 'selected' : ''; ?>>Asthma</option>
                                                <option value="Diabetes" <?php echo $medical_condition == 'Diabetes' ? 'selected' : ''; ?>>Diabetes</option>
                                                <option value="Epilepsy" <?php echo $medical_condition == 'Epilepsy' ? 'selected' : ''; ?>>Epilepsy</option>
                                                <option value="Heart Condition" <?php echo $medical_condition == 'Heart Condition' ? 'selected' : ''; ?>>Heart Condition</option>
                                                <option value="Allergies" <?php echo $medical_condition == 'Allergies' ? 'selected' : ''; ?>>Allergies</option>
                                                <option value="Other" <?php echo $medical_condition == 'Other' ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                            <span class="text-danger"><?php echo htmlspecialchars($medical_condition_err); ?></span>
                                        </div>
                                        <div class="form-group col-12 col-md-6 mb-3" id="other_condition_group" style="display:<?php echo $medical_condition == 'Other' ? 'block' : 'none'; ?>;">
                                            <label for="other_condition">Please specify</label>
                                            <input type="text" name="other_condition" id="other_condition" class="form-control" value="<?php echo htmlspecialchars($other_condition); ?>">
                                            <span class="text-danger"><?php echo htmlspecialchars($other_condition_err); ?></span>
                                        </div>
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="disabilities">Disabilities <small class="text-danger">(optional)</small></label>
                                            <select name="disabilities" id="disabilities" class="form-control">
                                                <option value="">-- Select --</option>
                                                <option value="Yes" <?php echo $disabilities == 'Yes' ? 'selected' : ''; ?>>Yes</option>
                                                <option value="No" <?php echo $disabilities == 'No' ? 'selected' : ''; ?>>No</option>
                                            </select>
                                            <span class="text-danger"><?php echo htmlspecialchars($disabilities_err); ?></span>
                                        </div>
                                    </div>
                                    <!-- Academic Information -->
                                    <div class="row">
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="previous_school">Previous School / Institution<span class="text-danger">*</span></label>
                                            <input type="text" name="previous_school" id="previous_school" class="form-control" value="<?php echo htmlspecialchars($previous_school); ?>">
                                            <span class="text-danger"><?php echo htmlspecialchars($previous_school_err); ?></span>
                                        </div>
                                        <div class="form-group col-12 col-md-6 mb-3">
                                            <label for="previous_documents">Upload Previous Academic Documents <small class="text-danger">*</small></label>
                                            <input type="file" class="form-control" name="file" />
                                            <span class="text-danger"><?php echo htmlspecialchars($previous_documents_err); ?></span>
                                        </div>
                                    </div>
                                    <!-- Submit and Reset Buttons -->
                                    <div class="form-group">
                                        <input type="submit" name="register" class="btn btn-primary btn-lg btn-block" value="Register"/>
                                        <input type="reset" name="reset" class="btn btn-danger btn-lg btn-block" value="Reset"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>

<?php
include('../admin/footer.php');
// Close the database connection
if (isset($mysql) && $mysql instanceof mysqli) {
    mysqli_close($mysql);
}
?>