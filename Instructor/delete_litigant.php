<?php
include('adminHeader.php');
?>
<!-- Page Header -->
<div class="container">
         <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Delete Litigant</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Manage Cases</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">delete Litigant</a></li>
    </ul>
 </div>
<!-- End Page Header -->

 <!-- Main Content -->
<?php
 $case_id = isset($_GET['case_id']) ? $_GET['case_id'] : null;
?>
<div class="main-content">
 <section class="section">
   <div class="row">
     <div class="col-12 col-sm-12 col-lg-12">
      <div class="card ">
        <div class="card-header">
        <div class="row w-100 align-items-center">
          <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h4 class="mb-0">Case Detail of <?php echo $case_id ?></h4>
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
    $_SESSION["duid"] = basics($_GET["duid"]);
    if (deleteLitigantById($_SESSION["duid"]) == 1) {
        $success = "Litigant has been deleted successfully";

        // check the litigant count for the same case_id
        $caseId = $_GET["case_id"];
        $sql = "SELECT 
            SUM(CASE WHEN litigant_type = 'plaintiff' THEN 1 ELSE 0 END) AS plaintiff_count,
            SUM(CASE WHEN litigant_type = 'defendant' THEN 1 ELSE 0 END) AS defendant_count 
            FROM case_info 
            WHERE case_id = '$caseId'";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $plaintiffCount = $row['plaintiff_count'];
            $defendantCount = $row['defendant_count'];
            if ($plaintiffCount < 1 || $defendantCount < 1) {
                $update = "UPDATE `case` SET case_status = 0 WHERE case_id = '$caseId'";
                mysqli_query($conn, $update);
            }
        }
    } else {
        $allErr = "Unable to delete litigant information";
    }
}
  ?>
<?php
  if (isset($_SESSION["uid"])) {
      if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
          $searchTerm = $_GET['search'];
          $cases = searchCases($searchTerm);
      } else {
          $cases = getAllCaseInfoById($case_id);
      }
?>
      <?php if (!empty($success)) { ?>
      <div class=" form-control bg-success" id="successMessage"><?php echo $success; ?></div>
      <?php  } ?>
      <?php if (!empty($allErr)) { ?>
      <div class=" form-control bg-danger" id="errorMessage"><?php echo $allErr; ?> </div>
      <?php  } ?>
       
<div class="card-body">
  <div class="table-responsive">
   <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%; background-color: white;">
  <thead class="table-secondary">
      <tr>
    <th style="border: 2px solid black;">#</th>
    <th style="border: 2px solid black;">first_name</th>
    <th style="border: 2px solid black;">father_name</th>
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
    <td style="border: 2px solid black;"><?php echo $case["first_name"]; ?></td>
    <td style="border: 2px solid black;"><?php echo $case["father_name"]; ?></td>
   
    <td style="border: 2px solid black;"> 
    <a href="#" class="btn btn-danger shadow btn-xs sharp" onclick="deleteLitigant(<?php echo $case['kid']; ?>, '<?php echo $case['case_id']; ?>')">
    <i class="fa fa-trash"></i>
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
  <div class="text-left mt-4">
                <a href="delete_case.php?>" class="btn btn-outline-primary">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteLitigant(id, case_id) {
  Swal.fire({
    title: 'Are you sure?',
    text: "This will permanently delete the litigant file",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',  
    cancelButtonColor: '#3085d6', 
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "?duid=" + id + "&case_id=" + case_id;
    }
  });
}
</script>
