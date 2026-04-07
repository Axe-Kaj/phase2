<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database.php';

$error = '';
$truck = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $truckId = (int)($_POST['truck_id'] ?? 0);
    $TruckType = $_POST['truck_type'] ?? '';
    $Brand = $_POST['brand'] ?? '';

    try {
        $stmt = $conn->prepare("
            UPDATE Trucks
            SET TruckType = :truck_type,
                Brand = :brand
            WHERE truckID = :truck_id
        ");

	    $stmt->bindParam(':truck_type', $TruckType);
        $stmt->bindParam(':brand', $Brand);
        $stmt->bindParam(':truck_id', $truckId, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: ./index.php');
        exit();
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
} else {
    $truckId = (int)($_GET['truck_id'] ?? 0);
}

$statement = $conn->prepare("SELECT * FROM Trucks WHERE truckID = :truck_id");
$statement->bindParam(':truck_id', $truckId, PDO::PARAM_INT);
$statement->execute();

$truck = $statement->fetch(PDO::FETCH_ASSOC);

if (!$truck) {
    die('Truck not found.');
}
?>

<div class="edit-truck-form">
    <form id="edit-form" method="POST" action="./edit.php">

        <input type="hidden" name="truck_id" value="<?= htmlspecialchars($truck['truckID']) ?>">

        <label for="truck_type">Truck Type</label>
        <select name="truck_type" id="truck_type" required>
            <option value="">-- Select truck type --</option>
            <option value="Light Truck" <?= $truck['TruckType'] === 'Light Truck' ? 'selected' : '' ?>>Light truck</option>
            <option value="Heavy Truck" <?= $truck['TruckType'] === 'Heavy Truck' ? 'selected' : '' ?>>Heavy truck</option>
            <option value="Super Truck" <?= $truck['TruckType'] === 'Super Truck' ? 'selected' : '' ?>>Super truck</option>
        </select>
        
        <label for="brand">Brand</label>
        <input type="text" id="brand" name="brand" value="<?= htmlspecialchars($truck['Brand']) ?>" required>

        <input type="submit" class="btn" value="Save">

    </form>
</div>
