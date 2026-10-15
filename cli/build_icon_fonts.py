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

"""Builds the light icon fonts of theme_iqralight.

Run it after each Moodle upgrade, from any directory:

    pip install fonttools brotli
    python3 build_icon_fonts.py --moodle /path/to/moodle --css compiled.css

--css is the theme's compiled stylesheet (save the page served by
theme/styles.php/iqralight/.../all). Repeat --moodle and --css, in the same order, to
cover every supported Moodle version: the fonts keep the union of their icons.
For Moodle 5.1 and later, --moodle is the public/ directory. The script keeps every icon referenced by
Moodle's code (icon map, templates, JavaScript, SCSS), subsets the three
Font Awesome fonts shipped with Moodle and rewrites scss/iqra-icons.scss.

@package    theme_iqralight
@copyright  2026 IQRATIC
@license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
"""

import argparse
import os
import re
import subprocess

from fontTools.ttLib import TTFont

THEME = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
EXTENSIONS = ('.php', '.mustache', '.js', '.scss')
SKIP_DIRS = {'node_modules', 'vendor', 'fontawesome', 'build', 'yui', '.git'}
FACES = [
    ('Font Awesome 6 Free', 900, 'fa-solid-900.ttf', 'iqra-fa-solid-900.woff2'),
    ('Font Awesome 6 Free', 400, 'fa-regular-400.ttf', 'iqra-fa-regular-400.woff2'),
    ('Font Awesome 6 Brands', 400, 'fa-brands-400.ttf', 'iqra-fa-brands-400.woff2'),
]


def used_classes(moodle):
    """Every fa-* token referenced in Moodle's code."""
    found = set()
    pattern = re.compile(r'\bfa-[a-z0-9]+(?:-[a-z0-9]+)*\b')
    for root, dirs, files in os.walk(moodle):
        dirs[:] = [d for d in dirs if d not in SKIP_DIRS]
        for name in files:
            if name.endswith(EXTENSIONS):
                with open(os.path.join(root, name), encoding='utf8', errors='ignore') as handle:
                    found.update(pattern.findall(handle.read()))
    return found


def codepoints(css, classes):
    """Codepoints of the used classes, plus icons used directly in CSS rules."""
    bydefinition = {}
    for name, char in re.findall(r'\.(fa-[a-z0-9-]+)\{--fa:"(.)"', css):
        bydefinition.setdefault(name, set()).add(ord(char))
    keep = set()
    for name in classes & set(bydefinition):
        keep |= bydefinition[name]
    stripped = re.sub(r'(?:\.fa[a-z0-9-]*[.,]?)+\{--fa:"."\}', '', css)
    keep |= {ord(c) for c in stripped if 0xE000 <= ord(c) <= 0xF8FF}
    return keep


def unicode_range(cps):
    """Compact unicode-range value."""
    cps = sorted(cps)
    parts, start, prev = [], cps[0], cps[0]
    for c in cps[1:] + [None]:
        if c is not None and c == prev + 1:
            prev = c
            continue
        parts.append('U+%X' % start if start == prev else 'U+%X-%X' % (start, prev))
        if c is not None:
            start = prev = c
    return ',\n        '.join(parts)


def main():
    parser = argparse.ArgumentParser(description=__doc__.split('\n')[0])
    parser.add_argument('--moodle', required=True, action='append')
    parser.add_argument('--css', required=True, action='append')
    args = parser.parse_args()
    if len(args.moodle) != len(args.css):
        parser.error('give one --css per --moodle')

    keep = set()
    for moodle, cssfile in zip(args.moodle, args.css):
        with open(cssfile, encoding='utf8', errors='ignore') as handle:
            keep |= codepoints(handle.read(), used_classes(moodle))
    unicodes = ','.join('U+%04X' % c for c in sorted(keep))

    with open(os.path.join(THEME, 'scss', 'iqra-icons.scss'), encoding='utf8') as handle:
        header = handle.read().split('@font-face')[0]
    rules = []
    for family, weight, source, target in FACES:
        output = os.path.join(THEME, 'fonts', target)
        subprocess.run(['pyftsubset', os.path.join(args.moodle[-1], 'lib', 'fonts', source), '--unicodes=' + unicodes,
                        '--flavor=woff2', '--layout-features=*', '--no-hinting', '--desubroutinize',
                        '--output-file=' + output], check=True)
        covered = set(TTFont(output).getBestCmap())
        rules.append("@font-face {\n    font-family: '%s';\n    font-style: normal;\n    font-weight: %d;\n"
                     "    font-display: block;\n    src: url('[[font:theme|%s]]') format('woff2');\n"
                     "    unicode-range:\n        %s;\n}\n" % (family, weight, target, unicode_range(covered)))
        print('%-26s %4d icons  %6.1f KB' % (target, len(covered), os.path.getsize(output) / 1024))
    with open(os.path.join(THEME, 'scss', 'iqra-icons.scss'), 'w', encoding='utf8') as handle:
        handle.write(header + '\n'.join(rules))


if __name__ == '__main__':
    main()
