<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team since 2006
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
 * View object for Kimai.
 *
 * @author Kevin Papst
 */
class Kimai_View extends Zend_View
{
    public function init()
    {
        global $kga;

        $this->setBasePath(APPLICATION_PATH . '/templates/');
        $this->addHelperPath(APPLICATION_PATH . '/templates/helpers/', 'Zend_View_Helper');
        $this->addHelperPath(APPLICATION_PATH . '/libraries/Kimai/View/Helper/', 'Kimai_View_Helper');

        parent::init();
        $this->kga = $kga;
    }
}
