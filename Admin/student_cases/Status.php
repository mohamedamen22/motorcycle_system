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

// Get statistics for dashboard
$statsQuery = "SELECT 
    COUNT(*) as total_cases,
    SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_cases,
    SUM(CASE WHEN status = 'investigating' THEN 1 ELSE 0 END) as investigating_cases,
    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_cases,
    SUM(CASE WHEN severity = 'critical' THEN 1 ELSE 0 END) as critical_cases,
    SUM(CASE WHEN severity = 'high' THEN 1 ELSE 0 END) as high_cases
FROM student_cases $whereClause";

$statsStmt = $pdo->prepare($statsQuery);
$statsStmt->execute($params);
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

// Get total count for pagination
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM student_cases $whereClause");
$countStmt->execute($params);
$totalCases = $countStmt->fetchColumn();

// Pagination
$perPage = 15;
$totalPages = ceil($totalCases / $perPage);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, min($page, $totalPages));
$offset = ($page - 1) * $perPage;

// Get cases with additional details
$query = "SELECT sc.*, 
            (SELECT COUNT(*) FROM case_notes WHERE case_id = sc.case_id) as note_count,
            (SELECT MAX(created_at) FROM case_notes WHERE case_id = sc.case_id) as last_note_date
          FROM student_cases sc 
          $whereClause 
          ORDER BY case_date DESC, case_time DESC 
          LIMIT :limit OFFSET :offset";

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
    <title>Advanced Student Cases Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .stat-card {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stat-card.total { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card.open { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-card.investigating { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card.resolved { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .stat-card.critical { background: linear-gradient(135deg, #ff758c 0%, #ff7eb3 100%); }
        .stat-card.high { background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%); color: #333; }
        
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 500;
        }
        .badge-open { background-color: #f8d7da; color: #721c24; }
        .badge-investigating { background-color: #cce5ff; color: #004085; }
        .badge-resolved { background-color: #d4edda; color: #155724; }
        .badge-closed { background-color: #e2e3e5; color: #383d41; }
        
        .severity-low { color: #28a745; font-weight: bold; }
        .severity-medium { color: #ffc107; font-weight: bold; }
        .severity-high { color: #fd7e14; font-weight: bold; }
        .severity-critical { color: #dc3545; font-weight: bold; }
        
        .case-card {
            border-left: 4px solid;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .case-card.critical { border-left-color: #dc3545; }
        .case-card.high { border-left-color: #fd7e14; }
        .case-card.medium { border-left-color: #ffc107; }
        .case-card.low { border-left-color: #28a745; }
        
        .note-indicator {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #6c757d;
            color: white;
            text-align: center;
            line-height: 20px;
            font-size: 12px;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="header bg-primary text-white py-3">
        <div class="container">
            <h1 class="mb-0">Advanced Student Cases Management</h1>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <div class="navbar-nav">
                <a class="nav-link active" href="index.php">Dashboard</a>
                <a class="nav-link" href="add_case.php">Add New Case</a>
                <a class="nav-link" href="students_with_cases.php">Students with Cases</a>
                <a class="nav-link" href="reports.php">Reports</a>
                <a class="nav-link" href="../index.php">Back to Main</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <?php displayMessage(); ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card total">
                    <h5>Total Cases</h5>
                    <h2><?= $stats['total_cases'] ?? 0 ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card open">
                    <h5>Open Cases</h5>
                    <h2><?= $stats['open_cases'] ?? 0 ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card investigating">
                    <h5>Investigating</h5>
                    <h2><?= $stats['investigating_cases'] ?? 0 ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card resolved">
                    <h5>Resolved Cases</h5>
                    <h2><?= $stats['resolved_cases'] ?? 0 ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card critical">
                    <h5>Critical Cases</h5>
                    <h2><?= $stats['critical_cases'] ?? 0 ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card high">
                    <h5>High Priority</h5>
                    <h2><?= $stats['high_cases'] ?? 0 ?></h2>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h2 class="mb-0">Filter Cases</h2>
            </div>
            <div class="card-body">
                <form id="filter-form" method="get" action="index.php">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="student_id" class="form-label">Student ID</label>
                            <input type="text" id="student_id" name="student_id" class="form-control" 
                                   value="<?= htmlspecialchars($_GET['student_id'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="case_type" class="form-label">Case Type</label>
                            <select id="case_type" name="case_type" class="form-select">
                                <option value="">All Types</option>
                                <?php
                                $types = $pdo->query("SELECT DISTINCT case_type FROM student_cases ORDER BY case_type")->fetchAll(PDO::FETCH_COLUMN);
                                foreach ($types as $type) {
                                    $selected = ($_GET['case_type'] ?? '') == $type ? 'selected' : '';
                                    echo "<option value=\"$type\" $selected>" . htmlspecialchars($type) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="">All Cases</option>
                                <option value="open" <?= ($_GET['status'] ?? '') == 'open' ? 'selected' : '' ?>>Open</option>
                                <option value="investigating" <?= ($_GET['status'] ?? '') == 'investigating' ? 'selected' : '' ?>>Investigating</option>
                                <option value="resolved" <?= ($_GET['status'] ?? '') == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                <option value="closed" <?= ($_GET['status'] ?? '') == 'closed' ? 'selected' : '' ?>>Closed</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="severity" class="form-label">Severity</label>
                            <select id="severity" name="severity" class="form-select">
                                <option value="">All Severities</option>
                                <option value="low" <?= ($_GET['severity'] ?? '') == 'low' ? 'selected' : '' ?>>Low</option>
                                <option value="medium" <?= ($_GET['severity'] ?? '') == 'medium' ? 'selected' : '' ?>>Medium</option>
                                <option value="high" <?= ($_GET['severity'] ?? '') == 'high' ? 'selected' : '' ?>>High</option>
                                <option value="critical" <?= ($_GET['severity'] ?? '') == 'critical' ? 'selected' : '' ?>>Critical</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="from_date" class="form-label">From Date</label>
                            <input type="date" id="from_date" name="from_date" class="form-control" 
                                   value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="to_date" class="form-label">To Date</label>
                            <input type="date" id="to_date" name="to_date" class="form-control" 
                                   value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                        <button type="button" id="reset-filter" class="btn btn-outline-secondary">Reset Filter</button>
                        <a href="export.php?<?= http_build_query($_GET) ?>" class="btn btn-success">Export to Excel</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cases Table -->
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Student Cases</h2>
                <span class="badge bg-primary">Total: <?= $totalCases ?> cases</span>
            </div>
            
            <div class="card-body">
                <?php if (empty($cases)): ?>
                    <div class="alert alert-info">No cases found matching your criteria</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Case ID</th>
                                    <th>Student</th>
                                    <th>Date/Time</th>
                                    <th>Type</th>
                                    <th>Severity</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cases as $case): ?>
                                    <tr class="case-card <?= $case['severity'] ?>">
                                        <td><?= $case['case_id'] ?></td>
                                        <td>
                                            <strong><?= $case['student_id'] ?></strong><br>
                                            <?= htmlspecialchars($case['student_FullName']) ?>
                                        </td>
                                        <td>
                                            <?= date('M j, Y', strtotime($case['case_date'])) ?><br>
                                            <small><?= date('g:i a', strtotime($case['case_time'])) ?></small>
                                        </td>
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
                                            <?php if ($case['note_count'] > 0): ?>
                                                <span class="note-indicator" title="<?= $case['note_count'] ?> notes">
                                                    <?= $case['note_count'] ?>
                                                </span>
                                                <small class="text-muted"><?= date('M j', strtotime($case['last_note_date'])) ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">No notes</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="view_case.php?id=<?= $case['case_id'] ?>" class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="edit_case.php?id=<?= $case['case_id'] ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="add_note.php?case_id=<?= $case['case_id'] ?>" class="btn btn-sm btn-outline-info" title="Add Note">
                                                    <i class="bi bi-journal-text"></i>
                                                </a>
                                                <a href="index.php?delete=<?= $case['case_id'] ?>" class="btn btn-sm btn-outline-danger btn-delete" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($totalPages > 1): ?>
                <div class="card-footer">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">
                                        First
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                                        Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($page + 2, $totalPages); $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="index.php?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                                        Next
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?<?= http_build_query(array_merge($_GET, ['page' => $totalPages])) ?>">
                                        Last
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Charts Modal -->
    <div class="modal fade" id="chartsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cases Analytics</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="statusChart" height="250"></canvas>
                        </div>
                        <div class="col-md-6">
                            <canvas id="severityChart" height="250"></canvas>
                        </div>
                        <div class="col-md-12 mt-4">
                            <canvas id="timelineChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Initialize date pickers
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            allowInput: true
        });

        // Delete confirmation
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this case?')) {
                    e.preventDefault();
                }
            });
        });

        // Reset filter
        document.getElementById('reset-filter').addEventListener('click', function() {
            window.location.href = 'index.php';
        });

        // Charts data (example - you would replace with real data from your database)
        const statusData = {
            labels: ['Open', 'Investigating', 'Resolved', 'Closed'],
            datasets: [{
                data: [<?= $stats['open_cases'] ?? 0 ?>, <?= $stats['investigating_cases'] ?? 0 ?>, 
                       <?= $stats['resolved_cases'] ?? 0 ?>, <?= ($stats['total_cases'] ?? 0) - ($stats['open_cases'] + $stats['investigating_cases'] + $stats['resolved_cases']) ?>],
                backgroundColor: [
                    '#f8d7da',
                    '#cce5ff',
                    '#d4edda',
                    '#e2e3e5'
                ]
            }]
        };

        const severityData = {
            labels: ['Critical', 'High', 'Medium', 'Low'],
            datasets: [{
                data: [<?= $stats['critical_cases'] ?? 0 ?>, <?= $stats['high_cases'] ?? 0 ?>, 
                       <?= ($stats['total_cases'] ?? 0) - ($stats['critical_cases'] + $stats['high_cases']) ?>, 0], // Simplified
                backgroundColor: [
                    '#dc3545',
                    '#fd7e14',
                    '#ffc107',
                    '#28a745'
                ]
            }]
        };

        // Initialize charts when modal is shown
        document.getElementById('chartsModal').addEventListener('shown.bs.modal', function() {
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: statusData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        title: { display: true, text: 'Cases by Status' }
                    }
                }
            });

            new Chart(document.getElementById('severityChart'), {
                type: 'pie',
                data: severityData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        title: { display: true, text: 'Cases by Severity' }
                    }
                }
            });

            // Timeline chart would be more complex with real date-based data
        });
    </script>
</body>
</html>