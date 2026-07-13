# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Corporate website for **AGA Soluciones de Ingeniería y Tecnología S.A.S.**, a Colombian electronic security company. Multi-page static site (HTML5/CSS/JS) with a PHP backend for contact form email delivery.

## Tech Stack

- **Frontend**: HTML5, Bootstrap 5, jQuery, Owl Carousel, WOW.js + Animate.css
- **Backend**: PHP with PHPMailer (SMTP via mail.agasit.com)
- **Deployment**: cPanel rsync via [.cpanel.yml](.cpanel.yml)

There is no build step — no npm, no bundler, no transpilation. Files are served as-is.

## Local Development

Use VS Code Live Server or any static file server. Navigation links use `.html` extensions, so they work directly in Live Server without any server-side configuration.

```bash
python -m http.server 8000
```

The contact form (`contacto.html` → `send_email.php`) requires a PHP server to function. For that, use XAMPP or Laragon locally.

## Deployment

Defined in [.cpanel.yml](.cpanel.yml). cPanel auto-deploys on push to `main`:

```
rsync -avz css/ img/ js/ lib/ partials/ *.html $DEPLOYPATH/
```

Target server path: `/home/agasitco/public_html`

**Before first deploy on a new server**, create the credentials file outside the document root:

```
/home/agasitco/app_config.php   ← copy from app_config.example.php and fill credentials
```

This file must never be inside `public_html` and is already in `.gitignore`.

## Architecture

### Page Structure

All pages share the same layout: Bootstrap navbar → page-specific content → footer. The five HTML pages are:

- [index.html](index.html) — Homepage (hero carousel, company intro)
- [acerca-de.html](acerca-de.html) — About Us (mission, vision, values)
- [servicios.html](servicios.html) — Services grid with "Leer más" expand buttons
- [proyectos.html](proyectos.html) — Portfolio with filterable Bootstrap carousel galleries
- [contacto.html](contacto.html) — Contact form

### Shared Layout ([partials/](partials/))

The navbar and footer markup lives once in [partials/navbar.html](partials/navbar.html) and
[partials/footer.html](partials/footer.html), not duplicated in each HTML page. Each page has
an empty placeholder instead:

```html
<div id="navbar-placeholder"></div>
...
<div id="footer-placeholder"></div>
```

[js/partials.js](js/partials.js) `fetch()`es both files at page load and replaces each
placeholder's `outerHTML` with the response, then sets the active nav link by comparing
`data-page` attributes on the nav links against the current URL. This runs with zero build
step (no templating, no PHP include) and works identically under Live Server and Apache, which
is why this approach was chosen over PHP includes — converting pages to `.php` would require
running a PHP server for every page in local dev, not just `send_email.php`.

Trade-offs to keep in mind when touching this:
- There's a brief flash before the navbar/footer render (no server-side content), and the
  markup won't render at all if JavaScript is disabled — acceptable for this site, but worth
  remembering if SEO/no-JS crawling ever becomes a concern.
- WOW.js is initialized with its default `live: true` option, which uses a `MutationObserver`
  to pick up the footer's `.wow` element even though it's injected after `new WOW().init()`
  runs — don't disable `live` without accounting for this.
- The back-to-top button listener in `js/main.js` is bound via event delegation
  (`$(document).on("click", ".back-to-top", ...)`) rather than a direct binding, because the
  button doesn't exist in the DOM yet when `main.js` executes.
- To edit the navbar or footer, edit the partial — don't add the markup back into individual
  HTML pages.

### URL Routing (dual-environment strategy)

Internal nav links use `.html` extensions (e.g., `href="servicios.html"`):
- **Live Server / local**: files are served directly by filename — works with no configuration.
- **Apache / cPanel**: [.htaccess](.htaccess) contains two rewrite rules that together create clean public URLs:
  1. Redirect `/pagina.html` → `/pagina` (301, canonical)
  2. Serve `/pagina` from `pagina.html`

Never change nav links back to bare names (without `.html`) — that would break local development.

### Consistency Checks

[scripts/check-consistency.sh](scripts/check-consistency.sh) checks for broken internal
`.html` links, broken `img/` references (HTML `src` and CSS `url()`), orphaned files in
`img/`, and mismatched `?v=` cache-busting query strings between pages. No dependencies
beyond bash/grep/find. Run it after moving or renaming anything under `img/`, or after
adding a new page:

```bash
./scripts/check-consistency.sh
```

### Contact Form Flow

`contacto.html` → POST → [send_email.php](send_email.php) → PHPMailer → SMTP.

`send_email.php` loads credentials from `dirname(__DIR__) . '/app_config.php'` (one level above `public_html`). It also applies:
- `filter_input()` validation on all fields
- Session-based rate limiting (max 3 submissions per hour)
- HTTP security headers (`X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`)

### JavaScript

[js/partials.js](js/partials.js) loads the shared navbar/footer (see [Shared Layout](#shared-layout-partials) above) and must run before [js/main.js](js/main.js) — it has no jQuery dependency, so its `<script>` tag is placed right after jQuery/Bootstrap and before the WOW/easing/Owl Carousel tags in every page.

[js/main.js](js/main.js) (jQuery) handles: page load spinner, sticky navbar, scroll-to-top, WOW.js init, Owl Carousel (hero), portfolio filter, Bootstrap Carousel hover play/pause, "Leer más" expand toggle.

All `<script>` tags use `defer` for parallel download with ordered execution.

### CSS ([css/style.css](css/style.css))

Custom styles only — Bootstrap is untouched in `css/bootstrap.min.css`. The file is organized into named sections:

1. Variables globales (CSS custom properties: `--color-primario`, `--color-whatsapp`, etc.)
2. Tipografía y utilidades
3. Spinner de carga
4. Botones
5. Botón flotante WhatsApp
6. Botón volver arriba
7. Navbar
8. Carrusel principal (Owl)
9. Cabeceras de página interior
10. Tarjetas de servicio / Proyectos / Contacto / Footer / Galería de marcas

Always add new custom rules here, never modify `css/bootstrap.min.css`.

## Conventions

- All content and `lang` attribute are in **Spanish** (`lang="es"`)
- Images in [img/](img/), grouped into thematic subfolders: `carousel/` (hero slides), `servicios/` (service card photos), `proyectos/` (portfolio gallery), `marcas/` (partner/brand logos), `nosotros/` (About Us photos), `paginas/` (page-header backgrounds), `icon/` (nav logo, favicon, service icons). Run [scripts/check-consistency.sh](scripts/check-consistency.sh) after adding or moving images to catch broken references or orphans.
- Third-party libraries are vendored in [lib/](lib/) — do not add CDN links for new libraries; see [lib/VERSIONS.md](lib/VERSIONS.md) for which version of each is in use
- Navbar/footer markup lives in [partials/](partials/), not in each HTML page — see [Shared Layout](#shared-layout-partials)
- Font Awesome 6.5 is the only icon library for FA icons; Bootstrap Icons (`bi-*`) are used for UI controls (arrows, chevrons)
- `app_config.example.php` is the template for server credentials — fill it in and place it at `/home/agasitco/app_config.php` on the server
