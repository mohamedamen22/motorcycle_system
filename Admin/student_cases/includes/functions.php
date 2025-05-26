<?php
session_start();

function displayMessage() {
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
    
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
}

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
function getUserName($pdo, $user_id) {
    if (!$user_id) return 'System';
    
    try {
        $stmt = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        return $user ? htmlspecialchars($user['username']) : 'Unknown';
    } catch (PDOException $e) {
        error_log("Error getting username: " . $e->getMessage());
        return 'Unknown';
    }
}
?>
