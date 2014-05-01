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
            case 'add':
                return $this->getAdd($options);
            case 'edit':
                return $this->getEdit($options);
            case 'filter':
                return $this->getFilter($options);
            case 'mail':
            case 'email':
                return $this->getEmail($options);
            case 'quickdelete':
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
            case 'refresh':
            case 'reload':
                return $this->getReload($options);
            case 'start':
            case 'recordAgain':
                return $this->getStart($options);
            default:
                return $this;
        }

    }

    protected function renderIcon($iconId, $options, $title, $style = '')
    {
        $icon = $this->mapIconId($iconId);

        $default = array(
            'title'  => $title,
            'width'  => '13',
            'height' => '13'
        );
        $options = array_merge($default, $options);

        if (isset($options['disabled'])) {
            $icon = str_replace('.', '_.', $icon);
        }

        // most icons will be in side the skin directory
        if (strpos($icon, '../') === false) {
            $icon = '../skins/'. $this->view->escape($this->view->kga['conf']['skin']) . '/grfx/' . $icon;
        }

        return '<img src="'.$icon.'" width="'.$options['width'].'" height="'.$options['height'].'" alt="'. $options['title'] . '" title="'. $options['title'] . '" border="0" />';
    }

    protected function mapIconId($identifier)
    {
        switch($identifier)
        {
            case 'add':
                return 'add.png';
            case 'edit':
                return 'edit2.gif';
            case 'filter':
                return 'filter.png';
            case 'email':
                return 'button_mail.gif';
            case 'quickdelete':
            case 'delete':
                return 'button_trashcan.png';
            case 'locked':
                return 'lock.png';
            case 'unlocked':
                return 'jipp.gif';
            case 'warning':
                return 'caution_mini.png';
            case 'stop':
                return 'button_stopthis.gif';
            case 'start':
                return 'button_recordthis.gif';
            case 'reload':
                return '../extensions/ext_debug/grfx/action_refresh.png';
        }

        throw new Exception('Could not find Icon ID');
    }

    public function getAdd($options = array())
    {
        $options['width'] = '22';
        $options['height'] = '16';

        return $this->renderIcon('add', $options, $this->view->kga['lang']['new_activity']);
    }

    public function getEdit($options = array())
    {
        return $this->renderIcon('edit', $options, $this->view->kga['lang']['edit']);
    }

    public function getFilter($options = array())
    {
        return $this->renderIcon('filter', $options, $this->view->kga['lang']['filter']);
    }

    public function getStop($options = array())
    {
        return $this->renderIcon('stop', $options, $this->view->kga['lang']['stop']);
    }

    public function getStart($options = array())
    {
        return $this->renderIcon('start', $options, $this->view->kga['lang']['recordAgain']);
    }

    public function getDelete($options = array())
    {
        // there is no global "delete" translation, so we use the quickdelete
        return $this->renderIcon('delete', $options, $this->view->kga['lang']['quickdelete']);
    }

    public function getEmail($options = array())
    {
        return $this->renderIcon('email', $options, $this->view->kga['lang']['mailUser']);
    }

    public function getLocked($options = array())
    {
        return $this->renderIcon('locked', $options, $this->view->kga['lang']['bannedUser']);
    }

    public function getUnlocked($options = array())
    {
        return $this->renderIcon('unlocked', $options, $this->view->kga['lang']['activeAccount']);
    }

    // FIXME flat
    public function getSublistFilterAll($options = array())
    {
        return $this->renderIcon('filter', $options, '');
    }

    // FIXME flat
    public function getSublistFilterNone($options = array())
    {
        return $this->renderIcon('filter', $options, '');
    }

    // FIXME flat
    public function getSublistFilterInvert($options = array())
    {
        return $this->renderIcon('filter', $options, '');
    }

    public function getReload($options = array())
    {
        return $this->renderIcon('reload', $options, '');
    }

    public function getWarning($options = array())
    {
        // there is no global "warning" translation
        return $this->renderIcon('warning', $options, '');
    }
}
