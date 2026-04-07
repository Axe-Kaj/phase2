<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $truckID = (int)($_POST['truck_id'] ?? 0);
    $TruckType = $_POST['truck_type'] ?? '';
    $Brand = $_POST['brand'] ?? '';

    try {
        $checkTruck = $conn->prepare("
            SELECT COUNT(*)
            FROM Trucks
            WHERE truckID = :truck_id
        ");
        $checkTruck->bindParam(':truck_id', $truckID, PDO::PARAM_INT);
        $checkTruck->execute();

        if ($checkTruck->fetchColumn() > 0) {
            $error = "Truck ID already exists.";
        } 
        else {
            // Insert new mission
            $truck = $conn->prepare("
                INSERT INTO Trucks
                (truckID, TruckType, Brand)
                VALUES (:truck_id, :truck_type, :brand)
                 ");
                 $truck->bindParam(':truck_id', $truckID, PDO::PARAM_INT);
                 $truck->bindParam(':truck_type', $TruckType);
                 $truck->bindParam(':brand', $Brand);
            
            $truck->execute();
            header('Location: ./index.php');
            exit();
        }
    }
    catch (PDOException $e) {
        $msg = $e->getMessage();

       if (strpos($msg, 'Duplicate entry') !== false || strpos($msg, 'PRIMARY') !== false) {
            $error = "Truck ID already exists.";
        } else {
            $error = $msg;
        }
    }
}
?>

<div class="create-truck-form">
    <form id="create-form" action="./create.php" method="POST">
            
        <label for="truck_id">Truck ID</label>
        <input type="number" id="truck_id" name="truck_id" required>
        
        <label for="truck_type">Truck Type</label>
        <select name="truck_type" id="truck_type" required>
            <option value="">-- Select truck type --</option>
            <option value="Light Truck">Light truck</option>
            <option value="Heavy Truck">Heavy truck</option>
            <option value="Super Truck">Super truck</option>
        </select>
        
        <label for="brand">Brand</label>
        <input type="text" id="brand" name="brand" required>
        
        <input type="submit" class="btn" value="Add truck">

        <?php if (!empty($error)) { ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php } ?>
    </form>
</div>