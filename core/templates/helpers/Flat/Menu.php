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
        'ext_debug'             => 'medkit',
        'deb_ext'               => 'medkit',
        'ki_demoextension'      => 'wrench',
        'ki_adminpanel'         => 'cogs',
        'adminPanel_extension'  => 'cogs',
        'ki_budget'             => 'bar-chart-o',
        'ki_expenses'           => 'money', // icon-credit-card
        'ki_export'             => 'download',
        'ki_invoice'            => 'file-text',
        'ki_timesheets'         => 'clock-o',
        'ki_timesheet'          => 'clock-o',
    );

    private $unknownIcon = 'icon-question';

    public function __toString()
    {
        $html = '<ul class="nav navbar-nav" id="fliptabs">';

        foreach($this->getEntries() as $entry)
        {
            $icon = $this->unknownIcon;
            if (isset($this->iconMap[$entry['key']])) {
                $icon = $this->iconMap[$entry['key']];
            }

            $html .= '
                        <li id="'.$entry['id'].'" data-id="'.$entry['key'].'">
                            <a href="javascript:void(0);" onclick="' . $entry['onclick'] . '">
                                <i class="fa fa-'.$icon.'"></i>
                                <span>' . $entry['title'] . '</span>
                            </a>
                        </li>';
        }

        $html .= '</ul>';

        return $html;
    }


} 
