<?php
require_once __DIR__ . '/database.php';

// Predefined Queries
$queryType = $_GET['queryType'] ?? '';
$minReservationID = $_GET['minReservationID'] ?? '';
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';
$brand = $_GET['brand'] ?? '';

$results = [];
$columns = [];
$error = '';

if ($queryType !== '') {
    try {
        switch ($queryType) {
            case 'business_customers':
                $sql = "
                    SELECT *
                    FROM Customer
                    WHERE CustomerType IN ('Enterprise', 'Company')
                ";
                $statement = $conn->prepare($sql);
                $statement->execute();
                break;

            case 'reservations_gt':
                $sql = "
                    SELECT *
                    FROM Reservations
                    WHERE reservationID > :minReservationID
                ";
                $statement = $conn->prepare($sql);
                $statement->execute([
                    ':minReservationID' => (int)$minReservationID
                ]);
                break;

            case 'missions_between_dates':
                $sql = "
                    SELECT
                        m.missionID,
                        m.StartDateTime,
                        m.EndDateTime,
                        m.status,
                        d.driverID,
                        d.FirstName,
                        d.LastName,
                        t.truckID,
                        t.Brand,
                        t.TruckType
                    FROM Missions m
                    JOIN Driver d ON m.driverID = d.driverID
                    JOIN Trucks t ON m.truckID = t.truckID
                    WHERE DATE(m.StartDateTime) BETWEEN :startDate AND :endDate
                ";
                $statement = $conn->prepare($sql);
                $statement->execute([
                    ':startDate' => $startDate,
                    ':endDate' => $endDate
                ]);
                break;

            case 'unpaid_customers':
                $sql = "
                    SELECT DISTINCT
                        c.customerID,
                        c.CustomerType,
                        c.CustomerName,
                        c.street,
                        c.City,
                        c.PostalCode,
                        i.InvoiceID,
                        i.TotalAmount,
                        i.PaidFlag
                    FROM Customer c
                    JOIN Invoice i ON c.customerID = i.customerID
                    WHERE i.PaidFlag = FALSE
                ";
                $statement = $conn->prepare($sql);
                $statement->execute();
                break;

            case 'drivers_by_brand':
                $sql = "
                    SELECT DISTINCT
                        d.driverID,
                        d.FirstName,
                        d.LastName
                    FROM Missions m
                    JOIN Driver d ON m.driverID = d.driverID
                    JOIN Trucks t ON m.truckID = t.truckID
                    WHERE t.Brand = :brand
                ";
                $statement = $conn->prepare($sql);
                $statement->execute([
                    ':brand' => $brand
                ]);
                break;

            default:
                $statement = null;
                $error = 'Invalid query type selected.';
                break;
        }

        if (isset($statement) && $statement) {
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($results)) {
                $columns = array_keys($results[0]);
            }
        }
    } catch (PDOException $e) {
        $error = 'Query failed: ' . $e->getMessage();
    }
}

// Custom Queries
$sqlResults = [];
$sqlColumns = [];
$sqlError = '';
$sqlQuery = $_POST['sqlQuery'] ?? '';

if (isset($_POST['runSql'])) {
    $sqlQuery = trim($sqlQuery);

    if ($sqlQuery === '') {
        $sqlError = 'Please enter a SQL query.';
    } else {
        $upperQuery = strtoupper(ltrim($sqlQuery));

        if (strpos($upperQuery, 'SELECT') !== 0) {
            $sqlError = 'Only SELECT queries are allowed.';
        } elseif (
            strpos($upperQuery, 'INSERT') !== false ||
            strpos($upperQuery, 'UPDATE') !== false ||
            strpos($upperQuery, 'DELETE') !== false ||
            strpos($upperQuery, 'DROP') !== false ||
            strpos($upperQuery, 'ALTER') !== false ||
            strpos($upperQuery, 'TRUNCATE') !== false
        ) {
            $sqlError = 'Dangerous SQL keywords are not allowed.';
        } else {
            try {
                $statement = $conn->prepare($sqlQuery);
                $statement->execute();
                $sqlResults = $statement->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($sqlResults)) {
                    $sqlColumns = array_keys($sqlResults[0]);
                }
            } catch (PDOException $e) {
                $sqlError = 'SQL Error: ' . $e->getMessage();
            }
        }
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENTRUCK Inc.</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    
</head>
<body>
    <!-- Top nav -->
    <nav class="topnav">
        <a class="nav-title" href="./">RENTRUCK</a>
        
        <div class="profile">
            <i class="fa-solid fa-users"></i>
            <div class="dropdown">
                <p><strong>COMP 353 - Phase 2</strong></p>
                <p>Group ID: nwc353_4</p>
                <p>
                    Matthew Greiss      40316531 
                    Aksheeta Kajrolkar  40223846
                    Aaisha Mushtaq      40285341
                    Aasiya Qadri        40263011
                </p>
            </div>
        </div>
    </nav>
    <div class="layout">
        <!-- Side nav -->
        <div class="sidebar">
            <nav class="side-nav">
                <a class="nav-item active" href="./">Home</a>
                <a class="nav-item" href="./customer/">Customers</a>
                <a class="nav-item" href="./driver/">Drivers</a>
                <a class="nav-item" href="./trucks/">Trucks</a>
                <a class="nav-item" href="./reservations/">Reservations</a>
                <a class="nav-item" href="./missions/">Missions</a>
                <a class="nav-item" href="./invoice/">Invoices</a>
                <a class="nav-item" href="./invoiceLine/">Invoice Lines</a>
                <a class="nav-item" href="./payment/">Payments</a>
            </nav>
        </div>

        <!-- Dynamic side content -->
        <div class="main">
            <div class="content">
                <div style="text-align:center">
                    <h1 style="font-size:52px;font-weight:600;letter-spacing:6px;margin-bottom:8px">RENTRUCK</h1>
                    <p style="font-size:12px;margin-bottom:48px;letter-spacing:1px">TRUCK RENTAL MANAGEMENT SYSTEM &nbsp;|&nbsp; COMP 353 &nbsp;|&nbsp; GROUP nwc353_4</p>
                </div>

                <div class="page-title">User Reports</div>
                <div class="page-desc" style="font-size: 13px;padding: 4px 0px 16px 2px;">Generate reports from predefined queries</div>
                
                <form method="GET" action="" style="width: 50%;">
                    
                    <label for="queryType">Report type</label>
                    <select name="queryType" id="queryType">
                        <option value="">-- Select a query --</option>
                        <option value="business_customers" <?= $queryType === 'business_customers' ? 'selected' : '' ?>>
                            Business customers
                        </option>
                        <option value="reservations_gt" <?= $queryType === 'reservations_gt' ? 'selected' : '' ?>>
                            Reservations with ID greater than
                        </option>
                        <option value="missions_between_dates" <?= $queryType === 'missions_between_dates' ? 'selected' : '' ?>>
                            Missions between two dates
                        </option>
                        <option value="unpaid_customers" <?= $queryType === 'unpaid_customers' ? 'selected' : '' ?>>
                            Customers with unpaid invoices
                        </option>
                        <option value="drivers_by_brand" <?= $queryType === 'drivers_by_brand' ? 'selected' : '' ?>>
                            Drivers who drove a selected brand
                        </option>
                    </select>
                    
                    <label for="minReservationID">Minimum reservation ID</label>
                    <input type="number" name="minReservationID" id="minReservationID" value="<?= htmlspecialchars($minReservationID) ?>">
                    
                    <label for="startDate">Start date</label>
                    <input type="date" name="startDate" id="startDate" value="<?= htmlspecialchars($startDate) ?>">
                    
                    <label for="endDate">End date</label>
                    <input type="date" name="endDate" id="endDate" value="<?= htmlspecialchars($endDate) ?>">
                    
                    <label for="brand">Truck brand</label>
                    <input type="text" name="brand" id="brand" value="<?= htmlspecialchars($brand) ?>">
                    
                    <input type="submit" class="btn" value="Generate">
                    <a href="index.php" class="btn">Reset</a>
                    
                    <?php if (!empty($error)) { ?>
                        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
                    <?php } ?>
                </form>

                <?php if ($queryType !== ''): ?>
                    <div class="page-title">Results</div>

                    <?php if (!empty($results)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <?php foreach ($columns as $column): ?>
                                        <th><?= htmlspecialchars($column) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $row): ?>
                                    <tr>
                                        <?php foreach ($columns as $column): ?>
                                            <td><?= htmlspecialchars((string)$row[$column]) ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No report results found</p>
                    <?php endif; ?>
                <?php endif; ?>                 

            </div>
        </div>
    </div>
</body>
</html>
