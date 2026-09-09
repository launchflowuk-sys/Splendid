# Every navigation destination and page template

Read content/SITEMAP.md for the complete 38-route inventory. All links below must resolve to real WordPress content, with the same active site origin. The site logo links home.

## Desktop header, in order

| Item | Destination / children |
|---|---|
| Windows | /windows; dropdown: Explore all windows, Double glazing, Triple glazing, uPVC windows, Aluminium windows, Sash windows, Bay windows |
| Doors | /doors; dropdown: Explore all doors, Composite doors, uPVC doors, Aluminium doors, Bifold doors, Sliding patio doors, French doors |
| Conservatories | /conservatories |
| Inspiration | /gallery |
| Our story | /about |
| Let’s talk about your home | /free-quote |

Product child URLs are in the sitemap. Keep dropdown eyebrows “FRAME YOUR EVERYDAY” and “MAKE AN ENTRANCE”. Parent links remain navigable; separate accessible toggle buttons can open the child lists on touch and keyboard.

## Mobile drawer

Source order: Windows, Doors, Conservatories, Porches, Inspiration, Our story, Areas we cover, Advice, Contact, Start your quote. Add expandable Windows/Doors children so all product pages are reachable on mobile without requiring a visit to the hubs. Keep the drawer scrollable, trap focus when open, close with Escape or its close button, restore trigger focus and close after following a link. Use a labelled menu button with aria-expanded and aria-controls.

## Footer

| Column | Content in order |
|---|---|
| Brand/contact | Brand; “Beautifully considered glazing. A brighter way to live.”; phone; email |
| Windows | Double glazing; Triple glazing; uPVC windows; Aluminium windows; Sash windows; Bay windows |
| Doors & more | Composite doors; uPVC doors; Aluminium doors; Bifold doors; Sliding patio doors; French doors; Conservatories; Porches |
| Discover Splendid | Our story; Inspiration gallery; Customer reviews; Advice & ideas; Areas we cover; Quote planner; Contact us |

Bottom row: current copyright year, Splendid Double Glazing Ltd, company no. 12437986, registered address. Privacy → /privacy-policy; Cookies → /cookie-policy; Terms → /terms-of-service. Include Registered in England and Wales. Add VAT number only if supplied and applicable. If optional cookies are used, provide a functional Cookie settings action alongside the cookie-policy link.

## Template families and content mapping

| Family | Required composition and behaviour |
|---|---|
| Home | Exact section order in design brief; copy from home.md. |
| Product hubs | Split inner hero, collection heading, six linked product cards, FAQ, CTA. |
| 12 products | Split hero; distinct product heading/introduction; detailed paragraph; feature list panel; enquiry preselected to that exact product; related hub link; FAQ; CTA. Use all text/features in original-data.json. |
| Conservatories | Split hero; Victorian, Edwardian, Lean-to and Contemporary idea cards; planning section; FAQ; CTA. Specific supply availability needs confirmation. |
| Porches | Split hero; Glazed entrance, Brick and glazing, A complete welcome cards; planning section; FAQ; CTA. |
| Our story | Split hero; company approach; real legal identity; contact link; CTA. Do not invent founder, years of trading or staff biographies. |
| Areas hub | Intro; all seven area links; Windows/Doors/Conservatories cards; CTA. |
| Seven area pages | Distinct provided local intro, enquiry guidance, area links, product cards, CTA. Confirm actual coverage. Add genuine local project evidence only when supplied. |
| Inspiration | Heading; All/Windows/Doors/Living spaces filter buttons; three current image cards; visible illustrative-image note; CTA. Keep an empty state when a real gallery category has no items. |
| Reviews | Heading and independent-feedback guidance; correct business search link; CTA. Replace generic search with a verified direct review-profile URL if available. No invented testimonial cards or rating. |
| Advice hub | Heading; three original article cards, categories and intros; CTA. |
| Three articles | Category eyebrow; unique title/intro; three complete sections; All advice link; CTA. Preserve /blog/article-slug. No false author or publication history. |
| Contact / free quote | Contact details at left, real enquiry form at right, FAQ beneath. |
| Quote planner | Planner heading, same core form with product/material/quantity, explanatory price note; real enquiry submission. |
| Legal | Heading and readable narrow article body. Replace prototype notices using legal brief. |
| 404 | A DIFFERENT VIEW; “Let’s find your way home.”; helpful explanation; green Back to Splendid CTA; HTTP 404. |

## Editable WordPress content

All headings, paragraphs, labels, features, FAQs, images, alt text, contact data and CTA text must be editable. Register main/mobile/footer menus or equivalent Site Editor navigation blocks. Store contact details once in site settings and use the same values across header, footer, contact and schema.

Use pages for service/area/legal content. For advice, either use native posts with /blog/%postname%/ or hierarchical pages beneath /blog; preserve URLs and ensure the hub lists them. The provided starter importer uses hierarchical pages for deterministic paths. If converting them to posts, migrate the existing imported IDs deliberately and prevent duplicate paths. Avoid using a /blog page as both a custom editable hub and a posts-page template without planning that interaction.
