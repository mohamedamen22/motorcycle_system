<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Initialize variables
$statusMsg = '';
$editRow = null;

// Handle form submissions
if(isset($_POST['save'])) {
    $customerId = $_POST['customerId'];
    $saleDate = $_POST['saleDate'];
    $totalAmount = $_POST['totalAmount'];
    $paymentMethod = $_POST['paymentMethod'];
    $status = $_POST['status'];
    
    // Validate inputs
    if(empty($customerId) || empty($totalAmount)) {
        $statusMsg = '<div class="alert alert-danger">Please fill all required fields!</div>';
    } else {
        // Insert new sale
        $query = $conn->prepare("INSERT INTO sales (customer_id, sale_date, total_amount, payment_method, status) 
                                VALUES (?, ?, ?, ?, ?)");
        $query->bind_param("issss", $customerId, $saleDate, $totalAmount, $paymentMethod, $status);
        
        if($query->execute()) {
            $statusMsg = '<div class="alert alert-success">Sale recorded successfully!</div>';
        } else {
            $statusMsg = '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
        }
    }
}

if(isset($_POST['update'])) {
    $saleId = $_POST['saleId'];
    $customerId = $_POST['customerId'];
    $saleDate = $_POST['saleDate'];
    $totalAmount = $_POST['totalAmount'];
    $paymentMethod = $_POST['paymentMethod'];
    $status = $_POST['status'];
    
    // Validate inputs
    if(empty($customerId) || empty($totalAmount)) {
        $statusMsg = '<div class="alert alert-danger">Please fill all required fields!</div>';
    } else {
        // Update sale
        $query = $conn->prepare("UPDATE sales SET 
                                customer_id = ?,
                                sale_date = ?,
                                total_amount = ?,
                                payment_method = ?,
                                status = ?
                                WHERE sale_id = ?");
        $query->bind_param("issssi", $customerId, $saleDate, $totalAmount, $paymentMethod, $status, $saleId);
        
        if($query->execute()) {
            $statusMsg = '<div class="alert alert-success">Sale updated successfully!</div>';
        } else {
            $statusMsg = '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
        }
    }
}

// Handle delete action
if(isset($_GET['action']) && $_GET['action'] == 'delete') {
    $saleId = $_GET['sale_id'];
    
    $query = $conn->prepare("DELETE FROM sales WHERE sale_id = ?");
    $query->bind_param("i", $saleId);
    
    if($query->execute()) {
        $statusMsg = '<div class="alert alert-success">Sale deleted successfully!</div>';
    } else {
        $statusMsg = '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
    }
}

// Fetch data for editing
if(isset($_GET['action']) && $_GET['action'] == 'edit') {
    $saleId = $_GET['sale_id'];
    $query = $conn->prepare("SELECT * FROM sales WHERE sale_id = ?");
    $query->bind_param("i", $saleId);
    $query->execute();
    $result = $query->get_result();
    $editRow = $result->fetch_assoc();
}

// Get all sales with customer information
$salesQuery = "SELECT s.*, c.customer_name 
               FROM sales s
               JOIN customers c ON s.customer_id = c.customer_id
               ORDER BY s.sale_date DESC";
$salesResult = $conn->query($salesQuery);
$sales = $salesResult->fetch_all(MYSQLI_ASSOC);

// Get all customers for dropdown
$customersQuery = "SELECT * FROM customers ORDER BY customer_name";
$customersResult = $conn->query($customersQuery);
$customers = $customersResult->fetch_all(MYSQLI_ASSOC);
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
    .status-completed {
        color: #28a745;
        font-weight: bold;
    }
    .status-pending {
        color: #ffc107;
        font-weight: bold;
    }
    .status-cancelled {
        color: #dc3545;
        font-weight: bold;
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
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-cash-register"></i> Sales Management</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Sales</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Sales Form -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary"><?php echo isset($editRow) ? 'Edit Sale' : 'Record New Sale'; ?></h6>
                </div>
                <div class="card-body">
                  <?php echo $statusMsg; ?>
                  <form method="post">
                    <?php if(isset($editRow)): ?>
                      <input type="hidden" name="saleId" value="<?php echo $editRow['sale_id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Customer<span class="text-danger ml-2">*</span></label>
                      <div class="col-sm-9">
                        <select class="form-control" name="customerId" required>
                          <option value="">Select Customer</option>
                          <?php foreach($customers as $customer): ?>
                            <option value="<?php echo $customer['customer_id']; ?>"
                              <?php if(isset($editRow) && $editRow['customer_id'] == $customer['customer_id']) echo 'selected'; ?>>
                              <?php echo htmlspecialchars($customer['customer_name']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Sale Date<span class="text-danger ml-2">*</span></label>
                      <div class="col-sm-9">
                        <input type="datetime-local" class="form-control" name="saleDate" 
                          value="<?php echo isset($editRow) ? date('Y-m-d\TH:i', strtotime($editRow['sale_date'])) : date('Y-m-d\TH:i'); ?>" required>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Total Amount<span class="text-danger ml-2">*</span></label>
                      <div class="col-sm-9">
                        <input type="number" step="0.01" class="form-control" name="totalAmount" 
                          value="<?php echo isset($editRow) ? $editRow['total_amount'] : ''; ?>" required>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Payment Method</label>
                      <div class="col-sm-9">
                        <select class="form-control" name="paymentMethod">
                          <option value="Cash" <?php if(isset($editRow) && $editRow['payment_method'] == 'Cash') echo 'selected'; ?>>Cash</option>
                          <option value="Credit" <?php if(isset($editRow) && $editRow['payment_method'] == 'Credit') echo 'selected'; ?>>Credit</option>
                          <option value="Mobile Money" <?php if(isset($editRow) && $editRow['payment_method'] == 'Mobile Money') echo 'selected'; ?>>Mobile Money</option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Status</label>
                      <div class="col-sm-9">
                        <select class="form-control" name="status">
                          <option value="Completed" <?php if(isset($editRow) && $editRow['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                          <option value="Pending" <?php if(isset($editRow) && $editRow['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                          <option value="Cancelled" <?php if(isset($editRow) && $editRow['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <div class="col-sm-12 text-right">
                        <?php if(isset($editRow)): ?>
                          <button type="submit" name="update" class="btn btn-primary">Update Sale</button>
                          <a href="sales.php" class="btn btn-secondary">Cancel</a>
                        <?php else: ?>
                          <button type="submit" name="save" class="btn btn-primary">Record Sale</button>
                        <?php endif; ?>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Sales List -->
              <div class="row">
                <div class="col-lg-12">
                  <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-primary">Sales Records</h6>
                    </div>
                    <div class="table-responsive p-3">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>Sale ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if(!empty($sales)): ?>
                            <?php foreach($sales as $index => $sale): ?>
                              <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo $sale['sale_id']; ?></td>
                                <td><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($sale['sale_date'])); ?></td>
                                <td><?php echo number_format($sale['total_amount'], 2); ?></td>
                                <td><?php echo $sale['payment_method']; ?></td>
                                <td>
                                  <span class="status-<?php echo strtolower($sale['status']); ?>">
                                    <?php echo $sale['status']; ?>
                                  </span>
                                </td>
                                <td>
                                  <a href="?action=edit&sale_id=<?php echo $sale['sale_id']; ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-fw fa-edit"></i>
                                  </a>
                                  <a href="?action=delete&sale_id=<?php echo $sale['sale_id']; ?>" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this sale?')">
                                    <i class="fas fa-fw fa-trash"></i>
                                  </a>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <tr><td colspan="8" class="text-center">No sales records found</td></tr>
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
        "order": [[3, "desc"]], // Default sort by date descending
        "columnDefs": [
          { "orderable": false, "targets": [7] } // Disable sorting for action column
        ]
      });
    });
  </script>
</body>
</html>