<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database.php';

$error = '';
$driver = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $driverID = (int) ($_POST['driver_id'] ?? 0);
    $FirstName = $_POST['first_name'] ?? '';
    $LastName = $_POST['last_name'] ?? '';
    $class = $_POST['class'] ?? '';

    try {
        $stmt = $conn->prepare("
            UPDATE Driver
            SET FirstName = :first_name,
                LastName = :last_name,
                class = :class
            WHERE driverID = :driver_id
        ");

        $stmt->bindParam(':driver_id', $driverID, PDO::PARAM_INT);
        $stmt->bindParam(':first_name', $FirstName);
        $stmt->bindParam(':last_name', $LastName);
        $stmt->bindParam(':class', $class);

        $stmt->execute();

        header('Location: ./index.php');
        exit();
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
} else {
    $driverID = (int) ($_GET['driver_id'] ?? 0);
}

$statement = $conn->prepare("
    SELECT *
    FROM Driver
    WHERE driverID = :driver_id
");
$statement->bindParam(':driver_id', $driverID, PDO::PARAM_INT);
$statement->execute();

$driver = $statement->fetch(PDO::FETCH_ASSOC);

if (!$driver) {
    die('Driver not found.');
}
?>

<div class="edit-driver-form">
    <form id="edit-form" action="./edit.php" method="POST">
        
        <input type="hidden" name="driver_id" value="<?= htmlspecialchars($driver['driverID']) ?>">

        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($driver['FirstName']) ?>" required><br><br>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($driver['LastName']) ?>" required><br><br>

        <label for="class">Class:</label>
        <select name="class" id="class" required>
            <option value="">-- Select Class --</option>
            <option value="tourism" <?= ($driver['class'] === 'tourism') ? 'selected' : '' ?>>tourism</option>
            <option value="heavyweight" <?= ($driver['class'] === 'heavyweight') ? 'selected' : '' ?>>heavyweight</option>
            <option value="super-heavyweight" <?= ($driver['class'] === 'super-heavyweight') ? 'selected' : '' ?>>super-heavyweight</option>
        </select><br><br>

        <input type="submit" class="btn" value="Save">

        <?php if (!empty($error)) { ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php } ?>
    </form>
</div>
