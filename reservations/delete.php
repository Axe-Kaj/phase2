<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../database.php';
$statement = $conn->prepare('DELETE FROM Reservations WHERE reservationID = :reservation_id;');
$statement->bindParam(':reservation_id', $_GET['reservation_id']);
$statement->execute();
header('Location: .');
?>