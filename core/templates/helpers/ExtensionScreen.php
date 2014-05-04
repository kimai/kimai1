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
 * Displays a error message in a pre-formatted way.
 * If no message is passed, the default message "noItems" (no entry is available) is used.
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_ExtensionScreen extends Zend_View_Helper_Abstract
{
    private $options = array();

    public function extensionScreen($options = array())
    {
        if (!empty($options)) {
            $this->options = $options;
        }

        return $this;
    }

    protected function getOptions()
    {
        return $this->options;
    }

    public function getHeader()
    {
        $options = $this->getOptions();
        $pre    = isset($options['pre_title']) ? $options['pre_title'] : '';
        $post   = isset($options['post_title']) ? $options['post_title'] : '';
        $title  = isset($options['title']) ? $options['title'] : '';
        $id     = isset($options['id']) ? $options['id'] : '';
        $level  = isset($options['level']) ? $options['level'] : array();
        $styles = isset($options['styles']) ? $options['styles'] : array();

        $html = '
            <div id="'.$id.'" class="kimai_extension_header">
                '.$pre.' <strong>'.$title.'</strong> '.$post.'
            </div>
        ';

        $i = 0;
        foreach($level as $lvl) {
            if ($styles && $i == 0) {
                $html .= '<div id="'.$lvl.'" class="kimai_extension_wrap">';
            } else if ($styles && $i == 1) {
                $html .= '<div id="'.$lvl.'" class="kimai_extension_body">';
            } else {
                $html .= '<div id="'.$lvl.'">';
            }
            $i++;
        }

        return $html;
    }

    public function getFooter()
    {
        $options = $this->getOptions();
        $level  = isset($options['level']) ? $options['level'] : array();

        $html = '';

        foreach($level as $lvl) {
            $html .= '</div>';
        }

        return $html;
    }
} 
