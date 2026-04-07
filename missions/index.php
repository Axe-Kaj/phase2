<?php
require_once __DIR__ . '/../database.php';

$statement = $conn->prepare('SELECT * FROM Missions');
$statement->execute();
$all = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENTRUCK Inc.</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    
</head>
<body>
    <!-- Top nav -->
    <nav class="topnav">
        <a class="nav-title" href="../index.php">RENTRUCK</a>
    </nav>
    <div class="layout">
        <!-- Side nav -->
        <div class="sidebar">
            <nav class="side-nav">
                <a class="nav-item" href="../index.php">Home</a>
                <a class="nav-item" href="../customer/">Customers</a>
                <a class="nav-item" href="../driver/">Drivers</a>
                <a class="nav-item" href="../trucks/">Trucks</a>
                <a class="nav-item" href="../reservations/">Reservations</a>
                <a class="nav-item active" href="./">Missions</a>
                <a class="nav-item" href="../invoice/">Invoices</a>
                <a class="nav-item" href="../invoiceLine/">Invoice Lines</a>
                <a class="nav-item" href="../payment/">Payments</a>
            </nav>
        </div>

      <!-- Dynamic side content -->
      <div class="main">
        <div class="content">
            <div class="page-title">Missions</div>
            
            <div class="table-container">
                <div class="button-wrapper">
                    <button class="btn" id="btn">
                        <i class="fa-solid fa-plus"></i> Add mission
                    </button>

                    <table>
                        <thead>
                        <tr>
                            <td>ID</td>
                            <td>Start Date</td>
                            <td>End Date</td>
                            <td>Rendez-vous address</td>
                            <td>Status</td>
                            <td>Odometer start</td>
                            <td>Odometer end</td>
                            <td>driverID</td>
                            <td>truckID</td>
                            <td>reservationID</td>
                            <td></td>
                        </tr>
                        </thead>
                        
                        <tbody>
                        <?php foreach ($all as $row): ?>
                            <tr>
                            <td><?php echo htmlspecialchars($row['missionID']); ?></td>
                            <td><?php echo htmlspecialchars($row['StartDateTime']); ?></td>
                            <td><?php echo htmlspecialchars($row['EndDateTime']); ?></td>
                            <td><?php echo htmlspecialchars($row['rendezvousAddress']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['odometerStart']); ?></td>
                            <td><?php echo htmlspecialchars($row['odometerEnd']); ?></td>
                            <td><?php echo htmlspecialchars($row['driverID']); ?></td>
                            <td><?php echo htmlspecialchars($row['truckID']); ?></td>
                            <td><?php echo htmlspecialchars($row['reservationID']); ?></td>
                            <td>
                                <a href="./edit.php?mission_id=<?= $row['missionID'] ?>" title="Edit mission">
                                <i class="fa-solid fa-pen"></i></a>
                                <a href="./delete.php?mission_id=<?= $row['missionID'] ?>" title="Delete mission">
                                <i class="fa-solid fa-trash-can"></i></a>
                            </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>    
                </div>
            </div> 
        </div>
    </div>
</body>
</html>