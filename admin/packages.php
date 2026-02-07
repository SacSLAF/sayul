<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
require_once '../db_connect.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get image path to delete file
    $stmt = $conn->prepare("SELECT image_path FROM packages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imagePath);
    $stmt->fetch();
    $stmt->close();
    
    if ($imagePath && file_exists("../" . $imagePath)) {
        unlink("../" . $imagePath);
    }
    
    $delStmt = $conn->prepare("DELETE FROM packages WHERE id = ?");
    $delStmt->bind_param("i", $id);
    if ($delStmt->execute()) {
        $message = "Package deleted successfully.";
    }
    $delStmt->close();
}

$result = $conn->query("SELECT * FROM packages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages - Sayul Admin</title>
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
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn { padding: 10px 20px; text-decoration: none; border-radius: 5px; color: white; background: #1a6d38; }
        .btn-danger { background: #dc3545; padding: 5px 10px; font-size: 14px; }
        .btn-edit { background: #ffc107; color: #000; padding: 5px 10px; font-size: 14px; margin-right: 5px; }
        
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
            <li><a href="packages.php" class="active"><i class="fas fa-box"></i> Packages</a></li>
            <li><a href="gallery.php"><i class="fas fa-images"></i> Gallery</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Manage Packages</h1>
            <a href="package_form.php" class="btn"><i class="fas fa-plus"></i> Add New Package</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Duration</th>
                    <th>Featured</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><img src="../<?php echo htmlspecialchars($row['image_path']); ?>" class="thumb" alt="Thumb"></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td>$<?php echo htmlspecialchars($row['price']); ?> <?php echo htmlspecialchars($row['price_note']); ?></td>
                            <td><?php echo htmlspecialchars($row['duration']); ?></td>
                            <td><?php echo $row['is_featured'] ? 'Yes' : 'No'; ?></td>
                            <td>
                                <a href="package_form.php?id=<?php echo $row['id']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">No packages found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
