<?php

error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Initialize variables
$statusMsg = '';
$saleId = isset($_GET['sale_id']) ? $_GET['sale_id'] : '';

// Handle form submissions
if(isset($_POST['save'])) {
    $productId = $_POST['productId'];
    $quantitySold = $_POST['quantitySold'];
    $saleDate = $_POST['saleDate'];

    // Insert new sale record
    $query = "INSERT INTO sales (product_id, quantity_sold, sale_date) 
              VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $productId, $quantitySold, $saleDate);

    if($stmt->execute()) {
        $statusMsg = '<div class="alert alert-success">Sale record added successfully!</div>';
    } else {
        $statusMsg = '<div class="alert alert-danger">Error adding sale record: '.$conn->error.'</div>';
    }
}

if(isset($_POST['update'])) {
    $saleId = $_POST['saleId'];
    $productId = $_POST['productId'];
    $quantitySold = $_POST['quantitySold'];
    $saleDate = $_POST['saleDate'];

    // Update sale record
    $query = "UPDATE sales SET product_id = ?, quantity_sold = ?, sale_date = ? 
              WHERE sale_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isii", $productId, $quantitySold, $saleDate, $saleId);

    if($stmt->execute()) {
        $statusMsg = '<div class="alert alert-success">Sale record updated successfully!</div>';
    } else {
        $statusMsg = '<div class="alert alert-danger">Error updating sale record: '.$conn->error.'</div>';
    }
}

if(isset($_GET['action']) && $_GET['action'] == 'delete') {
    $saleId = $_GET['sale_id'];

    // Delete sale record
    $query = "DELETE FROM sales WHERE sale_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $saleId);

    if($stmt->execute()) {
        $statusMsg = '<div class="alert alert-success">Sale record deleted successfully!</div>';
    } else {
        $statusMsg = '<div class="alert alert-danger">Error deleting sale record: '.$conn->error.'</div>';
    }
}

// Fetch all sales records
$salesQuery = "SELECT s.*, p.product_name 
               FROM sales s
               JOIN products p ON s.product_id = p.product_id
               ORDER BY s.sale_date DESC";
$salesResult = $conn->query($salesQuery);
$salesRecords = [];
while($row = $salesResult->fetch_assoc()) {
    $salesRecords[] = $row;
}

// If editing, fetch the sale record details
if(isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['sale_id'])) {
    $editQuery = "SELECT * FROM sales WHERE sale_id = ?";
    $editStmt = $conn->prepare($editQuery);
    $editStmt->bind_param("i", $_GET['sale_id']);
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
  <title>Sales Management</title>
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
</head>

<body>
  <div class="container">
    <h1 class="mt-4">Sales Management</h1>
    <?php echo $statusMsg; ?>

    <!-- Add/Edit Sale Form -->
    <form method="post" action="">
      <?php if(isset($editRow)): ?>
        <input type="hidden" name="saleId" value="<?php echo $editRow['sale_id']; ?>">
      <?php endif; ?>
      
      <div class="form-group">
        <label for="productId">Product</label>
        <select class="form-control" name="productId" required>
          <option value="">Select Product</option>
          <?php
          // Fetch products for dropdown
          $productsQuery = "SELECT * FROM products ORDER BY product_name ASC";
          $productsResult = $conn->query($productsQuery);
          while($product = $productsResult->fetch_assoc()): ?>
            <option value="<?php echo $product['product_id']; ?>" 
              <?php if(isset($editRow) && $editRow['product_id'] == $product['product_id']) echo 'selected'; ?>>
              <?php echo htmlspecialchars($product['product_name']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="quantitySold">Quantity Sold</label>
        <input type="number" class="form-control" name="quantitySold" min="1" 
          value="<?php echo isset($editRow) ? $editRow['quantity_sold'] : ''; ?>" required>
      </div>

      <div class="form-group">
        <label for="saleDate">Sale Date</label>
        <input type="date" class="form-control" name="saleDate" 
          value="<?php echo isset($editRow) ? $editRow['sale_date'] : ''; ?>" required>
      </div>

      <button type="submit" name="<?php echo isset($editRow) ? 'update' : 'save'; ?>" class="btn btn-primary">
        <?php echo isset($editRow) ? 'Update Sale' : 'Add Sale'; ?>
      </button>
    </form>

    <!-- Sales Records Table -->
    <h2 class="mt-4">Sales Records</h2>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>Product Name</th>
          <th>Quantity Sold</th>
          <th>Sale Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($salesRecords)): ?>
          <?php foreach($salesRecords as $index => $record): ?>
            <tr>
              <td><?php echo $index + 1; ?></td>
              <td><?php echo htmlspecialchars($record['product_name']); ?></td>
              <td><?php echo $record['quantity_sold']; ?></td>
              <td><?php echo date('M d, Y', strtotime($record['sale_date'])); ?></td>
              <td>
                <a href="?action=edit&sale_id=<?php echo $record['sale_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="?action=delete&sale_id=<?php echo $record['sale_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this sale record?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" class="text-center">No sales records found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>