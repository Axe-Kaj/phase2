<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../database.php';

$truckId = (int)($_GET['truck_id'] ?? 0);

try {
    $statement = $conn->prepare('DELETE FROM Trucks WHERE truckID = :truck_id');
    $statement->bindParam(':truck_id', $truckId, PDO::PARAM_INT);
    $statement->execute();

    header('Location: ./index.php');
    exit();
} 
catch (PDOException $e) {
    echo "<h2>Cannot delete truck</h2>";
    echo "<p>This truck may be linked to one or more missions.</p>";
}
?>


