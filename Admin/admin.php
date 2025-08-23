<?php
include "adminHeader.php"; 

// ------- Quick helpers -------
function getScalar($conn, $sql) {
    $res = mysqli_query($conn, $sql);
    if (!$res) return 0;
    $row = mysqli_fetch_assoc($res);
    return (int)($row['total'] ?? 0);
}

// ------- Top metrics -------
$studentCount  = getScalar($conn, "SELECT COUNT(*) AS total FROM students");
$teacherCount  = getScalar($conn, "SELECT COUNT(*) AS total FROM users WHERE user_type = '1'");
$classCount    = getScalar($conn, "SELECT COUNT(*) AS total FROM sections");
$subjectCount  = getScalar($conn, "SELECT COUNT(*) AS total FROM subjects");

// ------- Announcements: Active vs Inactive -------
// "Active" means: today is within [start_date, end_date] if provided.
// If only end_date exists -> end_date >= today
// If only start_date exists -> start_date <= today
$annSql = "
    SELECT
        SUM(
            CASE
                WHEN
                    (COALESCE(start_date, '1900-01-01') <= CURDATE())
                    AND (COALESCE(end_date,   '2999-12-31') >= CURDATE())
                THEN 1 ELSE 0
            END
        ) AS active,
        SUM(
            CASE
                WHEN
                    (COALESCE(start_date, '1900-01-01') <= CURDATE())
                    AND (COALESCE(end_date,   '2999-12-31') >= CURDATE())
                THEN 0 ELSE 1
            END
        ) AS inactive
    FROM announcements
";
$annRes = mysqli_query($conn, $annSql);
$activeAnnouncements = 0; 
$inactiveAnnouncements = 0;
if ($annRes) {
    $annRow = mysqli_fetch_assoc($annRes);
    $activeAnnouncements   = (int)($annRow['active'] ?? 0);
    $inactiveAnnouncements = (int)($annRow['inactive'] ?? 0);
}
$announcementCount = $activeAnnouncements; // show only active on the big number
?>

<style>
  /* --- Typography: bolder & larger --- */
  .card .card-category {
    font-weight: 700 !important;
    font-size: 0.95rem !important;
    letter-spacing: .2px;
    margin-bottom: .15rem;
  }
  .card .card-title {
    font-weight: 800 !important;
    font-size: 1.85rem !important;
    line-height: 1.2;
    margin: 0;
  }
  .card.card-stats .icon-big {
    width: 56px; height: 56px;
    display: grid; place-items: center;
    border-radius: 14px;
  }
  .card.card-stats .numbers { margin-left: .25rem; }
  .metric-card { transition: transform .15s ease, box-shadow .15s ease; }
  .metric-card:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(0,0,0,.08); }

  /* Announcement badges next to icon */
  .ann-badges {
    display: flex; gap: .4rem; align-items: center; flex-wrap: wrap;
    margin-left: .75rem;
  }
  .ann-badge {
    font-weight: 800;
    font-size: .75rem;
    padding: .25rem .5rem;
    border-radius: 999px;
    line-height: 1;
    white-space: nowrap;
  }
  .ann-badge.active { background: #e8f5e9; color: #1b5e20; border: 1px solid #c8e6c9; }
  .ann-badge.inactive { background: #fff3e0; color: #e65100; border: 1px solid #ffe0b2; }

  /* Chart container */
  .chart-wrap { max-width: 1050px; margin: 0 auto; }
  .chart-card { border-radius: 18px; }
</style>

<div class="container">
  <div class="page-inner">
    <div class="row g-4"> 

      <!-- Students -->
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round h-100 metric-card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <div class="icon-big text-center icon-primary bubble-shadow-small">
                  <i class="fas fa-user-graduate"></i>
                </div>
              </div>
              <div class="col">
                <div class="numbers">
                  <p class="card-category">Total Students</p>
                  <h4 class="card-title"><?= $studentCount ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Teachers -->
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round h-100 metric-card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <div class="icon-big text-center icon-info bubble-shadow-small">
                  <i class="fas fa-chalkboard-teacher"></i>
                </div>
              </div>
              <div class="col">
                <div class="numbers">
                  <p class="card-category">Total Teachers</p>
                  <h4 class="card-title"><?= $teacherCount ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Classes -->
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round h-100 metric-card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <div class="icon-big text-center icon-success bubble-shadow-small">
                  <i class="fas fa-school"></i>
                </div>
              </div>
              <div class="col">
                <div class="numbers">
                  <p class="card-category">Total Classes</p>
                  <h4 class="card-title"><?= $classCount ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Subjects -->
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round h-100 metric-card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <div class="icon-big text-center icon-warning bubble-shadow-small">
                  <i class="fas fa-book"></i>
                </div>
              </div>
              <div class="col">
                <div class="numbers">
                  <p class="card-category">Total Subjects</p>
                  <h4 class="card-title"><?= $subjectCount ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Announcements: big number = Active only, with badges beside the icon -->
      <div class="col-sm-12 col-md-6 col-lg-4">
        <div class="card card-stats card-round h-100 metric-card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-auto d-flex align-items-center">
                <div class="icon-big text-center icon-secondary bubble-shadow-small">
                  <i class="fas fa-bullhorn"></i>
                </div>
                <div class="ann-badges" aria-label="Announcement status">
                  <span class="ann-badge active">Active: <?= $activeAnnouncements ?></span>
                  <span class="ann-badge inactive">Inactive: <?= $inactiveAnnouncements ?></span>
                </div>
              </div>
              <div class="col">
                <div class="numbers text-end text-md-start">
                  <p class="card-category">Announcements (Active)</p>
                  <h4 class="card-title"><?= $announcementCount ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <!-- Charts -->
    <div class="mt-4 chart-wrap">
      <div class="row g-4">
        <div class="col-md-7">
          <div class="card chart-card">
            <div class="card-body">
              <h5 class="fw-bold mb-3"><i class="far fa-chart-bar me-2"></i>Key Metrics</h5>
              <canvas id="metricsBar"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-5">
          <div class="card chart-card">
            <div class="card-body">
              <h5 class="fw-bold mb-3"><i class="far fa-chart-pie me-2"></i>Announcements Status</h5>
              <canvas id="annPie"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
