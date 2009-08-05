<?php
/*
	File: xajaxCallableObjectPlugin.inc.php

	Contains the xajaxCallableObjectPlugin class

	Title: xajaxCallableObjectPlugin class

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: xajaxCallableObjectPlugin.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Constant: XAJAX_CALLABLE_OBJECT
		Specifies that the item being registered via the <xajax->register> function is a
		object who's methods will be callable from the browser.
*/
if (!defined ('XAJAX_CALLABLE_OBJECT')) define ('XAJAX_CALLABLE_OBJECT', 'callable object');

//SkipAIO
require dirname(__FILE__) . '/support/xajaxCallableObject.inc.php';
//EndSkipAIO

/*
	Class: xajaxCallableObjectPlugin
*/
class xajaxCallableObjectPlugin extends xajaxRequestPlugin
{
	/*
		Array: aCallableObjects
	*/
	var $aCallableObjects;

	/*
		String: sXajaxPrefix
	*/
	var $sXajaxPrefix;
	
	/*
		String: sDefer
	*/
	var $sDefer;
	
	var $bDeferScriptGeneration;

	/*
		String: sRequestedClass
	*/
	var $sRequestedClass;
	
	/*
		String: sRequestedMethod
	*/
	var $sRequestedMethod;

	/*
		Function: xajaxCallableObjectPlugin
	*/
	function xajaxCallableObjectPlugin()
	{
		$this->aCallableObjects = array();

		$this->sXajaxPrefix = 'xajax_';
		$this->sDefer = '';
		$this->bDeferScriptGeneration = false;

		$this->sRequestedClass = NULL;
		$this->sRequestedMethod = NULL;

		if (!empty($_GET['xjxcls'])) $this->sRequestedClass = $_GET['xjxcls'];
		if (!empty($_GET['xjxmthd'])) $this->sRequestedMethod = $_GET['xjxmthd'];
		if (!empty($_POST['xjxcls'])) $this->sRequestedClass = $_POST['xjxcls'];
		if (!empty($_POST['xjxmthd'])) $this->sRequestedMethod = $_POST['xjxmthd'];
	}

	/*
		Function: configure
	*/
	function configure($sName, $mValue)
	{
		if ('wrapperPrefix' == $sName) {
			$this->sXajaxPrefix = $mValue;
		} else if ('scriptDefferal' == $sName) {
			if (true === $mValue) $this->sDefer = 'defer ';
			else $this->sDefer = '';
		} else if ('deferScriptGeneration' == $sName) {
			if (true === $mValue || false === $mValue)
				$this->bDeferScriptGeneration = $mValue;
			else if ('deferred' === $mValue)
				$this->bDeferScriptGeneration = $mValue;
		}
	}

	/*
		Function: register
	*/
	function register($aArgs)
	{
		if (1 < count($aArgs))
		{
			$sType = $aArgs[0];

			if (XAJAX_CALLABLE_OBJECT == $sType)
			{
				$xco =& $aArgs[1];

//SkipDebug
				if (false === is_object($xco))
				{
					trigger_error("To register a callable object, please provide an instance of the desired class.", E_USER_WARNING);
					return false;
				}
//EndSkipDebug

				if (false === is_a($xco, 'xajaxCallableObject'))
					$xco =& new xajaxCallableObject($xco);

				if (2 < count($aArgs))
					if (is_array($aArgs[2]))
						foreach ($aArgs[2] as $sKey => $aValue)
							foreach ($aValue as $sName => $sValue)
								$xco->configure($sKey, $sName, $sValue);

				$this->aCallableObjects[] =& $xco;

				return $xco->generateRequests($this->sXajaxPrefix);
			}
		}

		return false;
	}

	/*
		Function: generateClientScript
	*/
	function generateClientScript()
	{
		if (false === $this->bDeferScriptGeneration || 'deferred' === $this->bDeferScriptGeneration)
		{
			if (0 < count($this->aCallableObjects))
			{
				$sCrLf = "\n";
				
				print $sCrLf;
				print '<';
				print 'script type="text/javascript" ';
				print $this->sDefer;
				print 'charset="UTF-8">';
				print $sCrLf;
				print '/* <';
				print '![CDATA[ */';
				print $sCrLf;

				foreach(array_keys($this->aCallableObjects) as $sKey)
					$this->aCallableObjects[$sKey]->generateClientScript($this->sXajaxPrefix);

				print '/* ]]> */';
				print $sCrLf;
				print '<';
				print '/script>';
				print $sCrLf;
			}
		}
	}

	/*
		Function: canProcessRequest
	*/
	function canProcessRequest()
	{
		if (NULL == $this->sRequestedClass)
			return false;
		if (NULL == $this->sRequestedMethod)
			return false;

		return true;
	}

	/*
		Function: processRequest
	*/
	function processRequest()
	{
		if (NULL == $this->sRequestedClass)
			return false;
		if (NULL == $this->sRequestedMethod)
			return false;

		$objArgumentManager =& xajaxArgumentManager::getInstance();
		$aArgs = $objArgumentManager->process();

		foreach (array_keys($this->aCallableObjects) as $sKey)
		{
			$xco =& $this->aCallableObjects[$sKey];

			if ($xco->isClass($this->sRequestedClass))
			{
				if ($xco->hasMethod($this->sRequestedMethod))
				{
					$xco->call($this->sRequestedMethod, $aArgs);
					return true;
				}
			}
		}

		return 'Invalid request for a callable object.';
	}
}

$objPluginManager =& xajaxPluginManager::getInstance();
$objPluginManager->registerPlugin(new xajaxCallableObjectPlugin(), 102);
