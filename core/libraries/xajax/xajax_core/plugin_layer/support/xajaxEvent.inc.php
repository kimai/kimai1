<?php

/*
	File: xajaxEvent.inc.php

	Definition of the xajax Event object.

	Title: xajaxEvent

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: xajaxEvent.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

// require_once is necessary here as the function plugin also includes this
//SkipAIO
require_once dirname(__FILE__) . '/xajaxUserFunction.inc.php';
//EndSkipAIO

/*
	Class: xajaxEvent
	
	A container class which holds a reference to handler functions and configuration
	options associated with a registered event.
*/
class xajaxEvent
{
	/*
		String: sName
		
		The name of the event.
	*/
	var $sName;
	
	/*
		Array: aConfiguration
		
		Configuration / call options to be used when initiating a xajax request
		to trigger this event.
	*/
	var $aConfiguration;
	
	/*
		Array: aHandlers
		
		A list of <xajaxUserFunction> objects associated with this registered
		event.  Each of these functions will be called when the event is triggered.
	*/
	var $aHandlers;
	
	/*
		Function: xajaxEvent
		
		Construct and initialize this <xajaxEvent> object.
	*/
	function xajaxEvent($sName)
	{
		$this->sName = $sName;
		$this->aConfiguration = array();
		$this->aHandlers = array();
	}
	
	/*
		Function: getName
		
		Returns the name of the event.
		
		Returns:
		
		string - the name of the event.
	*/
	function getName()
	{
		return $this->sName;
	}
	
	/*
		Function: configure
		
		Sets/stores configuration options that will be used when generating
		the client script that is sent to the browser.
	*/
	function configure($sName, $mValue)
	{
		$this->aConfiguration[$sName] = $mValue;
	}
	
	/*
		Function: addHandler
		
		Adds a <xajaxUserFunction> object to the list of handlers that will
		be fired when the event is triggered.
	*/
	function addHandler(&$xuf)
	{
		$this->aHandlers[] =& $xuf;
	}
	
	/*
		Function: generateRequest
		
		Generates a <xajaxRequest> object that corresponds to the
		event so that the client script can easily invoke this event.
		
		sXajaxPrefix - (string):  The prefix that will be prepended to
			the client script stub function associated with this event.
			
		sEventPrefix - (string):  The prefix prepended to the client script
			function stub and <xajaxRequest> script.
	*/
	function generateRequest($sXajaxPrefix, $sEventPrefix)
	{
		$sEvent = $this->sName;
		return new xajaxRequest("{$sXajaxPrefix}{$sEventPrefix}{$sEvent}");
	}
 	
 	/*
 		Function: generateClientScript
 		
 		Generates a block of javascript code that declares a stub function
 		that can be used to easily trigger the event from the browser.
 	*/
 	function generateClientScript($sXajaxPrefix, $sEventPrefix)
	{
		$sMode = '';
		$sMethod = '';
		
		if (isset($this->aConfiguration['mode']))
			$sMode = $this->aConfiguration['mode'];
			
		if (isset($this->aConfiguration['method']))
			$sMethod = $this->aConfiguration['method'];
			
		if (0 < strlen($sMode))
			$sMode = ", mode: '{$sMode}'";
		
		if (0 < strlen($sMethod))
			$sMethod = ", method: '{$sMethod}'";
		
		$sEvent = $this->sName;
		echo "{$sXajaxPrefix}{$sEventPrefix}{$sEvent} = function() { return xajax.request( { xjxevt: '{$sEvent}' }, { parameters: arguments{$sMode}{$sMethod} } ); };\n";
	}
	
	/*
		Function: fire
		
		Called by the <xajaxEventPlugin> when the event has been triggered.
	*/
	function fire($aArgs)
	{
		$objResponseManager =& xajaxResponseManager::getInstance();
		
		foreach (array_keys($this->aHandlers) as $sKey)
			$this->aHandlers[$sKey]->call($aArgs);
	}
}
