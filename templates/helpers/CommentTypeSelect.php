<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team since 2006
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
 * A select box to choose the comment type.
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_CommentTypeSelect extends Zend_View_Helper_FormSelect
{
    /**
     * @param string|int|null $value
     * @return string
     */
    public function commentTypeSelect($value = null)
    {
        return $this->formSelect(
            'commentType',
            $value,
            array(
                'id' => 'commentType',
                'class' => 'formfield',
                'tabindex' => '14'
            ),
            $this->getTypes()
        );
    }

    /**
     * @return array
     */
    protected function getTypes()
    {
        return array(
            0 => $this->view->translate('ctype0'),
            1 => $this->view->translate('ctype1'),
            2 => $this->view->translate('ctype2')
        );
    }
}
