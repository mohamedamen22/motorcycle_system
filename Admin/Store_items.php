<?php

error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Initialize variables
$statusMsg = '';
$storeItemId = isset($_GET['store_item_id']) ? $_GET['store_item_id'] : '';

// Handle form submissions
if(isset($_POST['save'])) {
    $partId = $_POST['partId'];
    $storeLocation = $_POST['storeLocation'];
    $quantityInStock = $_POST['quantityInStock'];
    $lastStockedDate = $_POST['lastStockedDate'];

    // Insert new store item
    $query = "INSERT INTO store_items (part_id, store_location, quantity_in_stock, last_stocked_date) 
              VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isis", $partId, $storeLocation, $quantityInStock, $lastStockedDate);

    if($stmt->execute()) {
        $statusMsg = '<div class="alert alert-success">Store item added successfully!</div>';
    } else {
        $statusMsg = '<div class="alert alert-danger">Error adding store item: '.$conn->error.'</div>';
    }
}

if(isset($_POST['update'])) {
    $storeItemId = $_POST['storeItemId'];
    $partId = $_POST['partId'];
    $storeLocation = $_POST['storeLocation'];
    $quantityInStock = $_POST['quantityInStock'];
    $lastStockedDate = $_POST['lastStockedDate'];
    $lastAuditDate = $_POST['lastAuditDate'];

    // Update store item
    $query = "UPDATE store_items SET part_id = ?, store_location = ?, quantity_in_stock = ?, 
              last_stocked_date = ?, last_audit_date = ?
              WHERE store_item_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isissi", $partId, $storeLocation, $quantityInStock, $lastStockedDate, $lastAuditDate, $storeItemId);

    if($stmt->execute()) {
        $statusMsg = '<div class="alert alert-success">Store item updated successfully!</div>';
    } else {
        $statusMsg = '<div class="alert alert-danger">Error updating store item: '.$conn->error.'</div>';
    }
}

if(isset($_GET['action']) && $_GET['action'] == 'delete') {
    $storeItemId = $_GET['store_item_id'];

    // Delete store item
    $query = "DELETE FROM store_items WHERE store_item_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $storeItemId);

    if($stmt->execute()) {
        $statusMsg = '<div class="alert alert-success">Store item deleted successfully!</div>';
    } else {
        $statusMsg = '<div class="alert alert-danger">Error deleting store item: '.$conn->error.'</div>';
    }
}

// Fetch all parts for dropdown
$partsQuery = "SELECT * FROM parts ORDER BY part_name ASC";
$partsResult = $conn->query($partsQuery);
$parts = [];
while($row = $partsResult->fetch_assoc()) {
    $parts[] = $row;
}

// Fetch all store items
$itemsQuery = "SELECT si.*, p.part_name, p.part_number, p.category 
               FROM store_items si
               JOIN parts p ON si.part_id = p.part_id
               ORDER BY si.store_location, p.part_name ASC";
$itemsResult = $conn->query($itemsQuery);
$storeItems = [];
while($row = $itemsResult->fetch_assoc()) {
    $storeItems[] = $row;
}

// If editing, fetch the store item details
if(isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['store_item_id'])) {
    $editQuery = "SELECT * FROM store_items WHERE store_item_id = ?";
    $editStmt = $conn->prepare($editQuery);
    $editStmt->bind_param("i", $_GET['store_item_id']);
    $editStmt->execute();
    $editResult = $editStmt->get_result();
    $editRow = $editResult->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="img/logo/attnlg.jpg" rel="icon">
  <?php include 'includes/title.php';?>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <style>
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #3498db;
        --success-color: #27ae60;
        --danger-color: #e74c3c;
        --light-bg: #f8f9fa;
        --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    body {
        font-family: 'Segoe UI', system-ui, sans-serif;
        background-color: #f5f7fb;
    }
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        transition: transform 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .card-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border-radius: 12px 12px 0 0 !important;
        padding: 1.25rem 1.5rem;
        font-weight: 600;
    }
    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        transition: border-color 0.3s ease;
    }
    .form-control:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }
    .btn-primary {
        background-color: var(--secondary-color);
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        background-color: var(--primary-color);
        transform: translateY(-2px);
    }
    .table {
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    .table thead th {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 15px;
    }
    .table tbody tr {
        background: white;
        transition: all 0.3s ease;
        box-shadow: var(--card-shadow);
    }
    .table tbody tr:hover {
        transform: translateX(8px);
    }
    .dataTables_wrapper {
        padding: 0;
    }
    .dataTables_filter input {
        border-radius: 8px;
        padding: 8px 12px;
    }
    .alert {
        border-radius: 8px;
        padding: 15px 20px;
        margin: 20px 0;
    }
    .action-buttons .btn {
        padding: 8px 12px;
        margin: 0 3px;
        border-radius: 6px;
    }
    .btn-warning {
        background-color: #f39c12;
        border: none;
    }
    .btn-danger {
        background-color: var(--danger-color);
        border: none;
    }
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }
        .form-group.row > div {
            margin-bottom: 1rem;
        }
    }
  </style>
</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
    <?php include "Includes/sidebar.php";?>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
        <?php include "Includes/topbar.php";?>
        <!-- Topbar -->

        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-boxes"></i> Store Items Management</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Store Items</li>
            </ol>
          </div>

          <!-- Add New Item Button -->
          <div class="row">
            <div class="col-lg-12 mb-3">
              <div class="d-flex justify-content-end">
                <?php if(isset($editRow)): ?>
                  <a href="Store_items.php" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Add New Item
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <?php echo $statusMsg; ?>
              
              <!-- Add/Edit Store Item Form -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary"><?php echo isset($editRow) ? 'Edit' : 'Add'; ?> Store Item</h6>
                </div>
                <div class="card-body">
                  <form method="post" action="">
                    <?php if(isset($editRow)): ?>
                      <input type="hidden" name="storeItemId" value="<?php echo $editRow['store_item_id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Part</label>
                      <div class="col-sm-9">
                        <select class="form-control" name="partId" required>
                          <option value="">Select Part</option>
                          <?php foreach($parts as $part): ?>
                            <option value="<?php echo $part['part_id']; ?>" 
                              <?php if(isset($editRow) && $editRow['part_id'] == $part['part_id']) echo 'selected'; ?>>
                              <?php echo htmlspecialchars($part['part_name']); ?> (<?php echo htmlspecialchars($part['part_number']); ?>)
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Store Location</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" name="storeLocation" 
                          value="<?php echo isset($editRow) ? $editRow['store_location'] : ''; ?>" required>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Quantity in Stock</label>
                      <div class="col-sm-9">
                        <input type="number" class="form-control" name="quantityInStock" min="0" 
                          value="<?php echo isset($editRow) ? $editRow['quantity_in_stock'] : ''; ?>" required>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Last Stocked Date</label>
                      <div class="col-sm-9">
                        <input type="date" class="form-control" name="lastStockedDate" 
                          value="<?php echo isset($editRow) ? $editRow['last_stocked_date'] : ''; ?>">
                      </div>
                    </div>
                    
                    <?php if(isset($editRow)): ?>
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Last Audit Date</label>
                      <div class="col-sm-9">
                        <input type="date" class="form-control" name="lastAuditDate" 
                          value="<?php echo isset($editRow) ? $editRow['last_audit_date'] : ''; ?>">
                      </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group row">
                      <div class="col-sm-12 text-right">
                        <?php if(isset($editRow)): ?>
                          <button type="submit" name="update" class="btn btn-primary">Update Item</button>
                          <a href="Store_items.php" class="btn btn-secondary">Cancel</a>
                        <?php else: ?>
                          <button type="submit" name="save" class="btn btn-primary">Add Item</button>
                        <?php endif; ?>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Store Items List -->
              <div class="row">
                <div class="col-lg-12">
                  <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-primary">All Store Items</h6>
                    </div>
                    <div class="table-responsive p-3">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>Part Name</th>
                            <th>Part Number</th>
                            <th>Category</th>
                            <th>Store Location</th>
                            <th>Quantity</th>
                            <th>Last Stocked</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if(!empty($storeItems)): ?>
                            <?php foreach($storeItems as $index => $item): ?>
                              <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($item['part_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['part_number']); ?></td>
                                <td><?php echo htmlspecialchars($item['category']); ?></td>
                                <td><?php echo htmlspecialchars($item['store_location']); ?></td>
                                <td><?php echo $item['quantity_in_stock']; ?></td>
                                <td><?php echo $item['last_stocked_date'] ? date('M d, Y', strtotime($item['last_stocked_date'])) : 'N/A'; ?></td>
                                <td>
                                  <a href="?action=edit&store_item_id=<?php echo $item['store_item_id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-fw fa-edit"></i></a>
                                  <a href="?action=delete&store_item_id=<?php echo $item['store_item_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item?')"><i class="fas fa-fw fa-trash"></i></a>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <tr><td colspan="8" class="text-center">No store items found</td></tr>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--Row-->
        </div>
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
      <?php include "Includes/footer.php";?>
      <!-- Footer -->
    </div>
  </div>

  <!-- Scroll to top -->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
  <!-- Page level plugins -->
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function () {
      $('#dataTableHover').DataTable({
        "columnDefs": [
          { "orderable": false, "targets": [7] } // Disable sorting for action column
        ]
      });
    });
  </script>
</body>
</html>