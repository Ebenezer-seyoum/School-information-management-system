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
              <h3 class="fw-bold mb-3">Update class</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Manage class</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">View class</a></li>
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
      <h4 class="mb-0">View all Class</h4>
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
            $users = getAllSections();
        
 ?>
<div class="card-body">
  <div class="table-responsive">
    <table class="table table-hover align-middle text-center" id="userTable"  style="border: 2px solid black; border-collapse: collapse; width: 60%; background-color: white;">
  <thead class="table-secondary">
      <tr>
    <th style="border: 2px solid black;">#</th>
    <th style="border: 2px solid black;">Section_name</th>
    <th style="border: 2px solid black;">class_type</th>
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
    <td style="border: 2px solid black;"><?php echo $user["section_name"]; ?></td>
    <td style="border: 2px solid black;"><?php echo $user["class_type"]; ?></td>
   
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
