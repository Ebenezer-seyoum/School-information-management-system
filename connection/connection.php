<?php
$databaseUrl = getenv('MYSQL_URL');

if ($databaseUrl) {
    $database = parse_url($databaseUrl);

    $host = $database['host'] ?? 'localhost';
    $username = $database['user'] ?? 'root';
    $password = $database['pass'] ?? '';
    $dbname = isset($database['path']) ? ltrim($database['path'], '/') : 'SIMS';
    $port = $database['port'] ?? 3306;
} else {
    $host = getenv('MYSQLHOST') ?: 'localhost';
    $username = getenv('MYSQLUSER') ?: 'root';
    $password = getenv('MYSQLPASSWORD') ?: '';
    $dbname = getenv('MYSQLDATABASE') ?: 'SIMS';
    $port = getenv('MYSQLPORT') ?: 3306;
}

$conn = mysqli_connect($host, $username, $password, $dbname, (int) $port);
if(!$conn){
    die('Connection failed: '.mysqli_connect_error());
}/*else{
    echo 'Connected successfully';
}*/
