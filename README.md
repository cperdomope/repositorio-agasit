# AGA Soluciones de Ingeniería y Tecnología S.A.S.

Sitio web corporativo de AGA Soluciones de Ingeniería y Tecnología S.A.S., empresa
colombiana de seguridad electrónica (CCTV, control de acceso, cercas eléctricas,
detección de incendios, intrusión, citofonía, integración de sistemas).

Sitio estático multi-página (HTML5/CSS/JS) con un backend en PHP para el envío
del formulario de contacto.

## Stack

- **Frontend**: HTML5, Bootstrap 5, jQuery, Owl Carousel, WOW.js + Animate.css
- **Backend**: PHP con PHPMailer (SMTP vía mail.agasit.com)
- **Despliegue**: rsync automático desde cPanel al hacer push a `main` ([.cpanel.yml](.cpanel.yml))

No hay paso de build — sin npm, sin bundler, sin transpilación. Los archivos se sirven tal cual.

## Desarrollo local

Cualquier servidor de archivos estático sirve el sitio (los enlaces de navegación
usan extensión `.html`, por lo que funcionan igual en Live Server):

```bash
python -m http.server 8000
```

El formulario de contacto (`contacto.html` → `send_email.php`) necesita un servidor
PHP para funcionar (XAMPP, Laragon, etc.).

## Páginas

| Página | Contenido |
|---|---|
| [index.html](index.html) | Home (carrusel hero, presentación de la empresa) |
| [acerca-de.html](acerca-de.html) | Quiénes somos (misión, visión, valores) |
| [servicios.html](servicios.html) | Grid de servicios con expansión "Leer más" |
| [proyectos.html](proyectos.html) | Portafolio con galerías filtrables |
| [contacto.html](contacto.html) | Formulario de contacto |

## Despliegue

Automático vía cPanel al hacer push a `main` (ver [.cpanel.yml](.cpanel.yml)).
Antes del primer despliegue en un servidor nuevo, crear el archivo de credenciales
fuera del document root a partir de `app_config.example.php`:

```
/home/agasitco/app_config.php
```

Este archivo nunca debe estar dentro de `public_html` y ya está en `.gitignore`.

## Para contribuir con Claude Code

Ver [CLAUDE.md](CLAUDE.md) para las convenciones detalladas del proyecto
(estructura de carpetas, estrategia de rutas dual local/producción, organización
de `css/style.css`, etc.).

## Licencia

Propietaria — ver [LICENSE](LICENSE). Todos los derechos reservados por AGA
Soluciones de Ingeniería y Tecnología S.A.S.
