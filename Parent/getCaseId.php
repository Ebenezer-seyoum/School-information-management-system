<?php
session_start();
include '../connection/connection.php';
include '../connection/function.php';

if (isset($_GET['case_type'])) {
    echo getNextCaseId($_GET['case_type']);
}
?>

