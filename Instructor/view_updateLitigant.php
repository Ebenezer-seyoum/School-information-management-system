<?php
include('adminHeader.php');
?>
<!-- Page Header -->
<div class="container">
         <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Update Cases</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Manage Cases</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Update Case</a></li>
    </ul>
 </div>
<!-- End Page Header -->

 <!-- Main Content -->
<?php
$cid = null;
$case_id = null;
 if (isset($_GET['case_id'])) 
    $case_id = $_GET['case_id'];
if (isset($_GET['cid'])) 
    $cid = $_GET['cid'];
?>
<div class="main-content">
 <section class="section">
   <div class="row">
     <div class="col-12 col-sm-12 col-lg-12">
      <div class="card ">
        <div class="card-header">
        <div class="row w-100 align-items-center">
          <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h4 class="mb-0">view <?php echo $case_id; ?> case detail</h4>
          </div>
  <div class="col-12 col-md-6">
  <form method="GET">
  <input type="hidden" name="case_id" value="<?php echo $case_id; ?>">
  <div class="input-group">
    <input type="text" name="search" class="form-control" placeholder="Search by case_id...." 
      value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
  </div>
</form>
</div>
<?php
  if (isset($_SESSION["uid"])) {
      if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
          $searchTerm = $_GET['search'];
          $cases = searchCaseInfo($searchTerm);
      } else {
          $cases = getAllCaseInfoById($case_id);
      }
?>       
<div class="card-body">
  <div class="table-responsive">
     <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%; background-color: white;">
  <thead class="table-secondary">
      <tr>
    <th style="border: 2px solid black;">#</th>
    <th style="border: 2px solid black;">first_name</th>
    <th style="border: 2px solid black;">father_name</th>
    <th style="border: 2px solid black;">grandfather_name</th>
    <th style="border: 2px solid black;">Edit</th>
     </tr>
    </thead>
   <tbody>  
   <?php
  $no = 1;
  if (!empty($cases)) {
   foreach ($cases as $case) {
    $status = case_status($case['case_status']);
         $region = getRegionById($case["region"]);
            $zone = getZoneById($case["zone"]);
            $woreda = getWoredaById($case["woreda"]);                                           
?>                                                                
   <tr>
    <td style="border: 2px solid black;"><?php echo $no; ?></td>
    <td style="border: 2px solid black;"><?php echo $case["first_name"]; ?></td>
    <td style="border: 2px solid black;"><?php echo $case["father_name"]; ?></td>
    <td style="border: 2px solid black;"><?php echo $case["grandfather_name"]; ?></td>
    <td style="border: 2px solid black;"> <a href="update_litigant.php?kid=<?php echo $case["kid"];?>"class="btn btn-primary btn-sm"><i class="fas fa-edit"></i>Edit</a>
     </tr>
     <?php  
         $no++; }
     ?>       
     <?php } }  ?>
         <?php if (empty($cases)) { ?>
           <tr><td colspan="13" class="text-center text-danger" style="border: 2px solid black;">No cases found.</td></tr>
          <?php } ?>                                      
</tbody>
</table>
</div> 
            <div class="text-left mt-4">
                <a href="case_Detail.php?case_id=<?= $case_id;?>&cid=<?= $cid;?>?>" class="btn btn-outline-primary">
                    <i class="fa fa-arrow-left"></i> Back 
                </a>
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
include 'footer.php'; 
?>