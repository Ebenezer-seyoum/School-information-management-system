<?php
include "cdheader.php"; 
?>
<?php
$allCases = 0;
$openCases = 0;
$distributedCases = 0;
$pending_appointmentCases = 0;
$appointedCases = 0;
$pending_decisionCases = 0;
$decidedCases = 0;

$queryAll = "SELECT COUNT(*) AS total FROM `case`";
$queryOpen = "SELECT COUNT(*) AS total FROM `case` WHERE case_status = 1";
$queryDistributed = "SELECT COUNT(*) AS total FROM `case` WHERE case_status = 2";
$pendingAppointment = "SELECT COUNT(*) AS total FROM `case` WHERE case_status = 2 || case_status = 3";
$queryAppointed = "SELECT COUNT(*) AS total FROM `case` WHERE case_status = 5";
$pendingDecision = "SELECT COUNT(*) AS total FROM `case` WHERE case_status = 6 || case_status = 7";
$queryDecided = "SELECT COUNT(*) AS total FROM `case` WHERE case_status = 8";

$resultAll = mysqli_query($conn, $queryAll);
$resultOpen = mysqli_query($conn, $queryOpen);
$resultDistributed = mysqli_query($conn, $queryDistributed);
$resultPendingAppointment = mysqli_query($conn, $pendingAppointment);
$resultAppointed = mysqli_query($conn, $queryAppointed);
$resultPendingDecision = mysqli_query($conn, $pendingDecision);
$resultDecided = mysqli_query($conn, $queryDecided);

if ($row = mysqli_fetch_assoc($resultAll)) { $allCases = $row['total']; }
if ($row = mysqli_fetch_assoc($resultOpen)) { $openCases = $row['total']; }
if ($row = mysqli_fetch_assoc($resultDistributed)) { $distributedCases = $row['total']; }
if ($row = mysqli_fetch_assoc($resultPendingAppointment)) { $pendingAppointmentCases = $row['total']; }
if ($row = mysqli_fetch_assoc($resultAppointed)) { $appointedCases = $row['total']; }
if ($row = mysqli_fetch_assoc($resultPendingDecision)) { $pendingDecisionCases = $row['total']; }
if ($row = mysqli_fetch_assoc($resultDecided)) { $decidedCases = $row['total']; }
?>
<div class="container">
<div class="page-inner">
  <div class="row g-4"> 
<!-- All Cases -->
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round h-100">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-primary bubble-shadow-small">
                        <i class="fas fa-briefcase"></i>
                    </div>
                </div>
                <div class="col col-stats ms-3">
                    <div class="numbers">
                        <p class="card-category">All Cases</p>
                        <h4 class="card-title"><?php echo $allCases; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Open Cases -->
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round h-100">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-info bubble-shadow-small">
                        <i class="fas fa-folder-open"></i>
                    </div>
                </div>
                <div class="col col-stats ms-3">
                    <div class="numbers">
                        <p class="card-category">Open Cases</p>
                        <h4 class="card-title"><?php echo $openCases; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Distributed Cases -->
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round h-100">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-success bubble-shadow-small">
                        <i class="fas fa-share-square"></i>
                    </div>
                </div>
                <div class="col col-stats ms-3">
                    <div class="numbers">
                        <p class="card-category">Distributed Cases</p>
                        <h4 class="card-title"><?php echo $distributedCases; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 <!-- pending appointment Cases -->
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round h-100">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-secondary bubble-shadow-small">
                        <i class="fas fas fa-clock"></i>
                    </div>
                </div>
                <div class="col col-stats ms-3">
                    <div class="numbers">
                        <p class="card-category">Pending Appointment Cases</p>
                        <h4 class="card-title"><?php echo $pendingAppointmentCases; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Appointed Cases -->
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round h-100">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-secondary bubble-shadow-small">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
                <div class="col col-stats ms-3">
                    <div class="numbers">
                        <p class="card-category">Appointed Cases</p>
                        <h4 class="card-title"><?php echo $appointedCases; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 <!-- Pendig Decision Cases -->
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round h-100">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-secondary bubble-shadow-small">
                        <i class="fas fas fa-clock"></i> 
                    </div>
                </div>
                <div class="col col-stats ms-3">
                    <div class="numbers">
                        <p class="card-category">Pending Decision Cases</p>
                        <h4 class="card-title"><?php echo $pendingDecisionCases; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Decided Cases -->
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round h-100">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-warning bubble-shadow-small">
                       <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="col col-stats ms-3">
                    <div class="numbers">
                        <p class="card-category">Decided Cases</p>
                        <h4 class="card-title"><?php echo $decidedCases; ?></h4>
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
$summaryData = getCaseSummaryData();
$caseStatus = $summaryData['status'];
$caseType = $summaryData['type'];
$allLabels = array_unique(array_merge(
  array_column($caseStatus, 'label'),
  array_column($caseType, 'label')
));
$labels = array_values($allLabels);
function extractCounts($labels, $dataSet) {
    $mapped = array_column($dataSet, 'count', 'label');
    $result = [];
    foreach ($labels as $label) {
        $result[] = isset($mapped[$label]) ? $mapped[$label] : 0;
    }
    return $result;
}
$statusCounts = extractCounts($labels, $caseStatus);
$typeCounts = extractCounts($labels, $caseType);
?>
<div class="mt-5">
<!-- Chart Container -->
  <div class="d-flex justify-content-center mb-4">
    <div style="max-width: 700px; width: 100%;">
      <canvas id="combinedChart"></canvas>
    </div>
  </div>
<?php
include "../Admin/footer.php";
?>

