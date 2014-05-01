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

require_once __DIR__ . '/../Floater.php';

/**
 * Returns the HTML for an icon.
 * Made as ViewHelper for easier customization of skins.
 *
 * For all available icons, see http://fortawesome.github.io/Font-Awesome/icons/
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_Flat_Floater extends Zend_View_Helper_Floater
{
    /**
     * Return the HTML for starting a floater page.
     *
     * @return string
     */
    public function floaterBegin()
    {
        $html = '';

        if (!empty($this->formAction)) {
            $html .= '<form action="'.$this->formAction.'" id="'.$this->formId.'" method="'.$this->formMethod.'">';
        }

        $html .= '<div class="modal-header">';

        if ($this->showButtonClose) {
            $html .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
        }

        $html .= '<h4 class="modal-title">'.$this->title.'</h4>';
        $html .= '</div>';

        $html .= '<div class="modal-body">';

        if(!empty($this->tabs)) {
            $html .= '<ul class="nav nav-tabs">';
            foreach($this->tabs as $id => $title)
            {
                $html .= '<li';
                if(!empty($this->activeTab) && $this->activeTab == $id) {
                    $html .= ' class="active"';
                }
                $html .= '><a href="#'.$id.'" data-toggle="tab">'.$title.'</a></li>';
            }
            $html .= '</ul>';
            $html .= '<div class="tab-content">';
        }

        return $html;
    }

    /**
     * Return the HTML for ending a floater page.
     *
     * @return string
     */
    public function floaterEnd()
    {
        $html = '';

        if(!empty($this->tabs)) {
            $html .= '</div>'; // .tab-content
        }

        $html .= '</div>'; // .modal-body

        // form buttons
        if($this->showButtonCancel || $this->showButtonSave)
        {
            $html .= '<div class="modal-footer">';
            if($this->showButtonCancel) {
                $html .= '<button type="button" class="btn btn-default" data-dismiss="modal">'.$this->view->translate('cancel').'</button> ';
            }

            if($this->showButtonSave) {
                $html .= '<button type="submit" class="btn btn-primary">'.$this->view->translate('submit').'</button> ';
            }
            $html .= '</div>';
        }

        if (!empty($this->formAction)) {
            $html .= '</form>';
        }

        return $html;
    }

    /**
     * Returns the main structure of the floater.
     *
     * @return string
     */
    public function floaterBody()
    {
        return '
            <div class="modal fade" id="kimai_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                            ...
                    </div>
                </div>
            </div>
        ';
    }

    public function tabContentBegin($id)
    {
        $html = '<div id="'.$id.'" class="tab-pane';
        if(!empty($this->activeTab) && $this->activeTab == $id) {
            $html .= ' active';
        }
        $html .= '">';
        return $html;
    }

    public function tabContentEnd()
    {
        return '</div>';
    }
}
