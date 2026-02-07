<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
require_once '../db_connect.php';

$message = '';

// Handle Approval
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $stmt = $conn->prepare("UPDATE reviews SET is_approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Review approved and published.";
    }
    $stmt->close();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Review deleted.";
    }
    $stmt->close();
}

$result = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderate Reviews - Sayul Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; background: #f8f9fa; }
        .sidebar { width: 220px; background: #1a6d38; color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar h2 { margin-bottom: 30px; text-align: center; }
        .nav-links { list-style: none; padding: 0; }
        .nav-links li { margin-bottom: 15px; }
        .nav-links a { color: rgba(255,255,255,0.8); text-decoration: none; display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 50px; transition: 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: rgba(255,255,255,0.2); color: white; }
        .main-content { margin-left: 250px; padding: 30px; width: calc(100% - 250px); }
        
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .review-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        
        .btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; color: white; font-size: 14px; margin-left: 10px; }
        .btn-approve { background: #28a745; }
        .btn-delete { background: #dc3545; }
        
        .rating { color: #ffc107; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Sayul Admin</h2>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="packages.php"><i class="fas fa-box"></i> Packages</a></li>
            <li><a href="gallery.php"><i class="fas fa-images"></i> Gallery</a></li>
            <li><a href="reviews.php" class="active"><i class="fas fa-star"></i> Reviews</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <h1>Moderate Reviews</h1>
        
        <?php if($message): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="review-header">
                        <div>
                            <strong><?php echo htmlspecialchars($row['reviewer_name']); ?></strong> 
                            <span style="color: #666;">(<?php echo htmlspecialchars($row['reviewer_country']); ?>)</span>
                            <div class="rating">
                                <?php for($i=0; $i<$row['rating']; $i++) echo '<i class="fas fa-star"></i>'; ?>
                            </div>
                        </div>
                        <div>
                            <?php if(!$row['is_approved']): ?>
                                <span class="status-badge status-pending">Pending</span>
                                <a href="?approve=<?php echo $row['id']; ?>" class="btn btn-approve">Approve</a>
                            <?php else: ?>
                                <span class="status-badge status-approved">Approved</span>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this review?')">Delete</a>
                        </div>
                    </div>
                    <p>"<?php echo htmlspecialchars($row['review_text']); ?>"</p>
                    <small style="color: #999;"><?php echo $row['created_at']; ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No reviews submitted yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
