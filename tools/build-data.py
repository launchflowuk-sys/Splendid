#!/usr/bin/env python3
"""Regenerate the plugin's data.php from the handover's original-data.json.

Usage: python3 tools/build-data.py
"""
import json
import pathlib

ROOT = pathlib.Path(__file__).resolve().parent.parent
SRC = ROOT / "handover/content/original-data.json"
DEST = ROOT / "wp-content/plugins/splendid-core/includes/data.php"


def php(value, indent=1):
    pad = "\t" * indent
    if isinstance(value, dict):
        out = "array(\n"
        for k, v in value.items():
            out += f"{pad}\t'{k}' => {php(v, indent + 1)},\n"
        return out + pad + ")"
    if isinstance(value, list):
        out = "array(\n"
        for v in value:
            out += f"{pad}\t{php(v, indent + 1)},\n"
        return out + pad + ")"
    if isinstance(value, bool):
        return "true" if value else "false"
    if isinstance(value, (int, float)):
        return str(value)
    s = str(value).replace("\\", "\\\\").replace("'", "\\'")
    return f"'{s}'"


HEADER = """<?php
/**
 * Product, area, article and FAQ data from the approved design.
 *
 * Generated from handover/content/original-data.json by tools/build-data.py.
 * These values seed the imported pages and validate enquiry submissions; the
 * published copy afterwards lives in the pages themselves and is editable.
 *
 * @package Splendid_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * The full source dataset.
 *
 * @return array
 */
function splendid_data() {
\treturn """


def main():
    data = json.loads(SRC.read_text())
    DEST.write_text(HEADER + php(data, 1) + ";\n}\n")
    print(f"wrote {DEST.relative_to(ROOT)}")


if __name__ == "__main__":
    main()
