<?php
ob_start();
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//error_reporting(0);
include '../connection/connection.php';
include '../connection/function.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title>SIMS  Dashboard</title>
	<meta content='width=device-width,initial-scale=1.0,shrink-to-fit=no'name='viewport'/>
	<link rel="icon" href="../assets/img/icon.png" type="image/x-icon"/>
	<!-- Fonts and icons -->
	<script src="../assets/js/plugin/webfont/webfont.min.js"></script>
	<script>
	WebFont.load({
	google: {"families":["Public Sans:300,400,500,600,700"]},
	custom: {"families":["Font Awesome 5 Solid","Font Awesome 5 Regular","Font Awesome 5 Brands",
		     "simple-line-icons"], urls: ['../assets/css/fonts.min.css']},
		active: function() {
		sessionStorage.fonts = true;
			}
		});
	</script>
	<!-- CSS Files -->
	<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="../assets/css/plugins.min.css">
	<link rel="stylesheet" href="../assets/css/kaiadmin.min.css">
<!-- Custom Layout Styles -->
<style>
:root {
  --sims-sidebar-width: 290px;
  --sims-sidebar-width-min: 72px;
  --sims-color-sidebar-bg: #081528; 
  --sims-color-navbar-bg: #081528;  
  --sims-color-text: #ffffff;       
  --sims-color-hover: rgba(255, 255, 255, 1); 
}

/* ===== Sidebar ===== */
.wrapper .sidebar {
  width: var(--sims-sidebar-width) !important;
  min-width: var(--sims-sidebar-width) !important;
  background-color: var(--sims-color-sidebar-bg) !important;
  color: var(--sims-color-text) !important;
  position: fixed;
  height: 100%;
  top: 0;
  left: 0;
  overflow-y: auto;
  transition: width .2s ease;
}
.sidebar .sidebar-wrapper {
    width: var(--sims-sidebar-width) !important;
}
.sidebar .nav-secondary > li > a {
  color: var(--sims-color-text) !important;
  display: flex;
  align-items: center;
  gap: .6rem;
}

/* Sidebar hover / expanded */
.sidebar .nav-secondary > li > a:hover,
.sidebar .nav-secondary > li > a[aria-expanded="true"] {
    background-color: #ffffff !important;
    color: #000000 !important;
}

/* Remove persistent active gray */
.sidebar .nav-secondary > li.active > a {
    background-color: transparent !important;
    color: var(--sims-color-text) !important;
}

/* Sidebar nested collapse links hover */
.sidebar .nav.nav-collapse li a:hover {
    background-color: #ffffff !important;
    color: #000000 !important;
}

/* Nested collapse links default color */
.sidebar .nav.nav-collapse li a {
    color: var(--sims-color-text) !important;
}

/* Sidebar caret */
.sidebar .nav-item > a .caret {
  border-top-color: currentColor;
  margin-left: auto;
  transition: transform .2s ease;
  transform: rotate(90deg); /* pointing right */
}
.sidebar .nav-item > a[aria-expanded="true"] .caret {
  transform: rotate(0deg); /* pointing down when open */
}

/* Main panel shift based on sidebar width */
.main-panel {
  margin-left: var(--sims-sidebar-width);
  transition: margin-left .2s ease;
}

/* Minimized sidebar */
.wrapper.sidebar-min .sidebar {
  width: var(--sims-sidebar-width-min) !important;
  min-width: var(--sims-sidebar-width-min) !important;
}
.wrapper.sidebar-min .main-panel {
  margin-left: var(--sims-sidebar-width-min);
}
.wrapper.sidebar-min .sidebar .nav-secondary > li > a p,
.wrapper.sidebar-min .sidebar .logo-header .logo img + span,
.wrapper.sidebar-min .sidebar .sidebar-content .nav.nav-collapse {
  display: none !important;
}
.wrapper.sidebar-min .sidebar .logo-header .logo img {
  width: 48px !important;
  height: auto !important;
}

/* ===== Navbar ===== */
.logo-header[data-background-color],
.main-header .navbar,
.navbar-header {
  background-color: var(--sims-color-navbar-bg) !important;
  color: var(--sims-color-text) !important;
}
.navbar .nav-link,
.navbar .fa,
.navbar .navbar-brand {
  color: var(--sims-color-text) !important;
}
.navbar .nav-link:hover,
.navbar .fa:hover {
  color: var(--sims-color-text) !important;
}

/* Notification badge */
.notification {
    background-color: #ff0000ff !important; /* red badge */
    color: #000000ff !important;
}

/* Profile image */
.profile-img {
    width: 50px;           
    height: 50px;          
    border-radius: 5px;    
    object-fit: cover;     
    border: 2px solid rgba(255, 255, 255, 1); 
}


/* Profile username */
.profile-username {
    color: #060505ff !important;
    font-weight: 100;
}

/* Header title */
.sims-title {
    color:#ffffff;
    margin:0;
    font-weight:600;
    letter-spacing:.3px;
}
/* Dropdown menus - white theme */
.dropdown-menu,
.dropdown-user,
.notif-box {
    border-radius: .5rem;
    box-shadow: 0 10px 30px rgba(0,0,0,.15);
    background-color: #ffffff !important;
    color: #000000 !important;
}

/* Dropdown items */
.dropdown-menu .dropdown-item {
  color: #000000 !important;
}
.dropdown-menu .dropdown-item:hover {
  background-color: #f0f0f0 !important;
  color: #000000 !important;
}

/* Dropdown buttons inside profile */
.dropdown-user .btn {
    background-color: #f0f0f0 !important;
    color: #000000 !important;
    border: none;
}
.dropdown-user .btn:hover {
    background-color: #dcdcdc !important;
    color: #000000 !important;
}

/* Notification dropdown content */
.notif-box .dropdown-title {
    color: #000000ff !important;
}

/* Logo image */
.logo-header .logo img {
    image-rendering: -webkit-optimize-contrast;
}
/* Avatar sizing: small in navbar, large in dropdown */
.navbar .topbar-user .avatar-sm .profile-img {
  width: 36px;
  height: 36px;
  border-radius: 50%;
}
.dropdown-user .avatar-lg .profile-img {
  width: 96px;
  height: 96px;
  border-radius: 8px;
}

/* Button colors in profile dropdown without changing markup */
.dropdown-user .dropdown-user-scroll a.btn-primary {
  background-color: #0d6efd !important;
  border-color: #0d6efd !important;
  color: #ffffff !important;
}
.dropdown-user .dropdown-user-scroll a.btn-primary:hover {
  background-color: #0b5ed7 !important;
  border-color: #0a58ca !important;
}
.dropdown-user .dropdown-user-scroll a[href*="logout.php"] {
  background-color: #dc3545 !important;
  border-color: #dc3545 !important;
  color: #ffffff !important;
}
.dropdown-user .dropdown-user-scroll a[href*="logout.php"]:hover {
  background-color: #bb2d3b !important;
  border-color: #b02a37 !important;
}
</style>
</head>
<?php
$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]); 

if (isset($_SESSION["uid"]) and ($roleName == "Director")) {
?>	
<body>
<div class="wrapper">
  <div class="sidebar" data-background-color="dark">
	<!-- Logo Header -->
<div class="sidebar-logo">
 <div class="logo-header" data-background-color="dark">
   <div class = "logo">
	<img src="../assets/img/logo.png" alt="navbar brand" class="navbar-brand" height="60" width="170">
   </div>
  <div class="nav-toggle">
	<button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
	<button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button></div>
	<button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button></div>
   </div>	
	<!-- End Logo Header -->
<!-- side bar -->
<div class="sidebar-wrapper scrollbar scrollbar-inner">
  <div class="sidebar-content">
	<ul class="nav nav-secondary">
	    <li class="nav-item">
          <a href="director.php" class="collapsed">
            <i class="fas fa-tachometer-alt"></i>
            <p style="font-weight: 200; text-transform: uppercase; margin: 0;">
              <?php echo htmlspecialchars($roleName . ' - ' . $profile['first_name'] . ' ' . $profile['father_name'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
          </a>
        </li>
<li class="nav-item">
    <a data-bs-toggle="collapse" href="#users" role="button" aria-expanded="false" aria-controls="users">
        <i class="fas fa-users-cog"></i>
        <p>Student Management</p>
        <span class="caret"></span>
    </a>
    <div class="collapse" id="users">
        <ul class="nav nav-collapse">
            <li><a href="register_student.php"><i class="fas fa-user-plus"></i> Register Student</a></li>
            <li><a href="view_studentForUpdate.php"><i class="fas fa-user-edit"></i> Update Student</a></li>
            <li><a href="view_studentForDelete.php"><i class="fas fa-user-times"></i> Delete Student</a></li>
            <li><a href="assign_student.php"><i class="fas fa-user-slash"></i>Assign Student</a></li>
            <li><a href="view_transfer_student.php"><i class="fas fa-user-slash"></i>Transfer Student</a></li>
            <li><a href="promote_student.php"><i class="fas fa-user-graduate"></i>Promote Student</a></li>
            <li><a href="report_card.php"><i class="fas fa-file-alt"></i> Generate Report Cards</a></li>
            <li><a href="view_allStudents.php"><i class="fas fa-users"></i> View All Students</a></li>
        </ul>
    </div>
</li>
<!-- Manage Teachers -->
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#teachers" role="button" aria-expanded="false" aria-controls="teachers">
          <i class="fas fa-chalkboard-teacher"></i>
          <p>Teacher Management</p>
          <span class="caret"></span>
        </a>
        <div class="collapse" id="teachers">
          <ul class="nav nav-collapse">
            <li><a href="assign_teacher.php"><i class="fas fa-user-plus"></i> Assign Teacher</a></li>
            <li><a href="view_transfer_teacher.php"><i class="fas fa-user-slash"></i>Transfer Teacher</a></li>
            <li><a href="view_AllTeacher.php"><i class="fas fa-users"></i> View All Teachers</a></li>
          </ul>
        </div>
      </li>
<!-- Manage Instructors -->
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#instructors" role="button" aria-expanded="false" aria-controls="instructors">
          <i class="fas fa-user-tie"></i>
          <p>Instructor Management</p>
          <span class="caret"></span>
        </a>
        <div class="collapse" id="instructors">
          <ul class="nav nav-collapse">
            <li><a href="assign_instructor.php"><i class="fas fa-user-plus"></i> Assign Instructor</a></li>
            <li><a href="view_transfer_instructor.php"><i class="fas fa-user-slash"></i>Transfer Instructor</a></li>
            <li><a href="view_Allnstructor.php"><i class="fas fa-users"></i> View All Instructors</a></li>
          </ul>
        </div>
      </li>
     <!-- Manage Class -->
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#class" role="button" aria-expanded="false" aria-controls="class">
          <i class="fas fa-chalkboard"></i>
          <p>Class Management</p>
          <span class="caret"></span>
        </a>
        <div class="collapse" id="class">
          <ul class="nav nav-collapse">
            <li><a href="add_class.php"><i class="fas fa-plus-circle"></i> Register Class</a></li>
            <li><a href="view_class.php"><i class="fas fa-eye"></i> View Class</a></li>
          </ul>
        </div>
      </li>
        <!-- Manage subject -->
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#subject" role="button" aria-expanded="false" aria-controls="subject">
          <i class="fas fa-book"></i>
          <p>Subject Management</p>
          <span class="caret"></span>
        </a>
        <div class="collapse" id="subject">
          <ul class="nav nav-collapse">
            <li><a href="add_subject.php"><i class="fas fa-plus-circle"></i> Register Subject</a></li>
            <li><a href="view_subject.php"><i class="fas fa-eye"></i> View Subject</a></li>
          </ul>
        </div>
      </li>

 <!-- Attendance Management -->
          <li class="nav-item">
            <a data-bs-toggle="collapse" href="#attendance" role="button" aria-expanded="false" aria-controls="attendance">
              <i class="fas fa-calendar-check"></i>
              <p>Attendance Management</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="attendance">
              <ul class="nav nav-collapse">
                <li><a href="view_attendance.php"><i class="fas fa-eye"></i> View Attendance</a></li>
                <li><a href="update_attendance.php"><i class="fas fa-edit"></i> Update Attendance</a></li>
              </ul>
            </div>
          </li>

          <!-- Grade Management -->
          <li class="nav-item">
            <a data-bs-toggle="collapse" href="#grades" role="button" aria-expanded="false" aria-controls="grades">
              <i class="fas fa-graduation-cap"></i>
              <p>Grade Management</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="grades">
              <ul class="nav nav-collapse">
                <li><a href="add_grade.php"><i class="fas fa-plus"></i> Manage Grade</a></li>
                <li><a href="mark_role.php"><i class="fas fa-user-cog"></i> Role to Edit</a></li>
                <li><a href="view_grades.php"><i class="fas fa-eye"></i> View Grades</a></li>
              </ul>
            </div>
          </li>

          <!-- Schedule Management -->
          <li class="nav-item">
            <a data-bs-toggle="collapse" href="#schedule" role="button" aria-expanded="false" aria-controls="schedule">
              <i class="fas fa-calendar-alt"></i>
              <p>Schedule Management</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="schedule">
              <ul class="nav nav-collapse">
                <li><a href="create_schedule.php"><i class="fas fa-plus-circle"></i> Create Schedule</a></li>
                <li><a href="view_schedule.php"><i class="fas fa-eye"></i> View Schedule</a></li>
                <li><a href="edit_schedule.php"><i class="fas fa-edit"></i> Edit Schedule</a></li>
              </ul>
            </div>
          </li>
          <!-- Feedback -->
          <li class="nav-item">
            <a data-bs-toggle="collapse" href="#feedback" role="button" aria-expanded="false" aria-controls="feedback">
              <i class="fas fa-comments"></i>
              <p>Feedback Management</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="feedback">
              <ul class="nav nav-collapse">
                <li><a href="customer_feedback.php"><i class="fas fa-users"></i> Customer Feedback</a></li>
              </ul>
            </div>
          </li>
            <!-- Report -->
    <li class="nav-item">
  <a data-bs-toggle="collapse" href="#reports" role="button" aria-expanded="false" aria-controls="reports">
    <i class="fas fa-chart-bar"></i>
    <p>Report Management</p>
    <span class="caret"></span>
  </a>
  <div class="collapse" id="reports">
    <ul class="nav nav-collapse">
      <li><a href="teacher_report.php"><i class="fas fa-chalkboard-teacher"></i> Teacher Report</a></li>
      <li><a href="student_report.php"><i class="fas fa-user-graduate"></i> Student Report</a></li>
      <li><a href="attendance_report.php"><i class="fas fa-users"></i> Attendance Report</a></li>

      <!-- Academic Report -->
      <li>
        <a data-bs-toggle="collapse" href="#academicReports" role="button" aria-expanded="false" aria-controls="academicReports">
          <i class="fas fa-users"></i> Academic Report
          <span class="caret"></span>
        </a>
        <div class="collapse" id="academicReports">
          <ul class="nav nav-collapse">
            <li><a href="academic_report_student.php"><i class="fas fa-user-graduate"></i> Student-level Report</a></li>
            <li><a href="academic_report_section.php"><i class="fas fa-layer-group"></i> Section-level Report</a></li>
          </ul>
        </div>
      </li>
    </ul>
  </div>
</li>
  <!-- Announcements -->
          <li class="nav-item">
            <a data-bs-toggle="collapse" href="#announcement" role="button" aria-expanded="false" aria-controls="announcement">
              <i class="fas fa-bullhorn"></i>
              <p>Announcement</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="announcement">
              <ul class="nav nav-collapse">
                <li><a href="create_announcement.php"><i class="fas fa-plus-circle"></i> Create Announcement</a></li>
                <li><a href="view_announcement.php"><i class="fas fa-eye"></i> View Announcements</a></li>
                <li><a href="edit_announcement.php"><i class="fas fa-edit"></i> Edit Announcement</a></li>
              </ul>
            </div>
          </li>
  </div>
 </div>
</div>
<!-- End Sidebar -->
<div class="main-panel">
	<div class="main-header">
		<div class="main-header-logo">
				<!-- Logo Header -->
	<div class="logo-header" data-background-color="dark">
		<a href="index.html" class="logo">
    <img src="../assets/img/logo.png" alt="navbar brand" class="navbar-brand" height="20"></a>
	<div class="nav-toggle">
		<button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
		<button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button></div>
		<button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button></div>
				<!-- End Logo Header -->
			</div>
				  <!--Notification -->
<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
  <div class="container-fluid">
	<div class="input-group">
	  <div class="input-group-prepend">									
		<h4>Balela Secondary School Dashboard </h4></div>			
	    <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
			        <!-- Language Selection -->

           <!-- End Language Selection -->
<!-- Notification -->
<ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
 <li class="nav-item topbar-icon dropdown hidden-caret">
  <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
   <i class="fa fa-bell"></i><span class="notification" id="notifCount">0</span></a>
      <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
      <li><div class="dropdown-title" id="notifTitle">You have 0 new notification</div></li>
	<li>
 <div class="notif-scroll scrollbar-outer" style="max-height: 300px; overflow-y: auto;">
   <div class="notif-center" id="notifList">
      <!-- Notifications will be loaded here --></div></div></li>
	<li><a class="see-all" href="view_notifications.php">See all notifications<i class="fa fa-angle-right"></i> </a></li>
      </ul>
	</li>
			  <!-- End Notification -->	
					  
<!-- User Profile -->
<li class="nav-item topbar-user dropdown hidden-caret">
 <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
    <div class="avatar-sm"><img class="profile-img" src="<?php echo $profile["profile_picture"]; ?>" 
	       alt="Profile Picture" width="100" height="100"></div>
 </a>
<ul class="dropdown-menu dropdown-user animated fadeIn">
  <div class="dropdown-user-scroll scrollbar-outer">
<li>
<div class="user-box">
    <img class="profile-img" src="<?php echo $profile["profile_picture"]; ?>" alt="Profile Picture" width="100" height="100">
	  <div class="u-text">
		<h4><?php echo $profile['first_name']. ' ' . $profile['father_name']; ?></h4>
		<p class="text-muted"><?php echo "$profile[email]";?></p>
       <a href="profile.php?uid=<?php echo $profile['uid']; ?>" class="btn btn-xs btn-primary btn-md">View Profile</a>
       <a href="../connection/logout.php" class="btn btn-xs btn-secondary btn-md">Logout</a>
								    </div>
								</div>
							</li>									
						</div>
					</ul>
				</li>
			</ul>
		</div>
	</nav>
</div>

	<!-- End Navbar -->  
<?php
} else {
    //header('location: ../index.php');
}
?>




    