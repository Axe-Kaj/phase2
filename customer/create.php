<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerID = (int) ($_POST['customer_id'] ?? 0);
    $customerType = ($_POST['customer_type'] ?? '');
    $customerName = $_POST['customer_name'] ?? '';
    $phoneNumber = $_POST['phone_number'] ?? '';  
    $street = ($_POST['street'] ?? '');
    $city = ($_POST['city'] ?? '');
    $postalCode = ($_POST['postal_code'] ?? '');

    try {
        $checkCustomer = $conn->prepare("
            SELECT COUNT(*)
            FROM Customer
            WHERE customerID = :customer_id
        ");
        $checkCustomer->bindParam(':customer_id', $customerID, PDO::PARAM_INT);
        $checkCustomer->execute();

        if ($checkCustomer->fetchColumn() > 0) {
            $error = "Customer ID already exists.";
        } 
        else {
            // Insert new mission
            $customer = $conn->prepare("
                INSERT INTO Customer
                (customerID, CustomerType, CustomerName, PhoneNumber, street, City, PostalCode)
                VALUES (:customer_id, :customer_type, :customer_name, :phone_number, :street, :city, :postal_code)
                 ");
                 $customer->bindParam(':customer_id', $customerID, PDO::PARAM_INT);
                 $customer->bindParam(':customer_type', $customerType);
                 $customer->bindParam(':customer_name', $customerName);
                 $customer->bindParam(':phone_number', $phoneNumber);
                 $customer->bindParam(':street', $street);
                 $customer->bindParam(':city', $city);
                 $customer->bindParam(':postal_code', $postalCode);
            
            $customer->execute();
            header('Location: ./index.php');
            exit();
        }
    }
    catch (PDOException $e) {
        $msg = $e->getMessage();

       if (strpos($msg, 'Duplicate entry') !== false || strpos($msg, 'PRIMARY') !== false) {
            $error = "Customer ID already exists.";
        } else {
            $error = $msg;
        }
    }
}
?>

<div class="create-customer-form">
    <form id="create-form" action="./create.php" method="POST">
            
        <label for="customer_id">Customer ID</label>
        <input type="number" id="customer_id" name="customer_id" required>
        
        <label for="customer_type">Customer type</label>
        <select name = "customer_type" id="customer_type" required>
            <option value="">-- Select customer type --</option>
            <option value="Individual">Individual</option>
            <option value="Individual">Enterprise</option>
            <option value="Company">Company</option>
        </select>
        
        <label for="customer_name">Customer name</label>
        <input type="text" id="customer_name" name="customer_name" required>
            
        <label for="phone_number">Phone number</label>
        <input type="text" id="phone_number" name="phone_number" required>

        <label for="street">Street</label>
        <input type="text" id="street" name="street" required>
        
        <label for="city">City</label>
        <input type="text" id="city" name="city" required>
        
        <label for="postal_code">Postal Code</label>
        <input type="text" id="postal_code" name="postal_code" required>
        
        <input type="submit" class="btn" value="Add user">

        <?php if (!empty($error)) { ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php } ?>
    </form>
</div>