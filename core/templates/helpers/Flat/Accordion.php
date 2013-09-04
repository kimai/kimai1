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

require_once __DIR__ . '/../Accordion.php';

/**
 * A jQuery Accordion.
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_Flat_Accordion extends Zend_View_Helper_Accordion
{

    public function start()
    {
        $id = $this->increaseAccordionCounter();
        return '<div class="accordion" id = "accordion'.$id.'">';
    }

    public function end()
    {
        return '</div>';
    }

    public function renderEntry($title, $content, $options = array())
    {
        $id = $this->increaseEntryCounter();

        return '
             <div class="accordion-group" >
                <div class="accordion-heading">
                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion'.$this->getAccordionCounter().'" href="#collapse'.$id.'">
                        '.$title.'
                     </a>
                </div>
                <div id="collapse'.$id.'" class="accordion-body collapse" style="height: 0px;">
                    <div id="adminPanel_extension_s'.$id.'" class="accordion-inner text-small">
                    '.$content.'
                    </div>
                </div>
             </div>
        ';
    }
}
