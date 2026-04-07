<?php
require_once __DIR__ . '/../database.php';

$statement = $conn->prepare('SELECT * FROM Customer');
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
                <a class="nav-item active" href="./">Customers</a>
                <a class="nav-item" href="../driver/">Drivers</a>
                <a class="nav-item" href="../trucks/">Trucks</a>
                <a class="nav-item" href="../reservations/">Reservations</a>
                <a class="nav-item" href="../missions/">Missions</a>
                <a class="nav-item" href="../invoice/">Invoices</a>
                <a class="nav-item" href="../invoiceLine/">Invoice Lines</a>
                <a class="nav-item" href="../payment/">Payments</a>
            </nav>
        </div>

      <!-- Dynamic side content -->
      <div class="main">
        <div class="content">
          <div class="page-title">Customers</div>

            <div class="table-container">
              <div class="button-wrapper">
                  <a href="./create.php" class="btn" id="create-link">
                    <i class="fa-solid fa-pen"></i> Add customer</a>

                <table>
                  <thead>
                    <tr>
                      <td>ID</td>
                      <td>Type</td>
                      <td>Name</td>
                      <td>Phone Number</td>
                      <td>Street</td>
                      <td>City</td>
                      <td>Postal Code</td>
                      <td></td>
                    </tr>
                  </thead>
                    
                  <tbody>
                    <?php foreach ($all as $row): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($row['customerID']); ?></td>
                        <td><?php echo htmlspecialchars($row['CustomerType']); ?></td>
                        <td><?php echo htmlspecialchars($row['CustomerName']); ?></td>
                        <td><?php echo htmlspecialchars($row['PhoneNumber']); ?></td>
                        <td><?php echo htmlspecialchars($row['street']); ?></td>
                        <td><?php echo htmlspecialchars($row['City']); ?></td>
                        <td><?php echo htmlspecialchars($row['PostalCode']); ?></td>
                        <td>
                          <a href="./edit.php?customer_id=<?= $row['customerID'] ?>" title="Edit customer" class="edit-link" data-id="<?= $row['customerID'] ?>">
                            <i class="fa-solid fa-pen"></i></a>
                          <a href="./delete.php?customer_id=<?= $row['customerID'] ?>" title="Delete customer" class="delete-link" data-id="<?= $row['customerID'] ?>">
                            <i class="fa-solid fa-trash-can"></i>
                          </a>
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
              <div class="dialog-title">Edit 1 customer</div>
              <button class="close">&times;</button>
            </div>
            <div id="edit-dialog-body">
              <!-- form -->
            </div>
          </dialog>

          <!-- Create dialog -->
          <dialog id="create-dialog">
            <div class="dialog-header">
              <div class="dialog-title">Add customer</div>
              <button class="close">&times;</button>
            </div>
            <div id="create-dialog-body">
              <!-- form -->
            </div>
          </dialog>

          <!-- Delete dialog -->
          <dialog id="delete-dialog">
            <div class="dialog-header">
              <div class="dialog-title">Add customer</div>
              <button class="close">&times;</button>
            </div>
            <div id="delete-dialog-body">
              <!-- form -->
            </div>
          </dialog>

        </div>
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
          openDialog(editDialog, editBody, `edit.php?customer_id=${id}`);
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
          openDialog(deleteDialog, deleteBody, `delete.php?customer_id=${id}`);
        });
      });
      deleteDialog.querySelector('.close').addEventListener('click', () => deleteDialog.close())
    });
  </script>
</body>
</html>