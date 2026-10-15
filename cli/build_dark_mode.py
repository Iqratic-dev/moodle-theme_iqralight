#!/usr/bin/env python3
# This file is part of Moodle - https://moodle.org/
#
# Moodle is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Moodle is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

"""Builds scss/iqra-dark-core.scss, the dark overrides of Moodle's own colours.

Moodle compiles fixed colours into its stylesheet. This script reads the compiled
stylesheet and the classes found on the learner pages covered by the dark mode,
keeps the rules that apply to those pages, and converts each colour with explicit
rules. Run it after each Moodle upgrade:

    python3 build_dark_mode.py --css compiled.css --pages pages.json

pages.json holds {"classes": [...], "ids": [...]} collected from the covered pages.

@package    theme_iqralight
@copyright  2026 IQRATIC
@license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
"""

import argparse
import colorsys
import json
import os
import re

THEME = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
COLOUR = re.compile(r'#[0-9a-fA-F]{6}\b|#[0-9a-fA-F]{3}\b|\bwhite\b|rgba?\([^)]*\)')
PROPERTIES = ('background-color', 'background', 'color', 'border-color', 'border-top-color', 'border-right-color',
              'border-bottom-color', 'border-left-color', 'border', 'border-top', 'border-right', 'border-bottom',
              'border-left', 'outline-color')
# Must match the palette of scss/iqra-dark.scss.
DARK_SURFACE = (0x17 / 255, 0x21 / 255, 0x1e / 255)
ELEMENTS = re.compile(r'^(a|table|th|td|tr|thead|hr|input|select|textarea|button|code|pre|legend|fieldset|label|kbd)\b')


def rgb(value):
    """Red, green, blue (0-1) and alpha of a CSS colour."""
    value = value.strip().lower()
    if value == 'white':
        return 1.0, 1.0, 1.0, 1.0
    if value.startswith('#'):
        hexa = value[1:]
        if len(hexa) == 3:
            hexa = ''.join(c * 2 for c in hexa)
        return tuple(int(hexa[i:i + 2], 16) / 255 for i in (0, 2, 4)) + (1.0,)
    parts = [p.strip() for p in re.split(r'[,\s/]+', value[value.index('(') + 1:-1]) if p.strip()]
    channels = [float(p.rstrip('%')) / (100 if p.endswith('%') else 255) for p in parts[:3]]
    alpha = float(parts[3].rstrip('%')) / (100 if parts[3].endswith('%') else 1) if len(parts) > 3 else 1.0
    return channels[0], channels[1], channels[2], alpha


def convert(prop, value):
    """Dark equivalent of a colour, or None to keep it."""
    r, g, b, alpha = rgb(value)
    hue, lightness, saturation = colorsys.rgb_to_hls(r, g, b)
    if alpha < 0.5:
        return None
    background = prop.startswith('background')
    border = prop.startswith('border') or prop == 'outline-color'
    if background:
        if saturation > 0.2 and lightness > 0.75:
            return mix((r, g, b), DARK_SURFACE, 0.18)
        if saturation <= 0.2 and lightness > 0.85:
            return '$dark-surface'
        if saturation <= 0.2 and lightness > 0.6:
            return '$dark-surface-2'
        return None
    if border:
        return '$dark-line' if lightness > 0.6 and saturation <= 0.2 else None
    if lightness < 0.3 and saturation <= 0.2:
        return '$dark-ink'
    if lightness < 0.6 and saturation <= 0.2:
        return '$dark-muted'
    if lightness < 0.45 and saturation > 0.2:
        return mix((r, g, b), (1.0, 1.0, 1.0), 0.4)
    return None


def mix(first, second, weight):
    """Hexadecimal mix of two colours, like Sass mix(): weight is the share of the first."""
    return to_hex(*(a * weight + b * (1 - weight) for a, b in zip(first, second)))


def spaced(selector):
    """Selector with single spaces around combinators, outside brackets and parentheses."""
    out, depth = '', 0
    for char in selector:
        depth += 1 if char in '[(' else -1 if char in '])' else 0
        out += ' %s ' % char if depth == 0 and char in '>+~' else char
    return re.sub(r'\s+', ' ', out).strip()


def to_hex(r, g, b):
    """#rrggbb from 0-1 channels."""
    return '#%02x%02x%02x' % tuple(round(c * 255) for c in (r, g, b))


def applies(selector, classes, ids):
    """Whether a selector can match the covered pages."""
    if 'iqra' in selector or ':root' in selector or selector.startswith('html') or '[data-bs-theme' in selector:
        return False
    names = re.findall(r'\.([a-zA-Z0-9_-]+)', selector)
    idents = re.findall(r'#([a-zA-Z0-9_-]+)', selector)
    if not names and not idents:
        return bool(ELEMENTS.match(selector)) or selector.startswith('body')
    return all(n in classes for n in names) and all(i in ids for i in idents)


def scoped(selector, bodyclasses, bodyids):
    """Selector nested under the dark scope (the body element).

    When the first part of the selector targets the body itself (page type, layout
    or path classes, page id), it is attached to the scope instead of nested in it.
    """
    if selector.startswith('body'):
        return '&' + selector[4:]
    first = re.split(r'[\s>+~]', selector, maxsplit=1)[0]
    names = re.findall(r'\.([a-zA-Z0-9_-]+)', first)
    idents = re.findall(r'#([a-zA-Z0-9_-]+)', first)
    if (names or idents) and re.fullmatch(r'(?:[.#][a-zA-Z0-9_-]+)+', first) and \
            all(n in bodyclasses for n in names) and all(i in bodyids for i in idents):
        return '&' + selector
    return '& ' + selector


def main():
    parser = argparse.ArgumentParser(description=__doc__.split('\n')[0])
    parser.add_argument('--css', required=True)
    parser.add_argument('--pages', required=True)
    args = parser.parse_args()
    with open(args.css, encoding='utf8') as handle:
        css = handle.read()
    with open(args.pages, encoding='utf8') as handle:
        pages = json.load(handle)
    classes, ids = set(pages['classes']), set(pages['ids'])
    bodyclasses, bodyids = set(pages.get('bodyclasses', [])), set(pages.get('bodyids', []))

    css = re.sub(r'/\*.*?\*/', '', css, flags=re.S)
    css = re.sub(r'@media[^{]*\{((?:[^{}]*\{[^{}]*\})*)\s*\}', '', css)
    blocks = []
    for selectors, body in re.findall(r'([^{}@]+)\{([^{}]*)\}', css):
        chosen = [spaced(s.strip()) for s in selectors.split(',') if s.strip() and applies(s.strip(), classes, ids)]
        if not chosen:
            continue
        declarations = []
        for declaration in body.split(';'):
            if ':' not in declaration:
                continue
            prop, value = [p.strip() for p in declaration.split(':', 1)]
            if prop not in PROPERTIES or 'var(' in value or 'gradient' in value:
                continue
            important = '!important' in value
            value = value.replace('!important', '').strip()
            changed = False

            def replace(match):
                nonlocal changed
                dark = convert(prop, match.group(0))
                if dark is None:
                    return match.group(0)
                changed = True
                return dark
            newvalue = COLOUR.sub(replace, value)
            if changed:
                declarations.append('    %s: %s%s;' % (prop, newvalue, ' !important' if important else ''))
        if declarations:
            blocks.append(',\n'.join(scoped(s, bodyclasses, bodyids) for s in chosen) + ' {\n' + '\n'.join(
                '    ' + d.strip().replace('!important', '!important // stylelint-disable-line declaration-no-important')
                if '!important' in d else '    ' + d.strip() for d in declarations) + '\n}')

    header = ('// Generated by cli/build_dark_mode.py from the compiled stylesheet: do not edit by hand.\n'
              '// Dark equivalents of the fixed colours Moodle uses on the learner pages covered\n'
              '// by the dark mode. Included by scss/iqra-dark.scss inside the dark scope.\n\n'
              '@mixin iqra-dark-core {\n')
    output = header + '\n\n'.join('    ' + block.replace('\n', '\n    ') for block in blocks) + '\n}\n'
    with open(os.path.join(THEME, 'scss', 'iqra-dark-core.scss'), 'w', encoding='utf8') as handle:
        handle.write(output)
    print('%d rules converted' % len(blocks))


if __name__ == '__main__':
    main()
