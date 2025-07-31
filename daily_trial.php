<?php
include '../CIMS/Home/indexHeader.php';
?>
 <!-- Header Start -->
 <div class="container-fluid bg-breadcrumb">
            <div class="container text-center py-5" style="max-width: 900px;">
                <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">daily court trial schedule</h4>
                <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Pages</a></li>
                    <li class="breadcrumb-item active text-primary">Course</li>
                </ol>    
            </div>
        </div>
        <!-- Header End -->

<!-- body -->
<div class="container-fluid feature bg-light py-5">
  <div class="container py-1"> 
    <div class="text-center mx-auto pb-1 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;"> 
      <h1 class="display-4 mb-4">Daily Court Trial</h1>                 
    </div>
  </div>
</div>   

<div class="site-section section-1 bg-light">
  <div class="container">
    <div class="card p-3">
      <?php
      $cases = getAssignedCasesWithAppointmentsToday();
      ?>
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Case ID</th>
              <th>Plaintiff</th>
              <th>Defendant</th>
              <th>Date Appointed</th>
              <th>Appointment Reason</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($cases) > 0): ?>
              <?php foreach ($cases as $index => $case): ?>
                <tr>
                  <th scope="row"><?php echo $index + 1; ?></th>
                  <td><?php echo $case['case_id_dup']; ?></td>
                  <td><?php echo $case['plaintiff']; ?></td>
                  <td><?php echo $case['defendant']; ?></td>
              
                  <td><?php echo $case['appointment_date']; ?></td>
                  
                  
                  <td><?php echo $case['appointment_reason']; ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="8">No cases scheduled for today.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div> 
    </div> 
  </div> 
</div>

<?php
include '../CIMS/Home/indexFooter.php';
?>