<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = $_SESSION['register_error'] ?? '';
unset($_SESSION['register_error']);
?>
<?php include '../includes/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h2>Create Account</h2>
        <p class="subtitle">Join the Lost and Found community</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="../api/auth/register_process.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Choose a username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password (min 6 chars)" required minlength="6">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>

        <p class="auth-link">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>