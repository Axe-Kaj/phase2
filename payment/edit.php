<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database.php';

$error = '';
$payment = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentId = (int)($_POST['payment_id'] ?? 0);
    $amount = $_POST['amount'] ?? '';
    $method = $_POST['method'] ?? '';
    $paymentDate = $_POST['payment_date'] ?? '';
    $invoiceId = (int)($_POST['invoice_id'] ?? 0);

    try {
      $stmt = $conn->prepare("
        UPDATE Payment
        SET amount = :amount,
            Method = :method,
            PaymentDate = :payment_date,
            InvoiceID = :invoice_id
        WHERE PaymentID = :payment_id
      ");

      $stmt->bindParam(':amount', $amount);
      $stmt->bindParam(':method', $method);
      $stmt->bindParam(':payment_date', $paymentDate);
      $stmt->bindParam(':invoice_id', $invoiceId, PDO::PARAM_INT);
      $stmt->bindParam(':payment_id', $paymentId, PDO::PARAM_INT);
      $stmt->execute();

      header('Location: ./index.php');
      exit();
    } catch (PDOException $e) {
      $error = $e->getMessage();
    }
} else {
  $paymentId = (int)($_GET['payment_id'] ?? 0);
}

$statement = $conn->prepare("SELECT * FROM Payment WHERE PaymentID = :payment_id");
$statement->bindParam(':payment_id', $paymentId, PDO::PARAM_INT);
$statement->execute();

$payment = $statement->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    die('Payment not found.');
}
?>

<div class="edit-payment-form">
  <form id="edit-form" action="./edit.php" method="POST">
    
    <input type="hidden" name="payment_id" value="<?= htmlspecialchars($payment['PaymentID']) ?>">
    <p>
      <strong>Payment ID: <?= htmlspecialchars($payment['PaymentID']) ?></strong>
    </p>
    
    <label for="amount">Amount</label>
    <input type="number" step="0.01" id="amount" name="amount" value="<?= htmlspecialchars($payment['amount']) ?>" required>
    
    <label for="method">Payment method</label>
    <select name="method" id="method" required>
        <option value="credit card" <?= $payment['Method'] === 'credit card' ? 'selected' : '' ?>>Credit card</option>
        <option value="cash" <?= $payment['Method'] === 'cash' ? 'selected' : '' ?>>Cash</option>
        <option value="check" <?= $payment['Method'] === 'check' ? 'selected' : '' ?>>Cheque</option>
    </select>
    
    <label for="payment_date">Payment date</label>
    <input type="date" id="payment_date" name="payment_date" value="<?= htmlspecialchars($payment['PaymentDate']) ?>" required>
    
    <label for="invoice_id">Invoice ID</label>
    <input type="number" id="invoice_id" name="invoice_id" value="<?= htmlspecialchars($payment['InvoiceID']) ?>" required>
    
    <input type="submit" class="btn" value="Save">
    
    <?php if (!empty($error)) { ?>
      <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php } ?>
  </form>

</div>



