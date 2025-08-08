<?php
include('cdheader.php');
?>
<?php
if (isset($_POST['distribute'])) {
if (!empty($_POST['selected_cases']) && !empty($_POST['selected_judges'])) {
 $selected_cases = $_POST['selected_cases'];
 $selected_judges = $_POST['selected_judges'];
 $judge_types = $_POST['judge_type'];
 foreach ($selected_cases as $case_id) {
  foreach ($selected_judges as $judge_id) {
    $judge_type = isset($judge_types[$judge_id]) ? $judge_types[$judge_id] : 'secondary'; 
    $query = "INSERT INTO assigned_judges (user_id, case_id, judge_type) 
              VALUES ('$judge_id', '$case_id', '$judge_type')";
    if (mysqli_query($conn, $query)) {
        $ethiopian = \Andegna\DateTimeFactory::now();
    $ethiopian_date = $ethiopian->format('Y-m-d');
$update_case = "UPDATE `case` SET `case_status` = 2, `distributed_date` = '$ethiopian_date' WHERE `cid` = '$case_id'";
        if (mysqli_query($conn, $update_case)) {
            if (mysqli_affected_rows($conn) > 0) {
                echo "Trying to update case_id: $case_id<br>";
            } else {
                echo "No case found with cid = $case_id.<br>";
            }
        } else {
            echo "Error updating case status for case #$case_id: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "Error assigning case #$case_id to Judge #$judge_id: " . mysqli_error($conn) . "<br>";
    }
  }
}
echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><script>
Swal.fire({
  title: 'Success!',
  text: 'Cases successfully assigned to Judges',
  icon: 'success',
  confirmButtonText: 'OK'
}).then(() => {
  window.location.href = window.location.href;
});
</script>";
 // Get all judges assigned to this case
  // Get all judges assigned to this case
$sql_judges = "SELECT user_id FROM assigned_judges WHERE case_id = '$case_id'";
$result_judges = mysqli_query($conn, $sql_judges);

// Get the proper case_id from the case table using cid
$sql_cases = "SELECT case_id FROM `case` WHERE cid = '$case_id'";
$result_cases = mysqli_query($conn, $sql_cases);

$cases_id = ''; // Initialize
if ($result_cases && mysqli_num_rows($result_cases) > 0) {
    $case = mysqli_fetch_assoc($result_cases);
    $cases_id = $case['case_id']; // Real case identifier
}

// Prepare notification message using the real case_id
$notif_msg = "New case assigned to you: Case ID - $cases_id.";

// Send notification to each assigned judge
if ($result_judges && mysqli_num_rows($result_judges) > 0) {
    while ($row = mysqli_fetch_assoc($result_judges)) {
        $judge_id = $row['user_id'];
        $sql_notif = "INSERT INTO notifications (user_id, message) VALUES ('$judge_id', '$notif_msg')";
        mysqli_query($conn, $sql_notif);
    }
}
 exit();
  } else {
    echo "Please select at least one case and one judge.";
  }
}
?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
       <h3 class="fw-bold mb-3">Assign cases</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Case Management</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Assign Case</a></li>
        </ul>
    </div>
<style>
    .profile-img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
    }
</style>
<style>
.scrollable-table {
    max-height: 400px; /* or any height you prefer */
    overflow-y: auto;
    overflow-x: auto;
    display: block;
    width: 100%;
}
</style>

<div class="col-xl-12">
  <div class="page-title flex-wrap">
    <form method="post">
    <button type="submit" name="distribute" class="btn btn-primary">Distribute Case</button>
  </div>
</div>
<div class="row">
  <div class="col-6">
    <div class="card">
      <div class="card-header">
        <div class="card-title">case</div>
           <input type="text" id="caseSearch" class="form-control" 
       style="font-weight: bold;" 
       placeholder="Search by Case ID, Plaintiff, Defendant, or Status..." />
      </div>
<div class="card-body">
  <div class="scrollable-table">
    <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%;">
  <thead class="table-secondary">
        <tr>
        <th style="border: 2px solid black;">no</th>
		    <th style="border: 2px solid black;">#</th>		
        <th style="border: 2px solid black;">case_id</th>
        <th style="border: 2px solid black;">Plaintiff</th>
        <th style="border: 2px solid black;">defendant</th>               
        </tr>
    </thead>
  <tbody>
<?php 
 $no = 1;
$cases = getAllCasesWithoutAjTable();
foreach ($cases as $case): 
?>
<tr>
<td style="border: 2px solid black;"><?php echo $no ?></td>
<td style="border: 2px solid black;"><input type="checkbox" name="selected_cases[]" value="<?php echo $case['cid']; ?>"></td>										
    <td style="border: 2px solid black;"><?php echo $case["case_id"]; ?></td>
    <td style="border: 2px solid black;"><?php echo $case["plaintiff"]; ?></td>
    <td style="border: 2px solid black;"><?php echo $case["defendant"]; ?></td>                                                       
</tr>
 <?php
 $no++;
 endforeach; 
 ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="col-6">
  <div class="card">
    <div class="card-header">
      <div class="card-title">Judge</div>
         <input type="text" id="caseSearch" class="form-control" 
       style="font-weight: bold;" 
       placeholder="Search by Case ID, Plaintiff, Defendant, or Status..." />
    </div>
<div class="card-body">
  <div class="scrollable-table">
    <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%;">
  <thead class="table-secondary">
       <tr>
        <th style="border: 2px solid black;">no</th>
        <th style="border: 2px solid black;">#</th>
        <th style="border: 2px solid black;">IdNumber</th>
        <th style="border: 2px solid black;">profile_picture</th>
        <th style="border: 2px solid black;">First Name</th>
        <th style="border: 2px solid black;">judge_type</th>
       </tr>
    </thead>
<tbody>
    <?php
    $no = 1;
    $users = getAllJudges();  
    foreach ($users as $user): 
    ?>
<tr>
    <td style="border: 2px solid black;"><?php echo $no ?></td>
<td style="border: 2px solid black;"><input type="checkbox" name="selected_judges[]" value="<?php echo $user['uid']; ?>"></td>
    <td style="border: 2px solid black;"><?php echo $user["idNumber"]; ?></td>
    <td style="border: 2px solid black; style="border: 2px solid black;""><img class="profile-img" src="<?php echo $user["profile_pic"]; ?>" alt="Profile Picture" width="100" height="100"></td>
    <td style="border: 2px solid black; style="border: 2px solid black;""><?php echo $user["first_name"]; ?></td>
  <td style="border: 2px solid black;">
  <div class="btn-group btn-group-toggle" data-toggle="buttons">
    <a href="#" class="btn btn-outline-secondary btn-sm judge-type-btn px-2 py-1" data-judge-id="<?= $user['uid']; ?>" data-judge-type="primary">Primary</a>
    <a href="#" class="btn btn-outline-primary btn-sm judge-type-btn px-2 py-1" data-judge-id="<?= $user['uid']; ?>" data-judge-type="second">Second</a>
    <a href="#" class="btn btn-outline-info btn-sm judge-type-btn px-2 py-1" data-judge-id="<?= $user['uid']; ?>" data-judge-type="third">Third</a>
  </div>
  <input type="hidden" name="judge_type[<?= $user['uid']; ?>]" value="" id="judge_type_<?= $user['uid']; ?>">
</td>
</tr>
<?php 
$no++;
endforeach;
 ?>
</tbody>
                </table>
            </div>
        </div>
    </div>
</div>
 </div>
  </div>
   </div>
    </form>
<?php
include('../admin/footer.php');
?>
