<?php
require_once 'db_connect.php';

// Fetch All Packages
$all_sql = "SELECT * FROM packages ORDER BY created_at DESC";
$all_result = $conn->query($all_sql);
$total_packages = $all_result->num_rows;

// Handle Review Submission (If user submits from floating widget on this page)
$review_msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $name = $_POST['reviewer_name'];
    $country = $_POST['reviewer_country'];
    $text = $_POST['review_text'];
    $rating = $_POST['rating'];
    
    $stmt = $conn->prepare("INSERT INTO reviews (reviewer_name, reviewer_country, review_text, rating) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $country, $text, $rating);
    if ($stmt->execute()) {
        $review_msg = "Thank you! Your review has been submitted for moderation.";
    } else {
        $review_msg = "Error submitting review. Please try again.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Tour Packages - Sayul Ceylon Tours</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <style>
        .packages-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('assets/img/sections/tours.webp');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0 60px;
            text-align: center;
        }
        .packages-hero h1 { font-size: 3.5rem; margin-bottom: 20px; color: white; }
        .breadcrumb { background: rgba(255, 255, 255, 0.1); padding: 15px 30px; border-radius: 50px; display: inline-flex; align-items: center; gap: 10px; backdrop-filter: blur(10px); }
        .breadcrumb a { color: #fff; text-decoration: none; transition: all 0.3s ease; }
        .breadcrumb a:hover { color: #eefec3; }
        .packages-grid-all { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; margin-bottom: 50px; }
        
        /* Modal Styles (from index.php) */
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); backdrop-filter: blur(5px); align-items: center; justify-content: center; }
        .modal-content { background-color: #fff; padding: 40px; border-radius: 15px; width: 90%; max-width: 600px; position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.2); color: #333; }
        .close-modal { position: absolute; top: 15px; right: 20px; font-size: 30px; font-weight: bold; color: #aaa; cursor: pointer; }

        .floating-widgets { position: fixed; bottom: 30px; right: 30px; display: flex; flex-direction: column; gap: 15px; z-index: 1000; }
        .floating-icon { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; color: white; box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: all 0.3s; cursor: pointer; text-decoration: none; }
        .floating-icon:hover { transform: scale(1.1); }
        .floating-icon.whatsapp { background-color: #25d366; }
        .floating-icon.review-trigger { background-color: #ffc107; color: #333; }
    </style>
</head>
<body>
    <header>
      <div class="container header-container">
        <a href="index.php" class="logo">
          <img src="assets/img/logo-ai-tiny.png" alt="" />
          Sayul Ceylon
        </a>
        <div class="mobile-toggle">
          <iconify-icon icon="mdi:menu"></iconify-icon>
        </div>
        <ul class="nav-menu">
          <li><a href="index.php">Home</a></li>
          <li><a href="index.php#packages">Packages</a></li>
          <li><a href="gallery.php">Gallery</a></li>
          <li><a href="index.php#testimonials">Reviews</a></li>
          <li><a href="https://wa.me/94773324056" target="_blank" style="color: #25d366; font-weight: bold; display: flex; align-items: center; gap: 5px;">Book Now <iconify-icon icon="ic:baseline-whatsapp"></iconify-icon></a></li>
          <li><a href="index.php#contact">Contact</a></li>
        </ul>
      </div>
    </header>

    <section class="packages-hero">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php"><iconify-icon icon="mdi:home"></iconify-icon> Home</a>
                <iconify-icon icon="mdi:chevron-right"></iconify-icon>
                <span>All Packages</span>
            </div>
            <h1>Explore All Tour Packages</h1>
            <p>Discover our complete collection of Sri Lanka tour packages meticulously designed for your comfort and enjoyment.</p>
        </div>
    </section>

    <section class="section packages" id="all-packages">
        <div class="container">
            <?php if($review_msg): ?>
                <div style="background: #eefec3; color: #1a6d38; padding: 15px; border-radius: 8px; margin-bottom: 30px; text-align: center;">
                    <?php echo $review_msg; ?>
                </div>
            <?php endif; ?>

            <div style="text-align: center; margin-bottom: 30px; font-size: 1.1rem; color: #666;">
                Showing <span><?php echo $total_packages; ?></span> amazing tour packages
            </div>

            <div class="packages-grid-all">
                <?php if ($total_packages > 0): ?>
                    <?php while($row = $all_result->fetch_assoc()): ?>
                        <div class="package-card">
                            <div class="package-img">
                                <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" />
                            </div>
                            <div class="package-content">
                                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                                <div class="package-price">$<?php echo htmlspecialchars($row['price']); ?> <span style="font-size: 0.8rem; color: #666;"><?php echo htmlspecialchars($row['price_note']); ?></span></div>
                                <ul class="package-features">
                                    <li><?php echo htmlspecialchars($row['duration']); ?></li>
                                </ul>
                                <p style="font-size: 0.9rem; color: #777; margin: 10px 0;"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                                
                                <?php if ($row['includes']): ?>
                                  <div style="margin-top: 10px; font-size: 0.85rem; color: #555;">
                                      <strong>Includes:</strong> <?php echo str_replace("\n", ", ", htmlspecialchars($row['includes'])); ?>
                                  </div>
                                <?php endif; ?>
                                
                                <a href="https://wa.me/94773324056" class="btn" style="margin-top: 15px;">Book Now</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align:center; grid-column: 1/-1;">No packages available at the moment.</p>
                <?php endif; ?>
            </div>

            <div style="text-align: center;">
                <a href="index.php" style="display: inline-flex; align-items: center; gap: 10px; padding: 12px 25px; border: 2px solid #1a6d38; border-radius: 50px; text-decoration: none; color: #1a6d38; font-weight: 600;">
                    <iconify-icon icon="mdi:arrow-left"></iconify-icon> Back to Home
                </a>
            </div>
        </div>
    </section>

    <!-- Floating Widgets -->
    <div class="floating-widgets">
        <a href="https://wa.me/94773324056" class="floating-icon whatsapp" target="_blank" title="Chat on WhatsApp">
            <iconify-icon icon="ic:baseline-whatsapp"></iconify-icon>
        </a>
        <div class="floating-icon review-trigger" id="openReviewModal" title="Leave a Review">
            <iconify-icon icon="material-symbols:star"></iconify-icon>
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" id="closeReviewModal">&times;</span>
            <div class="review-form-container">
                <h3 style="text-align: center; margin-bottom: 20px;">Leave a Review</h3>
                <form action="packages.php" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <input type="text" name="reviewer_name" placeholder="Your Name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        <input type="text" name="reviewer_country" placeholder="Country" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <select name="rating" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 15px;">
                        <option value="5">5 Stars - Excellent</option>
                        <option value="4">4 Stars - Very Good</option>
                        <option value="3">3 Stars - Good</option>
                        <option value="2">2 Stars - Fair</option>
                        <option value="1">1 Star - Poor</option>
                    </select>
                    <textarea name="review_text" placeholder="Your Experience" required style="width: 100%; height: 100px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px; resize: vertical;"></textarea>
                    <button type="submit" name="submit_review" class="btn btn-primary" style="width: 100%;">Submit Review</button>
                </form>
            </div>
        </div>
    </div>

    <footer>
      <div class="container" style="text-align: center; padding: 40px 0; border-top: 1px solid #eee;">
        <p>&copy; 2026 Explore Sri Lanka - Sayul Ceylon Tours. All rights reserved.</p>
      </div>
    </footer>

    <script>
      // Mobile toggle
      document.querySelector(".mobile-toggle").addEventListener("click", function () {
          document.querySelector(".nav-menu").classList.toggle("active");
      });

      // Review Modal Toggle
      const reviewModal = document.getElementById("reviewModal");
      const openBtn = document.getElementById("openReviewModal");
      const closeBtn = document.getElementById("closeReviewModal");

      if(openBtn) {
          openBtn.onclick = function() {
              reviewModal.style.display = "flex";
          }
      }

      if(closeBtn) {
          closeBtn.onclick = function() {
              reviewModal.style.display = "none";
          }
      }

      window.onclick = function(event) {
          if (event.target == reviewModal) {
              reviewModal.style.display = "none";
          }
      }
    </script>
</body>
</html>
