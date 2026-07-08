<?php
// api/items/add_item.php
session_start();
require_once '../../config/db_connect.php';

// TODO: Add authentication check
// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../pages/login.php");
//     exit();
// }

// For testing, hardcode user ID
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $itemType = trim($_POST['item_type'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($itemType) || !in_array($itemType, ['lost', 'found'])) {
        $errors[] = 'Invalid item type';
    }
    
    if (empty($title) || strlen($title) > 100) {
        $errors[] = 'Title is required (max 100 characters)';
    }
    
    if (empty($location) || strlen($location) > 200) {
        $errors[] = 'Location is required (max 200 characters)';
    }
    
    if (empty($description) || strlen($description) > 1000) {
        $errors[] = 'Description is required (max 1000 characters)';
    }
    
    // Handle image upload
    $imagePath = '';
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/items/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $file = $_FILES['item_image'];
        $fileSize = $file['size'];
        $fileType = $file['type'];
        $fileTmpName = $file['tmp_name'];
        
        // Validate file size (5MB max)
        if ($fileSize > 5 * 1024 * 1024) {
            $errors[] = 'Image size must be less than 5MB';
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = 'Only JPG, JPEG, and PNG files are allowed';
        }
        
        // Generate unique filename
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = uniqid('item_', true) . '.' . $fileExtension;
        $imagePath = 'uploads/items/' . $newFilename;
        $uploadPath = $uploadDir . $newFilename;
        
        // Move uploaded file
        if (empty($errors) && !move_uploaded_file($fileTmpName, $uploadPath)) {
            $errors[] = 'Failed to upload image';
        }
    } else {
        $errors[] = 'Image is required';
    }
    
    // If there are errors, redirect back with error message
    if (!empty($errors)) {
        $_SESSION['error'] = implode(', ', $errors);
        header("Location: ../../pages/report_item.php");
        exit();
    }
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO items (user_id, title, description, location, image_url, type, approval_status, claim_status, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, 'pending', 'unclaimed', NOW())");
    $stmt->bind_param("isssss", $userId, $title, $description, $location, $imagePath, $itemType);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Item reported successfully! It will be reviewed by an admin.';
        header("Location: ../../pages/profile.php");
        exit();
    } else {
        $_SESSION['error'] = 'Failed to submit report. Please try again.';
        header("Location: ../../pages/report_item.php");
        exit();
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: ../../pages/report_item.php");
    exit();
}
?>