<?php
set_time_limit(300);

$expectedToken = getenv('IMPORT_TOKEN');
$providedToken = $_GET['token'] ?? '';

header('Content-Type: text/plain; charset=utf-8');

if (!$expectedToken || !hash_equals($expectedToken, $providedToken)) {
    http_response_code(403);
    exit("Forbidden: invalid import token.\n");
}

require __DIR__ . '/connection/connection.php';

$sqlFile = __DIR__ . '/Database/sims.sql';

if (!is_readable($sqlFile)) {
    http_response_code(500);
    exit("SQL file not found or not readable: {$sqlFile}\n");
}

$sql = file_get_contents($sqlFile);

if ($sql === false || trim($sql) === '') {
    http_response_code(500);
    exit("SQL file is empty or could not be read.\n");
}

mysqli_set_charset($conn, 'utf8mb4');

if (!mysqli_multi_query($conn, $sql)) {
    http_response_code(500);
    exit("Import failed: " . mysqli_error($conn) . "\n");
}

$statements = 1;

do {
    if ($result = mysqli_store_result($conn)) {
        mysqli_free_result($result);
    }

    if (mysqli_more_results($conn)) {
        $statements++;
    }
} while (mysqli_more_results($conn) && mysqli_next_result($conn));

if (mysqli_errno($conn)) {
    http_response_code(500);
    exit("Import stopped after {$statements} statements: " . mysqli_error($conn) . "\n");
}

echo "Database import completed successfully.\n";
echo "Executed statement groups: {$statements}\n";
