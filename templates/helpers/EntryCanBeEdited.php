<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // https://www.kimai.org
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
 * Class Zend_View_Helper_EntryCanBeEdited
 */
class Zend_View_Helper_EntryCanBeEdited extends Zend_View_Helper_Abstract
{
    /**
     * @param array $entry
     *
     * @return bool
     */
    public function entryCanBeEdited(array $entry)
    {
        $kga = Kimai_Registry::getConfig();

        if (!$kga->isEditLimit() || time() - $entry['end'] <= $kga->getEditLimit()) {
            return true;
        }
        return false;
    }
}
