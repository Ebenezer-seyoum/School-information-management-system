<?php
include '../connection/connection.php';

if (isset($_POST['region_id'])) {
    $region_id = intval($_POST['region_id']);
    $result = $conn->query("SELECT * FROM zones WHERE region_id = $region_id");

    echo '<option value="">Select Zone</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
    }
}
?>
