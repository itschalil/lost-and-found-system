<?php
// pages/report_item.php
session_start();
require_once '../config/db_connect.php';
include '../includes/header.php';

// TODO: I-check kung naka-login ang user
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }
?>

<div class="report-container">
    <div class="report-card">
        <h1>Report Lost or Found Item</h1>
        <p class="form-subtitle">Please fill out the form below to report an item</p>

        <form id="reportForm" action="../api/items/add_item.php" method="POST" enctype="multipart/form-data">
            
            <!-- Item Type -->
            <div class="form-group">
                <label for="itemType">Item Type <span class="required">*</span></label>
                <select id="itemType" name="item_type" required>
                    <option value="">-- Select Type --</option>
                    <option value="lost">Lost Item</option>
                    <option value="found">Found Item</option>
                </select>
                <span class="error-message" id="error-itemType"></span>
            </div>

            <!-- Title -->
            <div class="form-group">
                <label for="title">Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" placeholder="e.g., Black Wallet, Blue Umbrella" required maxlength="100">
                <span class="error-message" id="error-title"></span>
            </div>

            <!-- Location -->
            <div class="form-group">
                <label for="location">Location <span class="required">*</span></label>
                <input type="text" id="location" name="location" placeholder="e.g., Library 2nd Floor, Canteen" required maxlength="200">
                <span class="error-message" id="error-location"></span>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description">Description <span class="required">*</span></label>
                <textarea id="description" name="description" rows="5" placeholder="Describe the item in detail (color, brand, distinguishing features, etc.)" required maxlength="1000"></textarea>
                <span class="error-message" id="error-description"></span>
            </div>

            <!-- Image Upload -->
            <div class="form-group">
                <label for="itemImage">Item Image <span class="required">*</span></label>
                <div class="image-upload-area" id="imageUploadArea">
                    <input type="file" id="itemImage" name="item_image" accept="image/*" required>
                    <div class="upload-placeholder" id="uploadPlaceholder">
                        <span class="upload-icon">📷</span>
                        <p>Click to upload or drag and drop</p>
                        <p class="upload-hint">PNG, JPG, JPEG (Max 5MB)</p>
                    </div>
                    <img id="imagePreview" class="image-preview" style="display: none;">
                </div>
                <span class="error-message" id="error-itemImage"></span>
            </div>

            <!-- Submit Button -->
            <div class="form-actions">
                <button type="submit" class="btn-submit" id="submitBtn">Submit Report</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>
</div>

<script src="../assets/js/form_validation.js"></script>

<?php 
$conn->close();
include '../includes/footer.php'; 
?>