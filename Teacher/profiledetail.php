<?php
include('cdheader.php');
?>
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
    <?php
    $uid = "";

    //get the user id from the database
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
            <div class="col-xl-4 col-lg-4">
              <label class="form-label text-primary">Profile picture</label>
              <div class="avatar-upload">
                <div class="avatar-preview">
                  <div class="user-img">
                    <img class="profile-images" name="profile_picture" src="<?php echo $userProfile["profile_pic"]; ?>" alt="Profile Picture" width="100" height="100">
                  </div>
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
                      <a href="view_judge.php" class="btn btn-secondary">Back</a>
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
  include('../admin/footer.php');
  ?>