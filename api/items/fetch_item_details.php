<?php
// api/items/fetch_item_details.php
header('Content-Type: application/json');
require_once '../../config/db_connect.php';

$itemId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($itemId > 0) {
    $stmt = $conn->prepare("SELECT items.*, users.name as reporter_name 
                            FROM items 
                            JOIN users ON items.user_id = users.id 
                            WHERE items.id = ?");
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    
    if ($item) {
        echo json_encode(['success' => true, 'data' => $item]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
}

$conn->close();
?>