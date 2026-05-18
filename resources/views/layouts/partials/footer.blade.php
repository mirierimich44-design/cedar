

<div class="tw-mt-auto">
  <div class="tw-mb-4 tw-ms-8 -tw-mt-1 no-print">
    <p class="tw-text-xs tw-font-normal tw-text-gray-500">
      {{ config('app.name', 'ultimatePOS') }} - <span class="tw-font-mono tw-font-medium"> V{{config('author.app_version')}}</span> | Copyright &copy; {{ date('Y') }} All rights reserved.
    </p>
  </div>
</div>

<script>
// ===== Dark Mode =====
function toggleDarkMode() {
    var isDark = document.body.classList.toggle('dark-mode');
    localStorage.setItem('apex_dark_mode', isDark ? '1' : '0');
}
// Apply on load (CSS already does early apply, this ensures body class is set after JS loads)
if (localStorage.getItem('apex_dark_mode') === '1') {
    document.body.classList.add('dark-mode');
}

// ===== Global Search =====
var searchTimer = null;
var searchSelected = -1;

function openGlobalSearch() {
    document.getElementById('global-search-overlay').style.display = 'block';
    setTimeout(function(){ document.getElementById('global-search-input').focus(); }, 50);
    document.body.style.overflow = 'hidden';
}

function closeGlobalSearch() {
    document.getElementById('global-search-overlay').style.display = 'none';
    document.getElementById('global-search-input').value = '';
    document.getElementById('global-search-results').innerHTML = '<div class="search-empty-state" style="text-align:center;padding:40px 20px;color:#94a3b8;"><p style="font-size:14px;margin:0;">Type to search across your system</p></div>';
    document.body.style.overflow = '';
    searchSelected = -1;
}

document.getElementById('global-search-trigger').addEventListener('click', openGlobalSearch);

document.addEventListener('keydown', function(e) {
    var overlay = document.getElementById('global-search-overlay');
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        if (overlay.style.display === 'none') openGlobalSearch();
        else closeGlobalSearch();
        return;
    }
    if (e.key === 'Escape' && overlay.style.display !== 'none') {
        closeGlobalSearch();
        return;
    }
    if (overlay.style.display !== 'none') {
        var results = document.querySelectorAll('#global-search-results .search-result-item');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            searchSelected = Math.min(searchSelected + 1, results.length - 1);
            results.forEach(function(r,i){ r.classList.toggle('search-result-active', i === searchSelected); });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            searchSelected = Math.max(searchSelected - 1, 0);
            results.forEach(function(r,i){ r.classList.toggle('search-result-active', i === searchSelected); });
        } else if (e.key === 'Enter' && searchSelected >= 0 && results[searchSelected]) {
            var link = results[searchSelected].querySelector('a');
            if (link) { closeGlobalSearch(); window.location.href = link.href; }
        }
    }
});

document.getElementById('global-search-input').addEventListener('input', function() {
    var q = this.value.trim();
    clearTimeout(searchTimer);
    searchSelected = -1;
    if (q.length < 2) {
        document.getElementById('global-search-results').innerHTML = '<div style="text-align:center;padding:40px 20px;color:#94a3b8;"><p style="font-size:14px;margin:0;">Type to search across your system</p></div>';
        return;
    }
    document.getElementById('global-search-results').innerHTML = '<div style="text-align:center;padding:20px;color:#94a3b8;"><svg style="width:20px;height:20px;animation:spin 1s linear infinite;display:inline-block;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a9 9 0 1 0 9 9"/></svg> Searching...</div>';
    searchTimer = setTimeout(function() { doSearch(q); }, 300);
});

function doSearch(q) {
    $.get('{{ route("global.search") }}', { q: q }, function(data) {
        var html = '';
        var typeIcons = {
            'transaction': '<svg style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/></svg>',
            'contact': '<svg style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/></svg>',
            'product': '<svg style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5"/></svg>',
            'parcel': '<svg style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 16.5l-5 -3l5 -3l5 3v5.5l-5 3z"/><path d="M2 13.5v5.5l5 3"/><path d="M7 16.520l5 -3.02"/></svg>',
        };
        var typeBg = { 'transaction': '#dbeafe', 'contact': '#dcfce7', 'product': '#fef3c7', 'parcel': '#ede9fe' };
        var typeColor = { 'transaction': '#2563eb', 'contact': '#16a34a', 'product': '#d97706', 'parcel': '#7c3aed' };
        if (data.results && data.results.length > 0) {
            var lastGroup = '';
            data.results.forEach(function(r, i) {
                if (r.type !== lastGroup) {
                    var label = { transaction:'Transactions', contact:'Contacts', product:'Products', parcel:'Parcels' }[r.type] || r.type;
                    html += '<div style="padding:6px 12px 2px;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">' + label + '</div>';
                    lastGroup = r.type;
                }
                var ic = typeIcons[r.type] || '';
                var bg = typeBg[r.type] || '#f1f5f9';
                var clr = typeColor[r.type] || '#475569';
                html += '<div class="search-result-item" style="border-radius:8px;margin:2px 0;">' +
                    '<a href="' + r.url + '" onclick="closeGlobalSearch()" style="display:flex;align-items:center;gap:12px;padding:10px 12px;text-decoration:none;color:#1e293b;border-radius:8px;" ' +
                    'onmouseover="this.parentElement.style.background=\'#f8fafc\'" onmouseout="this.parentElement.style.background=\'\'">' +
                    '<span style="width:32px;height:32px;background:' + bg + ';color:' + clr + ';border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">' + ic + '</span>' +
                    '<div style="min-width:0;"><div style="font-size:14px;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + r.title + '</div>' +
                    '<div style="font-size:12px;color:#94a3b8;">' + (r.subtitle || '') + '</div></div>' +
                    (r.meta ? '<span style="margin-left:auto;font-size:12px;color:#64748b;white-space:nowrap;">' + r.meta + '</span>' : '') +
                    '</a></div>';
            });
        } else {
            html = '<div style="text-align:center;padding:40px 20px;color:#94a3b8;"><p style="font-size:14px;margin:0;">No results for "' + q + '"</p></div>';
        }
        document.getElementById('global-search-results').innerHTML = html;
    }).fail(function() {
        document.getElementById('global-search-results').innerHTML = '<div style="text-align:center;padding:20px;color:#ef4444;">Search failed. Please try again.</div>';
    });
}

// CSS for search results
var searchStyle = document.createElement('style');
searchStyle.textContent = '.search-result-active { background: #f8fafc !important; } @keyframes spin { to { transform: rotate(360deg); } }';
document.head.appendChild(searchStyle);
</script>