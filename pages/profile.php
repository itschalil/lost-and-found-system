<?php
// pages/profile.php
session_start();
require_once '../config/db_connect.php';
include '../includes/header.php';

// TODO: I-check kung naka-login ang user
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

// For testing purposes, hardcode the user ID kung wala pang session
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

// Kunin ang user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Kunin ang listahan ng items na na-report ng user
$stmt = $conn->prepare("SELECT * FROM items WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$items = $stmt->get_result();
$stmt->close();
?>

<div class="profile-container">
    <!-- User Info Card -->
    <div class="user-info-card">
        <div class="user-avatar">
            <img src="../assets/images/default-avatar.png" alt="User Avatar">
        </div>
        <div class="user-details">
            <h1><?php echo htmlspecialchars($user['name']); ?></h1>
            <p class="user-email"><?php echo htmlspecialchars($user['email']); ?></p>
            <p class="user-role">Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
        </div>
    </div>

    <!-- My Reports Section -->
    <div class="reports-section">
        <h2>My Reports</h2>
        
        <?php if ($items->num_rows > 0): ?>
            <div class="reports-table-container">
                <table class="reports-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $items->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <img src="../<?php echo htmlspecialchars($item['image_url']); ?>" 
                                         alt="Item" 
                                         class="report-thumbnail">
                                </td>
                                <td>
                                    <a href="item_details.php?id=<?php echo $item['id']; ?>" class="item-link">
                                        <?php echo htmlspecialchars($item['title']); ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo $item['type']; ?>">
                                        <?php echo ucfirst($item['type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $item['claim_status']; ?>">
                                        <?php echo ucfirst($item['claim_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($item['created_at'])); ?></td>
                                <td>
                                    <?php if ($item['claim_status'] === 'unclaimed'): ?>
                                        <button class="btn-mark-claimed" 
                                                onclick="markAsClaimed(<?php echo $item['id']; ?>)">
                                            Mark as Claimed
                                        </button>
                                    <?php else: ?>
                                        <span class="claimed-text">✓ Claimed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-reports">
                <p>You haven't reported any items yet.</p>
                <a href="report_item.php" class="btn-report-now">Report an Item Now</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function markAsClaimed(itemId) {
    if (!confirm('Are you sure you want to mark this item as claimed?')) {
        return;
    }

    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('action', 'claim');

    fetch('../api/items/update_item.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
</script>

<?php 
$conn->close();
include '../includes/footer.php'; 
?>