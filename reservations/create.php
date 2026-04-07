<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservationId = (int) ($_POST['reservation_id'] ?? 0);
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $status = $_POST['status'] ?? '';
    $customerId = (int) ($_POST['customer_id'] ?? 0);

    try {
        // Check if reservation ID already exists
        $checkReservation = $conn->prepare("
            SELECT COUNT(*)
            FROM Reservations
            WHERE reservationID = :reservation_id
        ");
        $checkReservation->bindParam(':reservation_id', $reservationId, PDO::PARAM_INT);
        $checkReservation->execute();

        if ($checkReservation->fetchColumn() > 0) {
            $error = "Reservation ID already exists.";
        } else {
            // Check if customer ID exists
            $checkCustomer = $conn->prepare("
                SELECT COUNT(*)
                FROM Customer
                WHERE customerID = :customer_id
            ");
            $checkCustomer->bindParam(':customer_id', $customerId, PDO::PARAM_INT);
            $checkCustomer->execute();

            if ($checkCustomer->fetchColumn() == 0) {
                $error = "Customer ID does not exist.";
            } else {
                // Insert reservation
                $reservation = $conn->prepare("
                    INSERT INTO Reservations (reservationID, StartDate, EndDate, Status, customerID)
                    VALUES (:reservation_id, :start_date, :end_date, :status, :customer_id)
                ");

                $reservation->bindParam(':reservation_id', $reservationId, PDO::PARAM_INT);
                $reservation->bindParam(':start_date', $startDate);
                $reservation->bindParam(':end_date', $endDate);
                $reservation->bindParam(':status', $status);
                $reservation->bindParam(':customer_id', $customerId, PDO::PARAM_INT);
                $reservation->execute();

                header('Location: ./index.php');
                exit();
            }
        }
    } catch (PDOException $e) {
        $msg = $e->getMessage();

        if (strpos($msg, 'Reservations_chk_1') !== false) {
            $error = "End date must be on or after start date.";
        } elseif (strpos($msg, 'Start date cannot be in the past.') !== false) {
            $error = "Start date cannot be in the past.";
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
}
?>

<div class="create-reservation-form">
    <form id="create-form"  action="./create.php" method="POST">
        
    <label for="reservation_id">Reservation ID</label>
        <input type="number" id="reservation_id" name="reservation_id" required>

        <label for="start_date">Start date</label>
        <input type="date" id="start_date" name="start_date" required>

        <label for="end_date">End date</label>
        <input type="date" id="end_date" name="end_date" required>
        
        <label for="status">Status</label>
        <select name="status" id="status" required>
            <option value="">-- Select status --</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="cancelled">Cancelled</option>
        </select>

        <label for="customer_id">Customer ID</label>
        <input type="number" id="customer_id" name="customer_id" required>

        <input type="submit" class="btn" value="Add reservation">

        <?php if (!empty($error)) { ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php } ?>
    </form>
</div>