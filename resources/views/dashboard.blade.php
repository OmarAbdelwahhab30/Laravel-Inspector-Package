<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
:root,
html[data-theme="default"] {
    --bg: #ffffff;
    --bg-secondary: #f3f3f3;
    --bg-hover: #f1f3f4;
    --border: #e0e0e0;
    --text: #202124;
    --text-secondary: #5f6368;
    --accent: #ff2d20;
    --accent-bg: rgba(255, 45, 32, 0.08);
    --status-2xx: #1e8e3e;
    --status-3xx: #5f6368;
    --status-4xx: #e8710a;
    --status-5xx: #d93025;
}

html[data-theme="dark"] {
    --bg: #202124;
    --bg-secondary: #292a2d;
    --bg-hover: #303134;
    --border: #3c4043;
    --text: #e8eaed;
    --text-secondary: #9aa0a6;
    --accent: #ff6b54;
    --accent-bg: rgba(255, 107, 84, 0.14);
    --status-2xx: #81c995;
    --status-3xx: #9aa0a6;
    --status-4xx: #fdd663;
    --status-5xx: #f28b82;
}

* {
    box-sizing: border-box;
}

html,
body {
    height: 100%;
    margin: 0;
    padding: 0;
    background: var(--bg);
    color: var(--text);
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
    font-size: 11px;
}

code,
.mono {
    font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
}

.toolbar {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 28px;
    padding: 0 8px;
    border-bottom: 1px solid var(--border);
    background: var(--bg-secondary);
}

.toolbar__title {
    font-weight: 600;
}

.toolbar__actions {
    display: flex;
    align-items: center;
    gap: 6px;
}

.toolbar__button {
    font-size: 11px;
    padding: 2px 10px;
    border: 1px solid var(--border);
    border-radius: 3px;
    background: var(--bg);
    color: var(--text);
    cursor: pointer;
}

.toolbar__button:hover {
    background: var(--bg-hover);
}

.settings-panel {
    position: absolute;
    top: 28px;
    right: 8px;
    z-index: 10;
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 220px;
    padding: 10px;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.settings-row {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.settings-row label {
    color: var(--text-secondary);
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.settings-row select,
.settings-row input {
    font-size: 11px;
    padding: 3px 5px;
    background: var(--bg);
    color: var(--text);
    border: 1px solid var(--border);
    border-radius: 3px;
    font-family: inherit;
}

.split {
    display: flex;
    height: calc(100% - 28px);
}

.list-pane {
    width: 46%;
    min-width: 280px;
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.list-header {
    display: grid;
    grid-template-columns: 1fr 56px 64px;
    padding: 3px 8px;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border);
    color: var(--text-secondary);
    font-weight: 600;
    text-transform: uppercase;
    font-size: 9px;
    letter-spacing: 0.03em;
}

.list-body {
    flex: 1;
    overflow-y: auto;
}

.request-row {
    display: grid;
    grid-template-columns: 1fr 56px 64px;
    align-items: center;
    padding: 4px 8px;
    border-bottom: 1px solid var(--border);
    cursor: default;
    white-space: nowrap;
}

.request-row:hover {
    background: var(--bg-hover);
}

.request-row.is-selected {
    background: var(--accent-bg);
    border-left: 3px solid var(--accent);
    padding-left: 5px;
}

.request-row.is-plain {
    color: var(--text-secondary);
}

.request-row__name {
    overflow: hidden;
    text-overflow: ellipsis;
    display: flex;
    align-items: center;
    gap: 5px;
}

.laravel-dot {
    flex: 0 0 auto;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--accent);
}

.status-badge {
    font-weight: 600;
}

.status-2xx { color: var(--status-2xx); }
.status-3xx { color: var(--status-3xx); }
.status-4xx { color: var(--status-4xx); }
.status-5xx { color: var(--status-5xx); }

.detail-pane {
    flex: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.empty-state {
    color: var(--text-secondary);
    padding: 16px;
}

.empty-state--detail {
    max-width: 320px;
    margin: auto;
    text-align: center;
    line-height: 1.5;
}

.hidden {
    display: none !important;
}

.detail-content {
    display: flex;
    flex-direction: column;
    min-height: 100%;
}

.detail-title {
    padding: 8px 12px;
    font-weight: 600;
    border-bottom: 1px solid var(--border);
    word-break: break-all;
}

.tabs {
    display: flex;
    border-bottom: 1px solid var(--border);
    background: var(--bg-secondary);
}

.tab {
    font-size: 11px;
    padding: 6px 12px;
    border: none;
    background: none;
    color: var(--text-secondary);
    cursor: pointer;
    border-bottom: 2px solid transparent;
}

.tab:hover {
    color: var(--text);
}

.tab.is-active {
    color: var(--text);
    border-bottom-color: var(--accent);
    font-weight: 600;
}

.tab-panels {
    flex: 1;
    padding: 12px;
}

.tab-panel {
    display: none;
}

.tab-panel.is-active {
    display: block;
}

.kv-table {
    display: grid;
    grid-template-columns: 110px 1fr;
    row-gap: 6px;
    column-gap: 12px;
}

.kv-table dt {
    color: var(--text-secondary);
    font-weight: 600;
}

.kv-table dd {
    margin: 0;
    word-break: break-all;
}

.reserved-note {
    color: var(--text-secondary);
    font-style: italic;
}

.file-link {
    color: var(--accent);
    text-decoration: none;
    cursor: pointer;
}

.file-link:hover {
    text-decoration: underline;
}

.file-link--error {
    color: var(--status-5xx);
}

.error-note {
    color: var(--status-5xx);
}

/* ─── Timeline ─── */

.timeline-waterfall {
    padding: 12px 0;
}

.collector-summary {
    padding: 12px 12px 0;
    color: var(--text-secondary);
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 600;
}

.timeline-row {
    display: grid;
    grid-template-columns: 200px 1fr 60px;
    align-items: center;
    padding: 4px 12px;
    gap: 12px;
    border-bottom: 1px solid var(--border);
    cursor: pointer;
}

.timeline-row:hover {
    background: var(--bg-hover);
}

.timeline-label {
    display: flex;
    align-items: center;
    gap: 8px;
    overflow: hidden;
}

.timeline-type-badge {
    font-size: 9px;
    text-transform: uppercase;
    padding: 2px 4px;
    border-radius: 3px;
    font-weight: 600;
    color: #fff;
    flex-shrink: 0;
}

.timeline-entry-label {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 11px;
}

.timeline-bar-container {
    position: relative;
    height: 14px;
    background: var(--bg-secondary);
    border-radius: 2px;
    overflow: hidden;
}

.timeline-bar {
    position: absolute;
    height: 100%;
    top: 0;
    min-width: 2px;
    border-radius: 2px;
}

.timeline-time {
    text-align: right;
    font-size: 10px;
    color: var(--text-secondary);
    white-space: nowrap;
}

/* ─── Modal ─── */

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal.hidden {
    display: none !important;
}

.modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
}

.modal-content {
    position: relative;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 6px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    width: 90%;
    max-width: 600px;
    max-height: 90%;
    display: flex;
    flex-direction: column;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    border-bottom: 1px solid var(--border);
    background: var(--bg-secondary);
    border-radius: 6px 6px 0 0;
}

.modal-title {
    font-weight: 600;
    font-size: 13px;
}

.modal-close {
    background: none;
    border: none;
    font-size: 18px;
    color: var(--text-secondary);
    cursor: pointer;
    line-height: 1;
    padding: 0;
}

.modal-close:hover {
    color: var(--text);
}

.modal-body {
    padding: 14px;
    overflow-y: auto;
    word-break: break-all;
    font-size: 12px;
    line-height: 1.5;
}

</style>
</head>
<body>
    <header class="toolbar">
        <span class="toolbar__title">Laravel Inspector</span>
        <div class="toolbar__actions">
            <button id="clear-btn" class="toolbar__button">Clear</button>
            <button id="settings-btn" class="toolbar__button" title="Settings">&#9881;</button>
        </div>
    </header>

    <div id="settings-panel" class="settings-panel hidden">
        <div class="settings-row">
            <label for="setting-theme">Theme</label>
            <select id="setting-theme">
                <option value="auto">Auto (match DevTools)</option>
                <option value="default">Light</option>
                <option value="dark">Dark</option>
            </select>
        </div>
    </div>

    <main class="split">
        <section class="list-pane">
            <div class="list-header">
                <span class="col col--name">Name</span>
                <span class="col col--status">Status</span>
                <span class="col col--time">Time</span>
            </div>
            <div id="request-list" class="list-body">
                <div class="empty-state">Waiting for requests&hellip;</div>
            </div>
        </section>

        <section class="detail-pane">
            <div id="detail-empty" class="empty-state empty-state--detail">
                Select a request tagged with <code>X-Laravel-Devtools-Request</code> to see its backend execution.
            </div>

            <div id="detail-content" class="detail-content hidden">
                <div class="detail-title" id="detail-title"></div>

                <nav class="tabs" id="tabs">
                    <button class="tab is-active" data-tab="overview">Overview</button>
                    <button class="tab" data-tab="queries">Queries</button>
                    <button class="tab" data-tab="events">Events</button>
                    <button class="tab" data-tab="jobs">Jobs</button>
                    <button class="tab" data-tab="timeline">Timeline</button>
                </nav>

                <div class="tab-panels">
                    <div class="tab-panel is-active" data-panel="overview" id="panel-overview"></div>
                    <div class="tab-panel" data-panel="queries" id="panel-queries"></div>
                    <div class="tab-panel" data-panel="events" id="panel-events"></div>
                    <div class="tab-panel" data-panel="jobs" id="panel-jobs"></div>
                    <div class="tab-panel" data-panel="timeline" id="panel-timeline"></div>
                </div>
            </div>
        </section>
    </main>

    <div id="timeline-modal" class="modal hidden">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title" id="timeline-modal-title">Entry Details</span>
                <button class="modal-close" id="timeline-modal-close">&times;</button>
            </div>
            <div class="modal-body" id="timeline-modal-body"></div>
        </div>
    </div>

    <script>
const LaravelInspectorSettings = (() => {
    const STORAGE_KEY = 'laravel_inspector_settings';
    const DEFAULT_SETTINGS = { themeMode: 'auto' };

    async function load() {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            return { ...DEFAULT_SETTINGS, ...(stored ? JSON.parse(stored) : {}) };
        } catch (e) {
            return DEFAULT_SETTINGS;
        }
    }

    async function save(partial) {
        const current = await load();
        const next = { ...current, ...partial };
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(next));
        } catch (e) {}
        return next;
    }

    return { DEFAULT_SETTINGS, load, save };
})();
</script>
    <script>
const state = {
    nextId: 1,
    entries: [],
    selectedId: null,
    settings: LaravelInspectorSettings.DEFAULT_SETTINGS,
    lastSnapshot: null,
    lastOrigin: null,
};

const listEl = document.getElementById('request-list');
const detailEmpty = document.getElementById('detail-empty');
const detailContent = document.getElementById('detail-content');
const detailTitle = document.getElementById('detail-title');
const tabsEl = document.getElementById('tabs');
const settingsBtn = document.getElementById('settings-btn');
const settingsPanel = document.getElementById('settings-panel');
const themeSelect = document.getElementById('setting-theme');

const DEFAULT_EMPTY_MESSAGE = detailEmpty.innerHTML;

function applyTheme(settings) {
    const mode = settings.themeMode === 'auto'
        ? (chrome.devtools.panels.themeName === 'dark' ? 'dark' : 'default')
        : settings.themeMode;

    document.documentElement.dataset.theme = mode;
}

function initSettingsUi(settings) {
    themeSelect.value = settings.themeMode;
}

async function updateSettings(partial) {
    state.settings = await LaravelInspectorSettings.save(partial);
    applyTheme(state.settings);

    if (state.lastSnapshot) {
        renderOverview(document.getElementById('panel-overview'), state.lastSnapshot);
    }
}

settingsBtn.addEventListener('click', (event) => {
    event.stopPropagation();
    settingsPanel.classList.toggle('hidden');
});

document.addEventListener('click', (event) => {
    if (!settingsPanel.classList.contains('hidden') && !settingsPanel.contains(event.target)) {
        settingsPanel.classList.add('hidden');
    }
});

themeSelect.addEventListener('change', () => {
    updateSettings({ themeMode: themeSelect.value });
});

function statusClass(status) {
    if (status >= 500) return 'status-5xx';
    if (status >= 400) return 'status-4xx';
    if (status >= 300) return 'status-3xx';
    return 'status-2xx';
}

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (c) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
    }[c]));
}

function shortUrl(url) {
    try {
        const u = new URL(url);
        return u.pathname + u.search;
    } catch {
        return url;
    }
}

function renderList() {
    if (state.entries.length === 0) {
        listEl.innerHTML = '<div class="empty-state">Waiting for requests&hellip;</div>';
        return;
    }

    listEl.innerHTML = '';

    for (const entry of state.entries) {
        const row = document.createElement('div');
        row.className = 'request-row'
            + (entry.laravelRequestId ? '' : ' is-plain')
            + (entry.id === state.selectedId ? ' is-selected' : '');
        row.dataset.id = String(entry.id);

        row.innerHTML = `
            <span class="request-row__name">${entry.laravelRequestId ? '<span class="laravel-dot"></span>' : ''}<span>${escapeHtml(entry.method)} ${escapeHtml(shortUrl(entry.url))}</span></span>
            <span class="status-badge ${statusClass(entry.status)}">${entry.status}</span>
            <span>${Math.round(entry.time)}ms</span>
        `;

        row.addEventListener('click', () => selectEntry(entry.id));
        listEl.appendChild(row);
    }
}

function addEntry(entry) {
    if (!entry.laravelRequestId) {
        return;
    }
    entry.id = state.nextId++;
    state.entries.push(entry);
    renderList();
}

function showEmptyDetail(message) {
    detailContent.classList.add('hidden');
    detailEmpty.classList.remove('hidden');
    detailEmpty.innerHTML = message;
}

function selectEntry(id) {
    state.selectedId = id;
    renderList();

    const entry = state.entries.find((e) => e.id === id);

    if (!entry.laravelRequestId) {
        showEmptyDetail('This request has no <code>X-Laravel-Devtools-Request</code> header &mdash; nothing to show.');
        return;
    }

    detailEmpty.classList.add('hidden');
    detailContent.classList.remove('hidden');
    detailTitle.textContent = `${entry.method} ${shortUrl(entry.url)}`;
    loadSnapshot(entry);
}

function panels() {
    return {
        overview: document.getElementById('panel-overview'),
        queries: document.getElementById('panel-queries'),
        events: document.getElementById('panel-events'),
        jobs: document.getElementById('panel-jobs'),
        timeline: document.getElementById('panel-timeline'),
    };
}

async function loadSnapshot(entry) {
    const p = panels();
    p.overview.innerHTML = '<div class="empty-state">Loading&hellip;</div>';
    p.queries.innerHTML = '';
    p.events.innerHTML = '';
    p.jobs.innerHTML = '';
    p.timeline.innerHTML = '';

    let origin;
    try {
        origin = new URL(entry.url, window.location.origin).origin;
    } catch {
        p.overview.innerHTML = '<div class="error-note">Could not determine the request\'s origin.</div>';
        return;
    }

    let snapshot;
    try {
        const res = await fetch(`${origin}/__devtools/request/${encodeURIComponent(entry.laravelRequestId)}`);

        if (!res.ok) {
            p.overview.innerHTML = `<div class="error-note">Snapshot not found (HTTP ${res.status}). Is <code>LARAVEL_DEVTOOLS_ENABLED=true</code> set on ${escapeHtml(origin)}?</div>`;
            return;
        }

        snapshot = await res.json();
    } catch (err) {
        p.overview.innerHTML = `<div class="error-note">Could not reach ${escapeHtml(origin)} &mdash; ${escapeHtml(err.message)}</div>`;
        return;
    }

    state.lastSnapshot = snapshot;
    state.lastOrigin = origin;

    renderOverview(p.overview, snapshot);
    renderQueries(p.queries, snapshot.queries);
    renderEvents(p.events, snapshot.events);
    renderJobs(p.jobs, snapshot.jobs);
    renderTimeline(p.timeline, snapshot);

    document.querySelector('.tab[data-tab="queries"]').textContent = `Queries${snapshot.queries && snapshot.queries.length ? ` (${snapshot.queries.length})` : ''}`;
    document.querySelector('.tab[data-tab="events"]').textContent = `Events${snapshot.events && snapshot.events.length ? ` (${snapshot.events.length})` : ''}`;
    document.querySelector('.tab[data-tab="jobs"]').textContent = `Jobs${snapshot.jobs && snapshot.jobs.length ? ` (${snapshot.jobs.length})` : ''}`;
    document.querySelector('.tab[data-tab="timeline"]').textContent = `Timeline${snapshot.timeline && snapshot.timeline.length ? ` (${snapshot.timeline.length})` : ''}`;
}

// Reusable across any collector's output — the Vue-DevTools-style "open in
// IDE" affordance: any field with file+line becomes clickable, so future
// collectors (Job, Resource, ...) get this for free once they populate
// file/line, with no extension changes. No editor is picked here — the
// click just asks the Laravel backend to open the file, and it auto-detects
// whichever supported editor is running on that machine (see
// OpenEditorController), same as Vue DevTools.
function renderFileLink(file, line) {
    if (!file) {
        return '—';
    }

    const label = escapeHtml(file) + (line ? ':' + line : '');

    return `<a class="file-link mono" href="#" data-file="${escapeHtml(file)}" data-line="${line ?? ''}" title="Open in editor">${label}</a>`;
}

async function openInEditor(link) {
    const file = link.dataset.file;
    const line = link.dataset.line;

    if (!file || !state.lastOrigin) {
        return;
    }

    link.classList.remove('file-link--error');
    link.title = 'Opening…';

    try {
        const url = new URL('/__devtools/open-editor', state.lastOrigin);
        url.searchParams.set('file', file);
        if (line) {
            url.searchParams.set('line', line);
        }

        const res = await fetch(url);

        if (res.ok) {
            link.title = 'Open in editor';
            return;
        }

        const body = await res.json().catch(() => null);
        link.title = (body && body.message) || `Could not open editor (HTTP ${res.status})`;
        link.classList.add('file-link--error');
    } catch (err) {
        link.title = `Could not reach ${state.lastOrigin} — ${err.message}`;
        link.classList.add('file-link--error');
    }
}

document.addEventListener('click', (event) => {
    const link = event.target.closest('.file-link');
    if (!link) {
        return;
    }

    event.preventDefault();
    openInEditor(link);
});

function renderOverview(el, snapshot) {
    const req = snapshot.request || {};
    const route = req.route || {};
    const controller = snapshot.controller || {};
    const response = snapshot.response || {};

    el.innerHTML = `
        <dl class="kv-table">
            <dt>Method</dt><dd>${escapeHtml(req.method ?? '—')}</dd>
            <dt>URL</dt><dd class="mono">${escapeHtml(req.url ?? '—')}</dd>
            <dt>Route</dt><dd class="mono">${escapeHtml(route.uri ?? '—')}</dd>
            <dt>Route name</dt><dd>${escapeHtml(route.name ?? '—')}</dd>
            <dt>Controller</dt><dd class="mono">${escapeHtml(controller.class ?? '—')}${controller.method ? '@' + escapeHtml(controller.method) : ''}</dd>
            <dt>File</dt><dd>${renderFileLink(controller.file, controller.line)}</dd>
            <dt>Status</dt><dd><span class="status-badge ${statusClass(response.status)}">${response.status ?? '—'}</span></dd>
            <dt>Duration</dt><dd>${response.duration ?? '—'} ms</dd>
        </dl>
    `;
}

function renderQueries(el, queries) {
    if (!queries || queries.length === 0) {
        el.innerHTML = '<div class="reserved-note">No queries recorded for this request.</div>';
        return;
    }

    let html = '<div style="display:flex;flex-direction:column;gap:12px;">';
    for (const q of queries) {
        const badge = q.is_slow ? '<span class="status-badge status-5xx" style="margin-left: 8px;">SLOW</span>' : '';
        html += `<div style="padding: 8px; border: 1px solid var(--border); border-radius: 4px;">
            <div class="mono" style="margin-bottom: 6px; font-weight: 600;">${escapeHtml(q.sql)}</div>
            <dl class="kv-table" style="margin: 0; font-size: 10px;">
                <dt>Time</dt><dd>${q.time}ms ${badge}</dd>
                <dt>Connection</dt><dd>${escapeHtml(q.connection)}</dd>
                <dt>Bindings</dt><dd class="mono">${escapeHtml(JSON.stringify(q.bindings))}</dd>
                <dt>File</dt><dd>${renderFileLink(q.file, q.line)}</dd>
            </dl>
        </div>`;
    }
    html += '</div>';
    el.innerHTML = html;
}

function renderEvents(el, events) {
    if (!events || events.length === 0) {
        el.innerHTML = '<div class="reserved-note">No events recorded for this request.</div>';
        return;
    }

    let html = '<div style="display:flex;flex-direction:column;gap:12px;">';
    for (const e of events) {
        html += `<div style="padding: 8px; border: 1px solid var(--border); border-radius: 4px;">
            <div class="mono" style="margin-bottom: 6px; font-weight: 600;">${escapeHtml(e.name)}</div>
            <dl class="kv-table" style="margin: 0; font-size: 10px;">
                <dt>Payload</dt><dd class="mono">${escapeHtml(JSON.stringify(e.payload))}</dd>
                <dt>File</dt><dd>${renderFileLink(e.file, e.line)}</dd>
            </dl>
        </div>`;
    }
    html += '</div>';
    el.innerHTML = html;
}

function renderJobs(el, jobs) {
    if (!jobs || jobs.length === 0) {
        el.innerHTML = '<div class="reserved-note">No jobs recorded for this request.</div>';
        return;
    }

    let html = '<div style="display:flex;flex-direction:column;gap:12px;">';
    for (const j of jobs) {
        const statusCls = j.status === 'failed' ? 'status-5xx' : 'status-2xx';
        html += `<div style="padding: 8px; border: 1px solid var(--border); border-radius: 4px;">
            <div class="mono" style="margin-bottom: 6px; font-weight: 600;">${escapeHtml(j.class)}</div>
            <dl class="kv-table" style="margin: 0; font-size: 10px;">
                <dt>Status</dt><dd class="status-badge ${statusCls}">${escapeHtml(j.status)}</dd>
                <dt>Time</dt><dd>${j.time}ms</dd>
                <dt>Connection</dt><dd>${escapeHtml(j.connection)}</dd>
                <dt>Queue</dt><dd>${escapeHtml(j.queue)}</dd>
            </dl>
        </div>`;
    }
    html += '</div>';
    el.innerHTML = html;
}

function truncate(str, max) {
    if (!str) return '';
    return str.length > max ? str.substring(0, max - 1) + '…' : str;
}

function renderTimeline(el, snapshot) {
    const timeline = snapshot.timeline || [];
    
    if (timeline.length === 0) {
        el.innerHTML = '<div class="reserved-note">No timeline entries recorded yet.</div>';
        return;
    }

    const typeColors = {
        middleware: '#6366f1',
        controller: '#10b981',
        query: '#f59e0b',
        event: '#8b5cf6',
        job: '#ec4899',
    };

    const typeLabels = {
        middleware: 'MIDW',
        controller: 'CTRL',
        query: 'SQL',
        event: 'EVNT',
        job: 'JOB',
    };

    const totalDuration = snapshot.response?.duration || 100;
    
    let html = `<div class="collector-summary">Total: ${totalDuration}ms &middot; ${timeline.length} ${timeline.length === 1 ? 'entry' : 'entries'}</div>`;
    html += '<div class="timeline-waterfall">';

    timeline.forEach((entry, index) => {
        const offsetPercent = Math.min((entry.offset / totalDuration) * 100, 100);
        const durationPercent = entry.duration
            ? Math.max(Math.min((entry.duration / totalDuration) * 100, 100 - offsetPercent), 0.4)
            : 0.4;
        const color = typeColors[entry.type] || '#78909c';
        const typeLabel = typeLabels[entry.type] || entry.type.toUpperCase().substring(0, 4);
        const timeText = entry.duration != null
            ? entry.duration.toFixed(1) + 'ms'
            : entry.offset.toFixed(1) + 'ms';
        const tooltipText = `${entry.offset.toFixed(1)}ms` + (entry.duration != null ? ` — ${entry.duration.toFixed(1)}ms` : '');

        html += `<div class="timeline-row" data-index="${index}">
            <div class="timeline-label">
                <span class="timeline-type-badge" style="background: ${color}">${typeLabel}</span>
                <span class="timeline-entry-label mono" title="${escapeHtml(entry.label)}">${escapeHtml(truncate(entry.label, 50))}</span>
            </div>
            <div class="timeline-bar-container">
                <div class="timeline-bar" style="left: ${offsetPercent}%; width: ${durationPercent}%; background: ${color};" title="${tooltipText}"></div>
            </div>
            <div class="timeline-time">${timeText}</div>
        </div>`;
    });

    html += '</div>';
    el.innerHTML = html;
}

tabsEl.addEventListener('click', (event) => {
    const btn = event.target.closest('.tab');
    if (!btn) return;

    for (const tab of tabsEl.querySelectorAll('.tab')) {
        tab.classList.toggle('is-active', tab === btn);
    }

    const name = btn.dataset.tab;
    for (const panel of document.querySelectorAll('.tab-panel')) {
        panel.classList.toggle('is-active', panel.dataset.panel === name);
    }
});

document.getElementById('clear-btn').addEventListener('click', () => {
    state.entries = [];
    state.selectedId = null;
    renderList();
    showEmptyDetail(DEFAULT_EMPTY_MESSAGE);
});

/* ─── Modal Logic ─── */
const timelineModal = document.getElementById('timeline-modal');
const timelineModalTitle = document.getElementById('timeline-modal-title');
const timelineModalBody = document.getElementById('timeline-modal-body');

function closeTimelineModal() {
    timelineModal.classList.add('hidden');
}

document.getElementById('timeline-modal-close').addEventListener('click', closeTimelineModal);
document.querySelector('#timeline-modal .modal-backdrop').addEventListener('click', closeTimelineModal);

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !timelineModal.classList.contains('hidden')) {
        closeTimelineModal();
    }
});

document.getElementById('panel-timeline').addEventListener('click', (e) => {
    const row = e.target.closest('.timeline-row');
    if (!row) return;

    const index = row.dataset.index;
    if (index === undefined || !state.lastSnapshot || !state.lastSnapshot.timeline) return;

    const entry = state.lastSnapshot.timeline[index];
    if (!entry) return;

    timelineModalTitle.textContent = entry.type.toUpperCase() + ' Details';

    let bodyHtml = `<dl class="kv-table" style="margin-bottom: 0;">`;
    
    if (entry.label) {
        bodyHtml += `<dt>Label</dt><dd class="mono">${escapeHtml(entry.label)}</dd>`;
    }
    
    let fullItem = null;
    if (entry.type === 'query' && state.lastSnapshot.queries) {
        fullItem = state.lastSnapshot.queries.find(q => q.sql === entry.label);
    } else if (entry.type === 'event' && state.lastSnapshot.events) {
        fullItem = state.lastSnapshot.events.find(ev => ev.name === entry.label);
    } else if (entry.type === 'job' && state.lastSnapshot.jobs) {
        fullItem = state.lastSnapshot.jobs.find(j => j.class === entry.label);
    } else if (entry.type === 'controller' && state.lastSnapshot.controller) {
        fullItem = state.lastSnapshot.controller;
    }

    if (fullItem) {
        if (entry.type === 'query' && fullItem.bindings && fullItem.bindings.length > 0) {
            bodyHtml += `<dt>Bindings</dt><dd class="mono">${escapeHtml(JSON.stringify(fullItem.bindings))}</dd>`;
        }
        if (fullItem.connection) {
            bodyHtml += `<dt>Connection</dt><dd>${escapeHtml(fullItem.connection)}</dd>`;
        }
        if (fullItem.file) {
            bodyHtml += `<dt>File</dt><dd>${renderFileLink(fullItem.file, fullItem.line)}</dd>`;
        }
    }

    bodyHtml += `</dl>`;

    timelineModalBody.innerHTML = bodyHtml;
    timelineModal.classList.remove('hidden');
});


        LaravelInspectorSettings.load().then((settings) => {
            state.settings = settings;
            applyTheme(settings);
            initSettingsUi(settings);
        });

        // Polling logic
        let seenIds = new Set();
        async function pollLatest() {
            try {
                const res = await fetch('/__devtools/latest');
                if (!res.ok) return;
                const latest = await res.json();
                
                // Process in reverse to add chronologically
                for (let i = latest.length - 1; i >= 0; i--) {
                    const item = latest[i];
                    if (!seenIds.has(item.id)) {
                        seenIds.add(item.id);
                        addEntry({
                            method: item.request.method || 'GET',
                            url: item.request.url || '',
                            status: item.response.status || 200,
                            time: item.response.duration || 0,
                            laravelRequestId: item.id,
                        });
                    }
                }
            } catch (e) {
                // Ignore network errors on polling
            }
        }

        pollLatest();
        setInterval(pollLatest, 2000);
</script>
</body>
</html>
