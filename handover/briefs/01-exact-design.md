# Preserve the approved green design

The actual CSS is supplied. This document explains the visual intent and key measurements; when abbreviated here, inspect the original selector and its later overrides. Do not infer dimensions from a screenshot when source values are available.

## Tokens

| Role | Value |
|---|---|
| Main brand green / CTAs / italic emphasis | #18743c |
| Deep green / primary text / top strip / CTA band | #123c2b |
| Footer background | #0b281c |
| Page background | #ffffff |
| Subtle section background | #f3f5f4 |
| Rules and card borders | #dce2e3 |
| Body paragraph colour | #5b686d |
| Light green emphasis on dark sections | #c7ebbd |
| Button hover green | #115b2e |
| Visible keyboard focus | #258449 |

Use the bundled fonts at assets/fonts/sans.ttf and serif.ttf, registered as SplendidSans and SplendidSerif. The source identifies them as DM Sans and DM Serif Display; verify embedded family/style names and redistribute with the required font licence. The exact bytes take precedence over a similarly named web-font replacement. The serif is used in italic emphasis, not for every heading. Base body 16px/1.65; paragraph line-height ranges 1.8–1.95. Final overrides make body content at least 16px and principal labels/buttons 14px.

## Header and hero

Desktop top strip 35px high, deep green, three aligned facts. White sticky header 105px, horizontal padding 4.4%, with subtle bottom rule and background blur. Brand is the supplied text treatment: “splendid” with raised dot and spaced DOUBLE GLAZING below; 43px main word at desktop. It is a proposed wordmark, not a verified pre-existing business logo. Obtain brand approval or replace using an approved asset without changing its layout footprint.

Desktop hero uses 49%/51% columns, padding 28px 28px 65px 4.4%, minimum height 720px. Main heading uses clamp(4rem, 6.4vw, 7rem), line-height 1.01, letter-spacing -.06em. Preserve “A brighter / way to live.” and italic green “live.” Photograph height 620px, 9px radius, object-position 62% centre. Overlay label, bottom caption, circular link and perspective label stay aligned inside the image. The main CTA and secondary text link stack at ordinary desktop widths; at 1500px and above the source allows a row. Do not force them into one cramped line.

Buttons have approximately 54px minimum height, 17px 23px padding, 6px radius and restrained hover lift. Cards generally use 7–10px radii. The enquiry card is 12px. Retain this restrained rounding rather than changing every panel to a large pill.

## Home section order

1. Split hero with local address cue and scroll cue.
2. Four-item benefit strip: Made for your home; Light-filled living; Considered materials; Your local specialists.
3. Collection heading and three photographic cards: Windows, Doors, Living spaces.
4. Two-column story panel with bay-window photograph and local-team copy.
5. Four numbered process columns: Let’s talk; Make it yours; Get the details right; Enjoy the difference.
6. Wide architectural photograph with white/green overlay heading and garden-door link.
7. Local areas heading and six area links (full seven-item list on service-area pages).
8. FAQ section with editorial heading at left and accordions at right.
9. Deep-green project CTA band.
10. Four-column dark-green footer and legal row.

Section spacing: default 95px vertical and 6% horizontal; collection gap 25px; three image cards 350px tall at normal desktop. Story image min-height 610px; copy padding 65px 55px. Wide photograph min-height 570px. CTA band 85px 6%. Footer 70px 6% 25px.

## Responsive cascade

| Breakpoint | Source behaviour |
|---|---|
| >=1500px | Hero min-height 800px; image 700px; large heading 7.4rem; collection images 430px. |
| <=1150px | Tighter header, 48/52 hero split, image 575px, reduced text/layout gaps. |
| <=950px | Desktop nav hidden; mobile drawer shown; header 85px; product grid two columns. |
| <=700px | Header 78px; stacked hero and all major split sections; image 440px; collection and product grids single column; process two columns; area links two columns. |

At mobile: hero padding 44px 5% 50px; hero heading clamp(3.9rem,13.2vw,6rem); image object-position 58% centre; section padding 58px 5%; image cards 380px; CTA padding 60px 6%. The header CTA becomes “Get a quote”. Preserve touch space and avoid horizontal scrolling at 320px. Desktop reading composition must remain spacious on a 1440px screen.

## Motion and accessibility

Source uses a 1.7s subtle hero zoom, 0.7s reveal, 0.7s image hover zoom and approximately 0.25s button transitions. Recreate lightly. Content must remain visible when JavaScript fails. IntersectionObserver should reveal each section once. Respect prefers-reduced-motion. Add touch and keyboard submenu controls; do not require hover. Keep visible focus and a Skip to content link. Account for the WordPress admin bar in sticky offsets. Correct the original 404 button’s obsolete “navy” class to the green button class.
