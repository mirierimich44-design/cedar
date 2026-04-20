/**
 * ApexPOS Cloud Sync Manager
 * Two-way sync: Online ↔ Offline
 *
 * Usage:
 *   CloudSync.init({ businessId: 1, syncToken: 'abc...', autoSync: true });
 *   CloudSync.pull();   // Download latest from server
 *   CloudSync.push();   // Upload offline changes
 *   CloudSync.sync();   // Full two-way sync
 */

const CloudSync = (function () {
    'use strict';

    // ── Config ─────────────────────────────────────────────────────────────
    const DB_NAME    = 'apexpos_offline';
    const DB_VERSION = 1;
    const STORES = ['products', 'contacts', 'transactions', 'stock',
                    'categories', 'tax_rates', 'locations', 'pending_push'];

    let config = {
        businessId  : null,
        syncToken   : null,
        autoSync    : true,
        autoInterval: 5 * 60 * 1000, // 5 minutes
        baseUrl     : '/sync',
    };

    let db         = null;
    let syncTimer  = null;
    let isSyncing  = false;

    // ── IndexedDB bootstrap ────────────────────────────────────────────────

    function openDB() {
        return new Promise((resolve, reject) => {
            if (db) { resolve(db); return; }

            const req = indexedDB.open(DB_NAME, DB_VERSION);

            req.onupgradeneeded = e => {
                const database = e.target.result;
                STORES.forEach(store => {
                    if (!database.objectStoreNames.contains(store)) {
                        const s = database.createObjectStore(store, { keyPath: 'id', autoIncrement: store === 'pending_push' });
                        if (store === 'products')     s.createIndex('updated_at', 'updated_at');
                        if (store === 'contacts')     s.createIndex('updated_at', 'updated_at');
                        if (store === 'transactions') s.createIndex('offline_id', 'offline_id', { unique: false });
                        if (store === 'stock')        s.createIndex('variation_id', 'variation_id');
                    }
                });
            };

            req.onsuccess = e => { db = e.target.result; resolve(db); };
            req.onerror   = e => reject(e.target.error);
        });
    }

    // ── IDB helpers ────────────────────────────────────────────────────────

    function idbPutAll(storeName, records) {
        return openDB().then(database => new Promise((resolve, reject) => {
            const tx    = database.transaction(storeName, 'readwrite');
            const store = tx.objectStore(storeName);
            records.forEach(r => store.put(r));
            tx.oncomplete = () => resolve(records.length);
            tx.onerror    = e  => reject(e.target.error);
        }));
    }

    function idbGetAll(storeName) {
        return openDB().then(database => new Promise((resolve, reject) => {
            const req = database.transaction(storeName, 'readonly')
                               .objectStore(storeName).getAll();
            req.onsuccess = e => resolve(e.target.result);
            req.onerror   = e => reject(e.target.error);
        }));
    }

    function idbClear(storeName) {
        return openDB().then(database => new Promise((resolve, reject) => {
            const req = database.transaction(storeName, 'readwrite')
                               .objectStore(storeName).clear();
            req.onsuccess = () => resolve();
            req.onerror   = e  => reject(e.target.error);
        }));
    }

    function idbAdd(storeName, record) {
        return openDB().then(database => new Promise((resolve, reject) => {
            const req = database.transaction(storeName, 'readwrite')
                               .objectStore(storeName).add(record);
            req.onsuccess = e => resolve(e.target.result); // returns new id
            req.onerror   = e => reject(e.target.error);
        }));
    }

    function idbDelete(storeName, key) {
        return openDB().then(database => new Promise((resolve, reject) => {
            const req = database.transaction(storeName, 'readwrite')
                               .objectStore(storeName).delete(key);
            req.onsuccess = () => resolve();
            req.onerror   = e  => reject(e.target.error);
        }));
    }

    // ── HTTP helpers ───────────────────────────────────────────────────────

    function apiPost(endpoint, payload) {
        return fetch(config.baseUrl + endpoint, {
            method: 'POST',
            headers: {
                'Content-Type'  : 'application/json',
                'X-Sync-Token'  : config.syncToken,
                'X-CSRF-TOKEN'  : document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                'Accept'        : 'application/json',
            },
            body: JSON.stringify(payload),
        }).then(async res => {
            const json = await res.json();
            if (!res.ok) throw new Error(json.error || 'Sync request failed');
            return json;
        });
    }

    function apiGet(endpoint) {
        return fetch(config.baseUrl + endpoint + '?sync_token=' + config.syncToken, {
            headers: { 'Accept': 'application/json', 'X-Sync-Token': config.syncToken },
        }).then(async res => {
            const json = await res.json();
            if (!res.ok) throw new Error(json.error || 'Sync request failed');
            return json;
        });
    }

    // ── Pull ───────────────────────────────────────────────────────────────

    async function pull() {
        if (!navigator.onLine) {
            _emit('pull:skipped', { reason: 'offline' });
            return;
        }
        _emit('pull:start');

        try {
            const res = await apiPost('/pull', {
                business_id: config.businessId,
                sync_token : config.syncToken,
            });

            const { data, pulled_at, summary } = res;

            // Persist all received data into IndexedDB
            const ops = [];
            if (data.products?.length)     ops.push(idbPutAll('products',     data.products));
            if (data.contacts?.length)     ops.push(idbPutAll('contacts',     data.contacts));
            if (data.transactions?.length) ops.push(idbPutAll('transactions', data.transactions));
            if (data.stock?.length)        ops.push(idbPutAll('stock',        _normaliseStock(data.stock)));
            if (data.categories?.length)   ops.push(idbPutAll('categories',   data.categories));
            if (data.tax_rates?.length)    ops.push(idbPutAll('tax_rates',    data.tax_rates));
            if (data.locations?.length)    ops.push(idbPutAll('locations',    data.locations));

            await Promise.all(ops);

            _setMeta('last_pulled_at', pulled_at);
            _emit('pull:success', { pulled_at, summary });
            _updateBadge('pull', 'success', summary);

            return res;
        } catch (err) {
            _emit('pull:error', { error: err.message });
            _updateBadge('pull', 'error', {});
            throw err;
        }
    }

    // ── Push ───────────────────────────────────────────────────────────────

    async function push() {
        if (!navigator.onLine) {
            _emit('push:skipped', { reason: 'offline' });
            return;
        }

        const pendingItems = await idbGetAll('pending_push');
        if (pendingItems.length === 0) {
            _emit('push:skipped', { reason: 'nothing_pending' });
            return;
        }

        _emit('push:start', { count: pendingItems.length });

        // Bucket by type
        const contacts     = pendingItems.filter(i => i.type === 'contact').map(i => i.data);
        const transactions = pendingItems.filter(i => i.type === 'transaction').map(i => i.data);
        const adjustments  = pendingItems.filter(i => i.type === 'stock_adjustment').map(i => i.data);

        try {
            const res = await apiPost('/push', {
                sync_token        : config.syncToken,
                contacts          : contacts,
                transactions      : transactions,
                stock_adjustments : adjustments,
            });

            // Clear pushed items from queue
            for (const item of pendingItems) {
                await idbDelete('pending_push', item.id);
            }

            _emit('push:success', res);
            _updateBadge('push', 'success', res.summary);

            return res;
        } catch (err) {
            _emit('push:error', { error: err.message });
            _updateBadge('push', 'error', {});
            throw err;
        }
    }

    // ── Full Two-Way Sync ──────────────────────────────────────────────────

    async function sync() {
        if (isSyncing) return;
        isSyncing = true;
        _emit('sync:start');

        try {
            // Push first (send local → server), then pull (get server → local)
            await push().catch(e => console.warn('[CloudSync] push error:', e));
            await pull().catch(e => console.warn('[CloudSync] pull error:', e));

            _setMeta('last_synced_at', new Date().toISOString());
            _emit('sync:complete');
        } finally {
            isSyncing = false;
        }
    }

    // ── Queue an offline change ────────────────────────────────────────────

    /**
     * Call this when user creates a transaction or contact while offline.
     * type: 'transaction' | 'contact' | 'stock_adjustment'
     */
    async function queueForPush(type, data) {
        const item = {
            type     : type,
            data     : data,
            queued_at: new Date().toISOString(),
            offline_id: data.offline_id ?? ('OFL-' + Date.now() + '-' + Math.random().toString(36).substr(2, 5)),
        };
        // Ensure offline_id is on the data too (for server idempotency)
        item.data.offline_id = item.offline_id;

        await idbAdd('pending_push', item);
        _emit('queue:added', { type, offline_id: item.offline_id });
        _updatePendingCount();

        return item.offline_id;
    }

    // ── Local queries (for offline POS) ───────────────────────────────────

    async function getProducts(query) {
        const all = await idbGetAll('products');
        if (!query) return all;
        const q = query.toLowerCase();
        return all.filter(p =>
            p.name?.toLowerCase().includes(q) ||
            p.sku?.toLowerCase().includes(q)
        );
    }

    async function getContacts(query) {
        const all = await idbGetAll('contacts');
        if (!query) return all;
        const q = query.toLowerCase();
        return all.filter(c =>
            c.name?.toLowerCase().includes(q) ||
            c.mobile?.includes(q)
        );
    }

    async function getStock(variationId, locationId) {
        const all = await idbGetAll('stock');
        return all.find(s => s.variation_id === variationId &&
                             s.location_id  === locationId) || { qty_available: 0 };
    }

    async function getPendingCount() {
        const items = await idbGetAll('pending_push');
        return items.length;
    }

    // ── Init ───────────────────────────────────────────────────────────────

    async function init(options) {
        Object.assign(config, options);

        await openDB();

        // Online/offline listeners
        window.addEventListener('online', () => {
            _emit('connection:online');
            _updateConnectionBadge(true);
            if (config.autoSync) sync();
        });

        window.addEventListener('offline', () => {
            _emit('connection:offline');
            _updateConnectionBadge(false);
        });

        // Auto-sync interval
        if (config.autoSync) {
            syncTimer = setInterval(sync, config.autoInterval);
            // Do an initial sync after 2s
            setTimeout(sync, 2000);
        }

        _updateConnectionBadge(navigator.onLine);
        await _updatePendingCount();

        return {
            pull, push, sync, queueForPush,
            getProducts, getContacts, getStock, getPendingCount,
        };
    }

    // ── Event emitter ──────────────────────────────────────────────────────

    const _listeners = {};

    function on(event, fn) { (_listeners[event] = _listeners[event] || []).push(fn); }

    function _emit(event, detail) {
        (_listeners[event] || []).forEach(fn => fn(detail || {}));
        // Also dispatch a DOM event for components that don't import this module
        window.dispatchEvent(new CustomEvent('cloud-sync:' + event, { detail: detail || {} }));
    }

    // ── UI helpers ─────────────────────────────────────────────────────────

    function _updateConnectionBadge(online) {
        const badge = document.getElementById('sync-connection-badge');
        if (!badge) return;
        badge.className = online
            ? 'tw-inline-flex tw-items-center tw-gap-1 tw-text-xs tw-font-semibold tw-text-green-700 tw-bg-green-100 tw-rounded-full tw-px-2 tw-py-0.5'
            : 'tw-inline-flex tw-items-center tw-gap-1 tw-text-xs tw-font-semibold tw-text-red-700 tw-bg-red-100 tw-rounded-full tw-px-2 tw-py-0.5';
        badge.innerHTML = online
            ? '<span class="tw-w-1.5 tw-h-1.5 tw-rounded-full tw-bg-green-500"></span> Online'
            : '<span class="tw-w-1.5 tw-h-1.5 tw-rounded-full tw-bg-red-500"></span> Offline';
    }

    function _updateBadge(direction, status, summary) {
        const el = document.getElementById('sync-last-' + direction);
        if (!el) return;
        const count = summary ? Object.values(summary).reduce((a, b) => a + b, 0) : 0;
        el.textContent = status === 'success'
            ? `Last ${direction}: ${new Date().toLocaleTimeString()} (${count} records)`
            : `Last ${direction}: FAILED`;
    }

    async function _updatePendingCount() {
        const count = await getPendingCount();
        const el = document.getElementById('sync-pending-count');
        if (el) el.textContent = count;
        const badge = document.getElementById('sync-pending-badge');
        if (badge) {
            badge.style.display = count > 0 ? 'inline-flex' : 'none';
            badge.textContent   = count;
        }
    }

    function _setMeta(key, value) {
        try { localStorage.setItem('apexpos_sync_' + key, value); } catch (_) {}
    }

    function _normaliseStock(stockRows) {
        // stock rows from server have variation_id + location_id — create a composite ID
        return stockRows.map(s => ({
            ...s,
            id: `${s.variation_id}_${s.location_id}`,
        }));
    }

    // ── Public API ─────────────────────────────────────────────────────────
    return { init, pull, push, sync, queueForPush, on, getProducts, getContacts, getStock, getPendingCount };

})();

// Auto-init if data attributes are present on the <body>
document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const token      = body.dataset.syncToken;
    const businessId = parseInt(body.dataset.businessId, 10);

    if (token && businessId) {
        CloudSync.init({ businessId, syncToken: token, autoSync: true })
            .then(() => console.log('[CloudSync] Initialized for business #' + businessId));
    }
});
