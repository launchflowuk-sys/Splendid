#!/usr/bin/env python3
"""Write the three legal pages as WordPress block markup.

The bodies come from handover/briefs/04-legal-and-client-confirmations.md, not
from the prototype's notices: the prototype described a browser-only email draft
and ChatGPT hosting, neither of which is true of the WordPress site.

Bracketed markers are deliberate. They mark the facts only the client can
confirm, and the importer keeps these three pages as drafts so nothing
bracketed can reach the live site.

Usage: python3 tools/build-legal.py
"""

import json
import pathlib

ROOT = pathlib.Path(__file__).resolve().parent.parent
OUT = ROOT / "content/legal"


def attrs(data):
    return " " + json.dumps(data, ensure_ascii=False, separators=(",", ":")) if data else ""


def heading(text, level=2):
    return (
        f'<!-- wp:heading {{"level":{level}}} -->\n'
        f'<h{level} class="wp-block-heading">{text}</h{level}>\n'
        f"<!-- /wp:heading -->"
    )


def paragraph(text, className=""):
    a = {"className": className} if className else {}
    c = f' class="{className}"' if className else ""
    return f"<!-- wp:paragraph{attrs(a)} -->\n<p{c}>{text}</p>\n<!-- /wp:paragraph -->"


def group(tag, className, inner):
    a = {"tagName": tag, "className": className, "layout": {"type": "default"}}
    body = "".join(inner)
    return (
        f"<!-- wp:group{attrs(a)} -->\n"
        f'<{tag} class="wp-block-group {className}">{body}</{tag}>\n'
        f"<!-- /wp:group -->"
    )


def page(title, sections):
    head = group("section", "page-heading", [
        paragraph("THE DETAILS", "eyebrow"),
        '<!-- wp:heading {"level":1} -->\n'
        f'<h1 class="wp-block-heading">{title}</h1>\n'
        "<!-- /wp:heading -->",
    ])

    body_blocks = []
    for section in sections:
        if section[0] == "h":
            body_blocks.append(heading(section[1]))
        else:
            body_blocks.append(paragraph(section[1]))

    body = group("div", "article-body", body_blocks)
    return head + "\n\n" + body + "\n"


BUSINESS = (
    "Splendid Double Glazing Ltd<br>Company number 12437986<br>"
    "758 Sidcup Road, London, SE9 3NS<br>info@splendidglazing.co.uk"
)

PRIVACY = [
    ("p", "Last updated: [PUBLICATION_DATE]"),
    ("h", "Who we are"),
    ("p", "Splendid Double Glazing Ltd, company number 12437986, is responsible for the personal information described in this notice. Our registered address is 758 Sidcup Road, London, SE9 3NS. You can contact us about privacy at info@splendidglazing.co.uk or by writing to that address."),
    ("h", "Information you provide"),
    ("p", "When you contact us or submit a website enquiry, you may provide your name, email address, telephone number, postcode and details of your proposed work. Please avoid including information about other people unless it is needed for your enquiry. We also receive the correspondence needed to discuss your requirements and, if you proceed, your project."),
    ("h", "How we use information"),
    ("p", "We use enquiry information to respond, discuss requirements, prepare quotations and arrange agreed next steps. Where you ask us to take steps towards a contract, our proposed basis is taking those steps at your request. We use customer information to manage an agreed contract and meet applicable record-keeping obligations. We may process limited technical information to keep the website secure and address misuse, where supported by a documented legitimate-interest assessment. [CONFIRM THE ACTUAL PURPOSES AND BASES; REMOVE INAPPLICABLE ITEMS.]"),
    ("p", "We will not treat submitting a quote enquiry as agreement to receive unrelated marketing. If we offer optional marketing, we will explain the choice separately and how to stop receiving it."),
    ("h", "Website forms and service providers"),
    ("p", "Our WordPress enquiry forms send your details to our configured enquiry service. [STATE WHETHER ENQUIRIES ARE ALSO STORED IN WORDPRESS OR A CRM.] Access is restricted to authorised people who need it. We use [IDENTIFY HOSTING, MAIL, FORM/CRM AND OTHER RELEVANT PROVIDERS OR CLEAR RECIPIENT CATEGORIES] to operate the website and handle enquiries. These providers process information for the purposes described here under the relevant arrangements."),
    ("h", "Transfers outside the UK"),
    ("p", "[STATE ACTUAL PROCESSING LOCATIONS. IF RELEVANT, EXPLAIN THE APPLICABLE TRANSFER ARRANGEMENT AND HOW TO OBTAIN INFORMATION ABOUT SAFEGUARDS. DO NOT ASSERT UK-ONLY STORAGE WITHOUT EVIDENCE.]"),
    ("h", "How long we keep information"),
    ("p", "We keep unsuccessful or inactive enquiries for [APPROVED PERIOD OR EXPLAINED CRITERIA]. Customer and transaction records are retained for [APPROVED PERIOD/CRITERIA AND REASON]. Technical security logs are retained for [APPROVED PERIOD]. We then delete or anonymise information unless a continuing legal reason requires retention. Backup deletion follows [APPROVED BACKUP CYCLE]."),
    ("h", "Your choices and rights"),
    ("p", "Depending on the circumstances, you can ask to access or correct your information, request deletion or restriction, object to certain uses, or ask for a portable copy where that right applies. Where processing depends on consent, you can withdraw it without affecting processing that happened before withdrawal. Contact us using the details above; we may need to verify your identity before acting on a request."),
    ("p", 'You can raise a concern with the Information Commissioner&#8217;s Office. Information about complaints is available at <a href="https://ico.org.uk/make-a-complaint/">ico.org.uk</a>.'),
    ("h", "Cookies and other websites"),
    ("p", "See our cookie notice for information about technologies used on this website. Following a directions or review link takes you to a separate service with its own privacy information. [IF EMBEDS OR ANALYTICS ARE USED, ENSURE THIS DESCRIPTION MATCHES THEIR ACTUAL BEHAVIOUR.]"),
    ("h", "Changes to this notice"),
    ("p", "We will update this page if the way we handle information changes. The date above records the latest update."),
    ("h", "Business details"),
    ("p", BUSINESS),
]

COOKIES = [
    ("p", "Last updated: [PUBLICATION_DATE]"),
    ("p", "Cookies and similar technologies can store or access information on your device. This notice describes the technologies actually used on the Splendid Double Glazing website."),
    ("h", "What this website uses"),
    ("p", "[INSERT A COMPLETED COOKIE/STORAGE INVENTORY AFTER INSPECTING THE DEPLOYED BUILD. INCLUDE NAME, PROVIDER, PURPOSE, DURATION AND CATEGORY. DO NOT PUBLISH EXAMPLE ROWS AS IF DETECTED.]"),
    ("p", "The public website is designed to operate without advertising trackers. [ONLY RETAIN THIS SENTENCE IF TRUE.] Logged-in administration may use technologies for authentication that do not apply in the same way to an ordinary visitor. The enquiry planner [STATE WHETHER SELECTIONS EXIST ONLY IN MEMORY OR ARE STORED]."),
    ("h", "Your choices"),
    ("p", "[IF OPTIONAL COOKIES EXIST:] You can accept or reject optional cookies using the consent controls and change your choice through Cookie settings in the footer. Optional technologies covered by those controls do not load before the required choice. Rejecting optional cookies does not stop you using the basic website or submitting an enquiry."),
    ("p", "[IF NONE EXIST:] We do not currently use optional analytics or advertising cookies on the public website. If that changes, we will update this notice and introduce the required controls before using them."),
    ("p", "You can also manage cookies through your browser. Blocking some necessary technologies may affect features that depend on them."),
    ("h", "External services and contact"),
    ("p", "Directions and review links open external services whose own notices apply. For questions about this website, contact info@splendidglazing.co.uk or write to Splendid Double Glazing Ltd, 758 Sidcup Road, London, SE9 3NS."),
    ("h", "Business details"),
    ("p", BUSINESS),
]

TERMS = [
    ("p", "Last updated: [PUBLICATION_DATE]"),
    ("h", "About this website"),
    ("p", "This website is operated by Splendid Double Glazing Ltd, company number 12437986, whose registered address is 758 Sidcup Road, London, SE9 3NS. Contact us at info@splendidglazing.co.uk or on 020 7998 6802."),
    ("h", "Information and enquiries"),
    ("p", "The website introduces glazing ideas and possible product options. Available products, specifications, performance, colours, dimensions, installation scope, guarantees, prices and timing must be confirmed in your individual written quotation. Sending an enquiry does not place an order or reserve an installation date. The quote planner collects your requirements and does not calculate a binding price."),
    ("h", "Your project"),
    ("p", "Before ordering, agree the specification, measurements, installation responsibilities, price and applicable contract terms with the business. Any necessary permissions and project responsibilities should be clarified in writing. Separate agreed terms apply to purchases and installations. These website terms do not replace that contract or remove rights that cannot lawfully be excluded."),
    ("h", "Images and other material"),
    ("p", "Architectural design illustrations are labelled as inspiration and are not evidence of completed Splendid installations or a promise that a pictured specification is available. Genuine project photographs, where shown, are identified separately. Website materials may be protected by intellectual-property rights. Please contact us if you want to reuse material beyond uses permitted by law."),
    ("h", "External links and availability"),
    ("p", "We link to some independent services, such as directions and reviews, for convenience. Those services have their own terms and privacy information. We may update website information as products and services change. If information is important to your decision, ask us to confirm it directly."),
    ("h", "Questions or concerns"),
    ("p", "Contact us using the details above if you notice an error or have a concern about the website. For a concern about an installation or purchase, provide the project details so it can be handled under the relevant customer arrangements."),
    ("h", "Business details"),
    ("p", BUSINESS),
]


def main():
    OUT.mkdir(parents=True, exist_ok=True)
    (OUT / "privacy-policy.html").write_text(page("Privacy notice", PRIVACY))
    (OUT / "cookie-policy.html").write_text(page("Cookie notice", COOKIES))
    (OUT / "terms-of-service.html").write_text(page("Website terms", TERMS))
    print("wrote 3 legal pages")


if __name__ == "__main__":
    main()
