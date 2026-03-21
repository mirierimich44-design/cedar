<?php

namespace App\Http;

use Nwidart\Menus\Presenters\Presenter;

class AdminlteCustomPresenter extends Presenter
{
    /**
     * {@inheritdoc}.
     */
    public function getOpenTagWrapper()
    {
        return '<div class="tw-flex-1 tw-py-3 tw-px-3 tw-space-y-1 tw-overflow-y-auto" id="side-bar">' . PHP_EOL;
    }

    /**
     * {@inheritdoc}.
     */
    public function getCloseTagWrapper()
    {
        return '</div>' . PHP_EOL;
    }

    /**
     * {@inheritdoc}.
     */
    public function getMenuWithoutDropdownWrapper($item)
    {
        $isActive = $item->isActive();
        $style = $isActive
            ? 'color:white;background:rgba(255,255,255,0.1);border-left:3px solid #38bdf8;padding-left:13px;'
            : 'color:#94a3b8;';

        return '<a href="' . $item->getUrl() . '" title="" class="sidebar-nav-link tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2 tw-text-sm tw-font-medium tw-transition-all tw-rounded-lg tw-whitespace-nowrap" style="' . $style . '" ' . $item->getAttributes() . '>' .
            $this->formatIcon($item->icon) . ' <span class="tw-truncate">' . $item->title . '</span>' .
            '</a>' . PHP_EOL;
    }

    /**
     * {@inheritdoc}.
     */
    public function getActiveState($item, $state = '')
    {
        return '';
    }

    /**
     * Get active state on child items.
     */
    public function getActiveStateOnChild($item, $state = '')
    {
        return '';
    }

    /**
     * {@inheritdoc}.
     */
    public function getDividerWrapper()
    {
        return '<div style="border-top:1px solid rgba(255,255,255,0.06);margin:6px 0;"></div>';
    }

    /**
     * {@inheritdoc}.
     */
    public function getHeaderWrapper($item)
    {
        return '<div style="color:#64748b;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;padding:18px 16px 4px;">' . $item->title . '</div>';
    }

    /**
     * {@inheritdoc}.
     */
    public function getMenuWithDropDownWrapper($item)
    {
        $hasActive = $item->hasActiveOnChild();
        $parentStyle = $hasActive
            ? 'color:white;background:rgba(255,255,255,0.05);'
            : 'color:#94a3b8;';

        $chevron = $hasActive
            ? '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6" />'
            : '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" />';

        $dropdownToggle = '<a href="#" title="" class="drop_down sidebar-nav-link tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2 tw-text-sm tw-font-medium tw-transition-all tw-rounded-lg tw-whitespace-nowrap" style="' . $parentStyle . '" ' . $item->getAttributes() . '>' .
            $this->formatIcon($item->icon) .
            ' <span class="tw-truncate">' . $item->title . '</span>' .
            '<svg aria-hidden="true" class="svg" style="width:14px;height:14px;flex-shrink:0;margin-left:auto;color:#64748b;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">' . $chevron . '</svg>' .
            '</a>';

        $childItems = $this->getChildMenuItems($item);

        return '<div class="tw-mb-1">' . $dropdownToggle . $childItems . '</div>' . PHP_EOL;
    }

    /**
     * {@inheritdoc}.
     */
    public function getMultiLevelDropdownWrapper($item)
    {
        return '';
    }

    /**
     * Get child menu items.
     */
    public function getChildMenuItems($item)
    {
        $children = '';
        $displayStyle = $item->hasActiveOnChild() ? 'block' : 'none';

        if (count($item->getChilds()) > 0) {
            $children .= '<div class="chiled" style="display:' . $displayStyle . ';position:relative;margin-top:2px;margin-bottom:4px;padding-left:32px;">
            <div style="position:absolute;top:0;bottom:0;left:18px;width:1px;background:rgba(255,255,255,0.1);"></div>
            <div>';

            foreach ($item->getChilds() as $child) {
                $childStyle = $child->isActive()
                    ? 'color:#38bdf8;font-weight:600;'
                    : 'color:#64748b;';

                $children .= '<a href="' . $child->getUrl() . '" title="" class="sidebar-child-link tw-flex tw-items-center tw-gap-2 tw-px-3 tw-py-2 tw-text-sm tw-font-medium tw-truncate tw-transition-all tw-rounded-lg tw-whitespace-nowrap" style="' . $childStyle . '" ' . $child->getAttributes() . '>' .
                    $child->getIcon() . ' <span>' . $child->title . '</span>' .
                    '</a>' . PHP_EOL;
            }

            $children .= '</div></div>';
        }

        return $children;
    }

    /**
     * Returns the icon HTML.
     */
    protected function formatIcon($icon)
    {
        if (strpos($icon, '<svg') !== false) {
            return $icon;
        } else {
            return '<i class="' . $icon . '"></i>';
        }
    }

    public function getArray($item)
    {
        if ($item->hasActiveOnChild()) {
            return '<path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6 9l6 6l6 -6" />';
        } else {
            return '<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M15 6l-6 6l6 6" />';
        }
    }
}
