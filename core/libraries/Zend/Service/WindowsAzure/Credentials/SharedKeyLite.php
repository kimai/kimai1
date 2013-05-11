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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: SharedKeyLite.php 23775 2011-03-01 17:25:24Z ralph $
 */

/**
 * @see Zend_Service_WindowsAzure_Credentials_CredentialsAbstract
 */
require_once 'Zend/Service/WindowsAzure/Credentials/CredentialsAbstract.php';

/**
 * @see Zend_Service_WindowsAzure_Storage
 */
require_once 'Zend/Service/WindowsAzure/Storage.php';

/**
 * @see Zend_Service_WindowsAzure_Credentials_SharedKey
 */
require_once 'Zend/Service/WindowsAzure/Credentials/SharedKey.php';

/**
 * @see Zend_Service_WindowsAzure_Credentials_Exception
 */
require_once 'Zend/Service/WindowsAzure/Credentials/Exception.php';

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_WindowsAzure_Credentials_SharedKeyLite
    extends Zend_Service_WindowsAzure_Credentials_CredentialsAbstract
{
    /**
     * Sign request URL with credentials
     *
     * @param string $requestUrl Request URL
     * @param string $resourceType Resource type
     * @param string $requiredPermission Required permission
     * @return string Signed request URL
     */
    public function signRequestUrl(
        $requestUrl = '',
        $resourceType = Zend_Service_WindowsAzure_Storage::RESOURCE_UNKNOWN,
        $requiredPermission = Zend_Service_WindowsAzure_Credentials_CredentialsAbstract::PERMISSION_READ
    ) {
        return $requestUrl;
    }
    
    /**
     * Sign request headers with credentials
     *
     * @param string $httpVerb HTTP verb the request will use
     * @param string $path Path for the request
     * @param string $queryString Query string for the request
     * @param array $headers x-ms headers to add
     * @param boolean $forTableStorage Is the request for table storage?
     * @param string $resourceType Resource type
     * @param string $requiredPermission Required permission
     * @param mixed  $rawData Raw post data
     * @return array Array of headers
     */
    public function signRequestHeaders(
        $httpVerb = Zend_Http_Client::GET,
        $path = '/',
        $queryString = '',
        $headers = null,
        $forTableStorage = false,
        $resourceType = Zend_Service_WindowsAzure_Storage::RESOURCE_UNKNOWN,
        $requiredPermission = Zend_Service_WindowsAzure_Credentials_CredentialsAbstract::PERMISSION_READ,
        $rawData = null
    ) {
        // Table storage?
        if (!$forTableStorage) {
            throw new Zend_Service_WindowsAzure_Credentials_Exception('The Windows Azure SDK for PHP does not support SharedKeyLite authentication on blob or queue storage. Use SharedKey authentication instead.');
        }
        
        // Determine path
        if ($this->_usePathStyleUri) {
            $path = substr($path, strpos($path, '/'));
        }

        // Determine query
        $queryString = $this->_prepareQueryStringForSigning($queryString);

        // Build canonicalized resource string
        $canonicalizedResource  = '/' . $this->_accountName;
        if ($this->_usePathStyleUri) {
            $canonicalizedResource .= '/' . $this->_accountName;
        }
        $canonicalizedResource .= $path;
        if ($queryString !== '') {
            $canonicalizedResource .= $queryString;
        }

        // Request date
        $requestDate = '';
        if (isset($headers[Zend_Service_WindowsAzure_Credentials_CredentialsAbstract::PREFIX_STORAGE_HEADER . 'date'])) {
            $requestDate = $headers[Zend_Service_WindowsAzure_Credentials_CredentialsAbstract::PREFIX_STORAGE_HEADER . 'date'];
        } else {
            $requestDate = gmdate('D, d M Y H:i:s', time()) . ' GMT'; // RFC 1123
        }

		// Create string to sign
		$stringToSign   = array();
    	$stringToSign[] = $requestDate; // Date
    	$stringToSign[] = $canonicalizedResource;		 			// Canonicalized resource
    	$stringToSign   = implode("\n", $stringToSign);
    	$signString     = base64_encode(hash_hmac('sha256', $stringToSign, $this->_accountKey, true));

    	// Sign request
    	$headers[Zend_Service_WindowsAzure_Credentials_CredentialsAbstract::PREFIX_STORAGE_HEADER . 'date'] = $requestDate;
    	$headers['Authorization'] = 'SharedKeyLite ' . $this->_accountName . ':' . $signString;
    	
    	// Return headers
    	return $headers;
	}
	
	/**
	 * Prepare query string for signing
	 *
	 * @param  string $value Original query string
	 * @return string        Query string for signing
	 */
	protected function _prepareQueryStringForSigning($value)
	{
	    // Check for 'comp='
	    if (strpos($value, 'comp=') === false) {
	        // If not found, no query string needed
	        return '';
	    } else {
	        // If found, make sure it is the only parameter being used
    		if (strlen($value) > 0 && strpos($value, '?') === 0) {
    			$value = substr($value, 1);
    		}
    		
    		// Split parts
    		$queryParts = explode('&', $value);
    		foreach ($queryParts as $queryPart) {
    		    if (strpos($queryPart, 'comp=') !== false) {
    		        return '?' . $queryPart;
    		    }
    		}

            // Should never happen...
            return '';
        }
    }
}
