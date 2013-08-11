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

require_once __DIR__ . '/../Icons.php';

/**
 * Returns the HTML for an icon.
 * Made as ViewHelper for easier customization of skins.
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_Flat_Icons extends Zend_View_Helper_Icons
{

    public function getEdit($options = array())
    {
        $title = isset($options['title']) ? $options['title'] : $this->view->kga['lang']['edit'];
        return '<i title="'.$title.'" class="icon-pencil"></i>';
    }

    public function getFilter($options = array())
    {
        $title = isset($options['title']) ? $options['title'] : $this->view->kga['lang']['filter'];
        return '<i title="'.$title.'" class="icon-filter"></i>';
    }

    public function getStop($options = array())
    {
        $title = isset($options['title']) ? $options['title'] : $this->view->kga['lang']['stop'];
        return '<i title="'.$title.'" style="color:red" class="icon-stop"></i>';
    }

    public function getStart($options = array())
    {
        $title = isset($options['title']) ? $options['title'] : $this->view->kga['lang']['stop'];
        return '<i title="'.$title.'" style="color:green" class="icon-play-circle"></i>';
    }
}
