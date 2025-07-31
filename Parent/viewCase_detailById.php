<?php
include('loHeader.php');
?>
<!-- Page Header -->
<div class="container">
         <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">View Litigant</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Case Management</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">View Litigant</a></li>
    </ul>
 </div>
<!-- End Page Header -->
 <!-- Main Content -->
<?php
$case_id = null;
 if (isset($_GET['case_id'])) 
    $case_id = $_GET['case_id'];
$cid = null;
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
          <h4 class="mb-0">View <?php echo $case_id ?> Litigant</h4>
        </div>
<div class="col-12 col-md-6">
<form method="GET">
  <input type="hidden" name="case_id" value="<?php echo $case_id; ?>">
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
       $searchTerm = trim($_GET['search']);
       $cases = searchCaseInfo($searchTerm);
   } else  {
       $cases = getAllCaseInfoById($case_id);
    } 
?>     
<div class="card-body">
<div class="table-responsive">
  <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%;">
  <thead class="table-secondary">
    <tr>
      <th style="border: 2px solid black;">#</th>
      <th style="border: 2px solid black;">First Name</th>
      <th style="border: 2px solid black;">Father Name</th>
      <th style="border: 2px solid black;">Download</th>
      <th style="border: 2px solid black;">View</th>
  </thead>
<tbody>
    <?php
    $no = 1;
if (!empty($cases)) {
  foreach ($cases as $case) {
    ?>
    <tr>
      <td style="border: 2px solid black;"><?php echo $no; ?></td>
      <td style="border: 2px solid black;"><?php echo $case["first_name"]; ?></td>
      <td style="border: 2px solid black;"><?php echo $case["father_name"]; ?></td>
       <td style="border: 2px solid black;">
        <a href="viewLitigantDetail.php?kid=<?= $case['kid']; ?>" class="btn btn-sm btn-info">
          <i class="fa fa-eye"></i> View Details
        </a>
      </td>
      <td style="border: 2px solid black;">
        <form method="POST" action="generateLitigantPdf.php" target="_blank">
          <input type="hidden" name="case_id" value="<?= $case_id; ?>">
          <input type="hidden" name="kid" value="<?= $case['kid']; ?>">
          <button type="submit" class="btn btn-sm btn-danger">
            <i class="fa fa-file-pdf"></i> Download PDF
          </button>
        </form>
      </td>
    </tr>
<?php
  $no++;
 }
 }} else {
      echo '<tr><td colspan="5" style="border: 2px solid black;" class="text-center text-danger">No cases found.</td></tr>';
    }
    ?>
  </tbody>
</table>

</div> 
  <div class="text-left mt-4">
                <a href="AllCase_detail.php?case_id=<?= $case_id;?>&cid=<?= $cid;?>" class="btn btn-outline-primary">
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
include '../Admin/footer.php'; 
?>