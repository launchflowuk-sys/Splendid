# Outstanding items

Two lists: what the client must answer, and what is left to build once those
answers arrive. Everything not on these lists is done.

## Needs a client answer

These come from `handover/briefs/04-legal-and-client-confirmations.md`. Nothing
here blocks the rest of the build, and nothing here has been guessed at.

1. **Contact details.** Phone, email and address are set (020 7998 6802,
   info@splendidglazing.co.uk, 758 Sidcup Road). Still open: can customers visit,
   and are there opening hours? (None are published or in the structured data.)
2. **Brand.** Approve the green palette and the proposed text wordmark, or supply
   the real master logo. The wordmark in the header is a proposal, not a verified
   existing logo. A supplied logo drops into Appearance → Customize → Site Identity
   without touching code.
3. **Product range.** Confirm all 12 ranges are actually offered, including triple
   glazing, sash and bay designs, the six door ranges, and which conservatory
   types are available. Supply specification sheets and any approved performance
   or security claims.
4. ~~**Local coverage.**~~ **Answered September 2026:** all seven areas are
   covered. The area pages now import as published and indexable.
5. **Warranty and compliance.** Supply warranty wording, installation cover and
   independently verifiable accreditation details. No FENSA registration or
   guarantee period is claimed anywhere on the site.
6. **Photographs and reviews.** Supply owned installation photos with captions and
   permission to publish, plus the exact independent review profile URL. The three
   images on the site are design illustrations and are labelled as such. No
   ratings, reviewer names or testimonials are invented.
7. ~~**Access.**~~ **Answered:** LaunchFlow holds hosting (Hostinger), DNS and
   mail access. Credentials stay outside this repository.
8. **Data practices.** Host, mail and form providers, storage locations, who can
   access leads, retention periods, legal bases, international transfers, and
   whether analytics or marketing will exist. The privacy draft cannot be
   finished without these. **Answered September 2026:** website and email on
   Hostinger; BizzFlowUK data on Hetzner in the EU; enquiry storage in WordPress
   stays off; no analytics, embeds or cookies (live site checked 17 September
   2026). The privacy and cookie drafts now say so. **Still open:** retention
   periods (enquiries, customer records, security logs, backup cycle); the
   Hostinger data centre location; confirmation of the purposes and legal bases
   paragraph; and a data processing agreement between LaunchFlow UK Limited and
   the business — **not yet in place**, so the privacy notice cannot be
   published until it is signed.
9. **VAT number, contract terms, cancellation arrangements and complaints
   process**, if these are to be published.

## Left to build (and what unblocks each)

| Item | Unblocked by |
|---|---|
| Publish the three legal notices with the bracketed facts filled in | Answers 1, 8, 9 |
| Publish the seven local area pages on the live site and untick noindex (the importer keeps an existing page's status, so this is manual — see below) | Nothing — can be done now |
| Replace the illustrative gallery with real project photography | Answer 6 |
| Swap the review search link for the verified profile URL (one setting) | Answer 6 |
| Live enquiry delivery test to the real recipient | Answers 1, 7 |
| Enable enquiry storage in WordPress, if wanted | Answer 8 |
| Redirects from the old site, if any old URLs still receive traffic | Nothing — access is in hand |
| Consent mechanism — only if optional cookies or analytics are ever added | Not needed today |
| Re-import the privacy draft so the site carries the BizzFlowUK wording (`wp splendid import --force`, or Settings → Splendid) — the importer skips pages already on the site | Nothing — can be done now |

## Added after the build: BizzFlowUK

Commissioned September 2026. Every website enquiry is now also sent into
**BizzFlowUK**, the lead system the business uses to manage enquiries. The site's
own notification email carries on exactly as before.

- **How it connects.** The *BizzFlow Connector* plugin, installed on the live
  site and maintained by LaunchFlow alongside BizzFlowUK (it is not kept in this
  repository — ask LaunchFlow for the current zip). It listens to hooks this
  plugin already fires; Splendid's theme and templates are untouched.
- **Business code:** `splendid`. First live lead confirmed 16 September 2026.
- **One change in `splendid-core`:** a vendor-neutral filter,
  `splendid_enquiry_delivered_elsewhere`, in `includes/enquiry.php`. When the
  notification email fails, it asks whether any other route has confirmed
  receipt. The connector answers yes only once BizzFlowUK has accepted the
  enquiry — so a visitor is no longer turned away during a mail outage when the
  enquiry has in fact arrived, and is never told it arrived when it has not.
  Where WordPress storage is on, the email is still queued so the team's own
  notification catches up.
- **Personal data held in WordPress.** If BizzFlowUK cannot be reached, the
  connector keeps the enquiry for **no more than 24 hours** while it retries,
  then deletes it. Its activity log records outcomes, never names or messages.
  The privacy draft states this.
- **Deployed.** Splendid Core 1.1.4 and BizzFlow Connector 1.1.0 are on the
  live site (17 September 2026).

## Live site status (17 September 2026)

Fixed on the live site this week:

- **The quote form never submitted with JavaScript on** (1.1.3/1.1.4). WordPress's
  REST API checks a `_wpnonce` form field before the `X-WP-Nonce` header, and the
  form carries the no-JavaScript fallback's `_wpnonce`, so every submission was
  refused with "Cookie check failed". Fixed in `assets/enquiry.js` and, because
  LiteSpeed serves its combined script for a year under an unchanged name, on the
  server too (`splendid_enquiry_prefer_header_nonce`). The header nonce is still
  verified. Any enquiry sent before this fix never arrived anywhere.
- **Stale nonces from the page cache** (1.1.2). The nonce route is no longer
  cached and the form fetches a fresh nonce on every submit.
- **FluentSMTP was not recognised** (1.1.1), so every enquiry was refused as
  "no transport". Detection now counts any non-core `wp_mail()`.
- Smoke test passed end to end: visitor sees the confirmation, BizzFlowUK
  accepted the lead.

Still broken on the live site:

| Problem | Effect | Fix |
|---|---|---|
| FluentSMTP: "SMTP Error: Could not authenticate" for info@splendidglazing.co.uk | No enquiry email reaches the business; leads only in BizzFlowUK | Correct mailbox password in Settings → FluentSMTP, then Retry the failed log entry |
| The form's "Read our privacy notice" link goes to a draft page | Visitors get a 404 while the form collects personal data | Answer 8, fill the brackets, publish the privacy notice (and cookie notice and terms) |
| The seven local area pages are drafts | Not reachable or indexed | Coverage now confirmed: publish them and untick noindex. WP-CLI: `wp post list --post_type=page --post_status=draft --meta_key=_splendid_template --meta_value=local-area --field=ID \| xargs -I{} sh -c 'wp post meta update {} _splendid_noindex 0 && wp post update {} --post_status=publish'` |
| No-JavaScript fallback still checks a page-cached nonce | A visitor with JavaScript off may see "link expired" | Low priority; exclude `/free-quote` and `/contact` from LiteSpeed page cache, or accept |

## Deliberately not built

None of these has been commissioned, and none is invented:

- ecommerce checkout, instant pricing, finance application, booking calendar
  or WhatsApp link (a CRM has since been commissioned — see *BizzFlowUK* above)
- a numeric quote estimator — the planner collects requirements and says plainly
  that it does not calculate a price
- AggregateRating, opening hours, offers, prices or certification in structured data
- an embedded map (the directions link opens Google Maps as an external link)
- analytics or advertising scripts

## Verified so far

- All 38 routes return HTTP 200 on a local WordPress 7.1 install with no PHP
  notice, warning or fatal. `/does-not-exist` returns a real 404.
- PHP syntax checks pass on every theme and plugin file; `node --check` passes on
  all three JavaScript files.

## Not yet verified

- Browser testing at 1440, 1024, 768, 390 and 320px, keyboard navigation, focus
  handling, reduced motion and JavaScript-disabled rendering. The code is written
  for all of these but they have not been exercised in a browser yet.
- A real enquiry end to end **with the email arriving** — done on the live site
  17 September 2026 except the email, which fails SMTP authentication (above).
- Lighthouse or Core Web Vitals numbers. None are quoted anywhere; they should be
  measured on the real host.
