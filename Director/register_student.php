<?php
include('directorHeader.php');

// Initialize variables
$student_id = $profile_pic = $firstName = $fatherName = $gFatherName = $gender = $email = "";
$region = $zone = $woreda = $kebele = "";
$password = $confirmPassword = $username = $phone = $role_type = $success = "";
$dob = $birth_place = $emergency_contact_name = $emergency_contact_phone = "";
$student_id_err = $firstName_err = $fatherName_err = $gFatherName_err = "";
$region_err = $zone_err = $woreda_err = $kebele_err = "";
$gender_err = $email_err = $password_err = $confirmPassword_err = $username_err = "";
$profile_pic_err = $phone_err = $role_type_err = $allErr = "";
$dob_err = $birth_place_err = $emergency_contact_name_err = $emergency_contact_phone_err = "";
$test = true;
$generatedId = getNextIdNumber();

if (isset($_POST["register"]) && ($_SERVER["REQUEST_METHOD"] == "POST")) {
    // Validate student ID
    if (empty($_POST["student_id"])) {
        $student_id_err = "Student ID is required";
        $test = false;
    } elseif (!preg_match("/^[A-Z0-9]{6,10}$/", $_POST["student_id"])) {
        $student_id_err = "Student ID must be 6-10 alphanumeric characters";
        $test = false;
    } else {
        $student_id = trim($_POST["student_id"]);
    }

    // Validate profile picture
    if (empty($_FILES["profile_picture"]["name"])) {
        $profile_pic_err = "Profile picture is required";
        $test = false;
    } else {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        $file_type = $_FILES["profile_picture"]["type"];
        $file_size = $_FILES["profile_picture"]["size"];
        
        if (!in_array($file_type, $allowed_types)) {
            $profile_pic_err = "Only JPG, PNG, or GIF files are allowed";
            $test = false;
        } elseif ($file_size > $max_size) {
            $profile_pic_err = "File size must be less than 2MB";
            $test = false;
        } elseif ($_FILES["profile_picture"]["error"] !== UPLOAD_ERR_OK) {
            $profile_pic_err = "Error uploading file. Error code: " . $_FILES["profile_picture"]["error"];
            $test = false;
        } else {
            $uploadDir = '../assets/img/';
            $file_extension = pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION);
            $unique_filename = uniqid('profile_') . '.' . $file_extension;
            $uploadFile = $uploadDir . $unique_filename;
            
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $uploadFile)) {
                $profile_pic = $uploadFile;
            } else {
                $profile_pic_err = "Failed to upload the profile picture";
                $test = false;
            }
        }
    }

    // Validate first name
    if (empty($_POST["first_name"])) {
        $firstName_err = "First name is required";
        $test = false;
    } elseif (!preg_match("/^[a-zA-Z]{2,50}$/", $_POST["first_name"])) {
        $firstName_err = "First name must be 2-50 letters only";
        $test = false;
    } else {
        $firstName = trim($_POST["first_name"]);
    }

    // Validate father name
    if (empty($_POST["father_name"])) {
        $fatherName_err = "Father name is required";
        $test = false;
    } elseif (!preg_match("/^[a-zA-Z]{2,50}$/", $_POST["father_name"])) {
        $fatherName_err = "Father name must be 2-50 letters only";
        $test = false;
    } else {
        $fatherName = trim($_POST["father_name"]);
    }

    // Validate grand father name
    if (empty($_POST["grand_father_name"])) {
        $gFatherName_err = "Grandfather name is required";
        $test = false;
    } elseif (!preg_match("/^[a-zA-Z]{2,50}$/", $_POST["grand_father_name"])) {
        $gFatherName_err = "Grandfather name must be 2-50 letters only";
        $test = false;
    } else {
        $gFatherName = trim($_POST["grand_father_name"]);
    }

    // Validate gender
    if (empty($_POST["gender"])) {
        $gender_err = "Gender is required";
        $test = false;
    } elseif (!in_array($_POST["gender"], ['M', 'F'])) {
        $gender_err = "Invalid gender selection";
        $test = false;
    } else {
        $gender = $_POST["gender"];
    }

    // Validate email
    if (empty($_POST["email"])) {
        $email_err = "Email is required";
        $test = false;
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format";
        $test = false;
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate region
    if (empty($_POST["region"])) {
        $region_err = "Region is required";
        $test = false;
    } else {
        $region = trim($_POST["region"]);
    }

    // Validate zone
    if (empty($_POST["zone"])) {
        $zone_err = "Zone is required";
        $test = false;
    } else {
        $zone = trim($_POST["zone"]);
    }

    // Validate woreda
    if (empty($_POST["woreda"])) {
        $woreda_err = "Woreda is required";
        $test = false;
    } else {
        $woreda = trim($_POST["woreda"]);
    }

    // Validate kebele
    if (empty($_POST["kebele"])) {
        $kebele_err = "Kebele is required";
        $test = false;
    } elseif (!preg_match("/^[a-zA-Z0-9\s]{2,50}$/", $_POST["kebele"])) {
        $kebele_err = "Kebele must be 2-50 alphanumeric characters";
        $test = false;
    } else {
        $kebele = trim($_POST["kebele"]);
    }

    // Validate Date of Birth
    if (empty($_POST["dob"])) {
        $dob_err = "Date of birth is required";
        $test = false;
    } elseif (!DateTime::createFromFormat('Y-m-d', $_POST["dob"])) {
        $dob_err = "Invalid date format";
        $test = false;
    } else {
        $dob = $_POST["dob"];
    }

    // Validate Place of Birth
    if (empty($_POST["birth_place"])) {
        $birth_place_err = "Place of birth is required";
        $test = false;
    } elseif (!preg_match("/^[a-zA-Z\s]{2,100}$/", $_POST["birth_place"])) {
        $birth_place_err = "Place of birth must be 2-100 letters only";
        $test = false;
    } else {
        $birth_place = trim($_POST["birth_place"]);
    }

    // Validate Emergency Contact Name
    if (empty($_POST["emergency_contact_name"])) {
        $emergency_contact_name_err = "Emergency contact name is required";
        $test = false;
    } elseif (!preg_match("/^[a-zA-Z\s]{2,50}$/", $_POST["emergency_contact_name"])) {
        $emergency_contact_name_err = "Emergency contact name must be 2-50 letters only";
        $test = false;
    } else {
        $emergency_contact_name = trim($_POST["emergency_contact_name"]);
    }

    // Validate Emergency Contact Phone
    if (empty($_POST["emergency_contact_phone"])) {
        $emergency_contact_phone_err = "Emergency contact phone is required";
        $test = false;
    } elseif (!preg_match("/^\+?[0-9]{10,13}$/", $_POST["emergency_contact_phone"])) {
        $emergency_contact_phone_err = "Invalid emergency contact phone number";
        $test = false;
    } else {
        $emergency_contact_phone = trim($_POST["emergency_contact_phone"]);
    }

    // Validate username
    if (empty($_POST["username"])) {
        $username_err = "Username is required";
        $test = false;
    } elseif (!preg_match("/^[a-zA-Z0-9_]{4,20}$/", $_POST["username"])) {
        $username_err = "Username must be 4-20 alphanumeric characters or underscore";
        $test = false;
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate password
    if (empty($_POST["password"])) {
        $password_err = "Password is required";
        $test = false;
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $_POST["password"])) {
        $password_err = "Password must be at least 8 characters, include uppercase, lowercase, number, and special character";
        $test = false;
    } else {
        $password = $_POST["password"];
    }

    // Validate password confirmation
    if (empty($_POST["confirm_password"])) {
        $confirmPassword_err = "Password confirmation is required";
        $test = false;
    } elseif ($_POST["password"] !== $_POST["confirm_password"]) {
        $confirmPassword_err = "Passwords do not match";
        $test = false;
    } else {
        $confirmPassword = $_POST["confirm_password"];
    }

    // Validate phone
    if (empty($_POST["phone"])) {
        $phone_err = "Phone number is required";
        $test = false;
    } elseif (!preg_match("/^\+?[0-9]{10,13}$/", $_POST["phone"])) {
        $phone_err = "Invalid phone number format";
        $test = false;
    } else {
        $phone = trim($_POST["phone"]);
    }

    // Validate role_type
    if (empty($_POST["role_type"])) {
        $role_type_err = "Class type is required";
        $test = false;
    } elseif (!in_array($_POST["role_type"], ['general', 'natural', 'social'])) {
        $role_type_err = "Invalid class type";
        $test = false;
    } else {
        $role_type = $_POST["role_type"];
    }

    // Process form if all validations pass
    if ($test) {
        $userStatus = 0;
        if (!studentExist($student_id)) {
            $encryptedPassword = encryptPassword($password);
            if (registerStudent($student_id, $profile_pic, $firstName, $fatherName, $gFatherName, $gender, $role_type, $username, 
                $encryptedPassword, $email, $phone, $userStatus, $dob, $birth_place, $emergency_contact_name, $emergency_contact_phone)) {
                $success = "Student successfully registered";
                header('refresh:2;url=register_student.php');
            } else {
                $allErr = "Error occurred during registration";
            }
        } else {
            $allErr = "Student with this ID already exists";
        }
    }
}
?>

<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <form action="" method="POST" enctype="multipart/form-data" id="studentForm"> 
      <div class="row">
        <div class="col-lg-4 mb-4">
          <label class="form-label text-primary">Photo</label>
          <div class="avatar-upload">
            <div class="avatar-preview">
              <div class="user-img">
                <img class="profile-images" src="<?php echo !empty($profile_pic) ? htmlspecialchars($profile_pic) : '../assets/img/no.png'; ?>" alt="Profile Picture" width="100" height="100">
              </div>
            </div>
            <div class="change-btn mt-2 mb-lg-0 mb-3">
              <input type="file" class="form-control d-none" id="imageUpload" name="profile_picture" accept="image/jpeg,image/png,image/gif">
              <label for="imageUpload" class="dlab-upload mb-0 btn btn-primary btn-sm">Choose File</label>
              <button type="button" id="removeImage" class="btn btn-danger light remove-img ms-2 btn-sm">Remove</button><br>
              <span class="text-danger"><?php echo htmlspecialchars($profile_pic_err); ?></span>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header">
              <h4>Register Student</h4>
            </div>
            <?php if (!empty($success)) { ?>
              <div class="form-control bg-success text-white"><?php echo htmlspecialchars($success); ?></div>
            <?php } ?>
            <?php if (!empty($allErr)) { ?>
              <div class="form-control bg-danger text-white"><?php echo htmlspecialchars($allErr); ?></div>
            <?php } ?>
            <div class="card-body">                                
              <div class="row">
                <div class="form-group col-12 col-md-6 mb-3">
                  <label for="student_id">Student ID</label>
                  <input id="student_id" type="text" class="form-control" name="student_id" value="<?php echo htmlspecialchars($generatedId); ?>" readonly/>
                  <span class="text-danger"><?php echo htmlspecialchars($student_id_err); ?></span>
                </div>
                <div class="form-group col-12 col-md-6 mb-3">
                  <label for="first_name">First Name</label>
                  <input id="first_name" type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($firstName); ?>"/>
                  <span class="text-danger"><?php echo htmlspecialchars($firstName_err); ?></span>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-12 col-md-6 mb-3">
                  <label for="father_name">Father Name</label>
                  <input id="father_name" type="text" class="form-control" name="father_name" value="<?php echo htmlspecialchars($fatherName); ?>"/>
                  <span class="text-danger"><?php echo htmlspecialchars($fatherName_err); ?></span>
                </div>
                <div class="form-group col-12 col-md-6 mb-3">
                  <label for="grand_father_name">Grand Father Name</label>
                  <input id="grand_father_name" type="text" class="form-control" name="grand_father_name" value="<?php echo htmlspecialchars($gFatherName); ?>"/>
                  <span class="text-danger"><?php echo htmlspecialchars($gFatherName_err); ?></span>
                </div>       
              </div>
              <div class="row">
                <div class="form-group col-12 col-md-6 mb-3">
                  <label for="gender">Gender</label>
                  <select name="gender" id="gender" class="form-control">
                    <option value="">Select Gender</option>
                    <option value="M" <?php echo $gender == 'M' ? 'selected' : ''; ?>>Male</option>
                    <option value="F" <?php echo $gender == 'F' ? 'selected' : ''; ?>>Female</option>
                  </select>
                  <span class="text-danger"><?php echo htmlspecialchars($gender_err); ?></span>
                </div> 
                <div class="form-group col-12 col-md-6 mb-3">
                  <label for="email">Email</label>
                  <input id="email" type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>" />
                  <span class="text-danger"><?php echo htmlspecialchars($email_err); ?></span>
                </div>   
              </div>
              <!-- Personal Details -->
              <div class="row">
                <div class="form-group col-12 col-md-6 mb-3">
                  <label for="dob">Date of Birth</label>
                  <input type="date" name="dob" id="dob" class="form-control" value="<?php echo htmlspecialchars($dob); ?>">
                  <span class="text-danger"><?php echo htmlspecialchars($dob_err); ?></span>
                </div>
                <div class="form-group col-12 col-md-6 mb-3">
                  <label for="birth_place">Place of Birth</label>
                  <input type="text" name="birth_place" id="birth_place" class="form-control" value="<?php echo htmlspecialchars($birth_place); ?>">
                  <span class="text-danger"><?php echo htmlspecialchars($birth_place_err); ?></span>
                </div>
              </div>  
              <div class="row">
                <div class="form-group col-6">
                  <label>Region</label>
                  <select class="form-control" name="region" id="region">
                    <option value="">Select Region</option>
                    <?php
                    $regions = getAllRegions();
                    foreach ($regions as $regionItem): ?>
                      <option value="<?php echo htmlspecialchars($regionItem['id']); ?>" <?php echo $region == $regionItem['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($regionItem['name']); ?></option>
                    <?php endforeach; ?>
                  </select>
                  <span class="text-danger"><?php echo htmlspecialchars($region_err); ?></span>
                </div>
                <div class="form-group col-6">
                  <label>Zone</label>
                  <select class="form-control" name="zone" id="zone">
                    <option value="">Select Zone</option>
                  </select>
                  <span class="text-danger"><?php echo htmlspecialchars($zone_err); ?></span>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-6">
                  <label>Woreda</label>
                  <select class="form-control" name="woreda" id="woreda">
                    <option value="">Select Woreda</option>
                  </select>
                  <span class="text-danger"><?php echo htmlspecialchars($woreda_err); ?></span>
                </div>
                <div class="form-group col-6">
                  <label>Kebele</label>
                  <input type="text" class="form-control" name="kebele" id="kebele" value="<?php echo htmlspecialchars($kebele); ?>" />
                  <span class="text-danger"><?php echo htmlspecialchars($kebele_err); ?></span>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-12 col-md-6 mb-3">
                  <label for="password" class="d-block">Password</label>
                  <input type="password" id="password" name="password" class="form-control" onkeyup="checkADDPassword()" />
                  <ul id="password-checklist" style="list-style: none; padding: 0; display: none;">
                    <li id="lower" style="color: red;">❌ One lowercase letter</li>
                    <li id="upper" style="color: red;">❌ One uppercase letter</li>
                    <li id="number" style="color: red;">❌ One number</li>
                    <li id="special" style="color: red;">❌ One special character (@$!%*?&)</li>
                    <li id="length" style="color: red;">❌ At least 8 characters</li>
                  </ul>
                  <span class="text-danger"><?php echo htmlspecialchars($password_err); ?></span>
                </div>
                <div class="form-group col-12 col-md-6 mb-3">
                  <label for="confirm_password" class="d-block">Password Confirmation</label>
                  <input id="confirm_password" type="password" class="form-control" name="confirm_password" />
                  <span class="text-danger"><?php echo htmlspecialchars($confirmPassword_err); ?></span>
                </div>
                <div class="form-group col-12 col-md-6 mb-3">
                  <label for="username">Username</label>
                  <input id="username" type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($username); ?>" />
                  <span class="text-danger"><?php echo htmlspecialchars($username_err); ?></span>
                </div> 
                <div class="form-group col-12 col-md-6 mb-3">
                  <label for="phone">Phone Number</label>
                  <input id="phone" type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($phone); ?>" />
                  <span class="text-danger"><?php echo htmlspecialchars($phone_err); ?></span>
                </div>
              </div>
              <div class="form-group col-12 col-md-6 mb-3">
                <label for="role_type">Class Type</label>
                <select name="role_type" id="role_type" class="form-control">
                  <option value="">Select Class Type</option>
                  <option value="general" <?php echo $role_type == 'general' ? 'selected' : ''; ?>>General</option>
                  <option value="natural" <?php echo $role_type == 'natural' ? 'selected' : ''; ?>>Natural</option>
                  <option value="social" <?php echo $role_type == 'social' ? 'selected' : ''; ?>>Social</option>
                </select>
                <span class="text-danger"><?php echo htmlspecialchars($role_type_err); ?></span>
              </div>
              <!-- Emergency Contact -->
              <h5 class="mt-4">Emergency Contact</h5>
              <div class="row">
                <div class="form-group col-12 col-md-6 mb-3">
                  <label for="emergency_contact_name">Contact Name</label>
                  <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control" value="<?php echo htmlspecialchars($emergency_contact_name); ?>">
                  <span class="text-danger"><?php echo htmlspecialchars($emergency_contact_name_err); ?></span>
                </div>
                <div class="form-group col-12 col-md-6 mb-3">
                  <label for="emergency_contact_phone">Contact Phone</label>
                  <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="form-control" value="<?php echo htmlspecialchars($emergency_contact_phone); ?>">
                  <span class="text-danger"><?php echo htmlspecialchars($emergency_contact_phone_err); ?></span>
                </div>
              </div>
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

<?php
include('../admin/footer.php');
?>