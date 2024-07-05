(function ($) {
  "use strict";

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

  // Sticky Navbar
  $(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
      $(".sticky-top").addClass("shadow-sm").css("top", "0px");
    } else {
      $(".sticky-top").removeClass("shadow-sm").css("top", "-100px");
    }
  });

  // Back to top button
  $(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
      $(".back-to-top").fadeIn("slow");
    } else {
      $(".back-to-top").fadeOut("slow");
    }
  });
  $(".back-to-top").click(function () {
    $("html, body").animate({ scrollTop: 0 }, 1500, "easeInOutExpo");
    return false;
  });

  // Facts counter
  $('[data-toggle="counter-up"]').counterUp({
    delay: 10,
    time: 2000,
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

  // Testimonials carousel
  $(".testimonial-carousel").owlCarousel({
    autoplay: true,
    smartSpeed: 1000,
    loop: true,
    nav: false,
    dots: true,
    items: 1,
    dotsData: true,
  });

  // Portfolio isotope and filter
  var portfolioIsotope = $(".portfolio-container").isotope({
    itemSelector: ".portfolio-item",
    layoutMode: "fitRows",
  });
  $("#portfolio-flters li").on("click", function () {
    $("#portfolio-flters li").removeClass("active");
    $(this).addClass("active");

    portfolioIsotope.isotope({ filter: $(this).data("filter") });
  });
})(jQuery);

// Carrusel de menú proyectos (Bootstrap)

document.addEventListener("DOMContentLoaded", function () {
  // Selecciona todos los carruseles en la página
  document.querySelectorAll(".carousel").forEach((carousel) => {
    // Obtén la instancia del carrusel de Bootstrap
    const bootstrapCarousel = new bootstrap.Carousel(carousel);

    // Al posicionar el ratón sobre el carrusel, inicia el desplazamiento automático
    carousel.addEventListener("mouseenter", () => {
      bootstrapCarousel.cycle(); // Inicia el desplazamiento automático
    });

    // Al salir el ratón del carrusel, detiene el desplazamiento automático
    carousel.addEventListener("mouseleave", () => {
      bootstrapCarousel.pause(); // Detiene el desplazamiento automático
    });
  });
});

// Filtrado de elementos de proyectos

document.addEventListener("DOMContentLoaded", function () {
  // Selecciona los botones de filtro y los elementos
  const filterButtons = document.querySelectorAll(".botones-elementos button");
  const elements = document.querySelectorAll(".elemento");

  // Función para mostrar todos los elementos
  const showAllElements = () => {
    elements.forEach((element) => {
      element.classList.add("show");
    });
  };

  // Función para filtrar los elementos
  const filterElements = (category) => {
    elements.forEach((element) => {
      if (category === "todos" || element.dataset.elemento === category) {
        element.classList.add("show");
      } else {
        element.classList.remove("show");
      }
    });
  };

  // Agrega un evento click a cada botón de filtro
  filterButtons.forEach((button) => {
    button.addEventListener("click", function () {
      // Remueve la clase active de todos los botones
      filterButtons.forEach((btn) => btn.classList.remove("active"));
      // Agrega la clase active al botón clicado
      this.classList.add("active");

      // Obtén la categoría del botón clicado
      const category = this.classList.contains("todos")
        ? "todos"
        : this.classList[0];

      // Filtra los elementos basados en la categoría
      filterElements(category);
    });
  });

  // Muestra todos los elementos al cargar la página
  showAllElements();
});

// Código nuevo para el carrusel del menú proyectos

// Espera a que el documento esté completamente cargado
document.addEventListener("DOMContentLoaded", function () {
  // Selecciona todos los carruseles en la página
  document.querySelectorAll(".carousel").forEach((carousel) => {
    // Obtén la instancia del carrusel de Bootstrap
    const bootstrapCarousel = new bootstrap.Carousel(carousel);

    // Al posicionar el ratón sobre el carrusel, inicia el desplazamiento automático
    carousel.addEventListener("mouseenter", () => {
      bootstrapCarousel.cycle(); // Inicia el desplazamiento automático
    });

    // Al salir el ratón del carrusel, detiene el desplazamiento automático
    carousel.addEventListener("mouseleave", () => {
      bootstrapCarousel.pause(); // Detiene el desplazamiento automático
    });
  });
});

// Botones leeer mas...

document.addEventListener("DOMContentLoaded", function () {
  // Ocultar el texto adicional y establecer el texto del botón
  document.querySelectorAll(".more-text").forEach((moreText) => {
    moreText.style.display = "none";
    const button = moreText.previousElementSibling;
    button.innerHTML =
      '<i class="fa fa-arrow-right text-white me-3"></i>Leer más...';
  });

  // Agregar el evento click al botón para mostrar/ocultar el texto adicional
  document.querySelectorAll(".btn.leer-mas").forEach((button) => {
    button.addEventListener("click", function () {
      const moreText = this.nextElementSibling;
      if (moreText.style.display === "none" || moreText.style.display === "") {
        moreText.style.display = "block";
        this.innerHTML =
          '<i class="fa fa-arrow-up text-white me-3"></i>Leer menos...';
      } else {
        moreText.style.display = "none";
        this.innerHTML =
          '<i class="fa fa-arrow-right text-white me-3"></i>Leer más...';
      }
    });
  });
});

document
  .getElementById("contactForm")
  .addEventListener("submit", function (event) {
    event.preventDefault(); // Prevenir el envío tradicional del formulario

    // Obtener los datos del formulario
    const formData = new FormData(this);

    // Enviar los datos a FormSubmit.co
    fetch(this.action, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Mostrar mensaje de agradecimiento
          document.getElementById("thankYouMessage").style.display = "block";
          // Opcional: Redirigir a una página de gracias
          // window.location.href = "https://agasit.com/gracias.html";
        } else {
          // Mostrar mensaje de error
          document.getElementById("errorMessage").style.display = "block";
        }
      })
      .catch((error) => {
        console.error("Error al enviar el formulario:", error);
        alert(
          "Hubo un error al enviar tu mensaje. Por favor, inténtalo de nuevo."
        );
      });
  });
