<?php
/*
	File: xajaxPluginManager.inc.php

	Contains the xajax plugin manager.
	
	Title: xajax plugin manager
	
	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: xajaxPluginManager.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

//SkipAIO
require(dirname(__FILE__) . '/xajaxPlugin.inc.php');
//EndSkipAIO

/*
	Class: xajaxPluginManager
*/
class xajaxPluginManager
{
	/*
		Array: aRequestPlugins
	*/
	var $aRequestPlugins;
	
	/*
		Array: aResponsePlugins
	*/
	var $aResponsePlugins;
	
	/*
		Array: aConfigurable
	*/
	var $aConfigurable;
	
	/*
		Array: aRegistrars
	*/
	var $aRegistrars;
	
	/*
		Array: aProcessors
	*/
	var $aProcessors;
	
	/*
		Array: aClientScriptGenerators
	*/
	var $aClientScriptGenerators;
	
	/*
		Function: xajaxPluginManager
		
		Construct and initialize the one and only xajax plugin manager.
	*/
	function xajaxPluginManager()
	{
		$this->aRequestPlugins = array();
		$this->aResponsePlugins = array();
		
		$this->aConfigurable = array();
		$this->aRegistrars = array();
		$this->aProcessors = array();
		$this->aClientScriptGenerators = array();
	}
	
	/*
		Function: getInstance
		
		Implementation of the singleton pattern: returns the one and only instance of the 
		xajax plugin manager.
		
		Returns:
		
		object - a reference to the one and only instance of the
			plugin manager.
	*/
	function &getInstance()
	{
		static $obj;
		if (!$obj) {
			$obj = new xajaxPluginManager();    
		}
		return $obj;
	}
	
	/*
		Function: loadPlugins
		
		Loads plugins from the folders specified.
	*/
	function loadPlugins($aFolders)
	{
		foreach ($aFolders as $sFolder) {
			if ($handle = opendir($sFolder)) {
				while (!(false === ($sName = readdir($handle)))) {
					$nLength = strlen($sName);
					if (8 < $nLength) {
						$sFileName = substr($sName, 0, $nLength - 8);
						$sExtension = substr($sName, $nLength - 8, 8);
						if ('.inc.php' == $sExtension) {
							require $sFolder . '/' . $sFileName . $sExtension;
						}
					}
				}
				
				closedir($handle);
			}
		}
	}
	
	/*
		Function: _insertIntoArray
		
		Inserts an entry into an array given the specified priority number. 
		If a plugin already exists with the given priority, the priority is
		automatically incremented until a free spot is found.  The plugin
		is then inserted into the empty spot in the array.
		
		nPriorityNumber - (number):  The desired priority, used to order
			the plugins.
	*/
	function _insertIntoArray(&$aPlugins, &$objPlugin, $nPriority)
	{
		while (isset($aPlugins[$nPriority]))
			$nPriority++;
		
		$aPlugins[$nPriority] =& $objPlugin;
	}
	
	/*
		Function: registerPlugin
		
		Registers a plugin.
		
		objPlugin - (object):  A reference to an instance of a plugin.
		
		Below is a table for priorities and their description:
		0 thru 999: Plugins that are part of or extensions to the xajax core
		1000 thru 8999: User created plugins, typically, these plugins don't care about order
		9000 thru 9999: Plugins that generally need to be last or near the end of the plugin list
	*/
	function registerPlugin(&$objPlugin, $nPriority=1000)
	{
		if (is_a($objPlugin, 'xajaxRequestPlugin'))
		{
			$this->_insertIntoArray($this->aRequestPlugins, $objPlugin, $nPriority);
			
			if (method_exists($objPlugin, 'register'))
				$this->_insertIntoArray($this->aRegistrars, $objPlugin, $nPriority);
			
			if (method_exists($objPlugin, 'canProcessRequest'))
				if (method_exists($objPlugin, 'processRequest'))
					$this->_insertIntoArray($this->aProcessors, $objPlugin, $nPriority);
		}
		else if (is_a($objPlugin, 'xajaxResponsePlugin'))
		{
			$this->aResponsePlugins[] =& $objPlugin;
		}
		else
		{
//SkipDebug
			$objLanguageManager =& xajaxLanguageManager::getInstance();
			trigger_error(
				$objLanguageManager->getText('XJXPM:IPLGERR:01') 
				. get_class($objPlugin) 
				. $objLanguageManager->getText('XJXPM:IPLGERR:02')
				, E_USER_ERROR
				);
//EndSkipDebug
		}
		
		if (method_exists($objPlugin, 'configure'))
			$this->_insertIntoArray($this->aConfigurable, $objPlugin, $nPriority);

		if (method_exists($objPlugin, 'generateClientScript'))
			$this->_insertIntoArray($this->aClientScriptGenerators, $objPlugin, $nPriority);
	}

	/*
		Function: canProcessRequest
		
		Calls each of the request plugins and determines if the
		current request can be processed by one of them.  If no processor identifies
		the current request, then the request must be for the initial page load.
		
		See <xajax->canProcessRequest> for more information.
	*/
	function canProcessRequest()
	{
		$bHandled = false;
		
		$aKeys = array_keys($this->aProcessors);
		sort($aKeys);
		foreach ($aKeys as $sKey) {
			$mResult = $this->aProcessors[$sKey]->canProcessRequest();
			if (true === $mResult)
				$bHandled = true;
			else if (is_string($mResult))
				return $mResult;
		}

		return $bHandled;
	}

	/*
		Function: processRequest
		
		Calls each of the request plugins to request that they process the
		current request.  If the plugin processes the request, it will
		return true.
	*/
	function processRequest()
	{
		$bHandled = false;
		
		$aKeys = array_keys($this->aProcessors);
		sort($aKeys);
		foreach ($aKeys as $sKey) {
			$mResult = $this->aProcessors[$sKey]->processRequest();
			if (true === $mResult)
				$bHandled = true;
			else if (is_string($mResult))
				return $mResult;
		}

		return $bHandled;
	}
	
	/*
		Function: configure
		
		Call each of the request plugins passing along the configuration
		setting specified.
		
		sName - (string):  The name of the configuration setting to set.
		mValue - (mixed):  The value to be set.
	*/
	function configure($sName, $mValue)
	{
		$aKeys = array_keys($this->aConfigurable);
		sort($aKeys);
		foreach ($aKeys as $sKey)
			$this->aConfigurable[$sKey]->configure($sName, $mValue);
	}
	
	/*
		Function: register
		
		Call each of the request plugins and give them the opportunity to 
		handle the registration of the specified function, event or callable object.
	*/
	function register($aArgs)
	{
		$aKeys = array_keys($this->aRegistrars);
		sort($aKeys);
		foreach ($aKeys as $sKey)
		{
			$objPlugin =& $this->aRegistrars[$sKey];
			$mResult =& $objPlugin->register($aArgs);
			if (is_a($mResult, 'xajaxRequest'))
				return $mResult;
			if (is_array($mResult))
				return $mResult;
			if (is_bool($mResult))
				if (true === $mResult)
					return true;
		}
//SkipDebug
		$objLanguageManager =& xajaxLanguageManager::getInstance();
		trigger_error(
			$objLanguageManager->getText('XJXPM:MRMERR:01') 
			. print_r($aArgs, true)
			, E_USER_ERROR
			);
//EndSkipDebug
	}
	
	/*
		Function: generateClientScript
		
		Call each of the request and response plugins giving them the
		opportunity to output some javascript to the page being generated.  This
		is called only when the page is being loaded initially.  This is not 
		called when processing a request.
	*/
	function generateClientScript()
	{
		$aKeys = array_keys($this->aClientScriptGenerators);
		sort($aKeys);
		foreach ($aKeys as $sKey)
			$this->aClientScriptGenerators[$sKey]->generateClientScript();
	}
	
	/*
		Function: getPlugin
		
		Locate the specified response plugin by name and return
		a reference to it if one exists.
	*/
	function &getPlugin($sName)
	{
		$aKeys = array_keys($this->aResponsePlugins);
		sort($aKeys);
		foreach ($aKeys as $sKey)
			if (is_a($this->aResponsePlugins[$sKey], $sName))
				return $this->aResponsePlugins[$sKey];

		$bFailure = false;
		return $bFailure;
	}
}
