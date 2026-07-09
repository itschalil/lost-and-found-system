<?php
// api/users/fetch_user_reports.php
header('Content-Type: application/json');
session_start();
require_once '../../config/db_connect.php';

// TODO: Add authentication check
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(['success' => false, 'message' => 'Unauthorized']);
//     exit();
// }
// Kunin ang User ID (mula sa session o GET parameter para sa testing)
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0);

if ($userId > 0) {
    $stmt = $conn->prepare("SELECT * FROM items WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $items]);
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
}

$conn->close();
?>