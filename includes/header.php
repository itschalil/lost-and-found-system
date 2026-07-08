<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost and Found System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <?php if ($current_page == 'login.php' || $current_page == 'register.php'): ?>
        <link rel="stylesheet" href="../assets/css/auth.css">
    <?php elseif ($current_page == 'index.php'): ?>
        <link rel="stylesheet" href="../assets/css/dashboard.css">
    <?php elseif ($current_page == 'report_item.php'): ?>
        <link rel="stylesheet" href="../assets/css/form.css">
    <?php elseif ($current_page == 'profile.php' || $current_page == 'item_details.php'): ?>
        <link rel="stylesheet" href="../assets/css/profile.css">
    <?php endif; ?>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="../pages/index.php" class="nav-logo">🔍 Lost & Found</a>
            <ul class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="../pages/index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="../pages/report_item.php" class="<?php echo $current_page == 'report_item.php' ? 'active' : ''; ?>">Report Item</a></li>
                    <li><a href="../pages/profile.php" class="<?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">My Reports</a></li>
                    <li><a href="../api/auth/logout.php" class="btn-logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="../pages/login.php" class="<?php echo $current_page == 'login.php' ? 'active' : ''; ?>">Login</a></li>
                    <li><a href="../pages/register.php" class="<?php echo $current_page == 'register.php' ? 'active' : ''; ?>">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <main class="main-content">