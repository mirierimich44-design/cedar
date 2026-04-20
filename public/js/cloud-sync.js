/**
 * ApexPOS Cloud Sync Manager  v2
 * ═══════════════════════════════
 * Two-way sync between any device (browser / Laragon) and the cloud server.
 *
 * How it works:
 *  1. Register this device once  → get a unique sync token
 *  2. PULL  (cloud → device)     → products, contacts, stock cached in IndexedDB
 *  3. PUSH  (device → cloud)     → sales made offline uploaded to server
 *  4. Auto-sync every 5 min while online
 *
 * Cross-domain (Laragon ↔ Live server):
 *  Set data-sync-remote="https://reenson.apextechsolutions.co.ke" on <body>
 *  OR call CloudSync.init({ remoteUrl: 'https://...' })
 *
 * Auto-init: add these data attributes to <body>:
 *   data-sync-token="your-token"
 *   data-business-id="1"
 *   data-sync-remote="https://reenson.apextechsolutions.co.ke"  ← optional, for cross-domain
 */

const CloudSync = (function () {
    'use strict';

    // ── Constants ──────────────────────────────────────────────────────────
    const DB_NAME    = 'apexpos_offline';
    const DB_VERSION = 2;
    const STORES = ['products', 'contacts', 'transactions', 'stock',
                    'categories', 'tax_rates', 'locations', 'units',
                    'brands', 'pending_push', 'meta'];

    // ── State ──────────────────────────────────────────────────────────────
    let cfg = {
        businessId  : null,
        syncToken   : null,
        remoteUrl   : '',          // '' = same origin; 'https://example.com' = cross-domain
        autoSync    : true,
        autoInterval: 5 * 60 * 1000,
    };

    let idb        = null;
    let syncTimer  = null;
    let isSyncing  = false;
    let isOnline   = navigator.onLine;

    // ── IndexedDB ──────────────────────────────────────────────────────────

    function openDB() {
        if (idb) return Promise.resolve(idb);
        return new Promise((res, rej) => {
            const req = indexedDB.open(DB_NAME, DB_VERSION);
            req.onupgradeneeded = e => {
                const d = e.target.result;
                STORES.forEach(name => {
                    if (d.objectStoreNames.contains(name)) return;
                    const store = d.createObjectStore(name, {
                        keyPath      : name === 'pending_push' || name === 'meta' ? 'id' : 'id',
                        autoIncrement: name === 'pending_push',
                    });
                    if (name === 'stock')   store.createIndex('variation_id', 'variation_id');
                    if (name === 'products') store.createIndex('sku', 'sku');
                    if (name === 'contacts') store.createIndex('mobile', 'mobile');
                    if (name === 'meta')     store.createIndex('key', 'key', { unique: true });
                });
            };
            req.onsuccess = e => { idb = e.target.result; res(idb); };
            req.onerror   = e => rej(e.target.error);
        });
    }

    function dbRun(storeName, mode, fn) {
        return openDB().then(d => new Promise((res, rej) => {
            const tx = d.transaction(storeName, mode);
            const st = tx.objectStore(storeName);
            fn(st, res, rej, tx);
            tx.onerror = e => rej(e.target.error);
        }));
    }

    function putAll(store, rows)   { return dbRun(store, 'readwrite', (s, res) => { rows.forEach(r => s.put(r)); s.transaction.oncomplete = () => res(rows.length); }); }
    function getAll(store)         { return dbRun(store, 'readonly',  (s, res) => { const r = s.getAll(); r.onsuccess = e => res(e.target.result); }); }
    function clearStore(store)     { return dbRun(store, 'readwrite', (s, res) => { s.clear(); s.transaction.oncomplete = () => res(); }); }
    function addOne(store, record) { return dbRun(store, 'readwrite', (s, res) => { const r = s.add(record); r.onsuccess = e => res(e.target.result); }); }
    function deleteOne(store, key) { return dbRun(store, 'readwrite', (s, res) => { s.delete(key); s.transaction.oncomplete = () => res(); }); }

    function getMeta(key) {
        return dbRun('meta', 'readonly', (s, res) => {
            const idx = s.index('key');
            const r   = idx.get(key);
            r.onsuccess = e => res(e.target.result?.value ?? null);
        });
    }

    function setMeta(key, value) {
        return dbRun('meta', 'readwrite', (s, res) => {
            s.put({ id: key, key, value });
            s.transaction.oncomplete = () => res();
        });
    }

    // ── HTTP helpers ───────────────────────────────────────────────────────

    function apiUrl(path) {
        const base = cfg.remoteUrl ? cfg.remoteUrl.replace(/\/$/, '') : '';
        return base + '/sync' + path;
    }

    function apiPost(path, body) {
        return fetch(apiUrl(path), {
            method : 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'Accept'       : 'application/json',
                'X-Sync-Token' : cfg.syncToken,
                'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
            credentials: cfg.remoteUrl ? 'omit' : 'same-origin',
            body: JSON.stringify(body),
        }).then(_handleResponse);
    }

    function apiGet(path) {
        return fetch(apiUrl(path) + '?sync_token=' + encodeURIComponent(cfg.syncToken), {
            headers    : { 'Accept': 'application/json', 'X-Sync-Token': cfg.syncToken },
            credentials: cfg.remoteUrl ? 'omit' : 'same-origin',
        }).then(_handleResponse);
    }

    function _handleResponse(res) {
        return res.json().then(json => {
            if (!res.ok) throw new Error(json.error || `HTTP ${res.status}`);
            return json;
        });
    }

    // ── PULL  (cloud → device) ─────────────────────────────────────────────

    async function pull() {
        if (!isOnline) { _emit('pull:skipped', { reason: 'offline' }); return null; }
        _setButtonState('pull', 'loading');
        _emit('pull:start');

        try {
            const lastPulled = await getMeta('last_pulled_at');
            const res = await apiPost('/pull', {
                business_id: cfg.businessId,
                sync_token : cfg.syncToken,
                since      : lastPulled,
            });

            const d = res.data;
            const ops = [];

            // Store each dataset — normalise stock to composite key
            if (d.products?.length)     ops.push(putAll('products',   d.products));
            if (d.contacts?.length)     ops.push(putAll('contacts',   d.contacts));
            if (d.transactions?.length) ops.push(putAll('transactions', d.transactions));
            if (d.categories?.length)   ops.push(putAll('categories', d.categories));
            if (d.tax_rates?.length)    ops.push(putAll('tax_rates',  d.tax_rates));
            if (d.locations?.length)    ops.push(putAll('locations',  d.locations));
            if (d.units?.length)        ops.push(putAll('units',      d.units));
            if (d.brands?.length)       ops.push(putAll('brands',     d.brands));
            if (d.stock?.length) {
                const normalised = d.stock.map(s => ({ ...s, id: `${s.variation_id}_${s.location_id}` }));
                ops.push(putAll('stock', normalised));
            }

            await Promise.all(ops);
            await setMeta('last_pulled_at', res.pulled_at);
            await setMeta('last_pull_summary', JSON.stringify(res.summary));

            _emit('pull:success', { pulled_at: res.pulled_at, summary: res.summary });
            _setButtonState('pull', 'idle');
            _updateLastSyncLabel('pull', res.pulled_at, res.summary);
            return res;
        } catch (err) {
            _emit('pull:error', { error: err.message });
            _setButtonState('pull', 'error');
            console.error('[CloudSync] pull error:', err);
            throw err;
        }
    }

    // ── PUSH  (device → cloud) ─────────────────────────────────────────────

    async function push() {
        if (!isOnline) { _emit('push:skipped', { reason: 'offline' }); return null; }

        const pending = await getAll('pending_push');
        if (!pending.length) {
            _emit('push:skipped', { reason: 'nothing_pending' });
            return { summary: {}, status: 'skipped' };
        }

        _setButtonState('push', 'loading');
        _emit('push:start', { count: pending.length });

        const contacts     = pending.filter(i => i.type === 'contact').map(i => i.data);
        const transactions = pending.filter(i => i.type === 'transaction').map(i => i.data);
        const adjustments  = pending.filter(i => i.type === 'stock_adjustment').map(i => i.data);

        try {
            const res = await apiPost('/push', {
                sync_token       : cfg.syncToken,
                contacts,
                transactions,
                stock_adjustments: adjustments,
            });

            // Remove successfully pushed items
            for (const item of pending) {
                await deleteOne('pending_push', item.id);
            }

            await setMeta('last_pushed_at', new Date().toISOString());
            _emit('push:success', res);
            _setButtonState('push', 'idle');
            _updateLastSyncLabel('push', new Date().toISOString(), res.summary);
            await _refreshPendingCount();
            return res;
        } catch (err) {
            _emit('push:error', { error: err.message });
            _setButtonState('push', 'error');
            console.error('[CloudSync] push error:', err);
            throw err;
        }
    }

    // ── FULL SYNC ──────────────────────────────────────────────────────────

    async function sync() {
        if (isSyncing) return;
        isSyncing = true;
        _setButtonState('sync', 'loading');
        _emit('sync:start');

        try {
            await push().catch(e => console.warn('[CloudSync] push partial error:', e.message));
            await pull().catch(e => console.warn('[CloudSync] pull partial error:', e.message));
            await setMeta('last_synced_at', new Date().toISOString());
            _emit('sync:complete');
        } finally {
            isSyncing = false;
            _setButtonState('sync', 'idle');
        }
    }

    // ── QUEUE (offline write) ──────────────────────────────────────────────

    /**
     * Queue a record to be pushed next time we go online.
     *
     * @param {string} type  'transaction' | 'contact' | 'stock_adjustment'
     * @param {object} data  The record payload
     * @returns {string}     offline_id (use this as temp ID in your UI)
     */
    async function queue(type, data) {
        const offline_id = data.offline_id
            || ('OFL-' + cfg.businessId + '-' + Date.now() + '-' + Math.random().toString(36).slice(2, 7));

        data.offline_id = offline_id;

        await addOne('pending_push', { type, data, queued_at: new Date().toISOString() });
        _emit('queue:added', { type, offline_id });
        await _refreshPendingCount();

        // If online, push immediately
        if (isOnline) {
            push().catch(() => {});
        }

        return offline_id;
    }

    // ── OFFLINE QUERIES ────────────────────────────────────────────────────

    async function searchProducts(q) {
        const all = await getAll('products');
        if (!q) return all;
        const s = q.toLowerCase();
        return all.filter(p => p.name?.toLowerCase().includes(s) || p.sku?.toLowerCase().includes(s));
    }

    async function searchContacts(q) {
        const all = await getAll('contacts');
        if (!q) return all;
        const s = q.toLowerCase();
        return all.filter(c => c.name?.toLowerCase().includes(s) || c.mobile?.includes(s));
    }

    async function getStockLevel(variationId, locationId) {
        const all = await getAll('stock');
        const row = all.find(s => s.variation_id == variationId && s.location_id == locationId);
        return row?.qty_available ?? 0;
    }

    async function pendingCount() {
        const items = await getAll('pending_push');
        return items.length;
    }

    // ── STATUS ─────────────────────────────────────────────────────────────

    async function getLocalStatus() {
        const [lastPull, lastPush, lastSync, pending] = await Promise.all([
            getMeta('last_pulled_at'),
            getMeta('last_pushed_at'),
            getMeta('last_synced_at'),
            pendingCount(),
        ]);
        return { lastPull, lastPush, lastSync, pending, online: isOnline };
    }

    // ── INIT ───────────────────────────────────────────────────────────────

    async function init(options = {}) {
        Object.assign(cfg, options);

        await openDB();

        // Restore last sync times into UI labels
        const [lp, ls] = await Promise.all([getMeta('last_pulled_at'), getMeta('last_pushed_at')]);
        if (lp) _updateLastSyncLabel('pull', lp);
        if (ls) _updateLastSyncLabel('push', ls);
        await _refreshPendingCount();

        // Online/offline events
        window.addEventListener('online',  _goOnline);
        window.addEventListener('offline', _goOffline);
        _updateConnectionBadge(isOnline);

        // Auto-sync timer
        if (cfg.autoSync) {
            clearInterval(syncTimer);
            syncTimer = setInterval(sync, cfg.autoInterval);
            // Initial sync after 3s
            setTimeout(sync, 3000);
        }

        // Expose to window for console debugging
        window._CloudSync = { pull, push, sync, queue, searchProducts, searchContacts, getStockLevel, getLocalStatus, cfg };

        return { pull, push, sync, queue, searchProducts, searchContacts, getStockLevel, pendingCount, getLocalStatus };
    }

    function _goOnline() {
        isOnline = true;
        _updateConnectionBadge(true);
        _emit('online');
        if (cfg.autoSync) sync();
    }

    function _goOffline() {
        isOnline = false;
        _updateConnectionBadge(false);
        _emit('offline');
    }

    // ── EVENTS ─────────────────────────────────────────────────────────────

    const _handlers = {};

    function on(event, fn) {
        (_handlers[event] = _handlers[event] || []).push(fn);
        return () => { _handlers[event] = _handlers[event].filter(h => h !== fn); }; // returns unsubscribe
    }

    function _emit(event, detail = {}) {
        (_handlers[event] || []).forEach(fn => fn(detail));
        window.dispatchEvent(new CustomEvent('cs:' + event, { detail }));
    }

    // ── UI HELPERS ─────────────────────────────────────────────────────────

    const ICONS = {
        idle   : { pull: '↓ Pull', push: '↑ Push', sync: '⟳ Sync' },
        loading: { pull: 'Pulling…', push: 'Pushing…', sync: 'Syncing…' },
        error  : { pull: 'Retry Pull', push: 'Retry Push', sync: 'Retry' },
    };

    function _setButtonState(action, state) {
        const btn = document.getElementById('btn-' + action);
        if (!btn) return;
        btn.disabled = state === 'loading';
        btn.textContent = ICONS[state]?.[action] ?? ICONS.idle[action];
        btn.className = btn.className
            .replace(/tw-bg-\w+-\d+/g, '')
            .trimEnd();
        const colours = { pull: 'tw-bg-blue-600', push: 'tw-bg-green-600', sync: 'tw-bg-purple-600' };
        if (state === 'error')   btn.classList.add('tw-bg-red-600');
        else if (state === 'loading') btn.classList.add('tw-opacity-60');
        else btn.classList.add(colours[action]);
    }

    function _updateConnectionBadge(online) {
        const el = document.getElementById('sync-connection-badge');
        if (!el) return;
        el.className = online
            ? 'tw-inline-flex tw-items-center tw-gap-1 tw-text-xs tw-font-semibold tw-text-green-700 tw-bg-green-100 tw-rounded-full tw-px-3 tw-py-1'
            : 'tw-inline-flex tw-items-center tw-gap-1 tw-text-xs tw-font-semibold tw-text-red-700 tw-bg-red-100 tw-rounded-full tw-px-3 tw-py-1';
        el.innerHTML = online
            ? '<span class="tw-w-1.5 tw-h-1.5 tw-rounded-full tw-bg-green-500 tw-animate-pulse"></span> Online'
            : '<span class="tw-w-1.5 tw-h-1.5 tw-rounded-full tw-bg-red-500"></span> Offline';
    }

    function _updateLastSyncLabel(dir, isoTime, summary) {
        const el = document.getElementById('sync-last-' + dir);
        if (!el) return;
        const d    = new Date(isoTime);
        const time = d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const date = d.toLocaleDateString([], { day: 'numeric', month: 'short' });
        const total = summary ? Object.values(summary).reduce((a, b) => a + b, 0) : null;
        el.textContent = `Last ${dir}: ${time}, ${date}${total !== null ? ` · ${total} records` : ''}`;
    }

    async function _refreshPendingCount() {
        const n   = await pendingCount();
        const el  = document.getElementById('sync-pending-count');
        const bdg = document.getElementById('sync-pending-badge');
        if (el)  el.textContent = n;
        if (bdg) { bdg.textContent = n; bdg.style.display = n > 0 ? 'inline-flex' : 'none'; }
    }

    // ── PUBLIC API ─────────────────────────────────────────────────────────
    return { init, pull, push, sync, queue, on, searchProducts, searchContacts, getStockLevel, pendingCount, getLocalStatus };
})();

// ── Auto-init from <body> data attributes ────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const body       = document.body;
    const token      = body.dataset.syncToken      || localStorage.getItem('apexpos_sync_token_' + body.dataset.businessId);
    const businessId = parseInt(body.dataset.businessId, 10);
    const remoteUrl  = body.dataset.syncRemote     || localStorage.getItem('apexpos_sync_remote') || '';

    if (token && businessId) {
        CloudSync.init({ businessId, syncToken: token, remoteUrl, autoSync: true })
            .then(() => console.info('[CloudSync] Ready. Remote:', remoteUrl || '(same origin)'));
    }
});
