<?php
include "directorHeader.php"; 
?>

<div class="container">
<div class="page-inner">
  <div class="row g-4"> 

<!-- All Students -->
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round h-100">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-primary bubble-shadow-small">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
                <div class="col col-stats ms-3">
                    <div class="numbers">
                        <p class="card-category">Total Students</p>
                        <h4 class="card-title"></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Teachers -->
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round h-100">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-info bubble-shadow-small">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
                <div class="col col-stats ms-3">
                    <div class="numbers">
                        <p class="card-category">Total Teachers</p>
                        <h4 class="card-title"></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Classes -->
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round h-100">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-success bubble-shadow-small">
                        <i class="fas fa-school"></i>
                    </div>
                </div>
                <div class="col col-stats ms-3">
                    <div class="numbers">
                        <p class="card-category">Total Classes</p>
                        <h4 class="card-title"></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Exams -->
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round h-100">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-warning bubble-shadow-small">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                <div class="col col-stats ms-3">
                    <div class="numbers">
                        <p class="card-category">Upcoming Exams</p>
                        <h4 class="card-title"></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Events -->
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round h-100">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-secondary bubble-shadow-small">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="col col-stats ms-3">
                    <div class="numbers">
                        <p class="card-category">Recent Events</p>
                        <h4 class="card-title"></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

  </div>		
</div>
</div>

<div class="mt-5">
  <!-- Chart Container -->
  <div class="d-flex justify-content-center mb-4">
    <div style="max-width: 700px; width: 100%;">
      <canvas id="combinedChart"></canvas>
    </div>
  </div>
</div>

<?php
include "footer.php";
?>

