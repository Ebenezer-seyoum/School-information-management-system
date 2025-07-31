<?php
include '../CIMS/Home/indexHeader.php';
?>
<!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
            <div class="container text-center py-5" style="max-width: 900px;">
                <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Case tracker</h4>
                <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">case Services</a></li>
                    <li class="breadcrumb-item active text-primary">case tracker</li>
                </ol>    
            </div>
        </div>
        <!-- Header End -->
<?php
$caseNumber = isset($_GET['CaseNumber']) ? $_GET['CaseNumber'] : '';
$plaintiff = isset($_GET['Plaintiff']) ? $_GET['Plaintiff'] : '';
$defendant = isset($_GET['Defendant']) ? $_GET['Defendant'] : '';

$cases = getCasesBySearch($caseNumber, $plaintiff, $defendant);
?>
 <!-- body -->
 <div class="container py-5">
    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold">Case Tracker</h1>                 
           </div>
            <h2 class="h4 fw-bold mb-4">Search Case</h2>
            <form method="GET" id="searchForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md">
                        <label for="CaseNumber" class="form-label fw-bold">Case File No.</label>
                        <input class="form-control fw-bold" id="CaseNumber" name="CaseNumber" type="text"
                         value="<?php echo htmlspecialchars($caseNumber); ?>">
                    </div>

                    <div class="col-md">
                        <label for="Plaintiff" class="form-label fw-bold">Plaintiff</label>
                        <input class="form-control fw-bold" id="Plaintiff" name="Plaintiff" type="text" 
                        value="<?php echo htmlspecialchars($plaintiff); ?>">
                    </div>

                    <div class="col-md">
                        <label for="Defendant" class="form-label fw-bold">Defendant</label>
                        <input class="form-control fw-bold" id="Defendant" name="Defendant" type="text"
                         value="<?php echo htmlspecialchars($defendant); ?>">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <button id="searchbutton" type="submit" class="btn btn-primary w-100 fw-bold">Search</button>
                    </div>
                </div>
            </form>

            <?php
            if (!empty($caseNumber) || !empty($plaintiff) || !empty($defendant)) {
                echo '<div class="mt-5">';
                if (count($cases) > 0) {
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-bordered table-striped text-center">';
                    echo '<thead class="table-dark">';
                    echo '<tr>';
                    echo '<th class="fw-bold">Case ID</th>';
                    echo '<th class="fw-bold">Plaintiff</th>';
                    echo '<th class="fw-bold">Defendant</th>';
                    echo '<th class="fw-bold">Who won</th>';
                    echo '<th class="fw-bold">Data Resolved</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                  foreach ($cases as $case) {
    echo '<tr>';
    echo '<td class="fw-bold">' . htmlspecialchars($case['case_id']) . '</td>';
    echo '<td class="fw-bold">' . htmlspecialchars($case['plaintiff']) . '</td>';
    echo '<td class="fw-bold">' . htmlspecialchars($case['defendant']) . '</td>';
    echo '<td class="fw-bold">' . htmlspecialchars($case['who_won'] ?? 'N/A') . '</td>';
    echo '<td class="fw-bold">' . htmlspecialchars($case['date_resolved'] ?? 'N/A') . '</td>';
    echo '</tr>';
}

                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                } else {
                    echo '<p class="fw-bold mt-3">No case found.</p>';
                }
                echo '</div>';
            }
            ?>

        </div> <!-- card-body -->
    </div> <!-- card -->
</div> <!-- container -->
</div> <!-- site-section -->              
        <?php
include '../CIMS/Home/indexFooter.php';
?>