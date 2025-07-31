<?php
include('adminHeader.php');
?>
<!-- page header -->
<div class="container">
 <div class="page-inner">
   <div class="page-header">
     <h3 class="fw-bold mb-3">View Account</h3>
     <ul class="breadcrumbs mb-3">
       <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
       <li class="separator"><i class="icon-arrow-right"></i></li>
       <li class="nav-item"><a href="#">users</a></li>
       <li class="separator"><i class="icon-arrow-right"></i></li>
       <li class="nav-item"><a href="#">view account</a></li>
     </ul>
  </div>
<!-- end page header -->
<?php
  $uid = "";
    if (isset($_GET["uid"])) {
      $uid = basics($_GET["uid"]);
      $userProfile = getUserByID($uid);
    } else {
      echo "<p>User ID not provided.</p>";
      include('footer.php');
      exit;
    }
?>

<?php
$profile = getUserByID($_SESSION["uid"]);
if (isset($_SESSION["uid"])) {
?>
<div class="main-content">
 <section class="section">
   <div class="row">
    <div class="col-xl-4 col-lg-4 mb-4">
     <div class="card shadow-sm">
       <div class="card-header">
        <h5 class="text-primary fw-bold mb-0">Profile Picture</h5>
      </div>
<div class="card-body text-center">
  <img class="rounded-rectangle border border-2 shadow-sm" name="profile_picture" src="<?php echo $userProfile["profile_pic"]; ?>" 
  alt="Profile Picture" width="150" height="150">
   </div> 
 </div>
</div>
<div class="col-8 col-sm-8 col-lg-8">
  <div class="card ">
    <div class="card-header">
      <h4>profile details</h4>
    </div>
 <div class="card-body">
   <div class="row">
     <div class="form-group col-6">
       <label for="id_number">ID Number</label>
       <input id="id_number" type="text" class="form-control" name="idNumber" value="<?php echo $userProfile["idNumber"]; ?>" readonly />
     </div>
     <div class="form-group col-6">
       <label for="first_name">First Name</label>
       <input id="first_name" type="text" class="form-control" name="first_name" value="<?php echo $userProfile["first_name"]; ?>" readonly />
     </div>
   </div>
   <div class="row">
     <div class="form-group col-6">
       <label for="father_name">Father Name</label>
       <input id="father_name" type="text" class="form-control" name="father_name" autofocus value="<?php echo $userProfile["father_name"]; ?>" readonly />
     </div>
     <div class="form-group col-6">
       <label for="grand_father_name">Grand Father Name</label>
       <input id="grand_father_name" type="text" class="form-control" name="grand_father_name" value="<?php echo $userProfile["gfather_name"]; ?>" readonly />
     </div>
   </div>
   <div class="row">
     <div class="form-group col-6">
       <label for="gender">Gender</label>
       <input type="text" class="form-control" value="<?php echo $userProfile["gender"]; ?>" readonly />
     </div>
     <div class="form-group col-6">
       <label for="Email">Email</label>
       <input id="Email" type="text" class="form-control" name="email" value="<?php echo $userProfile["email"]; ?>" readonly />
     </div>
   </div>
   <div class="row">
     <div class="form-group col-6">
       <label for="password" class="d-block">Password</label>
       <input id="password" type="text" class="form-control pwstrength" name="password" value="<?php echo $userProfile["password"]; ?>" readonly />
     </div>
     <div class="form-group col-6">
       <label for="username">username</label>
       <input id="username" type="text" class="form-control" name="username" value="<?php echo $userProfile["username"]; ?>" readonly />
     </div>
      <div class="form-group col-6">
       <label for="phone">phone</label>
       <input type="text" class="form-control" value="<?php echo $userProfile["phone"]; ?>" readonly />
     </div>
     <div class="form-group col-6">
       <label for="user_type">user_type</label>
       <input type="text" class="form-control" value="<?php echo $userProfile["user_type"]; ?>" readonly />
     </div>
     <div class="form-group col-12 mt-3">
       <a href="list_user.php" class="btn btn-secondary">Back</a>
     </div>
    </div>
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