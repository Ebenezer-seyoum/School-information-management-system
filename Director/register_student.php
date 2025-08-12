<?php
include('directorHeader.php');
?>
<!-- Page content start -->
<div class="container">
  <div class="page-inner">
   <div class="page-header">
     <h3 class="fw-bold mb-3">Register Student</h3>
    <ul class="breadcrumbs mb-3">
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
     $region = $zone = $woreda = $kebele ="";
     $password = $confirmPassword =$username = $phone = $role_type = $success = "";
     $student_id_err = $firstName_err = $fatherName_err = $gFatherName_err = "";
     $region_err = $zone_err = $woreda_err = $kebele_err = "";
     $gender_err = $email_err = $password_err = $confirmPassword_err = $username_err = "";
     $profile_pic_err = $phone_err = $role_type_err= $allErr = "";
     $test = true;
     $generatedId = getNextIdNumber();
if (isset($_POST["register"]) && ($_SERVER["REQUEST_METHOD"] == "POST")) {
    //validate student id
    if (empty($_POST["student_id"])) {
        $student_id_err = "Please enter your student_id";
        $test = false;
    } else if (validateStudent_id($_POST["student_id"]) == 0) {
        $student_id_err = "Please enter valid student_id";
        $test = false;
    } else {
        $student_id = $_POST["student_id"];
    }
    //validate profile picture
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
    //validate first name
    if (empty($_POST["first_name"])) {
        $firstName_err = "Please enter your first name";
        $test = false;
    } else if (validateName($_POST["first_name"]) == 0) {
        $firstName_err = "Please enter valid first name";
        $test = false;
    } else {
        $firstName = $_POST["first_name"];
    }

    //validate father name
    if (empty($_POST["father_name"])) {
        $fatherName_err = "Please enter your father name";
        $test = false;
    } else if (validateName($_POST["father_name"]) == 0) {
        $fatherName_err = "Please enter valid father name";
        $test = false;
    } else {
        $fatherName = $_POST["father_name"];
    }

    //validate grand father name
    if (empty($_POST["grand_father_name"])) {
        $gFatherName_err = "Please enter your grand father name";
        $test = false;
    } else if (validateName($_POST["grand_father_name"]) == 0) {
        $gFatherName_err = "Please enter valid grand father name";
        $test = false;
    } else {
        $gFatherName = $_POST["grand_father_name"];
    }

    //validate gender
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
// Validate region
if (empty($_POST["region"])) {
    $region_err = "Please select your region";
    $test = false;
} else {
    $region = $_POST["region"];
}

// Validate zone
if (empty($_POST["zone"])) {
    $zone_err = "Please select your zone";
    $test = false;
} else {
    $zone = $_POST["zone"];
}

// Validate woreda
if (empty($_POST["woreda"])) {
    $woreda_err = "Please select your woreda";
    $test = false;
} else {
    $woreda = $_POST["woreda"];
}

// Validate kebele
if (empty($_POST["kebele"])) {
    $kebele_err = "Please enter your kebele";
    $test = false;
} else {
    $kebele = $_POST["kebele"];
}
    //validate username
    if (empty($_POST["username"])) {
        $username_err = "Please enter your username";
        $test = false;
    } else if (validateName($_POST["username"]) == 0) {
        $username_err = "Please enter valid username";
        $test = false;
    } else {
        $username = $_POST["username"];
    }   
    //validate password
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

    //validate password confirmation
    if (empty($_POST["confirm_password"])) {
        $confirmPassword_err = "Please enter your new password";
        $test = false;
    } else if (validatePassword($_POST["confirm_password"]) == 0) {
        $confirmPassword_err = "Please enter valid password";
        $test = false;
    } else if (comparePasswords($_POST["password"], $_POST["confirm_password"]) == 0) {
        $confirmPassword_err = "Password did not match";
    } else {
        $confirmPassword = $_POST["confirm_password"];
    }
      //validate  Phone 
    if (empty($_POST["phone"])) {
        $phone_err = "Please enter your id number";
        $test = false;
    } else if (validatePhoneNumber($_POST["phone"]) == 0) {
        $phone_err = "Please enter valid phone number";
        $test = false;
    } else {
        $phone = $_POST["phone"];
    }
    //validate role_type
    if (empty($_POST["role_type"])) {
        $role_type_err = "Please select role_type";
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
        
       if (addUser($student_id, $profile_pic, $firstName, $fatherName, $gFatherName, $gender, $role_type, $username, 
    $encryptedPassword, $email, $phone, $userStatus) == 1) {
    $success = "User Successfully registered";
    header('refresh:2');
} else {
    $allErr = "There was error while registration";
}

} else {
    $allErr = "User with this id number already exists";
}
}

} //if (isset($_POST["register"]) and ($_SERVER["REQUEST_METHOD"] = "POST")) {
?>
     <!-- Main Content -->
<div class="main-content">
  <section class="section">
    <form action="" method="POST" enctype="multipart/form-data"> 
    <div class="row">
    <div class="col-lg-4 mb-4">
    <label class="form-label text-primary">Photo</label>
<div class="avatar-upload">
  <div class="avatar-preview">
    <div class="user-img">
    <img class="profile-images" src="<?php echo !empty($userProfile["profile_picture"]) ? $userProfile["profile_picture"] :
     '../assets/img/no.png'; ?>" alt="Profile Picture" width="100" height="100">
     </div>
   </div>
<div class="change-btn mt-2 mb-lg-0 mb-3">
<input type="file" class="form-control d-none" id="imageUpload" name="profile_picture">
<label for="imageUpload" class="dlab-upload mb-0 btn btn-primary btn-sm">Choose File</label>
    <button type="button" id="removeImage" class="btn btn-danger light remove-img ms-2 btn-sm">Remove</button><br>
    <span class="text-danger"><?php echo $profile_pic_err; ?></span>
     </div>
  </div>
</div>
<div class="col-lg-8">
 <div class="card ">
  <div class="card-header">
    <h4>Register Student</h4>
  </div>
    <?php if (!empty($success))
     { ?>
  <div class=" form-control bg-success"><?php echo $success; ?></div>
        <?php  } ?>
    <?php if (!empty($allErr)) { ?>
  <div class=" form-control bg-danger"><?php echo $allErr; ?></div>
        <?php  } ?>
<div class="card-body">                                
  <div class="row">
    <div class="form-group col-12 col-md-6 mb-3">
       <label for="student_id">Student_id</label>
       <input id="student_id" type="text" class="form-control" name="student_id" 
       value="<?php echo $generatedId; ?>" readonly/>
       <span class="text-danger"><?php echo $student_id_err; ?></span>
    </div>
    <div class="form-group col-12 col-md-6 mb-3">
        <label for="first_name">First Name</label>
        <input id="first_name" type="text" class="form-control" name="first_name"/>
        <span class="text-danger"><?php echo $firstName_err; ?></span>
    </div>
</div>
<div class="row">
     <div class="form-group col-12 col-md-6 mb-3">
       <label for="father_name">Father Name</label>
       <input id="father_name" type="text" class="form-control" name="father_name"/>
       <span class="text-danger"><?php echo $fatherName_err; ?></span>
     </div>
     <div class="form-group col-12 col-md-6 mb-3">
       <label for="grand_father_name">Grand Father Name</label>
       <input id="grand_father_name" type="text" class="form-control" name="grand_father_name"/>
       <span class="text-danger"><?php echo $gFatherName_err; ?></span>
    </div>       
</div>
<div class="row">
  <div class="form-group col-12 col-md-6 mb-3">
    <label for="gender">Gender</label>
    <select name="gender" id="gender" class="form-control">
        <option value="">Gender</option>
        <option value="M">M</option>
        <option value="F">F</option>
    </select>
    <span class="text-danger"><?php echo $gender_err; ?></span>
  </div> 
  <div class="form-group col-12 col-md-6 mb-3">
    <label for="Email">Email</label>
    <input id="Email" type="text" class="form-control" name="email" />
    <span class="text-danger"><?php echo $email_err; ?></span>
  </div>   
  <!-- Personal Details -->
<div class="row">
    <div class="form-group col-12 col-md-6 mb-3">
        <label for="dob">Date of Birth</label>
        <input type="date" name="dob" id="dob" class="form-control">
    </div>
    <div class="form-group col-12 col-md-6 mb-3">
        <label for="birth_place">Place of Birth</label>
        <input type="text" name="birth_place" id="birth_place" class="form-control">
    </div>
</div>  
  <div class="row">
                                <div class="form-group col-6">
                                    <label>Region</label>
                                    <select class="form-control" name="region">
                                        <option value="">Select Region</option>
                                        <?php
                                        $regions = getAllRegions();
                                        foreach ($regions as $regionItem): ?>
                                            <option value="<?php echo $regionItem['id']; ?>"><?php echo $regionItem['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="text-danger"><?php echo $region_err; ?></span>
                                </div>
                                <div class="form-group col-6">
                                <label>Zone</label>
                     <select class="form-control" name="zone" id="zone">
                  <option value="">Select Zone</option>
                  </select>
                 <span class="text-danger"><?php echo $zone_err; ?></span>
                           </div>
                         </div>
                            <div class="row">
                                <div class="form-group col-6">
                                <label>Woreda</label>
        <select class="form-control" name="woreda" id="woreda">
            <option value="">Select Woreda</option>
        </select>
        <span class="text-danger"><?php echo $woreda_err; ?></span>
    </div>
                                <div class="form-group col-6">
                                    <label>Kebele</label>
                                    <input type="text" class="form-control" name="kebele" />
                                    <span class="text-danger"><?php echo $kebele_err; ?></span>
                                </div>
                            </div>
<div class="row">
  <div class="form-group col-12 col-md-6 mb-3">
    <label for="password" class="d-block">Password</label>
    <input type="password" id="password" name="password" class="form-control" onkeyup="checkADDPassword()" />
  <ul id="password-checklist" style="list-style: none; padding: 0; display: none;">
    <li id="lower" style="color: red;">❌ One lowercase letter</li>
    <li id="upper" style="color: red;">❌ One uppercase letter</li>
    <li id="special" style="color: red;">❌ One special character (@#$%^&+=!)</li>
    <li id="length" style="color: red;">❌ At least 8 characters</li>
</ul>
    <span class="text-danger"><?php echo $password_err; ?></span>
  </div>
  <div class="form-group col-12 col-md-6 mb-3">
    <label for="password2" class="d-block">Password Confirmation</label>
    <input id="password2" type="password" class="form-control" name="confirm_password" />
    <span class="text-danger"><?php echo $confirmPassword_err; ?></span>
  </div>
  <div class="form-group col-12 col-md-6 mb-3">
    <label for="username">username</label>
    <input id="username" type="text" class="form-control" name="username" />
    <span class="text-danger"><?php echo $username_err; ?></span>
  </div> 
   <div class="form-group col-12 col-md-6 mb-3">
    <label for="phone">Phone Number</label>
    <input id="phone" type="text" class="form-control" name="phone" />
    <span class="text-danger"><?php echo $phone_err; ?></span>
  </div>
</div>
 <div class="form-group col-12 col-md-6 mb-3">
    <label for="role_type">Class Type</label>
    <select name="role_type" id="role_type" class="form-control">
        <option value="">Select Class Type</option>
        <option value="general">General</option>
        <option value="natural">Natural</option>
        <option value="social">Social</option>
    </select>
<span class="text-danger"><?php echo $role_type_err; ?></span>
</div>
<!-- Emergency Contact -->
<h5 class="mt-4">Emergency Contact</h5>
<div class="row">
    <div class="form-group col-12 col-md-6 mb-3">
        <label for="emergency_contact_name">Contact Name</label>
        <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control">
    </div>
    <div class="form-group col-12 col-md-6 mb-3">
        <label for="emergency_contact_phone">Contact Phone</label>
        <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="form-control">
    </div>
</div>

  <div class="form-group">
    <input type="submit" name="register" class="btn btn-primary btn-lg btn-block" value="Register"/>   
    <input type="reset" name="reset"  class="btn btn-danger btn-lg btn-block" value="reset"/>
  </div>
</form>
 </div>
  </div>
   </div>
    </div>
</section>
</div>
 </div>
  </div>
<?php
include('../admin/footer.php');
?>