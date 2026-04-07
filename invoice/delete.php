<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../database.php';

$invoiceId = (int)($_GET['invoice_id'] ?? 0);

try {
    $statement = $conn->prepare('DELETE FROM Invoice WHERE InvoiceID = :invoice_id');
    $statement->bindParam(':invoice_id', $invoiceId, PDO::PARAM_INT);
    $statement->execute();

    header('Location: ./index.php');
    exit();
} catch (PDOException $e) {
    echo "<h2>Cannot delete invoice</h2>";
    echo "<p>This invoice may be linked to invoice lines or payments.</p>";
}
?>