<?php
include('judgeHeader.php');
?>
<?php
if (isset($_SESSION['uid'])) {
    $judge_id = $_SESSION['uid'];
    $userType = getUserType($judge_id);
    $judgeType = getAssignedJudgeType($judge_id);
     ?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
    <h3 class="fw-bold mb-3">Assign cases</h3>
     <ul class="breadcrumbs mb-3">
         <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Case management</a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Assign cases</a></li>
     </ul>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
         <div class="row w-100 align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
        <h4 class="mb-0">Assign cases</h4>
        </div>
<div class="col-12 col-md-6">
 <form method="GET">
   <div class="input-group">
    <input type="text" name="search" class="form-control" placeholder="Search by case_id...." 
     value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
      <button class="btn btn-primary" type="submit">
        <i class="fa fa-search"></i>
      </button>
    </div>
  </form>
</div>
<div class="card-body">
  <div class="table-responsive">
      <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%; background-color: white;">
  <thead class="table-secondary">
          <tr>
              <th style="border: 2px solid black;">#</th>
              <th style="border: 2px solid black;">Case ID</th>
              <th style="border: 2px solid black;">Plaintiff</th>
              <th style="border: 2px solid black;">Defendant</th>
              <th style="border: 2px solid black;">Status</th>
              <th style="border: 2px solid black;">Actions</th>
          </tr>
      </thead>
    <tbody>
<?php 
if ($userType['user_type'] == 'Judge') 
$cases = getAssignedCasesById($judge_id);
    if (!empty($cases)) {    
       $no = 1;
    foreach ($cases as $case):
        $status = case_status($case["case_status"]); 
?>
<tr>
    <td style="border: 2px solid black;"><?= $no++; ?></td>
    <td style="border: 2px solid black;"><?= $case['case_id']; ?></td>
    <td style="border: 2px solid black;"><?= $case['plaintiff']; ?></td>
    <td style="border: 2px solid black;"><?= $case['defendant']; ?></td>
    <td style="border: 2px solid black;"><?= $status; ?></td>
    <td style="border: 2px solid black;">
    <a href="case_detail.php?case_id=<?php echo $case["case_id"]; ?>&cid=<?php echo $case["cid"]; ?>"
        class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Details
    </a>
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