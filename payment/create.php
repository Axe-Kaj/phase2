<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentId = (int)($_POST['payment_id'] ?? 0);
    $amount = $_POST['amount'] ?? '';
    $method = $_POST['method'] ?? '';
    $paymentDate = $_POST['payment_date'] ?? '';
    $invoiceId = (int)($_POST['invoice_id'] ?? 0);

    try {
	$stmt = $conn->prepare("
            INSERT INTO Payment (PaymentID, amount, Method, PaymentDate, InvoiceID)
            VALUES (:payment_id, :amount, :method, :payment_date, :invoice_id)
        ");

	$stmt->bindParam(':payment_id', $paymentId, PDO::PARAM_INT);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':method', $method);
        $stmt->bindParam(':payment_date', $paymentDate);
        $stmt->bindParam(':invoice_id', $invoiceId, PDO::PARAM_INT);

        $stmt->execute();

        header('Location: ./index.php');
        exit();
    } catch (PDOException $e) {
        $msg = $e->getMessage();

        if (strpos($msg, 'Duplicate entry') !== false || strpos($msg, 'PRIMARY') !== false) {
            $error = "Payment ID already exists.";
        } elseif (strpos($msg, 'foreign key constraint fails') !== false) {
            $error = "Invoice ID does not exist.";
        } elseif (strpos($msg, 'Data truncated') !== false) {
            $error = "Invalid payment method. Use credit card, cash, or check.";
        } elseif (strpos($msg, 'CHECK constraint') !== false || strpos($msg, 'amount') !== false) {
            $error = "Amount must be 0 or greater.";
        } else {
            $error = $msg;
        }
    }
}
?>

<div class="create-payment-form">
    <form id="create-form" action="./create.php" method="POST">
        
    <label for="payment_id">Payment ID</label>
        <input type="number" id="payment_id" name="payment_id" required>

        <label for="amount">Amount</label>
        <input type="number" step="0.01" id="amount" name="amount" required>

        <label for="method">Method</label>
        <select name="method" id="method" required>
            <option value="">-- Select payment method --</option>
            <option value="credit card">Credit card</option>
            <option value="cash">Cash</option>
            <option value="check">Cheque</option>
        </select>

        <label for="payment_date">Payment date</label>
        <input type="date" id="payment_date" name="payment_date" required>

        <label for="invoice_id">Invoice ID</label>
        <input type="number" id="invoice_id" name="invoice_id" required>

        <input type="submit" class="btn" value="Add payment">

        <?php if (!empty($error)) { ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php } ?>
    </form>
</div>