(function ($) {
  "use strict";

  var STICKY_SCROLL_THRESHOLD = 300;

  // Spinner
  var spinner = function () {
    setTimeout(function () {
      if ($("#spinner").length > 0) {
        $("#spinner").removeClass("show");
      }
    }, 1);
  };
  spinner();

  // Initiate the wowjs
  new WOW().init();

  // Sticky navbar + back to top button (un solo listener de scroll)
  $(window).on("scroll", function () {
    var scrolled = $(this).scrollTop() > STICKY_SCROLL_THRESHOLD;
    $(".sticky-top").toggleClass("shadow-sm", scrolled).css("top", scrolled ? "0px" : "-100px");
    if (scrolled) {
      $(".back-to-top").fadeIn("slow");
    } else {
      $(".back-to-top").fadeOut("slow");
    }
  });

  // Delegado en document: el botón se inyecta de forma asíncrona
  // desde partials/footer.html (ver js/partials.js), así que aún no
  // existe en el DOM cuando este script se ejecuta.
  $(document).on("click", ".back-to-top", function () {
    $("html, body").animate({ scrollTop: 0 }, 1500, "easeInOutExpo");
    return false;
  });

  // Header carousel
  $(".header-carousel").owlCarousel({
    autoplay: true,
    smartSpeed: 1500,
    items: 1,
    dots: true,
    loop: true,
    nav: true,
    navText: [
      '<i class="bi bi-chevron-left"></i>',
      '<i class="bi bi-chevron-right"></i>',
    ],
  });
})(jQuery);

// Carrusel de menú de proyectos (Bootstrap): autoplay solo mientras el mouse está encima
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".carousel").forEach((carousel) => {
    const bootstrapCarousel = new bootstrap.Carousel(carousel);

    carousel.addEventListener("mouseenter", () => {
      bootstrapCarousel.cycle();
    });

    carousel.addEventListener("mouseleave", () => {
      bootstrapCarousel.pause();
    });
  });
});

// Filtrado de elementos de proyectos
document.addEventListener("DOMContentLoaded", function () {
  const filterButtons = document.querySelectorAll(".botones-elementos button");
  const elements = document.querySelectorAll(".elemento");

  const showAllElements = () => {
    elements.forEach((element) => {
      element.classList.add("show");
    });
  };

  const filterElements = (category) => {
    elements.forEach((element) => {
      if (category === "todos" || element.dataset.elemento === category) {
        element.classList.add("show");
      } else {
        element.classList.remove("show");
      }
    });
  };

  filterButtons.forEach((button) => {
    button.addEventListener("click", function () {
      filterButtons.forEach((btn) => btn.classList.remove("active"));
      this.classList.add("active");

      const category = this.classList.contains("todos")
        ? "todos"
        : this.classList[0];

      filterElements(category);
    });
  });

  showAllElements();
});

// Botones "Leer más..."
// El botón vive dentro de .service-item pero no depende de estar entre
// .service-text y .more-text: así el texto se muestra siempre junto y el
// botón no interrumpe la lectura.
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".service-item").forEach((item) => {
    const serviceText = item.querySelector(".service-text");
    const moreText = item.querySelector(".more-text");
    const button = item.querySelector(".btn.leer-mas");
    if (!serviceText || !moreText || !button) return;

    moreText.style.display = "none";
    button.innerHTML =
      '<i class="fa fa-arrow-right text-white me-3"></i>Leer más...';

    button.addEventListener("click", function () {
      const expandido = moreText.style.display === "block";

      if (!expandido) {
        serviceText.classList.add("service-text--expanded");
        moreText.style.display = "block";
        button.innerHTML =
          '<i class="fa fa-arrow-up text-white me-3"></i>Leer menos...';
      } else {
        serviceText.classList.remove("service-text--expanded");
        moreText.style.display = "none";
        button.innerHTML =
          '<i class="fa fa-arrow-right text-white me-3"></i>Leer más...';
      }
    });
  });
});

// Mensaje de estado del formulario de contacto (tras el redirect desde send_email.php)
document.addEventListener("DOMContentLoaded", function () {
  const params = new URLSearchParams(window.location.search);
  const estado = params.has("enviado")
    ? "exito"
    : params.has("limite")
    ? "limite"
    : params.has("error")
    ? "error"
    : null;

  if (!estado) return;

  const errorMessage = document.getElementById("errorMessage");
  const thankYouMessage = document.getElementById("thankYouMessage");
  if (!errorMessage || !thankYouMessage) return;

  const mensajesError = {
    limite: "Demasiados intentos. Por favor espera una hora antes de enviar otro mensaje.",
    error: "Hubo un error al enviar tu mensaje. Por favor, inténtalo de nuevo o escríbenos a agasitsas@gmail.com.",
  };

  const mensajeVisible = estado === "exito" ? thankYouMessage : errorMessage;
  if (estado !== "exito") {
    mensajeVisible.querySelector("p").textContent = mensajesError[estado];
  }
  mensajeVisible.style.display = "block";
  mensajeVisible.scrollIntoView({ behavior: "smooth", block: "center" });

  history.replaceState(null, "", window.location.pathname);
});
