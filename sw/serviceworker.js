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
 * Iqralight service worker, served by sw.php which fills in the IQRA_ values.
 *
 * Caches only versioned static resources (styles, scripts, fonts, theme
 * images): their URL changes with every Moodle revision, so they never go
 * stale. Pages, files, AJAX calls and form submissions always use the
 * network: no personal data is ever cached (principle 6). When a page cannot
 * be reached, a light offline page is shown instead of the browser's error.
 *
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* eslint-env serviceworker */
/* global IQRA_VERSION, IQRA_ENABLED, IQRA_OFFLINE_URL, IQRA_ROOT, IQRA_READING */

const PREFIX = 'iqra-';
const STATIC_CACHE = PREFIX + 'static-' + IQRA_VERSION;
const SIZE_HEADER = 'x-iqra-size';

// Offline reading (EF-69): reading resources a learner chose to keep, in a cache of their own.
const OFFLINE_PREFIX = PREFIX + 'offline-';
const OFFLINE_INDEX = IQRA_ROOT + '/theme/iqralight/offline-index.json';
const OFFLINE_MAX_AGE = 30 * 24 * 3600 * 1000;
const COURSE_FILE = /\/pluginfile\.php\//;
const KEEPABLE_PAGE = new RegExp('^' + IQRA_ROOT.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') +
    '/mod/(page|book|resource)/view\\.php\\?');
const KEEPABLE_FILE = new RegExp('^' + IQRA_ROOT.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '/pluginfile\\.php/');

// Bytes served from the cache since a page last asked. Kept in memory only: if the
// browser stops the worker, the figure is lost, so the counter can only under-count.
let servedFromCache = 0;

// Connection mode of the pages, sent by them. Unknown after a restart of the worker:
// profile pictures then load normally until a page says otherwise.
let economy = false;
const PROFILE_PICTURE = /\/pluginfile\.php\/\d+\/user\/icon\//;
const SILHOUETTE = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="12" fill="#cbd6d2"/>' +
    '<circle cx="12" cy="9.5" r="4" fill="#4a5b56"/><path d="M4.5 19.5a7.5 6 0 0 1 15 0" fill="#4a5b56"/></svg>';

// Moodle serves static resources under a revision number: these URLs are immutable.
const VERSIONED = new RegExp('^' + IQRA_ROOT.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') +
    '/(theme/styles\\.php|theme/font\\.php|theme/image\\.php|lib/javascript\\.php|lib/requirejs\\.php)/');

/**
 * Whether a request targets an immutable, versioned resource.
 *
 * @param {Request} request
 * @returns {boolean}
 */
const isVersioned = (request) => {
    if (request.method !== 'GET' || !VERSIONED.test(request.url)) {
        return false;
    }
    // Revision -1 means "development mode": nothing is immutable then.
    return !/\/-1(_|\/)/.test(request.url);
};

/**
 * Deletes every cache created by Iqralight except the one to keep.
 *
 * @param {string|null} keep
 */
const clearCaches = async(keep, withOffline = true) => {
    const keys = await caches.keys();
    for (const key of keys) {
        if (key.startsWith(PREFIX) && key !== keep && (withOffline || !key.startsWith(OFFLINE_PREFIX))) {
            await caches.delete(key);
        }
    }
};

/**
 * Serves a versioned resource from the cache, downloading it the first time only.
 *
 * @param {Request} request
 * @returns {Response}
 */
const cacheFirst = async(request) => {
    const cache = await caches.open(STATIC_CACHE);
    const cached = await cache.match(request);
    if (cached) {
        servedFromCache += parseInt(cached.headers.get(SIZE_HEADER), 10) || 0;
        return cached;
    }
    const response = await fetch(request);
    if (response.ok && response.type === 'basic') {
        const body = await response.clone().blob();
        const headers = new Headers(response.headers);
        headers.set(SIZE_HEADER, String(body.size));
        await cache.put(request, new Response(body, {status: response.status, statusText: response.statusText, headers}));
    }
    return response;
};

/**
 * Loads a page from the network, or shows the offline page when it is unreachable.
 *
 * @param {Request} request
 * @returns {Response}
 */
const pageOrOffline = async(request) => {
    try {
        return await fetch(request);
    } catch (error) {
        return (await keptCopy(request)) || caches.match(IQRA_OFFLINE_URL);
    }
};

/**
 * Names of the offline reading caches present on the device (one per learner, at most).
 *
 * @returns {string[]}
 */
const offlineCaches = async() => (await caches.keys()).filter((key) => key.startsWith(OFFLINE_PREFIX));

/**
 * Reads the list of kept resources of a cache.
 *
 * @param {Cache} cache
 * @returns {Object[]}
 */
const readIndex = async(cache) => {
    const response = await cache.match(OFFLINE_INDEX);
    return response ? response.json() : [];
};

/**
 * Writes the list of kept resources of a cache.
 *
 * @param {Cache} cache
 * @param {Object[]} index
 */
const writeIndex = (cache, index) => cache.put(OFFLINE_INDEX,
    new Response(JSON.stringify(index), {headers: {'Content-Type': 'application/json'}}));

/**
 * The kept copy of a page or file, if the learner kept it; records the consultation.
 *
 * @param {Request} request
 * @returns {Response|undefined}
 */
const keptCopy = async(request) => {
    for (const name of await offlineCaches()) {
        const cache = await caches.open(name);
        const response = await cache.match(request.url);
        if (response) {
            const index = await readIndex(cache);
            const entry = index.find((item) => item.url === request.url);
            if (entry) {
                entry.used = Date.now();
                await writeIndex(cache, index);
            }
            return response;
        }
    }
    return undefined;
};

/**
 * Keeps a page and its files for offline reading, within the quota of the device.
 *
 * @param {Object} data key, url, files, title, course and quota sent by the page.
 * @returns {Object} Result for the page: ok, and reason when it failed.
 */
const keep = async(data) => {
    // Only this site's reading pages and course files, never any other address.
    if (typeof data.url !== 'string' || !KEEPABLE_PAGE.test(data.url) || !Array.isArray(data.files) ||
            !data.files.every((file) => typeof file === 'string' && KEEPABLE_FILE.test(file))) {
        return {ok: false, reason: 'failed'};
    }
    const cache = await caches.open(OFFLINE_PREFIX + data.key);
    const index = (await readIndex(cache)).filter((item) => item.url !== data.url);
    const stored = [];
    let size = 0;
    for (const url of [data.url, ...data.files]) {
        const response = await fetch(url, {credentials: 'include', cache: url === data.url ? 'no-store' : 'force-cache'});
        if (!response.ok || response.redirected) {
            return {ok: false, reason: 'failed'};
        }
        const body = await response.blob();
        size += body.size;
        stored.push([url, new Response(body, {headers: {'Content-Type': response.headers.get('Content-Type') || ''}})]);
    }
    const used = index.reduce((total, item) => total + item.size, 0);
    if (used + size > data.quota) {
        return {ok: false, reason: 'full'};
    }
    for (const [url, response] of stored) {
        await cache.put(url, response);
    }
    index.push({url: data.url, files: data.files, title: data.title, course: data.course, size, used: Date.now()});
    await writeIndex(cache, index);
    return {ok: true, size};
};

/**
 * Removes a kept resource; files still used by another kept resource stay.
 *
 * @param {Cache} cache
 * @param {Object[]} index
 * @param {string} url
 */
const forget = async(cache, index, url) => {
    const entry = index.find((item) => item.url === url);
    const remaining = index.filter((item) => item.url !== url);
    if (entry) {
        const shared = new Set(remaining.flatMap((item) => [item.url, ...item.files]));
        for (const file of [entry.url, ...entry.files]) {
            if (!shared.has(file)) {
                await cache.delete(file);
            }
        }
    }
    await writeIndex(cache, remaining);
};

/**
 * Removes kept resources not read for 30 days.
 */
const expireKept = async() => {
    for (const name of await offlineCaches()) {
        const cache = await caches.open(name);
        const index = await readIndex(cache);
        for (const item of index.filter((entry) => Date.now() - entry.used > OFFLINE_MAX_AGE)) {
            await forget(cache, await readIndex(cache), item.url);
        }
    }
};

self.addEventListener('install', (event) => {
    event.waitUntil((async() => {
        if (IQRA_ENABLED) {
            const cache = await caches.open(STATIC_CACHE);
            await cache.add(new Request(IQRA_OFFLINE_URL, {cache: 'reload'}));
        }
        await self.skipWaiting();
    })());
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async() => {
        if (!IQRA_ENABLED) {
            // Kill switch: remove everything, then step aside for good.
            await clearCaches(null);
            await self.registration.unregister();
            return;
        }
        // A new version renews the static files; resources kept by the learner stay.
        await clearCaches(STATIC_CACHE, false);
        await expireKept();
        await self.clients.claim();
    })());
});

/**
 * Answers a page through the port it sent with its message.
 *
 * @param {ExtendableMessageEvent} event
 * @param {Promise<Object>} work
 */
const reply = (event, work) => {
    if (event.ports[0]) {
        event.waitUntil(work.then((result) => event.ports[0].postMessage(result)));
    }
};

// Messages sent by the pages, by type.
const handlers = {
    // Sent on logout: nothing from this session stays on the device.
    'iqra-clear': (event) => event.waitUntil(clearCaches(null)),
    // Another learner on this device: the previous one's kept resources go.
    'iqra-user': (event) => event.waitUntil((async() => {
        for (const name of await offlineCaches()) {
            if (!IQRA_READING || name !== OFFLINE_PREFIX + event.data.key) {
                await caches.delete(name);
            }
        }
    })()),
    'iqra-keep': (event) => IQRA_READING && reply(event, keep(event.data).catch(() => ({ok: false, reason: 'failed'}))),
    'iqra-forget': (event) => IQRA_READING && reply(event, (async() => {
        const cache = await caches.open(OFFLINE_PREFIX + event.data.key);
        await forget(cache, await readIndex(cache), event.data.url);
        return {ok: true};
    })()),
    'iqra-status': (event) => reply(event, (async() => {
        const index = await readIndex(await caches.open(OFFLINE_PREFIX + event.data.key));
        return {
            kept: index.some((item) => item.url === event.data.url),
            used: index.reduce((total, item) => total + item.size, 0),
        };
    })()),
    'iqra-mode': (event) => {
        economy = event.data.economy === true;
    },
    // A page collects the bytes served from the cache, for the data counter.
    'iqra-stats': (event) => {
        const cache = servedFromCache;
        servedFromCache = 0;
        reply(event, (async() => ({cache}))());
    },
};

self.addEventListener('message', (event) => {
    const handler = event.data && handlers[event.data.type];
    if (handler) {
        handler(event);
    }
});

self.addEventListener('fetch', (event) => {
    if (!IQRA_ENABLED) {
        return;
    }
    const request = event.request;
    if (economy && request.method === 'GET' && PROFILE_PICTURE.test(request.url)) {
        // Data saver: a light silhouette instead of the profile photo (RG-9.1).
        event.respondWith(new Response(SILHOUETTE, {headers: {'Content-Type': 'image/svg+xml'}}));
        return;
    }
    if (isVersioned(request)) {
        event.respondWith(cacheFirst(request));
    } else if (IQRA_READING && request.method === 'GET' && COURSE_FILE.test(request.url)) {
        // Course files: always the network, and a kept copy only when it fails.
        event.respondWith(fetch(request).catch(async() => (await keptCopy(request)) || Response.error()));
    } else if (request.mode === 'navigate' && request.method === 'GET') {
        event.respondWith(pageOrOffline(request));
    }
    // Everything else (files, AJAX, POST) goes to the network untouched.
});
