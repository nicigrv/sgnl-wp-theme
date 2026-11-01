# SG Neptun Lampertheim – WordPress-Theme

Eigenständiges Vereinstheme für [sgnl.de](https://sgnl.de) (kein Parent-Theme, kein Page-Builder).
Gehört zum Plugin [sgnl-wp-core](https://github.com/nicigrv/sgnl-wp-core) („SG Neptun Funktionen“).

## Updates auf der Website
Das Theme fragt das neueste [Release](../../releases) ab. Neue Versionen erscheinen in WordPress unter
**Dashboard → Aktualisierungen** und werden dort wie gewohnt installiert („Erneut überprüfen“ prüft sofort,
sonst prüft WordPress automatisch alle paar Stunden).

## Neue Version veröffentlichen
1. Änderungen committen.
2. Versionsnummer erhöhen in `style.css` (`Version:`) **und** `functions.php` (`SGNL_THEME_VERSION`).
3. Tag mit Beschreibung der Änderungen anlegen und pushen:
   ```bash
   git tag -a v2.0.3 -m "Was sich geändert hat"
   git push origin main --tags
   ```
4. Die GitHub-Action baut `sgnl-neptun.zip` und legt das Release an. Passt die Version im Code nicht
   zum Tag, bricht sie mit Fehler ab.

Die Beschreibung des Tags erscheint in WordPress als Änderungsliste.

## Aufbau
| Pfad | Inhalt |
|---|---|
| `style.css`, `theme.json` | Theme-Kopf, Farben, Schriften, Abstände |
| `assets/css/theme.css`, `assets/js/theme.js` | Design, Navigation, Slideshow, Kennzahlen |
| `inc/customizer.php` | Design → Anpassen → „SG Neptun“ |
| `inc/class-github-updater.php` | Updates aus GitHub-Releases |
| `patterns/` | Muster im Editor (Team-Raster, Info-Box, Text + Bild) |

Lizenz: GPL-3.0-or-later. Schriften: Lexend / Lexend Exa (SIL Open Font License, siehe `assets/fonts/`).
