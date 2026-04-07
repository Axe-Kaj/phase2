<?php
require_once __DIR__ . '/../database.php';

$statement = $conn->prepare('SELECT * FROM Invoice');
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
                <a class="nav-item active" href="./">Invoices</a>
                <a class="nav-item" href="../invoiceLine/">Invoice Lines</a>
                <a class="nav-item" href="../payment/">Payments</a>
            </nav>
        </div>

      <!-- Dynamic side content -->
      <div class="main">
        <div class="content">
          <div class="page-title">Invoices</div>

          <div class="table-container">
            <div class="button-wrapper">
              <a href="./create.php" class="btn" id="create-link">
                <i class="fa-solid fa-plus"></i> Add invoice</a>
              </button>

              <table>
                <thead>
                  <tr>
                    <td>ID</td>
                    <td>Date</td>
                    <td>Total Amount</td>
                    <td>Paid</td>
                    <td>customerID</td>
                    <td></td>
                  </tr>
                </thead>
                  
                <tbody>
                  <?php foreach ($all as $row): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($row['InvoiceID']); ?></td>
                      <td><?php echo htmlspecialchars($row['InvoiceDate']); ?></td>
                      <td><?php echo htmlspecialchars($row['TotalAmount']); ?></td>
                      <td><?php echo htmlspecialchars($row['PaidFlag']); ?></td>
                      <td><?php echo htmlspecialchars($row['customerID']); ?></td>
                      <td>
                        <a href="./edit.php?invoice_id=<?= $row['InvoiceID'] ?>" title="Edit invoice" class="edit-link" data-id="<?= $row['InvoiceID'] ?>">
                          <i class="fa-solid fa-pen"></i></a>
                        <a href="./delete.php?invoice_id=<?= $row['InvoiceID'] ?>" title="Delete invoice" class="delete-link" data-id="<?= $row['InvoiceID'] ?>">
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