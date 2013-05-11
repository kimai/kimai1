<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: FormImage.php 23775 2011-03-01 17:25:24Z ralph $
 */


/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';


/**
 * Helper to generate an "image" element
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_View_Helper_FormImage extends Zend_View_Helper_FormElement
{
    /**
     * Generates an 'image' element.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The source ('src="..."') for the image.
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    public function formImage($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        // Determine if we should use the value or the src attribute
        if (isset($attribs['src'])) {
            $src = ' src="' . $this->view->escape($attribs['src']) . '"';
            unset($attribs['src']);
        } else {
            $src = ' src="' . $this->view->escape($value) . '"';
            unset($value);
        }

        // Do we have a value?
        if (isset($value) && !empty($value)) {
            $value = ' value="' . $this->view->escape($value) . '"';
        } else {
            $value = '';
        }

        // Disabled?
        $disabled = '';
        if ($disable) {
            $disabled = ' disabled="disabled"';
        }

        // XHTML or HTML end tag?
        $endTag = ' />';
        if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
            $endTag= '>';
        }

        // build the element
        $xhtml = '<input type="image"'
                . ' name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($id) . '"'
                . $src
                . $value
                . $disabled
                . $this->_htmlAttribs($attribs)
                . $endTag;

        return $xhtml;
    }
}
