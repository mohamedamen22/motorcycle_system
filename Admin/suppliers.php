<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

//------------------------SAVE SUPPLIER--------------------------------------------------
if(isset($_POST['save'])){
    
    $supplierName = $_POST['supplierName'];
    $contactPerson = $_POST['contactPerson'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $email = $_POST['email'];
   
    $query = mysqli_query($conn,"SELECT * FROM suppliers WHERE supplier_name ='$supplierName' OR email = '$email'");
    $ret = mysqli_fetch_array($query);

    if($ret > 0){ 
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Supplier Already Exists!</div>";
    }
    else{
        $query = mysqli_query($conn,"INSERT INTO suppliers (supplier_name, contact_person, phone, address, email) VALUES ('$supplierName','$contactPerson','$phone','$address','$email')");

        if ($query) {
            $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Supplier Created Successfully!</div>";
        }
        else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
        }
    }
}

//---------------------------------------EDIT SUPPLIER-------------------------------------------------------------
if (isset($_GET['supplier_id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $supplierId = $_GET['supplier_id'];

    $query = mysqli_query($conn,"SELECT * FROM suppliers WHERE supplier_id ='$supplierId'");
    $row = mysqli_fetch_array($query);

    //------------UPDATE SUPPLIER-----------------------------
    if(isset($_POST['update'])){
        $supplierName = $_POST['supplierName'];
        $contactPerson = $_POST['contactPerson'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $email = $_POST['email'];
    
        $query = mysqli_query($conn,"UPDATE suppliers SET supplier_name='$supplierName', contact_person='$contactPerson', phone='$phone', address='$address', email='$email' WHERE supplier_id='$supplierId'");

        if ($query) {
            echo "<script type = \"text/javascript\">
            window.location = (\"suppliers.php\")
            </script>"; 
        }
        else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
        }
    }
}

//--------------------------------DELETE SUPPLIER------------------------------------------------------------------
if (isset($_GET['supplier_id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $supplierId = $_GET['supplier_id'];

    $query = mysqli_query($conn,"DELETE FROM suppliers WHERE supplier_id='$supplierId'");

    if ($query == TRUE) {
        echo "<script type = \"text/javascript\">
        window.location = (\"suppliers.php\")
        </script>";  
    }
    else {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>"; 
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
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-truck"></i> Supplier Operations</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Suppliers</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary"><?php echo isset($supplierId) ? 'Edit Supplier' : 'Add New Supplier'; ?></h6>
                    <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                        <div class="col-xl-4">
                            <label class="form-control-label">Supplier Name<span class="text-danger ml-2">*</span></label>
                            <input type="text" class="form-control" name="supplierName" value="<?php echo isset($row) ? $row['supplier_name'] : ''; ?>" required>
                        </div>
                        <div class="col-xl-4">
                            <label class="form-control-label">Contact Person</label>
                            <input type="text" class="form-control" name="contactPerson" value="<?php echo isset($row) ? $row['contact_person'] : ''; ?>">
                        </div>
                        <div class="col-xl-4">
                            <label class="form-control-label">Phone Number<span class="text-danger ml-2">*</span></label>
                            <input type="text" class="form-control" name="phone" value="<?php echo isset($row) ? $row['phone'] : ''; ?>" required>
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                        <div class="col-xl-6">
                            <label class="form-control-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo isset($row) ? $row['email'] : ''; ?>">
                        </div>
                        <div class="col-xl-6">
                            <label class="form-control-label">Address</label>
                            <textarea class="form-control" name="address"><?php echo isset($row) ? $row['address'] : ''; ?></textarea>
                        </div>
                    </div>
                    <?php if (isset($supplierId)) { ?>
                        <button type="submit" name="update" class="btn btn-warning">Update</button>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php } else { ?>
                        <button type="submit" name="save" class="btn btn-primary">Save</button>
                    <?php } ?>
                  </form>
                </div>
              </div>

              <!-- Supplier List -->
              <div class="row">
                <div class="col-lg-12">
                  <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-primary">All Suppliers</h6>
                    </div>
                    <div class="table-responsive p-3">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>Supplier Name</th>
                            <th>Contact Person</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Edit</th>
                            <th>Delete</th>
                          </tr>
                        </thead>
                      
                        <tbody>
                          <?php
                              $query = "SELECT * FROM suppliers ORDER BY supplier_name ASC";
                              $rs = $conn->query($query);
                              $num = $rs->num_rows;
                              $sn=0;
                              if($num > 0) { 
                                  while ($rows = $rs->fetch_assoc()) {
                                      $sn = $sn + 1;
                                      echo "
                                      <tr>
                                        <td>".$sn."</td>
                                        <td>".$rows['supplier_name']."</td>
                                        <td>".$rows['contact_person']."</td>
                                        <td>".$rows['phone']."</td>
                                        <td>".$rows['email']."</td>
                                        <td>".$rows['address']."</td>
                                        <td><a href='?action=edit&supplier_id=".$rows['supplier_id']."' class='btn btn-sm btn-warning'><i class='fas fa-fw fa-edit'></i> Edit</a></td>
                                        <td><a href='?action=delete&supplier_id=".$rows['supplier_id']."' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this supplier?\")'><i class='fas fa-fw fa-trash'></i> Delete</a></td>
                                      </tr>";
                                  }
                              }
                              else {
                                  echo "<tr><td colspan='8' class='text-center'>No suppliers found</td></tr>";
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
    });
  </script>
</body>
</html>