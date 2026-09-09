# Splendid Double Glazing — WordPress build

A custom WordPress theme and companion plugin implementing the approved green
design across all 38 routes of the Splendid Double Glazing website.

Built from the client handover package in [`handover/`](handover/), which stays
in the repository as the authoritative design, copy and brief reference.

## What is here

| Path | What it is |
|---|---|
| `wp-content/themes/splendid/` | The theme: shell, templates, ported design CSS, local fonts, front-end behaviour |
| `wp-content/plugins/splendid-core/` | The plugin: business settings, enquiry service, custom blocks, SEO output, content importer |
| `wp-content/plugins/splendid-core/content/` | The 38 page bodies as WordPress block markup, plus the import manifest |
| `content/legal/` | Source for the three legal pages, written from `handover/briefs/04` |
| `tools/` | Generators: rebuild the data file, the page content and the legal pages |
| `docs/` | Install guide, editor guide, route matrix, outstanding client items |
| `handover/` | The supplied package: design CSS, images, fonts, briefs, original React source |

## Quick start

```bash
# 1. Copy the theme and plugin into a WordPress install
cp -a wp-content/themes/splendid   /path/to/wp/wp-content/themes/
cp -a wp-content/plugins/splendid-core /path/to/wp/wp-content/plugins/

# 2. Activate both, then import the content
wp theme activate splendid
wp plugin activate splendid-core
wp splendid import --dry-run   # preview
wp splendid import             # create the 38 routes, media and menus
wp splendid verify             # per-route status report
```

Without WP-CLI, the same import runs from **Settings → Splendid**.

Full detail: [`docs/INSTALL.md`](docs/INSTALL.md).

## Design decisions worth knowing

- **Hybrid classic theme, not a page builder.** PHP templates render the shell;
  every page body is block-editor content. Nothing requires Divi, Bricks,
  Elementor or a paid plugin, and the public site renders from PHP on ordinary
  hosting.
- **`assets/css/design.css` is the ported original**, unedited except for the
  font URLs. WordPress-specific additions live beside it in `wp.css`, so a future
  design refresh can drop in a new `design.css`.
- **Page bodies are real blocks**, not one HTML blob: groups, headings,
  paragraphs and native `<details>` accordions, plus small server-rendered
  Splendid blocks where the design needs markup core cannot produce (bare
  images, anchors wrapping content, the enquiry form, live card grids).
- **Contact details are stored once** in `splendid_settings` and read by the
  header, footer, contact page, enquiry email and structured data.
- **The enquiry service is real.** It validates server-side, refuses to claim
  delivery without a configured mail transport, and never takes the recipient
  from the request.

## Regenerating content

The page markup is generated, not hand-maintained:

```bash
python3 tools/build-data.py      # handover JSON  -> plugin data.php
python3 tools/build-legal.py     # briefs/04      -> content/legal/*.html
python3 tools/build-content.py   # pages.json     -> block markup + manifest
```

## Status

See [`docs/COMPLETION-MATRIX.md`](docs/COMPLETION-MATRIX.md) for every route and
[`docs/OUTSTANDING.md`](docs/OUTSTANDING.md) for what still needs a client answer
before launch.
