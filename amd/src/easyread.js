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
 * Easy reading: a Listen button that reads the page aloud with the phone's own voice.
 *
 * Uses the browser's speech synthesis: no audio file is downloaded. The button only
 * appears when the device has a voice for the page language. Paragraphs are read one
 * after the other and the one being read is highlighted.
 *
 * @module     theme_iqralight/easyread
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const READABLE = 'h1, h2, h3, h4, p, li, td, th, .alert, label';
const READING = 'iqra-reading';

/**
 * A voice of the device for the page language, if any.
 *
 * @param {string} lang Page language, such as "fr" or "ar".
 * @returns {SpeechSynthesisVoice|null}
 */
const findVoice = (lang) => {
    const prefix = lang.toLowerCase().split(/[-_]/)[0];
    return window.speechSynthesis.getVoices().find((voice) => voice.lang.toLowerCase().startsWith(prefix)) || null;
};

/**
 * The texts of the main content, in reading order.
 *
 * @returns {HTMLElement[]}
 */
const readableElements = () => {
    const main = document.querySelector('#region-main') || document.querySelector('[role="main"]');
    if (!main) {
        return [];
    }
    return Array.from(main.querySelectorAll(READABLE)).filter((element) => {
        if (element.closest('.iqra-listen, [aria-hidden="true"], .visually-hidden, .sr-only')) {
            return false;
        }
        // Skip containers whose text is read through a nested readable element.
        return element.textContent.trim() !== '' && !element.querySelector(READABLE);
    });
};

/**
 * Entry point, called by the theme when the learner chose easy reading.
 *
 * @param {Object} strings Localised labels: listen and stop.
 */
export const init = (strings) => {
    if (!('speechSynthesis' in window) || !window.SpeechSynthesisUtterance) {
        return;
    }
    const lang = document.documentElement.lang || 'en';
    let started = false;

    const setup = () => {
        const voice = findVoice(lang);
        if (!voice || document.querySelector('.iqra-listen')) {
            return;
        }
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-primary iqra-listen';
        button.textContent = strings.listen;

        const clear = () => {
            document.querySelectorAll('.' + READING).forEach((element) => element.classList.remove(READING));
            button.textContent = strings.listen;
            button.setAttribute('aria-pressed', 'false');
            started = false;
        };

        const readFrom = (elements, index) => {
            document.querySelectorAll('.' + READING).forEach((element) => element.classList.remove(READING));
            if (!started || index >= elements.length) {
                clear();
                return;
            }
            const element = elements[index];
            element.classList.add(READING);
            element.scrollIntoView({block: 'center', behavior: 'smooth'});
            const utterance = new window.SpeechSynthesisUtterance(element.textContent.trim());
            utterance.voice = voice;
            utterance.lang = voice.lang;
            utterance.rate = 0.9;
            utterance.onend = () => readFrom(elements, index + 1);
            utterance.onerror = clear;
            window.speechSynthesis.speak(utterance);
        };

        button.addEventListener('click', () => {
            if (started) {
                window.speechSynthesis.cancel();
                clear();
                return;
            }
            started = true;
            button.textContent = strings.stop;
            button.setAttribute('aria-pressed', 'true');
            readFrom(readableElements(), 0);
        });
        button.setAttribute('aria-pressed', 'false');
        document.body.append(button);
        window.addEventListener('pagehide', () => window.speechSynthesis.cancel());
    };

    setup();
    // Some browsers load their voices after the page.
    window.speechSynthesis.addEventListener('voiceschanged', setup);
};
