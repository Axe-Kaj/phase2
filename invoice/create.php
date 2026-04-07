<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $InvoiceID = (int) ($_POST['invoice_id'] ?? 0);
    $InvoiceDate = $_POST['invoice_date'] ?? '';
    $TotalAmount = $_POST['total_amount'] ?? '';
    $PaidFlag = $_POST['paid_flag'] ?? '';
    $customerID = $_POST['customer_id'] ?? '';


    try {
        $checkInvoice = $conn->prepare("
            SELECT COUNT(*)
            FROM Invoice
            WHERE InvoiceID = :invoice_id
        ");
        $checkInvoice->bindParam(':invoice_id', $InvoiceID, PDO::PARAM_INT);
        $checkInvoice->execute();

        if ($checkInvoice->fetchColumn() > 0) {
            $error = "Invoice ID already exists.";
        } 
        else {
            // Insert new mission
            $driver = $conn->prepare("
                INSERT INTO Invoice
                (InvoiceID, InvoiceDate, TotalAmount, PaidFlag, customerID)
                VALUES (:invoice_id, :invoice_date, :total_amount, :paid_flag, :customer_id)
                 ");
                 $driver->bindParam(':invoice_id', $driverID, PDO::PARAM_INT);
                    $driver->bindParam(':invoice_date', $FirstName);
                    $driver->bindParam(':total_amount', $LastName);
                    $driver->bindParam(':paid_flag', $PaidFlag);
                    $driver->bindParam(':customer_id', $customerID);
            
            $driver->execute();
            header('Location: ./index.php');
            exit();
        }
    }
    catch (PDOException $e) {
        $msg = $e->getMessage();

       if (strpos($msg, 'Duplicate entry') !== false || strpos($msg, 'PRIMARY') !== false) {
            $error = "Invoice ID already exists.";
        } else {
            $error = $msg;
        }
    }
}
?>

<div class="create-driver-form">
    <form id="create-form" action="./create.php" method="POST">
        
        <label for="invoice_id">Invoice ID</label>
        <input type="number" id="invoice_id" name="invoice_id" required>
        
        <label for="invoice_date">Invoice date</label>
        <input type="text" id="invoice_date" name="invoice_date" required>
        
        <label for="total_amount">Total amount</label>
        <input type="text" id="total_amount" name="total_amount" required>

        <label for="paid_flag">Paid</label>
        <input type="text" id="paid_flag" name="paid_flag" required>

        <label for="customer_id">CustomerID</label>
        <input type="text" id="customer_id" name="customer_id" required>
    
        <input type="submit" class="btn" value="Add invoice">

        <?php if (!empty($error)) { ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php } ?>
    </form>
</div>