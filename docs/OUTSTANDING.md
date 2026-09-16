# Outstanding items

Two lists: what the client must answer, and what is left to build once those
answers arrive. Everything not on these lists is done.

## Needs a client answer

These come from `handover/briefs/04-legal-and-client-confirmations.md`. Nothing
here blocks the rest of the build, and nothing here has been guessed at.

1. **Contact details.** Confirm the phone number, the email address enquiries
   should reach, and the trading/contact address. Can customers visit? Are there
   opening hours? (No opening hours are published or in the structured data.)
2. **Brand.** Approve the green palette and the proposed text wordmark, or supply
   the real master logo. The wordmark in the header is a proposal, not a verified
   existing logo. A supplied logo drops into Appearance → Customize → Site Identity
   without touching code.
3. **Product range.** Confirm all 12 ranges are actually offered, including triple
   glazing, sash and bay designs, the six door ranges, and which conservatory
   types are available. Supply specification sheets and any approved performance
   or security claims.
4. **Local coverage.** Confirm survey and installation coverage for Sidcup,
   Eltham, Bexley, Bromley, Chislehurst, South East London and North Kent. The
   seven area pages are imported as drafts with noindex until this is confirmed
   and real local evidence exists.
5. **Warranty and compliance.** Supply warranty wording, installation cover and
   independently verifiable accreditation details. No FENSA registration or
   guarantee period is claimed anywhere on the site.
6. **Photographs and reviews.** Supply owned installation photos with captions and
   permission to publish, plus the exact independent review profile URL. The three
   images on the site are design illustrations and are labelled as such. No
   ratings, reviewer names or testimonials are invented.
7. **Access.** Staging/hosting access, eventual domain and DNS access, and
   authenticated mail setup — through a secure channel, not this repository.
8. **Data practices.** Host, mail and form providers, storage locations, who can
   access leads, retention periods, legal bases, international transfers, and
   whether analytics or marketing will exist. The privacy draft cannot be
   finished without these. **Since enquiries now also go to BizzFlowUK**, this
   also needs: a data processing agreement with LaunchFlow UK Limited, the
   region BizzFlowUK hosts its data in, and how long enquiries are kept there.
   The privacy draft already names BizzFlowUK and brackets these three facts.
9. **VAT number, contract terms, cancellation arrangements and complaints
   process**, if these are to be published.

## Left to build (and what unblocks each)

| Item | Unblocked by |
|---|---|
| Publish the three legal notices with the bracketed facts filled in | Answers 1, 8, 9 |
| Publish the seven local area pages, remove noindex | Answer 4 |
| Replace the illustrative gallery with real project photography | Answer 6 |
| Swap the review search link for the verified profile URL (one setting) | Answer 6 |
| Live enquiry delivery test to the real recipient | Answers 1, 7 |
| Enable enquiry storage in WordPress, if wanted | Answer 8 |
| Production deployment, redirects from the existing site, canonical domain | Answer 7 |
| Cookie inventory and, if optional cookies are added, a consent mechanism | Answer 8 |
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
- **To deploy this change:** replace `splendid-core` on the live site with this
  version. Until then the site behaves as before: enquiries still reach
  BizzFlowUK whenever the email sends.

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
- A real enquiry submission end to end, including provider failure handling —
  the sandbox this was built in has no mail transport, which is itself the
  "no transport configured" path and does behave as designed (refuse, do not
  claim delivery).
- Lighthouse or Core Web Vitals numbers. None are quoted anywhere; they should be
  measured on the real host.
