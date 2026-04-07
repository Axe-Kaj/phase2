<?php
require_once __DIR__ . '/../database.php';

$statement = $conn->prepare('SELECT * FROM Payment');
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
                <a class="nav-item" href="../missions/">Missions</a>
                <a class="nav-item" href="../invoice/">Invoices</a>
                <a class="nav-item" href="../invoiceLine/">Invoice Lines</a>
                <a class="nav-item active" href="./">Payments</a>
            </nav>
        </div>

      <!-- Dynamic side content -->
      <div class="main">
        <div class="content">
          <div class="page-title">Payments</div>

          <div class="table-container">
            <div class="button-wrapper">
              <button class="btn" id="btn">
                <i class="fa-solid fa-plus"></i> Add payment
              </button>

              <table>
                <thead>
                  <tr>
                    <td>ID</td>
                    <td>Amount</td>
                    <td>Method</td>
                    <td>Date</td>
                    <td>InvoiceID</td>
                    <td></td>
                  </tr>
                </thead>
                  
                <tbody>
                  <?php foreach ($all as $row): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($row['PaymentID']); ?></td>
                      <td><?php echo htmlspecialchars($row['amount']); ?></td>
                      <td><?php echo htmlspecialchars($row['Method']); ?></td>
                      <td><?php echo htmlspecialchars($row['PaymentDate']); ?></td>
                      <td><?php echo htmlspecialchars($row['InvoiceID']); ?></td>
                      <td>
                        <a href="./edit.php?payment_id=<?= $row['PaymentID'] ?>" title="Edit payment">
                          <i class="fa-solid fa-pen"></i></a>
                        <a href="./delete.php?payment_id=<?= $row['PaymentID'] ?>" title="Delete payment">
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