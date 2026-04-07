<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database.php';

$error = '';

$missionID = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $missionID = (int) ($_POST['mission_id'] ?? 0);
} else {
    $missionID = (int) ($_GET['mission_id'] ?? 0);
}

if ($missionID <= 0) {
    die('Mission not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDateTime = $_POST['start_datetime'] ?? '';
    $endDateTime = $_POST['end_datetime'] ?? '';
    $rendezvousAddress = trim($_POST['rendezvous_address'] ?? '');
    $status = $_POST['status'] ?? '';
    $odometerStart = (int) ($_POST['odometer_start'] ?? 0);
    $odometerEnd = (int) ($_POST['odometer_end'] ?? 0);
    $driverID = (int) ($_POST['driver_id'] ?? 0);
    $truckID = (int) ($_POST['truck_id'] ?? 0);
    $reservationID = (int) ($_POST['reservation_id'] ?? 0);

    try {
        if (empty($startDateTime) || empty($endDateTime)) {
            $error = "Start and end date/time are required.";
        } elseif (empty($rendezvousAddress)) {
            $error = "Rendezvous address is required.";
        } elseif (!in_array($status, ['pending', 'confirmed', 'cancelled'], true)) {
            $error = "Invalid status selected.";
        } elseif ($odometerStart < 0) {
            $error = "Odometer start cannot be negative.";
        } elseif ($odometerEnd < $odometerStart) {
            $error = "Odometer end must be greater than or equal to odometer start.";
        }

        if (empty($error)) {
            $checkDriver = $conn->prepare("
                SELECT COUNT(*)
                FROM Driver
                WHERE driverID = :driver_id
            ");
            $checkDriver->bindParam(':driver_id', $driverID, PDO::PARAM_INT);
            $checkDriver->execute();

            if ($checkDriver->fetchColumn() == 0) {
                $error = "Driver ID does not exist.";
            }
        }

        if (empty($error)) {
            $checkTruck = $conn->prepare("
                SELECT COUNT(*)
                FROM Trucks
                WHERE truckID = :truck_id
            ");
            $checkTruck->bindParam(':truck_id', $truckID, PDO::PARAM_INT);
            $checkTruck->execute();

            if ($checkTruck->fetchColumn() == 0) {
                $error = "Truck ID does not exist.";
            }
        }

        if (empty($error)) {
            $checkReservation = $conn->prepare("
                SELECT COUNT(*)
                FROM Reservations
                WHERE reservationID = :reservation_id
            ");
            $checkReservation->bindParam(':reservation_id', $reservationID, PDO::PARAM_INT);
            $checkReservation->execute();

            if ($checkReservation->fetchColumn() == 0) {
                $error = "Reservation ID does not exist.";
            }
        }

        if (empty($error)) {
            $mission = $conn->prepare("
                UPDATE Missions
                SET 
                    StartDateTime = :start_datetime,
                    EndDateTime = :end_datetime,
                    rendezvousAddress = :rendezvous_address,
                    status = :status,
                    odometerStart = :odometer_start,
                    odometerEnd = :odometer_end,
                    driverID = :driver_id,
                    truckID = :truck_id,
                    reservationID = :reservation_id
                WHERE missionID = :mission_id
            ");

            $mission->bindParam(':mission_id', $missionID, PDO::PARAM_INT);
            $mission->bindParam(':start_datetime', $startDateTime);
            $mission->bindParam(':end_datetime', $endDateTime);
            $mission->bindParam(':rendezvous_address', $rendezvousAddress);
            $mission->bindParam(':status', $status);
            $mission->bindParam(':odometer_start', $odometerStart, PDO::PARAM_INT);
            $mission->bindParam(':odometer_end', $odometerEnd, PDO::PARAM_INT);
            $mission->bindParam(':driver_id', $driverID, PDO::PARAM_INT);
            $mission->bindParam(':truck_id', $truckID, PDO::PARAM_INT);
            $mission->bindParam(':reservation_id', $reservationID, PDO::PARAM_INT);

            $mission->execute();

            header('Location: ./index.php');
            exit();
        }
    } catch (PDOException $e) {
        $msg = $e->getMessage();

        if (strpos($msg, 'Odometer start cannot be negative.') !== false) {
            $error = "Odometer start cannot be negative.";
        } elseif (strpos($msg, 'Odometer end must be greater than or equal to odometer start.') !== false) {
            $error = "Odometer end must be greater than or equal to odometer start.";
        } elseif (strpos($msg, 'foreign key constraint fails') !== false) {
            $error = "Driver ID, Truck ID, or Reservation ID does not exist.";
        } else {
            $error = $msg;
        }
    }
}

$statement = $conn->prepare("
    SELECT *
    FROM Missions
    WHERE missionID = :mission_id
");
$statement->bindParam(':mission_id', $missionID, PDO::PARAM_INT);
$statement->execute();

$mission = $statement->fetch(PDO::FETCH_ASSOC);

if (!$mission) {
    die('Mission not found.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mission</title>
</head>
<body>
    <h1>Edit Mission</h1>

    <form action="./edit.php" method="POST">
        <input type="hidden" name="mission_id" value="<?= htmlspecialchars($mission['missionID']) ?>">
        <input type="hidden" name="driver_id" value="<?= htmlspecialchars($mission['driverID']) ?>">
        <input type="hidden" name="truck_id" value="<?= htmlspecialchars($mission['truckID']) ?>">
        <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($mission['reservationID']) ?>">

        <p>
            <strong>Mission ID:</strong> <?= htmlspecialchars($mission['missionID']) ?><br>
            <strong>Driver ID:</strong> <?= htmlspecialchars($mission['driverID']) ?><br>
            <strong>Truck ID:</strong> <?= htmlspecialchars($mission['truckID']) ?><br>
            <strong>Reservation ID:</strong> <?= htmlspecialchars($mission['reservationID']) ?><br>
        </p>

        <label for="start_datetime">Start DateTime:</label>
        <input type="datetime-local" id="start_datetime" name="start_datetime"
               value="<?= htmlspecialchars($_POST['start_datetime'] ?? date('Y-m-d\TH:i', strtotime($mission['StartDateTime']))) ?>" required><br><br>

        <label for="end_datetime">End DateTime:</label>
        <input type="datetime-local" id="end_datetime" name="end_datetime"
               value="<?= htmlspecialchars($_POST['end_datetime'] ?? date('Y-m-d\TH:i', strtotime($mission['EndDateTime']))) ?>" required><br><br>

        <label for="rendezvous_address">Rendezvous Address:</label>
        <input type="text" id="rendezvous_address" name="rendezvous_address"
               value="<?= htmlspecialchars($_POST['rendezvous_address'] ?? $mission['rendezvousAddress']) ?>" required><br><br>

        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <?php $selectedStatus = $_POST['status'] ?? $mission['status']; ?>
            <option value="pending" <?= $selectedStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= $selectedStatus === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="cancelled" <?= $selectedStatus === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select><br><br>

        <label for="odometer_start">Odometer Start:</label>
        <input type="number" id="odometer_start" name="odometer_start"
               value="<?= htmlspecialchars($_POST['odometer_start'] ?? $mission['odometerStart']) ?>" required><br><br>

        <label for="odometer_end">Odometer End:</label>
        <input type="number" id="odometer_end" name="odometer_end"
               value="<?= htmlspecialchars($_POST['odometer_end'] ?? $mission['odometerEnd']) ?>" required><br><br>

        <input type="submit" value="Update">

        <?php if (!empty($error)) { ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php } ?>
    </form>

    <br><br>
    <a href="./index.php">Back to mission list</a>
</body>
</html>