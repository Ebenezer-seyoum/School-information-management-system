<?php
include('directorHeader.php');
?>
<?php
$success = $allErr = "";
$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);
if (isset($_SESSION["uid"]) && ($roleName == "Director")) {
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
              <h3 class="fw-bold mb-3">Update Student</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Manage Student</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Update Student</a></li>
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
<?php if (!empty($success)) { ?>
  <div class=" form-control bg-success">
<?php echo $success; ?>
  </div>
<?php  } ?>
<?php if (!empty($allErr)) { ?>
  <div class=" form-control bg-danger">
<?php echo $allErr; ?>
  </div>
<?php  } ?>
       
<?php
            $users = getAllStudents();
        
 ?>
<div class="card-body">
  <div class="table-responsive">
    <table class="table table-hover align-middle text-center" id="userTable"  style="border: 2px solid black; border-collapse: collapse; width: 60%; background-color: white;">
  <thead class="table-secondary">
      <tr>
    <th style="border: 2px solid black;">#</th>
    <th style="border: 2px solid black;">Student_id</th>
    <th style="border: 2px solid black;">Student_photo</th>
    <th style="border: 2px solid black;">First Name</th>
    <th style="border: 2px solid black;">Father Name</th>
    <th style="border: 2px solid black;">View Details</th>
    <th style="border: 2px solid black;">Actions</th>
     </tr>
    </thead>
   <tbody>  
   <?php
      $no = 1; 
     if (!empty($users)) {
       foreach ($users as $user) { 
      ?>                                                                  
   <tr>
    <td style="border: 2px solid black;"><?php echo $no; ?></td>
    <td style="border: 2px solid black;"><?php echo $user["student_id"]; ?></td>
    <td style="border: 2px solid black;"><img class="profile-img" src="<?php echo $user["student_photo"]; ?>" alt="Profile Picture" width="100" height="100"></td>
    <td style="border: 2px solid black;"><?php echo $user["first_name"]; ?></td>
    <td style="border: 2px solid black;"><?php echo $user["father_name"]; ?></td>
    <td style="border: 2px solid black;">
        <a href="view_studentDetail.php?sid=<?= $user['sid']; ?>" class="btn btn-sm btn-info">
          <i class="fa fa-eye"></i> Details
        </a>
      </td>
    <td style="border: 2px solid black;">
  <a href="updateStudent.php?sid=<?php echo $user['sid']; ?>">
    <i class="fas fa-file-signature fa-lg"></i> 
    </a>
   </td>
</tr>
<?php  
  $no++; }
  }
?>       
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
include('../Admin/footer.php');
?>
