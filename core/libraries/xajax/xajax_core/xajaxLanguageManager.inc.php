<?php
/*
	File: xajaxLanguageManager.inc.php

	Contains the code that manages the inclusion of alternate language support
	files; so debug and error messages can be shown in a language other than
	the default (english) language.

	Title: xajaxLanguageManager class

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: xajaxLanguageManager.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Class: xajaxLanguageManager
	
	This class contains the default language (english) and the code used to supply 
	debug and error messages upon request; as well as the code used to load alternate
	language text as requested via the <xajax::configure> function.
*/
class xajaxLanguageManager
{
	/*
		Array: aMessages
		
		An array of the currently registered languages.
	*/
	var $aMessages;
	
	/*
		String: sLanguage
		
		The currently configured language.
	*/
	var $sLanguage;
	
	/*
		Function: xajaxLanguageManager
		
		Construct and initialize the one and only xajax language manager object.
	*/
	function xajaxLanguageManager()
	{
		$this->aMessages = array();
		
		$this->aMessages['en'] = array(
			'LOGHDR:01' => '** xajax Error Log - ',
			'LOGHDR:02' => " **\n",
			'LOGHDR:03' => "\n\n\n",
			'LOGERR:01' => "** Logging Error **\n\nxajax was unable to write to the error log file:\n",
			'LOGMSG:01' => "** PHP Error Messages: **",
			'CMPRSJS:RDERR:01' => 'The xajax uncompressed Javascript file could not be found in the <b>',
			'CMPRSJS:RDERR:02' => '</b> folder.  Error ',
			'CMPRSJS:WTERR:01' => 'The xajax compressed javascript file could not be written in the <b>',
			'CMPRSJS:WTERR:02' => '</b> folder.  Error ',
			'CMPRSPHP:WTERR:01' => 'The xajax compressed file <b>',
			'CMPRSPHP:WTERR:02' => '</b> could not be written to.  Error ',
			'CMPRSAIO:WTERR:01' => 'The xajax compressed file <b>',
			'CMPRSAIO:WTERR:02' => '/xajaxAIO.inc.php</b> could not be written to.  Error ',
			'DTCTURI:01' => 'xajax Error: xajax failed to automatically identify your Request URI.',
			'DTCTURI:02' => 'Please set the Request URI explicitly when you instantiate the xajax object.',
			'ARGMGR:ERR:01' => 'Malformed object argument received: ',
			'ARGMGR:ERR:02' => ' <==> ',
			'ARGMGR:ERR:03' => 'The incoming xajax data could not be converted from UTF-8',
			'XJXCTL:IAERR:01' => 'Invalid attribute [',
			'XJXCTL:IAERR:02' => '] for element [',
			'XJXCTL:IAERR:03' => '].',
			'XJXCTL:IRERR:01' => 'Invalid request object passed to xajaxControl::setEvent',
			'XJXCTL:IEERR:01' => 'Invalid attribute (event name) [',
			'XJXCTL:IEERR:02' => '] for element [',
			'XJXCTL:IEERR:03' => '].',
			'XJXCTL:MAERR:01' => 'Missing required attribute [',
			'XJXCTL:MAERR:02' => '] for element [',
			'XJXCTL:MAERR:03' => '].',
			'XJXCTL:IETERR:01' => "Invalid end tag designation; should be forbidden or optional.\n",
			'XJXCTL:ICERR:01' => "Invalid class specified for html control; should be %inline, %block or %flow.\n",
			'XJXCTL:ICLERR:01' => 'Invalid control passed to addChild; should be derived from xajaxControl.',
			'XJXCTL:ICLERR:02' => 'Invalid control passed to addChild [',
			'XJXCTL:ICLERR:03' => '] for element [',
			'XJXCTL:ICLERR:04' => "].\n",
			'XJXCTL:ICHERR:01' => 'Invalid parameter passed to xajaxControl::addChildren; should be array of xajaxControl objects',
			'XJXCTL:MRAERR:01' => 'Missing required attribute [',
			'XJXCTL:MRAERR:02' => '] for element [',
			'XJXCTL:MRAERR:03' => '].',
			'XJXPLG:GNERR:01' => 'Response plugin should override the getName function.',
			'XJXPLG:PERR:01' => 'Response plugin should override the process function.',
			'XJXPM:IPLGERR:01' => 'Attempt to register invalid plugin: ',
			'XJXPM:IPLGERR:02' => ' should be derived from xajaxRequestPlugin or xajaxResponsePlugin.',
			'XJXPM:MRMERR:01' => 'Failed to locate registration method for the following: ',
			'XJXRSP:EDERR:01' => 'Passing character encoding to the xajaxResponse constructor is deprecated, instead use $xajax->configure("characterEncoding", ...);',
			'XJXRSP:MPERR:01' => 'Invalid or missing plugin name detected in call to xajaxResponse::plugin',
			'XJXRSP:CPERR:01' => "The \$sType parameter of addCreate has been deprecated.  Use the addCreateInput() method instead.",
			'XJXRSP:LCERR:01' => "The xajax response object could not load commands as the data provided was not a valid array.",
			'XJXRSP:AKERR:01' => 'Invalid tag name encoded in array.',
			'XJXRSP:IEAERR:01' => 'Improperly encoded array.',
			'XJXRSP:NEAERR:01' => 'Non-encoded array detected.',
			'XJXRSP:MBEERR:01' => 'The xajax response output could not be converted to HTML entities because the mb_convert_encoding function is not available',
			'XJXRSP:MXRTERR' => 'Error: Cannot mix types in a single response.',
			'XJXRSP:MXCTERR' => 'Error: Cannot mix content types in a single response.',
			'XJXRSP:MXCEERR' => 'Error: Cannot mix character encodings in a single response.',
			'XJXRSP:MXOEERR' => 'Error: Cannot mix output entities (true/false) in a single response.',
			'XJXRM:IRERR' => 'An invalid response was returned while processing this request.',
			'XJXRM:MXRTERR' => 'Error:  You cannot mix response types while processing a single request: '
			);
			
		$this->sLanguage = 'en';
	}
	
	/*
		Function: getInstance
		
		Implements the singleton pattern: provides a single instance of the xajax 
		language manager object to all object which request it.
	*/
	function &getInstance()
	{
		static $obj;
		if (!$obj) {
			$obj = new xajaxLanguageManager();
		}
		return $obj;
	}
	
	/*
		Function: configure
		
		Called by the main xajax object as configuration options are set.  See also:
		<xajax::configure>.  The <xajaxLanguageManager> tracks the following configuration
		options:
		
		- language (string, default 'en'): The currently selected language.
	*/
	function configure($sName, $mValue)
	{
		if ('language' == $sName) {
			if ($mValue !== $this->sLanguage) {
				$sFolder = dirname(__FILE__);
				require $sFolder . '/xajax_lang_' . $mValue . '.inc.php';
				$this->sLanguage = $mValue;
			}
		}
	}
	
	/*
		Function: register
		
		Called to register an array of alternate language messages.
		
		sLanguage - (string) the character code which represents the language being registered.
		aMessages - (array) the array of translated debug and error messages
	*/
	function register($sLanguage, $aMessages) {
		$this->aMessages[$sLanguage] = $aMessages;
	}
	
	/*
		Function: getText
		
		Called by the main xajax object and other objects during the initial page generation
		or request processing phase to obtain language specific debug and error messages.
		
		sMessage - (string):  A code indicating the message text being requested.
	*/
	function getText($sMessage)
	{
		if (isset($this->aMessages[$this->sLanguage]))
			 if (isset($this->aMessages[$this->sLanguage][$sMessage]))
				return $this->aMessages[$this->sLanguage][$sMessage];
				
		return '(Unknown language or message identifier)'
			. $this->sLanguage
			. '::'
			. $sMessage;
	}
}
