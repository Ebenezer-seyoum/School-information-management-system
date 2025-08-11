<?php
session_start();
include '../connection/connection.php';
include '../connection/function.php';

if (isset($_GET['role_type']) && !empty($_GET['role_type'])) {
    echo getNextSchoolId($_GET['role_type']);
}
?>
