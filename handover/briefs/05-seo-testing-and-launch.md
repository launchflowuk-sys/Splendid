# SEO, testing and launch acceptance

## SEO and migration

Use titles/descriptions in content/pages.json as starting values, trimming naturally where necessary. Each content page needs one visible H1, its own title, an appropriate description and a self-referencing canonical on the final domain. Use a single source of SEO output; do not emit conflicting theme/plugin metadata. Use en-GB and Europe/London for site configuration. Do not put preview-domain URLs in live canonicals.

Use WordPress's sitemap or the selected SEO implementation. Exclude drafts, private records, administration and form success states. Keep staging both access-protected and noindex; noindex alone is not access control. At launch verify the production robots configuration deliberately. Location pages with unconfirmed coverage or little meaningful unique content should remain unpublished/noindex until improved with real evidence.

Structured data should describe the actual business, using the supported company name, address, telephone and final site URL. Add verified sameAs URLs only. Do not fabricate AggregateRating, openingHours, FENSA certification, offers, pricing or service locations. Add breadcrumb data only where consistent with navigation. FAQ content remains useful even if no search enhancement is available.

Before replacing an existing site, crawl/export its actual URLs, posts, media and metadata. Map old routes to their closest equivalents with individual 301 redirects; do not redirect every old path to the homepage. Preserve useful legacy content and legal obligations after review. Do not assume the new sitemap proves the old site had no other pages.

## Media and performance

Use the supplied local images initially. Record each imported attachment ID and map source URLs to WordPress media URLs. Replace generated inspiration with genuine photos only where a real-work claim is made. Generate suitable responsive variants and preserve width/height to avoid layout shift. Load the hero promptly; lazy-load lower images. Avoid external font calls and unneeded front-end libraries. Serve a cached site where compatible with forms/nonces and consent. Measure the final hosted build; do not invent a Lighthouse or Core Web Vitals score.

## Required verification

- All 38 specified paths exist, with the correct page-specific copy and no empty cloned templates. Record any intentionally draft business-dependent pages.
- All desktop dropdown, mobile drawer, footer, article, area, card and CTA destinations resolve. Check nested product links and query-string product preselection.
- No missing images/fonts, old copper/navy overrides, competitor contact details, unreviewed bracketed placeholders, fake reviews, unsupported product claims or prototype privacy language on published pages.
- Desktop/mobile comparisons at 1440, 1024, 768 and 390px, plus 320px overflow and 200% text zoom. Verify hero crop, heading line breaks, card proportions, footer wrapping and sticky header/admin bar.
- Keyboard navigation, submenu Escape/focus handling, visible focus, form labels/errors, FAQ toggles, gallery filter state, reduced motion and content with JavaScript disabled.
- Valid form submission to an approved test destination; invalid fields rejected server-side; provider failure handled; duplicate prevention; preselected product retained; no PII in URL/log analytics; no publicly accessible leads.
- Cookie audit in fresh and returning sessions if tracking is present: no optional scripts before the required choice, rejection works, preferences can be changed.
- Normal pages return 200; missing URLs return 404; intentional redirects return 301 with no loops; canonical/robots/sitemap use the right production origin.
- WordPress theme/plugin syntax/build checks, supported installed PHP/WordPress versions, and no uncaught browser errors. Give actual tool results and limitations.

## Production transition

1. Deliver the complete staging build and the remaining client-confirmation list. Get concrete approval for production deployment.
2. Back up production database and files; record the current theme/plugin versions and a tested restoration method.
3. Deploy versioned theme/plugin assets and migrate approved content/media. Never import a development database over production without a reviewed migration plan.
4. Configure mail credentials securely and verify SPF/DKIM/DMARC as appropriate without breaking existing mail records.
5. Apply redirects, canonical domain and SSL. Remove staging-only noindex from production only; retain it on staging.
6. Verify contact details, real enquiry delivery with authorisation, sitemap, robots, key pages and all legal links after release.
7. If release fails, restore the recorded code/database state and routing; preserve enquiries received during the deployment window.

## Claude's final deliverables

Installable theme ZIP; companion plugin ZIP if needed; reproducible importer; source; media mapping; client editor guide; configuration/backup instructions; 38-row completion matrix; real test results and screenshots; short outstanding-items list. No production credentials in any ZIP.
