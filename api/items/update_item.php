<?php
// api/items/update_item.php
header('Content-Type: application/json');
session_start();
require_once '../../config/db_connect.php';

// TODO: Add authentication check
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(['success' => false, 'message' => 'Unauthorized']);
//     exit();
// } 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = $_POST['item_id'] ?? null;
    $action = $_POST['action'] ?? null;
    
    if (!$itemId || !$action) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }
    
    if ($action === 'claim') {
        // Update the claim_status to 'claimed'
        $stmt = $conn->prepare("UPDATE items SET claim_status = 'claimed' WHERE id = ?");
        $stmt->bind_param("i", $itemId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Item marked as claimed successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update item status']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>