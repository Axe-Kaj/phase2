<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../database.php';

$driverID = (int)($_GET['driver_id'] ?? 0);

try {
    $statement = $conn->prepare('DELETE FROM Driver WHERE driverID = :driver_id;');
    $statement->bindParam(':driver_id', $driverID, PDO::PARAM_INT);
    $statement->execute();

    header('Location: ./index.php');
    exit();
} catch (PDOException $e) {
    echo "<h2>Cannot delete driver</h2>";
    echo "<p>This driver may be linked to reservations or missions, so it cannot be deleted.</p>";
}
?>