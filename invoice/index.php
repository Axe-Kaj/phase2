<?php
require_once __DIR__ . '/../database.php';

$paid_flag = $_GET['paid_flag'] ?? '';

if ($paid_flag === '1') {
    $statement = $conn->prepare('SELECT * FROM Invoice WHERE PaidFlag = 1');
} elseif ($paid_flag === '0') {
    $statement = $conn->prepare('SELECT * FROM Invoice WHERE PaidFlag = 0');
} else {
    $statement = $conn->prepare('SELECT * FROM Invoice');
}
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

              <div class="filter">
                <i class="fa-solid fa-filter btn"></i>
                <div class="dropdown">
                  <form method="GET" class="filter-form">
                  
                  <label for="paid_flag">Payment status</label>  
                  <select name="paid_flag" onchange="this.form.submit()">
                      <option value="">All</option>
                      <option value="1" <?= (($_GET['paid_flag'] ?? '') === '1') ? 'selected' : '' ?>>Paid</option>
                      <option value="0" <?= (($_GET['paid_flag'] ?? '') === '0') ? 'selected' : '' ?>>Unpaid</option>
                    </select>
                  </form>
                </div>
              </div>
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
          
          <!-- Edit dialog -->
          <dialog id="edit-dialog">
            <div class="dialog-header">
              <div class="dialog-title">Edit 1 invoice</div>
              <button class="close">&times;</button>
            </div>
            <div id="edit-dialog-body">
              <!-- form -->
            </div>
          </dialog>
          <!-- Create dialog -->
          <dialog id="create-dialog">
            <div class="dialog-header">
              <div class="dialog-title">Add invoice</div>
              <button class="close">&times;</button>
            </div>
            <div id="create-dialog-body">
              <!-- form -->
            </div>
          </dialog>
          <!-- Delete dialog -->
          <dialog id="delete-dialog">
            <div class="dialog-header">
              <div class="dialog-title">Delete invoice</div>
              <button class="close">&times;</button>
            </div>
            <div id="delete-dialog-body">
              <!-- form -->
            </div>
          </dialog>  
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {

      // Open and load
      async function openDialog(dialog, modalBody, url) {
        dialog.showModal();
        try {
          const response = await fetch(url);
          const html = await response.text();
          modalBody.innerHTML = html;

          // Attach a single submit listener
          const form = modalBody.querySelector('form');
          if (!form) return;

          form.addEventListener('submit', async function(ev) {
            ev.preventDefault();
            const formData = new FormData(form);
            try {
              const res = await fetch(form.action, { method: 'POST', body: formData });
              const result = await res.text();
              if (result.includes("success")) {
                dialog.close();
                location.reload();
              } else {
                modalBody.innerHTML = result; // show errors
              }
            } catch (err) {
              alert("Form submission failed");
              console.error(err);
            }
          });
        } catch (err) {
          modalBody.innerHTML = "<p>Failed to load form.</p>";
          console.error(err);
        }
      }

      // Edit dialog
      const editDialog = document.getElementById('edit-dialog');
      const editBody = document.getElementById('edit-dialog-body');
      document.querySelectorAll('.edit-link').forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          const id = this.dataset.id;
          openDialog(editDialog, editBody, `edit.php?invoice_id=${id}`);
        });
      });
      editDialog.querySelector('.close').addEventListener('click', () => editDialog.close());

      // Create dialog
      const createDialog = document.getElementById('create-dialog');
      const createBody = document.getElementById('create-dialog-body');

      document.getElementById('create-link').addEventListener('click', function(e) {
        e.preventDefault();
        openDialog(createDialog, createBody, `create.php`);
      });
      createDialog.querySelector('.close').addEventListener('click', () => createDialog.close());

      // Delete dialog
      const deleteDialog = document.getElementById('delete-dialog');
      const deleteBody = document.getElementById('delete-dialog-body');
      document.querySelectorAll('.delete-link').forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          const id = this.dataset.id;
          openDialog(deleteDialog, deleteBody, `delete.php?invoice_id=${id}`);
        });
      });
      deleteDialog.querySelector('.close').addEventListener('click', () => deleteDialog.close())
    });
  </script>
</body>
</html>