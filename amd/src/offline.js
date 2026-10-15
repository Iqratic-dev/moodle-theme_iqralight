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
 * Offline reading (EF-69): the "Keep offline" button of pages, book chapters and PDFs.
 *
 * The service worker stores the page and its course files in a cache of this learner's
 * own; it is cleared on logout, when another learner uses the device, or after 30 days.
 *
 * @module     theme_iqralight/offline
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Sends a message to the service worker and waits for its answer.
 *
 * @param {Object} message
 * @returns {Promise<Object>}
 */
const ask = (message) => new Promise((resolve) => {
    const channel = new MessageChannel();
    channel.port1.onmessage = (e) => resolve(e.data || {});
    navigator.serviceWorker.controller.postMessage(message, [channel.port2]);
});

/**
 * Course files shown in the main content: images, and embedded documents.
 *
 * @param {string} root Site address.
 * @returns {string[]}
 */
const courseFiles = (root) => {
    const main = document.querySelector('#region-main') || document.body;
    const urls = new Set();
    main.querySelectorAll('img[src], object[data], embed[src], iframe[src], [data-iqra-src], [data-iqra-data]').forEach((node) => {
        const url = node.getAttribute('src') || node.getAttribute('data') ||
            node.getAttribute('data-iqra-src') || node.getAttribute('data-iqra-data');
        const absolute = url ? new URL(url, window.location.href).href : '';
        if (absolute.startsWith(root + '/pluginfile.php/')) {
            urls.add(absolute);
        }
    });
    return Array.from(urls);
};

/**
 * Entry point, called on the pages that can be kept.
 *
 * @param {Object} config key, quota, root, title, course and strings.
 */
export const init = async(config) => {
    if (!navigator.serviceWorker || !navigator.serviceWorker.controller || !window.MessageChannel) {
        return;
    }
    const url = window.location.href.split('#')[0];
    const box = document.createElement('div');
    box.className = 'iqra-keep';
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'btn btn-secondary iqra-keep-button';
    const note = document.createElement('span');
    note.className = 'iqra-keep-note';
    note.setAttribute('role', 'status');
    box.append(button, note);
    const main = document.querySelector('#region-main');
    if (!main) {
        return;
    }
    main.prepend(box);

    let kept = false;
    const show = (state, message) => {
        kept = state;
        button.textContent = state ? config.strings.remove : config.strings.keep;
        button.setAttribute('aria-pressed', state ? 'true' : 'false');
        note.textContent = message || (state ? config.strings.kept : '');
    };
    const status = await ask({type: 'iqra-status', key: config.key, url});
    show(Boolean(status.kept));

    button.addEventListener('click', async() => {
        button.disabled = true;
        if (kept) {
            await ask({type: 'iqra-forget', key: config.key, url});
            show(false);
        } else {
            note.textContent = config.strings.saving;
            const result = await ask({
                type: 'iqra-keep', key: config.key, url, files: courseFiles(config.root),
                title: config.title, course: config.course, quota: config.quota,
            });
            show(Boolean(result.ok), result.ok ? '' : config.strings[result.reason === 'full' ? 'full' : 'failed']);
        }
        button.disabled = false;
    });
};
