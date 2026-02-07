<?php
require_once 'db_connect.php';

// Handle Review Submission
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

// Fetch Featured Packages
$featured_sql = "SELECT * FROM packages WHERE is_featured = 1 LIMIT 6";
$featured_result = $conn->query($featured_sql);

// Fetch Approved Reviews
$reviews_sql = "SELECT * FROM reviews WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 10";
$reviews_result = $conn->query($reviews_sql);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sri Lanka Private Tours & Chauffeur Services | Sayul Ceylon</title>
    <meta name="description" content="Explore Sri Lanka in comfort with our private van hire and chauffeur services. Custom tour planning, airport transfers, and multi-day tours." />
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="stylesheet" href="style.css" />
    <style>
        .destination-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            height: 250px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: transform 0.3s;
        }
        .destination-card:hover { transform: scale(1.03); }
        .destination-card img { width: 100%; height: 100%; object-fit: cover; }
        .destination-overlay {
            position: absolute; bottom: 0; left: 0; width: 100%;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            color: white; padding: 20px;
        }
        .faq-item {
            background: #fff;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-radius: 8px;
            overflow: hidden;
        }
        .faq-question {
            padding: 15px 20px;
            cursor: pointer;
            font-weight: 600;
            background: #f9f9f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, padding 0.3s;
        }
        .faq-item.active .faq-answer {
            padding: 15px 20px;
            max-height: 200px;
        }
        .faq-item.active .faq-question {
            background: #eefec3;
            color: #1a6d38;
        }
    </style>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "TravelAgency",
      "name": "Sayul Ceylon Tours",
      "image": "https://sayulceylontours.com/assets/img/logo-ai-tiny.png",
      "@id": "https://sayulceylontours.com",
      "url": "https://sayulceylontours.com",
      "telephone": "+94773324056",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Colombo",
        "addressLocality": "Colombo",
        "addressCountry": "LK"
      },
      "priceRange": "$$",
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": [
          "Monday",
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday",
          "Saturday",
          "Sunday"
        ],
        "opens": "00:00",
        "closes": "23:59"
      }
    }
    </script>
  </head>
  <body>
    <!-- Header & Navigation -->
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
          <li><a href="#home">Home</a></li>
          <li><a href="#packages">Packages</a></li>
          <li><a href="#about">About Us</a></li>
          <li><a href="gallery.php">Gallery</a></li>
          <li><a href="#testimonials">Testimonials</a></li>
          <li><a href="https://wa.me/94773324056" target="_blank" style="color: #25d366; font-weight: bold; display: flex; align-items: center; gap: 5px;">Book Now <iconify-icon icon="ic:baseline-whatsapp"></iconify-icon></a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </div>
    </header>

    <section class="hero" id="home">
      <div class="video-background">
        <video autoplay muted loop playsinline>
          <source src="assets/video/background.mp4" type="video/mp4" />
          Your browser does not support the video tag.
        </video>
      </div>
      <div class="video-overlay"></div>
      <div class="container hero-content">
        <h1>Sri Lanka Private Tours & Chauffeur Adventures</h1>
        <p>Complete Tour Planning + Private Van Hire with Professional Driver</p>
        <div class="hero-buttons">
          <a href="#packages" class="btn btn-primary">View Packages</a>
          <a
            href="https://wa.me/94773324056"
            class="btn btn-whatsapp"
            target="_blank"
            >Book Now</a
          >
        </div>
      </div>
    </section>

    <!-- Why Choose Us / Trust Signals -->
    <section class="section why-choose-us" id="why-choose-us" style="background: #fdfdfd; padding-bottom: 20px;">
        <div class="container">
            <div class="section-title">
                <h2>Why Travel With Us?</h2>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-certificate"></i>
                    <h3>Certified Drivers</h3>
                    <p>Experienced & licensed chauffeur guides.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-car-side"></i>
                    <h3>Comfort & Safety</h3>
                    <p>Modern, AC vans maintained to high standards.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-clipboard-check"></i>
                    <h3>Custom Itineraries</h3>
                    <p>We plan the perfect trip around your interests.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-clock"></i>
                    <h3>24/7 Support</h3>
                    <p>We are always available to assist you.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Packages Section -->
    <section class="section packages" id="packages">
      <div class="container">
        <div class="section-title">
          <h2>Featured Packages</h2>
        </div>

        <div class="packages-grid">
          <?php if ($featured_result->num_rows > 0): ?>
            <?php while($row = $featured_result->fetch_assoc()): ?>
              <div class="package-card">
                <div class="package-img">
                  <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" />
                </div>
                <div class="package-content">
                  <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                  <div class="package-price">$<?php echo htmlspecialchars($row['price']); ?> <span style="font-size: 0.8rem; color: #666; font-weight: normal;"><?php echo htmlspecialchars($row['price_note']); ?></span></div>
                  <p style="font-size: 0.9rem; color: #666; margin: 10px 0;"><?php echo htmlspecialchars($row['duration']); ?></p>
                  
                  <?php if ($row['includes']): ?>
                    <div class="package-includes" style="margin-top: 10px; font-size: 0.85rem; color: #555;">
                        <strong>Includes:</strong> <?php echo str_replace("\n", ", ", htmlspecialchars($row['includes'])); ?>
                    </div>
                  <?php endif; ?>
                  
                  <a href="https://wa.me/94773324056" class="btn" style="margin-top: 15px;">Book Now</a>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p style="text-align:center; grid-column: 1/-1;">Check back soon for our new featured packages!</p>
          <?php endif; ?>
        </div>

        <div class="view-all-container" style="text-align: center; margin-top: 40px;">
          <a href="packages.php" class="btn btn-view-all">
            <i class="fas fa-eye"></i> View All Packages
          </a>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section class="section about" id="about">
      <div class="container">
        <div class="about-content">
          <div class="about-text">
            <h2>About Our Service</h2>
            <p>
              With over 10 years of experience, we provide reliable and
              comfortable private van hire services across Sri Lanka. Our
              professional drivers are knowledgeable about local attractions and
              committed to your safety and satisfaction.
            </p>
            <p>
              We take pride in offering personalized service that allows you to
              explore the beauty of Sri Lanka at your own pace, creating
              unforgettable travel experiences.
            </p>

            <div class="about-features">
              <div class="feature">
                <i class="fas fa-shield-alt"></i>
                <div>
                  <h4>Safety First</h4>
                  <p>Well-maintained vehicles and experienced drivers</p>
                </div>
              </div>
              <div class="feature">
                <i class="fas fa-map-marked-alt"></i>
                <div>
                  <h4>Local Knowledge</h4>
                  <p>Expert guidance to hidden gems and popular spots</p>
                </div>
              </div>
              <div class="feature">
                <i class="fas fa-clock"></i>
                <div>
                  <h4>Punctual Service</h4>
                  <p>We value your time with prompt pickups</p>
                </div>
              </div>
              <div class="feature">
                <i class="fas fa-smile"></i>
                <div>
                  <h4>Friendly Drivers</h4>
                  <p>Courteous and professional service guaranteed</p>
                </div>
              </div>
            </div>
          </div>

          <div class="about-image">
            <img
              src="assets/img/sections/two.webp"
              alt="Our Van and Driver"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- Dynamic Gallery Section -->
    <section class="section" id="gallery">
      <div class="container">
          <div class="section-title">
              <h2>Travel Gallery</h2>
          </div>

          <div class="gallery-grid">
              <?php
              $gallery_sql = "SELECT * FROM gallery_images ORDER BY created_at DESC LIMIT 6";
              $gallery_result = $conn->query($gallery_sql);
              if ($gallery_result && $gallery_result->num_rows > 0):
                  $i = 0;
                  while($row = $gallery_result->fetch_assoc()): ?>
                      <div class="gallery-item" data-index="<?php echo $i; ?>">
                          <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['caption']); ?>" />
                          <div class="gallery-overlay">
                              <h3><?php echo htmlspecialchars($row['caption']); ?></h3>
                              <p><?php echo ucfirst(htmlspecialchars($row['category'])); ?></p>
                          </div>
                      </div>
                  <?php $i++; endwhile; ?>
              <?php else: ?>
                  <p style="text-align:center; grid-column: 1/-1;">Visit our <a href="gallery.php">Gallery Page</a> to see more photos.</p>
              <?php endif; ?>
          </div>
          
          <div style="text-align: center; margin-top: 30px;">
              <a href="gallery.php" class="btn btn-primary">View Full Gallery</a>
          </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section testimonials" id="testimonials">
      <div class="container">
        <div class="section-title">
          <h2>Customer Reviews</h2>
          <?php if($review_msg): ?>
            <div style="background: #eefec3; color: #1a6d38; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <?php echo $review_msg; ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="testimonials-slider">
          <div class="testimonials-track">
            <?php if ($reviews_result && $reviews_result->num_rows > 0): ?>
                <?php while($row = $reviews_result->fetch_assoc()): ?>
                    <div class="testimonial-slide">
                      <div class="testimonial-card">
                        <div class="rating" style="color: #ffc107; margin-bottom: 10px; display: flex; gap: 2px;">
                            <?php for($i=0; $i<$row['rating']; $i++) echo '<iconify-icon icon="material-symbols:star"></iconify-icon>'; ?>
                        </div>
                        <div class="testimonial-text">
                          "<?php echo htmlspecialchars($row['review_text']); ?>"
                        </div>
                        <div class="testimonial-author">
                          <div class="author-info">
                            <h4><?php echo htmlspecialchars($row['reviewer_name']); ?></h4>
                            <p><?php echo htmlspecialchars($row['reviewer_country']); ?></p>
                          </div>
                        </div>
                      </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Fallback static content if no approved reviews -->
                <div class="testimonial-slide">
                  <div class="testimonial-card">
                    <div class="testimonial-text">
                      "Our driver was incredibly knowledgeable and took us to some hidden gems we would have never found on our own. The van was comfortable and clean. Highly recommend!"
                    </div>
                    <div class="testimonial-author">
                      <div class="author-info">
                        <h4>Sarah Johnson</h4>
                        <p>Australia</p>
                      </div>
                    </div>
                  </div>
                </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- (Review Form moved to Modal) -->
      </div>
    </section>

    <!-- Verified Reviews Trust Section -->
    <section class="section trust-badges" style="background: #fff; padding: 40px 0; text-align: center;">
        <div class="container">
            <h3>Trusted by Travelers Worldwide</h3>
            <div class="badges-container" style="display: flex; justify-content: center; gap: 30px; margin-top: 20px; flex-wrap: wrap;">
                <div class="trust-badge">
                    <iconify-icon icon="simple-icons:tripadvisor" style="font-size: 40px; color: #00af87;"></iconify-icon>
                    <p><strong>TripAdvisor</strong><br>Excellent Reviews</p>
                </div>
                <div class="trust-badge">
                    <iconify-icon icon="logos:google-icon" style="font-size: 40px;"></iconify-icon>
                    <p><strong>Google Reviews</strong><br>5-Star Rated</p>
                </div>
                <div class="trust-badge">
                    <iconify-icon icon="mdi:shield-check" style="font-size: 40px; color: #0056b3;"></iconify-icon>
                    <p><strong>Safe & Secure</strong><br>Certified Operator</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section contact" id="contact">
      <div class="container">
          <div class="contact-content">
              <div class="contact-info">
                  <h3>Get In Touch</h3>
                  <p>
                      Ready to explore Sri Lanka? Contact us to book your private van
                      hire or ask any questions about our services. We're here to help you plan your perfect Sri Lankan adventure.
                  </p>
                  <a
                      href="https://wa.me/94773324056"
                      class="whatsapp-contact"
                      target="_blank"
                      style="font-size: 1.5rem; padding: 1.5rem 3rem; background: #25d366; color: white; border-radius: 50px; text-decoration: none; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 5px 15px rgba(37, 211, 102, 0.4); animation: pulse 2s infinite;"
                  >
                      <iconify-icon icon="ic:baseline-whatsapp" style="font-size: 2.5rem;"></iconify-icon> 
                      <strong>Book Now on WhatsApp</strong>
                  </a>
                  
                  <style>
                    @keyframes pulse {
                        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); }
                        70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(37, 211, 102, 0); }
                        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
                    }
                  </style>

                  <div class="social-media" style="margin-top: 30px;">
                      <a href="#" class="social-icon facebook"><iconify-icon icon="brandico:facebook"></iconify-icon></a>
                      <a href="https://www.instagram.com/sayulceylontours?utm_source=qr&igsh=aWt5djdtcjVjZnlq" class="social-icon instagram"><iconify-icon icon="skill-icons:instagram"></iconify-icon></a>
                      <a href="https://share.google/1vMwjTNmRKnLtQp6O" class="social-icon google"><iconify-icon icon="logos:google-icon"></iconify-icon></a>
                  </div>
              </div>

              <div class="map-container">
                  <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126743.58638743578!2d79.78616430488758!3d6.921831294874699!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae253d10f7a7003%3A0x320b2e4d32d3838d!2sColombo%2C%20Sri%20Lanka!5e0!3m2!1sen!2sus!4v1689876543210!5m2!1sen!2sus" allowfullscreen="" loading="lazy"></iframe>
              </div>
          </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="section faq" id="faq" style="background: white;">
        <div class="container" style="max-width: 800px;">
            <div class="section-title">
                <h2>Frequently Asked Questions</h2>
            </div>
            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question">What is included in the tour price? <iconify-icon icon="mdi:plus"></iconify-icon></div>
                    <div class="faq-answer"><p>Our standard packages typically include the private vehicle (AC van/car), professional English-speaking driver, fuel, parking fees, and driver's accommodation/meals.</p></div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">Can I customize my itinerary? <iconify-icon icon="mdi:plus"></iconify-icon></div>
                    <div class="faq-answer"><p>Absolutely! All our tours are fully customizable. You can adjust the itinerary, add/remove locations, or change the duration to suit your preferences.</p></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
      <div class="container">
        <div class="copyright">
          <p>&copy; 2026 Explore Sri Lanka - Sayul Ceylon Tours. All rights reserved.</p>
        </div>
      </div>
    </footer>

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
                <form action="index.php" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 5px;">Your Name</label>
                            <input type="text" name="reviewer_name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 5px;">Country</label>
                            <input type="text" name="reviewer_country" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">Rating</label>
                        <select name="rating" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="5">5 Stars - Excellent</option>
                            <option value="4">4 Stars - Very Good</option>
                            <option value="3">3 Stars - Good</option>
                            <option value="2">2 Stars - Fair</option>
                            <option value="1">1 Star - Poor</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px;">Your Experience</label>
                        <textarea name="review_text" required style="width: 100%; height: 100px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; resize: vertical;"></textarea>
                    </div>
                    <button type="submit" name="submit_review" class="btn btn-primary" style="width: 100%;">Submit Review</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .floating-widgets {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 1000;
        }
        .floating-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
        }
        .floating-icon:hover { transform: scale(1.1); }
        .floating-icon.whatsapp { background-color: #25d366; }
        .floating-icon.review-trigger { background-color: #ffc107; color: #333; }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #fff;
            padding: 40px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .close-modal {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 30px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }
        .close-modal:hover { color: #333; }
    </style>

    <script>
      // FAQ Toggle
      document.querySelectorAll('.faq-question').forEach(item => {
        item.addEventListener('click', event => {
            const parent = item.parentNode;
            parent.classList.toggle('active');
        });
      });
      // Mobile Navigation Toggle
      document.querySelector(".mobile-toggle").addEventListener("click", function () {
          document.querySelector(".nav-menu").classList.toggle("active");
      });

      // Review Modal Toggle
      const reviewModal = document.getElementById("reviewModal");
      const openBtn = document.getElementById("openReviewModal");
      const closeBtn = document.getElementById("closeReviewModal");

      openBtn.onclick = function() {
          reviewModal.style.display = "flex";
      }

      closeBtn.onclick = function() {
          reviewModal.style.display = "none";
      }

      window.onclick = function(event) {
          if (event.target == reviewModal) {
              reviewModal.style.display = "none";
          }
      }
    </script>
  </body>
</html>
