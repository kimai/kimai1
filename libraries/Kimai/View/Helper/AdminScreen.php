<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team since 2006
 *
 * Kimai is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; Version 3, 29 June 2007
 *
 * Kimai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimai; If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * A view helper to render HTML components for the admin section.
 */
class Kimai_View_Helper_AdminScreen extends Zend_View_Helper_Abstract
{

    /**
     * @return $this
     */
    public function adminScreen()
    {
        return $this;
    }

    /**
     * Returns the HTML to render an accordion.
     *
     * @param $id
     * @param $title
     * @param $content
     * @return string
     */
    public function accordion($id, $title, $content)
    {
        return $this->accordionHeader($id, $title) .
            $this->accordionContent($content) .
            $this->accordionFooter($id);
    }

    protected function accordionHeader($id, $title)
    {
        return '
            <div id="adminPanel_extension_sub'.$id.'">
                <div class="adminPanel_extension_panel_header">
                    <a onClick="adminPanel_extension_subtab_expand('.$id.')">
                        <span class="adminPanel_extension_accordeon_triangle"></span>
                        '.$this->accordionTitle($title).'
                    </a>
                </div>
                <div id="adminPanel_extension_s'.$id.'" class="adminPanel_extension_subtab">
        ';
    }

    protected function accordionTitle($title)
    {
        return $title;
    }

    protected function accordionContent($content)
    {
        return $content;
    }

    protected function accordionFooter($id)
    {
        return '
                </div>
            </div>
        ';
    }
}
