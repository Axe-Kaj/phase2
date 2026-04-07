<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../database.php';

$reservationId = (int)($_GET['reservation_id'] ?? 0);

try {
    $statement = $conn->prepare('DELETE FROM Reservations WHERE reservationID = :reservation_id');
    $statement->bindParam(':reservation_id', $reservationId, PDO::PARAM_INT);
    $statement->execute();

    header('Location: ./index.php');
    exit();
} catch (PDOException $e) {
    echo "<h2>Cannot delete reservation</h2>";
    echo "<p>This reservation may be linked to one or more missions, so it cannot be deleted.</p>";
}
?>