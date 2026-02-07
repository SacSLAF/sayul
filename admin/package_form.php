<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
require_once '../db_connect.php';

$id = '';
$title = '';
$price = '';
$price_note = '';
$duration = '';
$description = '';
$includes = '';
$is_featured = 0;
$image_path = '';
$message = '';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM packages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $title = $row['title'];
        $price = $row['price'];
        $price_note = $row['price_note'];
        $duration = $row['duration'];
        $description = $row['description'];
        $includes = $row['includes'];
        $is_featured = $row['is_featured'];
        $image_path = $row['image_path'];
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $price_note = $_POST['price_note'];
    $duration = $_POST['duration'];
    $description = $_POST['description'];
    $includes = $_POST['includes']; // Storing as newline separated text, can format on display
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Handle Image Upload
    $new_image_path = $image_path;
    if (!empty($_FILES["image"]["name"])) {
        $targetDir = "../assets/uploads/packages/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        
        $fileName = basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . time() . "_" . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($fileType), ['jpg', 'jpeg', 'png', 'webp'])) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $new_image_path = "assets/uploads/packages/" . time() . "_" . $fileName;
            }
        }
    }
    
    if ($id) {
        $stmt = $conn->prepare("UPDATE packages SET title=?, price=?, price_note=?, duration=?, description=?, includes=?, is_featured=?, image_path=? WHERE id=?");
        $stmt->bind_param("sdssssisi", $title, $price, $price_note, $duration, $description, $includes, $is_featured, $new_image_path, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO packages (title, price, price_note, duration, description, includes, is_featured, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssssis", $title, $price, $price_note, $duration, $description, $includes, $is_featured, $new_image_path);
    }
    
    if ($stmt->execute()) {
        header("Location: packages.php");
        exit();
    } else {
        $message = "Error saving package: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit' : 'Add'; ?> Package - Sayul Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background: #f8f9fa; }
        .sidebar { width: 220px; background: #1a6d38; color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar h2 { margin-bottom: 30px; text-align: center; }
        .nav-links { list-style: none; padding: 0; }
        .nav-links li { margin-bottom: 15px; }
        .nav-links a { color: rgba(255,255,255,0.8); text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 5px; transition: 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: rgba(255,255,255,0.2); color: white; }
        .main-content { margin-left: 286px; padding: 30px; width: 100%; max-width: 800px; }
        
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .form-group textarea { height: 100px; resize: vertical; }
        
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; color: white; font-size: 16px; }
        .btn-primary { background: #1a6d38; }
        .btn-secondary { background: #6c757d; text-decoration: none; display: inline-block; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Sayul Admin</h2>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="packages.php" class="active"><i class="fas fa-box"></i> Packages</a></li>
            <li><a href="gallery.php"><i class="fas fa-images"></i> Gallery</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="card">
            <h2><?php echo $id ? 'Edit' : 'Add New'; ?> Package</h2>
            <?php if($message) echo "<p style='color:red'>$message</p>"; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Package Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Price ($)</label>
                        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($price); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Price Note (e.g. per day)</label>
                        <input type="text" name="price_note" value="<?php echo htmlspecialchars($price_note); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Duration (e.g. 5 Days / 4 Nights)</label>
                    <input type="text" name="duration" value="<?php echo htmlspecialchars($duration); ?>" required>
                </div>
                
                 <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Includes (One per line)</label>
                    <textarea name="includes" placeholder="Private Transport&#10;Driver&#10;Fuel"><?php echo htmlspecialchars($includes); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Package Image</label>
                    <?php if($image_path): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="../<?php echo htmlspecialchars($image_path); ?>" style="height: 100px; border-radius: 5px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" <?php echo $id ? '' : 'required'; ?>>
                </div>
                
                <div class="form-group">
                     <label>
                        <input type="checkbox" name="is_featured" <?php echo $is_featured ? 'checked' : ''; ?>>
                        Show on Homepage as Featured
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Package</button>
                <a href="packages.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
