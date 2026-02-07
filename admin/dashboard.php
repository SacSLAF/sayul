<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
require_once '../db_connect.php';

// Fetch pending reviews count
$pending_reviews_result = $conn->query("SELECT COUNT(*) as count FROM reviews WHERE is_approved = 0");
$pending_count = $pending_reviews_result->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sayul Ceylon</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background: #f8f9fa; }
        .sidebar { width: 220px; background: #1a6d38; color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar h2 { margin-bottom: 30px; text-align: center; }
        .nav-links { list-style: none; padding: 0; }
        .nav-links li { margin-bottom: 15px; }
        .nav-links a { color: rgba(255,255,255,0.8); text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 5px; transition: 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: rgba(255,255,255,0.2); color: white; }
        .main-content { margin-left: 250px; padding: 30px; width: calc(100% - 250px); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center; position: relative; }
        .card i { font-size: 30px; color: #1a6d38; margin-bottom: 10px; }
        .card h3 { margin: 0; font-size: 24px; }
        .card p { color: #666; }
        .badge { position: absolute; top: 10px; right: 10px; background: #dc3545; color: white; padding: 5px 10px; border-radius: 50%; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Sayul Admin</h2>
        <ul class="nav-links">
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="packages.php"><i class="fas fa-box"></i> Packages</a></li>
            <li><a href="gallery.php"><i class="fas fa-images"></i> Gallery</a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h1>
        </div>
        <div class="card-grid">
            <div class="card">
                <i class="fas fa-box"></i>
                <h3>Manage Packages</h3>
                <p>Add, edit or remove tour packages</p>
                <a href="packages.php" style="display:block; margin-top:10px; color:#1a6d38;">Go to Packages &rarr;</a>
            </div>
            <div class="card">
                <i class="fas fa-images"></i>
                <h3>Manage Gallery</h3>
                <p>Upload new photos</p>
                <a href="gallery.php" style="display:block; margin-top:10px; color:#1a6d38;">Go to Gallery &rarr;</a>
            </div>
            <div class="card">
                <?php if($pending_count > 0): ?>
                    <span class="badge"><?php echo $pending_count; ?></span>
                <?php endif; ?>
                <i class="fas fa-star"></i>
                <h3>Manage Reviews</h3>
                <p>Approve new feedback</p>
                <a href="reviews.php" style="display:block; margin-top:10px; color:#1a6d38;">Go to Reviews &rarr;</a>
            </div>
        </div>
    </div>
</body>
</html>
