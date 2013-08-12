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

    protected function renderIcon($icon, $options, $title, $style = '')
    {
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

    public function getEdit($options = array())
    {
        return $this->renderIcon('icon-pencil', $options, $this->view->kga['lang']['edit']);
    }

    public function getFilter($options = array())
    {
        return $this->renderIcon('icon-filter', $options, $this->view->kga['lang']['filter']);
    }

    public function getStop($options = array())
    {
        return $this->renderIcon('icon-stop', $options, $this->view->kga['lang']['stop'], 'color:red');
    }

    public function getStart($options = array())
    {
        return $this->renderIcon('icon-play-circle', $options, $this->view->kga['lang']['stop'], 'color:green');
    }

    public function getDelete($options = array())
    {
        // there is no global "delete" translation
        return $this->renderIcon('icon-trash', $options, '');
    }

    public function getEmail($options = array())
    {
        return $this->renderIcon('icon-envelope', $options, $this->view->kga['lang']['mailUser']);
    }

    public function getLocked($options = array())
    {
        return $this->renderIcon('icon-lock', $options, $this->view->kga['lang']['bannedUser']);
    }

    public function getWarning($options = array())
    {
        // there is no global "warning" translation
        return $this->renderIcon('icon-warning-sign', $options, '');
    }

    public function getUnlocked($options = array())
    {
        return $this->renderIcon('icon-unlock', $options, $this->view->kga['lang']['activeAccount']);
    }
}
