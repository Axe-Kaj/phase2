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

<div class="edit-reservation-form">
    <form id="edit-form" action="./edit.php" method="POST">
        
        <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($reservation['reservationID']) ?>">
        <input type="hidden" name="customer_id" value="<?= htmlspecialchars($reservation['customerID']) ?>">
        <p>
            <strong>Reservation ID:<?= htmlspecialchars($reservation['reservationID']) ?></strong>
            <strong>Customer ID:<?= htmlspecialchars($reservation['customerID']) ?></strong>
        </p>
        
        <label for="start_date">Start date</label>
        <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($reservation['StartDate']) ?>" required>

        <label for="end_date">End date</label>
        <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($reservation['EndDate']) ?>" required>

        <label for="status">Status</label>
        <select name="status" id="status" required>
            <option value="pending" <?= $reservation['Status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= $reservation['Status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="cancelled" <?= $reservation['Status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>

        <input type="submit" class="btn" value="Save">

        <?php if (!empty($error)) { ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php } ?>
        
    </form>
</div>
