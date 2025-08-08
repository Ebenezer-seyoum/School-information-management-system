<?php
include('adminHeader.php');
?>
<?php
$profile = getUserByID($_SESSION["uid"]);
if (isset($_SESSION["uid"]) &&  ($profile["user_type"] == "Admin")) {
?>
<!-- CSS for profile image -->
<style>
  .profile-img {
    width: 30px; 
    height: 30px;
    border-radius: 50%; 
    object-fit: cover; 
}
</style>
<!-- end CSS for profile image -->
 
<!-- Page Header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Manage Account</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Account</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Delete Account</a></li>
    </ul>
 </div>
<!-- End Page Header -->

 <!-- Main Content -->
 <div class="main-content">
  <section class="section">
    <div class="row">
      <div class="col-12">
       <div class="card">
         <div class="card-header">
           <div class="row w-100 align-items-center">
             <div class="col-12 col-md-6 mb-2 mb-md-0">
              <h4 class="mb-0">View all users</h4>
            </div>
 <div class="col-12 col-md-6">
   <form method="GET">
     <div class="input-group">
      <input type="text" name="search" id="userSearch" class="form-control" 
       style="font-weight: bold;" placeholder="Search by IdNumber, Name or user_type....">
     </div>
   </form>
  </div>
</div> 
</div>

<?php
$success = $allErr = "";  
if (isset($_GET["duid"])) {
  $_SESSION["duid"] = basics($_GET["duid"]);
  $duid = $_SESSION["duid"];
  $userQuery = mysqli_query($conn, "SELECT idNumber, username FROM users WHERE uid = '$duid'");
  $userData = mysqli_fetch_assoc($userQuery);
  $idNumberDisplay = $userData['idNumber'];
  $usernameDisplay = $userData['username'];  
  if (isJudgeAssigned($duid)) {
    $allErr = "The user (ID: {$idNumberDisplay}, Username: {$usernameDisplay}) cannot be deleted as they are currently assigned to cases.";
  } else {
      if (deleteUserById($duid) == 1) {
          $success = "User (ID Number = {$idNumberDisplay}, Username = {$usernameDisplay}) has been deleted successfully.";
      } else {
          $allErr = "Unable to delete user information for (ID Number = {$idNumberDisplay}, Username = {$usernameDisplay}).";
      }
  }
}   
?>
<?php
if (isset($_SESSION["uid"])) {
  if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
      $searchTerm = $_GET['search'];
      $users = searchUsers($searchTerm);
  } else {
      $users = getAllUsers();
  }
?>      
<div class="card-body">
  <div class="table-responsive">
 <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%; background-color: white;">
  <thead class="table-secondary">
      <tr>
    <th style="border: 2px solid black;">#</th>
    <th style="border: 2px solid black;">ID Number</th>
    <th style="border: 2px solid black;">profile_pic</th>
    <th style="border: 2px solid black;">First Name</th>
    <th style="border: 2px solid black;">Father Name</th>
    <th style="border: 2px solid black;">User Status</th>
    <th style="border: 2px solid black;">View Details</th>
    <th style="border: 2px solid black;">Actions</th>
     </tr>
    </thead>
   <tbody>  
<?php
$no = 1;
 if (!empty($users)) {
  foreach ($users as $user) {
   $status = user_status($user["user_status"]);     
?>  
  <tr>
    <td style="border: 2px solid black;"><?php echo $no; ?></td>
    <td style="border: 2px solid black;"><?php echo $user["idNumber"]; ?></td>
    <td style="border: 2px solid black;"><img class="profile-img" src="<?php echo $user["profile_pic"]; ?>" alt="Profile Picture" width="100" height="100"></td>
    <td style="border: 2px solid black;"><?php echo $user["first_name"]; ?></td>
    <td style="border: 2px solid black;"><?php echo $user["father_name"]; ?></td>
    <td style="border: 2px solid black;"><?php echo $status; ?>
    <td style="border: 2px solid black;">
      <a href="viewUserDetail.php?uid=<?= $user['uid']; ?>" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> </a>
    </td>
      <td style="border: 2px solid black;">
    <a href="#" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash fa-lg" onclick="deleteUser(<?php echo $user['uid']; ?>)"></i></a>
    </td>
  </tr>
   <?php  
     $no++; }
   ?>       
  <?php } }  ?>
<?php if (empty($users)) { ?>
  <tr><td colspan="13" class="text-center text-danger" style="border: 2px solid black;">No users found.</td></tr>
<?php } ?>                               
    </tbody>
  </table>
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
} else echo "You are not authorized to view this page.";
?>
<?php
include('footer.php');
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteUser(id) {
  Swal.fire({
    title: 'Are you sure?',
    text: "The user's data will be permanently removed.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "?duid=" + id;
    }
  });
}
// Show SweetAlert popups based on PHP success/error
<?php if (!empty($success)) { ?>
Swal.fire({
  icon: 'success',
  title: 'User Successfully Deleted!',
  text: '<?php echo addslashes($success); ?>',
  confirmButtonColor: '#3085d6'
});
<?php } ?>

<?php if (!empty($allErr)) { ?>
Swal.fire({
  icon: 'error',
  title: 'Unable to Delete User!',
  text: '<?php echo addslashes($allErr); ?>',
  confirmButtonColor: '#d33'
});
<?php } ?>
</script>

