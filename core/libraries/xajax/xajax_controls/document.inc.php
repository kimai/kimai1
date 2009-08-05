<?php
/*
	File: document.inc.php

	HTML Control Library - Document Level Tags

	Title: xajax HTML control class library

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: document.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Section: Description
	
	This file contains the class declarations for the following HTML Controls:
	
	- document, doctype, html, head, body
	- meta, link, script, style
	- title, base
	- noscript
	- frameset, frame, iframe, noframes
	
	The following controls are deprecated as of HTML 4.01, so they will not be supported:

	- basefont
*/

class clsDocument extends xajaxControlContainer
{
	function clsDocument($aConfiguration=array())
	{
		if (isset($aConfiguration['attributes']))
			trigger_error(
				'clsDocument objects cannot have attributes.'
				. $this->backtrace(),
				E_USER_ERROR);
		
		xajaxControlContainer::xajaxControlContainer('DOCUMENT', $aConfiguration);

		$this->sClass = '%block';
	}

	function printHTML()
	{
		$tStart = microtime();
		$this->_printChildren();
		$tStop = microtime();
		echo '<' . '!--';
		echo ' page generation took ';
		$nTime = $tStop - $tStart;
		$nTime *= 1000;
		echo $nTime;
		echo ' --' . '>';
	}
}


class clsDoctype extends xajaxControlContainer
{
	var $sText;
	
	var $sFormat;
	var $sVersion;
	var $sValidation;
	var $sEncoding;
	
	function clsDocType($sFormat=null, $sVersion=null, $sValidation=null, $sEncoding='UTF-8')
	{
		if (null === $sFormat && false == defined('XAJAX_HTML_CONTROL_DOCTYPE_FORMAT'))
			trigger_error('You must specify a doctype format.', E_USER_ERROR);
		if (null === $sVersion && false == defined('XAJAX_HTML_CONTROL_DOCTYPE_VERSION'))
			trigger_error('You must specify a doctype version.', E_USER_ERROR);
		if (null === $sValidation && false == defined('XAJAX_HTML_CONTROL_DOCTYPE_VALIDATION'))
			trigger_error('You must specify a doctype validation.', E_USER_ERROR);
			
		if (null === $sFormat)
			$sFormat = XAJAX_HTML_CONTROL_DOCTYPE_FORMAT;
		if (null === $sVersion)
			$sVersion = XAJAX_HTML_CONTROL_DOCTYPE_VERSION;
		if (null === $sValidation)
			$sValidation = XAJAX_HTML_CONTROL_DOCTYPE_VALIDATION;
			
		xajaxControlContainer::xajaxControlContainer('DOCTYPE', array());
		
		$this->sText = '<'.'!DOCTYPE html PUBLIC "-//W3C//DTD ';
		$this->sText .= $sFormat;
		$this->sText .= ' ';
		$this->sText .= $sVersion;
		if ('TRANSITIONAL' == $sValidation)
			$this->sText .= ' Transitional';
		else if ('FRAMESET' == $sValidation)
			$this->sText .= ' Frameset';
		$this->sText .= '//EN" ';
		
		if ('HTML' == $sFormat) {
			if ('4.0' == $sVersion) {
				if ('STRICT' == $sValidation)
					$this->sText .= '"http://www.w3.org/TR/html40/strict.dtd"';
				else if ('TRANSITIONAL' == $sValidation)
					$this->sText .= '"http://www.w3.org/TR/html40/loose.dtd"';
			} else if ('4.01' == $sVersion) {
				if ('STRICT' == $sValidation)
					$this->sText .= '"http://www.w3.org/TR/html401/strict.dtd"';
				else if ('TRANSITIONAL' == $sValidation)
					$this->sText .= '"http://www.w3.org/TR/html401/loose.dtd"';
				else if ('FRAMESET' == $sValidation)
					$this->sText .= '"http://www.w3.org/TR/html4/frameset.dtd"';
			}
		} else if ('XHTML' == $sFormat) {
			if ('1.0' == $sVersion) {
				if ('STRICT' == $sValidation)
					$this->sText .= '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"';
				else if ('TRANSITIONAL' == $sValidation)
					$this->sText .= '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"';
			} else if ('1.1' == $sVersion) {
				$this->sText .= '"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"';
			}
		} else
			trigger_error('Unsupported DOCTYPE tag.'
				. $this->backtrace(),
				E_USER_ERROR
				);
		
		$this->sText .= '>';
		
		$this->sFormat = $sFormat;
		$this->sVersion = $sVersion;
		$this->sValidation = $sValidation;
		$this->sEncoding = $sEncoding;
	}
	
	function printHTML($sIndent='')
	{
		header('content-type: text/html; charset=' . $this->sEncoding);
		
		if ('XHTML' == $this->sFormat)
			print '<' . '?' . 'xml version="1.0" encoding="' . $this->sEncoding . '" ' . '?' . ">\n";
			
		print $this->sText;
		
		print "\n";
		
		xajaxControlContainer::_printChildren($sIndent);
	}
}

class clsHtml extends xajaxControlContainer
{
	function clsHtml($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('html', $aConfiguration);

		$this->sClass = '%block';
		$this->sEndTag = 'optional';
	}
}

class clsHead extends xajaxControlContainer
{
	var $objXajax;
	
	function clsHead($aConfiguration=array())
	{
		$this->objXajax = null;
		if (isset($aConfiguration['xajax']))
			$this->setXajax($aConfiguration['xajax']);
			
		xajaxControlContainer::xajaxControlContainer('head', $aConfiguration);

		$this->sClass = '%block';
		$this->sEndTag = 'optional';
	}
	
	function setXajax(&$objXajax)
	{
		$this->objXajax =& $objXajax;
	}

	function _printChildren($sIndent='')
	{
		if (null != $this->objXajax)
			$this->objXajax->printJavascript();
		
		xajaxControlContainer::_printChildren($sIndent);
	}
}

class clsBody extends xajaxControlContainer
{
	function clsBody($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('body', $aConfiguration);
		
		$this->sClass = '%block';
		$this->sEndTag = 'optional';
	}
}

class clsScript extends xajaxControlContainer
{
	function clsScript($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('script', $aConfiguration);

		$this->sClass = '%block';
	}
}

class clsStyle extends xajaxControlContainer
{
	function clsStyle($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('style', $aConfiguration);

		$this->sClass = '%block';
	}
}

class clsLink extends xajaxControl
{
	function clsLink($aConfiguration=array())
	{
		xajaxControl::xajaxControl('link', $aConfiguration);

		$this->sClass = '%block';
	}
}

class clsMeta extends xajaxControl
{
	function clsMeta($aConfiguration=array())
	{
		xajaxControl::xajaxControl('meta', $aConfiguration);

		$this->sClass = '%block';
	}
}

class clsTitle extends xajaxControlContainer
{
	function clsTitle($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('title', $aConfiguration);

		$this->sClass = '%block';
	}

	function setEvent($sEvent, &$objRequest)
	{
		trigger_error(
			'clsTitle objects do not support events.'
			. $this->backtrace(),
			E_USER_ERROR);
	}
}

class clsBase extends xajaxControl
{
	function clsBase($aConfiguration=array())
	{
		xajaxControl::xajaxControl('base', $aConfiguration);

		$this->sClass = '%block';
	}
}

class clsNoscript extends xajaxControlContainer
{
	function clsNoscript($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('noscript', $aConfiguration);

		$this->sClass = '%flow';
	}
}

class clsIframe extends xajaxControlContainer
{
	function clsIframe($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('iframe', $aConfiguration);

		$this->sClass = '%block';
	}
}

class clsFrameset extends xajaxControlContainer
{
	function clsFrameset($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('frameset', $aConfiguration);

		$this->sClass = '%block';
	}
}

class clsFrame extends xajaxControl
{
	function clsFrame($aConfiguration=array())
	{
		xajaxControl::xajaxControl('frame', $aConfiguration);

		$this->sClass = '%block';
	}
}

class clsNoframes extends xajaxControlContainer
{
	function clsNoframes($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('noframes', $aConfiguration);

		$this->sClass = '%flow';
	}
}
