<?php include "studentsHeader.php"; ?>
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
                  <h4 class="card-title">0</h4>
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
                  <h4 class="card-title">0</h4>
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
                  <h4 class="card-title">0</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Pending Appointment Cases -->
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round h-100">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-secondary bubble-shadow-small">
                  <i class="fas fa-clock"></i>
                </div>
              </div>
              <div class="col col-stats ms-3">
                <div class="numbers">
                  <p class="card-category">Pending Appointment Cases</p>
                  <h4 class="card-title">0</h4>
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
                  <h4 class="card-title">0</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Pending Decision Cases -->
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round h-100">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-secondary bubble-shadow-small">
                  <i class="fas fa-clock"></i>
                </div>
              </div>
              <div class="col col-stats ms-3">
                <div class="numbers">
                  <p class="card-category">Pending Decision Cases</p>
                  <h4 class="card-title">0</h4>
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
                  <h4 class="card-title">0</h4>
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

<?php include "../Admin/footer.php"; ?>
