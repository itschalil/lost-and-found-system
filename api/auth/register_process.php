<?php
session_start();
require_once '../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate
    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['register_error'] = "All fields are required.";
        header("Location: ../../pages/register.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['register_error'] = "Passwords do not match.";
        header("Location: ../../pages/register.php");
        exit();
    }

    if (strlen($password) < 6) {
        $_SESSION['register_error'] = "Password must be at least 6 characters.";
        header("Location: ../../pages/register.php");
        exit();
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['register_error'] = "Email already exists.";
        header("Location: ../../pages/register.php");
        exit();
    }

    // Hash password and insert
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['register_success'] = "Account created successfully! Please login.";
        header("Location: ../../pages/login.php");
        exit();
    } else {
        $_SESSION['register_error'] = "Something went wrong. Try again.";
        header("Location: ../../pages/register.php");
        exit();
    }
}
?>