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
 * @version    $Id: XmlEnc.php 24375 2011-08-13 23:16:50Z padraic $
 */

/**
 * Zend_InfoCard_Xml_EncryptedData/Abstract.php
 */
require_once 'Zend/InfoCard/Xml/EncryptedData/Abstract.php';

/**
 * An XmlEnc formatted EncryptedData XML block
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_InfoCard_Xml_EncryptedData_XmlEnc extends Zend_InfoCard_Xml_EncryptedData_Abstract
{

    /**
     * Returns the Encrypted CipherValue block from the EncryptedData XML document
     *
     * @throws Zend_InfoCard_Xml_Exception
     * @return string The value of the CipherValue block base64 encoded
     */
    public function getCipherValue()
    {
        $this->registerXPathNamespace('enc', 'http://www.w3.org/2001/04/xmlenc#');

        list(,$cipherdata) = $this->xpath("//enc:CipherData");

        if(!($cipherdata instanceof Zend_InfoCard_Xml_Element)) {
            throw new Zend_InfoCard_Xml_Exception("Unable to find the enc:CipherData block");
        }
        $cipherdata->registerXPathNamespace('enc', 'http://www.w3.org/2001/04/xmlenc#');
        list(,$ciphervalue) = $cipherdata->xpath("//enc:CipherValue");

        if(!($ciphervalue instanceof Zend_InfoCard_Xml_Element)) {
            throw new Zend_InfoCard_Xml_Exception("Unable to fidn the enc:CipherValue block");
        }

        return (string)$ciphervalue;
    }
}
