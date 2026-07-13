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
rsync -avz css/ img/ js/ lib/ scss/ *.html $DEPLOYPATH/
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

### URL Routing (dual-environment strategy)

Internal nav links use `.html` extensions (e.g., `href="servicios.html"`):
- **Live Server / local**: files are served directly by filename — works with no configuration.
- **Apache / cPanel**: [.htaccess](.htaccess) contains two rewrite rules that together create clean public URLs:
  1. Redirect `/pagina.html` → `/pagina` (301, canonical)
  2. Serve `/pagina` from `pagina.html`

Never change nav links back to bare names (without `.html`) — that would break local development.

### Contact Form Flow

`contacto.html` → POST → [send_email.php](send_email.php) → PHPMailer → SMTP.

`send_email.php` loads credentials from `dirname(__DIR__) . '/app_config.php'` (one level above `public_html`). It also applies:
- `filter_input()` validation on all fields
- Session-based rate limiting (max 3 submissions per hour)
- HTTP security headers (`X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`)

### JavaScript ([js/main.js](js/main.js))

jQuery handles: page load spinner, sticky navbar, scroll-to-top, WOW.js init, Owl Carousel (hero), Isotope portfolio filter, Bootstrap Carousel hover play/pause, "Leer más" expand toggle.

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
- Images in [img/](img/); logo and favicon in [img/icon/](img/icon/)
- Third-party libraries are vendored in [lib/](lib/) — do not add CDN links for new libraries
- Bootstrap SCSS source is in [scss/bootstrap/](scss/bootstrap/) but `css/bootstrap.min.css` is what is served — the SCSS is not compiled as part of any build process
- Font Awesome 6.5 is the only icon library for FA icons; Bootstrap Icons (`bi-*`) are used for UI controls (arrows, chevrons)
- `app_config.example.php` is the template for server credentials — fill it in and place it at `/home/agasitco/app_config.php` on the server
