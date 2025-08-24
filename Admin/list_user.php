<?php
include('adminHeader.php');
?> 
<div class="container">
  <div class="page-inner">
    <div class="page-header">
     <h3 class="fw-bold mb-3">All Staff</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Account Management</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">View All Staff</a></li>
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
    </form>        
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
            $users = getAllUsers();
        }
?>

<div class="row">
    <?php
    if (!empty($users)) {
        foreach ($users as $user) {
    ?>
<div class="col-xl-3 col-lg-4 col-sm-6 user-card"
     data-idnumber="<?= strtolower($user['idNumber']) ?>"
     data-username="<?= strtolower($user['username']) ?>"
     data-usertype="<?= strtolower($user['user_type']) ?>"
     data-firstname="<?= strtolower($user['first_name']) ?>">
  
  <div class="card contact_list text-center">
    <div class="card-body">
      <div class="user-content">
        <div class="user-info">
          <div class="user-img">
            <img class="profile-images" src="<?php echo $user["profile_picture"]; ?>" alt="Profile Picture" width="100" height="100">
          </div>
          <div class="user-details">
            <h4 class="user-name mb-0"><?php echo $user["username"]; ?></h4>
            <p><?php echo getRoleNameById($user["user_type"]); ?></p>
          </div>
        </div>
      </div>
      <a href="profiledetail.php?uid=<?php echo $user['uid']; ?>" class="btn btn-primary btn-sm w-50 me-2"><i class="fa-solid fa-user me-2"></i>Detail</a>                                                       
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
include('footer.php');
?>
