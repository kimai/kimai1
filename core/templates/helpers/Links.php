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
 * Returns the HTML for several important links within Kimai.
 * Made as ViewHelper for easier customization of skins.
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_Links extends Zend_View_Helper_Abstract
{
    public function links($key = null, $options = array())
    {
        if ($key === null) {
            return $this;
        }

        $opts = $this->getOptions($options);

        switch ($key) {
            case 'preferences':
                return $this->getPreferences($opts);
            case 'credits':
            case 'about':
                return $this->getAbout($opts);
            case 'logout':
                return $this->getLogout($opts);
            default:
                return $this;
        }
    }

    protected function getOptions($options)
    {
        $opts = array();
        if (isset($options['content'])) {
            $opts['textlink'] = $options['content'];
        }
        return $opts;
    }

    public function getLogout($options = array())
    {
        $content = '<img src="../skins/' . $this->view->escape($this->view->kga['conf']['skin']).'/grfx/g3_menu_logout.png" width="36" height="27" alt="Logout" />';
        if (isset($options['textlink'])) {
            $content = $options['textlink'];
        }
        return '<a id="main_logout_button" href="../index.php?a=logout">'.$content.'</a>';
    }

    public function getAbout($options = array())
    {
        $content = $this->view->kga['lang']['about'].' Kimai';
        if (isset($options['textlink'])) {
            $content = $options['textlink'];
        }

        return '<a href="#" id="main_credits_button">'.$content.'</a>';
    }

    public function getPreferences($options = array())
    {
        $content = $this->view->kga['lang']['preferences'];
        if (isset($options['textlink'])) {
            $content = $options['textlink'];
        }

        return '<a href="#" id="main_prefs_button">'.$content.'</a>';
    }

} 
