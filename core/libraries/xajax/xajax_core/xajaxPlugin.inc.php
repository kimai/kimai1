<?php
/*
	File: xajaxPlugin.inc.php

	Contains the xajaxPlugin class

	Title: xajaxPlugin class

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: xajaxPlugin.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Class: xajaxPlugin
	
	The base class for all xajax plugins.
*/
class xajaxPlugin
{
}

/*
	Class: xajaxRequestPlugin
	
	The base class for all xajax request plugins.
	
	Request plugins handle the registration, client script generation and processing of
	xajax enabled requests.  Each plugin should have a unique signature for both
	the registration and processing of requests.  During registration, the user will
	specify a type which will allow the plugin to detect and handle it.  During client
	script generation, the plugin will generate a <xajax.request> stub with the
	prescribed call options and request signature.  During request processing, the
	plugin will detect the signature generated previously and process the request
	accordingly.
*/
class xajaxRequestPlugin extends xajaxPlugin
{
	/*
		Function: configure
		
		Called by the <xajaxPluginManager> when a configuration setting is changing.
		Plugins should store a local copy of the settings they wish to use during 
		registration, client script generation or request processing.
	*/
	function configure($sName, $mValue)
	{
	}
	
	/*
		Function: register
		
		Called by the <xajaxPluginManager> when a user script when a function, event 
		or callable object is to be registered.  Additional plugins may support other 
		registration types.
	*/
	function register($aArgs)
	{
		return false;
	}
	
	/*
		Function: generateClientScript
		
		Called by <xajaxPluginManager> when the page's HTML is being sent to the browser.
		This allows each plugin to inject some script / style or other appropriate tags
		into the HEAD of the document.  Each block must be appropriately enclosed, meaning
		javascript code must be enclosed in SCRIPT and /SCRIPT tags.
	*/
	function generateClientScript()
	{
	}
	
	/*
		Function: canProcessRequest
		
		Called by the <xajaxPluginManager> when a request has been received to determine
		if the request is for a xajax enabled function or for the initial page load.
	*/
	function canProcessRequest()
	{
		return false;
	}
	
	/*
		Function: processRequest
		
		Called by the <xajaxPluginManager> when a request is being processed.  This 
		will only occur when <xajax> has determined that the current request is a valid
		(registered) xajax enabled function via <xajax->canProcessRequest>.
	*/
	function processRequest()
	{
		return false;
	}
}

/*
	Class: xajaxResponsePlugin
	
	Base class for all xajax response plugins.
	
	A response plugin provides additional services not already provided by the 
	<xajaxResponse> class with regard to sending response commands to the
	client.  In addition, a response command may send javascript to the browser
	at page load to aid in the processing of it's response commands.
*/
class xajaxResponsePlugin extends xajaxPlugin
{
	/*
		Object: objResponse
		
		A reference to the current <xajaxResponse> object that is being used
		to build the response that will be sent to the client browser.
	*/
	var $objResponse;
	
	/*
		Function: setResponse
		
		Called by the <xajaxResponse> object that is currently being used
		to build the response that will be sent to the client browser.
		
		objResponse - (object):  A reference to the <xajaxResponse> object
	*/
	function setResponse(&$objResponse)
	{
		$this->objResponse =& $objResponse;
	}
	
	/*
		Function: addCommand
		
		Used internally to add a command to the response command list.  This
		will call <xajaxResponse->addPluginCommand> using the reference provided
		in <xajaxResponsePlugin->setResponse>.
	*/
 	function addCommand($aAttributes, $sData)
 	{
 		$this->objResponse->addPluginCommand($this, $aAttributes, $sData);
 	}
	
	/*
		Function: getName
		
		Called by the <xajaxPluginManager> when the user script requests a plugin.
		This name must match the plugin name requested in the called to 
		<xajaxResponse->plugin>.
	*/
	function getName()
	{
//SkipDebug
		$objLanguageManager =& xajaxLanguageManager::getInstance();
		trigger_error(
			$objLanguageManager->getText('XJXPLG:GNERR:01')
			, E_USER_ERROR
			);
//EndSkipDebug
	}
	
	/*
		Function: process
		
		Called by <xajaxResponse> when a user script requests the service of a
		response plugin.  The parameters provided by the user will be used to
		determine which response command and parameters will be sent to the
		client upon completion of the xajax request process.
	*/
	function process()
	{
//SkipDebug
		$objLanguageManager =& xajaxLanguageManager::getInstance();
		trigger_error(
			$objLanguageManager->getText('XJXPLG:PERR:01')
			, E_USER_ERROR
			);
//EndSkipDebug
	}
}
