# Route completion matrix

All 38 routes from `content/SITEMAP.md` are built and import into WordPress with their own page copy. 28 import published; 10 import as drafts on purpose (see notes).

Verified on a local WordPress 7.1 install with the theme and plugin active: every route returned HTTP 200 with no PHP notice, warning or fatal, and `/does-not-exist` returned a real 404.

Plus, outside the 38 content routes:

- a real HTTP 404 template (`404.php`), returning status 404
- an inline enquiry confirmation state (no separate thank-you URL to index)

| # | Route | Template | Imported as | Notes |
|---|---|---|---|---|
| 1 | `/` | home | publish |  |
| 2 | `/windows` | product-hub | publish |  |
| 3 | `/windows/double-glazing` | product | publish |  |
| 4 | `/windows/triple-glazing` | product | publish |  |
| 5 | `/windows/upvc-windows` | product | publish |  |
| 6 | `/windows/aluminium-windows` | product | publish |  |
| 7 | `/windows/sash-windows` | product | publish |  |
| 8 | `/windows/bay-windows` | product | publish |  |
| 9 | `/doors` | product-hub | publish |  |
| 10 | `/doors/composite-doors` | product | publish |  |
| 11 | `/doors/upvc-doors` | product | publish |  |
| 12 | `/doors/aluminium-doors` | product | publish |  |
| 13 | `/doors/bifold-doors` | product | publish |  |
| 14 | `/doors/patio-doors` | product | publish |  |
| 15 | `/doors/french-doors` | product | publish |  |
| 16 | `/conservatories` | living-space | publish |  |
| 17 | `/porches` | living-space | publish |  |
| 18 | `/about` | about | publish |  |
| 19 | `/service-areas` | areas-hub | publish |  |
| 20 | `/gallery` | gallery | publish |  |
| 21 | `/reviews` | reviews | publish |  |
| 22 | `/blog` | advice-hub | publish |  |
| 23 | `/contact` | enquiry | publish |  |
| 24 | `/free-quote` | enquiry | publish |  |
| 25 | `/estimate-calculator` | planner | publish |  |
| 26 | `/privacy-policy` | legal | draft | Draft until hosting, mail, storage and retention are confirmed and the business has reviewed the wording. |
| 27 | `/cookie-policy` | legal | draft | Draft until hosting, mail, storage and retention are confirmed and the business has reviewed the wording. |
| 28 | `/terms-of-service` | legal | draft | Draft until hosting, mail, storage and retention are confirmed and the business has reviewed the wording. |
| 29 | `/windows-doors-sidcup` | local-area | draft | Draft + noindex until the client confirms survey/installation coverage and supplies genuine local evidence. |
| 30 | `/windows-doors-eltham` | local-area | draft | Draft + noindex until the client confirms survey/installation coverage and supplies genuine local evidence. |
| 31 | `/windows-doors-bexley` | local-area | draft | Draft + noindex until the client confirms survey/installation coverage and supplies genuine local evidence. |
| 32 | `/windows-doors-bromley` | local-area | draft | Draft + noindex until the client confirms survey/installation coverage and supplies genuine local evidence. |
| 33 | `/windows-doors-chislehurst` | local-area | draft | Draft + noindex until the client confirms survey/installation coverage and supplies genuine local evidence. |
| 34 | `/windows-doors-south-east-london` | local-area | draft | Draft + noindex until the client confirms survey/installation coverage and supplies genuine local evidence. |
| 35 | `/windows-doors-kent` | local-area | draft | Draft + noindex until the client confirms survey/installation coverage and supplies genuine local evidence. |
| 36 | `/blog/choosing-your-window-material` | article | publish |  |
| 37 | `/blog/bifold-or-sliding-doors` | article | publish |  |
| 38 | `/blog/planning-your-window-replacement` | article | publish |  |
