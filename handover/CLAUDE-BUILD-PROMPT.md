# Claude Code: build the complete Splendid Double Glazing WordPress website

You are implementing a client website, using this package as the approved design and content specification. Build the whole 38-page website in WordPress. The client loves this design. Preserve its composition, green colours, typography, images, spacing, navigation, footer and restrained motion. Do not replace it with a generic template or stop after the homepage.

## Read before implementation

Read START-HERE.md, every document in briefs/, content/SITEMAP.md and content/pages.json. Inspect reference-source/app/site.tsx, content.ts and globals.css. Inspect assets/. The source is authoritative for visual detail. The production briefs override source functionality where explicitly stated. The selected palette is GREEN; reference-source/RESEARCH.md contains an older navy/copper design note which is superseded by the CSS and this instruction.

## Work autonomously

Inspect the available repository and WordPress environment. Start a branch and use a local or staging environment. Preserve existing production data. If there is no WordPress installation yet, build the distributable theme and companion plugin locally, with installation documentation; do not require hosting access to begin. Use existing project conventions where compatible with this brief.

Choose a custom WordPress theme with native editable blocks/patterns and small server-rendered custom blocks where required. Use theme.json, local styles, local fonts, reusable header/footer components and native WordPress APIs. Prefer minimal dependencies. Do not require Elementor, Divi or a paid plugin just to reproduce this design. Avoid headless WordPress unless specifically requested later. The visitor-facing site should render from WordPress/PHP on ordinary compatible hosting.

Use core block patterns for content that editors should change. Use a companion plugin for durable data or custom blocks rather than coupling business records to the theme. If using PHP template parts with registered post meta, provide usable editor controls for every editable field. Do not hardcode all page bodies inside PHP or a single opaque HTML block. Preserve URLs and allow client editing without code.

## Build all pages

Implement every route in content/pages.json, with unique page copy from content/pages/. Match the template families in the manifest. Build all Windows and Doors subpages, Conservatories, Porches, Our Story, Inspiration, Reviews, all seven location pages, Advice hub and all three articles, Contact, Free Quote, Quote Planner and all three legal pages. Add a true 404 template. Keep the seven locality pages in staging until coverage and meaningful local content are confirmed; do not invent local installations.

The page manifest contains complete source copy and HTML fragments for extraction, not final WordPress block markup. Convert it into editable content. The optional importer creates drafts as a starting point only. The importer does not build the theme, wire forms, map media URLs or publish pages. Complete those tasks yourself.

Preserve the shared desktop and mobile navigation and every footer link. Improve keyboard/touch access to product submenus without changing the desktop silhouette. Follow briefs/02-navigation-and-page-templates.md for exact order and mapping.

## Exact visual translation

Use assets/design.css and reference-source/app/globals.css as measured references. Port design selectors and cascade accurately; do not blindly paste Tailwind directives into WordPress. Rewrite asset URLs to theme or Media Library URLs. Preserve original local font bytes and verify font metadata/licensing. Match actual font faces visually, including italic serif emphasis. Preserve hero line breaks and the 49/51 desktop split, green CTAs, white background, dark green footer, full-width photographs, restrained rounded corners and responsive breakpoints. WordPress admin-bar offset must not obscure the sticky header.

Original React components are the reference. Translate Next Link to correct WordPress URLs, React state to lightweight browser interactions, Lucide SVG icons with their licence, and UI primitives to accessible WordPress/front-end equivalents. Do not load React on every public page merely to render marketing text. Keep source images illustrative until authentic images are supplied.

## Working production behaviour

Build real server-side enquiry submission for Contact, Free Quote and Quote Planner with the fields and states in briefs/03-production-behaviour.md. Deliver through configured authenticated mail. Use the verified recipient as a server-side setting, not a client-controlled request parameter. Validate, limit spam, escape output, protect against header injection and prevent duplicate submissions. Test delivery only to an authorised test mailbox or a mail sink until a real recipient test is authorised. Persist restricted lead records only if the agreed privacy configuration allows it; otherwise queue/retry securely using the approved email infrastructure.

Do not report an enquiry as sent merely because a browser button was clicked. Define success accurately according to provider acceptance or durable queue acceptance, and show a useful retry state for failure. Do not promise a response deadline that the business has not agreed.

The quote planner collects product, material, quantity, postcode and requirements. It does not calculate money. A numerical estimator requires a verified rate table and pricing rules. Preserve the existing /estimate-calculator path but label it Quote planner.

Use briefs/04-legal-and-client-confirmations.md to replace prototype policies with the supplied WordPress drafts. Finalise the drafts against the actual hosting, mail, forms, retention and cookie implementation. Never publish bracketed placeholders, internal notes, unsupported guarantees, invented reviews, fabricated accreditations or generated photos labelled as real work.

## Technical finish

Implement SEO titles and descriptions from the manifest, clean canonicals, XML sitemap, correct status codes, responsive image dimensions/srcset, local fonts, accessible navigation, keyboard-operable accordions, reduced motion and robust forms. Adapt metadata naturally if the final content changes. Use one SEO implementation to avoid duplicate tags or schema. Do not add fake AggregateRating, product prices or opening hours to structured data.

If migrating an existing live WordPress website, inventory current URLs and media first, back up files and database, map legacy URLs individually and retain useful existing content. The existing public website appears to use WordPress already; do not overwrite it with a fresh database. Keep mail DNS records intact when changing hosting.

## Deliverables and completion standard

Deliver an installable custom theme ZIP, companion plugin ZIP if used, repeatable content import/seed process, media mapping, full source, installation instructions, backup/rollback procedure and client editing guide. Provide a route-by-route completion matrix for all 38 URLs and a concise list of client-dependent items. Show desktop/mobile screenshots from the completed WordPress build and report the viewport sizes used.

Verify the acceptance checklist in briefs/05-seo-testing-and-launch.md. Test all routes and nested links, product preselection, forms, server rejection, mobile navigation, gallery filters, cookie choices if any, and 404 behaviour. Check 390, 768, 1024 and 1440px viewports plus a narrow 320px screen. Complete all locally/staging-testable work before asking for any missing production access. Obtain approval for the final production deployment after the concrete build is reviewable, unless that deployment has already been expressly authorised.

Continue through implementation and validation. Do not stop at a plan, design description, list of suggested plugins, homepage-only build or promise to implement the other pages later.
