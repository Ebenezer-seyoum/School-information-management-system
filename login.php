<?php
include '../School-Information-Management-System/login/loginHeader.php';
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
     
    // Check if the user is already logged in
    // if (checkIs_loggedIn(1)) {
      // $all_err = "You are already login.";
    // } else {   
    // Check user credentials
    if ($test == true) {
        if (checkUserByUsername($username)) {
            $user_data = checkUserCredentials($username , $password);
            if ($user_data) {
                if ($user_data['user_status'] == 2) {
                    $all_err = "This account is deactivated. Please contact the admin.";
                } else if ($user_data['user_status'] == 0 || $user_data['user_status'] == 1) {
                    $_SESSION["uid"] = $user_data['uid'];
                    updateUserStatus(1, $_SESSION["uid"]);
                   $roleName = getRoleNameById($user_data['user_type']);

if ($roleName === "Admin") {
    header('location: Admin/admin.php');
} else if ($roleName === "Director") {
    header('location: Director/director.php');
} else if ($roleName === "Judge") {
    header('location: judge/judge.php');
} else if ($roleName === "Case_distributer") {
    header('location: case_distributer/case_distributer.php');
} else if (strtolower($roleName) === "president") {
    header('location: president/president.php');
}

                } 
            } else {
                $all_err = "There is no user associated with the given information";
            }
        } else {
            $all_err = "There is no user associated with the given information";
        }
    }
 }
// }
?>
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">Balela Secondary School</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card shadow-lg rounded-4 p-4" style="max-width: 420px; width: 100%;">
    <div class="card-body">
      <div class="text-center mb-4">
        <h2 class="fw-bold mb-1">Login</h2>     
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

     
        <button type="submit" class="btn btn-primary w-100 fw-bold mt-2 mb-3" name="login">Login</button>
        <div class="text-center">
          <a href="index.php" class="btn btn-link text-decoration-none fw-semibold">← Back To Homepage</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php
include '../School-Information-Management-System/login/loginFooter.php';
?>
