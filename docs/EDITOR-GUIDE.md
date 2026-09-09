# Editing the Splendid website

A short guide for whoever maintains the site day to day. No code needed for
anything in here.

## Changing the phone number, email or address

**Settings → Splendid.** Change it once and it updates the top strip, the footer,
the contact page, the enquiry emails and the site's structured data together.
Don't retype these into page content — they are pulled in automatically.

## Editing a page

**Pages → All Pages**, open the page, edit the text directly. The page is built
from ordinary blocks: headings, paragraphs, groups and accordions. Click any
text and type.

The green italic emphasis (like *live.* in the homepage headline) is the
**Italic** format inside a heading — select the words and press Ctrl/Cmd+I.

## The Splendid blocks

Insert them with the **+** button, under **Splendid**:

| Block | What it does |
|---|---|
| Splendid enquiry form | The real enquiry form. Choose Contact, Free quote or Quote planner in the sidebar. |
| Splendid CTA band | The closing green call-to-action. |
| Splendid contact methods | Phone, email and directions, from Settings → Splendid. |
| Splendid inspiration grid | Filterable image cards. Edit the images and the note in the sidebar. |
| Splendid product grid | Lists the product pages under a hub — add a product page and a card appears. |
| Splendid area links | Lists the **published** local area pages. |
| Splendid article grid | Lists the published advice articles. |
| Splendid link | A link or button with the design's arrow. Choose the style in the sidebar. |
| Splendid image | A plain image, for the design's full-bleed photographs. |
| Splendid image card | A photographic card that links to another page. |
| Splendid benefit strip | The four-item strip under the hero. |

## Adding a product

Create a page **under** `/windows` or `/doors` (set the parent under
Page → Parent). Fill in:

- the page title — becomes the card title
- the **excerpt** — becomes the card description
- the custom field `_splendid_tag` — the small label above the card title

It then appears in the hub's product grid, the header dropdown (once added to
the menu) and the footer.

## Adding an advice article

Create a page under `/blog`. Set the excerpt and, optionally, the
`_splendid_category` field for the eyebrow label. It appears on `/blog`.

## Menus

**Appearance → Menus.** Six menus: Main navigation, Mobile drawer, Windows,
Doors & more, Discover Splendid and Legal. On the main navigation, a top-level
item's **Description** field is the small green eyebrow above its dropdown, and
its **Title Attribute** is the "Explore all…" link at the top of the dropdown.

## Search appearance

Each page has a **Search appearance** panel under the editor: the search engine
title, the meta description and a "don't index this page" checkbox. Leave the
title empty to use the page title.

## Enquiries

Enquiries are emailed to the address in **Settings → Splendid → Enquiries**. If
storage is switched on, they also appear under **Enquiries** in the admin menu,
visible to administrators only.

If the settings screen warns that no mail transport is configured, enquiries are
refused with a "please try again" message rather than silently lost. Fix the
mail configuration before relying on the form.

## Things to leave alone

- The illustrative-image note under the inspiration grid, while those images are
  design illustrations rather than photographs of real Splendid work.
- The quote planner's price note. The planner does not calculate a price and
  should not imply one.
- The three legal notices, until the business has reviewed the final wording.
