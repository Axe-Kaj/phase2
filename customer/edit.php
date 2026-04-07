<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database.php';

$error = '';
$customer = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize POST data
    $customerID = (int) ($_POST['customer_id'] ?? 0);
    $customerType = ($_POST['customer_type'] ?? '');
    $customerName = $_POST['customer_name'] ?? '';
    $phoneNumber = $_POST['phone_number'] ?? '';  
    $street = ($_POST['street'] ?? '');
    $city = ($_POST['city'] ?? '');
    $postalCode = ($_POST['postal_code'] ?? '');

    try {
        // Update Customer
        $stmt = $conn->prepare("
            UPDATE Customer
            SET CustomerName = :customer_name,
                CustomerType = :customer_type,
                PhoneNumber  = :phone_number,
                street = :street,
                City = :city,
                PostalCode = :postal_code
            WHERE customerID = :customer_id
        ");

        // Bind the parameters
        $stmt->bindParam(':customer_id', $customerID, PDO::PARAM_INT);
        $stmt->bindParam(':customer_name', $customerName);
        $stmt->bindParam(':customer_type', $customerType);
        $stmt->bindParam(':phone_number', $phoneNumber);
        $stmt->bindParam(':street', $street);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':postal_code', $postalCode);
        $stmt->execute();

        header('Location: ./index.php');
        exit();
    } catch (PDOException $e) {
        echo "error: " . $e->getMessage();
        exit();
    }
} else {
    // If it's a GET request, fetch the customer data
    $customerID = (int) ($_GET['customer_id'] ?? 0);
}

$statement = $conn->prepare("
    SELECT *
    FROM Customer
    WHERE customerID = :customer_id
");
$statement->bindParam(':customer_id', $customerID, PDO::PARAM_INT);
$statement->execute();

$customer = $statement->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    die('Customer not found.');
}
?>

<div class="edit-customer-form">
    <form id="edit-form" method="POST" action="./edit.php">

        <input type="hidden" name="customer_id" value="<?= htmlspecialchars($customer['customerID']) ?>">

        <label for="customer_name">Customer Name</label>
        <input type="text" id="customer_name" name="customer_name" value="<?= htmlspecialchars($customer['CustomerName']) ?>" required>

        <label for="phone_number">Phone Number</label>
        <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($customer['PhoneNumber']) ?>" required>

        <label for="street">Street</label>
        <input type="text" id="street" name="street" value="<?= htmlspecialchars($customer['street']) ?>" required>

        <label for="city">City</label>
        <input type="text" id="city" name="city" value="<?= htmlspecialchars($customer['City']) ?>" required>

        <label for="postal_code">Postal Code</label>
        <input type="text" id="postal_code" name="postal_code" value="<?= htmlspecialchars($customer['PostalCode']) ?>" required>

        <label for="customer_type">Customer Type</label>
        <select name="customer_type" id="customer_type" required>
            <option value="Individual" <?= $customer['CustomerType'] === 'Individual' ? 'selected' : '' ?>>Individual</option>
            <option value="Enterprise" <?= $customer['CustomerType'] === 'Enterprise' ? 'selected' : '' ?>>Enterprise</option>
            <option value="Company" <?= $customer['CustomerType'] === 'Company' ? 'selected' : '' ?>>Company</option>
        </select>

        <input type="submit" class="btn" value="Save">

    </form>
</div>
