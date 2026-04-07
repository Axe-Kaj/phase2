<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database.php';

$error = '';
$reservation = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservationId = (int) ($_POST['reservation_id'] ?? 0);
    $customerId = (int) ($_POST['customer_id'] ?? 0);
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $status = $_POST['status'] ?? '';

    try {
	$stmt = $conn->prepare("
            UPDATE Reservations
            SET StartDate = :start_date,
                EndDate = :end_date,
                Status = :status,
                customerID = :customer_id
            WHERE reservationID = :reservation_id
        ");

	$stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':customer_id', $customerId, PDO::PARAM_INT);
        $stmt->bindParam(':reservation_id', $reservationId, PDO::PARAM_INT);

        $stmt->execute();

        header('Location: ./index.php');
        exit();
    } catch (PDOException $e) {
        $msg = $e->getMessage();

if (strpos($msg, 'End date must be on or after start date') !== false) {
    $error = "End date must be on or after start date.";
        } elseif (strpos($msg, 'Reservation period cannot exceed 1 year.') !== false) {
            $error = "Reservation period cannot exceed 1 year.";
        } elseif (strpos($msg, 'foreign key constraint fails') !== false) {
            $error = "Customer ID does not exist.";
        } elseif (strpos($msg, 'Duplicate entry') !== false || strpos($msg, 'PRIMARY') !== false) {
            $error = "Reservation ID already exists.";
        } else {
            $error = $msg;
        }
    }
} else {
    $reservationId = (int) ($_GET['reservation_id'] ?? 0);
}

$statement = $conn->prepare("
    SELECT *
    FROM Reservations
    WHERE reservationID = :reservation_id
");
$statement->bindParam(':reservation_id', $reservationId, PDO::PARAM_INT);
$statement->execute();

$reservation = $statement->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    die('Reservation not found.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reservation</title>
</head>
<body>
    <h1>Edit Reservation</h1>

    <form action="./edit.php" method="POST">
        <input
            type="hidden"
            name="reservation_id"
            value="<?= htmlspecialchars($reservation['reservationID']) ?>"
        >
 <input
            type="hidden"
            name="customer_id"
            value="<?= htmlspecialchars($reservation['customerID']) ?>"
        >
<p>
    <strong>Reservation ID:</strong>
    <?= htmlspecialchars($reservation['reservationID']) ?><br>

    <strong>Customer ID:</strong>
    <?= htmlspecialchars($reservation['customerID']) ?>
</p>
        <label for="start_date">Start Date:</label>
        <input
            type="date"
            id="start_date"
            name="start_date"
            value="<?= htmlspecialchars($reservation['StartDate']) ?>"
            required
        ><br><br>

        <label for="end_date">End Date:</label>
        <input
            type="date"
            id="end_date"
            name="end_date"
            value="<?= htmlspecialchars($reservation['EndDate']) ?>"
            required
        ><br><br>

        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="pending" <?= $reservation['Status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= $reservation['Status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="cancelled" <?= $reservation['Status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select><br><br>

        <input type="submit" value="Update">

        <?php if (!empty($error)) { ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php } ?>
        
    </form>

    <br><br>
    <a href="./index.php">Back to reservation list</a>
</body>
</html>
