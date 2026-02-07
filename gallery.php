<?php
require_once 'db_connect.php';

// Fetch gallery images from DB
$sql = "SELECT * FROM gallery_images ORDER BY created_at DESC";
$result = $conn->query($sql);

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
    <title>Travel Gallery - Sayul Ceylon Tours</title>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        .gallery-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('assets/img/sections/two.webp');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0 60px;
            text-align: center;
        }
        .gallery-hero h1 { font-size: 3.5rem; margin-bottom: 20px; color: white; }
        .breadcrumb { background: rgba(255, 255, 255, 0.1); padding: 15px 30px; border-radius: 50px; display: inline-flex; align-items: center; gap: 10px; backdrop-filter: blur(10px); }
        .breadcrumb a { color: #fff; text-decoration: none; transition: all 0.3s ease; }
        .breadcrumb a:hover { color: #eefec3; }
        .gallery-item { cursor: pointer; }
        
        /* Lightbox Modal */
        .lightbox-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 2000; align-items: center; justify-content: center; }
        .lightbox-content { position: relative; max-width: 90%; max-height: 90%; text-align: center; }
        .lightbox-img { max-height: 80vh; max-width: 100%; border-radius: 8px; box-shadow: 0 0 30px rgba(0,0,0,0.5); }
        .close-lightbox { position: absolute; top: -40px; right: 0; color: white; font-size: 35px; cursor: pointer; }
        .lightbox-nav-btn { background: rgba(255,255,255,0.1); border: none; color: white; padding: 20px; border-radius: 50%; cursor: pointer; position: absolute; top: 50%; transform: translateY(-50%); transition: 0.3s; display: flex; align-items: center; justify-content: center; }
        .lightbox-nav-btn:hover { background: rgba(255,255,255,0.3); }
        .prev-btn { left: -80px; }
        .next-btn { right: -80px; }
        
        @media (max-width: 768px) {
            .prev-btn { left: -10px; padding: 10px; background: rgba(0,0,0,0.5); }
            .next-btn { right: -10px; padding: 10px; background: rgba(0,0,0,0.5); }
            .gallery-hero h1 { font-size: 2.5rem; }
        }

        /* Review Modal Styles (from index.php) */
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
          <li><a href="index.php#contact">Contact</a></li>
        </ul>
      </div>
    </header>

    <section class="gallery-hero">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php"><iconify-icon icon="mdi:home"></iconify-icon> Home</a>
                <iconify-icon icon="mdi:chevron-right"></iconify-icon>
                <span>Gallery</span>
            </div>
            <h1>Our Travel Gallery</h1>
        </div>
    </section>

    <section class="section" id="gallery-page">
        <div class="container">
            <?php if($review_msg): ?>
                <div style="background: #eefec3; color: #1a6d38; padding: 15px; border-radius: 8px; margin-bottom: 30px; text-align: center;">
                    <?php echo $review_msg; ?>
                </div>
            <?php endif; ?>

            <div class="gallery-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php $i = 0; while($row = $result->fetch_assoc()): ?>
                        <div class="gallery-item" data-index="<?php echo $i; ?>">
                            <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['caption']); ?>">
                            <div class="gallery-overlay">
                                <h3><?php echo htmlspecialchars($row['caption']); ?></h3>
                                <p><?php echo ucfirst(htmlspecialchars($row['category'])); ?></p>
                            </div>
                            <div class="gallery-data" style="display:none;" 
                                 data-src="<?php echo htmlspecialchars($row['image_path']); ?>" 
                                 data-title="<?php echo htmlspecialchars($row['caption']); ?>">
                            </div>
                        </div>
                    <?php $i++; endwhile; ?>
                <?php else: ?>
                    <p style="text-align:center; grid-column: 1/-1;">No travel photos yet. Check back soon!</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Lightbox Modal -->
    <div class="lightbox-modal" id="lightboxModal">
        <div class="lightbox-content">
            <span class="close-lightbox" id="closeLightbox">&times;</span>
            <button class="lightbox-nav-btn prev-btn" id="prevBtn"><iconify-icon icon="mdi:chevron-left"></iconify-icon></button>
            <img class="lightbox-img" id="lightboxImage" src="" alt="Gallery Image">
            <button class="lightbox-nav-btn next-btn" id="nextBtn"><iconify-icon icon="mdi:chevron-right"></iconify-icon></button>
            <div style="color: white; margin-top: 15px;">
                <h3 id="imageTitle" style="margin-bottom: 5px;"></h3>
                <span id="imageCounter" style="color: #ccc; font-size: 0.9rem;"></span>
            </div>
        </div>
    </div>

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
                <form action="gallery.php" method="POST">
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
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile toggle
            const mobileToggle = document.querySelector(".mobile-toggle");
            const navMenu = document.querySelector(".nav-menu");
            if(mobileToggle) {
                mobileToggle.addEventListener("click", () => navMenu.classList.toggle("active"));
            }

            // Lightbox
            const galleryItems = document.querySelectorAll('.gallery-item');
            const lightboxModal = document.getElementById('lightboxModal');
            const lightboxImage = document.getElementById('lightboxImage');
            const imageTitle = document.getElementById('imageTitle');
            const imageCounter = document.getElementById('imageCounter');
            const closeLightbox = document.getElementById('closeLightbox');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            
            let currentIndex = 0;
            const galleryData = [];
            document.querySelectorAll('.gallery-data').forEach((el) => {
                galleryData.push({ src: el.getAttribute('data-src'), title: el.getAttribute('data-title') });
            });

            function updateLightbox(index) {
                if(galleryData.length === 0) return;
                lightboxImage.src = galleryData[index].src;
                imageTitle.innerText = galleryData[index].title;
                imageCounter.innerText = (index + 1) + " / " + galleryData.length;
            }

            galleryItems.forEach(item => {
                item.addEventListener('click', function() {
                    currentIndex = parseInt(this.getAttribute('data-index'));
                    updateLightbox(currentIndex);
                    lightboxModal.style.display = 'flex';
                });
            });

            closeLightbox.onclick = () => lightboxModal.style.display = 'none';
            prevBtn.onclick = (e) => { e.stopPropagation(); currentIndex = (currentIndex > 0) ? currentIndex - 1 : galleryData.length - 1; updateLightbox(currentIndex); };
            nextBtn.onclick = (e) => { e.stopPropagation(); currentIndex = (currentIndex < galleryData.length - 1) ? currentIndex + 1 : 0; updateLightbox(currentIndex); };
            lightboxModal.onclick = (e) => { if(e.target === lightboxModal) lightboxModal.style.display = 'none'; };

            // Review Modal
            const reviewModal = document.getElementById("reviewModal");
            const openReviewBtn = document.getElementById("openReviewModal");
            const closeReviewBtn = document.getElementById("closeReviewModal");
            if(openReviewBtn) openReviewBtn.onclick = () => reviewModal.style.display = "flex";
            if(closeReviewBtn) closeReviewBtn.onclick = () => reviewModal.style.display = "none";
            window.onclick = (event) => { if (event.target == reviewModal) reviewModal.style.display = "none"; }
        });
    </script>
</body>
</html>
