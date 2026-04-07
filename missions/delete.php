<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../database.php';
$statement = $conn->prepare('DELETE FROM Missions WHERE missionID = :mission_id;');
$statement->bindParam(':mission_id', $_GET['mission_id']);
$statement->execute();
header('Location: .');
?>