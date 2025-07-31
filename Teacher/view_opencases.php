<?php
include('cdheader.php');
?>
<!-- Page Content -->
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Forms</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Forms</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Basic Form</a></li>
            </ul>
        </div>
<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="row">
      <div class="col-12 col-sm-12 col-lg-12">
        <div class="card ">
        <div class="card-header">
        <div class="row w-100 align-items-center">
              <div class="col-12 col-md-6 mb-2 mb-md-0">
                <h4 class="mb-0">View all cases</h4>
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
                $cases = searchCases($searchTerm);
            } else {
                $cases = getAllOpenCases();
            }
        ?>
<div class="card-body">
  <div class="table-responsive">
    <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
    <thead>
     <tr>
         <th>#</th>
         <th>case_id</th>
         <th>Plaintiff</th>
         <th>defendant</th>
         <th>case_status</th>
         <th>Actions</th>
     </tr>
   </thead>
  <tbody>
  <?php
    $no = 1;
      if (!empty($cases)) {
        foreach ($cases as $case) {
          $status = case_status($case['case_status']);

      ?>  
    <tr>
        <td><?php echo $no; ?></td>
        <td><?php echo $case['case_id']; ?></td>
        <td><?php echo $case['plaintiff']; ?></td>
        <td><?php echo $case['defendant']; ?></td>
        <td><?php echo $status; ?></td>
        <td>
        <a href="case_detail.php?case_id=<?php echo $case['case_id']; ?>&cid=<?php echo $case['cid']; ?>">
         <i class="fas fa-edit"></i> Details
        </a>
        </td>
    </tr>
    <?php  
       $no++; }
   ?>       
   <?php
              }
          }  ?>
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
</section>
</div>       
</div>
</div>

<?php
include('../admin/footer.php');
?>