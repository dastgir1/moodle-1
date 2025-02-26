  const featurePosts = document.querySelectorAll("div.features-post");
  featurePosts.forEach(function (post) {
    const contentHide = post.querySelector("div.content-hide");
    post.addEventListener("mouseenter", function () {
      if (contentHide) contentHide.style.display = "block";
    });
    post.addEventListener("mouseleave", function () {
      if (contentHide) contentHide.style.display = "none";
    });
  });
  function availableCourses() {
  const courseSlider = document.querySelector(".course-slider");
  if (!courseSlider) return;

  // Initialize custom carousel logic
  const rtl = typeof RTL !== "undefined" ? RTL : false;
  const slidesToShow = 4;
  const slidesToScroll = 4;

  const responsiveSettings = [
    {
      breakpoint: 991,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 3,
      },
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2,
      },
    },
    {
      breakpoint: 575,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
      },
    },
  ];

  // Replace this with your custom carousel initialization logic if using a library.
  // No direct equivalent of slick in vanilla JS without a library.

  // Handle custom logic for data-crow
  const prow = parseInt(courseSlider.getAttribute("data-crow"), 10);
  if (prow < 2) {
    const pageNav = document.querySelector("#available-courses .pagenav");
    if (pageNav) {
      pageNav.style.display = "none";
    }
  }
}
 document.addEventListener("DOMContentLoaded", function() {
        var carousel = new bootstrap.Carousel(document.getElementById("carouselExampleControls"), {
            interval: 3000, // Auto-slide every 3 seconds
            wrap: true
        });
    });