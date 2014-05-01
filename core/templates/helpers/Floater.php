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
 * Returns the HTML for the loader animation.
 *
 * Anatomy of a floater:
 *
 * floaterBody() is rendered within the main page and its content area is then
 * replaced as this pseudo-code show:
 *
 *  <floaterBody>
 *      <floaterBegin />
 *          <your-content-here />
 *      <floaterEnd />
 *  </floaterBody>
 *
 * @author Kevin Papst
 */
class Zend_View_Helper_Floater extends Zend_View_Helper_Abstract
{
    protected $title = '';

    protected $formAction = '';
    protected $formId = '';
    protected $formMethod = 'POST';

    protected $tabs = array();
    protected $activeTab = '';

    protected $showButtonClose = true;
    protected $showButtonCancel = true;
    protected $showButtonSave = true;

    /**
     * @return $this
     */
    public function floater()
    {
        return $this;
    }

    public function addTab($id, $title)
    {
        if (empty($this->tabs) && empty($this->activeTab)) {
            $this->activeTab = $id;
        }
        $this->tabs[$id] = $title;
        return $this;
    }

    public function setActiveTab($id)
    {
        $this->activeTab = $id;
        return $this;
    }

    public function setShowCloseButton($close)
    {
        $this->showButtonClose = $close;
        return $this;
    }

    public function setShowCancelButton($cancel)
    {
        $this->showButtonCancel = $cancel;
        return $this;
    }

    public function setShowSaveButton($save)
    {
        $this->showButtonSave = $save;
        return $this;
    }

    /**
     * @param $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param $action
     * @return $this
     */
    public function setFormAction($action)
    {
        $this->formAction = $action;
        return $this;
    }

    /**
     * @param string $formId
     * @return $this
     */
    public function setFormId($formId)
    {
        $this->formId = $formId;
        return $this;
    }

    /**
     * @param string $formMethod
     * @return $this
     */
    public function setFormMethod($formMethod)
    {
        $this->formMethod = $formMethod;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $html = '';

        return $html;
    }

    // ---------------------- SKIN SPECIFIC ----------------------

    /**
     * Return the HTML for starting a floater page.
     *
     * @return string
     */
    public function floaterBegin()
    {
        $html = '
            <div id="floater_innerwrap">
                <div id="floater_handle">
                    <span id="floater_title">'.$this->title.'</span>
                    <div class="right">
                    ';

        if ($this->showButtonClose) {
            $html .= '<a href="#" class="close" onClick="floaterClose();">'.$this->view->translate('close') . '</a>';
        }

        $html .= '
                    </div>
                </div>
        ';

        if(!empty($this->tabs)) {
            $html .= '<div class="menuBackground"><ul class="menu tabSelection">';
            foreach($this->tabs as $id => $title)
            {
                $html .= '<li class="tab norm"><a href="#'.$id.'">
                          <span class="aa">&nbsp;</span>
                          <span class="bb">'.$title.'</span>
                          <span class="cc">&nbsp;</span>
                          </a></li>';
            }
            $html .= '</ul></div>';
        }

        $html .= '<div class="floater_content">';

        if (!empty($this->formAction)) {
            $html .= '<form action="'.$this->formAction.'" id="'.$this->formId.'" method="'.$this->formMethod.'">';
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

        // form buttons
        if($this->showButtonCancel || $this->showButtonSave)
        {
            $html .= '<div id="formbuttons">';
            if($this->showButtonCancel) {
                $html .= '<input class="btn_norm" type="button" value="'.$this->view->translate('cancel').'" onClick="floaterClose(); return false;" /> ';
            }
            if($this->showButtonSave) {
                $html .= '<input class="btn_ok" type="submit" value="'.$this->view->translate('submit').'" /> ';
            }
            $html .= '</div>';
        }

        if (!empty($this->formAction)) {
            $html .= '</form>';
        }

        $html .= '</div>'; // .floater_content

        $html .= '</div>'; // #floater_innerwrap

        return $html;
    }

    public function tabContentBegin($id)
    {
        return '<fieldset id="'.$id.'">';

    }

    public function tabContentEnd()
    {
        return '</fieldset>';
    }

    /**
     * Returns the main structure of the floater.
     *
     * @return string
     */
    public function floaterBody()
    {
        return '<div id="floater">floater</div>';
    }

} 
