<?php
/*
	File: xajaxEventPlugin.inc.php

	Contains the xajaxEventPlugin class

	Title: xajaxEventPlugin class

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: xajaxEventPlugin.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Constant: XAJAX_EVENT
		Specifies that the item being registered via the <xajax->register> function
		is an event.
		
	Constant: XAJAX_EVENT_HANDLER
		Specifies that the item being registered via the <xajax->register> function
		is an event handler.
*/
if (!defined ('XAJAX_EVENT')) define ('XAJAX_EVENT', 'xajax event');
if (!defined ('XAJAX_EVENT_HANDLER')) define ('XAJAX_EVENT_HANDLER', 'xajax event handler');

//SkipAIO
require dirname(__FILE__) . '/support/xajaxEvent.inc.php';
//EndSkipAIO

/*
	Class: xajaxEventPlugin
	
	Plugin that adds server side event handling capabilities to xajax.  Events can
	be registered, then event handlers attached.
*/
class xajaxEventPlugin extends xajaxRequestPlugin
{
	/*
		Array: aEvents
	*/
	var $aEvents;

	/*
		String: sXajaxPrefix
	*/
	var $sXajaxPrefix;
	
	/*
		String: sEventPrefix
	*/
	var $sEventPrefix;

	/*
		String: sDefer
	*/
	var $sDefer;
	
	var $bDeferScriptGeneration;

	/*
		String: sRequestedEvent
	*/
	var $sRequestedEvent;

	/*
		Function: xajaxEventPlugin
	*/
	function xajaxEventPlugin()
	{
		$this->aEvents = array();

		$this->sXajaxPrefix = 'xajax_';
		$this->sEventPrefix = 'event_';
		$this->sDefer = '';
		$this->bDeferScriptGeneration = false;

		$this->sRequestedEvent = NULL;

		if (isset($_GET['xjxevt'])) $this->sRequestedEvent = $_GET['xjxevt'];
		if (isset($_POST['xjxevt'])) $this->sRequestedEvent = $_POST['xjxevt'];
	}

	/*
		Function: configure
	*/
	function configure($sName, $mValue)
	{
		if ('wrapperPrefix' == $sName) {
			$this->sXajaxPrefix = $mValue;
		} else if ('eventPrefix' == $sName) {
			$this->sEventPrefix = $mValue;
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

		$sType - (string): type of item being registered
		$sEvent - (string): the name of the event
		$ufHandler - (function name or reference): a reference to the user function to call
		$aConfiguration - (array): an array containing configuration options
	*/
	function register($aArgs)
	{
		if (1 < count($aArgs))
		{
			$sType = $aArgs[0];

			if (XAJAX_EVENT == $sType)
			{
				$sEvent = $aArgs[1];

				if (false === isset($this->aEvents[$sEvent]))
				{
					$xe =& new xajaxEvent($sEvent);

					if (2 < count($aArgs))
						if (is_array($aArgs[2]))
							foreach ($aArgs[2] as $sKey => $sValue)
								$xe->configure($sKey, $sValue);

					$this->aEvents[$sEvent] =& $xe;

					return $xe->generateRequest($this->sXajaxPrefix, $this->sEventPrefix);
				}
			}

			if (XAJAX_EVENT_HANDLER == $sType)
			{
				$sEvent = $aArgs[1];

				if (isset($this->aEvents[$sEvent]))
				{
					if (isset($aArgs[2]))
					{
						$xuf =& $aArgs[2];

						if (false === is_a($xuf, 'xajaxUserFunction'))
							$xuf =& new xajaxUserFunction($xuf);

						$objEvent =& $this->aEvents[$sEvent];
						$objEvent->addHandler($xuf);

						return true;
					}
				}
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
			if (0 < count($this->aEvents))
			{
				echo "\n<script type='text/javascript' ";
				echo $this->sDefer;
				echo "charset='UTF-8'>\n";
				echo "/* <![CDATA[ */\n";

				foreach (array_keys($this->aEvents) as $sKey)
					$this->aEvents[$sKey]->generateClientScript($this->sXajaxPrefix, $this->sEventPrefix);

				echo "/* ]]> */\n";
				echo "</script>\n";
			}
		}
	}

	/*
		Function: canProcessRequest
	*/
	function canProcessRequest()
	{
		if (NULL == $this->sRequestedEvent)
			return false;

		return true;
	}

	/*
		Function: processRequest
	*/
	function processRequest()
	{
		if (NULL == $this->sRequestedEvent)
			return false;

		$objArgumentManager =& xajaxArgumentManager::getInstance();
		$aArgs = $objArgumentManager->process();

		foreach (array_keys($this->aEvents) as $sKey)
		{
			$objEvent =& $this->aEvents[$sKey];

			if ($objEvent->getName() == $this->sRequestedEvent)
			{
				$objEvent->fire($aArgs);
				return true;
			}
		}

		return 'Invalid event request received; no event was registered with this name.';
	}
}

$objPluginManager =& xajaxPluginManager::getInstance();
$objPluginManager->registerPlugin(new xajaxEventPlugin(), 103);
