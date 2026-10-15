// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Mobile audit of Iqralight's learner pages, in a real Chromium, for development only.
 *
 * Checks horizontal overflow, touch targets under 40 px, text under 12 px and
 * JavaScript errors at phone widths, and takes a screenshot of each page.
 *
 *   npm install puppeteer-core @sparticuz/chromium
 *   BASE=https://site USER_LOGIN=learner USER_PASS=... WIDTHS=360,390 \
 *   PAGES='[["home","/my/courses.php"],["course","/course/view.php?id=2"]]' node mobile_audit.js
 *
 * Actions: add "openmode" or "tapmessages" as a third value of a page.
 *
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Mobile audit: screenshots and automatic checks on learner pages.
const chromium = require('@sparticuz/chromium');
const puppeteer = require('puppeteer-core');
const fs = require('fs');
const BASE = process.env.BASE || 'http://localhost:8000';
const WIDTHS = (process.env.WIDTHS || '360,390').split(',').map(Number);
const PAGES = JSON.parse(process.env.PAGES);
const OUT = process.env.OUT || '/home/claude/mobile';
const DARK = process.env.DARK === '1';
fs.mkdirSync(OUT, {recursive: true});

const checks = () => {
  const vw = window.innerWidth;
  const overflow = [];
  document.querySelectorAll('body *').forEach((el) => {
    const r = el.getBoundingClientRect();
    const cs = getComputedStyle(el);
    if (r.width && r.right > vw + 1 && cs.visibility !== 'hidden' && cs.display !== 'none' && !el.closest('.visually-hidden, .sr-only, [aria-hidden="true"]')) {
      let inScroller = false;
      for (let p = el.parentElement; p; p = p.parentElement) {
        const o = getComputedStyle(p).overflowX;
        if (o === 'auto' || o === 'scroll' || o === 'hidden') { inScroller = true; break; }
      }
      if (!inScroller) overflow.push((el.id ? '#' + el.id : el.tagName.toLowerCase() + '.' + [...el.classList].slice(0, 2).join('.')) + ' (' + Math.round(r.right) + 'px)');
    }
  });
  const small = [];
  document.querySelectorAll('a[href], button, input:not([type=hidden]), select, textarea, [role=button]').forEach((el) => {
    const r = el.getBoundingClientRect();
    const cs = getComputedStyle(el);
    if (!r.width || !r.height || cs.visibility === 'hidden' || el.closest('.visually-hidden, .sr-only, .visually-hidden-focusable, .sr-only-focusable, [aria-hidden="true"], .modal:not(.show)')) return;
    if (el.tagName === 'A' && el.closest('p, li, td') && cs.display === 'inline') return; // inline text links
    if (r.height < 40 || r.width < 40) small.push((el.textContent.trim().slice(0, 24) || el.getAttribute('aria-label') || el.tagName.toLowerCase()) + ' ' + Math.round(r.width) + 'x' + Math.round(r.height));
  });
  let tiny = 0;
  document.querySelectorAll('body *').forEach((el) => {
    if (el.childNodes.length && [...el.childNodes].some((n) => n.nodeType === 3 && n.textContent.trim()) && el.getBoundingClientRect().width) {
      if (parseFloat(getComputedStyle(el).fontSize) < 12) tiny++;
    }
  });
  return {scrollWidth: document.documentElement.scrollWidth, vw, overflow: [...new Set(overflow)].slice(0, 8), small: [...new Set(small)], tiny};
};

(async () => {
  const browser = await puppeteer.launch({args: [...chromium.args, '--no-sandbox'], executablePath: await chromium.executablePath(), headless: true});
  const report = {};
  let result_drawer = null;
  const page = (await browser.pages())[0] || await browser.newPage();
  const errors = [];
  page.on('pageerror', (e) => errors.push(e.message.slice(0, 140)));
  page.on('console', (m) => { if (m.type() === 'error') errors.push(m.text().slice(0, 140)); });
  const client = await page.createCDPSession();
  for (const width of WIDTHS) {
    await client.send('Network.clearBrowserCookies');
    await page.setViewport({width, height: 800, deviceScaleFactor: 1, isMobile: true, hasTouch: true});
    if (DARK) await page.emulateMediaFeatures([{name: 'prefers-color-scheme', value: 'dark'}]);
    if (process.env.USER_LOGIN) {
      await page.goto(BASE + '/login/index.php', {waitUntil: 'networkidle0'});
      await page.type('#username', process.env.USER_LOGIN);
      await page.type('#password', process.env.USER_PASS);
      await Promise.all([page.waitForNavigation({waitUntil: 'networkidle0'}), page.click('#loginbtn')]);
    }
    for (const [name, url, action] of PAGES) {
      errors.length = 0;
      await page.goto(BASE + url, {waitUntil: 'networkidle0', timeout: 60000}).catch((e) => errors.push('navigation: ' + e.message));
      await new Promise((r) => setTimeout(r, 600));
      if (action === 'tapmessages') {
        await page.click('[data-iqra-messages]').catch((e) => errors.push('clic: ' + e.message));
        await new Promise((r) => setTimeout(r, 1500));
        result_drawer = await page.evaluate(() => {
          const d = document.querySelector('[data-region="message-drawer"]');
          const c = d && d.closest('.drawer');
          const r = (c || d || document.body).getBoundingClientRect();
          return {url: location.pathname, visible: !!d && !(c && c.classList.contains('hidden')) && r.width > 0 && r.right > 0 && r.left < innerWidth};
        });
      }
      if (action === 'openmode') { await page.click('[data-action="theme_iqralight/open-mode"]').catch(() => {}); await new Promise((r) => setTimeout(r, 400)); }
      const result = await page.evaluate(checks);
      if (action === 'tapmessages') result.drawer = result_drawer;
      result.errors = [...errors];
      report[`${name}@${width}`] = result;
      await page.screenshot({path: `${OUT}/${name}-${width}${DARK ? '-dark' : ''}.png`, fullPage: false});
    }
  }
  await browser.close();
  for (const [key, r] of Object.entries(report)) {
    const flags = [];
    if (r.scrollWidth > r.vw) flags.push(`DÉBORDEMENT ${r.scrollWidth}px > ${r.vw}px : ${r.overflow.join(', ')}`);
    if (r.small.length) flags.push(`${r.small.length} cible(s) < 40px : ${r.small.slice(0, 5).join(' | ')}`);
    if (r.tiny) flags.push(`${r.tiny} texte(s) < 12px`);
    if (r.drawer) flags.push(r.drawer.visible && r.drawer.url !== '/message/index.php' ? 'tiroir de messagerie ouvert sur place' : `TIROIR NON OUVERT (${r.drawer.url})`);
    if (r.errors.length) flags.push(`ERREURS JS : ${r.errors.slice(0, 2).join(' | ')}`);
    console.log((flags.length ? '⚠ ' : '✓ ') + key.padEnd(22) + (flags.join('\n' + ' '.repeat(25)) || 'aucun problème détecté'));
  }
})().catch((e) => { console.error('ERREUR', e.message); process.exit(1); });
