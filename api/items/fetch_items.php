<?php
require_once '../../config/db_connect.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$sql = "SELECT items.*, users.username FROM items 
        JOIN users ON items.user_id = users.id 
        WHERE items.status = 'active'";

$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (items.title LIKE ? OR items.description LIKE ? OR items.location LIKE ?)";
    $searchParam = "%" . $search . "%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "sss";
}

if (!empty($category) && ($category === 'lost' || $category === 'found')) {
    $sql .= " AND items.category = ?";
    $params[] = $category;
    $types .= "s";
}

$sql .= " ORDER BY items.created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);

header('Content-Type: application/json');
echo json_encode($items);
?>