<?php
session_start();
require_once '../config/db_connect.php';

// Fetch items
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$sql = "SELECT items.*, users.username FROM items 
        JOIN users ON items.user_id = users.id WHERE 1=1";

$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (items.title LIKE ? OR items.description LIKE ? OR items.location LIKE ?)";
    $s = "%" . $search . "%";
    $params[] = $s;
    $params[] = $s;
    $params[] = $s;
    $types .= "sss";
}

if (!empty($category) && in_array($category, ['lost', 'found'])) {
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
?>
<?php include '../includes/header.php'; ?>

<div class="dashboard-header">
    <h1>Lost and Found Board</h1>
    <p>Browse reported items or use the search to find what you need</p>
</div>

<!-- Search & Filter Bar -->
<form class="search-filter-bar" method="GET" action="index.php">
    <input type="text" name="search" placeholder="🔍 Search items..." value="<?php echo htmlspecialchars($search); ?>">
    <select name="category">
        <option value="">All Categories</option>
        <option value="lost" <?php echo $category == 'lost' ? 'selected' : ''; ?>>Lost</option>
        <option value="found" <?php echo $category == 'found' ? 'selected' : ''; ?>>Found</option>
    </select>
    <button type="submit" class="btn btn-primary">Search</button>
    <a href="index.php" class="btn btn-danger">Clear</a>
</form>

<!-- Items Grid -->
<div class="items-grid" id="itemsGrid">
    <?php if (count($items) > 0): ?>
        <?php foreach ($items as $item): ?>
            <a href="item_details.php?id=<?php echo $item['id']; ?>" class="item-card">
                <div class="item-card-image">
                    <?php if (!empty($item['image_path'])): ?>
                        <img src="../<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <?php else: ?>
                        📦
                    <?php endif; ?>
                </div>
                <div class="item-card-body">
                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p>📍 <?php echo htmlspecialchars($item['location']); ?></p>
                    <p>📅 <?php echo date('M d, Y', strtotime($item['date_reported'])); ?></p>
                    <p>👤 <?php echo htmlspecialchars($item['username']); ?></p>
                    <span class="item-badge badge-<?php echo $item['category']; ?>">
                        <?php echo ucfirst($item['category']); ?>
                    </span>
                    <?php if ($item['status'] == 'claimed'): ?>
                        <span class="item-badge badge-claimed">Claimed</span>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-items">
            <h3>No items found</h3>
            <p>Try adjusting your search or be the first to report an item!</p>
        </div>
    <?php endif; ?>
</div>

<script src="../assets/js/search_filter.js"></script>
<?php include '../includes/footer.php'; ?>