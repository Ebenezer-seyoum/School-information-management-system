<?php
include('judgeHeader.php');
?>
<?php
if (isset($_GET['case_id']) && isset($_GET['cid'])) {
    $case_id = $_GET['case_id']; 
    $cid = $_GET['cid']; 
}
if (isset($_SESSION['uid'])) {
    $judge_id = $_SESSION['uid'];
    $userType = getUserType($judge_id);
    $judgeType = getAssignedJudgeType($judge_id);
     ?>
<div class="container">
  <div class="page-inner">
   <div class="page-header">
    <h3 class="fw-bold mb-3">Give appointment</h3>
     <ul class="breadcrumbs mb-3">
         <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Appointment detail</a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Give Appointment</a></li>
     </ul>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
			<div class="row">
        <div class="col-6 text-right">     
        <h4>view  </h4> 
        </div>  
           <input type="text" id="caseSearch" class="form-control" 
       style="font-weight: bold;" 
       placeholder="Search by Case ID, Plaintiff, Defendant, or Status..." />
	 </div>
 </div>
<div class="card-body">
  <div class="table-responsive">
   <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%;">
  <thead class="table-secondary">
          <tr>
              <th style="border: 2px solid black;">#</th>
              <th style="border: 2px solid black;">appointment_date</th>
              <th style="border: 2px solid black;">appointment_reason</th>
              <th style="border: 2px solid black;">record_date</th>
              <th style="border: 2px solid black;">Actions</th>
          </tr>
      </thead>
   <tbody>
<?php 
if ($userType['user_type'] == 'Judge' && $judgeType['judge_type'] == 'primary') 
$cases = getAppointmentDetailsByCaseId($cid);
           if (!empty($cases)) {    
               $no = 1;
           foreach ($cases as $case):
?>
  <tr>
    <td style="border: 2px solid black;"><?= $no++; ?></td>
    <td style="border: 2px solid black;"><?= $case['appointment_date']; ?></td>
    <td style="border: 2px solid black;"><?= $case['appointment_reason']; ?></td>
    <td style="border: 2px solid black;"><?= $case['record_date']; ?></td>
	<td style="border: 2px solid black;"> 
    <a href="update_appointment.php?case_id=<?= $case['case_id']; ?>&aid=<?= $case['aid']; ?>&cid=<?= $case['cid']; ?>
    &appointment_date=<?= $case['appointment_date']; ?>&appointment_reason=<?= $case['appointment_reason'];?>" 
    class="btn btn-primary btn-sm"><i class="fas fa-edit"></i>update</a>
    </td>
  </tr>
<?php                                    
endforeach;
 } 
?>
    <?php if (empty($cases)) { ?>                                      
<tr><td colspan="10" class="text-center danger" style="border: 2px solid black;">No cases found.</td></tr>                                     
    <?php } ?>              
                                </tbody>
                            </table>
                        </div>
 <div class="text-left mt-4">
                <a href="view_AllAppointment.php" class="btn btn-outline-primary">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
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