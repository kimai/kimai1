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
 * @version    $Id: ProviderManifestable.php 23775 2011-03-01 17:25:24Z ralph $
 */

/**
 * @see Zend_Tool_Framework_Manifest_Interface.php
 */
require_once 'Zend/Tool/Framework/Manifest/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Tool_Framework_Manifest_ProviderManifestable extends Zend_Tool_Framework_Manifest_Interface
{

    /**
     * getProviders()
     *
     * Should either return a single provider or an array
     * of providers
     *
     * @return array|string|Zend_Tool_Framework_Provider_Interface
     */
    public function getProviders();

}
