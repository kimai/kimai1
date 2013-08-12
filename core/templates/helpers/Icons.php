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
 * Returns the HTML for an icon.
 * Made as ViewHelper for easier customization of skins.
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_Icons extends Zend_View_Helper_Abstract
{
    protected $iconSize = null;
    const ICON_LARGE = 'large';

    public function setIconSize($size)
    {
        $this->iconSize = $size;
    }

    protected function getIconSize()
    {
        return $this->iconSize;
    }

    public function icons($key = null, $options = array())
    {
        if ($key === null) {
            return $this;
        }

        switch ($key) {
            case 'edit':
                return $this->getEdit($options);
            case 'filter':
                return $this->getFilter($options);
            case 'mail':
            case 'email':
                return $this->getEmail($options);
            case 'delete':
                return $this->getDelete($options);
            case 'bannedUser':
            case 'locked':
                return $this->getLocked($options);
            case 'activeAccount':
            case 'unlocked':
                return $this->getUnlocked($options);
            case 'warning':
                return $this->getWarning($options);
            case 'stop':
                return $this->getStop($options);
            case 'start':
            case 'recordAgain':
                return $this->getStart($options);
            default:
                return $this;
        }

    }

    public function getEdit($options = array())
    {
        return $this->renderIcon('edit2.gif', $options, $this->view->kga['lang']['edit']);
    }

    public function getFilter($options = array())
    {
        return $this->renderIcon('filter.png', $options, $this->view->kga['lang']['filter']);
    }

    public function getStop($options = array())
    {
        return $this->renderIcon('button_stopthis.gif', $options, $this->view->kga['lang']['stop']);
    }

    public function getStart($options = array())
    {
        return $this->renderIcon('button_recordthis.gif', $options, $this->view->kga['lang']['recordAgain']);
    }


    public function getDelete($options = array())
    {
        // there is no global "delete" translation
        return $this->renderIcon('button_trashcan.png', $options, '');
    }

    public function getEmail($options = array())
    {
        return $this->renderIcon('button_mail.gif', $options, $this->view->kga['lang']['mailUser']);
    }

    public function getLocked($options = array())
    {
        return $this->renderIcon('lock.png', $options, $this->view->kga['lang']['bannedUser']);
    }

    public function getUnlocked($options = array())
    {
        return $this->renderIcon('jipp.gif', $options, $this->view->kga['lang']['activeAccount']);
    }

    public function getWarning($options = array())
    {
        // there is no global "warning" translation
        return $this->renderIcon('caution_mini.png', $options, '');
    }

    protected function renderIcon($icon, $options, $title, $style = '')
    {
        $title = isset($options['title']) ? $options['title'] : $title;
        if (isset($options['disabled'])) {
            $icon = str_replace('.', '_.', $icon);
        }
        return '<img src="../skins/'. $this->view->escape($this->view->kga['conf']['skin']) .
            '/grfx/'.$icon.'" width="13" height="13" alt="'. $title . '" title="'. $title . '" border="0" />';
    }
}
