# SG Neptun Lampertheim – WordPress-Theme

Eigenständiges Vereinstheme für [sgnl.de](https://sgnl.de) (kein Parent-Theme, kein Page-Builder).
Gehört zum Plugin [sgnl-wp-core](https://github.com/nicigrv/sgnl-wp-core) („SG Neptun Funktionen“).

## Updates auf der Website
Das Theme fragt das neueste [Release](../../releases) ab. Neue Versionen erscheinen in WordPress unter
**Dashboard → Aktualisierungen** und werden dort wie gewohnt installiert („Erneut überprüfen“ prüft sofort,
sonst prüft WordPress automatisch alle paar Stunden).

## Neue Version veröffentlichen
1. Änderungen machen und die Versionsnummer erhöhen in `style.css` (`Version:`) **und** `functions.php` (`SGNL_THEME_VERSION`), z. B. auf `2.0.3`.
2. Commit auf `main` pushen. Die Commit-Nachrichten seit der letzten Version erscheinen in WordPress als Änderungsliste.
3. Die GitHub-Action merkt die neue Versionsnummer, baut die ZIP und legt das Release `v2.0.3` an.
   Ohne neue Versionsnummer wird kein Release erstellt.

## Aufbau
| Pfad | Inhalt |
|---|---|
| `style.css`, `theme.json` | Theme-Kopf, Farben, Schriften, Abstände |
| `assets/css/theme.css`, `assets/js/theme.js` | Design, Navigation, Slideshow, Kennzahlen |
| `inc/customizer.php` | Design → Anpassen → „SG Neptun“ |
| `inc/class-github-updater.php` | Updates aus GitHub-Releases |
| `patterns/` | Muster im Editor (Team-Raster, Info-Box, Text + Bild) |

Lizenz: GPL-3.0-or-later. Schriften: Lexend / Lexend Exa (SIL Open Font License, siehe `assets/fonts/`).
