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
 * Display a Timezone select box.
 * Fully compatible with Zend_View_Helper_FormSelect, if you leave the $options empty
 * the default timezone list is used.
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_TimeZoneSelect extends Zend_View_Helper_FormSelect
{
    public function timeZoneSelect($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n")
    {
        if ($options === null) {
            $options = array();
            $timezones = timezoneList();
            foreach($timezones as $zoneName) {
                $options[$zoneName] = $zoneName;
            }
        }
        return $this->formSelect($name, $value, $attribs, $options, $listsep);
    }
} 
