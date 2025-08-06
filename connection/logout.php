<?php
session_start();  
if (isset($_SESSION["uid"])) {
    include 'function.php';  
    updateUserStatus(0, $_SESSION["uid"]);
    session_unset();
    session_destroy();
}
header('Location: ../login.php');
exit(); 
?>

