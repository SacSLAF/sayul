<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
require_once '../db_connect.php';

$message = '';
$messageType = '';

// Handle Image Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {
    $caption = $_POST['caption'];
    $category = $_POST['category'];
    
    $targetDir = "../assets/uploads/gallery/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName; // Add timestamp to avoid collisions
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');
    if (in_array(strtolower($fileType), $allowTypes)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            // Store relative path in DB
            $dbPath = "assets/uploads/gallery/" . time() . "_" . $fileName;
            
            $stmt = $conn->prepare("INSERT INTO gallery_images (image_path, caption, category) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $dbPath, $caption, $category);
            
            if ($stmt->execute()) {
                $message = "Image uploaded successfully.";
                $messageType = "success";
            } else {
                $message = "Database error: " . $stmt->error;
                $messageType = "error";
            }
            $stmt->close();
        } else {
            $message = "Sorry, there was an error uploading your file.";
            $messageType = "error";
        }
    } else {
        $message = "Sorry, only JPG, JPEG, PNG, GIF, & WEBP files are allowed.";
        $messageType = "error";
    }
}

// Handle Image Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get path to delete file
    $stmt = $conn->prepare("SELECT image_path FROM gallery_images WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imagePath);
    $stmt->fetch();
    $stmt->close();
    
    if ($imagePath) {
        $fullPath = "../" . $imagePath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        
        $delStmt = $conn->prepare("DELETE FROM gallery_images WHERE id = ?");
        $delStmt->bind_param("i", $id);
        if ($delStmt->execute()) {
            $message = "Image deleted successfully.";
            $messageType = "success";
        } else {
            $message = "Error deleting record.";
            $messageType = "error";
        }
        $delStmt->close();
    }
}

// Fetch Images
$result = $conn->query("SELECT * FROM gallery_images ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - Sayul Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background: #f8f9fa; }
        .sidebar { width: 220px; background: #1a6d38; color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar h2 { margin-bottom: 30px; text-align: center; }
        .nav-links { list-style: none; padding: 0; }
        .nav-links li { margin-bottom: 15px; }
        .nav-links a { color: rgba(255,255,255,0.8); text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 5px; transition: 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: rgba(255,255,255,0.2); color: white; }
        .main-content { margin-left: 286px; padding: 30px; width: 100%; }
        
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; color: white; }
        .btn-primary { background: #1a6d38; }
        .btn-danger { background: #dc3545; padding: 5px 10px; font-size: 14px; }
        
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #1a6d38; color: white; }
        img.thumb { width: 80px; height: 60px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Sayul Admin</h2>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="packages.php"><i class="fas fa-box"></i> Packages</a></li>
            <li><a href="gallery.php" class="active"><i class="fas fa-images"></i> Gallery</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <h1>Manage Gallery</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h3>Upload New Image</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Select Image</label>
                    <input type="file" name="image" required>
                </div>
                <div class="form-group">
                    <label>Caption</label>
                    <input type="text" name="caption" placeholder="Enter image caption">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="general">General</option>
                        <option value="tours">Tours</option>
                        <option value="nature">Nature</option>
                        <option value="culture">Culture</option>
                    </select>
                </div>
                <button type="submit" name="upload" class="btn btn-primary">Upload Image</button>
            </form>
        </div>
        
        <div class="card">
            <h3>Existing Images</h3>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Caption</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><img src="../<?php echo htmlspecialchars($row['image_path']); ?>" class="thumb" alt="Thumb"></td>
                                <td><?php echo htmlspecialchars($row['caption']); ?></td>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td>
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this image?')"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No images found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
