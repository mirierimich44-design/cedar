/**
 * admin-actions.js
 * Applies card-style colored icon badges to all admin action dropdowns globally.
 */
(function ($) {
    'use strict';

    // Map of icon class keywords → {background, color}
    var iconColors = [
        { pattern: /fa-eye|fa-search|fa-binoculars/,        bg: '#dbeafe', color: '#1d4ed8' },   // blue  – view
        { pattern: /fa-edit|fa-pencil|fa-pen|glyphicon-edit/, bg: '#ede9fe', color: '#7c3aed' }, // purple – edit
        { pattern: /fa-trash|fa-times|fa-remove|glyphicon-trash/, bg: '#fee2e2', color: '#dc2626' }, // red – delete
        { pattern: /fa-money|fa-dollar|fa-hand-holding|fa-coins|pay_/,  bg: '#dcfce7', color: '#16a34a' }, // green – payment
        { pattern: /fa-power|fa-toggle/,                    bg: '#ffedd5', color: '#ea580c' },   // orange – activate/deactivate
        { pattern: /fa-history|fa-clock|fa-calendar/,       bg: '#cffafe', color: '#0891b2' },   // teal   – history
        { pattern: /fa-print/,                              bg: '#f1f5f9', color: '#475569' },   // slate  – print
        { pattern: /fa-download|fa-file-pdf|fa-file-alt|fa-file/,  bg: '#e0e7ff', color: '#4338ca' }, // indigo – download
        { pattern: /fa-plus|fa-user-plus|fa-cart-plus/,     bg: '#d1fae5', color: '#059669' },  // emerald – add
        { pattern: /fa-sync|fa-refresh|fa-repeat/,          bg: '#e0f2fe', color: '#0284c7' },  // sky    – sync
        { pattern: /fa-star|fa-award|fa-trophy/,            bg: '#fef9c3', color: '#ca8a04' },  // yellow – reward
        { pattern: /fa-barcode|fa-tag|fa-qrcode/,           bg: '#fae8ff', color: '#9333ea' },  // fuchsia – labels
        { pattern: /fa-map|fa-location|fa-compass/,         bg: '#ecfdf5', color: '#10b981' },  // green  – map/location
        { pattern: /fa-exchange|fa-arrows|fa-transfer/,     bg: '#fff7ed', color: '#f59e0b' },  // amber  – transfer
        { pattern: /fa-envelope|fa-mail|fa-paper-plane/,    bg: '#eff6ff', color: '#3b82f6' },  // blue   – email
        { pattern: /fa-link|fa-share|fa-external/,          bg: '#f0fdf4', color: '#22c55e' },  // green  – link/share
    ];

    function getIconStyle(iconClass) {
        for (var i = 0; i < iconColors.length; i++) {
            if (iconColors[i].pattern.test(iconClass)) {
                return iconColors[i];
            }
        }
        // default
        return { bg: '#f1f5f9', color: '#64748b' };
    }

    function styleDropdownIcons($menu) {
        $menu.find('> li > a').each(function () {
            var $a = $(this);
            var $icon = $a.find('i, .glyphicon').first();
            if (!$icon.length || $icon.data('action-styled')) return;

            var cls = $icon.attr('class') || '';
            var style = getIconStyle(cls);

            $icon.css({
                background: style.bg,
                color: style.color,
            });
            $icon.data('action-styled', true);
        });
    }

    $(document).on('show.bs.dropdown', '.btn-group', function () {
        var $menu = $(this).find('.dropdown-menu[role="menu"]');
        styleDropdownIcons($menu);
    });

    // Also style any dropdowns that are already open or rendered in datatables
    $(document).on('draw.dt', function () {
        $('.dropdown-menu[role="menu"]').each(function () {
            styleDropdownIcons($(this));
        });
    });

}(jQuery));
