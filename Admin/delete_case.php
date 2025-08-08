<?php
include('adminHeader.php');
?>
<!-- Page Header -->
<div class="container">
         <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Delete case</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Manage cases</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Delete case</a></li>
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
            <h4 class="mb-0">View Case</h4>
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
      $success = $allErr = "";  
      if (isset($_GET["duid"])) {
        $duid = basics($_GET["duid"]);  
        $_SESSION["duid"] = $duid;      
    
        // Fetch case_id BEFORE delete (so we can show it)
        $caseQuery = mysqli_query($conn, "SELECT case_id FROM `case` WHERE cid = '$duid'");
        $caseData = mysqli_fetch_assoc($caseQuery);
        $caseIdDisplay = $caseData ? $caseData['case_id'] : 'Unknown';
    
        // Now delete
        $deleteResult = deleteCaseById($duid);
    
        if ($deleteResult === 1) {
            $success = "Case with case_id = {$caseIdDisplay} has been deleted successfully.";
        } elseif ($deleteResult === -1) {
            $allErr = "Cannot delete case with case_id = {$caseIdDisplay} because it has related records in case_info.";
        } else {
            $allErr = "Unable to delete case information for case_id = {$caseIdDisplay}.";
        }
    }
      ?>
<?php
  if (isset($_SESSION["uid"])) {
      if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
          $searchTerm = $_GET['search'];
          $cases = searchCases($searchTerm);
      } else {
          $cases = getAllCases();
      }
?>      
<div class="card-body">
  <div class="table-responsive">
   <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%; background-color: white;">
  <thead class="table-secondary">
     <tr>
    <th style="border: 2px solid black;">#</th>
    <th style="border: 2px solid black;">case_id</th>
    <th style="border: 2px solid black;">Plaintiff</th>
    <th style="border: 2px solid black;">defendant</th>
    <th style="border: 2px solid black;">case_status</th>
    <th style="border: 2px solid black;">Actions</th>
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
    <td style="border: 2px solid black;"><?php echo $no; ?></td>
    <td style="border: 2px solid black;"><?php echo $case["case_id"]; ?></td>
    <td style="border: 2px solid black;"><?php echo $case["plaintiff"]; ?></td>
    <td style="border: 2px solid black;"><?php echo $case["defendant"]; ?></td>
    <td style="border: 2px solid black;"><?php echo $status; ?></td>
    <td style="border: 2px solid black;"> 
    <a href="#" class="btn btn-danger shadow btn-xs sharp" onclick="deleteCase(<?php echo $case['cid']; ?>)"><i class="fa fa-trash"></i></a>
    <a href="delete_litigant.php?case_id=<?php echo $case['case_id']; ?>">
    <i class="fas fa-file-signature fa-lg"></i></a>
    </td>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteCase(id) {
  Swal.fire({
    title: 'Are you sure?',
    text: "This will permanently delete the case.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      // Attempt deletion
      window.location.href = "?duid=" + id;
    }
  });
}

// Show error alert if case has associated data
<?php if (strpos($allErr, "Cannot delete case with case_id") !== false) { ?>
Swal.fire({
  icon: 'error',
  title: 'Deletion Blocked',
  text: '<?php echo $allErr; ?>',
});
<?php } ?>


</script>
