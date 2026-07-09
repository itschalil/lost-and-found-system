<?php
session_start();
require_once '../config/db_connect.php';

// Fetch items
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// FIXED: users.name instead of users.username
$sql = "SELECT items.*, users.name as username FROM items 
        LEFT JOIN users ON items.user_id = users.id WHERE 1=1";

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

// FIXED: items.type instead of items.category
if (!empty($category) && in_array($category, ['lost', 'found'])) {
    $sql .= " AND items.type = ?";
    $params[] = $category;
    $types .= "s";
}

$sql .= " ORDER BY items.created_at DESC";

$stmt = $conn->prepare($sql);

// ✅ Error handling para makita kung may mali sa SQL
if (!$stmt) {
    die("SQL Prepare Error: " . $conn->error);
}

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
                    <!-- FIXED: image_url instead of image_path -->
                    <?php if (!empty($item['image_url'])): ?>
                        <img src="../<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <?php else: ?>
                        📦
                    <?php endif; ?>
                </div>
                <div class="item-card-body">
                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p>📍 <?php echo htmlspecialchars($item['location']); ?></p>
                    <!-- FIXED: created_at instead of date_reported -->
                    <p>📅 <?php echo date('M d, Y', strtotime($item['created_at'])); ?></p>
                    <p>👤 <?php echo htmlspecialchars($item['username']); ?></p>
                    <!-- FIXED: type instead of category -->
                    <span class="item-badge badge-<?php echo $item['type']; ?>">
                        <?php echo ucfirst($item['type']); ?>
                    </span>
                    <!-- FIXED: claim_status instead of status -->
                    <?php if ($item['claim_status'] == 'claimed'): ?>
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