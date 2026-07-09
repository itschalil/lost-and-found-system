<?php
// pages/item_details.php
session_start();
require_once '../config/db_connect.php';
include '../includes/header.php';

// I-check kung may ID na nasa URL
$itemId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($itemId > 0) {
    // Kunin ang details ng item at pangalan ng nag-report
    $stmt = $conn->prepare("SELECT items.*, users.name as reporter_name 
                            FROM items 
                            JOIN users ON items.user_id = users.id 
                            WHERE items.id = ?");
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();
} else {
    $item = null;
}
?>

<div class="details-container">
    <?php if ($item): ?>
        <a href="index.php" class="back-link">&larr; Back to Dashboard</a>
        
        <div class="details-card">
            <!-- Left Side: Image -->
            <div class="details-image">
                <img src="../<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
            </div>

            <!-- Right Side: Info -->
            <div class="details-info">
                <div class="info-header">
                    <h1><?php echo htmlspecialchars($item['title']); ?></h1>
                    <span class="badge badge-<?php echo $item['type']; ?>">
                        <?php echo strtoupper($item['type']); ?>
                    </span>
                </div>

                <div class="info-meta">
                    <p><strong>📍 Location:</strong> <?php echo htmlspecialchars($item['location']); ?></p>
                    <p><strong>📅 Date Reported:</strong> <?php echo date('F d, Y h:i A', strtotime($item['created_at'])); ?></p>
                    <p><strong>👤 Reported by:</strong> <?php echo htmlspecialchars($item['reporter_name']); ?></p>
                </div>

                <div class="info-description">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                </div>

                <div class="info-status">
                    <span class="status-label">Status:</span>
                    <span class="status-value <?php echo $item['claim_status'] === 'claimed' ? 'claimed' : 'unclaimed'; ?>">
                        <?php echo strtoupper($item['claim_status']); ?>
                    </span>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $item['user_id']): ?>
                        <button class="btn-claim" onclick="alert('Feature coming soon: Mark as Claimed')">Mark as Claimed</button>
                    <?php else: ?>
                        <button class="btn-contact" onclick="alert('Feature coming soon: Contact Reporter')">Contact Reporter</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="error-state">
            <h2>Item Not Found</h2>
            <p>The item you are looking for does not exist or has been removed.</p>
            <a href="index.php" class="back-link">&larr; Go back to Home</a>
        </div>
    <?php endif; ?>
</div>

<?php 
$conn->close();
include '../includes/footer.php'; 
?>