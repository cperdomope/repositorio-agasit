# Versiones de librerías vendored

Registro de qué versión de cada librería de terceros vive en `lib/`, para saber
qué actualizar y desde dónde descargar la próxima versión.

| Librería | Carpeta | Versión | Origen |
|---|---|---|---|
| Owl Carousel | `lib/owlcarousel/` | 2.1.6 | https://github.com/OwlCarousel2/OwlCarousel2 |
| Animate.css | `lib/animate/` | 3.x (clases sin prefijo `animate__`) | https://github.com/daneden/animate.css |
| WOW.js | `lib/wow/` | 1.1.2 (última release del proyecto, sin banner de versión en el archivo) | https://github.com/matthieua/WOW |
| jQuery Easing | `lib/easing/` | sin versión declarada en el archivo | https://github.com/gdsmith/jquery.easing |

## Pendientes de vendorizar (actualmente vía CDN)

Estas librerías todavía se cargan desde CDN externo; hay una nota "ACCIÓN REQUERIDA"
en el `<head>` de cada página HTML recordando bajarlas a `lib/` y actualizar la CSP
en `.htaccess` cuando se haga:

| Librería | Versión CDN actual | Destino previsto |
|---|---|---|
| jQuery | 3.6.4 | `lib/jquery/jquery.min.js` |
| Bootstrap (JS bundle) | 5.3.0 | `lib/bootstrap/bootstrap.bundle.min.js` |
| Bootstrap Icons | 1.4.1 | `lib/bootstrap-icons/` |

`css/bootstrap.min.css` ya está vendorizado localmente (compilado con variables de marca
personalizadas — `--bs-primary`, etc.), pero el archivo minificado no conserva un banner
de versión; se asume Bootstrap 5.3.x para que coincida con el bundle JS cargado por CDN.

## Cómo actualizar una librería

1. Descargar la nueva versión desde el repositorio de origen (columna "Origen"/"Destino previsto").
2. Reemplazar los archivos dentro de la carpeta correspondiente en `lib/`.
3. Actualizar la fila de este archivo con la nueva versión.
4. Probar visualmente las páginas que dependen de esa librería antes de hacer commit.
