<?php
/*
	File: xajaxArgumentManager.inc.php

	Contains the xajaxArgumentManager class

	Title: xajaxArgumentManager class

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: xajaxArgumentManager.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

if (!defined('XAJAX_METHOD_UNKNOWN')) define('XAJAX_METHOD_UNKNOWN', 0);
if (!defined('XAJAX_METHOD_GET')) define('XAJAX_METHOD_GET', 1);
if (!defined('XAJAX_METHOD_POST')) define('XAJAX_METHOD_POST', 2);

/*
	Class: xajaxArgumentManager
	
	This class processes the input arguments from the GET or POST data of 
	the request.  If this is a request for the initial page load, no arguments
	will be processed.  During a xajax request, any arguments found in the
	GET or POST will be converted to a PHP array.
*/
class xajaxArgumentManager
{
	/*
		Array: aArgs
		
		An array of arguments received via the GET or POST parameter
		xjxargs.
	*/
	var $aArgs;
	
	/*
		Boolean: bDecodeUTF8Input
		
		A configuration option used to indicate whether input data should be
		UTF8 decoded automatically.
	*/
	var $bDecodeUTF8Input;
	
	/*
		String: sCharacterEncoding
		
		The character encoding in which the input data will be received.
	*/
	var $sCharacterEncoding;
	
	/*
		Integer: nMethod
		
		Stores the method that was used to send the arguments from the client.  Will
		be one of: XAJAX_METHOD_UNKNOWN, XAJAX_METHOD_GET, XAJAX_METHOD_POST
	*/
	var $nMethod;
	
	/*
		Array: aSequence
		
		Stores the decoding sequence table.
	*/
	var $aSequence;
	
	function convertStringToBool($sValue)
	{
		if (0 == strcasecmp($sValue, 'true'))
			return true;
		if (0 == strcasecmp($sValue, 'false'))
			return false;
		if (is_numeric($sValue))
		{
			if (0 == $sValue)
				return false;
			return true;
		}
		return false;
	}
	
	function argumentStripSlashes(&$sArg)
	{
		if (false == is_string($sArg))
			return;
		
		$sArg = stripslashes($sArg);
	}
	
	function argumentDecodeXML(&$sArg)
	{
		if (false == is_string($sArg))
			return;
		
		if (0 == strlen($sArg))
			return;



		$nStackDepth = 0;
		$aStack = array();
		$aArg = array();
		
		$nCurrent = 0;
		$nLast = 0;
		$aExpecting = array();
		$nFound = 0;
		list($aExpecting, $nFound) = $this->aSequence['start'];

		$nLength = strlen($sArg);
			
		$sKey = '';
		$mValue = '';


		while ($nCurrent < $nLength)
		{
			$bFound = false;
			
			foreach ($aExpecting as $sExpecting => $nExpectedLength)
			{
				if ($sArg[$nCurrent] == $sExpecting[0])
				if ($sExpecting == substr($sArg, $nCurrent, $nExpectedLength))
				{
					list($aExpecting, $nFound) = $this->aSequence[$sExpecting];
					
					switch ($nFound)
					{
					case 3:	// k
						$sKey = '';
						break;
					case 4:	// /k
						$sKey = str_replace(
							array('<'.'![CDATA[', ']]>'), 
							'', 
							substr($sArg, $nLast, $nCurrent - $nLast)
							);
						break;
					case 5:	// v
						$mValue = '';
						break;
					case 6:	// /v
						if ($nLast < $nCurrent)
						{
							$mValue = str_replace(
								array('<'.'![CDATA[', ']]>'), 
								'', 
								substr($sArg, $nLast, $nCurrent - $nLast)
								);
							
							$cType = substr($mValue, 0, 1);
							$sValue = substr($mValue, 1);
							switch ($cType) {
								case 'S': $mValue = false === $sValue ? '' : $sValue;  break;
								case 'B': $mValue = $this->convertStringToBool($sValue); break;
								case 'N': $mValue = floatval($sValue); break;
								case '*': $mValue = null; break;
							}
						}
						break;
					case 7:	// /e
						$aArg[$sKey] = $mValue;
						break;
					case 1:	// xjxobj
						++$nStackDepth;
						array_push($aStack, $aArg);
						$aArg = array();
						array_push($aStack, $sKey);
						$sKey = '';
						break;
					case 8:	// /xjxobj
						if (1 < $nStackDepth) {
							$mValue = $aArg;								
							$sKey = array_pop($aStack);
							$aArg = array_pop($aStack);
							--$nStackDepth;
						} else {
							$sArg = $aArg;
							return;
						}
						break;
					}
					$nCurrent += $nExpectedLength;
					$nLast = $nCurrent;
					$bFound = true;
					break;
				}
			}

			if (false == $bFound)
			{
				if (0 == $nCurrent)
				{
					$sArg = str_replace(
						array('<'.'![CDATA[', ']]>'), 
						'', 
						$sArg
						);
					
					$cType = substr($sArg, 0, 1);
					$sValue = substr($sArg, 1);
					switch ($cType) {
							case 'S': $sArg = false === $sValue ? '' : $sValue;  break;
					    case 'B': $sArg = $this->convertStringToBool($sValue); break;
					    case 'N': $sArg = floatval($sValue); break;
					    case '*': $sArg = null; break;
					}

					return;
				}

//				for larger arg data, performance may suffer using concatenation				
//				$sText .= $sArg[$nCurrent];
				$nCurrent++;
			}
		}
		
		$objLanguageManager =& xajaxLanguageManager::getInstance();
		
		trigger_error(
			$objLanguageManager->getText('ARGMGR:ERR:01') 
			. $sExpected 
			. $objLanguageManager->getText('ARGMGR:ERR:02') 
			. $sChunk
			, E_USER_ERROR
			);
	}
	
	function argumentDecodeUTF8_iconv(&$mArg)
	{
		if (is_array($mArg))
		{
			foreach (array_keys($mArg) as $sKey)
			{
				$sNewKey = $sKey;
				$this->argumentDecodeUTF8_iconv($sNewKey);
				
				if ($sNewKey != $sKey)
				{
					$mArg[$sNewKey] = $mArg[$sKey];
					unset($mArg[$sKey]);
					$sKey = $sNewKey;
				}

				$this->argumentDecodeUTF8_iconv($mArg[$sKey]);
			}
		}
		else if (is_string($mArg))
			$mArg = iconv("UTF-8", $this->sCharacterEncoding.'//TRANSLIT', $mArg);
	}
	
	function argumentDecodeUTF8_mb_convert_encoding(&$mArg)
	{
		if (is_array($mArg))
		{
			foreach (array_keys($mArg) as $sKey)
			{
				$sNewKey = $sKey;
				$this->argumentDecodeUTF8_mb_convert_encoding($sNewKey);
				
				if ($sNewKey != $sKey)
				{
					$mArg[$sNewKey] = $mArg[$sKey];
					unset($mArg[$sKey]);
					$sKey = $sNewKey;
				}

				$this->argumentDecodeUTF8_mb_convert_encoding($mArg[$sKey]);
			}
		}
		else if (is_string($mArg))
			$mArg = mb_convert_encoding($mArg, $this->sCharacterEncoding, "UTF-8");
	}
	
	function argumentDecodeUTF8_utf8_decode(&$mArg)
	{
		if (is_array($mArg))
		{
			foreach (array_keys($mArg) as $sKey)
			{
				$sNewKey = $sKey;
				$this->argumentDecodeUTF8_utf8_decode($sNewKey);
				
				if ($sNewKey != $sKey)
				{
					$mArg[$sNewKey] = $mArg[$sKey];
					unset($mArg[$sKey]);
					$sKey = $sNewKey;
				}

				$this->argumentDecodeUTF8_utf8_decode($mArg[$sKey]);
			}
		}
		else if (is_string($mArg))
			$mArg = utf8_decode($mArg);
	}
	
	/*
		Constructor: xajaxArgumentManager
		
		Initializes configuration settings to their default values and reads
		the argument data from the GET or POST data.
	*/
	function xajaxArgumentManager()
	{
		$this->aArgs = array();

		$this->bDecodeUTF8Input = false;
		$this->sCharacterEncoding = 'UTF-8';
		$this->nMethod = XAJAX_METHOD_UNKNOWN;
		
		$this->aSequence = array(
			'<'.'k'.'>' => array(array(
				'<'.'/k'.'>' => 4
				), 3),
			'<'.'/k'.'>' => array(array(
				'<'.'v'.'>' => 3, 
				'<'.'/e'.'>' => 4
				), 4),
			'<'.'v'.'>' => array(array(
				'<'.'xjxobj'.'>' => 8, 
				'<'.'/v'.'>' => 4
				), 5),
			'<'.'/v'.'>' => array(array(
				'<'.'/e'.'>' => 4, 
				'<'.'k'.'>' => 3
				), 6),
			'<'.'e'.'>' => array(array(
				'<'.'k'.'>' => 3, 
				'<'.'v'.'>' => 3, 
				'<'.'/e'.'>' => 4
				), 2),
			'<'.'/e'.'>' => array(array(
				'<'.'e'.'>' => 3, 
				'<'.'/xjxobj'.'>' => 9
				), 7),
			'<'.'xjxobj'.'>' => array(array(
				'<'.'e'.'>' => 3, 
				'<'.'/xjxobj'.'>' => 9
				), 1),
			'<'.'/xjxobj'.'>' => array(array(
				'<'.'/v'.'>' => 4
				), 8),
			'start' => array(array(
				'<'.'xjxobj'.'>' => 8
				), 9)
			);
		
		if (isset($_POST['xjxargs'])) {
			$this->nMethod = XAJAX_METHOD_POST;
			$this->aArgs = $_POST['xjxargs'];
		} else if (isset($_GET['xjxargs'])) {
			$this->nMethod = XAJAX_METHOD_GET;
			$this->aArgs = $_GET['xjxargs'];
		}


		if (1 == get_magic_quotes_gpc())
			array_walk($this->aArgs, array(&$this, 'argumentStripSlashes'));
		
		array_walk($this->aArgs, array(&$this, 'argumentDecodeXML'));
	}
	
	/*
		Function: getInstance
		
		Returns:
		
		object - A reference to an instance of this class.  This function is
			used to implement the singleton pattern.
	*/
	function &getInstance()
	{
		static $obj;
		if (!$obj) {
			$obj = new xajaxArgumentManager();
		}
		return $obj;
	}
	
	/*
		Function: configure
		
		Accepts configuration settings from the main <xajax> object.
		
		The <xajaxArgumentManager> tracks the following configuration settings:
			<decodeUTF8Input> - (boolean): See <xajaxArgumentManager->bDecodeUTF8Input>
			<characterEncoding> - (string): See <xajaxArgumentManager->sCharacterEncoding>
	*/
	function configure($sName, $mValue)
	{
		if ('decodeUTF8Input' == $sName) {
			if (true === $mValue || false === $mValue)
				$this->bDecodeUTF8Input = $mValue;
		} else if ('characterEncoding' == $sName) {
			$this->sCharacterEncoding = $mValue;
		}
	}
	
	/*
		Function: getRequestMethod
		
		Returns the method that was used to send the arguments from the client.
	*/
	function getRequestMethod()
	{
		return $this->nMethod;
	}
	
	/*
		Function: process
		
		Returns the array of arguments that were extracted and parsed from 
		the GET or POST data.
	*/
	function process()
	{
		if ($this->bDecodeUTF8Input)
		{
			$sFunction = '';
			
			if (function_exists('iconv'))
				$sFunction = "iconv";
			else if (function_exists('mb_convert_encoding'))
				$sFunction = "mb_convert_encoding";
			else if ($this->sCharacterEncoding == "ISO-8859-1")
				$sFunction = "utf8_decode";
			else {
				$objLanguageManager =& xajaxLanguageManager::getInstance();
				trigger_error(
					$objLanguageManager->getText('ARGMGR:ERR:03')
					, E_USER_NOTICE
					);
			}
			
			$mFunction = array(&$this, 'argumentDecodeUTF8_' . $sFunction);
			
			array_walk($this->aArgs, $mFunction);
			
			$this->bDecodeUTF8Input = false;
		}
		
		return $this->aArgs;
	}
}
