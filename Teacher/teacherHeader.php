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
  --sims-sidebar-width: 280px;
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

if (isset($_SESSION["uid"]) and ($roleName == "Teacher")) {
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
          <a href="teacher.php" class="collapsed">
            <i class="fas fa-tachometer-alt"></i>
            <p style="font-weight: 200; text-transform: uppercase; margin: 0;">
              <?php echo htmlspecialchars($roleName . ' - ' . $profile['first_name'] . ' ' . $profile['father_name'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
          </a>
        </li>
 <!-- My Classes -->
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#myClasses" role="button" aria-expanded="false" aria-controls="myClasses">
          <i class="fas fa-chalkboard"></i>
          <p>Class Management</p>
          <span class="caret"></span>
        </a>
        <div class="collapse" id="myClasses">
          <ul class="nav nav-collapse">
            <li><a href="view_my_classes.php"><i class="fas fa-list"></i>Assigned Classes</a></li>
            <li><a href="view_students_in_class.php"><i class="fas fa-users"></i>Enrolled Students</a></li>
            <li><a href="view_attendance.php"><i class="fas fa-users"></i>Attendance Records</a></li>
          </ul>
        </div>
      </li>
<!-- Assessments -->
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#Assessments" role="button" aria-expanded="false" aria-controls="Assessments">
          <i class="fas fa-pen"></i>
          <p>Assessments</p>
          <span class="caret"></span>
        </a>
        <div class="collapse" id="Assessments">
          <ul class="nav nav-collapse">
            <li><a href="add_marks.php"><i class="fas fa-plus-circle"></i> Manage Grades</a></li>
            <li><a href="view_marks.php"><i class="fas fa-eye"></i> Assessment Results</a></li>
          </ul>
        </div>
      </li>

      <!-- Academic Management -->
      <li class="nav-item">
        <a data-bs-toggle="collapse" href="#academic" role="button" aria-expanded="false" aria-controls="academic">
          <i class="fas fa-book"></i>
          <p>Academic Schedule</p>
          <span class="caret"></span>
        </a>
        <div class="collapse" id="academic">
          <ul class="nav nav-collapse">
            <li><a href="view_timetable.php"><i class="fas fa-calendar-check"></i>Class Schedule</a></li>
            <li><a href="view_timetable.php"><i class="fas fa-calendar-check"></i>Academic Sessions</a></li>
          </ul>
        </div>
      </li>
  <!-- Attendance -->
<li class="nav-item">
  <a data-bs-toggle="collapse" href="#attendance" role="button" aria-expanded="false" aria-controls="attendance">
    <i class="fas fa-calendar-check"></i>
    <p>Attendance</p>
    <span class="caret"></span>
  </a>
  <div class="collapse" id="attendance">
    <ul class="nav nav-collapse">
        <li><a href="mark_attendance.php"><i class="fas fa-calendar-check"></i> Mark Attendance</a></li>
        <li><a href="show_attendance.php"><i class="fas fa-table"></i> View Attendance</a></li>
    </ul>
  </div>
</li>    
<!-- Announcements -->
<li class="nav-item">
  <a data-bs-toggle="collapse" href="#announcements" role="button" aria-expanded="false" aria-controls="announcements">
    <i class="fas fa-bullhorn"></i>
    <p>Announcements</p>
    <span class="caret"></span>
  </a>
  <div class="collapse" id="announcements">
    <ul class="nav nav-collapse">
      <li><a href="view_announcements.php"><i class="fas fa-eye"></i> View Announcements</a></li>
    </ul>
  </div>
</li>


<!-- Report -->
<li class="nav-item">
  <a data-bs-toggle="collapse" href="#report" role="button" aria-expanded="false" aria-controls="report">
        <i class="fas fa-chart-bar"></i>
    <p>Report Management</p>
    <span class="caret"></span>
  </a>
  <div class="collapse" id="report">
    <ul class="nav nav-collapse">
      <li><a href="subject_report.php"><i class="fas fa-user-graduate"></i> subject Report</a></li>
      <li><a href="student_report.php"> <i class="fas fa-book-open"></i>  student Report</a></li>
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




    