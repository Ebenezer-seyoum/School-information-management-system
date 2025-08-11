<?php
include('adminHeader.php');
?>
<?php
$success = $allErr = "";  
$profile = getUserByID($_SESSION["uid"]);
if (isset($_SESSION["uid"]) &&  ($profile["user_type"] == "Admin")) {
?>
<!-- Page Header -->
<div class="container">
         <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">feedback</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">View Feedback</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Customer Feedback</a></li>
    </ul>
 </div>
<!-- End Page Header -->

 <!-- Main Content -->
<div class="main-content">
<section class="section">
 <div class="row">
  <div class="col-12 col-sm-12 col-lg-12">
   <div class="card ">
     <div class="card-header">
      <div class="row w-100 align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
          <h4 class="mb-0">View Feedback</h4>
        </div>
   <div class="col-12 col-md-6">
     <form method="GET">
       <div class="input-group">
         <input type="text" name="search" class="form-control" placeholder="Search by Name, subject ...." 
           value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
         <button class="btn btn-primary" type="submit">
           <i class="fa fa-search"></i>
         </button>
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
  if (isset($_SESSION["uid"])) {
      if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
          $searchTerm = $_GET['search'];
          $feedbacks = searchFeedbacks($searchTerm);
      } else {
          $feedbacks = getAllFeedbacks();
      }
?>
       
<div class="card-body">
  <div class="table-responsive">
 <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%;">
  <thead class="table-secondary">
      <tr>
    <th style="border: 2px solid black;">#</th>
    <th style="border: 2px solid black;">full_name</th>
    <th style="border: 2px solid black;">email</th>
    <th style="border: 2px solid black;">Subject</th>
    <th style="border: 2px solid black;">Message</th>
     </tr>
    </thead>
   <tbody>  
   <?php
    $no = 1;
      if (!empty($feedbacks)) {
        foreach ($feedbacks as $feedback) {  
      ?>                                                                 
   <tr>
    <td style="border: 2px solid black;"><?php echo $no; ?></td>
    <td style="border: 2px solid black;"><?php echo $feedback["full_name"]; ?></td>
    <td style="border: 2px solid black;"><?php echo $feedback["email"]; ?></td>
    <td style="border: 2px solid black;"><?php echo $feedback["subject"]; ?></td>
    <td style="border: 2px solid black;"><?php echo $feedback["message"]; ?></td>
   </tr>
   <?php  
       $no++; }
   ?>       
   <?php } }  ?>
<?php if (empty($feedbacks)) { ?>
  <tr><td colspan="13" class="text-center text-danger" style="border: 2px solid black;">No feedback found.</td></tr>
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
