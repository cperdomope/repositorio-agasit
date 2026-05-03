# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Corporate website for **AGA Soluciones de Ingeniería y Tecnología S.A.S.**, a Colombian electronic security company. Multi-page static site (HTML/CSS/JS) with a PHP backend for contact form email delivery.

## Tech Stack

- **Frontend**: HTML5, Bootstrap 5, jQuery, Owl Carousel, WOW.js + Animate.css
- **Backend**: PHP with PHPMailer (SMTP via mail.agasit.com)
- **Deployment**: cPanel rsync via `.cpanel.yml`

There is no build step — no npm, no bundler, no transpilation. Files are served as-is.

## Local Development

Open any `.html` file directly in a browser, or serve with any static server:

```bash
python -m http.server 8000
# or
npx serve .
```

The contact form (`contacto.html` → `send_email.php`) requires a PHP server to function.

## Deployment

Defined in [.cpanel.yml](.cpanel.yml). cPanel auto-deploys on push to `main`:

```yaml
rsync -avz css/ img/ js/ lib/ scss/ *.html $DEPLOYPATH/
```

Target server path: `/home/agasitco/public_html`

## Architecture

### Page Structure

All pages share the same layout: Bootstrap navbar → page-specific content → footer. The five HTML pages are:

- [index.html](index.html) — Homepage (hero carousel, company intro)
- [acerca-de.html](acerca-de.html) — About Us
- [servicios.html](servicios.html) — Services with Isotope filter grid
- [proyectos.html](proyectos.html) — Portfolio with Bootstrap carousel galleries
- [contacto.html](contacto.html) — Contact form

### JavaScript ([js/main.js](js/main.js))

jQuery handles all interactivity: page load spinner, sticky navbar, scroll-to-top button, WOW.js init, Owl Carousel init, Isotope filtering on the services page, and CounterUp for animated stats.

### Contact Form Flow

`contacto.html` form → POST to [send_email.php](send_email.php) → PHPMailer → SMTP. Credentials are hardcoded in `send_email.php`.

### URL Routing

[.htaccess](.htaccess) strips `.html` extensions from URLs, so internal links use bare names (e.g., `href="servicios"` resolves to `servicios.html`).

## Conventions

- All content is in **Spanish** (`lang="es"`)
- Images live in [img/](img/); site logo/favicon in [img/icon/](img/icon/)
- Third-party libraries are vendored in [lib/](lib/) — do not replace with CDN links
- Bootstrap SCSS source is in [scss/bootstrap/](scss/bootstrap/) but the minified CSS in `css/bootstrap.min.css` is what's actually used
