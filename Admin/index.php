<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Inventory Management System Dashboard">
  <meta name="author" content="">
  <link href="img/logo/attnlg.jpg" rel="icon">
  <?php include 'includes/title.php';?>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <link href="../vendor/chart.js/Chart.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #2c3e50;
      --secondary-color: #3498db;
      --success-color: #27ae60;
      --warning-color: #f39c12;
      --danger-color: #e74c3c;
      --light-bg: #f8f9fa;
      --dark-bg: #343a40;
    }
    
    body {
      font-family: 'Segoe UI', system-ui, sans-serif;
      background-color: #f5f7fb;
    }
    
    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      margin-bottom: 20px;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      border-radius: 12px 12px 0 0 !important;
      padding: 1rem 1.5rem;
      font-weight: 600;
    }
    
    .stat-card {
      border-left: 4px solid;
      padding: 15px;
    }
    
    .stat-card.primary {
      border-left-color: var(--secondary-color);
    }
    
    .stat-card.success {
      border-left-color: var(--success-color);
    }
    
    .stat-card.warning {
      border-left-color: var(--warning-color);
    }
    
    .stat-card.danger {
      border-left-color: var(--danger-color);
    }
    
    .stat-card .stat-title {
      font-size: 14px;
      color: #6c757d;
      margin-bottom: 5px;
    }
    
    .stat-card .stat-value {
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 5px;
    }
    
    .stat-card .stat-change {
      font-size: 12px;
    }
    
    .stat-card .stat-change.positive {
      color: var(--success-color);
    }
    
    .stat-card .stat-change.negative {
      color: var(--danger-color);
    }
    
    .table-responsive {
      border-radius: 12px;
    }
    
    .table thead th {
      background-color: var(--primary-color);
      color: white;
      border: none;
    }
    
    .table tbody tr {
      transition: all 0.3s ease;
    }
    
    .table tbody tr:hover {
      background-color: rgba(52, 152, 219, 0.1);
    }
    
    .badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-weight: 500;
    }
    
    .bg-low-stock {
      background-color: var(--warning-color);
    }
    
    .bg-out-of-stock {
      background-color: var(--danger-color);
    }
    
    .bg-in-stock {
      background-color: var(--success-color);
    }
    
    .quick-actions .btn {
      margin: 5px;
      border-radius: 8px;
      padding: 12px 20px;
      font-weight: 500;
    }
    
    @media (max-width: 768px) {
      .stat-card {
        margin-bottom: 15px;
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
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </div>

          <!-- Quick Actions -->
          <div class="row mb-4 quick-actions">
            <div class="col-xl-12">
              <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body text-center">
                  <a href="parts.php?action=add" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Add New Part</a>
                  <a href="purchases.php?action=add" class="btn btn-success"><i class="fas fa-shopping-cart"></i> New Purchase</a>
                  <a href="sales.php?action=add" class="btn btn-info"><i class="fas fa-cash-register"></i> New Sale</a>
                  <a href="customers.php?action=add" class="btn btn-warning"><i class="fas fa-user-plus"></i> Add Customer</a>
                  <a href="suppliers.php?action=add" class="btn btn-secondary"><i class="fas fa-truck"></i> Add Supplier</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Statistics Cards -->
          <div class="row mb-4">
            <?php
            // Get counts for all statistics
            $partsCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM parts"))['count'];
            $lowStockCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM parts WHERE quantity <= reorder_level"))['count'];
            $customersCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM customers"))['count'];
            $suppliersCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM suppliers"))['count'];
            $purchasesCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM purchases WHERE status = 'Received'"))['count'];
            $pendingPurchases = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM purchases WHERE status = 'Pending'"))['count'];
            $salesCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM sales WHERE status = 'Completed'"))['count'];
            $pendingSales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM sales WHERE status = 'Pending'"))['count'];
            
            // Get total inventory value
            $inventoryValue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity * unit_price) as value FROM parts"))['value'];
            $inventoryValue = number_format($inventoryValue, 2);
            
            // Get recent sales total
            $recentSales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM sales WHERE sale_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)"))['total'];
            $recentSales = number_format($recentSales, 2);
            ?>
            
            <!-- Parts Card -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card stat-card primary">
                <div class="stat-title">Total Parts</div>
                <div class="stat-value"><?php echo $partsCount; ?></div>
                <div class="stat-change">
                  <span class="text-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $lowStockCount; ?> low stock</span>
                </div>
              </div>
            </div>
            
            <!-- Inventory Value Card -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card stat-card success">
                <div class="stat-title">Inventory Value</div>
                <div class="stat-value">$<?php echo $inventoryValue; ?></div>
                <div class="stat-change">
                  <span class="text-success"><i class="fas fa-chart-line"></i> Total value</span>
                </div>
              </div>
            </div>
            
            <!-- Customers Card -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card stat-card warning">
                <div class="stat-title">Customers</div>
                <div class="stat-value"><?php echo $customersCount; ?></div>
                <div class="stat-change">
                  <span class="text-success"><i class="fas fa-users"></i> Active</span>
                </div>
              </div>
            </div>
            
            <!-- Suppliers Card -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card stat-card danger">
                <div class="stat-title">Suppliers</div>
                <div class="stat-value"><?php echo $suppliersCount; ?></div>
                <div class="stat-change">
                  <span class="text-success"><i class="fas fa-truck"></i> Active</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Charts and Recent Activity Row -->
          <div class="row">
            <!-- Sales/Purchases Chart -->
            <div class="col-xl-8 col-lg-7">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Sales & Purchases Overview</h6>
                </div>
                <div class="card-body">
                  <div class="chart-area">
                    <canvas id="salesPurchasesChart"></canvas>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Inventory Status -->
            <div class="col-xl-4 col-lg-5">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Inventory Status</h6>
                </div>
                <div class="card-body">
                  <div class="chart-pie pt-4 pb-2">
                    <canvas id="inventoryStatusChart"></canvas>
                  </div>
                  <div class="mt-4 text-center small">
                    <span class="mr-2">
                      <i class="fas fa-circle text-success"></i> In Stock
                    </span>
                    <span class="mr-2">
                      <i class="fas fa-circle text-warning"></i> Low Stock
                    </span>
                    <span class="mr-2">
                      <i class="fas fa-circle text-danger"></i> Out of Stock
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Transactions Row -->
          <div class="row">
            <!-- Recent Purchases -->
            <div class="col-xl-6 col-lg-6">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Recent Purchases</h6>
                  <a href="purchases.php" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="table-responsive">
                  <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                      <tr>
                        <th>ID</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "SELECT p.purchase_id, s.supplier_name, p.purchase_date, p.total_amount, p.status 
                                FROM purchases p 
                                JOIN suppliers s ON p.supplier_id = s.supplier_id 
                                ORDER BY p.purchase_date DESC LIMIT 5";
                      $result = mysqli_query($conn, $query);
                      
                      if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                          $statusClass = '';
                          if($row['status'] == 'Received') $statusClass = 'success';
                          elseif($row['status'] == 'Pending') $statusClass = 'warning';
                          else $statusClass = 'danger';
                          
                          echo "<tr>
                            <td>#{$row['purchase_id']}</td>
                            <td>{$row['supplier_name']}</td>
                            <td>" . date('M d, Y', strtotime($row['purchase_date'])) . "</td>
                            <td>$" . number_format($row['total_amount'], 2) . "</td>
                            <td><span class='badge badge-$statusClass'>{$row['status']}</span></td>
                          </tr>";
                        }
                      } else {
                        echo "<tr><td colspan='5' class='text-center'>No recent purchases</td></tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            
            <!-- Recent Sales -->
            <div class="col-xl-6 col-lg-6">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Recent Sales</h6>
                  <a href="sales.php" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="table-responsive">
                  <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                      <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "SELECT s.sale_id, c.customer_name, s.sale_date, s.total_amount, s.status 
                                FROM sales s 
                                JOIN customers c ON s.customer_id = c.customer_id 
                                ORDER BY s.sale_date DESC LIMIT 5";
                      $result = mysqli_query($conn, $query);
                      
                      if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                          $statusClass = '';
                          if($row['status'] == 'Completed') $statusClass = 'success';
                          elseif($row['status'] == 'Pending') $statusClass = 'warning';
                          else $statusClass = 'danger';
                          
                          echo "<tr>
                            <td>#{$row['sale_id']}</td>
                            <td>{$row['customer_name']}</td>
                            <td>" . date('M d, Y', strtotime($row['sale_date'])) . "</td>
                            <td>$" . number_format($row['total_amount'], 2) . "</td>
                            <td><span class='badge badge-$statusClass'>{$row['status']}</span></td>
                          </tr>";
                        }
                      } else {
                        echo "<tr><td colspan='5' class='text-center'>No recent sales</td></tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Low Stock Items -->
          <div class="row">
            <div class="col-xl-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Low Stock Items</h6>
                  <a href="parts.php" class="btn btn-sm btn-primary">View All Parts</a>
                </div>
                <div class="table-responsive">
                  <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                      <tr>
                        <th>Part Name</th>
                        <th>Part Number</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Reorder Level</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "SELECT * FROM parts WHERE quantity <= reorder_level ORDER BY quantity ASC LIMIT 5";
                      $result = mysqli_query($conn, $query);
                      
                      if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                          $status = '';
                          $badgeClass = '';
                          if($row['quantity'] == 0) {
                            $status = 'Out of Stock';
                            $badgeClass = 'bg-out-of-stock';
                          } elseif($row['quantity'] <= $row['reorder_level']) {
                            $status = 'Low Stock';
                            $badgeClass = 'bg-low-stock';
                          } else {
                            $status = 'In Stock';
                            $badgeClass = 'bg-in-stock';
                          }
                          
                          echo "<tr>
                            <td>{$row['part_name']}</td>
                            <td>{$row['part_number']}</td>
                            <td>{$row['category']}</td>
                            <td>{$row['quantity']}</td>
                            <td>{$row['reorder_level']}</td>
                            <td><span class='badge $badgeClass'>$status</span></td>
                            <td>
                              <a href='purchases.php?action=add' class='btn btn-sm btn-warning'>
                                <i class='fas fa-shopping-cart'></i> Reorder
                              </a>
                            </td>
                          </tr>";
                        }
                      } else {
                        echo "<tr><td colspan='7' class='text-center'>No low stock items</td></tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
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
  <script src="../vendor/chart.js/Chart.min.js"></script>
  
  <script>
    // Sales & Purchases Chart
    var ctx = document.getElementById("salesPurchasesChart");
    var salesPurchasesChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: [
          <?php
          // Get last 7 days for chart labels
          for($i = 6; $i >= 0; $i--) {
            $date = date('M j', strtotime("-$i days"));
            echo "'$date',";
          }
          ?>
        ],
        datasets: [
          {
            label: "Sales",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: [
              <?php
              // Get sales data for last 7 days
              for($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $query = "SELECT SUM(total_amount) as total FROM sales WHERE DATE(sale_date) = '$date'";
                $result = mysqli_query($conn, $query);
                $row = mysqli_fetch_assoc($result);
                echo $row['total'] ? $row['total'] : '0', ',';
              }
              ?>
            ]
          },
          {
            label: "Purchases",
            lineTension: 0.3,
            backgroundColor: "rgba(28, 200, 138, 0.05)",
            borderColor: "rgba(28, 200, 138, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(28, 200, 138, 1)",
            pointBorderColor: "rgba(28, 200, 138, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(28, 200, 138, 1)",
            pointHoverBorderColor: "rgba(28, 200, 138, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: [
              <?php
              // Get purchase data for last 7 days
              for($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $query = "SELECT SUM(total_amount) as total FROM purchases WHERE DATE(purchase_date) = '$date'";
                $result = mysqli_query($conn, $query);
                $row = mysqli_fetch_assoc($result);
                echo $row['total'] ? $row['total'] : '0', ',';
              }
              ?>
            ]
          }
        ]
      },
      options: {
        maintainAspectRatio: false,
        layout: {
          padding: {
            left: 10,
            right: 25,
            top: 25,
            bottom: 0
          }
        },
        scales: {
          xAxes: [{
            gridLines: {
              display: false,
              drawBorder: false
            },
            ticks: {
              maxTicksLimit: 7
            }
          }],
          yAxes: [{
            ticks: {
              beginAtZero: true,
              callback: function(value) {
                return '$' + value.toLocaleString();
              },
              maxTicksLimit: 5,
              padding: 10
            },
            gridLines: {
              color: "rgb(234, 236, 244)",
              zeroLineColor: "rgb(234, 236, 244)",
              drawBorder: false,
              borderDash: [2],
              zeroLineBorderDash: [2]
            }
          }]
        },
        legend: {
          display: true,
          position: 'top'
        },
        tooltips: {
          backgroundColor: "rgb(255,255,255)",
          bodyFontColor: "#858796",
          titleMarginBottom: 10,
          titleFontColor: '#6e707e',
          titleFontSize: 14,
          borderColor: '#dddfeb',
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: false,
          intersect: false,
          mode: 'index',
          caretPadding: 10,
          callbacks: {
            label: function(tooltipItem, chart) {
              var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
              return datasetLabel + ': $' + tooltipItem.yLabel.toLocaleString();
            }
          }
        }
      }
    });

    // Inventory Status Chart
    var ctx2 = document.getElementById("inventoryStatusChart");
    var inventoryStatusChart = new Chart(ctx2, {
      type: 'doughnut',
      data: {
        labels: ["In Stock", "Low Stock", "Out of Stock"],
        datasets: [{
          data: [
            <?php
            $inStock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM parts WHERE quantity > reorder_level"))['count'];
            $lowStock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM parts WHERE quantity <= reorder_level AND quantity > 0"))['count'];
            $outOfStock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM parts WHERE quantity = 0"))['count'];
            echo "$inStock, $lowStock, $outOfStock";
            ?>
          ],
          backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
          hoverBackgroundColor: ['#17a673', '#dda20a', '#be2617'],
          hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
      },
      options: {
        maintainAspectRatio: false,
        tooltips: {
          backgroundColor: "rgb(255,255,255)",
          bodyFontColor: "#858796",
          borderColor: '#dddfeb',
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: false,
          caretPadding: 10,
        },
        legend: {
          display: false
        },
        cutoutPercentage: 80,
      },
    });
  </script>
</body>
</html>