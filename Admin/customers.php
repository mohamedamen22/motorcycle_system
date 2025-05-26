<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

//------------------------SAVE CUSTOMER--------------------------------------------------
if(isset($_POST['save'])){
    $customerName = $_POST['customerName'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $email = $_POST['email'];
   
    $query = mysqli_query($conn,"SELECT * FROM customers WHERE phone ='$phone' OR email = '$email'");
    $ret = mysqli_fetch_array($query);

    if($ret > 0){ 
        $statusMsg = "<div class='alert alert-danger'>Customer with this phone or email already exists!</div>";
    }
    else{
        $query = mysqli_query($conn,"INSERT INTO customers (customer_name, phone, address, email) 
                                  VALUES ('$customerName','$phone','$address','$email')");

        if ($query) {
            $statusMsg = "<div class='alert alert-success'>Customer created successfully!</div>";
            
            // Log this action
            $adminId = $_SESSION['userId'];
            $action = "Added new customer: $customerName";
            mysqli_query($conn,"INSERT INTO audit_log (admin_id, action) VALUES ('$adminId','$action')");
        }
        else {
            $statusMsg = "<div class='alert alert-danger'>An error occurred!</div>";
        }
    }
}

//---------------------------------------EDIT CUSTOMER-------------------------------------------------------------
if (isset($_GET['customer_id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $customerId = $_GET['customer_id'];

    $query = mysqli_query($conn,"SELECT * FROM customers WHERE customer_id ='$customerId'");
    $row = mysqli_fetch_array($query);

    //------------UPDATE CUSTOMER-----------------------------
    if(isset($_POST['update'])){
        $customerName = $_POST['customerName'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $email = $_POST['email'];
    
        $query = mysqli_query($conn,"UPDATE customers SET 
                    customer_name='$customerName',
                    phone='$phone',
                    address='$address',
                    email='$email'
                    WHERE customer_id='$customerId'");

        if ($query) {
            $statusMsg = "<div class='alert alert-success'>Customer updated successfully!</div>";
            
            // Log this action
            $adminId = $_SESSION['userId'];
            $action = "Updated customer: $customerName (ID: $customerId)";
            mysqli_query($conn,"INSERT INTO audit_log (admin_id, action) VALUES ('$adminId','$action')");
            
            echo "<script type = \"text/javascript\">
            window.location = (\"all_customers.php\")
            </script>"; 
        }
        else {
            $statusMsg = "<div class='alert alert-danger'>An error occurred!</div>";
        }
    }
}

//--------------------------------DELETE CUSTOMER------------------------------------------------------------------
if (isset($_GET['customer_id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $customerId = $_GET['customer_id'];
    
    // First get customer name for logging
    $customerQuery = mysqli_query($conn,"SELECT customer_name FROM customers WHERE customer_id ='$customerId'");
    $customer = mysqli_fetch_assoc($customerQuery);
    $customerName = $customer['customer_name'];

    $query = mysqli_query($conn,"DELETE FROM customers WHERE customer_id='$customerId'");

    if ($query == TRUE) {
        // Log this action
        $adminId = $_SESSION['userId'];
        $action = "Deleted customer: $customerName (ID: $customerId)";
        mysqli_query($conn,"INSERT INTO audit_log (admin_id, action) VALUES ('$adminId','$action')");
        
        echo "<script type = \"text/javascript\">
        window.location = (\"all_customers.php\")
        </script>";  
    }
    else {
        $statusMsg = "<div class='alert alert-danger'>Cannot delete customer with existing sales records!</div>"; 
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

    .customer-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background-color: #e3f2fd;
        color: #1976d2;
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
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-users"></i> Customer Management</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Customers</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
             

              <!-- Customer List -->
              <div class="row">
                <div class="col-lg-12">
                  <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-primary">All Customers</h6>
                      <div>
                        <a href="customer_report.php" class="btn btn-sm btn-info"><i class="fas fa-file-pdf"></i> Generate Report</a>
                      </div>
                    </div>
                    <div class="table-responsive p-3">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>Customer Name</th>
                            <th>Contact Info</th>
                            <th>Address</th>
                            <th>Registered</th>
                       
                          </tr>
                        </thead>
                      
                        <tbody>
                          <?php
                              $query = "SELECT * FROM customers ORDER BY customer_name ASC";
                              $rs = $conn->query($query);
                              $num = $rs->num_rows;
                              $sn=0;
                              if($num > 0) { 
                                  while ($rows = $rs->fetch_assoc()) {
                                      $sn = $sn + 1;
                                      echo "
                                      <tr>
                                        <td>".$sn."</td>
                                        <td>
                                          <strong>".$rows['customer_name']."</strong>
                                          <div class='customer-badge mt-1'>ID: ".$rows['customer_id']."</div>
                                        </td>
                                        <td>
                                          <div><i class='fas fa-phone mr-2'></i> ".$rows['phone']."</div>
                                          ".($rows['email'] ? "<div><i class='fas fa-envelope mr-2'></i> ".$rows['email']."</div>" : "")."
                                        </td>
                                        <td>".$rows['address']."</td>
                                        <td>".date('M d, Y', strtotime($rows['created_at']))."</td>
                                        
                                      </tr>";
                                  }
                              }
                              else {
                                  echo "<tr><td colspan='6' class='text-center'>No customers found</td></tr>";
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
      $('#dataTableHover').DataTable({
        "order": [[1, "asc"]],
        "columnDefs": [
          { "orderable": false, "targets": [5] } // Disable sorting for action column
        ]
      }); // ID From dataTable with Hover
    });
  </script>
</body>
</html>