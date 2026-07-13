#!/usr/bin/env bash
# Chequeos de consistencia para el sitio estático de AGA Soluciones IT.
# No requiere build ni dependencias: solo bash + grep + find, coherente
# con el "sin build step" del proyecto (ver CLAUDE.md).
#
# Uso: ./scripts/check-consistency.sh
# Exit code 0 si todo está bien, 1 si encontró algo que revisar.

set -uo pipefail
cd "$(dirname "$0")/.."

issues=$(mktemp)
trap 'rm -f "$issues"' EXIT

section() {
    echo
    echo "== $1 =="
}

# 1. Enlaces internos rotos (href="pagina.html")
section "Enlaces internos (href=\"*.html\")"
for f in *.html; do
    while IFS= read -r target; do
        [ -n "$target" ] || continue
        if [ ! -f "$target" ]; then
            echo "FALTA $target (referenciado en $f)" | tee -a "$issues"
        fi
    done < <(grep -oE 'href="[A-Za-z0-9_-]+\.html"' "$f" | sed -E 's/^href="//; s/"$//' | sort -u)
done

# 2. Referencias de imagen rotas (src="img/..." y url(...img/...))
section "Referencias de imagen (src=\"img/...\" y url(...) en CSS)"
for f in *.html css/style.css; do
    while IFS= read -r target; do
        [ -n "$target" ] || continue
        clean="${target#../}"
        if [ ! -f "$clean" ]; then
            echo "FALTA $clean (referenciado en $f)" | tee -a "$issues"
        fi
    done < <(grep -oE '(\.\./)?img/[A-Za-z0-9_./-]+\.(jpg|jpeg|png|gif|svg|webp)' "$f" | sort -u)
done

# 3. Imágenes huérfanas (ningún .html ni css/style.css las menciona)
section "Imágenes huérfanas en img/"
while IFS= read -r imgfile; do
    [ -n "$imgfile" ] || continue
    if ! grep -qlF "$imgfile" *.html css/style.css 2>/dev/null; then
        echo "HUERFANA: $imgfile" | tee -a "$issues"
    fi
done < <(find img -type f | sort)

# 4. Cache-busting (?v=) consistente entre páginas para css/style.css y js/main.js
section "Versión de css/style.css y js/main.js consistente entre páginas"
css_versions=$(grep -ohE 'css/style\.css\?v=[0-9]+' *.html | sort -u)
js_versions=$(grep -ohE 'js/main\.js\?v=[0-9]+' *.html | sort -u)
if [ "$(printf '%s\n' "$css_versions" | grep -c .)" -gt 1 ]; then
    echo "css/style.css tiene versiones distintas entre páginas:" | tee -a "$issues"
    printf '%s\n' "$css_versions" | tee -a "$issues"
fi
if [ "$(printf '%s\n' "$js_versions" | grep -c .)" -gt 1 ]; then
    echo "js/main.js tiene versiones distintas entre páginas:" | tee -a "$issues"
    printf '%s\n' "$js_versions" | tee -a "$issues"
fi

echo
if [ -s "$issues" ]; then
    echo "Resultado: se encontraron inconsistencias (ver arriba)."
    exit 1
else
    echo "Resultado: todo consistente."
    exit 0
fi
