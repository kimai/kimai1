<?php
/*
	File: xajaxControl.inc.php

	Contains the base class for all controls.

	Title: xajaxControl class

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: xajaxControl.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Constant: XAJAX_HTML_CONTROL_DOCTYPE_FORMAT
	
	Defines the doctype of the current document; this will effect how the HTML is formatted
	when the html control library is used to construct html documents and fragments.  This can
	be one of the following values:
	
	'XHTML' - (default)  Typical effects are that certain elements are closed with '/>'
	'HTML' - Typical differences are that closing tags for certain elements cannot be '/>'
*/
if (false == defined('XAJAX_HTML_CONTROL_DOCTYPE_FORMAT')) define('XAJAX_HTML_CONTROL_DOCTYPE_FORMAT', 'XHTML');

/*
	Constant: XAJAX_HTML_CONTROL_DOCTYPE_VERSION
*/
if (false == defined('XAJAX_HTML_CONTROL_DOCTYPE_VERSION')) define('XAJAX_HTML_CONTROL_DOCTYPE_VERSION', '1.0');

/*
	Constant: XAJAX_HTML_CONTROL_DOCTYPE_VALIDATION
*/
if (false == defined('XAJAX_HTML_CONTROL_DOCTYPE_VALIDATION')) define('XAJAX_HTML_CONTROL_DOCTYPE_VALIDATION', 'TRANSITIONAL');

/*
	Class: xajaxControl

	The base class for all xajax enabled controls.  Derived classes will generate the
	HTML and javascript code that will be sent to the browser via <xajaxControl->printHTML>
	or sent to the browser in a <xajaxResponse> via <xajaxControl->getHTML>.
*/
class xajaxControl
{
	/*
		String: sTag
	*/
	var $sTag;
	
	/*
		Boolean: sEndTag
		
		'required' - (default) Indicates the control must have a full end tag
		'optional' - The control may have an abbr. begin tag or a full end tag
		'forbidden' - The control must have an abbr. begin tag and no end tag
	*/
	var $sEndTag;
	
	/*
		Array: aAttributes
		
		An associative array of attributes that will be used in the generation
		of the HMTL code for this control.
	*/
	var $aAttributes;
	
	/*
		Array: aEvents
		
		An associative array of events that will be assigned to this control.  Each
		event declaration will include a reference to a <xajaxRequest> object; it's
		script will be extracted using <xajaxRequest->printScript> or 
		<xajaxRequest->getScript>.
	*/
	var $aEvents;
	
	/*
		String: sClass
		
		Contains a declaration of the class of this control.  %inline controls do not 
		need to be indented, %block controls should be indented.
	*/
	var $sClass;

	/*
		Function: xajaxControl
		
		$aConfiguration - (array):  An associative array that contains a variety
			of configuration options for this <xajaxControl> object.
			
		This array may contain the following entries:
		
		'attributes' - (array):  An associative array containing attributes
			that will be passed to the <xajaxControl->setAttribute> function.
		
		'children' - (array):  An array of <xajaxControl> derived objects that
			will be the children of this control.
	*/
	function xajaxControl($sTag, $aConfiguration=array())
	{
		$this->sTag = $sTag;

		$this->clearAttributes();
				
		if (isset($aConfiguration['attributes']))
			if (is_array($aConfiguration['attributes']))
				foreach ($aConfiguration['attributes'] as $sKey => $sValue)
					$this->setAttribute($sKey, $sValue);

		$this->clearEvents();
		
		if (isset($aConfiguration['event']))
			call_user_func_array(array(&$this, 'setEvent'), $aConfiguration['event']);
		
		else if (isset($aConfiguration['events']))
			if (is_array($aConfiguration['events']))
				foreach ($aConfiguration['events'] as $aEvent)
					call_user_func_array(array(&$this, 'setEvent'), $aEvent);
		
		$this->sClass = '%block';
		$this->sEndTag = 'forbidden';
	}
	
	/*
		Function: getClass
		
		Returns the *adjusted* class of the element
	*/
	function getClass()
	{
		return $this->sClass;
	}

	/*
		Function: clearAttributes
		
		Removes all attributes assigned to this control.
	*/
	function clearAttributes()
	{
		$this->aAttributes = array();
	}

	/*
		Function: setAttribute
		
		Call to set various control specific attributes to be included in the HTML
		script that is returned when <xajaxControl->printHTML> or <xajaxControl->getHTML>
		is called.
	*/
	function setAttribute($sName, $sValue)
	{
//SkipDebug
		if (class_exists('clsValidator'))
		{
			$objValidator =& clsValidator::getInstance();
			if (false == $objValidator->attributeValid($this->sTag, $sName)) {
				$objLanguageManager =& xajaxLanguageManager::getInstance();
				trigger_error(
					$objLanguageManager->getText('XJXCTL:IAERR:01') 
					. $sName 
					. $objLanguageManager->getText('XJXCTL:IAERR:02') 
					. $this->sTag 
					. $objLanguageManager->getText('XJXCTL:IAERR:03')
					, E_USER_ERROR
					);
			}
		}
//EndSkipDebug

		$this->aAttributes[$sName] = $sValue;
	}
	
	/*
		Function: getAttribute
		
		Call to obtain the value currently associated with the specified attribute
		if set.
		
		sName - (string): The name of the attribute to be returned.
		
		Returns:
		
		mixed - The value associated with the attribute, or null.
	*/
	function getAttribute($sName)
	{
		if (false == isset($this->aAttributes[$sName]))
			return null;
		
		return $this->aAttributes[$sName];
	}
	
	/*
		Function: clearEvents
		
		Clear the events that have been associated with this object.
	*/
	function clearEvents()
	{
		$this->aEvents = array();
	}

	/*
		Function: setEvent
		
		Call this function to assign a <xajaxRequest> object as the handler for
		the specific DOM event.  The <xajaxRequest->printScript> function will 
		be called to generate the javascript for this request.
		
		sEvent - (string):  A string containing the name of the event to be assigned.
		objRequest - (xajaxRequest object):  The <xajaxRequest> object to be associated
			with the specified event.
		aParameters - (array, optional):  An array containing parameter declarations
			that will be passed to this <xajaxRequest> object just before the javascript
			is generated.
		sBeforeRequest - (string, optional):  a string containing a snippet of javascript code
			to execute prior to calling the xajaxRequest function
		sAfterRequest - (string, optional):  a string containing a snippet of javascript code
			to execute after calling the xajaxRequest function
	*/
	function setEvent($sEvent, &$objRequest, $aParameters=array(), $sBeforeRequest='', $sAfterRequest='; return false;')
	{
//SkipDebug
		if (false == is_a($objRequest, 'xajaxRequest')) {
			$objLanguageManager =& xajaxLanguageManager::getInstance();
			trigger_error(
				$objLanguageManager->getText('XJXCTL:IRERR:01')
				. $this->backtrace()
				, E_USER_ERROR
				);
		}

		if (class_exists('clsValidator')) {
			$objValidator =& clsValidator::getInstance();
			if (false == $objValidator->attributeValid($this->sTag, $sEvent)) {
				$objLanguageManager =& xajaxLanguageManager::getInstance();
				trigger_error(
					$objLanguageManager->getText('XJXCTL:IEERR:01') 
					. $sEvent 
					. $objLanguageManager->getText('XJXCTL:IEERR:02') 
					. $this->sTag 
					. $objLanguageManager->getText('XJXCTL:IEERR:03')
					, E_USER_ERROR
					);
			}
		}
//EndSkipDebug

		$this->aEvents[$sEvent] = array(
			&$objRequest, 
			$aParameters, 
			$sBeforeRequest, 
			$sAfterRequest
			);
	}

	/*
		Function: getHTML
		
		Generates and returns the HTML representation of this control and 
		it's children.
		
		Returns:
		
		string - The HTML representation of this control.
	*/
	function getHTML($bFormat=false)
	{
		ob_start();
		if ($bFormat)
			$this->printHTML();
		else
			$this->printHTML(false);
		return ob_get_clean();
	}
	
	/*
		Function: printHTML
		
		Generates and prints the HTML representation of this control and 
		it's children.
		
		Returns:
		
		string - The HTML representation of this control.
	*/
	function printHTML($sIndent='')
	{
//SkipDebug
		if (class_exists('clsValidator'))
		{
			$objValidator =& clsValidator::getInstance();
			$sMissing = '';
			if (false == $objValidator->checkRequiredAttributes($this->sTag, $this->aAttributes, $sMissing)) {
				$objLanguageManager =& xajaxLanguageManager::getInstance();
				trigger_error(
					$objLanguageManager->getText('XJXCTL:MAERR:01') 
					. $sMissing
					. $objLanguageManager->getText('XJXCTL:MAERR:02') 
					. $this->sTag 
					. $objLanguageManager->getText('XJXCTL:MAERR:03')
					, E_USER_ERROR
					);
			}
		}
//EndSkipDebug

		$sClass = $this->getClass();
		
		if ('%inline' != $sClass)
			// this odd syntax is necessary to detect request for no formatting
			if (false === (false === $sIndent))
				echo $sIndent;
			
		echo '<';
		echo $this->sTag;
		echo ' ';
		$this->_printAttributes();
		$this->_printEvents();
		
		if ('forbidden' == $this->sEndTag)
		{
			if ('HTML' == XAJAX_HTML_CONTROL_DOCTYPE_FORMAT)
				echo '>';
			else if ('XHTML' == XAJAX_HTML_CONTROL_DOCTYPE_FORMAT)
				echo '/>';
			
			if ('%inline' != $sClass)
				// this odd syntax is necessary to detect request for no formatting
				if (false === (false === $sIndent))
					echo "\n";
				
			return;
		}
		else if ('optional' == $this->sEndTag)
		{
			echo '/>';
			
			if ('%inline' == $sClass)
				// this odd syntax is necessary to detect request for no formatting
				if (false === (false === $sIndent))
					echo "\n";
				
			return;
		}
//SkipDebug
		else
		{
			$objLanguageManager =& xajaxLanguageManager::getInstance();
			trigger_error(
				$objLanguageManager->getText('XJXCTL:IETERR:01')
				. $this->backtrace()
				, E_USER_ERROR
				);
		}
//EndSkipDebug
	}

	function _printAttributes()
	{
		// NOTE: Special case here: disabled='false' does not work in HTML; does work in javascript
		foreach ($this->aAttributes as $sKey => $sValue)
			if ('disabled' != $sKey || 'false' != $sValue)
				echo "{$sKey}='{$sValue}' ";
	}

	function _printEvents()
	{
		foreach (array_keys($this->aEvents) as $sKey)
		{
			$aEvent =& $this->aEvents[$sKey];
			$objRequest =& $aEvent[0];
			$aParameters = $aEvent[1];
			$sBeforeRequest = $aEvent[2];
			$sAfterRequest = $aEvent[3];

			foreach ($aParameters as $aParameter)
			{
				$nParameter = $aParameter[0];
				$sType = $aParameter[1];
				$sValue = $aParameter[2];
				$objRequest->setParameter($nParameter, $sType, $sValue);
			}

			$objRequest->useDoubleQuote();

			echo "{$sKey}='{$sBeforeRequest}";

			$objRequest->printScript();

			echo "{$sAfterRequest}' ";
		}
	}

	function backtrace()
	{
		// debug_backtrace was added to php in version 4.3.0
		// version_compare was added to php in version 4.0.7
		if (0 <= version_compare(PHP_VERSION, '4.3.0'))
			return '<div><div>Backtrace:</div><pre>' 
				. print_r(debug_backtrace(), true) 
				. '</pre></div>';
		return '';
	}
}

/*
	Class: xajaxControlContainer
	
	This class is used as the base class for controls that will contain
	other child controls.
*/
class xajaxControlContainer extends xajaxControl
{
	/*
		Array: aChildren
		
		An array of child controls.
	*/
	var $aChildren;

	/*
		Boolean: sChildClass
		
		Will contain '%inline' if all children are class = '%inline', '%block' if all children are '%block' or
		'%flow' if both '%inline' and '%block' elements are detected.
	*/
	var $sChildClass;

	/*
		Function: xajaxControlContainer
		
		Called to construct and configure this control.
		
		aConfiguration - (array):  See <xajaxControl->xajaxControl> for more
			information.
	*/
	function xajaxControlContainer($sTag, $aConfiguration=array())
	{
		xajaxControl::xajaxControl($sTag, $aConfiguration);

		$this->clearChildren();
		
		if (isset($aConfiguration['child']))
			$this->addChild($aConfiguration['child']);

		else if (isset($aConfiguration['children']))
			$this->addChildren($aConfiguration['children']);
		
		$this->sEndTag = 'required';
	}
	
	/*
		Function: getClass
		
		Returns the *adjusted* class of the element
	*/
	function getClass()
	{
		$sClass = xajaxControl::getClass();
		
		if (0 < count($this->aChildren) && '%flow' == $sClass)
			return $this->getContentClass();
		else if (0 == count($this->aChildren) || '%inline' == $sClass || '%block' == $sClass)
			return $sClass;
		
		$objLanguageManager =& xajaxLanguageManager::getInstance();
		trigger_error(
			$objLanguageManager->getText('XJXCTL:ICERR:01')
			. $this->backtrace()
			, E_USER_ERROR
			);
	}
	
	/*
		Function: getContentClass
		
		Returns the *adjusted* class of the content (children) of this element
	*/
	function getContentClass()
	{
		$sClass = '';
		
		foreach (array_keys($this->aChildren) as $sKey)
		{
			if ('' == $sClass)
				$sClass = $this->aChildren[$sKey]->getClass();
			else if ($sClass != $this->aChildren[$sKey]->getClass())
				return '%flow';
		}
		
		if ('' == $sClass)
			return '%inline';
			
		return $sClass;
	}
	
	/*
		Function: clearChildren
		
		Clears the list of child controls associated with this control.
	*/
	function clearChildren()
	{
		$this->sChildClass = '%inline';
		$this->aChildren = array();
	}

	/*
		Function: addChild
		
		Adds a control to the array of child controls.  Child controls
		must be derived from <xajaxControl>.
	*/
	function addChild(&$objControl)
	{
//SkipDebug
		if (false == is_a($objControl, 'xajaxControl')) {
			$objLanguageManager =& xajaxLanguageManager::getInstance();
			trigger_error(
				$objLanguageManager->getText('XJXCTL:ICLERR:01')
				. $this->backtrace()
				, E_USER_ERROR
				);
		}

		if (class_exists('clsValidator'))
		{
			$objValidator =& clsValidator::getInstance();
			if (false == $objValidator->childValid($this->sTag, $objControl->sTag)) {
				$objLanguageManager =& xajaxLanguageManager::getInstance();
				trigger_error(
					$objLanguageManager->getText('XJXCTL:ICLERR:02') 
					. $objControl->sTag
					. $objLanguageManager->getText('XJXCTL:ICLERR:03') 
					. $this->sTag 
					. $objLanguageManager->getText('XJXCTL:ICLERR:04')
					. $this->backtrace()
					, E_USER_ERROR
					);
			}
		}
//EndSkipDebug

		$this->aChildren[] =& $objControl;
	}
	
	function addChildren(&$aChildren)
	{
//SkipDebug
		if (false == is_array($aChildren)) {
			$objLanguageManager =& xajaxLanguageManager::getInstance();
			trigger_error(
				$objLanguageManager->getText('XJXCTL:ICHERR:01')
				. $this->backtrace()
				, E_USER_ERROR
				);
		}
//EndSkipDebug
				
		foreach (array_keys($aChildren) as $sKey)
			$this->addChild($aChildren[$sKey]);
	}

	function printHTML($sIndent='')
	{
//SkipDebug
		if (class_exists('clsValidator'))
		{
			$objValidator =& clsValidator::getInstance();
			$sMissing = '';
			if (false == $objValidator->checkRequiredAttributes($this->sTag, $this->aAttributes, $sMissing)) {
				$objLanguageManager =& xajaxLanguageManager::getInstance();
				trigger_error(
					$objLanguageManager->getText('XJXCTL:MRAERR:01') 
					. $sMissing
					. $objLanguageManager->getText('XJXCTL:MRAERR:02') 
					. $this->sTag 
					. $objLanguageManager->getText('XJXCTL:MRAERR:03')
					, E_USER_ERROR
					);
			}
		}
//EndSkipDebug

		$sClass = $this->getClass();
		
		if ('%inline' != $sClass)
			// this odd syntax is necessary to detect request for no formatting
			if (false === (false === $sIndent))
				echo $sIndent;
			
		echo '<';
		echo $this->sTag;
		echo ' ';
		$this->_printAttributes();
		$this->_printEvents();
		
		if (0 == count($this->aChildren))
		{
			if ('optional' == $this->sEndTag)
			{
				echo '/>';
				
				if ('%inline' != $sClass)
					// this odd syntax is necessary to detect request for no formatting
					if (false === (false === $sIndent))
						echo "\n";
					
				return;
			}
//SkipDebug
			else if ('required' != $this->sEndTag)
				trigger_error("Invalid end tag designation; should be optional or required.\n"
					. $this->backtrace(),
					E_USER_ERROR
					);
//EndSkipDebug
		}
		
		echo '>';
		
		$sContentClass = $this->getContentClass();
		
		if ('%inline' != $sContentClass)
			// this odd syntax is necessary to detect request for no formatting
			if (false === (false === $sIndent))
				echo "\n";

		$this->_printChildren($sIndent);
		
		if ('%inline' != $sContentClass)
			// this odd syntax is necessary to detect request for no formatting
			if (false === (false === $sIndent))
				echo $sIndent;
		
		echo '<' . '/';
		echo $this->sTag;
		echo '>';
		
		if ('%inline' != $sClass)
			// this odd syntax is necessary to detect request for no formatting
			if (false === (false === $sIndent))
				echo "\n";
	}

	function _printChildren($sIndent='')
	{
		if (false == is_a($this, 'clsDocument'))
			// this odd syntax is necessary to detect request for no formatting
			if (false === (false === $sIndent))
				$sIndent .= "\t";

		// children
		foreach (array_keys($this->aChildren) as $sKey)
		{
			$objChild =& $this->aChildren[$sKey];
			$objChild->printHTML($sIndent);
		}
	}
}
