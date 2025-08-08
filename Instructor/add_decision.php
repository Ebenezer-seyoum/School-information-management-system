<?php
include('judgeHeader.php');
?>
<?php
$decision_err = $who_won_err = $decision_compared_err = "";
$case_id = $decision = $appointment_date = $appointment_reason = "";
$who_won = $decision_compared = "";
$test = true;
$allErr = $success = "";
$cid = "";
list($ethYear, $ethMonth, $ethDay) = gregorianToEthiopian(date('Y'), date('m'), date('d'));
$ethiopianDate = sprintf('%04d-%02d-%02d', $ethYear, $ethMonth, $ethDay);
    if (isset($_GET['case_id'])) 
        $case_id = $_GET['case_id']; 
    if (isset($_GET['appointment_date'])) 
        $appointment_date = $_GET['appointment_date'];
    if (isset($_GET['cid']))
        $cid = $_GET['cid'];

if ((isset($_POST["submit"]) and $_SERVER["REQUEST_METHOD"] == "POST")) {
    //validate decision    
    if (empty($_POST["decision"])) {
        $decision_err = "Decision is required";
        $test = false;
    } else {
        $decision = $_POST["decision"];
    }
    if (empty($_POST["decision"])) {
        $decision_err = "Decision is required";
        $test = false;
    } else {
        $decision = $_POST["decision"];
    }
    //validation who won
    if (empty($_POST["who_won"])) {
        $who_won_err = "Please select who won the case";
        $test = false;
    } else {
        $who_won = $_POST["who_won"];
    }
    
  

    if ($test == true) {
        if (decisionExist($cid) == 0) {
            if (addDecision($cid, $decision, $who_won) == 1) {
                $success = " Decision has been recorded successfully";
                 $sql_judges = "SELECT user_id FROM assigned_judges WHERE case_id = '$cid'";
    $result_judges = mysqli_query($conn, $sql_judges);
    // Prepare notification message
        $notif_msg = "A decision inserted successfully  for Case ID: $case_id.";
    // Send notification to each assigned judge
    if ($result_judges && mysqli_num_rows($result_judges) > 0) {
        while ($row = mysqli_fetch_assoc($result_judges)) {
            $judge_id = $row['user_id'];
            $sql_notif = "INSERT INTO notifications (user_id, message) VALUES ('$judge_id', '$notif_msg')";
            mysqli_query($conn, $sql_notif);
        }
    }
            } else {
                $allErr = "There was error while recording decision";
            }
        } else {
            $allErr = "A decision for this case on this appointment date has already been recorded";
        }
    }
} //if (isset($_POST["register"]) and ($_SERVER["REQUEST_METHOD"] = "POST")) {
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
    <h3 class="fw-bold mb-3">Record decision</h3>
    <ul class="breadcrumbs mb-3">
       <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
       <li class="separator"><i class="icon-arrow-right"></i></li>
       <li class="nav-item"><a href="#">Decision details</a></li>
       <li class="separator"><i class="icon-arrow-right"></i></li>
       <li class="nav-item"><a href="#">Record decision</a></li>
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
</div>
<div class="card-body">
    <form method="POST">                                                     
    <div class="form-group">
        <label for="case_id">Case ID</label>
        <input type="text" class="form-control" id="case_id" name="case_id" value="<?= $case_id ?>" readonly>
    </div>
    <div class="form-group">
        <label for="appointment_date">Appointment Date</label>
        <input type="date" class="form-control" id="appointment_date" name="appointment_date" value="<?= $appointment_date ?>" readonly>
    </div>
        <div class="form-group">
            <label for="decision">Decision</label>
            <input type="text" class="form-control" id="decision" name="decision">
            <span class="text-danger"><?php echo $decision_err; ?></span>
        </div>   
        <div class="form-group">
    <label for="who_won">Who Won</label>
    <input type="text" class="form-control" id="who_won" name="who_won" >
    <span class="text-danger"><?php echo $who_won_err; ?></span>
</div>
  <div class="form-group">
            <button type="submit" name="submit" class="btn btn-primary">Add Decision</button>
            <a href="record_decision.php" class="btn btn-secondary">Back</a>
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