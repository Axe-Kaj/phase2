<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database.php';

$error = '';
$invoice = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $InvoiceID = (int) ($_POST['invoice_id'] ?? 0);
    $InvoiceDate = $_POST['invoice_date'] ?? '';
    $TotalAmount = $_POST['total_amount'] ?? '';
    $PaidFlag = $_POST['paid_flag'] ?? '';
    $customerID = $_POST['customer_id'] ?? '';
    
    try {
        $stmt = $conn->prepare("
            UPDATE Invoice
            SET InvoiceDate = :invoice_date,
                TotalAmount = :total_amount,
                PaidFlag = :paid_flag
                customerID = :customer_id
            WHERE InvoiceID = :invoice_id
        ");

        $stmt->bindParam(':invoice_id', $InvoiceID, PDO::PARAM_INT);
        $stmt->bindParam(':invoice_date', $InvoiceDate);
        $stmt->bindParam(':total_amount', $TotalAmount);
        $stmt->bindParam(':paid_flag', $PaidFlag);
        $stmt->bindParam(':customer_id', $customerID);

        $stmt->execute();

        header('Location: ./index.php');
        exit();
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
} else {
    $InvoiceID = (int) ($_GET['invoice_id'] ?? 0);
}

$statement = $conn->prepare("
    SELECT *
    FROM Invoice
    WHERE InvoiceID = :invoice_id
");
$statement->bindParam(':invoice_id', $InvoiceID, PDO::PARAM_INT);
$statement->execute();

$invoice = $statement->fetch(PDO::FETCH_ASSOC);

if (!$invoice) {
    die('Invoice not found.');
}
?>

<div class="edit-invoice-form">
    <form id="edit-form" action="./edit.php" method="POST">
        
        <input type="hidden" name="invoice_id" value="<?= htmlspecialchars($invoice['InvoiceID']) ?>">

        <label for="invoice_date">Invoice date</label>
        <input type="text" id="invoice_date" name="invoice_date" value="<?= htmlspecialchars($invoice['InvoiceDate']) ?>" required>

        <label for="total_amount">Total Amount</label>
        <input type="text" id="total_amount" name="total_amount" value="<?= htmlspecialchars($invoice['TotalAmount']) ?>" required>

        <label for="paid_flag">Paid</label>
        <input type="text" id="paid_flag" name="paid_flag" value="<?= htmlspecialchars($invoice['PaidFlag']) ?>" required>

        <label for="customer_id">CustomerID</label>
        <input type="text" id="customer_id" name="customer_id" value="<?= htmlspecialchars($invoice['customerID']) ?>" required>

        <input type="submit" class="btn" value="Save">

        <?php if (!empty($error)) { ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php } ?>
    </form>
</div>