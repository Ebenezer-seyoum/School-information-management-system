<?php
include('directorHeader.php');
?>

<!-- Page content start -->
<div class="container-fluid px-2 px-md-4">
  <div class="page-inner">
    <div class="page-header d-flex flex-column flex-md-row align-items-md-center">
      <h3 class="fw-bold mb-3">Register Student</h3>
      <ul class="breadcrumbs mb-3 ms-md-auto">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Students</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Register Student</a></li>
      </ul>
    </div>
    <!-- End page header -->

    <?php
    $student_id = $profile_pic = $firstName = $fatherName = $gFatherName = $gender = $email = "";
    $password = $confirmPassword = $username = $phone = $role_type = $success = "";
    $student_id_err = $firstName_err = $fatherName_err = $gFatherName_err = "";
    $gender_err = $email_err = $password_err = $confirmPassword_err = $username_err = "";
    $profile_pic_err = $phone_err = $role_type_err = $allErr = "";
    $test = true;
    $generatedId = getNextIdNumber();

    if (isset($_POST["register"]) && ($_SERVER["REQUEST_METHOD"] == "POST")) {
      // Validate student id
      if (empty($_POST["student_id"])) {
        $student_id_err = "Please enter your student ID";
        $test = false;
      } else if (validateStudent_id($_POST["student_id"]) == 0) {
        $student_id_err = "Please enter a valid student ID";
        $test = false;
      } else {
        $student_id = $_POST["student_id"];
      }

      // Validate profile picture
      if (empty($_FILES["profile_picture"]["name"])) {
        $profile_pic_err = "Please select your profile picture";
        $test = false;
      } else if ($_FILES["profile_picture"]["error"] !== UPLOAD_ERR_OK) {
        $profile_pic_err = "Error uploading file. Error code: " . $_FILES["profile_picture"]["error"];
        $test = false;
      } else if (validateProfilePicture($_FILES["profile_picture"]) !== true) {
        $profile_pic_err = validateProfilePicture($_FILES["profile_picture"]);
        $test = false;
      } else {
        $uploadDir = '../assets/img/';
        $uploadFile = $uploadDir . basename($_FILES["profile_picture"]["name"]);
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $uploadFile)) {
          $profile_pic = $uploadFile;
        } else {
          $profile_pic_err = "Failed to upload the profile picture.";
          $test = false;
        }
      }

      // Validate first name
      if (empty($_POST["first_name"])) {
        $firstName_err = "Please enter your first name";
        $test = false;
      } else if (validateName($_POST["first_name"]) == 0) {
        $firstName_err = "Please enter a valid first name";
        $test = false;
      } else {
        $firstName = $_POST["first_name"];
      }

      // Validate father name
      if (empty($_POST["father_name"])) {
        $fatherName_err = "Please enter your father name";
        $test = false;
      } else if (validateName($_POST["father_name"]) == 0) {
        $fatherName_err = "Please enter a valid father name";
        $test = false;
      } else {
        $fatherName = $_POST["father_name"];
      }

      // Validate grand father name
      if (empty($_POST["grand_father_name"])) {
        $gFatherName_err = "Please enter your grand father name";
        $test = false;
      } else if (validateName($_POST["grand_father_name"]) == 0) {
        $gFatherName_err = "Please enter a valid grand father name";
        $test = false;
      } else {
        $gFatherName = $_POST["grand_father_name"];
      }

      // Validate gender
      if (empty($_POST["gender"])) {
        $gender_err = "Please select your gender";
        $test = false;
      } else if (validateGender($_POST["gender"]) == 0) {
        $gender_err = "Invalid input";
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
        } else {
          $email = $_POST["email"];
        }
      }

      // Validate username
      if (empty($_POST["username"])) {
        $username_err = "Please enter your username";
        $test = false;
      } else if (validateName($_POST["username"]) == 0) {
        $username_err = "Please enter a valid username";
        $test = false;
      } else {
        $username = $_POST["username"];
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
        $confirmPassword_err = "Please enter a valid password";
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
      } else if (validatePhoneNumber($_POST["phone"]) == 0) {
        $phone_err = "Please enter a valid phone number";
        $test = false;
      } else {
        $phone = $_POST["phone"];
      }

      // Validate role_type
      if (empty($_POST["role_type"])) {
        $role_type_err = "Please select class type";
        $test = false;
      } else if (validateUserType($_POST["role_type"]) == 0) {
        $role_type_err = "Invalid input";
        $test = false;
      } else {
        $role_type = $_POST["role_type"];
      }

      if ($test == true) {
        $userStatus = 0;
        if (userExist($student_id) == 0) {
          $encryptedPassword = encryptPassword($password);
          if (addUser($student_id, $profile_pic, $firstName, $fatherName, $gFatherName, $gender, $role_type, $username, $encryptedPassword, $email, $phone, $userStatus) == 1) {
            $success = "User successfully registered";
            header('refresh:2');
          } else {
            $allErr = "There was an error while registering";
          }
        } else {
          $allErr = "User with this ID number already exists";
        }
      }
    }
    ?>

    <!-- Main Content -->
    <div class="main-content">
      <section class="section">
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="row g-3">
            <div class="col-12 col-md-4 col-lg-3">
              <label class="form-label text-primary">Photo</label>
              <div class="avatar-upload text-center">
                <div class="avatar-preview mb-3">
                  <img class="profile-images rounded" src="<?php echo !empty($userProfile["profile_picture"]) ? $userProfile["profile_picture"] : '../assets/img/no.png'; ?>" alt="Profile Picture" style="max-width: 150px; height: auto;">
                </div>
                <div class="change-btn">
                  <input type="file" class="form-control d-none" id="imageUpload" name="profile_picture" accept="image/*">
                  <label for="imageUpload" class="btn btn-primary btn-sm">Choose File</label>
                  <button type="button" id="removeImage" class="btn btn-danger btn-sm ms-2">Remove</button>
                  <br>
                  <span class="text-danger d-block mt-2"><?php echo $profile_pic_err; ?></span>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-8 col-lg-9">
              <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                  <h4 class="mb-0">Register Student</h4>
                </div>
                <div class="card-body">
                  <?php if (!empty($success)) { ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                  <?php } ?>
                  <?php if (!empty($allErr)) { ?>
                    <div class="alert alert-danger"><?php echo $allErr; ?></div>
                  <?php } ?>
                  <div class="row g-3">
                    <div class="col-12 col-md-6">
                      <label for="student_id" class="form-label">Student ID</label>
                      <input id="student_id" type="text" class="form-control" name="student_id" value="<?php echo htmlspecialchars($generatedId); ?>" readonly>
                      <span class="text-danger"><?php echo $student_id_err; ?></span>
                    </div>
                    <div class="col-12 col-md-6">
                      <label for="role_type" class="form-label">Class Type</label>
                      <select name="role_type" id="role_type" class="form-select">
                        <option value="">Select Class Type</option>
                        <option value="general">General</option>
                        <option value="natural">Natural</option>
                        <option value="social">Social</option>
                      </select>
                      <span class="text-danger"><?php echo $role_type_err; ?></span>
                    </div>
                    <div class="col-12 col-md-6">
                      <label for="first_name" class="form-label">First Name</label>
                      <input id="first_name" type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($firstName); ?>">
                      <span class="text-danger"><?php echo $firstName_err; ?></span>
                    </div>
                    <div class="col-12 col-md-6">
                      <label for="father_name" class="form-label">Father Name</label>
                      <input id="father_name" type="text" class="form-control" name="father_name" value="<?php echo htmlspecialchars($fatherName); ?>">
                      <span class="text-danger"><?php echo $fatherName_err; ?></span>
                    </div>
                    <div class="col-12 col-md-6">
                      <label for="grand_father_name" class="form-label">Grand Father Name</label>
                      <input id="grand_father_name" type="text" class="form-control" name="grand_father_name" value="<?php echo htmlspecialchars($gFatherName); ?>">
                      <span class="text-danger"><?php echo $gFatherName_err; ?></span>
                    </div>
                    <div class="col-12 col-md-6">
                      <label for="gender" class="form-label">Gender</label>
                      <select name="gender" id="gender" class="form-select">
                        <option value="">Select Gender</option>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                      </select>
                      <span class="text-danger"><?php echo $gender_err; ?></span>
                    </div>
                    <div class="col-12 col-md-6">
                      <label for="email" class="form-label">Email</label>
                      <input id="email" type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>">
                      <span class="text-danger"><?php echo $email_err; ?></span>
                    </div>
                    <div class="col-12 col-md-6">
                      <label for="password" class="form-label">Password</label>
                      <input type="password" id="password" name="password" class="form-control" onkeyup="checkADDPassword()">
                      <ul id="password-checklist" class="mt-2" style="list-style: none; padding: 0; display: none;">
                        <li id="lower" class="text-danger">❌ One lowercase letter</li>
                        <li id="upper" class="text-danger">❌ One uppercase letter</li>
                        <li id="special" class="text-danger">❌ One special character (@#$%^&+=!)</li>
                        <li id="length" class="text-danger">❌ At least 8 characters</li>
                      </ul>
                      <span class="text-danger"><?php echo $password_err; ?></span>
                    </div>
                    <div class="col-12 col-md-6">
                      <label for="password2" class="form-label">Confirm Password</label>
                      <input id="password2" type="password" class="form-control" name="confirm_password">
                      <span class="text-danger"><?php echo $confirmPassword_err; ?></span>
                    </div>
                    <div class="col-12 col-md-6">
                      <label for="username" class="form-label">Username</label>
                      <input id="username" type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($username); ?>">
                      <span class="text-danger"><?php echo $username_err; ?></span>
                    </div>
                    <div class="col-12 col-md-6">
                      <label for="phone" class="form-label">Phone Number</label>
                      <input id="phone" type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                      <span class="text-danger"><?php echo $phone_err; ?></span>
                    </div>
                  </div>
                  <div class="d-flex gap-2 mt-4">
                    <input type="submit" name="register" class="btn btn-primary btn-lg w-100" value="Register">
                    <input type="reset" name="reset" class="btn btn-danger btn-lg w-100" value="Reset">
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
?>

<style>
  .avatar-upload .avatar-preview img {
    max-width: 100%;
    height: auto;
    object-fit: cover;
  }
  .card {
    border-radius: 10px;
  }
  .form-control, .form-select {
    border-radius: 5px;
  }
  .alert {
    border-radius: 5px;
    margin-bottom: 1rem;
  }
  @media (max-width: 576px) {
    .page-header {
      text-align: center;
    }
    .breadcrumbs {
      justify-content: center;
    }
    .avatar-upload .change-btn {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }
    .btn-sm {
      width: 100%;
    }
  }
</style>

<script>
  function checkADDPassword() {
    const password = document.getElementById('password').value;
    const checklist = document.getElementById('password-checklist');
    const lower = document.getElementById('lower');
    const upper = document.getElementById('upper');
    const special = document.getElementById('special');
    const length = document.getElementById('length');

    checklist.style.display = 'block';

    lower.style.color = /[a-z]/.test(password) ? 'green' : 'red';
    lower.innerHTML = /[a-z]/.test(password) ? '✅ One lowercase letter' : '❌ One lowercase letter';

    upper.style.color = /[A-Z]/.test(password) ? 'green' : 'red';
    upper.innerHTML = /[A-Z]/.test(password) ? '✅ One uppercase letter' : '❌ One uppercase letter';

    special.style.color = /[@#$%^&+=!]/.test(password) ? 'green' : 'red';
    special.innerHTML = /[@#$%^&+=!]/.test(password) ? '✅ One special character (@#$%^&+=!)' : '❌ One special character (@#$%^&+=!)';

    length.style.color = password.length >= 8 ? 'green' : 'red';
    length.innerHTML = password.length >= 8 ? '✅ At least 8 characters' : '❌ At least 8 characters';
  }

  document.getElementById('removeImage').addEventListener('click', function() {
    document.getElementById('imageUpload').value = '';
    document.querySelector('.profile-images').src = '../assets/img/no.png';
  });
</script>