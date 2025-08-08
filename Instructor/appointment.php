<?php
include('judgeHeader.php');
?>
<?php
$appointment_date_err = $reason_err = $case_id_err = "";
$appointment_date  = $case_id = "";
$record_date = date('Y-m-d');
$test = true;
$allErr = $success = "";
$aid = $cid=  "";
list($ethYear, $ethMonth, $ethDay) = gregorianToEthiopian(date('Y'), date('m'), date('d'));
$ethiopianDate = sprintf('%04d-%02d-%02d', $ethYear, $ethMonth, $ethDay);
// Get case_id and cid from URL for pre-filling the form
if (isset($_GET['case_id']) && isset($_GET['cid'])) {
    $case_id = $_GET['case_id'];
    $cid = $_GET['cid'];
}
if ((isset($_POST["submit"]) and $_SERVER["REQUEST_METHOD"] == "POST")) {
    //validate cid
    if (empty($_POST["cid"])) {
        $cid_err = "Internal Case ID (cid) is required";
        $test = false;
    } else {
        $cid = $_POST["cid"];
    }
    //validate reason_id
    if (empty($_POST["reason_id"])) {
        $reason_err = "Reason is required";
        $test = false;
    } else {
        $reason_id = $_POST["reason_id"];
    }
    //validate record_date
    if (empty($_POST["record_date"])) {
        $record_date_err = "Record date is required";
    } else {
        $record_date = $_POST["record_date"];
    }
    //validate appointment_date
    if (empty($_POST["appointment_date"])) {
        $appointment_date_err = "Appointment date is required";
    } else {
        $appointment_date = $_POST["appointment_date"];
    }
    

    // Count how many appointments exist for this case
$appointment_count = 0;
$res_appoint = mysqli_query($conn, "SELECT COUNT(*) AS total FROM appointment WHERE case_id = '$cid'");
if ($row = mysqli_fetch_assoc($res_appoint)) {
    $appointment_count = (int)$row['total'];
}

if (addAppointment($appointment_date, $cid, $reason_id, $record_date) == 1) {
   $sql_judges = "SELECT user_id FROM assigned_judges WHERE case_id = '$cid'";
$result_judges = mysqli_query($conn, $sql_judges);
$judge_count = mysqli_num_rows($result_judges);

if ($appointment_count == 0) {
if ($judge_count == 1) {
    // Only one judge assigned, mark as appointed (status 5)
  $update = mysqli_query($conn, "UPDATE `case` SET case_status = 5 WHERE cid = '$cid'");
if ($update) {
    $success = "Appointment scheduled and judge appointed (only one judge assigned).";
     sendConfirmationEmail($cid, $reason_id, $appointment_date);
}} else {
    // Multiple judges assigned, mark as appointment given (status 3)
    mysqli_query($conn, "UPDATE `case` SET case_status = 3 WHERE cid = '$cid'");
    $success = "Appointment has been scheduled successfully.";

    // Notify all judges
    $notif_msg = "An appointment has been scheduled for Case ID: $case_id.";
    while ($row = mysqli_fetch_assoc($result_judges)) {
        $judge_id = $row['user_id'];
        mysqli_query($conn, "INSERT INTO notifications (user_id, message) VALUES ('$judge_id', '$notif_msg')");
    }
 }
}else {
    // Do NOT update case_status for 2nd, 3rd, etc.
    $ordinal = getOrdinalWord($appointment_count + 1);
    $success = "$ordinal appointment has been scheduled.";
     // Notify all judges
    $notif_msg = "An appointment has been scheduled for Case ID: $case_id.";
    while ($row = mysqli_fetch_assoc($result_judges)) {
        $judge_id = $row['user_id'];
        mysqli_query($conn, "INSERT INTO notifications (user_id, message) VALUES ('$judge_id', '$notif_msg')");
    }
}
}
}//if (isset($_POST["register"]) and ($_SERVER["REQUEST_METHOD"] = "POST")) {

?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
    <h3 class="fw-bold mb-3">Schedule Appointment</h3>
     <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Forms</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Schedule Appointment</a></li>
     </ul>
</div>
<div class="row">
 <div class="col-md-12">
   <div class="card">
     <div class="card-header">
        <div class="card-body">
<?php if (!empty($success)) { ?>
    <div class=" form-control bg-success" id = "successMessage">
<?php echo $success; ?>
    </div>
<?php  } ?>
<?php if (!empty($allErr)) { ?>
    <div class=" form-control bg-danger" id = "errorMessage">
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
  <form method="POST">                          
    <div class="form-group">
      <label for="case_id">Case ID</label>
      <input type="text" class="form-control" id="case_id" name="case_id" value="<?= $case_id ?>" readonly>
      <input type="hidden" name="cid" value="<?= $cid ?>">
    </div>                        
    <div class="form-group">
      <label for="appointment_date">Appointment Date</label>
      <input type="date" class="form-control" id="appointment_date" name="appointment_date">
      <span class="text-danger"><?php echo $appointment_date_err; ?></span>
    </div>
    <div class="form-group">
      <label for="reason_id">Reason</label>
      <select class="form-control" id="reason_id" name="reason_id">
          <option value="">Select Reason</option>
          <?php $reasons = getAllReasons();
          foreach ($reasons as $reason) {
          ?>
         <option value="<?php echo $reason["rid"]; ?>"><?php echo $reason["appointment_reason"]; ?></option>
            <?php } ?>
      </select>
      <span class="text-danger"><?php echo $reason_err; ?></span>
    </div>                     
    <div class="form-group">
        <label for="record_date">Record Date</label>
        <input type="date" class="form-control" id="record_date" name="record_date" value="<?= $ethiopianDate ?>" readonly>
    </div>
    <div class="form-group">
        <button type="submit" name="submit" class="btn btn-primary">Schedule Appointment</button>
        <a href="view_giveAppointment.php"class="btn btn-danger"></i> Back</a>
     </div>
  </form>
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