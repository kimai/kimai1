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

require_once __DIR__ . '/../ExtensionScreen.php';

/**
 * Displays a error message in a pre-formatted way.
 * If no message is passed, the default message "noItems" (no entry is available) is used.
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_Flat_ExtensionScreen extends Zend_View_Helper_ExtensionScreen
{

    public function getHeader()
    {
        $options = $this->getOptions();
        $pre    = isset($options['pre_title']) ? $options['pre_title'] : '';
        $post   = isset($options['post_title']) ? $options['post_title'] : '';
        $title = isset($options['title']) ? $options['title'] : '';
        $id    = isset($options['id']) ? $options['id'] : '';
        $level  = isset($options['level']) ? $options['level'] : array();

        $html = '
        <div class="panel panel-default">';
        if ($title != '') {
            $html .= '<div class="panel-heading">'.$pre.' <strong>'.$title.'</strong> '.$post.'</div>';
        }

        $html .= '<div class="row">';

        if (!empty($level)) {
//            $html .= '<div id="'.$level[count($level)-1].'">';
        }

        return $html;
    }


    public function getFooter()
    {
        $options = $this->getOptions();
        $level  = isset($options['level']) ? $options['level'] : array();

        $html = '';

        if (!empty($level)) {
//            $html .= '</div>';
        }

        $html .= '
        </div>
      </div>
        ';

        return $html;
    }
}
