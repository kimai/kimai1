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
 * Returns the translated string.
 *
 * - $key refers to the entry in your language file
 * - $subpackage refers to a potential sub-array in the language file
 * - an alternative referencing is possible by using the delimiter :
 *
 * $view->translate('foo')
 * $view->translate('foo', 'bar')
 * $view->translate('bar:foo')
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_Translate extends Zend_View_Helper_Abstract
{
    public function translate($key, $subpackage = null)
    {
        $keys = explode(':', $key);
        if ($subpackage === null && count($keys) > 1) {
            $subpackage = $keys[0];
            $key = $keys[1];
        }

        if ($subpackage !== null) {
            if (!isset($this->view->kga['lang'][$subpackage][$key])) {
                return '*' . $subpackage . ':' . $key . '*';
            }
            return $this->view->kga['lang'][$subpackage][$key];
        }

        if (!isset($this->view->kga['lang'][$key])) {
            return '*' . $key . '*';
        }

        return $this->view->kga['lang'][$key];
    }
} 
