<?php
require_once 'db_connect.php';
require_once 'includes/functions.php';

// Handle case deletion
if (isset($_GET['delete'])) {
    $case_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM student_cases WHERE case_id = ?");
    $stmt->execute([$case_id]);
    $_SESSION['message'] = "Case deleted successfully";
    header("Location: index.php");
    exit();
}

// Build filter query
$where = [];
$params = [];

if (!empty($_GET['student_id'])) {
    $where[] = "student_id = ?";
    $params[] = $_GET['student_id'];
}

if (!empty($_GET['case_type'])) {
    $where[] = "case_type LIKE ?";
    $params[] = '%' . $_GET['case_type'] . '%';
}

if (!empty($_GET['status'])) {
    $where[] = "status = ?";
    $params[] = $_GET['status'];
}

if (!empty($_GET['severity'])) {
    $where[] = "severity = ?";
    $params[] = $_GET['severity'];
}

if (!empty($_GET['from_date'])) {
    $where[] = "case_date >= ?";
    $params[] = $_GET['from_date'];
}

if (!empty($_GET['to_date'])) {
    $where[] = "case_date <= ?";
    $params[] = $_GET['to_date'];
}

$whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

// Get total count for pagination
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM student_cases $whereClause");
$countStmt->execute($params);
$totalCases = $countStmt->fetchColumn();

// Pagination
$perPage = 10;
$totalPages = ceil($totalCases / $perPage);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, min($page, $totalPages));
$offset = ($page - 1) * $perPage;

// Get cases - REMOVED case_time FROM ORDER BY
$query = "SELECT * FROM student_cases $whereClause ORDER BY case_date DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);

// Bind all filter parameters if they exist
foreach ($params as $key => $value) {
    $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $stmt->bindValue($key + 1, $value, $paramType);
}

// Bind pagination parameters as integers
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Cases Management</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Student Cases Management System</h1>
        </div>
    </div>

    <div class="navbar">
        <div class="container">
            <a href="Admin/index.php">Home</a>
            <a href="add_case.php">Add New Case</a>
            <a href="../index.php" >
   
             Back to Dashboard
</a>  <a href="Trackingpage.php">Student Cases Tracking Page</a>
<a href="Status.php">Student Cases Status</a>

        </div>
    </div>
    

    <div class="container">
        <?php displayMessage(); ?>

        <div class="card">
            <div class="card-header">
                <h2>Filter Cases</h2>
            </div>
            <form id="filter-form" method="get" action="index.php">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                    <div class="form-group">
                        <label for="student_id">Student ID</label>
                        <input type="text" id="student_id" name="student_id" class="form-control" 
                               value="<?= htmlspecialchars($_GET['student_id'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="case_type">Case Type</label>
                        <input type="text" id="case_type" name="case_type" class="form-control" 
                               value="<?= htmlspecialchars($_GET['case_type'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="open" <?= ($_GET['status'] ?? '') == 'open' ? 'selected' : '' ?>>Open</option>
                            <option value="investigating" <?= ($_GET['status'] ?? '') == 'investigating' ? 'selected' : '' ?>>Investigating</option>
                            <option value="resolved" <?= ($_GET['status'] ?? '') == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                            <option value="closed" <?= ($_GET['status'] ?? '') == 'closed' ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="severity">Severity</label>
                        <select id="severity" name="severity" class="form-control">
                            <option value="">All Severities</option>
                            <option value="low" <?= ($_GET['severity'] ?? '') == 'low' ? 'selected' : '' ?>>Low</option>
                            <option value="medium" <?= ($_GET['severity'] ?? '') == 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="high" <?= ($_GET['severity'] ?? '') == 'high' ? 'selected' : '' ?>>High</option>
                            <option value="critical" <?= ($_GET['severity'] ?? '') == 'critical' ? 'selected' : '' ?>>Critical</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="from_date">From Date</label>
                        <input type="date" id="from_date" name="from_date" class="form-control datepicker" 
                               value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="to_date">To Date</label>
                        <input type="date" id="to_date" name="to_date" class="form-control datepicker" 
                               value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">
                    </div>
                </div>
                
                <div style="margin-top: 15px;">
                    <button type="submit" class="btn">Apply Filter</button>
                    <button type="button" id="reset-filter" class="btn">Reset Filter</button>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Student Cases</h2>
                <span>Total: <?= $totalCases ?> cases</span>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Case ID</th>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Case Date</th>
                        <th>Case Type</th>
                        <th>Severity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cases)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">No cases found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cases as $case): ?>
                            <tr>
                                <td><?= $case['case_id'] ?></td>
                                <td><?= $case['student_id'] ?></td>
                                <td><?= htmlspecialchars($case['student_FullName']) ?></td>
                                <td><?= date('M j, Y', strtotime($case['case_date'])) ?></td>
                                <td><?= htmlspecialchars($case['case_type']) ?></td>
                                <td class="severity-<?= $case['severity'] ?>">
                                    <?= ucfirst($case['severity']) ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $case['status'] ?>">
                                        <?= ucfirst($case['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="view_case.php?id=<?= $case['case_id'] ?>" class="btn">View</a>
                                    <a href="edit_case.php?id=<?= $case['case_id'] ?>" class="btn">Edit</a>
                                    <a href="index.php?delete=<?= $case['case_id'] ?>" class="btn btn-danger btn-delete">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <?php if ($totalPages > 1): ?>
                <div style="display: flex; justify-content: center; margin-top: 20px;">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="index.php?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                           class="btn <?= $i == $page ? 'btn-success' : '' ?>" 
                           style="margin: 0 5px;">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="script.js"></script>
</body>
</html>