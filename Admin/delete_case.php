<?php
include('adminHeader.php');
?>
<!-- Page Header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Delete case</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage cases</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Delete case</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <section class="section">
        <div class="row">
          <div class="col-12 col-sm-12 col-lg-12">
            <div class="card">
              <div class="card-header">
                <div class="row w-100 align-items-center">
                  <div class="col-12 col-md-6 mb-2 mb-md-0">
                    <h4 class="mb-0">View Case</h4>
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
                  <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%; background-color: white;">
                    <thead class="table-secondary">
                      <tr>
                        <th style="border: 2px solid black;">#</th>
                        <th style="border: 2px solid black;">case_id</th>
                        <th style="border: 2px solid black;">Plaintiff</th>
                        <th style="border: 2px solid black;">defendant</th>
                        <th style="border: 2px solid black;">case_status</th>
                        <th style="border: 2px solid black;">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Dynamic data would go here -->
                      <tr><td colspan="6" class="text-center text-danger" style="border: 2px solid black;">No cases found.</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>

            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<?php
include('../admin/footer.php');
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteCase(id) {
  Swal.fire({
    title: 'Are you sure?',
    text: "This will permanently delete the case.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "?duid=" + id;
    }
  });
}
</script>
