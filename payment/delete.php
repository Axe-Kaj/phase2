<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../database.php';

$paymentId = (int)($_GET['payment_id'] ?? 0);

try {
    $statement = $conn->prepare('DELETE FROM Payment WHERE PaymentID = :payment_id');
    $statement->bindParam(':payment_id', $paymentId, PDO::PARAM_INT);
    $statement->execute();

    header('Location: ./index.php');
    exit();
} catch (PDOException $e) {
    echo "<h2>Cannot delete payment</h2>";
    echo "<p>This payment may be linked to invoice lines or invoices.</p>";
}
?>