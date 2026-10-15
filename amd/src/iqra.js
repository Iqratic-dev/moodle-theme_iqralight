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
 * Iqralight micro-script: network detection, mode chip and tap feedback.
 *
 * @module     theme_iqralight/iqra
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {setUserPreference} from 'core_user/repository';
import Notification from 'core/notification';
import Config from 'core/config';
import {show as showMessageDrawer} from 'core_message/message_drawer_helper';

const COOKIE = 'iqralight_net';
const PREFERENCE = 'theme_iqralight_mode';
const PREFERENCE_TEXTONLY = 'theme_iqralight_textonly';
const DISPLAY_OPTIONS = {
    'iqra-contrast': 'theme_iqralight_contrast',
    'iqra-easyread': 'theme_iqralight_easyread',
};

/**
 * Classifies the connection with the Network Information API, when available.
 *
 * @returns {string} 'veryslow', 'slow', 'fast' or '' when the browser gives no information.
 */
const measureNetwork = () => {
    const connection = navigator.connection;
    if (!connection) {
        return '';
    }
    if (connection.effectiveType === 'slow-2g') {
        return 'veryslow';
    }
    if (connection.saveData) {
        return 'slow';
    }
    if (['2g', '3g'].includes(connection.effectiveType)) {
        return 'slow';
    }
    if (typeof connection.downlink === 'number' && connection.downlink > 0 && connection.downlink < 1.5) {
        return 'slow';
    }
    return connection.effectiveType ? 'fast' : '';
};

/**
 * Stores the measure so that the next page is rendered in the right mode.
 *
 * @param {string} value
 */
const rememberNetwork = (value) => {
    const secure = window.location.protocol === 'https:' ? '; Secure' : '';
    document.cookie = `${COOKIE}=${value}; path=/; max-age=21600; SameSite=Lax${secure}`;
};

/**
 * Tells the service worker the mode of the page, for profile pictures in data saver mode.
 *
 * @param {string} effective 'economy' or 'full'.
 */
const tellWorker = (effective) => {
    if (navigator.serviceWorker && navigator.serviceWorker.controller) {
        navigator.serviceWorker.controller.postMessage({type: 'iqra-mode', economy: effective === 'economy'});
    }
};

/**
 * Applies a mode to the current page without reloading it.
 *
 * @param {string} effective 'economy' or 'full'.
 * @param {Object} labels Chip labels by mode.
 */
const applyMode = (effective, labels) => {
    tellWorker(effective);
    if (effective === 'full') {
        document.body.classList.remove('iqra-textonly');
    }
    document.body.classList.toggle('iqra-mode-economy', effective === 'economy');
    document.body.classList.toggle('iqra-mode-full', effective === 'full');
    applyCourseImages(document);
    const label = document.querySelector('[data-region="theme_iqralight/mode-label"]');
    if (label && labels[effective]) {
        label.textContent = labels[effective];
    }
};

/**
 * Wires the mode chip to its dialog and saves the learner's choice.
 *
 * @param {Object} config
 * @param {function} currentNetwork
 */
const initDialog = (config, currentNetwork) => {
    const chip = document.querySelector('[data-action="theme_iqralight/open-mode"]');
    const dialog = document.querySelector('[data-region="theme_iqralight/mode-dialog"]');
    if (!chip || !dialog) {
        return;
    }
    // Browsers without the dialog element (iOS before 15.4, old Android) get a plain panel.
    const native = typeof dialog.showModal === 'function';
    chip.addEventListener('click', () => (native ? dialog.showModal() : dialog.setAttribute('open', '')));
    if (!native) {
        dialog.querySelector('form').addEventListener('submit', (e) => {
            e.preventDefault();
            dialog.removeAttribute('open');
        });
    }
    dialog.addEventListener('change', (e) => {
        if (DISPLAY_OPTIONS[e.target.name]) {
            setUserPreference(DISPLAY_OPTIONS[e.target.name], e.target.checked ? 1 : 0).catch(Notification.exception);
            document.body.classList.toggle(e.target.name, e.target.checked);
            if (e.target.name === 'iqra-easyread' && e.target.checked && config.listenstrings) {
                import('theme_iqralight/easyread')
                    .then((easyread) => easyread.init(config.listenstrings))
                    .catch(Notification.exception);
            }
            return;
        }
        if (e.target.name === 'iqra-textonly') {
            setUserPreference(PREFERENCE_TEXTONLY, e.target.checked ? 1 : 0).catch(Notification.exception);
            const economy = document.body.classList.contains('iqra-mode-economy');
            document.body.classList.toggle('iqra-textonly', e.target.checked && economy);
            applyCourseImages(document);
            return;
        }
        if (e.target.name !== 'iqra-mode') {
            return;
        }
        const requested = e.target.value;
        setUserPreference(PREFERENCE, requested).catch(Notification.exception);
        let effective = requested;
        if (requested === 'auto') {
            const network = currentNetwork();
            effective = (config.savedata || network !== 'fast') ? 'economy' : 'full';
        }
        config.requested = requested;
        applyMode(effective, config.labels);
    });
};

/**
 * Shows a thin progress bar as soon as the learner follows a link (UX-06).
 */
const initTapFeedback = () => {
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a[href]');
        if (!link || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
            return;
        }
        if (link.target || link.hasAttribute('download') || link.origin !== window.location.origin) {
            return;
        }
        const href = link.getAttribute('href');
        const opensWidget = link.dataset.toggle || link.dataset.bsToggle || link.getAttribute('role') === 'button';
        if (!href || href.startsWith('#') || opensWidget) {
            return;
        }
        document.documentElement.classList.add('iqra-navigating');
    });
    window.addEventListener('pageshow', () => document.documentElement.classList.remove('iqra-navigating'));
};

/**
 * Registers the service worker, and clears its caches when the learner logs out.
 *
 * @param {Object|null} serviceworker URL and scope of the worker, or null when disabled.
 */
const initServiceWorker = (serviceworker) => {
    if (!serviceworker || !('serviceWorker' in navigator)) {
        return;
    }
    navigator.serviceWorker.register(serviceworker.url, {scope: serviceworker.scope}).catch(() => {
        // The server refused the worker's scope: the site keeps working without it.
    });
    if (navigator.storage && typeof navigator.storage.persist === 'function') {
        navigator.storage.persist().catch(() => null);
    }
    document.addEventListener('click', (e) => {
        const logout = e.target.closest('a[href*="/login/logout.php"]');
        if (logout && navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({type: 'iqra-clear'});
        }
    });
};

const PENDING = 'iqra-pending';
const COUNTED = 'iqra-counted';
const SEND_INTERVAL = 600000;

/**
 * Reads a JSON value from the browser's storage, which may be unavailable.
 *
 * @param {string} key
 * @param {*} fallback
 * @returns {*}
 */
const readStorage = (key, fallback) => {
    try {
        return JSON.parse(window.localStorage.getItem(key)) || fallback;
    } catch (e) {
        return fallback;
    }
};

/**
 * Writes a JSON value to the browser's storage, silently when it is unavailable.
 *
 * @param {string} key
 * @param {*} value
 */
const writeStorage = (key, value) => {
    try {
        window.localStorage.setItem(key, JSON.stringify(value));
    } catch (e) {
        // Private browsing or full storage: the counter simply under-counts.
    }
};

/**
 * Short, non-reversible fingerprint of an address, so that no URL is stored.
 *
 * @param {string} text
 * @returns {string}
 */
const fingerprint = (text) => {
    let hash = 5381;
    for (let i = 0; i < text.length; i++) {
        hash = (hash * 33 + text.charCodeAt(i)) % 4294967296;
    }
    return hash.toString(36);
};

/**
 * Adds amounts to the figures waiting to be sent.
 *
 * @param {number} cache Bytes served by the service worker cache.
 * @param {number} media Bytes of media left unloaded.
 */
const addPending = (cache, media) => {
    const pending = readStorage(PENDING, {cache: 0, media: 0, sent: 0});
    pending.cache += cache;
    pending.media += media;
    writeStorage(PENDING, pending);
};

/**
 * Counts the media of the page the learner did not load, each one once a day.
 */
const countUnloadedMedia = () => {
    const today = new Date().toISOString().slice(0, 10);
    const counted = readStorage(COUNTED, {day: today, keys: []});
    if (counted.day !== today) {
        counted.day = today;
        counted.keys = [];
    }
    let bytes = 0;
    document.querySelectorAll('[data-filter-iqra][data-iqra-bytes]:not(.filter-iqra-loaded)').forEach((wrapper) => {
        const media = wrapper.firstElementChild;
        const key = fingerprint((media && (media.getAttribute('data-iqra-src') || media.getAttribute('data-iqra-data'))) || '');
        if (!counted.keys.includes(key)) {
            counted.keys.push(key);
            bytes += parseInt(wrapper.dataset.iqraBytes, 10) || 0;
        }
    });
    writeStorage(COUNTED, counted);
    addPending(0, bytes);
};

/**
 * Sends the waiting figures, at most every ten minutes.
 *
 * @param {string} url
 */
const sendPending = (url) => {
    const pending = readStorage(PENDING, {cache: 0, media: 0, sent: 0});
    if (pending.cache + pending.media === 0 || Date.now() - pending.sent < SEND_INTERVAL || !navigator.sendBeacon) {
        return;
    }
    const data = new FormData();
    data.append('sesskey', Config.sesskey);
    data.append('cache', String(Math.floor(pending.cache)));
    data.append('media', String(Math.floor(pending.media)));
    if (navigator.sendBeacon(url, data)) {
        writeStorage(PENDING, {cache: 0, media: 0, sent: Date.now()});
    }
};

/**
 * Data counter: collects what was avoided and reports it in small, rare batches.
 *
 * @param {string|null} url Where to send the figures, or null when the counter is off.
 */
const initCounter = (url) => {
    if (!url) {
        return;
    }
    if (navigator.serviceWorker && navigator.serviceWorker.controller && window.MessageChannel) {
        const channel = new MessageChannel();
        channel.port1.onmessage = (e) => addPending(Number(e.data.cache) || 0, 0);
        navigator.serviceWorker.controller.postMessage({type: 'iqra-stats'}, [channel.port2]);
    }
    window.addEventListener('pagehide', () => {
        countUnloadedMedia();
        sendPending(url);
    });
};

/**
 * Course images (My courses): the original image, a 250-pixel thumbnail in data saver
 * mode, or nothing in text-only mode. Images generated by Moodle (data: addresses) weigh
 * nothing and are always shown.
 *
 * @param {Element} root
 */
const applyCourseImages = (root) => {
    const body = document.body.classList;
    root.querySelectorAll('[data-iqra-image]').forEach((element) => {
        let url = element.dataset.iqraImage;
        if (!url.startsWith('data:')) {
            if (body.contains('iqra-textonly')) {
                element.style.backgroundImage = '';
                return;
            }
            if (body.contains('iqra-mode-economy')) {
                url += (url.includes('?') ? '&' : '?') + 'preview=bigthumb';
            }
        }
        element.style.backgroundImage = `url("${url}")`;
    });
};

/**
 * Applies the course images now and to the cards Moodle renders later.
 */
const initCourseImages = () => {
    applyCourseImages(document);
    new MutationObserver((mutations) => {
        mutations.forEach((mutation) => mutation.addedNodes.forEach((node) => {
            if (node.nodeType === 1) {
                applyCourseImages(node.parentElement || node);
            }
        }));
    }).observe(document.body, {childList: true, subtree: true});
};

/**
 * The Messages item of the bottom bar opens Moodle's messaging drawer, made for phones,
 * rather than the full messaging page, which is not. The link stays as a fallback.
 */
const initMessagesLink = () => {
    document.addEventListener('click', (e) => {
        const link = e.target.closest('[data-iqra-messages]');
        if (!link || !document.querySelector('[data-region="message-drawer"]')) {
            return;
        }
        e.preventDefault();
        showMessageDrawer();
    });
};

/**
 * Without network, a form is not sent: the learner keeps what they typed and sees why (EF-68).
 *
 * Not used on quizzes, assignments and the login page, whose submission stays Moodle's.
 *
 * @param {string|null} message
 */
const initOfflineGuard = (message) => {
    if (!message) {
        return;
    }
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (navigator.onLine !== false || form.method === 'dialog' || form.getAttribute('method') === 'dialog') {
            return;
        }
        e.preventDefault();
        if (!form.querySelector('.iqra-offline-submit')) {
            const notice = document.createElement('div');
            notice.className = 'alert alert-warning iqra-offline-submit';
            notice.setAttribute('role', 'alert');
            notice.textContent = message;
            form.prepend(notice);
            window.addEventListener('online', () => notice.remove(), {once: true});
        }
    }, true);
};

/**
 * Entry point, called by the theme's layouts.
 *
 * @param {Object} config Mode resolved by the server.
 */
export const init = (config) => {
    let network = measureNetwork();
    if (network) {
        rememberNetwork(network);
        if (config.requested === 'auto' && !config.savedata) {
            applyMode(network === 'fast' ? 'full' : 'economy', config.labels);
        }
    }
    if (navigator.connection && typeof navigator.connection.addEventListener === 'function') {
        navigator.connection.addEventListener('change', () => {
            network = measureNetwork();
            if (network) {
                rememberNetwork(network);
            }
        });
    }
    if (config.canchoose) {
        initDialog(config, () => network);
    }
    initTapFeedback();
    initServiceWorker(config.serviceworker);
    initCounter(config.stats);
    initOfflineGuard(config.offlinesubmit);
    initCourseImages();
    initMessagesLink();
    if (config.learnerkey && navigator.serviceWorker && navigator.serviceWorker.controller) {
        navigator.serviceWorker.controller.postMessage({type: 'iqra-user', key: config.learnerkey});
    }
    tellWorker(document.body.classList.contains('iqra-mode-full') ? 'full' : 'economy');
};
