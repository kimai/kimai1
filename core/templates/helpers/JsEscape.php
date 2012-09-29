<?php
class Zend_View_Helper_JsEscape extends Zend_View_Helper_Abstract
{
    public $view;
 
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function jsEscape($text)
    {
        return str_replace("'","\\'",$this->view->escape($text));
    }
} 
