<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

$statusMsg = '';
$purchaseId = isset($_GET['purchase_id']) ? $_GET['purchase_id'] : 0;

// Handle form submission
if(isset($_POST['save']) || isset($_POST['update'])) {
    $supplierId = $_POST['supplierId'];
    $purchaseDate = $_POST['purchaseDate'];
    $status = $_POST['status'];
    $partIds = $_POST['partId'];
    $quantities = $_POST['quantity'];
    $unitPrices = $_POST['unitPrice'];
    
    // Calculate total amount
    $totalAmount = 0;
    for($i = 0; $i < count($partIds); $i++) {
        if($partIds[$i] != '' && $quantities[$i] > 0) {
            $totalAmount += $quantities[$i] * $unitPrices[$i];
        }
    }
    
    if(isset($_POST['save'])) {
        // Add new purchase
        $query = "INSERT INTO purchases (supplier_id, purchase_date, total_amount, status) 
                  VALUES ($supplierId, '$purchaseDate', $totalAmount, '$status')";
        if($conn->query($query)) {
            $purchaseId = $conn->insert_id;
            
            // Add purchase items
            $success = true;
            for($i = 0; $i < count($partIds); $i++) {
                if($partIds[$i] != '' && $quantities[$i] > 0) {
                    $query = "INSERT INTO purchase_items (purchase_id, part_id, quantity, unit_price) 
                              VALUES ($purchaseId, ".$partIds[$i].", ".$quantities[$i].", ".$unitPrices[$i].")";
                    if(!$conn->query($query)) {
                        $success = false;
                        break;
                    }
                    
                    // Update inventory
                    $query = "UPDATE parts SET quantity = quantity + ".$quantities[$i]." WHERE part_id = ".$partIds[$i];
                    $conn->query($query);
                }
            }
            
            if($success) {
                $statusMsg = '<div class="alert alert-success">Purchase added successfully!</div>';
            } else {
                $statusMsg = '<div class="alert alert-danger">Error adding purchase items: '.$conn->error.'</div>';
            }
        } else {
            $statusMsg = '<div class="alert alert-danger">Error adding purchase: '.$conn->error.'</div>';
        }
    } elseif(isset($_POST['update'])) {
        // Update existing purchase
        $query = "UPDATE purchases SET 
                  supplier_id = $supplierId,
                  purchase_date = '$purchaseDate',
                  total_amount = $totalAmount,
                  status = '$status'
                  WHERE purchase_id = $purchaseId";
        if($conn->query($query)) {
            // Delete existing purchase items
            $conn->query("DELETE FROM purchase_items WHERE purchase_id = $purchaseId");
            
            // Add updated purchase items
            $success = true;
            for($i = 0; $i < count($partIds); $i++) {
                if($partIds[$i] != '' && $quantities[$i] > 0) {
                    $query = "INSERT INTO purchase_items (purchase_id, part_id, quantity, unit_price) 
                              VALUES ($purchaseId, ".$partIds[$i].", ".$quantities[$i].", ".$unitPrices[$i].")";
                    if(!$conn->query($query)) {
                        $success = false;
                        break;
                    }
                    
                    // Update inventory (more complex for updates, would need to track changes)
                }
            }
            
            if($success) {
                $statusMsg = '<div class="alert alert-success">Purchase updated successfully!</div>';
            } else {
                $statusMsg = '<div class="alert alert-danger">Error updating purchase items: '.$conn->error.'</div>';
            }
        } else {
            $statusMsg = '<div class="alert alert-danger">Error updating purchase: '.$conn->error.'</div>';
        }
    }
}

// Handle delete action
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['purchase_id'])) {
    $deleteId = $_GET['purchase_id'];
    $query = "DELETE FROM purchases WHERE purchase_id = $deleteId";
    if($conn->query($query)) {
        $statusMsg = '<div class="alert alert-success">Purchase deleted successfully!</div>';
    } else {
        $statusMsg = '<div class="alert alert-danger">Error deleting purchase: '.$conn->error.'</div>';
    }
}

// Fetch purchase data for editing
if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['purchase_id'])) {
    $purchaseId = $_GET['purchase_id'];
    $query = "SELECT * FROM purchases WHERE purchase_id = $purchaseId";
    $result = $conn->query($query);
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Fetch purchase items
        $items = [];
        $itemQuery = "SELECT * FROM purchase_items WHERE purchase_id = $purchaseId";
        $itemResult = $conn->query($itemQuery);
        if($itemResult->num_rows > 0) {
            while($item = $itemResult->fetch_assoc()) {
                $items[] = $item;
            }
        }
    }
}

// Fetch suppliers and parts for dropdowns
$suppliers = [];
$supplierQuery = "SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name";
$supplierResult = $conn->query($supplierQuery);
if($supplierResult->num_rows > 0) {
    while($supplier = $supplierResult->fetch_assoc()) {
        $suppliers[] = $supplier;
    }
}

$parts = [];
$partQuery = "SELECT part_id, part_name, part_number, unit_price FROM parts ORDER BY part_name";
$partResult = $conn->query($partQuery);
if($partResult->num_rows > 0) {
    while($part = $partResult->fetch_assoc()) {
        $parts[] = $part;
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

    .item-row {
        margin-bottom: 15px;
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
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-shopping-cart"></i> Purchase Management</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Purchases</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary"><?php echo isset($row) ? 'Edit Purchase' : 'Add New Purchase'; ?></h6>
                  <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                        <div class="col-xl-4">
                            <label class="form-control-label">Supplier<span class="text-danger ml-2">*</span></label>
                            <select class="form-control" name="supplierId" required>
                                <option value="">Select Supplier</option>
                                <?php foreach($suppliers as $supplier): ?>
                                    <option value="<?php echo $supplier['supplier_id']; ?>" 
                                        <?php echo (isset($row) && $row['supplier_id'] == $supplier['supplier_id']) ? 'selected' : ''; ?>>
                                        <?php echo $supplier['supplier_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-xl-4">
                            <label class="form-control-label">Purchase Date<span class="text-danger ml-2">*</span></label>
                            <input type="datetime-local" class="form-control" name="purchaseDate" 
                                   value="<?php echo isset($row) ? date('Y-m-d\TH:i', strtotime($row['purchase_date'])) : date('Y-m-d\TH:i'); ?>" required>
                        </div>
                        <div class="col-xl-4">
                            <label class="form-control-label">Status<span class="text-danger ml-2">*</span></label>
                            <select class="form-control" name="status" required>
                                <option value="Received" <?php echo (isset($row) && $row['status'] == 'Received') ? 'selected' : ''; ?>>Received</option>
                                <option value="Pending" <?php echo (isset($row) && $row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="Cancelled" <?php echo (isset($row) && $row['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <h5 class="mb-3">Purchase Items</h5>
                    <div id="items-container">
                        <?php if(isset($items) && count($items) > 0): ?>
                            <?php foreach($items as $index => $item): ?>
                                <div class="item-row">
                                    <div class="form-group row">
                                        <div class="col-xl-5">
                                            <label class="form-control-label">Part</label>
                                            <select class="form-control part-select" name="partId[]" required>
                                                <option value="">Select Part</option>
                                                <?php foreach($parts as $part): ?>
                                                    <option value="<?php echo $part['part_id']; ?>" 
                                                        data-price="<?php echo $part['unit_price']; ?>"
                                                        <?php echo ($item['part_id'] == $part['part_id']) ? 'selected' : ''; ?>>
                                                        <?php echo $part['part_name']; ?> (<?php echo $part['part_number']; ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-xl-2">
                                            <label class="form-control-label">Quantity</label>
                                            <input type="number" class="form-control quantity" name="quantity[]" 
                                                   value="<?php echo $item['quantity']; ?>" min="1" required>
                                        </div>
                                        <div class="col-xl-3">
                                            <label class="form-control-label">Unit Price</label>
                                            <input type="number" step="0.01" class="form-control unit-price" name="unitPrice[]" 
                                                   value="<?php echo $item['unit_price']; ?>" min="0" required>
                                        </div>
                                        <div class="col-xl-2">
                                            <label class="form-control-label">Total</label>
                                            <input type="text" class="form-control item-total" 
                                                   value="<?php echo number_format($item['quantity'] * $item['unit_price'], 2); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="item-row">
                                <div class="form-group row">
                                    <div class="col-xl-5">
                                        <label class="form-control-label">Part</label>
                                        <select class="form-control part-select" name="partId[]" required>
                                            <option value="">Select Part</option>
                                            <?php foreach($parts as $part): ?>
                                                <option value="<?php echo $part['part_id']; ?>" 
                                                    data-price="<?php echo $part['unit_price']; ?>">
                                                    <?php echo $part['part_name']; ?> (<?php echo $part['part_number']; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-xl-2">
                                        <label class="form-control-label">Quantity</label>
                                        <input type="number" class="form-control quantity" name="quantity[]" value="1" min="1" required>
                                    </div>
                                    <div class="col-xl-3">
                                        <label class="form-control-label">Unit Price</label>
                                        <input type="number" step="0.01" class="form-control unit-price" name="unitPrice[]" 
                                               value="<?php echo $parts[0]['unit_price'] ?? 0; ?>" min="0" required>
                                    </div>
                                    <div class="col-xl-2">
                                        <label class="form-control-label">Total</label>
                                        <input type="text" class="form-control item-total" value="0.00" readonly>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                   
                    
                    <div class="form-group row mb-3">
                        <div class="col-xl-4 offset-xl-8">
                            <label class="form-control-label">Grand Total</label>
                            <input type="text" class="form-control" id="grand-total" 
                                   value="<?php echo isset($row) ? number_format($row['total_amount'], 2) : '0.00'; ?>" readonly>
                        </div>
                    </div>
                    
                    <?php if (isset($row)) { ?>
                        <input type="hidden" name="purchaseId" value="<?php echo $purchaseId; ?>">
                        <button type="submit" name="update" class="btn btn-warning">Update</button>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php } else { ?>
                        <button type="submit" name="save" class="btn btn-primary">Save</button>
                    <?php } ?>
                  </form>
                </div>
              </div>

              <!-- Purchase List -->
              <div class="row">
                <div class="col-lg-12">
                  <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-primary">All Purchases</h6>
                    </div>
                    <div class="table-responsive p-3">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>Purchase ID</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th>Edit</th>
                            <th>Delete</th>
                          </tr>
                        </thead>
                      
                        <tbody>
                          <?php
                              $query = "SELECT p.*, s.supplier_name 
                                        FROM purchases p 
                                        JOIN suppliers s ON p.supplier_id = s.supplier_id 
                                        ORDER BY p.purchase_date DESC";
                              $rs = $conn->query($query);
                              $num = $rs->num_rows;
                              $sn=0;
                              if($num > 0) { 
                                  while ($rows = $rs->fetch_assoc()) {
                                      $sn = $sn + 1;
                                      
                                      // Count items for this purchase
                                      $itemCount = 0;
                                      $countQuery = "SELECT COUNT(*) as count FROM purchase_items WHERE purchase_id = ".$rows['purchase_id'];
                                      $countResult = $conn->query($countQuery);
                                      if($countResult->num_rows > 0) {
                                          $countRow = $countResult->fetch_assoc();
                                          $itemCount = $countRow['count'];
                                      }
                                      
                                      echo "
                                      <tr>
                                        <td>".$sn."</td>
                                        <td>#".$rows['purchase_id']."</td>
                                        <td>".$rows['supplier_name']."</td>
                                        <td>".date('M d, Y h:i A', strtotime($rows['purchase_date']))."</td>
                                        <td>$".number_format($rows['total_amount'], 2)."</td>
                                        <td><span class='badge badge-".($rows['status'] == 'Received' ? 'success' : ($rows['status'] == 'Pending' ? 'warning' : 'danger'))."'>".$rows['status']."</span></td>
                                        <td>".$itemCount." items</td>
                                        <td><a href='?action=edit&purchase_id=".$rows['purchase_id']."' class='btn btn-sm btn-warning'><i class='fas fa-fw fa-edit'></i> Edit</a></td>
                                        <td><a href='?action=delete&purchase_id=".$rows['purchase_id']."' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this purchase?\")'><i class='fas fa-fw fa-trash'></i> Delete</a></td>
                                      </tr>";
                                  }
                              }
                              else {
                                  echo "<tr><td colspan='9' class='text-center'>No purchases found</td></tr>";
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
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
      
      // Add new item row
      $('#add-item').click(function() {
          var newRow = `
          <div class="item-row">
              <div class="form-group row">
                  <div class="col-xl-5">
                      <select class="form-control part-select" name="partId[]" required>
                          <option value="">Select Part</option>
                          <?php foreach($parts as $part): ?>
                              <option value="<?php echo $part['part_id']; ?>" 
                                  data-price="<?php echo $part['unit_price']; ?>">
                                  <?php echo $part['part_name']; ?> (<?php echo $part['part_number']; ?>)
                              </option>
                          <?php endforeach; ?>
                      </select>
                  </div>
                  <div class="col-xl-2">
                      <input type="number" class="form-control quantity" name="quantity[]" value="1" min="1" required>
                  </div>
                  <div class="col-xl-3">
                      <input type="number" step="0.01" class="form-control unit-price" name="unitPrice[]" 
                             value="<?php echo $parts[0]['unit_price'] ?? 0; ?>" min="0" required>
                  </div>
                  <div class="col-xl-1">
                      <input type="text" class="form-control item-total" value="0.00" readonly>
                  </div>
                  <div class="col-xl-1">
                      <button type="button" class="btn btn-danger remove-item"><i class="fas fa-trash"></i></button>
                  </div>
              </div>
          </div>`;
          $('#items-container').append(newRow);
      });
      
      // Remove item row
      $(document).on('click', '.remove-item', function() {
          $(this).closest('.item-row').remove();
          calculateGrandTotal();
      });
      
      // Part selection change
      $(document).on('change', '.part-select', function() {
          var selectedOption = $(this).find('option:selected');
          var unitPrice = selectedOption.data('price');
          if(unitPrice) {
              $(this).closest('.form-group.row').find('.unit-price').val(unitPrice);
              calculateItemTotal($(this).closest('.form-group.row'));
          }
      });
      
      // Quantity or unit price change
      $(document).on('input', '.quantity, .unit-price', function() {
          calculateItemTotal($(this).closest('.form-group.row'));
      });
      
      // Calculate item total
      function calculateItemTotal(row) {
          var quantity = parseFloat(row.find('.quantity').val()) || 0;
          var unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
          var total = quantity * unitPrice;
          row.find('.item-total').val(total.toFixed(2));
          calculateGrandTotal();
      }
      
      // Calculate grand total
      function calculateGrandTotal() {
          var grandTotal = 0;
          $('.item-row').each(function() {
              var total = parseFloat($(this).find('.item-total').val()) || 0;
              grandTotal += total;
          });
          $('#grand-total').val(grandTotal.toFixed(2));
      }
      
      // Initialize calculations
      $('.item-row').each(function() {
          calculateItemTotal($(this).find('.form-group.row'));
      });
    });

    // ...existing code...
// Quantity * Unit Price = Total (auto calculation)
$(document).on('input', '.quantity, .unit-price', function() {
    var row = $(this).closest('.form-group.row');
    var quantity = parseFloat(row.find('.quantity').val()) || 0;
    var unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
    var total = quantity * unitPrice;
    row.find('.item-total').val(total.toFixed(2));
    calculateGrandTotal();
});

function calculateGrandTotal() {
    var grandTotal = 0;
    $('.item-row').each(function() {
        var total = parseFloat($(this).find('.item-total').val()) || 0;
        grandTotal += total;
    });
    $('#grand-total').val(grandTotal.toFixed(2));
}

// Initialize totals on page load
$('.item-row').each(function() {
    var row = $(this).find('.form-group.row');
    var quantity = parseFloat(row.find('.quantity').val()) || 0;
    var unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
    var total = quantity * unitPrice;
    row.find('.item-total').val(total.toFixed(2));
});
calculateGrandTotal();
// ...existing code...
  </script>
</body>
</html>