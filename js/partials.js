(function () {
  "use strict";

  function currentPage() {
    var segments = window.location.pathname.split("/").filter(Boolean);
    var last = segments.pop() || "index";
    return last.replace(/\.html$/, "");
  }

  function setActiveLink() {
    var current = currentPage();
    document.querySelectorAll("#navbarCollapse .nav-link[data-page]").forEach(function (link) {
      var isActive = link.getAttribute("data-page") === current;
      link.classList.toggle("active", isActive);
      if (isActive) {
        link.setAttribute("aria-current", "page");
      } else {
        link.removeAttribute("aria-current");
      }
    });
  }

  function loadPartial(url, placeholderId, onLoaded) {
    var placeholder = document.getElementById(placeholderId);
    if (!placeholder) return;

    fetch(url)
      .then(function (response) {
        if (!response.ok) throw new Error("No se pudo cargar " + url);
        return response.text();
      })
      .then(function (html) {
        placeholder.outerHTML = html;
        if (onLoaded) onLoaded();
      })
      .catch(function (error) {
        console.error(error);
      });
  }

  loadPartial("partials/navbar.html", "navbar-placeholder", setActiveLink);
  loadPartial("partials/footer.html", "footer-placeholder");
})();
