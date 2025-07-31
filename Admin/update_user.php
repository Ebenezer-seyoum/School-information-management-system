<?php
include('adminHeader.php');
?>
<!-- page header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Update Account</h3>
     <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Account</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Update Account</a></li>
    </ul>
   </div>
<!-- end page header -->

<?php
  $idNumber = $firstName = $fatherName = $gFatherName = $gender = $email = "";
  $password = $confirmPassword = $username = $profile_pic = $user_type = $success = "";
  $idNumber_err = $firstName_err = $fatherName_err = $gFatherName_err = "";
  $phone_err = "";
  $gender_err = $email_err = $password_err = $confirmPassword_err = $username_err = "";
  $profile_pic_err = $user_type_err = $allErr = "";
  $test = true;
  $uid = "";
     //get the user id from the database
     if (isset($_GET["uid"]))
         $uid = basics($_GET["uid"]);
      $userProfile = getUserByID($uid);

    if (isset($_POST["update"]) and ($_SERVER["REQUEST_METHOD"] = "POST")){
    //validate id number
    if (empty($_POST["idNumber"])) {
        $idNumber_err = "Please enter your id number";
        $test = false;
    } else if (validateIdNumber($_POST["idNumber"]) == 0) {
        $idNumber_err = "Please enter valid id number";
        $test = false;
    } else {
        $idNumber = $_POST["idNumber"];
    }

    //validate profile picture
    if (!empty($_FILES["profile_picture"]["name"])) { 
        if ($_FILES["profile_picture"]["error"] !== UPLOAD_ERR_OK) {
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
    } else { 
        $profile_pic = $userProfile['profile_pic']; 
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
    //validate email
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
  
        // Validate  password
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
    //validate user_type
    if (empty($_POST["user_type"])) {
        $user_type_err = "Please select user_type";
        $test = false;
    } else if (validateUserType($_POST["user_type"]) == 0) {
        $user_type_err = "Invalid input";
        $test = false;
    } else {
        $user_type = $_POST["user_type"];
    }
    // Check if all validations passed
    if ($test == true) {
     if (userExist($uid) == 1) {
      if (updateUser($uid, $idNumber, $profile_pic, $firstName, $fatherName, $gFatherName, 
      $gender, $email, $username, $password, $user_type) == 1) { 
                $success = "Successfully updated";
                 header('refresh:2');
                  $Notif_msg = "your account detail updated.";
              $sql_Notif = "INSERT INTO notifications (user_id, message) 
                  VALUES ('$uid', '$Notif_msg')";
                     mysqli_query($conn, $sql_Notif);             
            } else {
                $allErr = " Something went wrong";
            }
        } else {
            $allErr = "There is no user associated with the given information";
        }
    }
  } //if (isset($_POST["update"]) and ($_SERVER["REQUEST_METHOD"] = "POST")) {
?>

<?php

$profile = getUserByID($_SESSION["uid"]);
if (isset($_SESSION["uid"]) && ($profile["user_type"] == "Admin")) {
?>
<div class="main-content">
 <section class="section">
  <form  method="POST" enctype="multipart/form-data">
   <div class="row">
     <div class="col-xl-4 col-lg-4">
       <label class="form-label text-primary">Photo</label>
   <div class="avatar-upload">
     <div class="avatar-preview">
       <div class="user-img">
       <img class="profile-images" name="profile_picture" src="<?php echo $userProfile["profile_pic"]; ?>" 
        alt="Profile Picture" width="100" height="100">
       </div>
   </div>
   <div class="change-btn mt-2 mb-lg-0 mb-3">
       <input type="file" class="form-control d-none" id="imageUpload" name="profile_picture" accept=".png, .jpg, .jpeg">
       <label for="imageUpload" class="dlab-upload mb-0 btn btn-primary btn-sm">Choose File</label>
       <button type="button" id="removeImage" class="btn btn-danger light remove-img ms-2 btn-sm">Remove</button>
   </div>
  </div>
</div>
<div class="col-8 col-sm-8 col-lg-8">
  <div class="card ">
    <div class="card-header">
        <h4>Update account</h4>
    </div>
    <?php if (!empty($success)) { ?>
    <div class=" form-control bg-success"><?php echo $success; ?></div>
    <?php } ?>
    <?php if (!empty($allErr)) { ?>
    <div class=" form-control bg-danger"><?php echo $allErr; ?></div>
    <?php } ?>

<div class="card-body">
 <div class="row">
   <div class="form-group col-6">
     <label for="id_number">ID Number</label>
     <input id="id_number" type="text" class="form-control" name="idNumber" value="<?php echo $userProfile["idNumber"]; ?>"/>
     <span class="text-danger"><?php echo $idNumber_err; ?></span>
   </div>
   <div class="form-group col-6">
     <label for="first_name">First Name</label>
     <input id="first_name" type="text" class="form-control" name="first_name" value="<?php echo $userProfile["first_name"]; ?>"/>
     <span class="text-danger"><?php echo $firstName_err; ?></span>
   </div>
 </div>
<div class="row">
  <div class="form-group col-6">
      <label for="father_name">Father Name</label>
      <input id="father_name" type="text" class="form-control" name="father_name" autofocus value="<?php echo $userProfile["father_name"]; ?>" />
      <span class="text-danger"><?php echo $fatherName_err; ?></span>
  </div>
  <div class="form-group col-6">
      <label for="grand_father_name">Grand Father Name</label>
      <input id="grand_father_name" type="text" class="form-control" name="grand_father_name" value="<?php echo $userProfile["gfather_name"]; ?>" />
      <span class="text-danger"><?php echo $gFatherName_err; ?></span>
  </div>
</div>
<div class="row">
  <div class="form-group col-6">
      <label for="gender">Gender</label>
      <select name="gender" id="gender" class="form-control">
          <option value="">...</option>
          <option value="M" <?php if ($userProfile["gender"] == "M") echo "selected=selected"; ?>>M</option>
          <option value="F" <?php if ($userProfile["gender"] == "F") echo "selected=selected"; ?>>F</option>
      </select>
      <span class="text-danger"><?php echo $gender_err; ?></span>
  </div>
  <div class="form-group col-6">
      <label for="Email">Email</label>
      <input id="Email" type="text" class="form-control" name="email" value="<?php echo $userProfile["email"]; ?>" />
      <span class="text-danger"><?php echo $email_err; ?></span>
  </div>
</div>
<div class="row">
  <div class="form-group col-6">
      <label for="password" class="d-block">Password</label>
      <input id="password" type="text" class="form-control" name="password" onkeyup="checkADDPassword()" />
      <span class="text-danger"><?php echo $password_err; ?></span>
      <!-- Password Checklist (initially hidden) -->
  <ul id="password-checklist" style="list-style: none; padding: 0; display: none;">
    <li id="lower" style="color: red;">❌ One lowercase letter</li>
    <li id="upper" style="color: red;">❌ One uppercase letter</li>
    <li id="special" style="color: red;">❌ One special character (@#$%^&+=!)</li>
    <li id="length" style="color: red;">❌ At least 8 characters</li>
  </ul>
  </div>
  <div class="form-group col-6">
      <label for="username">username</label>
      <input id="username" type="text" class="form-control" name="username" value="<?php echo $userProfile["username"]; ?>" />
      <span class="text-danger"><?php echo $username_err; ?></span>
  </div>
   <div class="form-group col-6">
      <label for="phone">phone</label>
      <input id="phone" type="text" class="form-control" name="phone"
      value="<?php echo $userProfile["phone"]; ?>" />
      <span class="text-danger"><?php echo $phone_err; ?></span>
  </div>
  <div class="form-group col-6">
      <label for="user_type">user_type</label>
      <select name="user_type" id="user_type" class="form-control">
          <option value="">...</option>
          <option value="Admin" <?php if ($userProfile["user_type"] == "Admin") echo "selected=selected"; ?>> Admin</option>
          <option value="Case_distributer" <?php if ($userProfile["user_type"] == "Case_distributer") echo "selected=selected"; ?>> Case_distributer</option>
          <option value="Law_officer" <?php if ($userProfile["user_type"] == "Law_officer") echo "selected=selected"; ?>> law_officer</option>
          <option value="Judge" <?php if ($userProfile["user_type"] == "Judge") echo "selected=selected"; ?>> Judge</option>
          <option value="President" <?php if ($userProfile["user_type"] == "President") echo "selected=selected"; ?>> President</option>
      </select>
      <span class="text-danger"><?php echo $user_type_err; ?></span>
  </div>
</div>
<div class="form-group">
    <input type="submit" name="update" class="btn btn-primary btn-lg btn-block" value="update info" />
    <a href="view_userForUpdate.php" class="btn btn-secondary btn-lg btn-block">Back</a>
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
 } else {
     //header('location: ../index.php');
 }
  ?>
<?php
include('footer.php');
?>
