<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../database.php';
$statement = $conn->prepare('DELETE FROM Customer WHERE customerID = :customer_id;');
$statement->bindParam(':customer_id', $_GET['customer_id']);
$statement->execute();
header('Location: .');
?>