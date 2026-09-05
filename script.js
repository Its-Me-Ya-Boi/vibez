/**
 * script.js — Theme management for Vibez.
 *
 * Combines two responsibilities:
 *   1. setTheme(name)  — called by the settings dropdown; switches CSS sheets
 *                        and logo, then persists the choice.
 *   2. loadSavedTheme() — applied on every DOMContentLoaded to restore the
 *                         user's saved theme without a flash of default styles.
 *
 * Storage: localStorage (primary) with sessionStorage as a write-through
 * mirror so the choice survives tab restores too.
 */

/**
 * Switch to a named theme and save it.
 *
 * @param {string} theme - 'dark' | 'light' | 'black'
 */
function setTheme(theme) {
    const themes = {
        dark:  {
            ccs:  '/project/assets/dark.css',
            mcs:  '/project/assets/darkMobile.css',
            logo: '/project/assets/images/vibezNight.png',
        },
        light: {
            ccs:  '/project/assets/light.css',
            mcs:  '/project/assets/lightMobile.css',
            logo: '/project/assets/images/vibezDay.png',
        },
        black: {
            ccs:  '/project/assets/black.css',
            mcs:  '/project/assets/blackMobile.css',
            logo: '/project/assets/images/vibezNight.png',
        },
    };

    const t = themes[theme] || themes.dark;

    // Apply immediately.
    const ccsEl  = document.getElementById('computerCSS');
    const mcsEl  = document.getElementById('mobileCSS');
    const logoEl = document.getElementById('logo');

    if (ccsEl)  ccsEl.setAttribute('href', t.ccs);
    if (mcsEl)  mcsEl.setAttribute('href', t.mcs);
    if (logoEl) logoEl.setAttribute('src',  t.logo);

    // Persist so the choice survives navigation and browser restarts.
    try {
        localStorage.setItem('ccs',  t.ccs);
        localStorage.setItem('mcs',  t.mcs);
        localStorage.setItem('logo', t.logo);
        sessionStorage.setItem('ccs',  t.ccs);
        sessionStorage.setItem('mcs',  t.mcs);
        sessionStorage.setItem('logo', t.logo);
    } catch (e) {
        // Storage may be blocked in private browsing — fail silently.
    }
}

/**
 * Restore the saved theme on every page load.
 * Reads the raw href/src values rather than the theme name so it works
 * regardless of which storage key format was written by older code.
 */
function loadSavedTheme() {
    try {
        const ccs  = localStorage.getItem('ccs')  || sessionStorage.getItem('ccs');
        const mcs  = localStorage.getItem('mcs')  || sessionStorage.getItem('mcs');
        const logo = localStorage.getItem('logo') || sessionStorage.getItem('logo');

        const ccsEl  = document.getElementById('computerCSS');
        const mcsEl  = document.getElementById('mobileCSS');
        const logoEl = document.getElementById('logo');

        if (ccs  && ccsEl)  ccsEl.setAttribute('href', ccs);
        if (mcs  && mcsEl)  mcsEl.setAttribute('href', mcs);
        if (logo && logoEl) logoEl.setAttribute('src',  logo);
    } catch (e) {
        // Storage blocked — leave the default stylesheet in place.
    }
}

window.addEventListener('DOMContentLoaded', loadSavedTheme);
