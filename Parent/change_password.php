<?php 
include('loHeader.php'); 
?>
<!-- page header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Account Details</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Account</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Account Details</a></li>
      </ul>
  </div>
<!-- end page header -->
<?php 
$password_err = $confirmPassword_err = $oldPassword_err = $allErr = $success = "";
$password = $confirmPassword = "";
$test = true;
$user_id = isset($_GET['uid']) ? $_GET['uid'] : null;
if (isset($_POST['change_password'])) {
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate old password
    if (empty($old_password)) {
        $oldPassword_err = "Please enter your old password";
        $test = false;
    }

    // Validate new password
if (empty($_POST["new_password"])) {
    $password_err = "Please enter your new password";
    $test = false;
} else if (validatePassword($_POST["new_password"]) == 0) {
    $password_err = "Please enter a valid password (no invalid symbols)";
    $test = false;
} else {
    $password = $_POST["new_password"];
    $strongPassword = isStrongPassword($password);
    if ($strongPassword !== true) {
        $password_err = $strongPassword;
        $test = false;
    }
}

// Validate confirm password
if (empty($_POST["confirm_password"])) {
    $confirmPassword_err = "Please confirm your new password";
    $test = false;
} else if (validatePassword($_POST["confirm_password"]) == 0) {
    $confirmPassword_err = "Please enter a valid password";
    $test = false;
} else if (comparePasswords($_POST["new_password"], $_POST["confirm_password"]) == 0) {
    $confirmPassword_err = "Passwords did not match";
    $test = false;
} else {
    $confirmPassword = $_POST["confirm_password"];
}


    if ($test) {
        // Check if old password is correct
        $stored_password = getUserPassword($conn, $user_id);
        if ($stored_password === false || encryptpassword($old_password) !== $stored_password) {
            $oldPassword_err = "Old password is incorrect";
        } else {
            // Everything valid: update password
            $response = changeUserPassword($conn, $user_id, $old_password, $new_password);
            if ($response['status']) {
                $success = $response['message'];
            } else {
                $allErr = $response['message'];
            }
        }
    }
}
?>
<div class="tab-pane fade show active" id="password_tab">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-xl-8 col-lg-10 col-md-12">
        <div class="card shadow-lg border-0 rounded-4">
          <div class="card-header bg-primary text-white rounded-top-4">
              <h4 class="mb-0"><i class="fas fa-key me-2"></i>Change Password</h4>
          </div>
         <?php if (!empty($success)) { ?>
  <div class="alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<?php if (!empty($allErr)) { ?>
  <div class="alert alert-danger"><?php echo $allErr; ?></div>
<?php } ?>

<div class="card-body p-4">
  <div class="row g-4">
    <form method="POST"  class="needs-validation">
         <!-- Old Password -->
 <!-- Old Password -->
<div class="col-md-6">
  <label for="old_password" class="form-label">Old Password</label>
  <div class="input-group has-validation">
    <span class="input-group-text"><i class="fas fa-lock"></i></span>
    <input type="password" class="form-control" id="old_password" name="old_password" placeholder="Enter current password">
    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="old_password">
      <i class="fas fa-eye"></i>
    </button>
  </div>
  <span class="text-danger"><?php echo $oldPassword_err; ?></span>
</div>
<!-- New Password -->
<!-- New Password -->
<div class="col-md-6">
  <label for="new_password" class="form-label">New Password</label>
  <div class="input-group has-validation">
    <span class="input-group-text"><i class="fas fa-lock"></i></span>
    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password" onkeyup="checkPassword()" />
    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
      <i class="fas fa-eye"></i>
    </button>
  </div>
  <span class="text-danger"><?php echo $password_err; ?></span>
  <!-- Password Checklist (initially hidden) -->
  <ul id="password-checklist" style="list-style: none; padding: 0; display: none;">
    <li id="lower" style="color: red;">❌ One lowercase letter</li>
    <li id="upper" style="color: red;">❌ One uppercase letter</li>
    <li id="special" style="color: red;">❌ One special character (@#$%^&+=!)</li>
    <li id="length" style="color: red;">❌ At least 8 characters</li>
  </ul>
</div>


<!-- Confirm New Password -->
<div class="col-md-6">
  <label for="confirm_password" class="form-label">Confirm New Password</label>
  <div class="input-group has-validation">
    <span class="input-group-text"><i class="fas fa-lock"></i></span>
    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password">
      <i class="fas fa-eye"></i>
    </button>
  </div>
  <span class="text-danger"><?php echo $confirmPassword_err; ?></span>
</div>

<!-- Submit Button -->
<div class="col-12 text-end">
    <button type="submit" name="change_password" class="btn btn-success px-4 mt-3"><i class="fas fa-save me-2"></i>Save Changes</button>
    <a href="profile.php?uid=<?php echo $user_id; ?>" class="btn btn-danger px-4 mt-3">
  <i class="fas fa-arrow"></i> Back
</a>

          </div>
        </div>
    </form>
</div>
 </div>
    </div>
        </div>
    </div>
</div>
<?php
 include '../Admin/footer.php'; 
 ?>
