/** js/theme.js - light / dark theme toggle, remembered in localStorage */
(function () {
    'use strict';
    var root = document.documentElement;

    function apply(t) {
        root.setAttribute('data-theme', t);
        try { localStorage.setItem('lcTheme', t); } catch (e) {}
        var m = document.querySelector('meta[name="theme-color"]');
        if (m) m.setAttribute('content', t === 'light' ? '#ffffff' : '#0B1E3D');
        [].forEach.call(document.querySelectorAll('.theme-toggle'), function (b) {
            b.setAttribute('aria-label', 'Switch to ' + (t === 'light' ? 'dark' : 'light') + ' theme');
        });
    }

    apply(root.getAttribute('data-theme') || 'dark');

    document.addEventListener('click', function (e) {
        if (e.target.closest('.theme-toggle')) {
            apply(root.getAttribute('data-theme') === 'light' ? 'dark' : 'light');
        }
    });
})();
