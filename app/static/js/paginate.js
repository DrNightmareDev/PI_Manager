'use strict';

/**
 * EvePaginate — lightweight client-side paginator
 * Works together with filter functions: call applyFilter(matchedRows) from your filter.
 *
 * @param {string} tableId     - id of the <table> element
 * @param {object} opts
 *   pageSize   {number}   initial page size (default 25; 0 = all)
 *   controlsId {string}   id of the container where pagination controls are rendered
 *   onCount    {function} called with (visibleTotal) after every update
 */
function EvePaginate(tableId, opts) {
    const o = Object.assign({ pageSize: 6, controlsId: null, onCount: null }, opts || {});
    const sizeStorageKey = `eve_paginate_size_${tableId}`;
    const pageStorageKey = `eve_paginate_page_${tableId}`;
    let persistedSize = null;
    let persistedPage = null;
    try {
        persistedSize = parseInt(localStorage.getItem(sizeStorageKey), 10);
        if (Number.isNaN(persistedSize)) persistedSize = null;
        persistedPage = parseInt(localStorage.getItem(pageStorageKey), 10);
        if (Number.isNaN(persistedPage) || persistedPage < 1) persistedPage = null;
    } catch (_) {
        persistedSize = null;
        persistedPage = null;
    }
    let ps   = persistedSize !== null ? persistedSize : o.pageSize;
    let page = persistedPage !== null ? persistedPage : 1;
    const sizes = o.pageSizes || [6, 15, 25, 100, 0];
    const labels = o.pageSizeLabels || sizes.map(v => v === 0 ? 'Alle' : String(v));

    const tbl       = () => document.getElementById(tableId);
    const tbody     = () => { const t = tbl(); return t ? t.querySelector('tbody') : null; };
    const allRows   = () => { const b = tbody(); return b ? [...b.querySelectorAll('tr')] : []; };
    const filtered  = () => allRows().filter(r => r.dataset.fp !== 'hide');
    const sid       = 'evePag_' + tableId;

    function run() {
        const fRows   = filtered();
        const total   = fRows.length;
        const pgSize  = ps <= 0 ? Math.max(total, 1) : ps;
        const totPg   = Math.max(1, Math.ceil(total / pgSize));
        if (page > totPg) page = 1;
        try { localStorage.setItem(pageStorageKey, String(page)); } catch (_) {}
        const start   = (page - 1) * pgSize;
        const end     = Math.min(start + pgSize, total);
        const showSet = new Set(fRows.slice(start, end));

        allRows().forEach(r => {
            r.style.display = (r.dataset.fp === 'hide' || !showSet.has(r)) ? 'none' : '';
        });

        if (o.onCount) o.onCount(total);
        renderControls(total, start + 1, end, totPg);
    }

    function getControlContainers() {
        const ids = [];
        if (Array.isArray(o.controlsIds)) ids.push(...o.controlsIds);
        else if (o.controlsIds) ids.push(o.controlsIds);
        if (o.controlsId) ids.push(o.controlsId);
        return [...new Set(ids)]
            .map(id => document.getElementById(id))
            .filter(Boolean);
    }

    function renderControls(total, from, to, totPg) {
        const containers = getControlContainers();
        if (!containers.length) return;
        if (total === 0) {
            containers.forEach(c => { c.innerHTML = ''; });
            return;
        }

        let h = `<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 px-3 py-2" style="border-top:1px solid #1a2d3d;">`;

        // Left: "X–Y von Z"
        h += `<span class="small text-muted">${Math.min(from, total)}–${to} von ${total}</span>`;

        // Center: page buttons
        if (totPg > 1) {
            h += `<div class="d-flex align-items-center gap-1">`;
            h += `<button class="btn btn-sm eve-btn-ghost px-2" onclick="${sid}.prev()" ${page<=1?'disabled':''}>&lsaquo;</button>`;
            let s = Math.max(1, page - 2), e = Math.min(totPg, s + 4);
            if (e - s < 4) s = Math.max(1, e - 4);
            for (let i = s; i <= e; i++) {
                h += `<button class="btn btn-sm px-2 ${i===page?'eve-btn-primary':'eve-btn-ghost'}" onclick="${sid}.goTo(${i})">${i}</button>`;
            }
            h += `<button class="btn btn-sm eve-btn-ghost px-2" onclick="${sid}.next()" ${page>=totPg?'disabled':''}>&rsaquo;</button>`;
            h += `</div>`;
        } else {
            h += `<div></div>`;
        }

        // Right: page size buttons
        h += `<div class="d-flex align-items-center gap-1">`;
        sizes.forEach((sz, i) => {
            h += `<button class="btn btn-sm px-2 ${ps===sz?'eve-btn-primary':'eve-btn-ghost'}" onclick="${sid}.setSize(${sz})">${labels[i]}</button>`;
        });
        h += `</div></div>`;

        containers.forEach(c => { c.innerHTML = h; });
    }

    const api = {
        refresh()     { run(); },
        goTo(p)       { page = parseInt(p); run(); },
        prev()        { if (page > 1) { page--; run(); } },
        next()        { const t = filtered().length, pg = ps<=0?Math.max(t,1):ps; if (page < Math.ceil(t/pg)) { page++; run(); } },
        setSize(sz)   {
            ps = parseInt(sz);
            page = 1;
            try { localStorage.setItem(sizeStorageKey, String(ps)); } catch (_) {}
            run();
        },
        applyFilter(matchedRows) {
            page = 1;
            try { localStorage.setItem(pageStorageKey, '1'); } catch (_) {}
            if (matchedRows !== undefined) {
                const s = new Set(matchedRows);
                allRows().forEach(r => r.dataset.fp = s.has(r) ? '' : 'hide');
            }
            run();
        }
    };

    window[sid] = api;
    allRows().forEach(r => r.dataset.fp = '');
    run();
    return api;
}
