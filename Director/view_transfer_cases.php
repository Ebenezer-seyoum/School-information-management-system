<?php
include('cdheader.php'); 
?>
<?php

?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
       <h3 class="fw-bold mb-3">View Assign Cases</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Case Management</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">View Assign Cases</a></li>
        </ul>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
      <div class="row w-100 align-items-center">
    <div class="col-12 col-md-6 mb-2 mb-md-0">
      <h4 class="mb-4">View Assign cases</h4>
    </div>
<div class="col-12 col-md-6">
  <form method="GET">
    <div class="input-group">
   <input type="text" id="caseSearch" class="form-control" 
       style="font-weight: bold;" 
       placeholder="Search by Case ID, Plaintiff, Defendant, or Status..." />
    </div>
  </form>
</div>          
<?php
  if (isset($_SESSION["uid"])) {
      if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
          $searchTerm = $_GET['search'];
          $cases = searchAssignedCase($searchTerm);
      } else {
          $cases = getAllAssignedJudges();
         
      }
?>
<div class="table-responsive">
  <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%;">
  <thead class="table-secondary">
      <tr>
      <th style="border: 2px solid black;">#</th>
      <th style="border: 2px solid black;">IdNumber</th>
      <th style="border: 2px solid black;">profile_picture</th>
      <th style="border: 2px solid black;">First Name</th>                 
      <th style="border: 2px solid black;">father Name</th>
      <th style="border: 2px solid black;">Action</th>
      </tr>
   </thead>
 <tbody>
<?php
$no = 1;
  if (!empty($cases)) {
    foreach ($cases as $case) {
?>  
<tr>
  <td style="border: 2px solid black;"><?= $no++; ?></td>
  <td style="border: 2px solid black;"><?= $case['idNumber']; ?></td>
  <td style="border: 2px solid black;"><img src="<?= $case['profile_pic']; ?>" width="40" height="40" class="rounded-circle" alt="Profile"></td>
  <td style="border: 2px solid black;"><?= $case['first_name']; ?></td>
  <td style="border: 2px solid black;"><?= $case['father_name']; ?></td>
  <td style="border: 2px solid black;"><a href="judge_cases.php?judge_id=<?= $case['uid']; ?>" class="btn btn-info btn-sm">View Cases</a></td>
  
</tr>
<?php  
  $no++; }
?>       
<?php } }  ?>
  <?php if (empty($cases)) { ?>
  <tr><td colspan="13" class="text-center text-danger">No cases found.</td></tr>
<?php } ?>

            </tbody>
        </table>
    </div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

<?php
include('../admin/footer.php'); 
?>