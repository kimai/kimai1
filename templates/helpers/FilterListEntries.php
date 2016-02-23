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
 * Filters the given list entries.
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_FilterListEntries extends Zend_View_Helper_Abstract
{
    /**
     * @param array $entries
     * @param bool $filterHidden
     * @return array
     */
    public function filterListEntries($entries, $filterHidden = true)
    {
        if (!is_array($entries) || count($entries) == 0) {
            return array();
        }

        $listEntries = array();
        foreach($entries as $row) {
            if ($filterHidden && isset($row['visible']) && !$row['visible']) {
                continue;
            }
            $listEntries[] = $row;
        }

        return $listEntries;
    }
} 
