# Evidence and asset register

## Captured design

Selected project: Splendid Double Glazing, appgprj_6aa19ddfe090819199eb07b8de306eb4. Local source commit d7052cd685806d2b0eae919f5c37eced3d256690, “Apply green Splendid brand palette throughout site”. The working tree was clean when captured. The Sites record reported latest version 2. The package was built from that retained source, not a guessed reconstruction of a screenshot.

The source includes all 38 routes. Page HTML exports are content snapshots: accordion answers are expanded and UI wrappers simplified for extraction. They are not a working WordPress theme or a pixel-tested browser reproduction. The original TSX/CSS remain authoritative for interaction and layout; the production brief explicitly changes the form and legal notices.

## Business and implementation sources

- Official business website: https://www.splendidglazing.co.uk/ — search result on 9 September 2026 exposed phone, email, service categories, materials and design-to-installation positioning. Direct open encountered a verification screen.
- Companies House: https://find-and-update.company-information.service.gov.uk/company/12437986 — registered identity, company number, address and incorporation date. The returned page may be cached; check status again at launch if needed.
- WordPress theme handbook: https://developer.wordpress.org/themes/ — implementation reference for custom theme architecture.
- WP-CLI command reference: https://developer.wordpress.org/cli/commands/post/create/ — page creation reference; installed CLI help is authoritative for the target environment.
- ICO privacy information guidance: https://ico.org.uk/for-organisations/uk-gdpr-guidance-and-resources/individual-rights/the-right-to-be-informed/what-privacy-information-should-we-provide/
- ICO cookie guidance: https://ico.org.uk/for-organisations/direct-marketing-and-privacy-and-electronic-communications/guide-to-pecr/cookies-and-similar-technologies/

Competitor/reference notes from the earlier build are retained in reference-source/RESEARCH.md for provenance, not as evidence for Splendid-specific facts. Its old colour description is superseded by the actual green CSS. No competitor copy or photographs need to be obtained for the WordPress build.

## Supplied assets

| File | Intended placement | Status |
|---|---|---|
| assets/images/sliding-glazing.webp | Homepage hero, wide garden-door feature, living-space inspiration, garden-door inner hero | Original AI-generated architectural illustration; not completed client work. |
| assets/images/bay-window.webp | Windows collection, story image, window/about/area inner heroes | Original AI-generated architectural illustration. |
| assets/images/composite-door.webp | Doors collection and entrance/porch inner heroes | Original AI-generated architectural illustration. |
| assets/fonts/sans.ttf | Main text and sans-serif headings | Exact source font bytes; retain embedded licence notices. |
| assets/fonts/serif.ttf | Italic heading emphasis | Exact source font bytes; retain embedded licence notices. |
| assets/favicon.svg | Browser favicon reference | Source-designed icon; confirm branding. |
| assets/design.css | Extracted custom CSS beginning at font declarations | Reference stylesheet, not an installable theme. Rewrite root-relative URLs. |

Additional authentic assets the client may supply: master logo, owner/team portrait if desired, product-specific manufacturer-approved images, installation photographs with permission, verified accreditation artwork and review-profile link. None should be fabricated. No generated image in this package should be represented as an actual installation.

Lucide icon shapes are referenced in the original source through lucide-react. When porting icons, use the official package's licence notices. The reference-source dependency manifest is for understanding the original implementation; installing its entire dependency list is not necessary for WordPress.
