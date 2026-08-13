/* =====================================================================
 * admin-app.js — UI kit panel admin Pekon Gunung Megang
 * Toast stackable, modal preview media, JsonTable (AJAX POST),
 * upload, hamburger sidebar (persist localStorage)
 * ===================================================================== */
(function (window, document) {
    'use strict';

    var API = '../admin/api.php';
    var UPLOAD = '../admin/upload.php';

    function $(sel, ctx) { return (ctx || document).querySelector(sel); }
    function $$(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }

    /* ------------------------- helpers ------------------------- */
    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function csrfHeader() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    function postJSON(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfHeader()
            },
            body: JSON.stringify(body || {})
        }).then(function (r) { return r.json(); });
    }

    function postForm(url, formData) {
        return fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-Token': csrfHeader() },
            body: formData
        }).then(function (r) { return r.json(); });
    }

    function showModal(el) {
        if (window.bootstrap && bootstrap.Modal) {
            var m = bootstrap.Modal.getOrCreateInstance(el);
            m.show();
        } else {
            el.classList.add('show');
            el.style.display = 'block';
            document.body.classList.add('modal-open');
        }
    }

    function hideModal(el) {
        if (window.bootstrap && bootstrap.Modal) {
            var m = bootstrap.Modal.getInstance(el);
            if (m) m.hide();
        } else {
            el.classList.remove('show');
            el.style.display = '';
            document.body.classList.remove('modal-open');
        }
    }

    /* ------------------------- TOAST ------------------------- */
    function ensureToastContainer() {
        var c = $('#app-toasts');
        if (!c) {
            c = document.createElement('div');
            c.id = 'app-toasts';
            c.className = 'app-toasts';
            document.body.appendChild(c);
        }
        return c;
    }

    /**
     * App.toast(msg, type, title)
     * type: 'success' (auto-hide + slider) | 'error' (manual close, no auto-hide)
     * Bisa menumpuk (stackable).
     */
    function toast(msg, type, title) {
        type = type || 'success';
        var container = ensureToastContainer();
        var el = document.createElement('div');
        el.className = 'app-toast ' + type;
        var icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
        el.innerHTML =
            '<div class="app-toast-icon"><i class="bi ' + icon + '"></i></div>' +
            '<div class="app-toast-body">' +
            '  <div class="app-toast-title">' + esc(title || (type === 'success' ? 'Berhasil' : 'Gagal')) + '</div>' +
            '  <div class="app-toast-msg">' + esc(msg) + '</div>' +
            '</div>' +
            '<button type="button" class="app-toast-close" aria-label="Tutup"><i class="bi bi-x-lg"></i></button>' +
            (type === 'success' ? '<div class="app-toast-progress"></div>' : '');
        container.appendChild(el);

        var close = function () {
            el.classList.add('hide');
            setTimeout(function () { el.remove(); }, 300);
        };
        $('.app-toast-close', el).addEventListener('click', close);

        if (type === 'success') {
            var timer = setTimeout(close, 4000);
            el.addEventListener('mouseenter', function () { clearTimeout(timer); });
            el.addEventListener('mouseleave', function () { timer = setTimeout(close, 4000); });
        }
        return el;
    }

    /* ------------------------- PREVIEW MEDIA ------------------------- */
    function ensurePreviewModal() {
        var m = $('#app-preview-modal');
        if (!m) {
            m = document.createElement('div');
            m.id = 'app-preview-modal';
            m.className = 'modal fade';
            m.setAttribute('tabindex', '-1');
            m.innerHTML =
                '<div class="modal-dialog modal-dialog-centered app-preview-dialog">' +
                '<div class="modal-content app-preview-content">' +
                '<div class="modal-header app-preview-header">' +
                '<h5 class="modal-title">Preview</h5>' +
                '<button type="button" class="btn-close" data-app-close aria-label="Tutup"></button>' +
                '</div>' +
                '<div class="modal-body app-preview-body text-center p-2"><div class="app-preview-loading">' +
                '<div class="spinner-border text-primary"></div></div></div>' +
                '</div></div>';
            document.body.appendChild(m);
            $('[data-app-close]', m).addEventListener('click', function () { hideModal(m); });
            m.addEventListener('click', function (e) { if (e.target === m) hideModal(m); });
        }
        return m;
    }

    /** App.preview(src, type) — 'image' | 'video' */
    function preview(src, type) {
        var modal = ensurePreviewModal();
        var body = $('.app-preview-body', modal);
        body.innerHTML = '<div class="app-preview-loading"><div class="spinner-border text-primary"></div></div>';
        showModal(modal);

        if (type === 'video') {
            var vid = document.createElement('video');
            vid.className = 'app-preview-media';
            vid.controls = true;
            vid.autoplay = true;
            vid.src = src;
            vid.onloadedmetadata = function () {
                body.innerHTML = '';
                body.appendChild(vid);
            };
            vid.onerror = function () {
                body.innerHTML = '<div class="text-danger p-4"><i class="bi bi-exclamation-triangle-fill"></i> Tidak dapat memuat video.</div>';
            };
        } else {
            var img = new Image();
            img.className = 'app-preview-media';
            img.alt = 'Preview';
            img.onload = function () {
                body.innerHTML = '';
                body.appendChild(img);
            };
            img.onerror = function () {
                body.innerHTML = '<div class="text-danger p-4"><i class="bi bi-exclamation-triangle-fill"></i> Tidak dapat memuat gambar.</div>';
            };
            img.src = src;
        }
    }

    document.addEventListener('click', function (e) {
        var t = e.target.closest('[data-preview]');
        if (t) {
            e.preventDefault();
            preview(t.getAttribute('data-preview'), t.getAttribute('data-preview-type') || 'image');
        }
    });

    /* ------------------------- UPLOAD ------------------------- */
    function uploadFile(input, cb, opts) {
        opts = opts || {};
        var file = input.files && input.files[0];
        if (!file) return;
        var isVideo = (file.type || '').indexOf('video') === 0;
        var max = isVideo ? 15 * 1024 * 1024 : 2 * 1024 * 1024;
        if (file.size > max) {
            toast((isVideo ? 'Video' : 'Foto') + ' melebihi batas maksimal ' + (isVideo ? '15 MB' : '2 MB') + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB).', 'error', 'Ukuran File');
            input.value = '';
            if (cb) cb(null);
            return;
        }
        var fd = new FormData();
        fd.append('file', file);
        postForm(UPLOAD, fd).then(function (res) {
            if (!res || !res.ok) {
                toast((res && res.detail ? res.error + ': ' + res.detail : 'Upload gagal.'), 'error', 'Upload Gagal');
                if (cb) cb(null);
                return;
            }
            toast('File ' + res.name + ' berhasil diunggah.', 'success', 'Upload Berhasil');
            if (cb) cb(res);
        }).catch(function () {
            toast('Terjadi kesalahan jaringan saat upload.', 'error', 'Upload Gagal');
            if (cb) cb(null);
        });
    }

    /* ------------------------- FORM HELPERS ------------------------- */
    function formToObject(form) {
        var out = {};
        $$('[name]', form).forEach(function (el) {
            var name = el.getAttribute('name');
            if (el.type === 'checkbox') out[name] = el.checked ? 1 : 0;
            else out[name] = el.value;
        });
        return out;
    }

    function setForm(form, obj) {
        $$('[name]', form).forEach(function (el) {
            var name = el.getAttribute('name');
            if (obj && name in obj) {
                if (el.type === 'checkbox') el.checked = !!obj[name];
                else el.value = obj[name] == null ? '' : obj[name];
            }
        });
    }

    function serializeRow(row) {
        var o = {};
        Object.keys(row).forEach(function (k) { o[k] = row[k]; });
        return o;
    }

    /* ------------------------- JSON TABLE ------------------------- */
    /**
     * new JsonTable({
     *   selector: '#tbl-x', module: 'perangkat',
     *   columns: [{key:'nama',label:'Nama'},{key:'foto',label:'Foto',type:'image'},
     *             {key:'jabatan',label:'Jabatan',type:'badge'}],
     *   actions: ['detail','edit','delete'],   // icon button kolom terakhir
     *   perPage: 10,
     *   search: '#search-x', filters: [{key:'jabatan',label:'Jabatan',select:'#f-jabatan'}],
     *   reset: '#reset-x', onDetail: fn(row,btn), onEdit: fn(row,btn), onDelete: fn(row,btn),
     *   rowClass: fn(row)
     * })
     */
    function JsonTable(opts) {
        this.opts = opts;
        this.state = { page: 1, search: '', filters: {} };
        this.table = $(opts.selector);
        if (!this.table) return;
        this.wrap = this.table.closest('.app-table-wrap');
        if (this.wrap) this.wrap._table = this;
        this.init();
    }

    JsonTable.prototype.init = function () {
        var self = this;
        var o = this.opts;

        var toolbar = $('.app-table-toolbar', this.wrap);
        if (toolbar && (o.filters || []).length > 1) {
            var resetBtn = document.createElement('button');
            resetBtn.type = 'button';
            resetBtn.className = 'btn btn-sm btn-outline-secondary app-btn-reset';
            resetBtn.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i> Reset Filter';
            resetBtn.addEventListener('click', function () {
                (o.filters || []).forEach(function (f) {
                    var sel = $(f.select);
                    if (sel) sel.value = '';
                });
                self.state.filters = {};
                self.state.page = 1;
                self.reload();
            });
            toolbar.appendChild(resetBtn);
        }

        if (o.search) {
            var searchEl = $(o.search);
            if (searchEl) {
                var timer = null;
                searchEl.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(function () {
                        self.state.search = searchEl.value.trim();
                        self.state.page = 1;
                        self.reload();
                    }, 350);
                });
            }
        }

        (o.filters || []).forEach(function (f) {
            var sel = $(f.select);
            if (sel) {
                sel.addEventListener('change', function () {
                    self.state.filters[f.key] = sel.value;
                    self.state.page = 1;
                    self.reload();
                });
            }
        });

        var prev = $('.app-pagination-prev', this.wrap);
        var next = $('.app-pagination-next', this.wrap);
        if (prev) prev.addEventListener('click', function () { if (self.state.page > 1) { self.state.page--; self.reload(); } });
        if (next) next.addEventListener('click', function () { self.state.page++; self.reload(); });

        this.reload();
    };

    JsonTable.prototype.loading = function (on) {
        var wrap = this.wrap;
        if (!wrap) return;
        var ov = $('.app-table-loading', wrap);
        if (on) {
            if (!ov) {
                ov = document.createElement('div');
                ov.className = 'app-table-loading';
                ov.innerHTML = '<div class="spinner-border text-primary"></div>';
                wrap.appendChild(ov);
            }
            ov.style.display = '';
        } else if (ov) {
            ov.style.display = 'none';
        }
    };

    JsonTable.prototype.reload = function () {
        var self = this;
        var o = this.opts;
        this.loading(true);
        postJSON(API, {
            action: 'list', module: o.module,
            page: this.state.page, perPage: o.perPage || 10,
            search: this.state.search, filters: this.state.filters
        }).then(function (res) {
            self.loading(false);
            if (!res || !res.ok) {
                toast((res && res.error) || 'Gagal memuat data.', 'error', 'Data');
                return;
            }
            self.render(res);
        }).catch(function () {
            self.loading(false);
            toast('Gagal memuat data (jaringan).', 'error', 'Data');
        });
    };

    JsonTable.prototype.render = function (res) {
        var self = this;
        var o = this.opts;
        var tbody = $('tbody', this.table);
        if (!tbody) return;

        /* filter options */
        (o.filters || []).forEach(function (f) {
            var sel = $(f.select);
            if (!sel || !res.filterOptions || !res.filterOptions[f.key]) return;
            var opts = res.filterOptions[f.key];
            var cur = sel.value;
            sel.innerHTML = '<option value="">' + esc(f.label) + '</option>' +
                opts.map(function (v) { return '<option value="' + esc(v) + '">' + esc(v) + '</option>'; }).join('');
            if (cur !== '') sel.value = cur;
        });

        if (!res.rows.length) {
            tbody.innerHTML = '<tr><td colspan="99" class="text-center text-muted py-5">' +
                '<i class="bi bi-inbox fs-2 d-block mb-2"></i>Tidak ada data</td></tr>';
        } else {
            tbody.innerHTML = res.rows.map(function (row, i) {
                var tr = document.createElement('tr');
                var html = '';
                o.columns.forEach(function (col) {
                    var v = row[col.key];
                    if (col.format) {
                        html += '<td class="' + (col.align || '') + '">' + col.format(row, v) + '</td>';
                    } else if (col.type === 'image') {
                        html += '<td class="app-cell-img">' + (v ? imgCell(v) : '<span class="text-muted">-</span>') + '</td>';
                    } else if (col.type === 'currency') {
                        html += '<td class="' + (col.align || 'text-right') + '">' + esc(fmtRp(v)) + '</td>';
                    } else if (col.type === 'badge') {
                        html += '<td><span class="badge rounded-pill bg-light text-dark border">' + esc(v == null ? '-' : v) + '</span></td>';
                    } else {
                        html += '<td class="' + (col.align || '') + '">' + esc(v == null ? '-' : v) + '</td>';
                    }
                });
                html += '<td class="text-end row-actions">';
                if (o.actions.indexOf('detail') !== -1) html += actBtn('eye', 'detail', 'Lihat detail');
                if (o.actions.indexOf('edit') !== -1) html += actBtn('pencil-square', 'edit', 'Edit');
                if (o.actions.indexOf('delete') !== -1) html += actBtn('trash', 'delete', 'Hapus');
                html += '</td>';
                tr.innerHTML = html;
                return tr.outerHTML;
            }).join('');
        }

        /* pagination */
        var info = $('.app-pagination-info', this.wrap);
        if (info) info.textContent = 'Hal ' + res.page + ' dari ' + res.pages + ' — ' + res.total + ' data';
        var prev = $('.app-pagination-prev', this.wrap);
        var next = $('.app-pagination-next', this.wrap);
        if (prev) prev.disabled = res.page <= 1;
        if (next) next.disabled = res.page >= res.pages;

        /* bind actions */
        $$('tbody tr', this.table).forEach(function (tr, i) {
            var row = res.rows[i];
            $('[data-act="detail"]', tr).addEventListener('click', function (e) { e.preventDefault(); if (o.onDetail) o.onDetail(serializeRow(row), this); });
            $('[data-act="edit"]', tr).addEventListener('click', function (e) { e.preventDefault(); if (o.onEdit) o.onEdit(serializeRow(row), this); });
            $('[data-act="delete"]', tr).addEventListener('click', function (e) { e.preventDefault(); if (o.onDelete) o.onDelete(serializeRow(row), this); });
        });
    };

    function actBtn(icon, act, title) {
        return '<button type="button" class="btn btn-sm btn-icon row-act" data-act="' + act + '" title="' + title + '">' +
            '<i class="bi bi-' + icon + '"></i></button>';
    }

    function imgCell(src) {
        return '<span class="app-thumb" data-preview="' + esc(src) + '" role="button" title="Klik untuk preview">' +
            '<span class="skeleton"></span>' +
            '<img src="' + esc(src) + '" alt="" loading="lazy" class="app-thumb-img" onload="this.previousElementSibling.remove()" onerror="this.remove()">' +
            '<span class="app-zoom"><i class="bi bi-zoom-in"></i></span></span>';
    }

    function fmtRp(v) {
        v = parseFloat(v || 0);
        return 'Rp ' + Math.round(v).toLocaleString('id-ID');
    }

    /* ------------------------- HAMBURGER SIDEBAR ------------------------- */
    function initSidebar() {
        var btn = $('#btn-hamburger');
        var app = $('#app');
        if (!btn || !app) return;

        var saved = null;
        try { saved = localStorage.getItem('admin.sidebar'); } catch (e) {}
        if (saved === 'collapsed') app.classList.add('sidebar-collapsed');

        btn.addEventListener('click', function () {
            var collapsed = app.classList.toggle('sidebar-collapsed');
            try { localStorage.setItem('admin.sidebar', collapsed ? 'collapsed' : 'open'); } catch (e) {}
        });
    }

    /* ------------------------- EXPORT ------------------------- */
    window.App = {
        toast: toast,
        preview: preview,
        uploadFile: uploadFile,
        postJSON: postJSON,
        postForm: postForm,
        showModal: showModal,
        hideModal: hideModal,
        formToObject: formToObject,
        setForm: setForm,
        JsonTable: JsonTable,
        initSidebar: initSidebar,
        esc: esc,
        fmtRp: fmtRp
    };

    document.addEventListener('DOMContentLoaded', function () {
        initSidebar();
    });
})(window, document);