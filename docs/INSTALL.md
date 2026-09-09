# Installing the Splendid website

## Requirements

- WordPress 6.5 or later (built and tested against 7.1)
- PHP 7.4 or later (tested on PHP 8.4)
- Pretty permalinks (`/%postname%/`) — the importer sets this
- An authenticated mail transport (SMTP or an email API) before enquiries go live

## 1. Install the theme and plugin

Copy both directories into the WordPress install, or upload the ZIPs built by
`tools/build-zips.sh` through **Appearance → Themes → Add New → Upload** and
**Plugins → Add New → Upload**.

```
wp-content/themes/splendid/
wp-content/plugins/splendid-core/
```

Activate the **Splendid** theme and the **Splendid Core** plugin.

## 2. Import the content

**With WP-CLI**

```bash
wp splendid import --dry-run    # shows what would change, writes nothing
wp splendid import              # creates pages, imports media, builds menus
wp splendid verify              # table of every route, status and word count
```

**Without WP-CLI** — go to **Settings → Splendid**, scroll to *Content import*,
tick *Preview only* for a dry run, then run it for real.

The import:

- creates the 38 pages with their own copy, SEO title and meta description
- imports the three supplied images into the Media Library and records the
  source-to-media URL mapping in the `splendid_media_map` option
- sets the homepage, permalink structure and the Europe/London timezone
- builds and assigns the six menus (main, mobile drawer, three footer columns,
  legal row)

It is safe to run again: it never duplicates a page, and it skips any page an
editor has changed since the last import unless you pass `--force`.

**Imported as drafts on purpose:** the seven local area pages (coverage not yet
confirmed) and the three legal notices (configuration not yet final). Publishing
them is a deliberate decision, not an oversight — see `docs/OUTSTANDING.md`.

## 3. Configure the business details

**Settings → Splendid.** Everything the shell, contact page, enquiry email and
structured data use is on this one screen. Confirm every value with the client
before launch.

## 4. Configure enquiry delivery

1. Install and configure an SMTP or email-API plugin for the site's own domain.
2. In **Settings → Splendid → Enquiries**, set *Send enquiries to* — use a test
   mailbox or mail sink until the owner confirms the real destination **and**
   authorises a live delivery test.
3. Set *Send enquiries from* to an address on a domain this site is authorised
   to send as (SPF/DKIM aligned). The visitor's address is used as Reply-To only.
4. Leave *Store enquiries in WordPress* off until the privacy notice describes
   what is stored, for how long and who can access it.

The settings screen tells you honestly whether a transport is detected. Without
one, enquiries are **refused with a retry message** rather than reported as sent.

## 5. Set the site language

Set **Settings → General → Site Language** to *English (UK)*. The importer sets
the timezone; the language pack needs an internet connection to install.

## 6. Before going live

Work through `handover/briefs/05-seo-testing-and-launch.md`. In particular:

- keep staging both password-protected and noindex — noindex alone is not access control
- verify the production robots configuration deliberately at launch
- if replacing an existing WordPress site, inventory its URLs, posts, media and
  metadata first, back up files and database, and map legacy URLs individually
  with 301s. Do not import a development database over production.
- keep mail DNS records intact when changing hosting

## Backup and rollback

Before deploying:

```bash
wp db export backup-before-splendid.sql
tar czf backup-before-splendid-files.tar.gz wp-content/
wp theme list --status=active   # record what is active now
wp plugin list --status=active
```

To roll back: restore the database export, restore `wp-content/`, reactivate the
previously recorded theme and plugins, then flush permalinks
(`wp rewrite flush`). Enquiries received during the deployment window live in the
mail destination, not only in WordPress, so a database rollback does not lose them.
