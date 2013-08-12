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
    public function icons($key = null, $options = array())
    {
        if ($key === null) {
            return $this;
        }

        $opts = $this->getOptions($options);

        switch ($key) {
            case 'edit':
                return $this->getEdit($opts);
            case 'filter':
                return $this->getFilter($opts);
            case 'delete':
                return $this->getDelete($opts);
            case 'stop':
                return $this->getStop($opts);
            case 'start':
            case 'recordAgain':
                return $this->getStart($opts);
            default:
                return $this;
        }

    }

    protected function getOptions($options)
    {
        $opts = array();
        if (isset($options['title'])) {
            $opts['title'] = $options['title'];
        }
        if (isset($options['alt'])) {
            $opts['alt'] = $options['alt'];
        }
        return $opts;
    }

    public function getEdit($options = array())
    {
        $title = isset($options['title']) ? $options['title'] : $this->view->kga['lang']['edit'];
        return '<img src="../skins/'.
            $this->view->escape($this->view->kga['conf']['skin']).
            '/grfx/edit2.gif" width="13" height="13" alt="'.
            $this->view->kga['lang']['edit'].'" title="'. $title . '" border="0" />';
    }

    public function getFilter($options = array())
    {
        $title = isset($options['title']) ? $options['title'] : $this->view->kga['lang']['filter'];
        return '<img src="../skins/'.
            $this->view->escape($this->view->kga['conf']['skin']).
            '/grfx/filter.png" width="13" height="13" alt="'.
            $this->view->kga['lang']['filter'].'" title="'. $title . '" border="0" />';
    }

    public function getStop($options = array())
    {
        $title = isset($options['title']) ? $options['title'] : $this->view->kga['lang']['stop'];
        return '<img src="../skins/'.
            $this->view->escape($this->view->kga['conf']['skin']).
            '/grfx/button_stopthis.gif" width="13" height="13" alt="'.
            $this->view->kga['lang']['stop'].'" title="'. $title . '" border="0" />';
    }

    public function getStart($options = array())
    {
        $title = isset($options['title']) ? $options['title'] : $this->view->kga['lang']['recordAgain'];
        return '<img src="../skins/'.
            $this->view->escape($this->view->kga['conf']['skin']).
            '/grfx/button_recordthis.gif" width="13" height="13" alt="'.
            $this->view->kga['lang']['recordAgain'].'" title="'. $title . '" border="0" />';
    }


    public function getDelete($options = array())
    {
        // there is no global delete translation
        $title = isset($options['title']) ? $options['title'] : '';
        return '<img src="../skins/'.
            $this->view->escape($this->view->kga['conf']['skin']).
            '/grfx/button_trashcan.png" width="13" height="13" alt="'.
            $this->view->kga['lang']['recordAgain'].'" title="'. $title . '" border="0" />';
    }

}
