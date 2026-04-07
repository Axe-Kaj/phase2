<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $missionID = (int) ($_POST['mission_id'] ?? 0);
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
        if ($missionID <= 0) {
            $error = "Mission ID must be a positive number.";
        } elseif (empty($startDateTime) || empty($endDateTime)) {
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
            $checkMission = $conn->prepare("
                SELECT COUNT(*)
                FROM Missions
                WHERE missionID = :mission_id
            ");
            $checkMission->bindParam(':mission_id', $missionID, PDO::PARAM_INT);
            $checkMission->execute();

            if ($checkMission->fetchColumn() > 0) {
                $error = "Mission ID already exists.";
            }
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
                INSERT INTO Missions (
                    missionID,
                    StartDateTime,
                    EndDateTime,
                    rendezvousAddress,
                    status,
                    odometerStart,
                    odometerEnd,
                    driverID,
                    truckID,
                    reservationID
                )
                VALUES (
                    :mission_id,
                    :start_datetime,
                    :end_datetime,
                    :rendezvous_address,
                    :status,
                    :odometer_start,
                    :odometer_end,
                    :driver_id,
                    :truck_id,
                    :reservation_id
                )
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
        } elseif (strpos($msg, 'Duplicate entry') !== false || strpos($msg, 'PRIMARY') !== false) {
            $error = "Mission ID already exists.";
        } else {
            $error = $msg;
        }
    }
}
?>

<div class="create-mission-form">
    <form id="create-form" action="./create.php" method="POST">

        <label for="mission_id">Mission ID</label>
        <input type="number" id="mission_id" name="mission_id"
               value="<?= htmlspecialchars($_POST['mission_id'] ?? '') ?>" required>

        <label for="start_datetime">Start DateTime</label>
        <input type="datetime-local" id="start_datetime" name="start_datetime"
               value="<?= htmlspecialchars($_POST['start_datetime'] ?? '') ?>" required>

        <label for="end_datetime">End DateTime</label>
        <input type="datetime-local" id="end_datetime" name="end_datetime"
               value="<?= htmlspecialchars($_POST['end_datetime'] ?? '') ?>" required>

        <label for="rendezvous_address">Rendez-vous address</label>
        <input type="text" id="rendezvous_address" name="rendezvous_address"
               value="<?= htmlspecialchars($_POST['rendezvous_address'] ?? '') ?>" required>

        <label for="status">Status</label>
        <select name="status" id="status" required>
            <option value="">-- Select Status --</option>
            <option value="pending" <?= (($_POST['status'] ?? '') === 'pending') ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= (($_POST['status'] ?? '') === 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
            <option value="cancelled" <?= (($_POST['status'] ?? '') === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
        </select>

        <label for="odometer_start">Odometer start</label>
        <input type="number" id="odometer_start" name="odometer_start"
               value="<?= htmlspecialchars($_POST['odometer_start'] ?? '') ?>" required>

        <label for="odometer_end">Odometer end</label>
        <input type="number" id="odometer_end" name="odometer_end"
               value="<?= htmlspecialchars($_POST['odometer_end'] ?? '') ?>" required>

        <label for="driver_id">Driver ID</label>
        <input type="number" id="driver_id" name="driver_id"
               value="<?= htmlspecialchars($_POST['driver_id'] ?? '') ?>" required>

        <label for="truck_id">Truck ID</label>
        <input type="number" id="truck_id" name="truck_id"
               value="<?= htmlspecialchars($_POST['truck_id'] ?? '') ?>" required>

        <label for="reservation_id">Reservation ID</label>
        <input type="number" id="reservation_id" name="reservation_id"
               value="<?= htmlspecialchars($_POST['reservation_id'] ?? '') ?>" required>

        <input type="submit" class="btn" value="Add mission">

        <?php if (!empty($error)) { ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php } ?>
    </form>

</div>