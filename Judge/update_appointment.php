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
    //validation aid
    if (empty($_POST["aid"])) {
        $aid_err = "Appointment ID (aid) is required";
        $test = false;
    } else {
        $aid = $_POST["aid"];
    }
    //validate record_date
    if (empty($_POST["record_date"])) {
        $record_date_err = "Record date is required";
        $test = false;
    } else {
        $record_date = $_POST["record_date"];
    }
    //validate appointment_date
    if (empty($_POST["appointment_date"])) {
        $appointment_date_err = "Appointment date is required";
    } else {
        $appointment_date = $_POST["appointment_date"];
    }
    // After successfully adding the appointment
    if($test){
        if (appExist($aid) == 1) {
if (UpdateAppointment_date($aid,$appointment_date, $record_date) == 1) {
    $success = "Appointment has been Updated successfully";
    // Re-fetch the updated appointment data
    $updatedAppointment = getAppointmentById($aid);
    if ($updatedAppointment) {
        $appointment_date = $updatedAppointment['appointment_date'];
        $record_date = $updatedAppointment['record_date'];
    }
     $sql_judges = "SELECT user_id FROM assigned_judges WHERE case_id = '$cid'";
    $result_judges = mysqli_query($conn, $sql_judges);
    // Prepare notification message
    $notif_msg = "An appointment updated for Case ID: $cid.";
    // Send notification to each assigned judge
    if ($result_judges && mysqli_num_rows($result_judges) > 0) {
        while ($row = mysqli_fetch_assoc($result_judges)) {
            $judge_id = $row['user_id'];
            $sql_notif = "INSERT INTO notifications (user_id, message) VALUES ('$judge_id', '$notif_msg')";
            mysqli_query($conn, $sql_notif);
        }
    }
} } else {
    $allErr = " Something went wrong";
   }
} else {
$allErr = "There is no user associated with the given information";
}
}//if (isset($_POST["register"]) and ($_SERVER["REQUEST_METHOD"] = "POST")) {

?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
    <h3 class="fw-bold mb-3">Update Appointment</h3>
     <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Appointment detail</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Update Appointment</a></li>
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
    if (isset($_GET['appointment_date']))
        $appointment_date = $_GET['appointment_date']; 
    if (isset($_GET['aid']))
        $aid = $_GET['aid'];
    ?>
    </div>
</div>
<div class="card-body">
  <form method="POST">                          
    <div class="form-group">
      <label for="case_id">Case ID</label>
      <input type="text" class="form-control" id="case_id" name="case_id" value="<?= $case_id ?>" readonly>
      <input type="hidden" name="aid" value="<?= $aid ?>">
    </div>                        
    <div class="form-group">
      <label for="appointment_date">Appointment Date</label>
      <input type="date" class="form-control" id="appointment_date" name="appointment_date" value="<?= $appointment_date ?>">
      <span class="text-danger"><?php echo $appointment_date_err; ?></span>
    </div>
    <div class="form-group">
        <label for="record_date">Record Date</label>
        <input type="date" class="form-control" id="record_date" name="record_date" value="<?= $ethiopianDate ?>" readonly>
    </div>
    <div class="form-group">
        <button type="submit" name="submit" class="btn btn-primary">update Appointment</button>
        <a href="view_updateAppointment.php"class="btn btn-danger"></i> Back</a>
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