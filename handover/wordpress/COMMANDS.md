# WordPress commands for Claude Code

Run from the intended WordPress root. Replace example paths with actual paths. Confirm the target with `wp option get siteurl` first. This package has not been run against a WordPress installation and does not contain a completed theme. Commands for the final theme are conditional on Claude building that deliverable.

## Inspect, before changing anything

```bash
wp core version
wp option get siteurl
wp option get home
wp theme list
wp plugin list
wp post list --post_type=page --fields=ID,post_title,post_name,post_status --format=table
wp eval 'echo wp_get_environment_type();'
```

Back up through the actual hosting workflow or use `wp db export /secure-backup-path/before-splendid.sql` plus a separate files/uploads backup. The target directory must exist and be outside the public document root. Do not put database exports in the handover ZIP.

## Create staging content

The installation must already identify itself as local, development or staging. Do not relabel production to bypass the importer guard. In an actual staging installation, WP_ENVIRONMENT_TYPE is configured in wp-config.php or the hosting environment.

```bash
wp eval-file /absolute/path/splendid-wordpress-handover/wordpress/import-drafts.php
```

Review the dry-run output. Then, on that staging installation:

```bash
SPLENDID_IMPORT_APPLY=1 wp eval-file /absolute/path/splendid-wordpress-handover/wordpress/import-drafts.php
```

The importer creates missing pages as drafts, preserves existing matching pages and records IDs in the splendid_handover_page_ids option. It can be rerun without overwriting those pages. Inspect existing slug conflicts manually. It does not set the front page, change permalinks, create menus, attach templates, publish policies or wire email. The HTML is migration input, not a finished block layout. Claude must replace it with properly editable blocks/meta/templates and remap assets.

## Import the supplied image assets on staging

Run once and save the returned IDs to the media mapping. Before rerunning, inspect existing attachments to avoid duplicates.

```bash
wp media import /absolute/path/splendid-wordpress-handover/assets/images/bay-window.webp --title='Bay window design inspiration' --alt='A bright room with a white bay window overlooking a garden; design illustration' --porcelain
wp media import /absolute/path/splendid-wordpress-handover/assets/images/composite-door.webp --title='Entrance door design inspiration' --alt='Charcoal entrance door with brass hardware in a brick home; design illustration' --porcelain
wp media import /absolute/path/splendid-wordpress-handover/assets/images/sliding-glazing.webp --title='Garden glazing design inspiration' --alt='Architectural garden glazing on a British brick home; design illustration' --porcelain
```

Bundle local fonts and their licences with the theme rather than requiring font upload permissions in the Media Library. The source favicon is an SVG; add it as a theme asset or create approved raster site-icon sizes for WordPress. Do not broadly enable unsanitised SVG uploads.

## After Claude has built the theme

```bash
wp theme install /absolute/path/dist/splendid-wordpress-theme.zip --activate
```

If the completed implementation has a companion plugin:

```bash
wp plugin install /absolute/path/dist/splendid-core.zip --activate
```

Set the front page using the verified imported Home ID, create/edit the menus, set the privacy page to its approved ID and connect SEO settings through the chosen implementation. `_splendid_seo_title` and `_splendid_meta_description` are handover metadata only: Claude must render/map them; WordPress does not automatically output them.

Use `wp option update timezone_string Europe/London` on staging if appropriate. Configure locale and permalink choices to suit the actual implementation. Preserve nested product and /blog/article URLs. Avoid global permalink changes on an existing live site without a redirect plan.

## Verification and launch

```bash
wp post list --post_type=page --fields=ID,post_title,post_name,post_status --format=table
wp rewrite list --format=table
```

Do not mass-publish all imported drafts: product availability, coverage and legal configuration must be resolved first. Use the final acceptance checklist, then publish approved pages individually or through Claude's reviewed deployment script. No real-client test emails or DNS changes are authorised by merely preparing this package.

References: [WP-CLI post creation](https://developer.wordpress.org/cli/commands/post/create/), [WordPress theme handbook](https://developer.wordpress.org/themes/). Inspect command help and installed versions before executing on the target environment.
