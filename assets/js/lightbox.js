document.addEventListener("DOMContentLoaded", function () {
  // Gallery data
  const galleryItems = document.querySelectorAll(".gallery-item");
  const lightboxModal = document.getElementById("lightboxModal");
  const lightboxImage = document.getElementById("lightboxImage");
  const closeLightbox = document.getElementById("closeLightbox");
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");
  const imageTitle = document.getElementById("imageTitle");
  const imageCounter = document.getElementById("imageCounter");

  let currentIndex = 0;
  let isZoomed = false;

  // Gallery data for lightbox
  const galleryData = [
    {
      src: "assets/img/gallery/anuradhapura.webp",
      title: "Ancient Ruins of Anuradhapura",
      desc: "Sri Lanka's ancient capital with well-preserved ruins",
    },
    {
      src: "assets/img/gallery/kandy.webp",
      title: "Comfortable Travel Van",
      desc: "Luxury transportation for your Sri Lankan journey",
    },
    {
      src: "assets/img/gallery/sigiriya.webp",
      title: "Sigiriya Rock Fortress",
      desc: "Ancient rock fortress with stunning panoramic views",
    },
    {
      src: "assets/img/gallery/weligama.webp",
      title: "Weligama Beach",
      desc: "Beautiful sandy beaches with crystal clear waters",
    },
    // {
    //   src: "https://images.unsplash.com/photo-1564507004663-b6dfb3e2ede5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1600&q=80",
    //   title: "Temple of the Sacred Tooth Relic",
    //   desc: "Kandy's most important Buddhist temple",
    // },
    // {
    //   src: "https://images.unsplash.com/photo-1573843989-c9d4a65d6c8c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1600&q=80",
    //   title: "Ella Rock Hike",
    //   desc: "Breathtaking views from one of Sri Lanka's best hikes",
    // },
    // {
    //   src: "https://images.unsplash.com/photo-1564507004663-b6dfb3e2ede5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1600&q=80",
    //   title: "Traditional Sri Lankan Cuisine",
    //   desc: "Experience the authentic flavors of Sri Lanka",
    // },
    // {
    //   src: "https://images.unsplash.com/photo-1552465011-b4e30bf7349d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1600&q=80",
    //   title: "Wildlife Safari",
    //   desc: "Encounter elephants and leopards in their natural habitat",
    // },
  ];

  // Open lightbox when gallery item is clicked
  galleryItems.forEach((item) => {
    item.addEventListener("click", function () {
      currentIndex = parseInt(this.getAttribute("data-index"));
      openLightbox(currentIndex);
    });
  });

  // Open lightbox with specific image
  function openLightbox(index) {
    currentIndex = index;
    updateLightboxImage();
    lightboxModal.style.display = "block";
    document.body.style.overflow = "hidden"; // Prevent scrolling
    isZoomed = false;
    lightboxImage.classList.remove("zoomed");
  }

  // Update lightbox image and info
  function updateLightboxImage() {
    const item = galleryData[currentIndex];
    lightboxImage.src = item.src;
    lightboxImage.alt = item.title;
    imageTitle.textContent = item.title;
    imageCounter.textContent = `${currentIndex + 1} / ${galleryData.length}`;
  }

  // Close lightbox
  closeLightbox.addEventListener("click", closeLightboxFunc);

  function closeLightboxFunc() {
    lightboxModal.style.display = "none";
    document.body.style.overflow = "auto"; // Re-enable scrolling
  }

  // Navigate to previous image
  prevBtn.addEventListener("click", function () {
    currentIndex = (currentIndex - 1 + galleryData.length) % galleryData.length;
    updateLightboxImage();
    resetZoom();
  });

  // Navigate to next image
  nextBtn.addEventListener("click", function () {
    currentIndex = (currentIndex + 1) % galleryData.length;
    updateLightboxImage();
    resetZoom();
  });

  // Zoom functionality
  lightboxImage.addEventListener("click", function () {
    isZoomed = !isZoomed;
    this.classList.toggle("zoomed", isZoomed);
  });

  function resetZoom() {
    isZoomed = false;
    lightboxImage.classList.remove("zoomed");
  }

  // Keyboard navigation
  document.addEventListener("keydown", function (e) {
    if (lightboxModal.style.display === "block") {
      switch (e.key) {
        case "Escape":
          closeLightboxFunc();
          break;
        case "ArrowLeft":
          prevBtn.click();
          break;
        case "ArrowRight":
          nextBtn.click();
          break;
        case " ":
          e.preventDefault();
          lightboxImage.click();
          break;
      }
    }
  });

  // Close lightbox when clicking outside the image
  lightboxModal.addEventListener("click", function (e) {
    if (e.target === lightboxModal) {
      closeLightboxFunc();
    }
  });

  // Touch swipe support for mobile
  let touchStartX = 0;
  let touchEndX = 0;

  lightboxModal.addEventListener("touchstart", function (e) {
    touchStartX = e.changedTouches[0].screenX;
  });

  lightboxModal.addEventListener("touchend", function (e) {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
  });

  function handleSwipe() {
    const swipeThreshold = 50;

    if (touchStartX - touchEndX > swipeThreshold) {
      // Swipe left - next image
      nextBtn.click();
    } else if (touchEndX - touchStartX > swipeThreshold) {
      // Swipe right - previous image
      prevBtn.click();
    }
  }
});
