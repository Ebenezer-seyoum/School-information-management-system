<?php
include('cdheader.php');

$success = $allErr = "";
$case_id = $cid = $user_id = $judgeType = "";
$judge_err = "";
// Prefill values from URL
if (isset($_GET['case_id'])) {
    $case_id = $_GET['case_id'];
}
if (isset($_GET['cid'])) {
    $cid = $_GET['cid'];
}
if (isset($_GET['judge_id'])) {
    $judge_id = $_GET['judge_id'];  
    $user_data = getUserIdNumberByID($judge_id);   
    $user_id = $user_data['IdNumber'];      
    $judge_data = getAssignedJudgeType($judge_id);   
    $judgeType = $judge_data['judge_type'];  
}

// When form is submitted
if (isset($_POST["submit"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $new_judge_id = $_POST['to_judge'];

    if (empty($new_judge_id)) {
        $judge_err = "Please select a judge to transfer the case.";
    } elseif ($new_judge_id == $judge_id) {
        $allErr = "This case is already assigned to the selected judge.";
    } elseif (isJudgeAlreadyAssigned($conn, $cid, $new_judge_id)) {
        $allErr = "This judge is already assigned to this case.";
    } else {
        if (transferCaseToJudge($conn, $cid, $judge_id, $new_judge_id)) {
            $success = "Case has been successfully transferred to the new judge.";
             $notif_msg = "A new case (ID: $case_id) has been transferred to you.";
              $sql_notif = "INSERT INTO notifications (user_id, message) 
                  VALUES ('$new_judge_id', '$notif_msg')";
                     mysqli_query($conn, $sql_notif);
        } else {
            $allErr = "Failed to transfer the case. Please try again.";
        }
    }
}
?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Transfer Cases</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Case Management</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Transfer Cases</a></li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
             <div class="card-body">
    <?php if (!empty($success)) { ?>
<?php } ?>

              <?php if (!empty($allErr)) { ?>
                <div class="alert alert-danger"><?php echo $allErr; ?></div>
              <?php } ?>   
            </div>

            <div class="card-body">
              <form method="POST"> 
                <div class="form-group mb-3">
                  <label for="case_id">Case ID</label>
                  <input type="text" class="form-control" id="case_id" name="case_id" value="<?= $case_id ?>" readonly>
                  <input type="hidden" name="cid" value="<?= $cid ?>">
                </div>  

                <div class="form-group mb-3">
                  <label for="current_judge">Current Judge</label>
                  <input type="text" class="form-control" id="current_judge" name="current_judge" value="<?= $user_id ?>" readonly>
                </div>

                <div class="form-group mb-4">
                  <label for="judge_type">Judge Type</label>
                  <input type="text" class="form-control" id="judge_type" name="judge_type" value="<?= $judgeType ?>" readonly>
                </div>

                <div class="form-group mb-3">
                  <label for="to_judge">Transfer To (New Judge)</label>
                  <select class="form-control" id="to_judge" name="to_judge">
                    <option value="">---Select Judge---</option>
                    <?php
                    $judges = getAllJudges();
                    foreach ($judges as $judge) {
                        if ($judge['uid'] != $judge_id) {
                    ?>
                      <option value="<?php echo $judge["uid"]; ?>">
                        <?php echo $judge["idNumber"] . ' - ' . $judge["first_name"]; ?>
                      </option>
                    <?php }
                    } ?>
                  </select>
                  <span class="text-danger"><?php echo $judge_err; ?></span>
                </div>

                <div class="form-group">
                  <button type="submit" name="submit" class="btn btn-primary">Transfer Case</button>
                  <a href="judge_cases.php?judge_id= <?= $judge_id; ?>" class="btn btn-secondary btn-block">Back</a>
                </div>
              </form>
            </div> <!-- card-body -->
          </div> <!-- card -->
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
<?php if (!empty($success)) { ?>
Swal.fire({
  icon: 'success',
  title: 'Success!',
  text: '<?php echo addslashes($success); ?>',
  confirmButtonColor: '#3085d6'
}).then((result) => {
  if (result.isConfirmed) {
    window.location.href = 'judge_cases.php?judge_id=<?= $judge_id ?>';
  }
});
<?php } ?>

<?php if (!empty($allErr)) { ?>
Swal.fire({
  icon: 'error',
  title: 'Action Blocked!',
  text: '<?php echo addslashes($allErr); ?>',
  confirmButtonColor: '#d33'
});
<?php } ?>
</script>

<?php
include('../admin/footer.php');
?>
