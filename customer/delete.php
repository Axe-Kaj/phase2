<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../database.php';

$customerId = (int)($_GET['customer_id'] ?? 0);

try {
    $statement = $conn->prepare('DELETE FROM Customer WHERE customerID = :customer_id');
    $statement->bindParam(':customer_id', $customerId, PDO::PARAM_INT);
    $statement->execute();

    header('Location: ./index.php');
    exit();
} catch (PDOException $e) {
    echo "<h2>Cannot delete customer</h2>";
    echo "<p>This customer may be linked to reservations or invoices, so it cannot be deleted.</p>";
}
?>