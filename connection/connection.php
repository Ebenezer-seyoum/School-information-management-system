<?php
$host = getenv('MYSQLHOST') ?: 'localhost';
$username = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: '';
$dbname = getenv('MYSQLDATABASE') ?: 'SIMS';
$port = getenv('MYSQLPORT') ?: 3306;

$conn = mysqli_connect($host, $username, $password, $dbname, (int) $port);
if(!$conn){
    die('Connection failed: '.mysqli_connect_error());
}/*else{
    echo 'Connected successfully';
}*/
