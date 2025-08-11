<?php
include 'adminHeader.php';

$fromDate = $_GET['fromDate'] ?? '';
$toDate = $_GET['toDate'] ?? '';
$status = $_GET['status'] ?? ''; 
$case_type = $_GET['case_type'] ?? '';
$created_filter = $_GET['created_filter'] ?? '';
?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Case Reports</h3>
    </div>
    <div class="card mb-3">
      <div class="card-body">
        <form method="GET" action="">
          <div class="row g-2">
            <!-- Case Status -->
            <div class="col-md-3">
              <label class="form-label mb-1">Case Status</label>
              <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Status</option>
                <?php
                  $statusList = getCaseStatusList();
                  foreach ($statusList as $key => $label) {
                    $selected = ($status !== '' && $status == $key) ? 'selected' : '';
                    echo "<option value=\"$key\" $selected>$label</option>";
                  }
                ?>
              </select>
            </div>
            
            <!-- Case Type -->
            <div class="col-md-3">
              <label class="form-label mb-1">Case Type</label>
              <select name="case_type" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Case Types</option>
                <?php
                  $caseTypeList = getCaseTypes();
                  foreach ($caseTypeList as $key => $label) {
                    $selected = ($case_type == $key) ? 'selected' : '';
                    echo "<option value=\"$key\" $selected>$label</option>";
                  }
                ?>
              </select>
            </div>
            
           <!-- Checkboxes to toggle each date filter -->
<div class="col-md-12 d-flex flex-wrap gap-4">
  <!-- Created Date Filter -->
  <div>
    <div class="form-check mb-1">
      <input class="form-check-input" type="checkbox" id="toggleCreated" onclick="toggleSection('created')" <?= isset($_GET['from_created']) ? 'checked' : '' ?>>
      <label class="form-check-label" for="toggleCreated">Created Date</label>
    </div>
    <div id="createdDateRange" class="d-none">
      <input type="date" name="from_created" class="form-control form-control-sm mb-1"
             value="<?= htmlspecialchars($_GET['from_created'] ?? '') ?>" onchange="autoSubmitIfFilled()">
      <input type="date" name="to_created" class="form-control form-control-sm"
             value="<?= htmlspecialchars($_GET['to_created'] ?? '') ?>" onchange="autoSubmitIfFilled()">
    </div>
  </div>

  <!-- Distributed Date Filter -->
  <div>
    <div class="form-check mb-1">
      <input class="form-check-input" type="checkbox" id="toggleDistributed" onclick="toggleSection('distributed')" <?= isset($_GET['from_distributed']) ? 'checked' : '' ?>>
      <label class="form-check-label" for="toggleDistributed">Distributed Date</label>
    </div>
    <div id="distributedDateRange" class="d-none">
      <input type="date" name="from_distributed" class="form-control form-control-sm mb-1"
             value="<?= htmlspecialchars($_GET['from_distributed'] ?? '') ?>" onchange="autoSubmitIfFilled()">
      <input type="date" name="to_distributed" class="form-control form-control-sm"
             value="<?= htmlspecialchars($_GET['to_distributed'] ?? '') ?>" onchange="autoSubmitIfFilled()">
    </div>
  </div>

  <!-- End Date Filter -->
  <div>
    <div class="form-check mb-1">
      <input class="form-check-input" type="checkbox" id="toggleEnd" onclick="toggleSection('end')" <?= isset($_GET['from_end']) ? 'checked' : '' ?>>
      <label class="form-check-label" for="toggleEnd">End Date</label>
    </div>
    <div id="endDateRange" class="d-none">
      <input type="date" name="from_end" class="form-control form-control-sm mb-1"
             value="<?= htmlspecialchars($_GET['from_end'] ?? '') ?>" onchange="autoSubmitIfFilled()">
      <input type="date" name="to_end" class="form-control form-control-sm"
             value="<?= htmlspecialchars($_GET['to_end'] ?? '') ?>" onchange="autoSubmitIfFilled()">
    </div>
  </div>
</div>

                      <div class="col-md-3 align-self-end">
  <a href="general_report.php" class="btn btn-secondary btn-sm w-100">Reset Filters</a>
</div>
          </div>
        </form>
      </div>
    </div>
    

    <!-- Data Table Section -->
    <div class="card mt-4">
      <div class="card-body">
        <div class="table-responsive">
    <table id="caseReportTable" class="table table-hover align-middle text-center" style="border: 2px solid black;">
    <thead class="table-secondary">
        <tr>
            <th style="border: 2px solid black;">#</th>
            <th style="border: 2px solid black;">Case ID</th>
            <th style="border: 2px solid black;">Plaintiff</th>
            <th style="border: 2px solid black;">Defendant</th>
            <th style="border: 2px solid black;">Case Type</th>
            <th style="border: 2px solid black;">Status</th>
            <th style="border: 2px solid black;">Created Date</th>
            <th style="border: 2px solid black;">Distributed Date</th>
            <th style="border: 2px solid black;">Decision</th>
            <th style="border: 2px solid black;">End Date</th>
            <th style="border: 2px solid black;">Plaintiff Count</th>
            <th style="border: 2px solid black;">Defendant Count</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $statusList = getCaseStatusList();
        $cases = getFilteredCaseReports($status, $case_type, $created_filter);
        
        if (empty($cases)): ?>
            <tr>
                <td colspan="12" class="text-center" style="border: 2px solid black;">No cases found</td>
            </tr>
        <?php else: ?>
            <?php foreach ($cases as $index => $case): ?>
                <tr>
                    <td style="border: 2px solid black;"><?= $index + 1 ?></td>
                    <td style="border: 2px solid black;"><?= htmlspecialchars($case['case_id']) ?></td>
                    <td style="border: 2px solid black;"><?= htmlspecialchars($case['plaintiff']) ?></td>
                    <td style="border: 2px solid black;"><?= htmlspecialchars($case['defendant']) ?></td>
                    <td style="border: 2px solid black;"><?= htmlspecialchars($case['case_type']) ?></td>
                    <td style="border: 2px solid black;"><?= htmlspecialchars($statusList[$case['case_status']] ?? 'Unknown') ?></td>
                    <td style="border: 2px solid black;"><?= htmlspecialchars($case['created_date']) ?></td>
                    <td style="border: 2px solid black;"><?= htmlspecialchars($case['distributed_date']) ?></td>
                    <td style="border: 2px solid black;"><?= htmlspecialchars($case['decision']) ?></td>
                    <td style="border: 2px solid black;"><?= htmlspecialchars($case['end_date']) ?></td>
                    <td style="border: 2px solid black;"><?= htmlspecialchars($case['total_plaintiff']) ?></td>
                    <td style="border: 2px solid black;"><?= htmlspecialchars($case['total_defendent']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
        </div>
      </div>
    </div>
  </div>
</div>


<?php
include('report_footer.php');
?>