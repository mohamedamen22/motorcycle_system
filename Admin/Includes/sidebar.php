<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
     <a class="sidebar-brand d-flex align-items-center bg-gradient-primary justify-content-center" href="index.php">
         <div class="sidebar-brand-icon">
             <img src="img/logo/images.jpg">
         </div>
         <div class="sidebar-brand-text mx-3">Motorcycle Parts System</div>
     </a>
     <hr class="sidebar-divider my-0">
     <li class="nav-item active">
         <a class="nav-link" href="index.php">
             <i class="fas fa-fw fa-tachometer-alt"></i>
             <span>Dashboard</span></a>
     </li>
     <hr class="sidebar-divider">
     <div class="sidebar-heading">
         Inventory Management
     </div>
     <li class="nav-item">
         <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInventory" aria-expanded="true" aria-controls="collapseInventory">
             <i class="fas fa-boxes"></i>
             <span>Parts Inventory</span>
         </a>
         <div id="collapseInventory" class="collapse" aria-labelledby="headingInventory" data-parent="#accordionSidebar">
             <div class="bg-white py-2 collapse-inner rounded">
                 <h6 class="collapse-header">Parts Operations</h6>
                 <a class="collapse-item" href="parts.php">All Parts</a>
                 <a class="collapse-item" href="add_part.php">Add New Part</a>
                 <a class="collapse-item" href="inventory_report.php">Inventory Report</a>
                 <a class="collapse-item" href="low_stock.php">Low Stock Alert</a>
             </div>
         </div>
     </li>
     <li class="nav-item">
         <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSuppliers" aria-expanded="true" aria-controls="collapseSuppliers">
             <i class="fas fa-truck"></i>
             <span>Suppliers</span>
         </a>
         <div id="collapseSuppliers" class="collapse" aria-labelledby="headingSuppliers" data-parent="#accordionSidebar">
             <div class="bg-white py-2 collapse-inner rounded">
                 <h6 class="collapse-header">Supplier Management</h6>
                
                 <a class="collapse-item" href="suppliers.php">Add Supplier</a>
             </div>
         </div>
     </li>
     <hr class="sidebar-divider">
     <div class="sidebar-heading">
         Sales Management
     </div>
     <li class="nav-item">
         <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCustomers" aria-expanded="true" aria-controls="collapseCustomers">
             <i class="fas fa-users"></i>
             <span>Customers</span>
         </a>
         <div id="collapseCustomers" class="collapse" aria-labelledby="headingCustomers" data-parent="#accordionSidebar">
             <div class="bg-white py-2 collapse-inner rounded">
                 <h6 class="collapse-header">Customer Operations</h6>
                 <a class="collapse-item" href="customers.php">All Customers</a>
                 <a class="collapse-item" href="add_customer.php">Add Customer</a>
             </div>
         </div>
     </li>
     <li class="nav-item">
         <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSales" aria-expanded="true" aria-controls="collapseSales">
             <i class="fas fa-cash-register"></i>
             <span>Sales</span>
         </a>
         <div id="collapseSales" class="collapse" aria-labelledby="headingSales" data-parent="#accordionSidebar">
             <div class="bg-white py-2 collapse-inner rounded">
                 <h6 class="collapse-header">Sales Operations</h6>
                 <a class="collapse-item" href="sales.php">New Sale</a>
                 <a class="collapse-item" href="new_sale.php">New Sale</a>
                 <a class="collapse-item" href="sales_report.php">Sales Reports</a>
             </div>
         </div>
     </li>
     <hr class="sidebar-divider">
     <div class="sidebar-heading">
         Purchasing
     </div>
     <li class="nav-item">
         <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePurchases" aria-expanded="true" aria-controls="collapsePurchases">
             <i class="fas fa-shopping-cart"></i>
             <span>Purchases</span>
         </a>
         <div id="collapsePurchases" class="collapse" aria-labelledby="headingPurchases" data-parent="#accordionSidebar">
             <div class="bg-white py-2 collapse-inner rounded">
                 <h6 class="collapse-header">Purchase Operations</h6>
                
                 <a class="collapse-item" href="new_purchase.php">New Purchase</a>
                <a class="collapse-item" href="Store_items.php">Store  items</a>
                
             </div>
         </div>
     </li>
     <hr class="sidebar-divider">
     <div class="sidebar-heading">
         Reports & Analytics
     </div>
     <li class="nav-item">
         <a class="nav-link" href="financial_reports.php">
             <i class="fas fa-chart-line"></i>
             <span>Financial Reports</span>
         </a>
     </li>
     <li class="nav-item">
         <a class="nav-link" href="performance_analytics.php">
             <i class="fas fa-chart-pie"></i>
             <span>Performance Analytics</span>
         </a>
     </li>
     <hr class="sidebar-divider">
     <div class="sidebar-heading">
         System Administration
     </div>
     <li class="nav-item">
         <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAdmin" aria-expanded="true" aria-controls="collapseAdmin">
             <i class="fas fa-user-cog"></i>
             <span>Administration</span>
         </a>
         <div id="collapseAdmin" class="collapse" aria-labelledby="headingAdmin" data-parent="#accordionSidebar">
             <div class="bg-white py-2 collapse-inner rounded">
                 <h6 class="collapse-header">System Settings</h6>
                 <a class="collapse-item" href="users.php">User Management</a>
                 <a class="collapse-item" href="audit_logs.php">Audit Logs</a>
                 <a class="collapse-item" href="system_settings.php">System Settings</a>
             </div>
         </div>
     </li>
</ul>

<style>
/* Enhanced Sidebar Styling */
:root {
    --sidebar-bg: linear-gradient(45deg, #0d1b3a, #1a237e);
    --accent-color: #FFD700;
 
    --hover-bg: rgba(255, 215, 0, 0.1);
}

.sidebar {
    background: var(--sidebar-bg) !important;
    backdrop-filter: blur(12px);
    border-right: none;
}

.sidebar-brand {
    background: rgba(0, 0, 0, 0.2) !important;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.sidebar-brand:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 217, 0, 0.2);
}

.nav-link, .collapse-item {
    color: var(--text-primary) !important;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    transition: all 0.2s ease;
}

.nav-link.active {
    background: var(--accent-color) !important;
    color: #000 !important;
    font-weight: bold;
}

.nav-link:hover {
    background: var(--hover-bg) !important;
    transform: translateX(5px);
}

.collapse-item:hover {
    background: rgba(255, 255, 255, 0.1);
}

.sidebar-brand-text {
    font-size: 1.1rem;
    letter-spacing: 1px;
    font-weight: bold;
}

.sidebar-divider {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-heading {
    color: var(--accent-color) !important;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Logo Animation */
.sidebar-brand-icon {
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
    transform-origin: center;
}

.sidebar-brand:hover .sidebar-brand-icon {
    transform: rotate(15deg) scale(1.1);
    filter: drop-shadow(0 0 8px rgba(252, 211, 77, 0.5));
}

/* Continuous Floating Animation */
@keyframes logoFloat {
    0% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-8px) rotate(5deg); }
    100% { transform: translateY(0px) rotate(0deg); }
}

.sidebar-brand-icon img {
    animation: logoFloat 4s ease-in-out infinite;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }
    .sidebar-brand {
        padding: 1rem 0;
    }
}
</style>