<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <title>CIMS Dashboard</title>
  <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport"/>
  <link rel="icon" href="../assets/img/icon.png" type="image/x-icon"/>
  <!-- Fonts and icons -->
  <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
  <script>
    WebFont.load({
      google: { families: ["Public Sans:300,400,500,600,700"] },
      custom: {
        families: [
          "Font Awesome 5 Solid",
          "Font Awesome 5 Regular",
          "Font Awesome 5 Brands",
          "simple-line-icons"
        ],
        urls: ['../assets/css/fonts.min.css']
      },
      active: function () {
        sessionStorage.fonts = true;
      }
    });
  </script>
  <!-- CSS Files -->
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/plugins.min.css">
  <link rel="stylesheet" href="../assets/css/kaiadmin.min.css">
  <style>
    .profile-img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar" data-background-color="dark">
      <div class="sidebar-logo">
        <div class="logo-header" data-background-color="dark">
          <div class="logo">
            <img src="../assets/img/logo.png" alt="Logo" class="navbar-brand" height="60" width="170">
          </div>
          <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
            <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
          </div>
          <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
        </div>
      </div>

      <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
          <ul class="nav nav-secondary">
            <li class="nav-item"><a href="admin.php"><i class="fas fa-home"></i><p>ADMIN</p></a></li>
            <li class="nav-item">
              <a data-bs-toggle="collapse" href="#users"><i class="fas fa-users-cog"></i><p>Manage Users</p><span class="caret"></span></a>
              <div class="collapse" id="users">
                <ul class="nav nav-collapse">
                  <li><a href="add_user.php"><i class="fas fa-user-plus"></i> Create Account</a></li>
                  <li><a href="view_userForUpdate.php"><i class="fas fa-user-edit"></i> Update Account</a></li>
                  <li><a href="delete_user.php"><i class="fas fa-user-times"></i> Delete Account</a></li>
                  <li><a href="Deactive_user.php"><i class="fas fa-user-slash"></i> De-Active Account</a></li>
                  <li><a href="list_user.php"><i class="fas fa-users"></i> View All Users</a></li>
                </ul>
              </div>
            </li>
<!--             
            <li class="nav-item">
              <a data-bs-toggle="collapse" href="#cases"><i class="fas fa-balance-scale"></i><p>Cases Status</p><span class="caret"></span></a>
              <div class="collapse" id="cases">
                <ul class="nav nav-collapse">
                  <li><a href="pending_case.php"><i class="fas fa-hourglass-half"></i> Pending Cases</a></li>
                  <li><a href="open_case.php"><i class="fas fa-folder-open"></i> Open Cases</a></li>
                  <li><a href="distributed_case.php"><i class="fas fa-gavel"></i> Distributed Cases</a></li>
                  <li><a href="pending_appointment.php"><i class="fas fa-hourglass-end"></i> Pending Appointment Cases</a></li>
                  <li><a href="appointed_case.php"><i class="fas fa-calendar-check"></i> Appointed Cases</a></li>
                  <li><a href="pending_decision.php"><i class="fas fa-hourglass-end"></i> Pending Decision Cases</a></li>
                  <li><a href="decided_case.php"><i class="fas fa-check-circle"></i> Decided Cases</a></li>
                </ul>
              </div>
            </li> -->
            <li class="nav-item">
              <a data-bs-toggle="collapse" href="#casesManagement"><i class="fas fa-file-contract"></i><p>Academic Management</p><span class="caret"></span></a>
              <div class="collapse" id="casesManagement">
                <ul class="nav nav-collapse">
                  <li><a href="update_case.php"><i class="fas fa-edit"></i> Update Cases</a></li>
                  <li><a href="delete_case.php"><i class="fas fa-trash-alt"></i> Delete Cases</a></li>
                  <li><a href="view_AllCases.php"><i class="fas fa-folder-open"></i> View All Cases</a></li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a data-bs-toggle="collapse" href="#feedback"><i class="fas fa-comments"></i><p>Feedback</p><span class="caret"></span></a>
              <div class="collapse" id="feedback">
                <ul class="nav nav-collapse">
                  <li><a href="view_feedback.php"><i class="fas fa-comments"></i> Customer Feedback</a></li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a data-bs-toggle="collapse" href="#reports"><i class="fas fa-chart-bar"></i><p>Reports</p><span class="caret"></span></a>
              <div class="collapse" id="reports">
                <ul class="nav nav-collapse">
                  <li><a href="general_report.php"><i class="fas fa-file-alt"></i> General Report</a></li>
                  <li><a href="litigant_report.php"><i class="fas fa-file-alt"></i> Litigant Report</a></li>
                </ul>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!-- End Sidebar -->

    <div class="main-panel">
      <div class="main-header">
        <div class="main-header-logo">
          <div class="logo-header" data-background-color="dark">
            <a href="index.html" class="logo">
              <img src="../assets/img/kaiadmin/logo_light.svg" alt="Logo" class="navbar-brand" height="20">
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
              <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
            </div>
            <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
          </div>
        </div>

        <!-- Navbar -->
        <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
          <div class="container-fluid">
            <div class="input-group">
              <div class="input-group-prepend">
                <h4>Dashboard</h4>
              </div>
            </div>
            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
              <!-- Notifications -->
              <li class="nav-item topbar-icon dropdown hidden-caret">
                <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-bell"></i>
                  <span class="notification" id="notifCount">0</span>
                </a>
                <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                  <li><div class="dropdown-title" id="notifTitle">You have 0 new notifications</div></li>
                  <li>
                    <div class="notif-scroll scrollbar-outer" style="max-height: 300px; overflow-y: auto;">
                      <div class="notif-center" id="notifList">
                        <!-- Notifications here -->
                      </div>
                    </div>
                  </li>
                  <li><a class="see-all" href="view_notifications.php">See all notifications<i class="fa fa-angle-right"></i></a></li>
                </ul>
              </li>

              <!-- User Profile -->
              <li class="nav-item topbar-user dropdown hidden-caret">
                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                  <div class="avatar-sm">
                    <img class="profile-img" src="../assets/img/default-profile.png" alt="Profile Picture">
                  </div>
                  <span class="profile-username"><span class="op-7">Admin</span></span>
                </a>
                <ul class="dropdown-menu dropdown-user animated fadeIn">
                  <div class="dropdown-user-scroll scrollbar-outer">
                    <li>
                      <div class="user-box">
                        <div class="avatar-lg">
                          <img class="profile-img" src="../assets/img/default-profile.png" alt="Profile Picture">
                        </div>
                        <div class="u-text">
                          <h4>Admin</h4>
                          <p class="text-muted">admin@example.com</p>
                          <a href="profile.php" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                          <a href="../connection/logout.php" class="btn btn-xs btn-secondary btn-sm">Logout</a>
                        </div>
                      </div>
                    </li>
                  </div>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
     
