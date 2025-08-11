<?php
include('adminHeader.php');
$test = true;
$allErr = $success = "";
$record_date_err = $file_err = $case_id_err = $file_pages_err = "";
$case_id = $cid  = $file = $file_pages = "";
if (isset($_GET['case_id']) && isset($_GET['cid'])) {
    $case_id = $_GET['case_id'];
    $cid = $_GET['cid'];
}
$file_pages = getFilePagesByCaseInfoId($cid);
$fid = null;
if (isset($_GET['fid'])) {
$currentFile = '';
$record_date = '';
$fid = $_GET['fid'] ?? null;
if ($fid) {
    $fileData = getAttachFileByFid($fid);
    if ($fileData) {
        $currentFile = $fileData['file'];
        $record_date = $fileData['record_date'];
        $case_id = $fileData['case_id'];
          $fromAttachTable = true; 
    } else {
        $allErr = "Invalid file ID (fid) provided.";
    }
}
}elseif (isset($_GET['kid'])) {
    $kid = $_GET['kid'];
    $OrignalData = getOriginalFileByFid($kid);
if ($OrignalData) {
        $currentFile = $OrignalData['file'];
        $record_date = "File uploaded at case registration";
        $case_id = $OrignalData['case_id'];
          $fromAttachTable = false; 
    } else {
        $allErr = "Invalid file ID (kid) provided.";
    }

}
if ((isset($_POST["submit"]) and $_SERVER["REQUEST_METHOD"] == "POST")) {
// validate case_id
if (empty($_POST["case_id"])) {
    $case_id_err = "Case ID is required";
    $test = false;
} else {
    $case_id = $_POST["case_id"];
}
$fileUploaded = false; // flag to check if file was uploaded

if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
    $allowed = ["pdf" => "application/pdf", "doc" => "application/msword", "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document"];
    $file_name = $_FILES["file"]["name"];
    $file_type = $_FILES["file"]["type"];
    $file_size = $_FILES["file"]["size"];      
    $ext = pathinfo($file_name, PATHINFO_EXTENSION);

    if (!array_key_exists($ext, $allowed)) {
        $file_err = "Invalid file format. Only PDF, DOC, and DOCX are allowed.";
        $test = false;
    } elseif ($file_size > 5 * 1024 * 1024) {
        $file_err = "File size exceeds the maximum limit of 5MB.";
        $test = false;
    } elseif (!in_array($file_type, $allowed)) {
        $file_err = "Invalid file type.";
        $test = false;
    } else {
        $destination = "../assets/case_files/" . $file_name;
        if (!move_uploaded_file($_FILES["file"]["tmp_name"], $destination)) {
            $file_err = "Failed to upload the file.";
            $test = false;
        } else {
            $file = $destination;
            $fileUploaded = true;
        }
    }
} else {
    $file = $currentFile;
     $fileUploaded = false;
}

//validate file pages
if (empty($_POST["file_pages"])) {
    $file_pages_err = "File pages is required";
    $test = false;
} else {
    $file_pages = $_POST["file_pages"];
}
if ($fileUploaded) {
     if (isset($_GET['fid'])) {
            $fid = $_GET['fid'];
            if (updateAttachFile($fid, $record_date, $file, $file_pages)) {
                if (updateFilePagesInCaseInfo($cid, $file_pages)) {
                $success = "File updated successfully in attach_files.";
                updateFilePagesInCaseInfo($cid, $file_pages);
                header('refresh:2');
                  // Get all judges assigned to this case
    $sql_judges = "SELECT user_id FROM assigned_judges WHERE case_id = '$cid'";
    $result_judges = mysqli_query($conn, $sql_judges);
    // Prepare notification message
    $notif_msg = "A new file has been updated for Case ID: $cid.";
    // Send notification to each assigned judge
    if ($result_judges && mysqli_num_rows($result_judges) > 0) {
        while ($row = mysqli_fetch_assoc($result_judges)) {
            $judge_id = $row['user_id'];
            $sql_notif = "INSERT INTO notifications (user_id, message) VALUES ('$judge_id', '$notif_msg')";
            mysqli_query($conn, $sql_notif);
        }
    }
}
            }  else { $allErr = "File attached but failed to update file pages.";
}
}
elseif (isset($_GET['kid'])) {
            // Update case_info original file
            if (updateOriginalFile($cid, $file, $file_pages)) {
                $success = "Original case_info file updated successfully.";
                 updateFilePagesInCaseInfo($cid, $file_pages);
                header('refresh:2');
                  // Get all judges assigned to this case
    $sql_judges = "SELECT user_id FROM assigned_judges WHERE case_id = '$cid'";
    $result_judges = mysqli_query($conn, $sql_judges);
    // Prepare notification message
    $notif_msg = "A new file has been updated for Case ID: $cid.";
    // Send notification to each assigned judge
    if ($result_judges && mysqli_num_rows($result_judges) > 0) {
        while ($row = mysqli_fetch_assoc($result_judges)) {
            $judge_id = $row['user_id'];
            $sql_notif = "INSERT INTO notifications (user_id, message) VALUES ('$judge_id', '$notif_msg')";
            mysqli_query($conn, $sql_notif);
        }
    }
            } else {
                $allErr = "Failed to update original case_info file.";
            }
        } else {
            $allErr = "No valid file ID provided.";
        }
    } else{
       $allErr = "File path not updated. Please upload a new file.";
    }
}//if (isset($_POST["register"]) and ($_SERVER["REQUEST_METHOD"] = "POST")) {
?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
    <h3 class="fw-bold mb-3">Attach File</h3>
     <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">File detail</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Attach File</a></li>
     </ul>
</div>
<div class="row">
 <div class="col-md-12">
   <div class="card">
     <div class="card-header">
        <div class="card-body">
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
<div class="card-title">
    <?php
    if (isset($_GET['case_id'])) 
        $case_id = $_GET['case_id'];      
    ?>
    </div>
</div>
<div class="card-body">
  <form method="POST" enctype="multipart/form-data">                          
    <div class="form-group">
      <label for="case_id">Case ID</label>
      <input type="text" class="form-control" id="case_id" name="case_id" value="<?= $case_id ?>" readonly>
      <input type="hidden" name="cid" value="<?= $cid ?>">
    </div>                        
   <div class="form-group">
  <label for="appointment_date">Record Date</label>
  <?php if ($fromAttachTable): ?>
    <input type="date" class="form-control" id="appointment_date" name="appointment_date" value="<?= htmlspecialchars($record_date) ?>" readonly>
  <?php else: ?>
    <input type="text" class="form-control" id="appointment_date" value="File uploaded at case registration" readonly>
  <?php endif; ?>
  <span class="text-danger"><?php echo $record_date_err; ?></span>
</div>

  <div class="form-group">
  <label for="file" class="form-label fw-semibold"><i class="fa fa-upload me-1"></i> Upload File</label>
  <input type="file" class="form-control" id="file" name="file">
  <?php if (isset($_GET['fid']) && !empty($currentFile) && file_exists($currentFile)): ?>
  <p class="mt-2"><strong>Current file </strong>: 
    <a href="<?= htmlspecialchars($currentFile) ?>" target="_blank">
      <?= htmlspecialchars(basename($currentFile)) ?>
    </a>
  </p>
<?php elseif (isset($_GET['kid']) && !empty($currentFile) && file_exists($currentFile)): ?>
  <p class="mt-2"> <strong>Current file</strong>: 
    <a href="<?= htmlspecialchars($currentFile) ?>" target="_blank">
      <?= htmlspecialchars(basename($currentFile)) ?>
    </a>
  </p>
<?php endif; ?>

  <span class="text-danger"><?php echo $file_err; ?></span>
</div>

    <div class="form-group">
      <label for="file_pages">File Pages</label>
      <input type="number" class="form-control" id="file_pages" name="file_pages" value="<?= $file_pages ?>">
      <span class="text-danger"><?php echo $file_pages_err; ?></span>
    </div>
    <div class="form-group">
        <button type="submit" name="submit" class="btn btn-primary">submit</button>
        <a href="case_fileByid.php?case_id=<?= $case_id; ?>&cid=<?= $cid; ?>" class="btn btn-secondary btn-block">Back</a>
    </div>
    </div>
</div>
</div>
<?php
include('footer.php');
?>