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
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 23775 2011-03-01 17:25:24Z ralph $
 */

/**
 * The Interface required by any InfoCard Assertion Object implemented within the component
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_InfoCard_Xml_Assertion_Interface
{
    /**
     * Get the Assertion ID of the assertion
     *
     * @return string The Assertion ID
     */
    public function getAssertionID();

    /**
     * Return an array of attributes (claims) contained within the assertion
     *
     * @return array An array of attributes / claims within the assertion
     */
    public function getAttributes();

    /**
     * Get the Assertion URI for this type of Assertion
     *
     * @return string the Assertion URI
     */
    public function getAssertionURI();

    /**
     * Return an array of conditions which the assertions are predicated on
     *
     * @return array an array of conditions
     */
    public function getConditions();

    /**
     * Validate the conditions array returned from the getConditions() call
     *
     * @param array $conditions An array of condtions for the assertion taken from getConditions()
     * @return mixed Boolean true on success, an array of condition, error message on failure
     */
    public function validateConditions(Array $conditions);
}
