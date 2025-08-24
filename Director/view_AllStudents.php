<?php
include('directorHeader.php');
?> 
<div class="container">
  <div class="page-inner">
    <div class="page-header">
     <h3 class="fw-bold mb-3">View All Students</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Student Management</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">View All Students</a></li>
      </ul>
   </div>
</div>
<div class="container-fluid">
 <div class="row">
  <div class="col-6">
    <div class="page-title flex-wrap">
       <form method="GET">
    <div class="search-box">
  <div class="input-group">
    <span class="input-group-text bg-primary text-white">
      <i class="fas fa-search"></i>
    </span>
    <input type="text" name="search" id="userSearch" 
           class="form-control search-input"
           placeholder="Search by ID, Name, or Role...">
    <button class="btn btn-primary" type="button">
      Search
    </button>
        </div>        
    </div>
  </div>
</div>

<?php
 $roleName = getRoleNameById($profile["user_type"]);
    if (isset($_SESSION["uid"])) {
        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $searchTerm = $_GET['search'];
            $users = searchUsers($searchTerm);
        } else {
            $users = getAllStudents();
        }
?>

<div class="row">
    <?php
    if (!empty($users)) {
        foreach ($users as $user) {
    ?>
<div class="col-xl-3 col-lg-4 col-sm-6 user-card"
     data-idnumber="<?= strtolower($user['student_id']) ?>"
     data-username="<?= strtolower($user['first_name']) ?>"
     data-usertype="<?= strtolower($user['gender']) ?>"
     data-firstname="<?= strtolower($user['first_name']) ?>">
  
  <div class="card contact_list text-center">
    <div class="card-body">
      <div class="user-content">
        <div class="user-info">
          <div class="user-img">
            <img class="profile-images" src="<?php echo $user["student_photo"]; ?>" alt="Profile Picture" width="100" height="100">
          </div>
          <div class="user-details">
            <h4 class="user-name mb-0"><?php echo $user["first_name"]; ?> <?php echo $user["father_name"]; ?></h4>
          </div>
        </div>
      </div>
      <a href="view_AllStudentdetail.php?sid=<?php echo $user['sid']; ?>" class="btn btn-primary btn-sm w-50 me-2"><i class="fa-solid fa-user me-2"></i>Detail</a>                                                       
    </div>
  </div>
</div>

    <?php }
    } else {
        echo "<p class='mt-3 text-danger'>No users found.</p>";
    }
    ?>
</div>
        <?php
        } else {
            // User not logged in
            // header('location: ../index.php');
        }
        ?>
    </div>
</div>

<?php
include('../Admin/footer.php');
?>
