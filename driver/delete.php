<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../database.php';
$statement = $conn->prepare('DELETE FROM Driver WHERE driverID = :driver_id;');
$statement->bindParam(':driver_id', $_GET['driver_id']);
$statement->execute();
header('Location: .');
?>