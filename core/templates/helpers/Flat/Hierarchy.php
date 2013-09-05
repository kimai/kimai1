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

require_once __DIR__ . '/../Hierarchy.php';

/**
 * Overwritten to support Bootstrap's "flat" style.
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_Flat_Hierarchy extends Zend_View_Helper_Hierarchy
{

    protected function renderLevelEnd()
    {
        return "</div>";
    }

    protected function renderLevelBegin($level, $id = null)
    {
        if ($id === null) {
            return '<div class="tab-pane hierarchyLevel'.$level.'">';
        }

        return '<div id="'.$id.'" class="tab-pane hierarchyLevel'.$level.'">';
    }

}
