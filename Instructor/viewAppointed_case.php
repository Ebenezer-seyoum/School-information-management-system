<?php
include('judgeHeader.php');
?>
<?php
if (isset($_SESSION['uid']))
    $judge_id= $_SESSION['uid'];
?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Appointed Cases</h3>
         <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Case status</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Appointed Cases</a></li>
        </ul>
   </div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <div class="row w-100 align-items-center">
      <div class="col-12 col-md-6 mb-2 mb-md-0">
        <h4 class="mb-0">View Appointed cases</h4>
      </div>
<div class="col-12 col-md-6">
  <form method="GET">
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
              $cases = searchAppointedCase($searchTerm);
          } else {
              $cases = getAssignedCasesWithAppointments();
          }
    ?>
</div>
<div class="card-body">
  <div class="table-responsive">
    <table class="table table-bordered">
      <thead>
          <tr>
              <th>#</th>
              <th>Case ID</th>
              <th>Plaintiff</th>
              <th>Defendant</th>                                       
              <th>Appointment Date</th>
              <th>Appointment Reason</th>
              <th>Case Status</th>           
          </tr>
      </thead>
    <tbody>
<?php
    $no = 1;
      if (!empty($cases)) {
        foreach ($cases as $case) {
      ?> 
<tr>
    <td><?= $no++; ?></td>
    <td><?= $case['case_id']; ?></td>
    <td><?= $case['plaintiff']; ?></td>
    <td><?= $case['defendant']; ?></td>                                     
    <td><?= $case['appointment_date']; ?></td>
    <td><?= $case['appointment_reason']; ?></td>                                         
    <td>
     <a href="viewCase_detailById.php?case_id=<?php echo $case["case_id"]; ?>">
         <i class="fas fa-edit"></i> Details
     </a>
    </td>
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
<?php
include('../admin/footer.php');
?>