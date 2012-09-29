<?php
class Zend_View_Helper_Truncate extends Zend_View_Helper_Abstract
{
    public function truncate($text, $maxLength, $append = '')
    {
        if (strlen($text) >  $maxLength)
          return substr($text,0,$maxLength) . $append;
        else
          return $text;
    }
} 
