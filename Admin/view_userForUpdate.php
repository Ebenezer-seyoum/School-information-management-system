<?php
include('adminHeader.php');
?>
<!-- CSS for profile image -->
<style>
  .profile-img {
    width: 30px; 
    height: 30px;
    border-radius: 50%; 
    object-fit: cover; 
  }
</style>
<!-- end CSS for profile image -->

<!-- Page Header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Update Account</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Account</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Update Account</a></li>
      </ul>
    </div>
    <!-- End Page Header -->

    <!-- Main Content -->
    <div class="main-content">
      <section class="section">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="row w-100 align-items-center">
                  <div class="col-12 col-md-6 mb-2 mb-md-0">
                    <h4 class="mb-0">View all users</h4>
                  </div>
                  <div class="col-12 col-md-6">
                    <form method="GET">
                      <div class="input-group">
                        <input type="text" name="search" id="userSearch" class="form-control" style="font-weight: bold;" placeholder="Search by IdNumber, Name or user_type....">
                      </div>
                    </form>
                  </div>
                </div> 
              </div>

              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover align-middle text-center" id="userTable" style="border: 2px solid black; border-collapse: collapse; width: 60%; background-color: white;">
                    <thead class="table-secondary">
                      <tr>
                        <th style="border: 2px solid black;">#</th>
                        <th style="border: 2px solid black;">ID Number</th>
                        <th style="border: 2px solid black;">Profile Pic</th>
                        <th style="border: 2px solid black;">First Name</th>
                        <th style="border: 2px solid black;">Father Name</th>
                        <th style="border: 2px solid black;">User Status</th>
                        <th style="border: 2px solid black;">View Details</th>
                        <th style="border: 2px solid black;">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Example Row -->
                      <tr>
                        <td style="border: 2px solid black;">1</td>
                        <td style="border: 2px solid black;">12345</td>
                        <td style="border: 2px solid black;"><img class="profile-img" src="profile.jpg" alt="Profile Picture" width="100" height="100"></td>
                        <td style="border: 2px solid black;">John</td>
                        <td style="border: 2px solid black;">Doe</td>
                        <td style="border: 2px solid black;">Active</td>
                        <td style="border: 2px solid black;">
                          <a href="#" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Details</a>
                        </td>
                        <td style="border: 2px solid black;">
                          <a href="#"><i class="fas fa-file-signature fa-lg"></i></a>
                        </td>
                      </tr>
                      <!-- End Example Row -->
                      <tr><td colspan="8" class="text-center text-danger" style="border: 2px solid black;">No users found.</td></tr>
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
include('footer.php');
?>