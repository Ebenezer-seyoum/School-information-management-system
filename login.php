<?php
include __DIR__ . '/login/loginHeader.php';
?>
<?php
$username = $password = "";
$username_err = $password_err = $all_err = "";
$test = true;

if (isset($_POST["login"]) and ($_SERVER["REQUEST_METHOD"] == "POST")) {
  // Validate username
  if (empty($_POST["username"])) {
    $username_err = "Please enter your username";
    $test = false;
  } else if (validateIdNumber($_POST["username"]) == 0) {
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
    $password_err = "Please enter a valid password";
    $test = false;
  } else {
    $password = $_POST["password"];
  }

  if ($test == true) {
    $user_data = checkUserCredentials($username, $password);

    if ($user_data) {
      // User found in users table
      if ($user_data['user_status'] == 2) {
        $all_err = "This account is deactivated. Please contact the admin.";
      } else {
        $_SESSION["uid"] = $user_data['uid'];
        updateUserStatus(1, $_SESSION["uid"]);
        $roleName = getRoleNameById($user_data['user_type']);

        if ($roleName === "Admin") {
          header('location: Admin/admin.php');
          exit;
        } else if ($roleName === "Director") {
          header('location: Director/director.php');
          exit;
        } else if ($roleName === "Instructor") {
          header('location: Instructor/instructor.php');
          exit;
        } else if ($roleName === "Teacher") {
          header('location: Teacher/teacher.php');
          exit;
        }
      }
    } else {
      // Not found in users table → check students table
      $student_data = checkStudentCredentials($username, $password);
      if ($student_data) {
        $_SESSION["sid"] = $student_data['student_id'];
        header('location: Student/student.php');
        exit;
      } else {
        $all_err = "There is no user associated with the given information";
      }
    }
  }
}
?>
<div class="container d-flex justify-content-center align-items-center min-vh-100">

  <div style="display:flex; flex-direction:row; gap:32px; max-width:800px; width:100%; background:#fff; border-radius:16px; box-shadow:0 2px 16px rgba(9,42,206,0.08); border:1px solid #092ace; padding:0;">
    <!-- Header Side -->
    <div style="background:#092ace; color:#fff; width:40%; min-width:220px; display:flex; flex-direction:column; justify-content:center; align-items:center; border-radius:16px 0 0 16px; padding:32px 12px;">
      <div style="width:100%; text-align:center;">
        <span style="font-size:1.5rem; font-weight:700; letter-spacing:1px;">Welcome to Balela Secondary School</span>
      </div>
    </div>
    <!-- Login Form Side -->
    <div class="card shadow-none rounded-0" style="width:60%; min-width:220px; border:none; border-radius:0 16px 16px 0; padding:32px 24px;">
      <div class="card-body p-0">
        <div class="text-center mb-4">
          <h2 class="fw-bold mb-1" style="color:#092ace;">Login</h2>
        </div>
        <?php if (!empty($all_err)) { ?>
          <div class="alert alert-danger fw-bold text-center">
            <?php echo $all_err; ?>
          </div>
        <?php } ?>
        <form action="" method="POST" novalidate>
          <div class="mb-3">
            <label for="username" class="form-label fw-semibold">Username</label>
            <input type="text" class="form-control <?php echo !empty($username_err) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?php echo $username; ?>">
            <div class="invalid-feedback">
              <?php echo $username_err; ?>
            </div>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Password</label>
            <div class="input-group">
              <input type="password" class="form-control pass-input" id="password" name="password">
              <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                <i class="fas fa-eye"></i>
              </button>
              <div class="invalid-feedback d-block">
                <?php echo $password_err; ?>
              </div>
            </div>
          </div>
          <button type="submit" class="btn w-100 fw-bold mt-2 mb-3" name="login" style="background:#092ace; color:#fff; border:none;">Login</button>
          <div class="text-center">
            <a href="index.php" class="btn btn-link text-decoration-none fw-semibold" style="color:#092ace;">← Back To Homepage</a>
          </div>
        </form>
      </div>
    </div>
  </div>
 
</div>
</div>
<?php
include __DIR__ . '/login/loginFooter.php';
?>
