<?php
include('judgeHeader.php');
?>
<?php
$aid= "";
$success =  $AllErr = "";
if (isset($_SESSION['uid'])) {
    $judge_id = $_SESSION['uid'];
    $userType = getUserType($judge_id);
    $judgeType = getAssignedJudgeType($judge_id);
   // Confirm action
   if (isset($_GET["confirm_id"])) {
    $cid = basics($_GET["confirm_id"]); // THIS should be cid, not aid
    if ($judgeType['judge_type'] == 'second') {
        $update = mysqli_query($conn, "UPDATE `case` SET case_status = 7 WHERE cid = '$cid'");
        $success = $update ? "Appointment confirmed." : "Failed to confirm request.";
    } elseif ($judgeType['judge_type'] == 'Third') {
        // You must first fetch the case details to check status
        $caseQuery = mysqli_query($conn, "SELECT * FROM `case` WHERE cid = '$cid'");
        $case = mysqli_fetch_assoc($caseQuery);
        if ($case && $case['case_status'] == 7) {
            $update = mysqli_query($conn, "UPDATE `case` SET case_status = 8 WHERE cid = '$cid'");
            $success = $update ? "Final judge confirmed." : "Failed to confirm.";
                           $sql_judges = "SELECT user_id FROM assigned_judges WHERE case_id = '$cid'";
    $result_judges = mysqli_query($conn, $sql_judges);
    // Prepare notification message
        $notif_msg = "A decision confirmed successfully  for Case ID: $case_id.";
    // Send notification to each assigned judge
    if ($result_judges && mysqli_num_rows($result_judges) > 0) {
        while ($row = mysqli_fetch_assoc($result_judges)) {
            $judge_id = $row['user_id'];
            $sql_notif = "INSERT INTO notifications (user_id, message) VALUES ('$judge_id', '$notif_msg')";
            mysqli_query($conn, $sql_notif);
        }
    }
        } else {
            $AllErr = "You cannot confirm this case yet.";
        }
    } else {
        $AllErr = "You cannot confirm this case yet.";
    }
}

 // Fetch assigned cases
  $cases = getConfirmDecision($judge_id);
 $filteredCases = [];

 if (is_array($cases)) {
     foreach ($cases as $case) {    
     if ($judgeType['judge_type'] == 'second' && in_array($case['case_status'], [6, 7])) {
         $filteredCases[] = $case;
     } elseif ($judgeType['judge_type'] == 'Third' && in_array($case['case_status'], [7, 8])) {
         $filteredCases[] = $case;
     }
 }
}    
     ?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
       <h3 class="fw-bold mb-3">confrim decision</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">decison detail</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">confirm decision</a></li>
        </ul>
    </div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
		<div class="row">
    <div class="col-6 text-right">     
    <h4>Case detail</h4> 
    </div>  
<?php if (!empty($success)) { ?>
    <div id="successMessage" class="form-control bg-success text-white">
        <?= $success; ?>
    </div>
<?php } ?>
<?php if (!empty($AllErr)) { ?>
    <div id="errorMessage" class="form-control bg-danger text-white">
        <?= $AllErr; ?>
    </div>
<?php } ?>      
<div class="card-body">
  <div class="">
    <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%;">
  <thead class="table-secondary">
          <tr>
              <th style="border: 2px solid black;">#</th>
              <th style="border: 2px solid black;">Case ID</th>                            
              <th style="border: 2px solid black;">Appointment Date</th>
              <th style="border: 2px solid black;">Appointment Reason</th>              
              <th style="border: 2px solid black;">Decision</th>
              <th style="border: 2px solid black;">Actions</th>
          </tr>
      </thead>
   <tbody>
<?php 
if (!empty($filteredCases)) {
    $no = 1;
    $judgeTypeValue = strtolower($judgeType['judge_type']); 
    foreach ($filteredCases as $case):
        $status = case_status($case["case_status"]);
?>
 <tr>
    <td style="border: 2px solid black;"><?= $no++; ?></td>
    <td style="border: 2px solid black;"><?= $case['case_id']; ?></td>
    <td style="border: 2px solid black;"><?= $case['appointment_date']; ?></td>    
    <td style="border: 2px solid black;"><?= $case['appointment_reason']; ?></td>
    <td style="border: 2px solid black;"><?= $case['decision']; ?></td>      
    <td style="border: 2px solid black;">                 
    <?php
    $showConfirmButton = false;                  
    if ($judgeTypeValue == 'second' && $case['case_status'] == 6) {
        $showConfirmButton = true;
    } elseif ($judgeTypeValue == 'third' && $case['case_status'] == 7) {
        $showConfirmButton = true;
    }                  
    if ($showConfirmButton): ?>
        <a href="?confirm_id=<?= $case['cid'] ?>" class="btn btn-success btn-sm">Confirm</a>
    <?php else: ?>
        <span class="badge badge-success">Confirmed</span>
    <?php endif; ?>                  
    </td>
</tr>
    <?php 
        endforeach;
    } else { 
    ?>
    <tr><td colspan="10" class="text-center text-danger" style="border: 2px solid black;">No cases found.</td></tr>
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
</div>
<?php
    } else {
        echo "You are not authorized to view this page. User Type: ";
    }
?>
<?php
include('../admin/footer.php');
?>