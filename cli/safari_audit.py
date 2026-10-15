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

# Safari-engine check of Iqralight's learner pages, for development only.
#
# Renders pages with WebKit (WebKitGTK, the engine of Safari) at an iPhone width,
# reports horizontal overflow and takes screenshots.
#
#   sudo apt install gir1.2-webkit2-4.1 python3-gi python3-gi-cairo xvfb
#   BASE=https://site WIDTH=393 USER_LOGIN=learner USER_PASS=... \
#   PAGES='[["home","/my/courses.php"]]' xvfb-run -a python3 safari_audit.py
#
# @copyright  2026 IQRATIC
# @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

"""Screenshots and overflow checks with WebKit (Safari's engine) at phone width."""
import gi, json, os, sys
gi.require_version('Gtk', '3.0'); gi.require_version('WebKit2', '4.1')
from gi.repository import Gtk, WebKit2, GLib

BASE = os.environ.get('BASE', 'http://localhost:8000')
WIDTH = int(os.environ.get('WIDTH', '393'))
PAGES = json.loads(os.environ['PAGES'])
OUT = os.environ.get('OUT', '/home/claude/wk')
UA = ('Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) AppleWebKit/605.1.15 '
      '(KHTML, like Gecko) Version/18.0 Mobile/15E148 Safari/604.1')

win = Gtk.OffscreenWindow(); win.set_default_size(WIDTH, 852)
view = WebKit2.WebView(); view.get_settings().set_user_agent(UA)
view.set_size_request(WIDTH, 852); win.add(view); win.show_all()
steps = []
if os.environ.get('USER_LOGIN'):
    steps.append(('load', BASE + '/login/index.php'))
    steps.append(('js', "document.getElementById('username').value=%s;document.getElementById('password').value=%s;"
                  "document.getElementById('login').submit();" % (json.dumps(os.environ['USER_LOGIN']), json.dumps(os.environ['USER_PASS']))))
    steps.append(('wait', 2500))
for name, url in PAGES:
    steps += [('load', BASE + url), ('wait', 2500), ('check', name), ('shot', name)]
results = {}

def nxt():
    if not steps:
        print(json.dumps(results, ensure_ascii=False, indent=1)); Gtk.main_quit(); return False
    kind, arg = steps.pop(0)
    if kind == 'load':
        view.load_uri(arg)
        def done(v, ev):
            if ev == WebKit2.LoadEvent.FINISHED:
                v.disconnect(hid); GLib.idle_add(nxt)
        hid = view.connect('load-changed', done)
    elif kind == 'wait':
        GLib.timeout_add(arg, lambda: (nxt(), False)[1])
    elif kind == 'js':
        view.evaluate_javascript(arg, -1, None, None, None, lambda *a: GLib.idle_add(nxt))
    elif kind == 'check':
        js = """(() => { const vw = document.documentElement.clientWidth; const wide = [];
          document.querySelectorAll('body *').forEach((el) => { const r = el.getBoundingClientRect();
            if (r.width && r.right > vw + 1 && getComputedStyle(el).position !== 'fixed') {
              let clip = false; for (let p = el.parentElement; p; p = p.parentElement) { const o = getComputedStyle(p).overflowX; if (o !== 'visible') { clip = true; break; } }
              if (!clip) wide.push(el.tagName.toLowerCase() + '.' + [...el.classList].slice(0, 3).join('.') + ' ' + Math.round(r.right)); } });
          return JSON.stringify({vw, scrollWidth: document.documentElement.scrollWidth, wide: wide.slice(0, 6)}); })()"""
        def got(v, res):
            try: results[arg] = json.loads(v.evaluate_javascript_finish(res).to_string())
            except Exception as e: results[arg] = {'error': str(e)}
            GLib.idle_add(nxt)
        view.evaluate_javascript(js, -1, None, None, None, got)
    elif kind == 'shot':
        def snap(v, res):
            v.get_snapshot_finish(res).write_to_png('%s/%s-wk%d.png' % (OUT, arg, WIDTH)); GLib.idle_add(nxt)
        view.get_snapshot(WebKit2.SnapshotRegion.VISIBLE, WebKit2.SnapshotOptions.NONE, None, snap)
    return False

GLib.idle_add(nxt); GLib.timeout_add(120000, lambda: (print('TIMEOUT'), Gtk.main_quit()))
Gtk.main()
