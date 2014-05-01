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
 * Renders the fours (users, customer, projects, tasks) select boxes.
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_RenderSelectLists extends Zend_View_Helper_Abstract
{

    public function renderSelectLists($entries = array())
    {
        $this->renderAll($entries);
    }

    protected function renderHeader($listEntry)
    {
        ?>
        <div id="<?php echo $listEntry['id']; ?>_head" class="panel-heading">
            <input class="livefilterfield" onkeyup="lists_live_filter('<?php echo $listEntry['id']; ?>', this.value);" type="text" id="<?php echo $listEntry['filter']; ?>" name="<?php echo $listEntry['filter']; ?>"/>
            <?php echo $listEntry['title']; ?>
        </div>
        <?php
    }

    protected function renderFooter($listEntry)
    {
        ?>
        <div id="<?php echo $listEntry['id']; ?>_foot" class="panel-footer">
            <?php if ($listEntry['showAddButon']): ?>
                <a href="#" class="addLink" onClick="floaterShow('<?php echo $listEntry['floaterFile']; ?>','<?php echo $listEntry['floatAction']; ?>',0,0,<?php echo $listEntry['floaterWidth']; ?>); $(this).blur(); return false;"><i class="fa fa-plus-square-o"></i></a>
            <?php endif; ?>
            <a href="#" class="selectAllLink" onClick="lists_filter_select_all('<?php echo $listEntry['id']; ?>'); $(this).blur(); return false;"><i class="fa fa-check"></i></a>
            <a href="#" class="deselectAllLink" onClick="lists_filter_deselect_all('<?php echo $listEntry['id']; ?>'); $(this).blur(); return false;"><i class="fa fa-check-square"></i></a>
            <a href="#" class="selectInvertLink" onClick="lists_filter_select_invert('<?php echo $listEntry['id']; ?>'); $(this).blur(); return false;"><i class="fa fa-check-square-o"></i></a>
            <div style="clear:both"></div>
        </div>
        <?php
    }

    protected function renderContent($listEntry)
    {
        ?>
        <div class="panel-content-full" id="<?php echo $listEntry['id']; ?>"><?php echo $listEntry['content']; ?></div>
        <?php
    }

    protected function renderAll($entries)
    {
        // render the box header
        foreach($entries as $listEntry)
        {
            $this->renderHeader($listEntry);
        }

        // render the main body
        foreach($entries as $listEntry)
        {
            $this->renderContent($listEntry);
        }

        // render the box footer
        foreach($entries as $listEntry)
        {
            $this->renderFooter($listEntry);
        }
    }

} 
