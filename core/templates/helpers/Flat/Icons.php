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
 * For all available icons, see http://fortawesome.github.io/Font-Awesome/icons/
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_Flat_Icons extends Zend_View_Helper_Icons
{

    protected function renderIcon($iconId, $options, $title, $style = '')
    {
        $icon = $this->mapIconId($iconId);
        $title = isset($options['title']) ? $options['title'] : $title;

        $class = $icon;
        switch ($this->getIconSize()) {
            case self::ICON_LARGE:
                $class .= ' icon-large';
                break;
        }
        if (isset($options['disabled'])) {
            $class .= ' disabledIcon';
        }

        if($style !== '') {
            $style = 'style="'.$style.'" ';
        }
        return '<i title="'.$title.'" '.$style.'class="'.$class.'"></i>';
    }


    protected function mapIconId($identifier)
    {
        switch($identifier)
        {
            case 'add':
                return 'icon-plus-sign-alt';
            case 'edit':
                return 'icon-pencil';
            case 'filter':
                return 'icon-filter';
            case 'email':
                return 'icon-envelope';
            case 'quickdelete':
            case 'delete':
                return 'icon-trash';
            case 'locked':
                return 'icon-lock';
            case 'unlocked':
                return 'icon-unlock';
            case 'warning':
                return 'icon-warning-sign';
            case 'stop':
                return 'icon-stop';
            case 'start':
                return 'icon-play-circle';
            case 'reload':
                return 'icon-refresh';
        }

        throw new Exception('Could not find Icon ID');
    }

}
