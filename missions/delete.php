<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../database.php';

$missionId = (int)($_GET['mission_id'] ?? 0);

try {
    $statement = $conn->prepare('DELETE FROM Missions WHERE missionID = :mission_id');
    $statement->bindParam(':mission_id', $missionId, PDO::PARAM_INT);
    $statement->execute();

    header('Location: ./index.php');
    exit();
} catch (PDOException $e) {
    echo "<h2>Cannot delete mission</h2>";
    echo "<p>This mission may be linked to invoice lines, so it cannot be deleted.</p>";
}
?>