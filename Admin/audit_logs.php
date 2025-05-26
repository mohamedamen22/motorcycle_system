<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Set pagination variables
$entries_per_page = 20;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $entries_per_page;

// Get total number of entries for pagination
$count_query = "SELECT COUNT(*) as total FROM audit_log";
$count_result = $conn->query($count_query);
$total_entries = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_entries / $entries_per_page);

// Get log entries with admin usernames (assuming you have an admins table)
$query = "SELECT a.log_id, a.admin_id, u.username, a.action, a.log_time 
          FROM audit_log a
          LEFT JOIN admins u ON a.admin_id = u.admin_id
          ORDER BY a.log_time DESC
          LIMIT $offset, $entries_per_page";
$result = $conn->query($query);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        .pagination a {
            color: #333;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 4px;
        }
        .pagination a.active {
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
        }
        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
        .search-box {
            margin-bottom: 20px;
        }
        .search-box input {
            padding: 8px;
            width: 300px;
        }
        .search-box button {
            padding: 8px 15px;
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>System Audit Logs</h1>
        
        <!-- Search Box (optional) -->
        <div class="search-box">
            <form method="get" action="">
                <input type="text" name="search" placeholder="Search actions or users...">
                <button type="submit">Search</button>
            </form>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['log_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['username'] ?? 'Unknown'); ?> (ID: <?php echo $row['admin_id']; ?>)</td>
                    <td><?php echo htmlspecialchars($row['action']); ?></td>
                    <td><?php echo date('Y-m-d H:i:s', strtotime($row['log_time'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="pagination">
            <?php if ($current_page > 1): ?>
                <a href="?page=1">&laquo; First</a>
                <a href="?page=<?php echo $current_page - 1; ?>">&lsaquo; Previous</a>
            <?php endif; ?>
            
            <?php 
            // Show page numbers
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++): 
            ?>
                <a href="?page=<?php echo $i; ?>" <?php if ($i == $current_page) echo 'class="active"'; ?>>
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?php echo $current_page + 1; ?>">Next &rsaquo;</a>
                <a href="?page=<?php echo $total_pages; ?>">Last &raquo;</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>