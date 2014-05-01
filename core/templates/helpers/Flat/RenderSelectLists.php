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

require_once __DIR__ . '/../RenderSelectLists.php';

/**
 * Renders the fours (users, customer, projects, tasks) select boxes.
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_Flat_RenderSelectLists extends Zend_View_Helper_RenderSelectLists
{

    protected function renderAll($entries)
    {
        foreach($entries as $listEntry)
        {
            echo '<div class="col-lg-3"><div class="panel">';
            $this->renderHeader($listEntry);
            $this->renderContent($listEntry);
            $this->renderFooter($listEntry);
            echo '</div></div>';
        }
    }

} 
