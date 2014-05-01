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
 * Renders the selection box with the values for the next / running time record.
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_SelectionBox extends Zend_View_Helper_Abstract
{
    protected $configuration = array(
        'pre_label'         => '<strong class="short">',
        'post_label'        => '</strong>',
        'pre_selection'     => '', // FIXME make me dynamic - <span class="selection" id="%s">
        'post_selection'    => '</span><br/>',
        'pre_title'         => '<strong>',
        'post_title'        => '</strong><br />',
        'box_header'        => '<div id="selector"><div class="preselection">',
        'box_footer'        => '</div></div>'
    );

    /**
     * @param null $configuration
     * @return $this
     */
    public function selectionBox($configuration = null)
    {
        if ($configuration !== null && !empty($configuration)) {
            $this->configuration = array_merge($this->configuration, $configuration);
        }
        return $this;
    }

    /**
     * @return array
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $conf = $this->getConfiguration();
        $html = $conf['box_header'] . $conf['pre_title'] . $this->view->kga['lang']['selectedForRecording'] . $conf['post_title'];

        $values = array(
            array(
                'label' => $this->view->kga['lang']['selectedCustomerLabel'],
                'id'    => 'selected_customer',
                'value' => $this->view->customerData['name']
            ),
            array(
                'label' => $this->view->kga['lang']['selectedProjectLabel'],
                'id'    => 'selected_project',
                'value' => $this->view->projectData['name']
            ),
            array(
                'label' => $this->view->kga['lang']['selectedActivityLabel'],
                'id'    => 'selected_activity',
                'value' => $this->view->activityData['name']
            ),
        );

        foreach($values as $entry) {
            $html .=    $conf['pre_label'] .
                        $entry['label'] .
                        $conf['post_label'] .
                        //$conf['pre_selection'] .
                        '<span class="selection" id="'.$entry['id'].'">' .
                        $this->view->escape($entry['value']) .
                        $conf['post_selection'];
            ;
        }

        $html .= $conf['box_footer'];

        return $html;
    }
}
