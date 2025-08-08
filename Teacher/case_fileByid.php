<?php
include('loHeader.php');
?>
<!-- Page Header -->
<div class="container">
         <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">FIle Detail</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Manage Cases</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">File Detail</a></li>
    </ul>
 </div>
<!-- End Page Header -->

<!-- Main Content -->
<?php
$case_id = null; 
$cid = null;
 if (isset($_GET['case_id'])) {
    $case_id = $_GET['case_id'];
 } 
  if(isset($_GET['cid'])) {
    $cid = $_GET['cid'];
  }
?>
<div class="main-content">
 <section class="section">
   <div class="row">
     <div class="col-12 col-sm-12 col-lg-12">
      <div class="card ">
        <div class="card-header">
          <div class="row w-100 align-items-center">
      <div class="col-12 col-md-6 mb-2 mb-md-0">
        <h4 class="mb-0">View <?php echo $case_id ?> files</h4>
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
       $searchTerm = trim($_GET['search']);
       $cases = searchCaseInfo($searchTerm);
   } elseif (!empty($case_id)) {
       $cases = getAllCaseInfoById($case_id);
   } else {
    $attachedFiles = getAttachFilesByCaseId($case_id);
   }          
  ?>
<div class="card-body">
  <div class="table-responsive">
      <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%; background-color: white;">
  <thead class="table-secondary">
      <tr>
    <th style="border: 2px solid black;">#</th>
    <th style="border: 2px solid black;">files</th>
    <th style="border: 2px solid black;">record_date</th>
    
    </tr>
    </thead>
    <tbody>
<?php
$no = 1;
if (!empty($cases)) {
  foreach ($cases as $case) {
      $filePath = $case["file"];
      if (!empty($filePath)) {
?>
<tr>
  <td style="border: 2px solid black;"><?php echo $no++; ?></td>
  <td style="border: 2px solid black;">
    <a href="<?= htmlspecialchars($filePath) ?>" target="_blank"><?= htmlspecialchars(basename($filePath)) ?></a>
 </td>
  <td style="border: 2px solid black;"><span class="text-info">File uploaded at case registration</span></td>
 
</tr>
<?php
        }
    }
}
?>
        <?php
        $no++;
    }
$attachedFiles = getAttachFilesByCaseId($case_id);
if (!empty($attachedFiles)) {
  foreach ($attachedFiles as $file) {
    $attachFilePath = $file["file"];
    $attachFileName = basename($attachFilePath);
    $recordDate = $file["record_date"];
        ?>
    <tr>
      <td style="border: 2px solid black;"><?php echo $no; ?></td>
      <td style="border: 2px solid black;">
        <?php
        if (!empty($attachFilePath) && file_exists($attachFilePath)) {
            echo '<a href="' . $attachFilePath . '" target="_blank">' . htmlspecialchars($attachFileName) . '</a>';
        } else {
            echo '<span class="text-muted">Attached file not found</span>';
        }
        ?>
      </td>
      <td style="border: 2px solid black;"><?php echo !empty($recordDate) ? htmlspecialchars($recordDate) : '<span class="text-muted">No date found</span>'; ?></td>
 
    </tr>  
  <?php
  $no++;
  }
}
?>
</tbody>
</table>
</div>    
  <div class="text-left mt-4">
                <a href="case_detail.php?case_id=<?= $case['case_id'];?>&cid=<?= $case['cid'];?>" class="btn btn-outline-primary">
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