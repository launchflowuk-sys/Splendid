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
   finished without these.
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

## Deliberately not built

None of these has been commissioned, and none is invented:

- ecommerce checkout, instant pricing, finance application, booking calendar,
  CRM subscription or WhatsApp link
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
