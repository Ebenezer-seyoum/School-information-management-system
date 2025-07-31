<?php include('judgeHeader.php'); ?>
?>
<!-- Page Header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">View Litigant</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Case Management</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">View Litigant</a></li>
      </ul>
    </div>
    <!-- End Page Header -->

    <!-- Main Content -->
    <div class="main-content">
      <section class="section">
        <div class="row">
          <div class="col-12 col-sm-12 col-lg-12">
            <div class="card">
              <div class="card-header">
                <div class="row w-100 align-items-center">
                  <div class="col-12 col-md-6 mb-2 mb-md-0">
                    <h4 class="mb-0">View Litigant</h4>
                  </div>
                  <div class="col-12 col-md-6">
                    <form method="GET">
                      <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by case_id....">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%;">
                    <thead class="table-secondary">
                      <tr>
                        <th style="border: 2px solid black;">#</th>
                        <th style="border: 2px solid black;">First Name</th>
                        <th style="border: 2px solid black;">Father Name</th>
                        <th style="border: 2px solid black;">Download</th>
                        <th style="border: 2px solid black;">View</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td colspan="5" style="border: 2px solid black;" class="text-center text-danger">No cases found.</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="text-left mt-4">
                  <a href="#" class="btn btn-outline-primary">
                    <i class="fa fa-arrow-left"></i> Back
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<?php include '../Admin/footer.php'; ?>
