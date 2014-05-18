<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2012 Kimai-Development-Team
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
 * Renders the HTML for a menu.
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_Menu extends Zend_View_Helper_Abstract
{
    private $entries = array();

    public function menu($entries = array())
    {
        if (is_array($entries) && !empty($entries)) {
            $this->entries = $entries;
        }
        return $this;
    }

    public function getEntries()
    {
        return $this->entries;
    }

    public function addEntry($entry)
    {
        if (is_array($entry) && !empty($entry)) {
            $this->entries[] = $entry;
        }
        return $this;
    }

    public function __toString()
    {
        $html = '
                <ul class="menu" id="fliptabs">';

        foreach($this->getEntries() as $entry)
        {
            $html .= '
                        <li class="tab '.$entry['class'].'" id="'.$entry['id'].'" data-id="'.$entry['key'].'">
                            <a href="javascript:void(0);" onclick="' . $entry['onclick'] . '">
                                <span class="aa">&nbsp;</span>
                                <span class="bb">
                                ' . $entry['title'] . '
                                </span>
                                <span class="cc">&nbsp;</span>
                            </a>
                        </li>';
        }

        $html .= '</ul>';

        return $html;
    }


} 
