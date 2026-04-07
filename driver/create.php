<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $driverID = (int) ($_POST['driver_id'] ?? 0);
    $FirstName = $_POST['first_name'] ?? '';
    $LastName = $_POST['last_name'] ?? '';
    $class = $_POST['class'] ?? '';


    try {
        $checkDriver = $conn->prepare("
            SELECT COUNT(*)
            FROM Driver
            WHERE driverID = :driver_id
        ");
        $checkDriver->bindParam(':driver_id', $customerID, PDO::PARAM_INT);
        $checkDriver->execute();

        if ($checkDriver->fetchColumn() > 0) {
            $error = "Driver ID already exists.";
        } 
        else {
            // Insert new mission
            $driver = $conn->prepare("
                INSERT INTO Driver
                (driverID, FirstName, LastName, class)
                VALUES (:driver_id, :first_name, :last_name, :class)
                 ");
                 $driver->bindParam(':driver_id', $driverID, PDO::PARAM_INT);
                    $driver->bindParam(':first_name', $FirstName);
                    $driver->bindParam(':last_name', $LastName);
                    $driver->bindParam(':class', $class);
            
            $driver->execute();
            header('Location: ./index.php');
            exit();
        }
    }
    catch (PDOException $e) {
        $msg = $e->getMessage();

       if (strpos($msg, 'Duplicate entry') !== false || strpos($msg, 'PRIMARY') !== false) {
            $error = "Driver ID already exists.";
        } else {
            $error = $msg;
        }
    }
}
?>

<div class="create-driver-form">
    <form id="create-form" action="./create.php" method="POST">
        
        <label for="driver_id">Driver ID</label><br>
        <input type="number" id="driver_id" name="driver_id" required>
        
        <label for="first_name">First name</label><br>
        <input type="text" id="first_name" name="first_name" required>
        
        <label for="last_name">Last name</label><br>
        <input type="text" id="last_name" name="last_name" required>

        <label for="class">Class</label><br>
        <select name = "class" id="class" required>
            <option value="">-- Select class --</option>
            <option value="tourism">Tourism</option>
            <option value="heavyweight">Heavyweight</option>
            <option value="super-heavyweight">Super-heavyweight</option>
        </select>
    
        <input type="submit" class="btn" value="Add driver">

        <?php if (!empty($error)) { ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php } ?>
    </form>
</div>