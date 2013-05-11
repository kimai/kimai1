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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 23775 2011-03-01 17:25:24Z ralph $
 */

/**
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Tool_Project_Profile_FileParser_Interface
{

    /**
     * serialize()
     *
     * This method should take a profile and return a string
     * representation of it.
     *
     * @param Zend_Tool_Project_Profile $profile
     * @return string
     */
    public function serialize(Zend_Tool_Project_Profile $profile);

    /**
     * unserialize()
     *
     * This method should be able to take string data an create a
     * struture in the provided $profile
     *
     * @param string $data
     * @param Zend_Tool_Project_Profile $profile
     */
    public function unserialize($data, Zend_Tool_Project_Profile $profile);

}