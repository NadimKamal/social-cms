/**
 * 2026-07-08 16:14:12
 * =>
 * 08 July, 2026
 */
function CUSTOM_DATE(date) {

    if (!date) {
        return '';
    }

    const d = new Date(date.replace(' ', 'T'));

    return d.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });

}

/**
 * 2026-07-08 16:14:12
 * =>
 * 08 July 2026, 04:14 PM
 */
function CUSTOM_DATE_TIME(date) {

    if (!date) {
        return '';
    }

    const d = new Date(date.replace(' ', 'T'));

    const datePart = d.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });

    const timePart = d.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });

    return `${datePart}, ${timePart}`;

}

/**
 * Debounce helper
 */
function delay(fn, ms = 500) {

    let timer;

    return function (...args) {

        clearTimeout(timer);

        timer = setTimeout(() => fn.apply(this, args), ms);

    };

}

/**
 * Shortcut for querySelector
 */
function qs(selector) {

    return document.querySelector(selector);

}

/**
 * Shortcut for querySelectorAll
 */
function qsa(selector) {

    return document.querySelectorAll(selector);

}

function showImage(path) {
    return APP_URL + path;
}

function redirect(path) {
    window.location.href = APP_URL + path;
}

function asset(path) {
    return APP_URL + path;
}