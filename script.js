function toggleBorder() {
    const box = document.querySelector('.box');
    const border = document.createElement('div');
    border.classList.add('circle-border');

    if (!box.querySelector('.circle-border')) {
        box.appendChild(border);
    } else {
        box.removeChild(box.querySelector('.circle-border'));
    }
}

function setCookie(name, value, days) {
    const expires = new Date(Date.now() + days * 864e5).toUTCString();
    document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + expires + '; path=/';
}

function getCookie(name) {
    return document.cookie.split('; ').reduce((r, v) => {
        const parts = v.split('=');
        return parts[0] === name ? decodeURIComponent(parts[1]) : r;
    }, '');
}