# Working WordPress functionality

## Enquiries: replace the prototype email-draft flow

The approved layout stays. The prototype's Prepare my enquiry / Open email draft interaction must be replaced by a server-backed enquiry. Contact, Free Quote and Quote Planner share a service and differ only in heading/context. Preserve the source query parameter `?product=...` for product preselection, validating it against allowed product values.

| Field | Rule |
|---|---|
| Interested in | Required select; Windows, Doors, Conservatories, Porches, A whole-home project, all 12 specific products. |
| Preferred material | Required choice; Help me choose, uPVC, Aluminium, Timber, Composite. Final combinations subject to product availability. |
| Approximate quantity | Integer 1–100; keyboard input or accessible +/- buttons. |
| Postcode | Required; trim and normalise spacing/case; sensible UK-format validation without falsely rejecting valid postcodes. |
| Name | Required, 1–100 characters; allow normal Unicode names. |
| Email | Required, valid email, max 200 characters. |
| Phone | Optional, max 30; accept international formatting. |
| Project details | Required, max 3000 characters. |

No upload field is necessary for parity. If later adding photos, set an explicit size/type policy, scan uploads and keep private records separate from the public Media Library.

### Exact production microcopy

Form introduction: “Tell us what you have in mind and our team will contact you about the next steps.”
Submit button: “Send my enquiry”
Submitting: “Sending your enquiry…”
Accepted by durable service: “Thank you. Your enquiry has been received.”
Supporting success copy: “Our team will review your project details and contact you using the information you provided.”
Failure: “We couldn’t submit your enquiry. Your details are still here—please try again, or call 020 7998 6802.”
Validation summary: “Please check the highlighted fields.”
Email error: “Enter a valid email address.”
Price note: “Your price will be confirmed after the team reviews your requirements and the agreed specification. This planner does not calculate a price.”
Privacy text: “We’ll use your details to respond to your enquiry. Read our privacy notice.”

No compulsory marketing opt-in. Enquiry processing and optional marketing have separate purposes. If marketing is added, use a separate unticked choice and document it. Do not imply guaranteed inbox delivery based only on wp_mail() returning true. Confirm the SMTP/API configuration, capture provider responses and implement retries or a restricted queue if appropriate. A service accepting a durable enquiry can show received; an unconfigured mail transport cannot.

### Server behaviour

Validate independently of browser validation; use nonces with a cache-aware refresh strategy, rate limits, a honeypot or accessible spam control and duplicate request protection. A public WordPress nonce alone is not bot protection. Avoid putting personal data in URLs or analytics. Escape output, reject CR/LF mail-header injection, and use an authenticated domain as From with the submitter's validated address as Reply-To. Keep destination email server-side. Use secrets through the hosting configuration, never in the theme ZIP or browser JS.

Configure destination info@splendidglazing.co.uk after the owner confirms it. Use a mail sink/test recipient during development. Store leads only in a restricted record type/service when approved; no public query, REST exposure, attachment URLs or search results. Document access roles, retention and deletion. Preserve the form data after errors and disable duplicate clicks while a request runs. Handle offline, timeout, server error and anti-spam rejection states accessibly.

Success messages need role=status or equivalent live announcement. Focus a useful summary on error and connect messages to fields. Provide no-JavaScript submission where practical. Test valid and invalid submissions, 100+ quantity, invalid product values, long text, duplicates, simulated provider failures and keyboard completion.

## Gallery and independent reviews

Preserve filter labels and active state. Filter accessible figures/cards without hiding all items on script failure. Use descriptive alt text. Existing three images are illustrations and remain labelled; do not call them recent projects. Add client-owned work only with permission, recording an accurate caption and approximate location without publishing a customer's private address.

Keep the independent review link until an exact business profile and permission to republish reviews are verified. Do not invent star counts, ratings, reviewers, dates, project names or a rating widget. Do not borrow ratings from a competitor.

## Maps, cookies and external services

The existing directions link can remain a plain external Google Maps link, requiring no embedded map. No analytics or advertising is required to launch the design. If adding optional tracking or embeds, configure and test a suitable consent mechanism before they load, following current applicable requirements. Offer reject and later withdrawal. A privacy/cookie page is not itself a consent mechanism. Keep external links safe and clearly labelled.

## Business-dependent functionality

No ecommerce checkout, instant price, finance application, appointment booking calendar, CRM subscription or WhatsApp link has been commissioned. Do not invent one or create a new paid service. Leave the quote planner as a tailored enquiry until approved pricing exists. Call-ahead copy replaces unverified opening hours. Ask for actual coverage instead of promising service across all of Kent.
