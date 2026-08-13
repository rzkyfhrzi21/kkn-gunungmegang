/* =====================================================================
 * app-front.js — UI utilitas halaman depan
 * Modal preview foto/video (klik media → preview, tombol silang tutup)
 * ===================================================================== */
(function (window, document) {
    'use strict';

    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function ensureModal() {
        var m = document.getElementById('front-preview-modal');
        if (m) return m;
        m = document.createElement('div');
        m.id = 'front-preview-modal';
        m.className = 'front-preview-overlay';
        m.setAttribute('role', 'dialog');
        m.setAttribute('aria-modal', 'true');
        m.innerHTML =
            '<div class="front-preview-box">' +
            '<button type="button" class="front-preview-close" aria-label="Tutup preview">' +
            '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>' +
            '</button>' +
            '<div class="front-preview-content"></div>' +
            '</div>';
        document.body.appendChild(m);

        function close() {
            m.classList.remove('open');
            var c = m.querySelector('.front-preview-content');
            if (c) c.innerHTML = '';
            document.body.style.overflow = '';
        }
        m.addEventListener('click', function (e) { if (e.target === m) close(); });
        m.querySelector('.front-preview-close').addEventListener('click', close);
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
        return m;
    }

    function openPreview(src, type) {
        var modal = ensureModal();
        var content = modal.querySelector('.front-preview-content');
        content.innerHTML = '<div class="front-preview-loading"></div>';
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';

        if (type === 'video') {
            var vid = document.createElement('video');
            vid.controls = true;
            vid.autoplay = true;
            vid.className = 'front-preview-media';
            vid.src = src;
            vid.onloadedmetadata = function () { content.innerHTML = ''; content.appendChild(vid); };
            vid.onerror = function () { content.innerHTML = '<p class="front-preview-error">Tidak dapat memuat video.</p>'; };
        } else {
            var img = new Image();
            img.className = 'front-preview-media';
            img.alt = 'Preview';
            img.onload = function () { content.innerHTML = ''; content.appendChild(img); };
            img.onerror = function () { content.innerHTML = '<p class="front-preview-error">Tidak dapat memuat gambar.</p>'; };
            img.src = src;
        }
    }

    document.addEventListener('click', function (e) {
        var t = e.target.closest('[data-preview]');
        if (t) {
            e.preventDefault();
            openPreview(t.getAttribute('data-preview'), t.getAttribute('data-preview-type') || 'image');
        }
    });

    window.FrontPreview = { open: openPreview };
})(window, document);