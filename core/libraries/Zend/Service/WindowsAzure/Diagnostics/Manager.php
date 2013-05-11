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
 * @package    Zend_Service_WindowsAzure
 * @subpackage Diagnostics
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Service_WindowsAzure_Storage_Blob
 */
require_once 'Zend/Service/WindowsAzure/Storage/Blob.php';

/**
 * @see Zend_Service_WindowsAzure_Diagnostics_Exception
 */
require_once 'Zend/Service/WindowsAzure/Diagnostics/Exception.php';

/**
 * @see Zend_Service_WindowsAzure_Diagnostics_ConfigurationInstance
 */
require_once 'Zend/Service/WindowsAzure/Diagnostics/ConfigurationInstance.php';

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage Diagnostics
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_WindowsAzure_Diagnostics_Manager
{
	/**
	 * Blob storage client
	 *
	 * @var Zend_Service_WindowsAzure_Storage_Blob
	 */
	protected $_blobStorageClient = null;
	
	/**
	 * Control container name
	 *
	 * @var string
	 */
	protected $_controlContainer = '';

	/**
	 * Create a new instance of Zend_Service_WindowsAzure_Diagnostics_Manager
	 *
	 * @param Zend_Service_WindowsAzure_Storage_Blob $blobStorageClient Blob storage client
	 * @param string $controlContainer Control container name
	 */
	public function __construct(Zend_Service_WindowsAzure_Storage_Blob $blobStorageClient = null, $controlContainer = 'wad-control-container')
	{
		$this->_blobStorageClient = $blobStorageClient;
		$this->_controlContainer  = $controlContainer;
        $this->_ensureStorageInitialized();
    }

	/**
	 * Ensure storage has been initialized
	 */
	protected function _ensureStorageInitialized()
	{
		if (!$this->_blobStorageClient->containerExists($this->_controlContainer)) {
			$this->_blobStorageClient->createContainer($this->_controlContainer);
		}
	}
	
	/**
	 * Get default configuration values
	 *
	 * @return Zend_Service_WindowsAzure_Diagnostics_ConfigurationInstance
	 */
	public function getDefaultConfiguration()
	{
		return new Zend_Service_WindowsAzure_Diagnostics_ConfigurationInstance();
	}
	
	/**
	 * Checks if a configuration for a specific role instance exists.
	 *
	 * @param string $roleInstance Role instance name, can be found in $_SERVER['RdRoleId'] when hosted on Windows Azure.
	 * @return boolean
	 * @throws Zend_Service_WindowsAzure_Diagnostics_Exception
	 */
	public function configurationForRoleInstanceExists($roleInstance = null)
	{
		if ($roleInstance === null) {
			throw new Zend_Service_WindowsAzure_Diagnostics_Exception('Role instance should be specified. Try reading $_SERVER[\'RdRoleId\'] for this information if the application is hosted on Windows Azure Fabric or Development Fabric.');
		}

		return $this->_blobStorageClient->blobExists($this->_controlContainer, $roleInstance);
	}
	
	/**
	 * Checks if a configuration for current role instance exists. Only works on Development Fabric or Windows Azure Fabric.
	 *
	 * @return boolean
	 * @throws Zend_Service_WindowsAzure_Diagnostics_Exception
	 */
	public function configurationForCurrentRoleInstanceExists()
	{
		if (!isset($_SERVER['RdRoleId'])) {
			throw new Zend_Service_WindowsAzure_Diagnostics_Exception('Server variable \'RdRoleId\' is unknown. Please verify the application is running in Development Fabric or Windows Azure Fabric.');
		}

		return $this->_blobStorageClient->blobExists($this->_controlContainer, $_SERVER['RdRoleId']);
	}
	
	/**
	 * Get configuration for current role instance. Only works on Development Fabric or Windows Azure Fabric.
	 *
	 * @return Zend_Service_WindowsAzure_Diagnostics_ConfigurationInstance
	 * @throws Zend_Service_WindowsAzure_Diagnostics_Exception
	 */
	public function getConfigurationForCurrentRoleInstance()
	{
		if (!isset($_SERVER['RdRoleId'])) {
			throw new Zend_Service_WindowsAzure_Diagnostics_Exception('Server variable \'RdRoleId\' is unknown. Please verify the application is running in Development Fabric or Windows Azure Fabric.');
		}
		return $this->getConfigurationForRoleInstance($_SERVER['RdRoleId']);
	}
	
	/**
	 * Set configuration for current role instance. Only works on Development Fabric or Windows Azure Fabric.
	 *
	 * @param Zend_Service_WindowsAzure_Diagnostics_ConfigurationInstance $configuration Configuration to apply
	 * @throws Zend_Service_WindowsAzure_Diagnostics_Exception
	 */
	public function setConfigurationForCurrentRoleInstance(Zend_Service_WindowsAzure_Diagnostics_ConfigurationInstance $configuration)
	{
		if (!isset($_SERVER['RdRoleId'])) {
			throw new Zend_Service_WindowsAzure_Diagnostics_Exception('Server variable \'RdRoleId\' is unknown. Please verify the application is running in Development Fabric or Windows Azure Fabric.');
		}
		$this->setConfigurationForRoleInstance($_SERVER['RdRoleId'], $configuration);
	}
	
	/**
	 * Get configuration for a specific role instance
	 *
	 * @param string $roleInstance Role instance name, can be found in $_SERVER['RdRoleId'] when hosted on Windows Azure.
	 * @return Zend_Service_WindowsAzure_Diagnostics_ConfigurationInstance
	 * @throws Zend_Service_WindowsAzure_Diagnostics_Exception
	 */
	public function getConfigurationForRoleInstance($roleInstance = null)
	{
		if ($roleInstance === null) {
			throw new Zend_Service_WindowsAzure_Diagnostics_Exception('Role instance should be specified. Try reading $_SERVER[\'RdRoleId\'] for this information if the application is hosted on Windows Azure Fabric or Development Fabric.');
		}

        if ($this->_blobStorageClient->blobExists($this->_controlContainer, $roleInstance)) {
            $configurationInstance = new Zend_Service_WindowsAzure_Diagnostics_ConfigurationInstance();
            $configurationInstance->loadXml( $this->_blobStorageClient->getBlobData($this->_controlContainer, $roleInstance) );
            return $configurationInstance;
        }

		return new Zend_Service_WindowsAzure_Diagnostics_ConfigurationInstance();
	}
	
	/**
	 * Set configuration for a specific role instance
	 *
	 * @param string $roleInstance Role instance name, can be found in $_SERVER['RdRoleId'] when hosted on Windows Azure.
	 * @param Zend_Service_WindowsAzure_Diagnostics_ConfigurationInstance $configuration Configuration to apply
	 * @throws Zend_Service_WindowsAzure_Diagnostics_Exception
	 */
	public function setConfigurationForRoleInstance($roleInstance = null, Zend_Service_WindowsAzure_Diagnostics_ConfigurationInstance $configuration)
	{
		if ($roleInstance === null) {
			throw new Zend_Service_WindowsAzure_Diagnostics_Exception('Role instance should be specified. Try reading $_SERVER[\'RdRoleId\'] for this information if the application is hosted on Windows Azure Fabric or Development Fabric.');
		}

        $this->_blobStorageClient->putBlobData($this->_controlContainer, $roleInstance, $configuration->toXml(), array(), null, array('Content-Type' => 'text/xml'));
    }
}
