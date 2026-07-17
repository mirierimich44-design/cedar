{{-- Package E: shared export / print toolbar
     @include('report.partials.export_toolbar', ['table' => '#my_table', 'title' => 'Report name'])
--}}
@php
    $tableSel = $table ?? '';
    $exportTitle = $title ?? 'Report';
@endphp
<div class="apex-export-toolbar no-print" style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:0 0 12px;">
    <button type="button" class="btn btn-default btn-sm" onclick="window.print();">
        <i class="fa fa-print"></i> Print
    </button>
    @if(!empty($tableSel))
    <button type="button" class="btn btn-success btn-sm apex-export-excel"
            data-table="{{ $tableSel }}" data-title="{{ $exportTitle }}">
        <i class="fa fa-file-excel-o"></i> Excel
    </button>
    <button type="button" class="btn btn-danger btn-sm apex-export-pdf"
            data-table="{{ $tableSel }}" data-title="{{ $exportTitle }}" data-orient="portrait">
        <i class="fa fa-file-pdf-o"></i> PDF
    </button>
    <button type="button" class="btn btn-warning btn-sm apex-export-pdf"
            data-table="{{ $tableSel }}" data-title="{{ $exportTitle }}" data-orient="landscape">
        <i class="fa fa-file-pdf-o"></i> PDF wide
    </button>
    @endif
    <span class="text-muted" style="font-size:12px;">Print / export this page table</span>
</div>
<script>
(function () {
    if (window.__apexExportBound) return;
    window.__apexExportBound = true;
    function tableToCsv(sel) {
        var $t = window.jQuery ? jQuery(sel) : null;
        if (!$t || !$t.length) { if (window.toastr) toastr.error('Table not found'); return ''; }
        var rows = [];
        $t.find('tr').each(function () {
            var cols = [];
            jQuery(this).find('th,td').each(function () {
                var t = jQuery(this).text().replace(/\s+/g, ' ').trim().replace(/"/g, '""');
                cols.push('"' + t + '"');
            });
            if (cols.length) rows.push(cols.join(','));
        });
        return rows.join('\n');
    }
    jQuery(document).on('click', '.apex-export-excel', function () {
        var sel = jQuery(this).data('table');
        var title = jQuery(this).data('title') || 'report';
        var csv = tableToCsv(sel);
        if (!csv) return;
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = (title + '').replace(/[^\w\-]+/g, '_') + '.csv';
        a.click();
        URL.revokeObjectURL(a.href);
    });
    jQuery(document).on('click', '.apex-export-pdf', function () {
        var sel = jQuery(this).data('table');
        var title = jQuery(this).data('title') || 'Report';
        var orient = jQuery(this).data('orient') || 'portrait';
        var $t = jQuery(sel);
        if (!$t.length) { if (window.toastr) toastr.error('Table not found'); return; }
        var w = window.open('', '_blank');
        if (!w) { if (window.toastr) toastr.error('Allow popups for PDF'); return; }
        var landscapeCss = orient === 'landscape'
            ? '@page{size:A4 landscape;margin:12mm;} table{font-size:10px;}'
            : '@page{size:A4 portrait;margin:12mm;} table{font-size:11px;}';
        w.document.write('<!DOCTYPE html><html><head><title>' + jQuery('<div>').text(title).html() + '</title>');
        w.document.write('<style>body{font-family:system-ui,sans-serif;color:#0f172a;}' + landscapeCss);
        w.document.write('h1{font-size:16px;margin:0 0 8px;} .meta{color:#64748b;font-size:12px;margin-bottom:12px;}');
        w.document.write('table{width:100%;border-collapse:collapse;} th,td{border:1px solid #cbd5e1;padding:5px 6px;text-align:left;} th{background:#f1f5f9;}</style></head><body>');
        w.document.write('<h1>' + jQuery('<div>').text(title).html() + '</h1>');
        w.document.write('<div class="meta">' + new Date().toLocaleString() + '</div>');
        w.document.write($t[0].outerHTML);
        w.document.write('</body></html>');
        w.document.close();
        setTimeout(function () { w.focus(); w.print(); }, 300);
    });
})();
</script>
