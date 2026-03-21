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
        return '<div class="tw-flex-1 tw-py-3 tw-px-3 tw-space-y-0.5 tw-overflow-y-auto" id="side-bar">' . PHP_EOL;
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
        return '<a href="' . $item->getUrl() . '" title="" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2 tw-text-sm tw-font-medium tw-tracking-tight tw-transition-all tw-duration-150 tw-rounded-lg tw-whitespace-nowrap hover:tw-bg-white/5 hover:tw-text-white ' . $this->getActiveState($item) . '" ' . $item->getAttributes() . '>' .
        $this->formatIcon($item->icon) . ' <span class="tw-truncate">' . $item->title . '</span>' .
            '</a>' . PHP_EOL;
    }

    /**
     * {@inheritdoc}.
     */
    public function getActiveState($item, $state = 'active-sidebar-item tw-bg-white/10 tw-text-white')
    {
        return $item->isActive() ? $state : 'tw-text-slate-400';
    }

    /**
     * Get active state on child items.
     *
     * @param $item
     * @param  string  $state
     * @return null|string
     */
    public function getActiveStateOnChild($item, $state = 'active-sidebar-parent tw-bg-white/5 tw-text-white')
    {
        return $item->hasActiveOnChild() ? $state : null;
    }

    /**
     * {@inheritdoc}.
     */
    public function getDividerWrapper()
    {
        return '<div class="tw-my-1 tw-border-t tw-border-white/5"></div>';
    }

    /**
     * {@inheritdoc}.
     */
    public function getHeaderWrapper($item)
    {
        return '<div class="tw-px-4 tw-pt-5 tw-pb-1 tw-text-[10px] tw-font-bold tw-uppercase tw-tracking-widest tw-text-slate-500">' . $item->title . '</div>';
    }

    /**
     * {@inheritdoc}.
     */
    public function getMenuWithDropDownWrapper($item)
    {
        $dropdownToggle = '<a href="#" title="" class="drop_down tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2 tw-text-sm tw-font-medium tw-tracking-tight tw-transition-all tw-duration-150 tw-rounded-lg tw-whitespace-nowrap hover:tw-bg-white/5 hover:tw-text-white ' . $this->getActiveStateOnChild($item) . '" ' . $item->getAttributes() . '>' .
        $this->formatIcon($item->icon) . ' <span class="tw-truncate">' . $item->title . '</span>' .
        '<svg aria-hidden="true" class="svg tw-ml-auto tw-text-slate-500 tw-size-3.5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">' . $this->getArray($item) .
            '</svg>' .
            '</a>';

        $childItems = $this->getChildMenuItems($item);

        return '<div class="tw-mb-0.5">' . $dropdownToggle . $childItems . '</div>' . PHP_EOL;
    }

    /**
     * Get multi-level dropdown wrapper.
     *
     * Note: This example doesn't directly implement a multi-level dropdown, as it wasn't specified, but you could extend
     * the functionality similarly to `getMenuWithDropDownWrapper`, adjusting for deeper nesting.
     *
     * @param  \Nwidart\Menus\MenuItem  $item
     * @return string
     */
    public function getMultiLevelDropdownWrapper($item)
    {
        // Placeholder for multi-level dropdown functionality if needed
        return '';
    }

    /**
     * Get child menu items.
     *
     * @param  \Nwidart\Menus\MenuItem  $item
     * @return string
     */
    public function getChildMenuItems($item)
    {

        $children = '';
        $displayStyle = $item->hasActiveOnChild() ? 'block' : 'none';

        


        if (count($item->getChilds()) > 0) {

            $children .= '<div class="chiled tw-relative tw-mt-0.5 tw-mb-1 tw-pl-9" style="display:' . $displayStyle . '">
            <div class="tw-absolute tw-inset-y-0 tw-w-px tw-bg-white/10 tw-left-[18px]"></div>
            <div class="tw-space-y-0.5">';

            foreach ($item->getChilds() as $child) {

                $isActive = $child->isActive() ? 'tw-text-sky-400 tw-font-semibold' : 'tw-text-slate-500 hover:tw-text-slate-200 hover:tw-bg-white/5';

                $children .= '<a href="' . $child->getUrl() . '" title="" class="tw-flex tw-items-center tw-gap-2 tw-px-3 tw-py-1.5 tw-text-sm tw-font-medium tw-tracking-tight tw-truncate tw-transition-all tw-duration-150 tw-rounded-lg tw-whitespace-nowrap ' . $isActive . '" ' . $child->getAttributes() . '>' .
                $child->getIcon() . ' <span>' . $child->title . '</span>' .
                    '</a>' . PHP_EOL;
            }

            $children .= '</div></div>';
        }

        return $children;
    }

    /**
     * Returns the icon HTML. If the icon is SVG, it returns directly; otherwise, it assumes it's a FontAwesome class and wraps it in an <i> tag.
     *
     * @param string $icon
     * @return string
     */
    protected function formatIcon($icon)
    {
        // Check if the icon string contains "<svg", indicating it's an SVG icon
        if (strpos($icon, '<svg') !== false) {
            return $icon; // Return the SVG icon directly
        } else {
            // Assume it's a FontAwesome icon and return it wrapped in an <i> tag
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


