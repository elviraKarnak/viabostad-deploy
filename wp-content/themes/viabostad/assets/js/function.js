jQuery(document).ready(function ($) {
  // get header height
  let headerHeight = jQuery("header").outerHeight();
  headerHeight = headerHeight;
  jQuery("body")
    .get(0)
    .style.setProperty("--headerHeight", headerHeight + "px");

  $(window).scroll(function () {
    var scroll = $(window).scrollTop();

    if (scroll >= 100) {
      $(".site_header").addClass("sticky");
    } else {
      $(".site_header").removeClass("sticky");
    }
  });

  $(".hemburger").click(function () {
    $(this).toggleClass("open");
    $("nav").slideToggle();
  });

  //  $(document).on("click", ".heart_icon", function() {
  //   $(this).toggleClass("active");
  // });
  $(".show_filter_option").click(function () {
    $(".filter_options_wrapper").slideToggle();
    var icon = $(this).find("i");
    if (icon.css("transform") === "none") {
      icon.css({
        transform: "rotate(90deg)",
        transition: "0.3s",
      });
    } else {
      icon.css("transform", "rotate(0deg)");
    }
  });

  $(document).on("click", ".filter-open-btn", function () {
    $(".listing_page_filter").addClass("active");
  });

  $(".cross").click(function () {
    $(".listing_page_filter").removeClass("active");
  });

  $(".listing_page_filter").on("click", function (e) {
    if ($(e.target).is(".listing_page_filter")) {
      $(this).removeClass("active");
    }
  });

  $(document).on("keydown", function (e) {
    if (e.key === "Escape") {
      $(".listing_page_filter").removeClass("active");
    }
  });
  
  $(".date_wrapper input").on("change", function () {
    var parent = $(this).closest(".date_wrapper");
    if ($(this).is(":checked")) {
      parent.find(".toggle").slideDown();
    } else {
      parent.find(".toggle").slideUp();
    }
  });

  let isOwlActive = false;

  function toggleOwl() {
    const $slider = $(".property-slider");

    if ($(window).width() < 768) {
      if (!isOwlActive) {
        $slider.addClass("owl-carousel owl-theme");
        $slider.owlCarousel({
          items: 1.2,
          loop: true,
          margin: 20,
          dots: false,
          nav: false,
          //   autoplay: true,
          //   autoplayTimeout: 10000,
          //   autoplaySpeed: 900,
          smartSpeed: 900,
          //   autoplayHoverPause: true,
        });
        isOwlActive = true;
      }
    } else {
      if (isOwlActive) {
        $slider.trigger("destroy.owl.carousel");
        $slider.removeClass("owl-carousel owl-theme");
        $slider.find(".owl-stage-outer").children().unwrap();
        $slider.removeAttr("style");
        isOwlActive = false;
      }
    }
  }

  toggleOwl();
});
