#!/usr/bin/env python3
"""Convert the approved page HTML into WordPress block markup.

Reads handover/content/pages.json and writes, for each of the 38 routes:

  wp-content/plugins/splendid-core/content/pages/<slug>.html   block markup
  wp-content/plugins/splendid-core/content/manifest.json       import manifest

The conversion turns the design's sections into core blocks wherever WordPress
has an equivalent (group, heading, paragraph, details, separator) and into the
small Splendid blocks where the design needs exact markup (bare images, anchors
that wrap content, the enquiry form, live card grids). The result is editable
in the block editor rather than one opaque HTML blob.

Usage: python3 tools/build-content.py
"""

from __future__ import annotations

import json
import pathlib
import re
from html.parser import HTMLParser

ROOT = pathlib.Path(__file__).resolve().parent.parent
SRC = ROOT / "handover/content/pages.json"
OUT = ROOT / "wp-content/plugins/splendid-core/content"

VOID = {"br", "img", "hr", "input", "source", "path", "circle", "rect", "meta", "link"}
INLINE = {"em", "strong", "b", "i", "br", "span", "small", "a", "svg", "path", "circle", "rect", "sup", "sub", "code"}


# ---------------------------------------------------------------------------
# A very small DOM
# ---------------------------------------------------------------------------
class Node:
    def __init__(self, tag=None, attrs=None, text=None):
        self.tag = tag
        self.attrs = dict(attrs or {})
        self.text = text
        self.children: list["Node"] = []
        self.parent: "Node | None" = None

    def add(self, node):
        node.parent = self
        self.children.append(node)
        return node

    @property
    def classes(self):
        return self.attrs.get("class", "").split()

    def has_class(self, name):
        return name in self.classes

    def find(self, tag):
        for child in self.children:
            if child.tag == tag:
                return child
            found = child.find(tag)
            if found:
                return found
        return None

    def find_all(self, tag, out=None):
        out = [] if out is None else out
        for child in self.children:
            if child.tag == tag:
                out.append(child)
            child.find_all(tag, out)
        return out


class DOM(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=False)
        self.root = Node("root")
        self.stack = [self.root]

    def handle_starttag(self, tag, attrs):
        node = self.stack[-1].add(Node(tag, dict(attrs)))
        if tag not in VOID:
            self.stack.append(node)

    def handle_startendtag(self, tag, attrs):
        self.stack[-1].add(Node(tag, dict(attrs)))

    def handle_endtag(self, tag):
        if tag in VOID:
            return
        for index in range(len(self.stack) - 1, 0, -1):
            if self.stack[index].tag == tag:
                del self.stack[index:]
                return

    def handle_data(self, data):
        if data:
            self.stack[-1].add(Node(None, None, data))

    def handle_entityref(self, name):
        self.stack[-1].add(Node(None, None, f"&{name};"))

    def handle_charref(self, name):
        self.stack[-1].add(Node(None, None, f"&#{name};"))


def parse(html: str) -> Node:
    dom = DOM()
    dom.feed(html)
    dom.close()
    return dom.root


# ---------------------------------------------------------------------------
# Serialising back to HTML (used for inline content inside rich-text blocks)
# ---------------------------------------------------------------------------
def render_inline(node: Node, drop_svg=True) -> str:
    out = []
    for child in node.children:
        if child.tag is None:
            out.append(child.text)
            continue
        if child.tag == "svg":
            if drop_svg:
                continue
            out.append(render_element(child))
            continue
        if child.tag == "br":
            css = child.attrs.get("class")
            out.append(f'<br class="{css}">' if css else "<br>")
            continue
        if child.tag == "a":
            href = child.attrs.get("href", "#")
            out.append(f'<a href="{href}">{render_inline(child, drop_svg)}</a>')
            continue
        if child.tag in INLINE:
            inner = render_inline(child, drop_svg)
            if child.tag == "span" and not inner.strip():
                continue
            out.append(f"<{child.tag}>{inner}</{child.tag}>")
            continue
        out.append(render_inline(child, drop_svg))
    return "".join(out).strip()


# HTMLParser lower-cases attribute names; SVG needs some of them back.
SVG_ATTR_CASE = {"viewbox": "viewBox"}
SVG_TAGS = {"svg", "path", "circle", "rect", "line", "polyline", "polygon", "g"}


def render_element(node: Node, in_svg: bool = False) -> str:
    if node.tag is None:
        return node.text or ""

    in_svg = in_svg or node.tag == "svg"

    pairs = []
    for key, value in node.attrs.items():
        if in_svg and key in SVG_ATTR_CASE:
            key = SVG_ATTR_CASE[key]
        pairs.append(f' {key}="{value}"')
    attrs = "".join(pairs)

    if node.tag in VOID:
        # Inside SVG the parser treats these as foreign content: self-close them
        # so the browser does not nest the following siblings inside them.
        return f"<{node.tag}{attrs} />" if in_svg else f"<{node.tag}{attrs}>"

    inner = "".join(render_element(child, in_svg) for child in node.children)
    return f"<{node.tag}{attrs}>{inner}</{node.tag}>"


def text_of(node: Node) -> str:
    return re.sub(r"\s+", " ", render_inline(node)).strip()


# ---------------------------------------------------------------------------
# Block markup helpers
# ---------------------------------------------------------------------------
def attrs_json(attrs: dict) -> str:
    if not attrs:
        return ""
    return " " + json.dumps(attrs, ensure_ascii=False, separators=(",", ":"))


def block(name: str, attrs: dict, inner: str) -> str:
    return f"<!-- wp:{name}{attrs_json(attrs)} -->\n{inner}\n<!-- /wp:{name} -->"


def void_block(name: str, attrs: dict) -> str:
    return f"<!-- wp:{name}{attrs_json(attrs)} /-->"


def group(node: Node, inner_blocks: list[str]) -> str:
    tag = node.tag if node.tag in ("section", "div", "article", "aside") else "div"
    classes = " ".join(node.classes)
    attrs = {"tagName": tag, "layout": {"type": "default"}}
    if classes:
        attrs["className"] = classes
    if node.attrs.get("id"):
        attrs["anchor"] = node.attrs["id"]

    class_attr = f'wp-block-group{" " + classes if classes else ""}'
    id_attr = f' id="{node.attrs["id"]}"' if node.attrs.get("id") else ""
    inner = f'<{tag} class="{class_attr}"{id_attr}>' + "".join(inner_blocks) + f"</{tag}>"
    return block("group", attrs, inner)


def heading(node: Node) -> str:
    level = int(node.tag[1])
    content = render_inline(node)
    classes = " ".join(node.classes)
    attrs = {"level": level}
    if classes:
        attrs["className"] = classes
    class_attr = f'wp-block-heading{" " + classes if classes else ""}'
    return block("heading", attrs, f'<h{level} class="{class_attr}">{content}</h{level}>')


def paragraph(node: Node, extra_class: str = "") -> str:
    content = render_inline(node)
    classes = [c for c in node.classes]
    if extra_class:
        classes.append(extra_class)
    class_str = " ".join(classes)
    attrs = {"className": class_str} if class_str else {}
    class_attr = f' class="{class_str}"' if class_str else ""
    return block("paragraph", attrs, f"<p{class_attr}>{content}</p>")


def html_block(raw: str) -> str:
    return block("html", {}, raw)


# Links whose destination is a business setting, so the client can change them
# in one place once a verified profile exists.
SETTING_LINKS = {
    "google.com/search": "reviews:",
    "google.com/maps": "maps:",
}


def link_block(node: Node) -> str:
    icon = ""
    size = 18
    svg = node.find("svg")
    if svg is not None:
        classes = svg.attrs.get("class", "")
        match = re.search(r"lucide-([a-z-]+)", classes)
        if match:
            icon = match.group(1)
        size = int(svg.attrs.get("width", 18))

    url = node.attrs.get("href", "/")
    for needle, replacement in SETTING_LINKS.items():
        if needle in url:
            url = replacement
            break

    attrs = {
        "label": text_of(node),
        "url": url,
        "className": " ".join(node.classes),
        "icon": icon,
        "size": size,
    }
    if node.attrs.get("aria-label"):
        attrs["ariaLabel"] = node.attrs["aria-label"]
    if node.attrs.get("target") == "_blank":
        attrs["external"] = True
    return void_block("splendid/link", attrs)


def image_block(node: Node) -> str:
    attrs = {
        "src": node.attrs.get("src", ""),
        "alt": node.attrs.get("alt", ""),
    }
    if node.attrs.get("fetchPriority") or node.attrs.get("fetchpriority"):
        attrs["priority"] = True
    return void_block("splendid/image", attrs)


def details_block(node: Node) -> str:
    summary = node.find("summary")
    question = text_of(summary) if summary else ""
    answer_node = None
    for child in node.children:
        if child.tag == "div":
            answer_node = child
            break
    answer = ""
    if answer_node is not None:
        inner_div = answer_node.find("div")
        answer = text_of(inner_div if inner_div is not None else answer_node)

    inner = (
        f'<details class="wp-block-details"><summary>{question}</summary>'
        + block("paragraph", {}, f"<p>{answer}</p>")
        + "</details>"
    )
    return block("details", {"summary": question}, inner)


# ---------------------------------------------------------------------------
# Section-level special cases
# ---------------------------------------------------------------------------
GALLERY_ALT = {
    "Windows": "Sunlit room with a white bay window framing a garden view",
    "Doors": "Charcoal composite entrance door set into a brick home",
    "Living spaces": "Sliding glass doors opening from a living room onto a garden",
}


def gallery_block(section: Node) -> str:
    items = []
    grid = None
    for node in section.find_all("div"):
        if node.has_class("collection-grid"):
            grid = node
            break
    if grid is not None:
        for card in grid.children:
            if card.tag != "a":
                continue
            img = card.find("img")
            caption = None
            for child in card.children:
                if child.tag == "div" and child.has_class("card-caption"):
                    caption = child
            title = text_of(caption.find("h3")) if caption and caption.find("h3") else ""
            type_span = None
            if caption:
                for child in caption.children:
                    if child.tag == "span":
                        type_span = child
            card_type = text_of(type_span) if type_span else ""
            items.append({
                "type": card_type,
                "title": title,
                "image": img.attrs.get("src", "") if img else "",
                # The prototype gave all three cards the same fallback alt text.
                # Describe each image instead, as the brief requires.
                "alt": GALLERY_ALT.get(card_type, img.attrs.get("alt", "") if img else ""),
                "href": card.attrs.get("href", "/"),
            })

    filters = []
    for node in section.find_all("div"):
        if node.has_class("filter-row"):
            filters = [text_of(button) for button in node.children if button.tag == "button"]
            break

    note = ""
    for node in section.find_all("p"):
        if node.has_class("small-note"):
            note = text_of(node)
            break

    attrs = {"items": items}
    if filters:
        attrs["filters"] = filters
    if note:
        attrs["note"] = note
    return void_block("splendid/gallery", attrs)


def cta_block(section: Node) -> str:
    eyebrow = ""
    head = ""
    text = ""
    label = "Start your project"
    url = "/free-quote"

    for node in section.find_all("p"):
        if node.has_class("eyebrow") and not eyebrow:
            eyebrow = render_inline(node)
        elif not node.has_class("eyebrow") and not text:
            text = render_inline(node)

    h2 = section.find("h2")
    if h2 is not None:
        head = render_inline(h2)

    for node in section.find_all("a"):
        if "button" in node.classes:
            label = text_of(node)
            url = node.attrs.get("href", url)
            break

    return void_block("splendid/cta-band", {
        "eyebrow": eyebrow,
        "heading": head,
        "text": text,
        "ctaLabel": label,
        "ctaUrl": url,
    })


def benefits_block(node: Node) -> str:
    items = []
    for child in node.children:
        if child.tag != "div":
            continue
        svg = child.find("svg")
        icon = "check"
        if svg is not None:
            match = re.search(r"lucide-([a-z-]+)", svg.attrs.get("class", ""))
            if match:
                icon = match.group(1)
        span = None
        for grandchild in child.children:
            if grandchild.tag == "span":
                span = grandchild
        items.append({"icon": icon, "label": text_of(span) if span else ""})
    return void_block("splendid/benefits", {"items": items})


def image_card_block(node: Node) -> str:
    img = node.find("img")
    caption = None
    number = ""
    for child in node.children:
        if child.tag == "div" and child.has_class("card-caption"):
            caption = child
        if child.tag == "div" and child.has_class("card-photo"):
            for grandchild in child.children:
                if grandchild.tag == "span" and grandchild.has_class("card-number"):
                    number = text_of(grandchild)

    title = ""
    tag = ""
    if caption is not None:
        h3 = caption.find("h3")
        title = text_of(h3) if h3 is not None else ""
        for child in caption.children:
            if child.tag == "span":
                tag = text_of(child)

    return void_block("splendid/image-card", {
        "title": title,
        "tag": tag,
        "href": node.attrs.get("href", "/"),
        "src": img.attrs.get("src", "") if img is not None else "",
        "alt": img.attrs.get("alt", "") if img is not None else "",
        "number": number,
    })


def product_grid_block(node: Node, route: str) -> str:
    parent = "windows" if "/windows" in route else "doors"
    if node.has_class("product-grid"):
        first = None
        for child in node.children:
            if child.tag == "a":
                first = child
                break
        if first is not None:
            href = first.attrs.get("href", "")
            match = re.match(r"^/([a-z-]+)/", href)
            if match:
                parent = match.group(1)
    attrs = {"parent": parent}
    if node.parent is not None and node.parent.has_class("tinted"):
        attrs["tinted"] = True
    return void_block("splendid/product-grid", attrs)


# ---------------------------------------------------------------------------
# The recursive converter
# ---------------------------------------------------------------------------
def convert_children(node: Node, route: str) -> list[str]:
    out = []
    for child in node.children:
        converted = convert(child, route)
        if converted:
            out.append(converted)
    return out


def convert(node: Node, route: str) -> str:
    # Loose text between elements (the breadcrumb's current page, for example).
    if node.tag is None:
        text = (node.text or "").strip()
        return html_block(text) if text else ""

    if node.tag in ("h1", "h2", "h3", "h4"):
        return heading(node)

    if node.tag == "p":
        if node.find("svg") is not None and node.parent is not None and node.parent.has_class("details-box"):
            return paragraph(node, "feature")
        return paragraph(node)

    if node.tag == "a":
        return link_block(node)

    if node.tag == "img":
        return image_block(node)

    if node.tag == "hr":
        return block("separator", {}, '<hr class="wp-block-separator has-alpha-channel-opacity"/>')

    if node.tag == "details":
        return details_block(node)

    if node.tag == "form":
        variant = "planner" if "estimate-calculator" in route else ("quote" if "free-quote" in route else "contact")
        return void_block("splendid/enquiry-form", {"variant": variant})

    if node.tag in ("span", "small", "output", "button", "svg", "label", "input", "textarea", "select"):
        rendered = render_element(node).strip()
        return html_block(rendered) if rendered else ""

    if node.tag in ("section", "div", "article", "aside"):
        # Whole-section replacements first.
        if node.has_class("cta-band"):
            return cta_block(node)
        if node.has_class("benefits-bar"):
            return benefits_block(node)
        if node.has_class("gallery-section"):
            # The gallery block renders the filters, the cards and the note.
            heading_nodes = [
                child for child in node.children
                if child.tag in ("h1", "h2", "h3", "p") and not child.has_class("small-note")
            ]
            return group(node, [convert(child, route) for child in heading_nodes] + [gallery_block(node)])
        if node.has_class("image-card"):
            return image_card_block(node)
        if node.has_class("product-grid"):
            return product_grid_block(node, route)
        if node.has_class("area-links"):
            count = len([child for child in node.children if child.tag == "a"])
            return void_block("splendid/area-links", {"limit": count or 6})
        if node.has_class("contact-methods"):
            return void_block("splendid/contact-methods", {})
        if node.has_class("quote-panel"):
            variant = "planner" if "estimate-calculator" in route else ("quote" if "free-quote" in route else "contact")
            return void_block("splendid/enquiry-form", {"variant": variant})

        return group(node, convert_children(node, route))

    # Anything unexpected keeps its markup rather than being dropped.
    rendered = render_element(node).strip()
    return html_block(rendered) if rendered else ""


def convert_page(body_html: str, route: str) -> str:
    root = parse(body_html)
    return "\n\n".join(convert_children(root, route))


# ---------------------------------------------------------------------------
# Manifest
# ---------------------------------------------------------------------------
DRAFT_TEMPLATES = {
    # Local pages stay unpublished until the client confirms real coverage.
    "local-area",
    # Legal notices stay unpublished until the configuration is final and reviewed.
    "legal",
}

LEGAL_ROUTES = {"/privacy-policy", "/cookie-policy", "/terms-of-service"}


def route_parts(route: str):
    """Return (parent_slug, slug) for a route."""
    trimmed = route.strip("/")
    if trimmed == "":
        return None, "home"
    if "/" in trimmed:
        parent, slug = trimmed.split("/", 1)
        return parent, slug
    return None, trimmed


def build_meta(route: str, template: str, data: dict) -> dict:
    meta = {"_splendid_template": template}
    parent, slug = route_parts(route)

    if template == "product":
        group = "windows" if parent == "windows" else "doors"
        for product in data[group]:
            if product["slug"] == slug:
                meta["_splendid_tag"] = product["tag"]
                break

    if template == "article":
        for article in data["articles"]:
            if article["slug"] == slug:
                meta["_splendid_category"] = article["category"]
                break

    if template == "local-area":
        for area in data["areas"]:
            area_slug, area_name = area[0], area[1]
            if slug == f"windows-doors-{area_slug}":
                meta["_splendid_area_name"] = area_name
                break

    return meta


def build_excerpt(route: str, template: str, data: dict) -> str:
    parent, slug = route_parts(route)

    if template == "product":
        group = "windows" if parent == "windows" else "doors"
        for product in data[group]:
            if product["slug"] == slug:
                return product["text"]

    if template == "article":
        for article in data["articles"]:
            if article["slug"] == slug:
                return article["intro"]

    return ""


def main():
    pages = json.loads(SRC.read_text())
    data = json.loads((ROOT / "handover/content/original-data.json").read_text())

    (OUT / "pages").mkdir(parents=True, exist_ok=True)

    manifest = []

    for order, page in enumerate(pages):
        route = page["route"]
        parent, slug = route_parts(route)
        template = page["template"]

        filename = f"{parent + '--' if parent else ''}{slug}.html"

        if route in LEGAL_ROUTES:
            # Legal bodies come from briefs/04, never from the prototype notices.
            source = ROOT / "content/legal" / f"{slug}.html"
            blocks = source.read_text() if source.exists() else ""
        else:
            blocks = convert_page(page["body_html"], route)

        (OUT / "pages" / filename).write_text(blocks)

        manifest.append({
            "route": route,
            "slug": slug,
            "parent": parent,
            "title": page["title"],
            "template": template,
            "file": filename,
            "seo_title": page["seo_title"],
            "meta_description": page["meta_description"],
            "status": "draft" if template in DRAFT_TEMPLATES else "publish",
            "menu_order": order,
            "excerpt": build_excerpt(route, template, data),
            "meta": build_meta(route, template, data),
            "front_page": route == "/",
        })

    (OUT / "manifest.json").write_text(json.dumps(manifest, ensure_ascii=False, indent=1))

    published = sum(1 for item in manifest if item["status"] == "publish")
    print(f"wrote {len(manifest)} pages ({published} published, {len(manifest) - published} draft)")


if __name__ == "__main__":
    main()
