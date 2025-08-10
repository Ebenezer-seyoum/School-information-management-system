<?php
include 'adminHeader.php';

$gender = $_GET['gender'] ?? '';
$region = $_GET['region'] ?? '';
$zone = $_GET['zone'] ?? '';
$woreda = $_GET['woreda'] ?? '';
$litigant_type = $_GET['litigant_type'] ?? '';
$wogen = $_GET['wogen'] ?? '';
?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Case Information Reports</h3>
    </div>
    <div class="card mb-3">
      <div class="card-body">
        <form method="GET" action="">
          <div class="row g-2">
            <!-- Gender -->
            <div class="col-md-3">
              <label class="form-label mb-1">Gender</label>
              <select name="gender" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All</option>
                <?php
                  $genders = getAllGenders();
                  foreach ($genders as $key => $label) {
                    $selected = ($gender == $key) ? 'selected' : '';
                    echo "<option value=\"$key\" $selected>$key</option>";
                  }
                ?>
              </select>
            </div>
            
            <!-- Region -->
            <div class="col-md-3">
              <label class="form-label mb-1">Region</label>
              <select name="region" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Regions</option>
                <?php
                  $regionList = getRegions();
                  foreach ($regionList as $key => $label) {
                    $selected = ($region !== '' && $region == $key) ? 'selected' : '';
                    echo "<option value=\"$key\" $selected>$label</option>";
                  }
                ?>
              </select>
            </div>
            
            <!-- Zone -->
            <div class="col-md-3">
              <label class="form-label mb-1">Zone</label>
              <select name="zone" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Zones</option>
                <?php
                  $zoneList = getZones();
                  foreach ($zoneList as $key => $label) {
                    $selected = ($zone !== '' && $zone == $key) ? 'selected' : '';
                    echo "<option value=\"$key\" $selected>$label</option>";
                  }
                ?>
              </select>
            </div>
            
            <!-- Woreda -->
            <div class="col-md-3">
              <label class="form-label mb-1">Woreda</label>
              <select name="woreda" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Woredas</option>
                <?php
                  $woredaList = getWoredas();
                  foreach ($woredaList as $key => $label) {
                    $selected = ($woreda !== '' && $woreda == $key) ? 'selected' : '';
                    echo "<option value=\"$key\" $selected>$label</option>";
                  }
                ?>
              </select>
            </div>
            
            <!-- Litigant Type -->
            <div class="col-md-3">
              <label class="form-label mb-1">Litigant Type</label>
              <select name="litigant_type" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Types</option>
                <?php
                  $litigantTypes = getLitigantTypes();
                  foreach ($litigantTypes as $key => $label) {
                    $selected = ($litigant_type == $key) ? 'selected' : '';
                    echo "<option value=\"$key\" $selected>$label</option>";
                  }
                ?>
              </select>
            </div>
            
            <!-- Wogen -->
            <div class="col-md-3">
              <label class="form-label mb-1">Wogen</label>
              <select name="wogen" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Wogens</option>
                <?php
                  $wogenList = getWogens();
                  foreach ($wogenList as $key => $label) {
                    $selected = ($wogen == $key) ? 'selected' : '';
                    echo "<option value=\"$key\" $selected>$label</option>";
                  }
                ?>
              </select>
            </div>
            <div class="col-md-3 align-self-end">
  <a href="litigant_report.php" class="btn btn-secondary btn-sm w-100">Reset</a>
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
                <th>#</th>
                <th>Case ID</th>
                <th>First Name</th>
                <th>Father Name</th>
                <th>Grandfather Name</th>
                <th>Gender</th>
                <th>Region</th>
                <th>Zone</th>
                <th>Woreda</th>
                <th>Litigant Type</th>
                <th>Wogen</th>
                
              </tr>
            </thead>
            <tbody>
              <?php
              $cases = getFilteredCaseInfoReports($gender, $region, $zone, $woreda, $litigant_type, $wogen);
              if (empty($cases)): ?>
                <tr>
                  <td colspan="17" class="text-center">No cases found</td>
                </tr>
              <?php else: ?>
                <?php foreach ($cases as $index => $case): ?>
                  <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($case['case_id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($case['first_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($case['father_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($case['grandfather_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($case['gender'] ?? '') ?></td>
                    <td><?= htmlspecialchars($case['region_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($case['zone_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($case['woreda_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($case['litigant_type'] ?? '') ?></td>
                    <td><?= htmlspecialchars($case['wogen'] ?? '') ?></td>
                  
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