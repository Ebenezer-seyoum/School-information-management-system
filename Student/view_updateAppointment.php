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
        <h4>Case </h4> 
        </div>  
	 </div>
 </div>
<div class="card-body">
  <div class="table-responsive">
    <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%;">
  <thead class="table-secondary">
          <tr>
              <th style="border: 2px solid black;">#</th>
              <th style="border: 2px solid black;">Case ID</th>
              <th style="border: 2px solid black;">Plaintiff</th>
              <th style="border: 2px solid black;">Defendant</th>
              <th style="border: 2px solid black;">Actions</th>
          </tr>
      </thead>
   <tbody>
<?php 
if ($userType['user_type'] == 'Judge' && $judgeType['judge_type'] == 'primary') 
$cases = getAssignedCasesById($judge_id);
           if (!empty($cases)) {    
               $no = 1;
           foreach ($cases as $case):
?>
  <tr>
    <td style="border: 2px solid black;"><?= $no++; ?></td>
    <td style="border: 2px solid black;"><?= $case['case_id']; ?></td>
    <td style="border: 2px solid black;"><?= $case['plaintiff']; ?></td>
    <td style="border: 2px solid black;"><?= $case['defendant']; ?></td>
	<td style="border: 2px solid black;"> 
    <a href="Appointment_list.php?case_id=<?= $case['case_id']; ?>&cid=<?= $case['cid']; ?>"
     class="btn btn-primary btn-sm"><i class="fas fa-edit"></i>update</a>
    </td>
  </tr>
<?php                                    
endforeach;
 } 
?>
    <?php if (empty($cases)) { ?>                                      
<tr><td colspan="10" class="text-center danger">No cases found.</td></tr>                                     
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
<?php
    } else {
        echo "You are not authorized to view this page. User Type: ";
    }
?>
<?php
include('../admin/footer.php');
?>