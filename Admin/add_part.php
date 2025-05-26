<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

$statusMsg = '';
$partId = isset($_GET['part_id']) ? intval($_GET['part_id']) : 0;

// Handle form submission
if(isset($_POST['save']) || isset($_POST['update'])) {
    // Sanitize and validate input
    $partName = mysqli_real_escape_string($conn, trim($_POST['partName']));
    $partNumber = mysqli_real_escape_string($conn, trim($_POST['partNumber']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $category = mysqli_real_escape_string($conn, trim($_POST['category']));
    $quantity = intval($_POST['quantity']);
    $unitPrice = floatval($_POST['unitPrice']);
    $supplierId = !empty($_POST['supplierId']) ? intval($_POST['supplierId']) : 'NULL';
    $reorderLevel = isset($_POST['reorderLevel']) ? intval($_POST['reorderLevel']) : 0;
    
    if(isset($_POST['save'])) {
        // Add new part
        $query = "INSERT INTO parts (part_name, part_number, description, category, quantity, unit_price, supplier_id, reorder_level) 
                  VALUES ('$partName', '$partNumber', '$description', '$category', $quantity, $unitPrice, $supplierId, $reorderLevel)";
        
        if($conn->query($query)) {
            $statusMsg = '<div class="alert alert-success">Part added successfully!</div>';
            // Clear form after successful submission
            unset($_POST);
        } else {
            $statusMsg = '<div class="alert alert-danger">Error adding part: '.$conn->error.'</div>';
        }
    } elseif(isset($_POST['update'])) {
        // Update existing part
        $query = "UPDATE parts SET 
                  part_name = '$partName',
                  part_number = '$partNumber',
                  description = '$description',
                  category = '$category',
                  quantity = $quantity,
                  unit_price = $unitPrice,
                  supplier_id = $supplierId,
                  reorder_level = $reorderLevel
                  WHERE part_id = $partId";
        
        if($conn->query($query)) {
            $statusMsg = '<div class="alert alert-success">Part updated successfully!</div>';
        } else {
            $statusMsg = '<div class="alert alert-danger">Error updating part: '.$conn->error.'</div>';
        }
    }
}

// Handle delete action
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['part_id'])) {
    $deleteId = intval($_GET['part_id']);
    $query = "DELETE FROM parts WHERE part_id = $deleteId";
    if($conn->query($query)) {
        $statusMsg = '<div class="alert alert-success">Part deleted successfully!</div>';
    } else {
        $statusMsg = '<div class="alert alert-danger">Error deleting part: '.$conn->error.'</div>';
    }
}

// Fetch part data for editing
$row = [];
if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['part_id'])) {
    $partId = intval($_GET['part_id']);
    $query = "SELECT * FROM parts WHERE part_id = $partId";
    $result = $conn->query($query);
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
}

// Fetch suppliers for dropdown
$suppliers = [];
$supplierQuery = "SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name";
$supplierResult = $conn->query($supplierQuery);
if($supplierResult->num_rows > 0) {
    while($supplier = $supplierResult->fetch_assoc()) {
        $suppliers[] = $supplier;
    }
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
    /* Professional Dashboard Styles */
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
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-cogs"></i> Parts Management</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Parts</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary"><?php echo isset($row['part_id']) ? 'Edit Part' : 'Add New Part'; ?></h6>
                  <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                        <div class="col-xl-4">
                            <label class="form-control-label">Part Name<span class="text-danger ml-2">*</span></label>
                            <input type="text" class="form-control" name="partName" value="<?php echo isset($row['part_name']) ? htmlspecialchars($row['part_name']) : ''; ?>" required>
                        </div>
                        <div class="col-xl-4">
                            <label class="form-control-label">Part Number<span class="text-danger ml-2">*</span></label>
                            <input type="text" class="form-control" name="partNumber" value="<?php echo isset($row['part_number']) ? htmlspecialchars($row['part_number']) : ''; ?>" required>
                        </div>
                        <div class="col-xl-4">
                            <label class="form-control-label">Category<span class="text-danger ml-2">*</span></label>
                            <select class="form-control" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Electrical" <?php echo (isset($row['category']) && $row['category'] == 'Electrical') ? 'selected' : ''; ?>>Electrical</option>
                                <option value="Mechanical" <?php echo (isset($row['category']) && $row['category'] == 'Mechanical') ? 'selected' : ''; ?>>Mechanical</option>
                                <option value="Hydraulic" <?php echo (isset($row['category']) && $row['category'] == 'Hydraulic') ? 'selected' : ''; ?>>Hydraulic</option>
                                <option value="Pneumatic" <?php echo (isset($row['category']) && $row['category'] == 'Pneumatic') ? 'selected' : ''; ?>>Pneumatic</option>
                                <option value="Electronic" <?php echo (isset($row['category']) && $row['category'] == 'Electronic') ? 'selected' : ''; ?>>Electronic</option>
                                <option value="Other" <?php echo (isset($row['category']) && $row['category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                        <div class="col-xl-6">
                            <label class="form-control-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"><?php echo isset($row['description']) ? htmlspecialchars($row['description']) : ''; ?></textarea>
                        </div>
                        <div class="col-xl-6">
                            <label class="form-control-label">Supplier</label>
                            <select class="form-control" name="supplierId">
                                <option value="">Select Supplier</option>
                                <?php foreach($suppliers as $supplier): ?>
                                    <option value="<?php echo $supplier['supplier_id']; ?>" 
                                        <?php echo (isset($row['supplier_id']) && $row['supplier_id'] == $supplier['supplier_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($supplier['supplier_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                        <div class="col-xl-3">
                            <label class="form-control-label">Quantity<span class="text-danger ml-2">*</span></label>
                            <input type="number" class="form-control" name="quantity" value="<?php echo isset($row['quantity']) ? $row['quantity'] : '0'; ?>" required min="0">
                        </div>
                        <div class="col-xl-3">
                            <label class="form-control-label">Unit Price ($)<span class="text-danger ml-2">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="unitPrice" value="<?php echo isset($row['unit_price']) ? number_format($row['unit_price'], 2, '.', '') : '0.00'; ?>" required min="0">
                        </div>
                        <div class="col-xl-3">
                            <label class="form-control-label">Total Value</label>
                            <input type="text" class="form-control" id="totalValue" value="<?php echo isset($row['quantity'], $row['unit_price']) ? number_format($row['quantity'] * $row['unit_price'], 2) : '0.00'; ?>" readonly>
                        </div>
                        <div class="col-xl-3">
                            <label class="form-control-label">Reorder Level</label>
                            <input type="number" class="form-control" name="reorderLevel" value="<?php echo isset($row['reorder_level']) ? $row['reorder_level'] : '0'; ?>" min="0">
                        </div>
                    </div>
                    <?php if (isset($row['part_id'])) { ?>
                        <input type="hidden" name="partId" value="<?php echo $partId; ?>">
                        <button type="submit" name="update" class="btn btn-warning">Update</button>
                        <a href="parts.php" class="btn btn-secondary">Cancel</a>
                    <?php } else { ?>
                        <button type="submit" name="save" class="btn btn-primary">Save</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    <?php } ?>
                  </form>
                </div>
              </div>

              <!-- Parts List -->
              <div class="row">
                <div class="col-lg-12">
                  <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-primary">All Parts</h6>
                    </div>
                    <div class="table-responsive p-3">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>Part Name</th>
                            <th>Part Number</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Value</th>
                            <th>Supplier</th>
                            <th>Status</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                      
                        <tbody>
                          <?php
                              $query = "SELECT p.*, s.supplier_name 
                                        FROM parts p 
                                        LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id 
                                        ORDER BY p.part_name ASC";
                              $rs = $conn->query($query);
                              $num = $rs->num_rows;
                              $sn=0;
                              if($num > 0) { 
                                  while ($rows = $rs->fetch_assoc()) {
                                      $sn = $sn + 1;
                                      $totalValue = $rows['quantity'] * $rows['unit_price'];
                                      $statusClass = ($rows['quantity'] <= $rows['reorder_level']) ? 'danger' : 'success';
                                      $statusText = ($rows['quantity'] <= $rows['reorder_level']) ? 'Low Stock' : 'In Stock';
                                      
                                      echo "
                                      <tr>
                                        <td>".$sn."</td>
                                        <td>".htmlspecialchars($rows['part_name'])."</td>
                                        <td>".htmlspecialchars($rows['part_number'])."</td>
                                        <td>".htmlspecialchars($rows['category'])."</td>
                                        <td>".$rows['quantity']."</td>
                                        <td>$".number_format($rows['unit_price'], 2)."</td>
                                        <td>$".number_format($totalValue, 2)."</td>
                                        <td>".($rows['supplier_name'] ? htmlspecialchars($rows['supplier_name']) : 'N/A')."</td>
                                        <td><span class='badge badge-$statusClass'>$statusText</span></td>
                                        <td>
                                          <a href='?action=edit&part_id=".$rows['part_id']."' class='btn btn-sm btn-warning'><i class='fas fa-fw fa-edit'></i></a>
                                          <a href='?action=delete&part_id=".$rows['part_id']."' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this part?\")'><i class='fas fa-fw fa-trash'></i></a>
                                        </td>
                                      </tr>";
                                  }
                              }
                              else {
                                  echo "<tr><td colspan='10' class='text-center'>No parts found</td></tr>";
                              }
                          ?>
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
          { "orderable": false, "targets": [9] } // Disable sorting for action column
        ]
      });

      // Live update Total Value when Quantity or Unit Price changes
      $('input[name="quantity"], input[name="unitPrice"]').on('input', function () {
        var qty = parseFloat($('input[name="quantity"]').val()) || 0;
        var price = parseFloat($('input[name="unitPrice"]').val()) || 0;
        var total = (qty * price).toFixed(2);
        $('#totalValue').val('$' + total);
      });

      // If editing, trigger calculation on page load
      $('input[name="quantity"], input[name="unitPrice"]').trigger('input');
    });
  </script>
</body>
</html>