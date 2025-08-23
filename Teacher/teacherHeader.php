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
    <a href="teacher.php" class="collapsed"><i class="fas fa-home"></i><p><?php echo $roleName = getRoleNameById($profile["user_type"]); ?></p></a></li>   
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
<!-- Attendance -->
<li class="nav-item">
  <a data-bs-toggle="collapse" href="#attendance" role="button" aria-expanded="false" aria-controls="attendance">
    <i class="fas fa-bullhorn"></i>
    <p>Attendance</p>
    <span class="caret"></span>
  </a>
  <div class="collapse" id="attendance">
    <ul class="nav nav-collapse">
      <li><a href="mark_attendance.php"><i class="fas fa-eye"></i> Mark Attendance</a></li>
      <li><a href="show_attendance.php"><i class="fas fa-eye"></i> View Attendance</a></li>
    </ul>
  </div>
</li>
<!-- Report -->
<li class="nav-item">
  <a data-bs-toggle="collapse" href="#report" role="button" aria-expanded="false" aria-controls="report">
    <i class="fas fa-bullhorn"></i>
    <p>Report Management</p>
    <span class="caret"></span>
  </a>
  <div class="collapse" id="report">
    <ul class="nav nav-collapse">
      <li><a href="subject_report.php"><i class="fas fa-eye"></i> subject Report</a></li>
      <li><a href="student_report.php"><i class="fas fa-eye"></i> student Report</a></li>
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
<!-- CSS for profile image -->
<style>
  .profile-img {
    width: 40px; 
    height: 40px;
    border-radius: 80%; 
    object-fit: cover; 
}
</style>
<!-- end CSS for profile image -->	  
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
    <span class="profile-username"><span class="op-7"><?php echo "$profile[username]";?></span></span></span>
 </a>
<ul class="dropdown-menu dropdown-user animated fadeIn">
  <div class="dropdown-user-scroll scrollbar-outer">
<li>
<div class="user-box">
  <div class="avatar-lg">
    <img class="profile-img" src="<?php echo $profile["profile_picture"]; ?>" alt="Profile Picture" width="100" height="100"></div>
	  <div class="u-text">
		<h4><?php echo "$profile[username]";?></h4>
		<p class="text-muted"><?php echo "$profile[email]";?></p>
        <a href="profile.php?uid=<?php echo $profile['uid']; ?>" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
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
</div>

	<!-- End Navbar -->  
<?php
} else {
    //header('location: ../index.php');
}
?>




    