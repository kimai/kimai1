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

require_once __DIR__ . '/../Menu.php';
/**
 * Renders the HTML for a menu within the flat skin.
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_Flat_Menu extends Zend_View_Helper_Menu
{
    private $iconMap = array(
        'ext_debug'             => 'icon-medkit',
        'deb_ext'               => 'icon-medkit',
        'ki_demoextension'      => 'icon-wrench',
        'ki_adminpanel'         => 'icon-cogs',
        'adminPanel_extension'  => 'icon-cogs',
        'ki_budget'             => 'icon-signal',
        'ki_expenses'           => 'icon-money', // icon-credit-card
        'ki_export'             => 'icon-download-alt',
        'ki_invoice'            => 'icon-file-text',
        'ki_timesheets'         => 'icon-time',
        'ki_timesheet'          => 'icon-time',
    );

    private $unknownIcon = 'icon-question';

    public function __toString()
    {
        $html = '
                <ul class="menu">';

        foreach($this->getEntries() as $entry)
        {
            $icon = $this->unknownIcon;
            if (isset($this->iconMap[$entry['key']])) {
                $icon = $this->iconMap[$entry['key']];
            }

            $html .= '
                        <li class="tab '.$entry['class'].'" id="'.$entry['id'].'">
                            <a href="javascript:void(0);" onclick="' . $entry['onclick'] . '">
                                <i class="'.$icon.' icon-3x"></i>
                                <span>' . $entry['title'] . '</span>
                            </a>
                        </li>';
        }

        $html .= '</ul>';

        return $html;
    }


} 
