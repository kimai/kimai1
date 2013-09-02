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

require_once __DIR__ . '/../Floater.php';

/**
 * Returns the HTML for an icon.
 * Made as ViewHelper for easier customization of skins.
 *
 * For all available icons, see http://fortawesome.github.io/Font-Awesome/icons/
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_Flat_Floater extends Zend_View_Helper_Floater
{

    /**
     * Returns the main structure of the floater.
     *
     * @return string
     */
    public function floaterBody()
    {
        return '
            <div class="modal fade" id="kimai_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Modal title</h4>
                        </div>
                        <div class="modal-body">
                            ...
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $html = '';

        return $html;
    }

}
