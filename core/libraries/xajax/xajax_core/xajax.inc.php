<?php
/*
	File: xajax.inc.php

	Main xajax class and setup file.

	Title: xajax class

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: xajax.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Section: Standard Definitions
*/

/*
	String: XAJAX_DEFAULT_CHAR_ENCODING

	Default character encoding used by both the <xajax> and
	<xajaxResponse> classes.
*/
if (!defined ('XAJAX_DEFAULT_CHAR_ENCODING')) define ('XAJAX_DEFAULT_CHAR_ENCODING', 'utf-8');

/*
	String: XAJAX_PROCESSING_EVENT
	String: XAJAX_PROCESSING_EVENT_BEFORE
	String: XAJAX_PROCESSING_EVENT_AFTER
	String: XAJAX_PROCESSING_EVENT_INVALID

	Identifiers used to register processing events.  Processing events are essentially
	hooks into the xajax core that can be used to add functionality into the request
	processing sequence.
*/
if (!defined ('XAJAX_PROCESSING_EVENT')) define ('XAJAX_PROCESSING_EVENT', 'xajax processing event');
if (!defined ('XAJAX_PROCESSING_EVENT_BEFORE')) define ('XAJAX_PROCESSING_EVENT_BEFORE', 'beforeProcessing');
if (!defined ('XAJAX_PROCESSING_EVENT_AFTER')) define ('XAJAX_PROCESSING_EVENT_AFTER', 'afterProcessing');
if (!defined ('XAJAX_PROCESSING_EVENT_INVALID')) define ('XAJAX_PROCESSING_EVENT_INVALID', 'invalidRequest');

/*
	Class: xajax

	The xajax class uses a modular plug-in system to facilitate the processing
	of special Ajax requests made by a PHP page.  It generates Javascript that
	the page must include in order to make requests.  It handles the output
	of response commands (see <xajaxResponse>).  Many flags and settings can be
	adjusted to effect the behavior of the xajax class as well as the client-side
	javascript.
*/
class xajax
{
	/**#@+
	 * @access protected
	 */

	/*
		Array: aSettings
		
		This array is used to store all the configuration settings that are set during
		the run of the script.  This provides a single data store for the settings
		in case we need to return the value of a configuration option for some reason.
		
		It is advised that individual plugins store a local copy of the settings they
		wish to track, however, settings are available via a reference to the <xajax> 
		object using <xajax->getConfiguration>.
	*/
	var $aSettings;

	/*
		Boolean: bErrorHandler
		
		This is a configuration setting that the main xajax object tracks.  It is used
		to enable an error handler function which will trap php errors and return them
		to the client as part of the response.  The client can then display the errors
		to the user if so desired.
	*/
	var $bErrorHandler;

	/*
		Array: aProcessingEvents
		
		Stores the processing event handlers that have been assigned during this run
		of the script.
	*/
	var $aProcessingEvents;

	/*
		Boolean: bExitAllowed
		
		A configuration option that is tracked by the main <xajax>object.  Setting this
		to true allows <xajax> to exit immediatly after processing a xajax request.  If
		this is set to false, xajax will allow the remaining code and HTML to be sent
		as part of the response.  Typically this would result in an error, however, 
		a response processor on the client side could be designed to handle this condition.
	*/
	var $bExitAllowed;
	
	/*
		Boolean: bCleanBuffer
		
		A configuration option that is tracked by the main <xajax> object.  Setting this
		to true allows <xajax> to clear out any pending output buffers so that the 
		<xajaxResponse> is (virtually) the only output when handling a request.
	*/
	var $bCleanBuffer;
	
	/*
		String: sLogFile
	
		A configuration setting tracked by the main <xajax> object.  Set the name of the
		file on the server that you wish to have php error messages written to during
		the processing of <xajax> requests.	
	*/
	var $sLogFile;

	/*
		String: sCoreIncludeOutput
		
		This is populated with any errors or warnings produced while including the xajax
		core components.  This is useful for debugging core updates.
	*/
	var $sCoreIncludeOutput;
	
	/*
		Object: objPluginManager
		
		This stores a reference to the global <xajaxPluginManager>
	*/
	var $objPluginManager;
	
	/*
		Object: objArgumentManager
		
		Stores a reference to the global <xajaxArgumentManager>
	*/
	var $objArgumentManager;
	
	/*
		Object: objResponseManager
		
		Stores a reference to the global <xajaxResponseManager>
	*/
	var $objResponseManager;
	
	/*
		Object: objLanguageManager
		
		Stores a reference to the global <xajaxLanguageManager>
	*/
	var $objLanguageManager;

	/**#@-*/

	/*
		Constructor: xajax

		Constructs a xajax instance and initializes the plugin system.

		sRequestURI - (optional):  The <xajax->sRequestURI> to be used
			for calls back to the server.  If empty, xajax fills in the current
			URI that initiated this request.
	*/
	function xajax($sRequestURI=null, $sLanguage=null)
	{
		$this->bErrorHandler = false;
		$this->aProcessingEvents = array();
		$this->bExitAllowed = true;
		$this->bCleanBuffer = true;
		$this->sLogFile = '';
		
		$this->__wakeup();
		
		// The default configuration settings.
		$this->configureMany(
			array(
				'characterEncoding' => XAJAX_DEFAULT_CHAR_ENCODING,
				'decodeUTF8Input' => false,
				'outputEntities' => false,
				'defaultMode' => 'asynchronous',
				'defaultMethod' => 'POST',	// W3C: Method is case sensitive
				'wrapperPrefix' => 'xajax_',
				'debug' => false,
				'verbose' => false,
				'useUncompressedScripts' => false,
				'statusMessages' => false,
				'waitCursor' => true,
				'scriptDeferral' => false,
				'exitAllowed' => true,
				'errorHandler' => false,
				'cleanBuffer' => false,
				'allowBlankResponse' => false,
				'allowAllResponseTypes' => false,
				'generateStubs' => true,
				'logFile' => '',
				'timeout' => 6000,
				'version' => $this->getVersion()
				)
			);

		if (null !== $sRequestURI)
			$this->configure('requestURI', $sRequestURI);
		else
			$this->configure('requestURI', $this->_detectURI());
		
		if (null !== $sLanguage)
			$this->configure('language', $sLanguage);

		if ('utf-8' != XAJAX_DEFAULT_CHAR_ENCODING) $this->configure("decodeUTF8Input", true);

	}
	
	/*
		Function: __sleep
	*/
	function __sleep()
	{
		$aMembers = get_class_vars(get_class($this));
		
		if (isset($aMembers['objLanguageManager']))
			unset($aMembers['objLanguageManager']);
		
		if (isset($aMembers['objPluginManager']))
			unset($aMembers['objPluginManager']);
			
		if (isset($aMembers['objArgumentManager']))
			unset($aMembers['objArgumentManager']);
			
		if (isset($aMembers['objResponseManager']))
			unset($aMembers['objResponseManager']);
			
		if (isset($aMembers['sCoreIncludeOutput']))
			unset($aMembers['sCoreIncludeOutput']);
		
		return array_keys($aMembers);
	}
	
	/*
		Function: __wakeup
	*/
	function __wakeup()
	{
		ob_start();

		$sLocalFolder = dirname(__FILE__);

//SkipAIO
		require $sLocalFolder . '/xajaxPluginManager.inc.php';
		require $sLocalFolder . '/xajaxLanguageManager.inc.php';
		require $sLocalFolder . '/xajaxArgumentManager.inc.php';
		require $sLocalFolder . '/xajaxResponseManager.inc.php';
		require $sLocalFolder . '/xajaxRequest.inc.php';
		require $sLocalFolder . '/xajaxResponse.inc.php';
//EndSkipAIO

		// this is the list of folders where xajax will look for plugins
		// that will be automatically included at startup.
		$aPluginFolders = array();
		$aPluginFolders[] = dirname($sLocalFolder) . '/xajax_plugins';
		
//SkipAIO
		$aPluginFolders[] = $sLocalFolder . '/plugin_layer';
//EndSkipAIO
		
		// Setup plugin manager
		$this->objPluginManager =& xajaxPluginManager::getInstance();
		$this->objPluginManager->loadPlugins($aPluginFolders);

		$this->objLanguageManager =& xajaxLanguageManager::getInstance();
		$this->objArgumentManager =& xajaxArgumentManager::getInstance();
		$this->objResponseManager =& xajaxResponseManager::getInstance();
		
		$this->sCoreIncludeOutput = ob_get_clean();
	}

	/*
		Function: getGlobalResponse

		Returns the <xajaxResponse> object preconfigured with the encoding
		and entity settings from this instance of <xajax>.  This is used
		for singleton-pattern response development.

		Returns:

		<xajaxResponse> - A <xajaxResponse> object which can be used to return
			response commands.  See also the <xajaxResponseManager> class.
	*/
	function &getGlobalResponse()
	{
		static $obj;
		if (!$obj) {
			$obj = new xajaxResponse();
		}
		return $obj;
	}

	/*
		Function: getVersion

		Returns:

		string - The current xajax version.
	*/
	function getVersion()
	{
		return 'xajax 0.5 Beta 4';
	}

	/*
		Function: register
		
		Call this function to register request handlers, including functions, 
		callable objects and events.  New plugins can be added that support
		additional registration methods and request processors.
		
		$sType - (string): Type of request handler being registered; standard 
			options include:
				XAJAX_FUNCTION: a function declared at global scope.
				XAJAX_CALLABLE_OBJECT: an object who's methods are to be registered.
				XAJAX_EVENT: an event which will cause zero or more event handlers
					to be called.
				XAJAX_EVENT_HANDLER: register an event handler function.
				
		$sFunction || $objObject || $sEvent - (mixed):
			when registering a function, this is the name of the function
			when registering a callable object, this is the object being registered
			when registering an event or event handler, this is the name of the event
			
		$sIncludeFile || $aCallOptions || $sEventHandler
			when registering a function, this is the (optional) include file.
			when registering a callable object, this is an (optional) array
				of call options for the functions being registered.
			when registering an event handler, this is the name of the function.
	*/
	function register($sType, $mArg)
	{
		$aArgs = func_get_args();
		$nArgs = func_num_args();

		if (2 < $nArgs)
		{
			if (XAJAX_PROCESSING_EVENT == $aArgs[0])
			{
				$sEvent = $aArgs[1];
				$xuf =& $aArgs[2];

				if (false == is_a($xuf, 'xajaxUserFunction'))
					$xuf =& new xajaxUserFunction($xuf);

				$this->aProcessingEvents[$sEvent] =& $xuf;

				return true;
			}
		}
		
		if (1 < $nArgs)
		{
			// for php4
			$aArgs[1] =& $mArg;
		}

		return $this->objPluginManager->register($aArgs);
	}

	/*
		Function: configure
		
		Call this function to set options that will effect the processing of 
		xajax requests.  Configuration settings can be specific to the xajax
		core, request processor plugins and response plugins.
		
		Options include:
			javascript URI - (string): The path to the folder that contains the 
				xajax javascript files.
			errorHandler - (boolean): true to enable the xajax error handler, see
				<xajax->bErrorHandler>
			exitAllowed - (boolean): true to allow xajax to exit after processing
				a request.  See <xajax->bExitAllowed> for more information.
	*/
	function configure($sName, $mValue)
	{
		if ('errorHandler' == $sName) {
			if (true === $mValue || false === $mValue)
				$this->bErrorHandler = $mValue;
		} else if ('exitAllowed' == $sName) {
			if (true === $mValue || false === $mValue)
				$this->bExitAllowed = $mValue;
		} else if ('cleanBuffer' == $sName) {
			if (true === $mValue || false === $mValue)
				$this->bCleanBuffer = $mValue;
		} else if ('logFile' == $sName) {
			$this->sLogFile = $mValue;
		}

		$this->objLanguageManager->configure($sName, $mValue);
		$this->objArgumentManager->configure($sName, $mValue);
		$this->objPluginManager->configure($sName, $mValue);
		$this->objResponseManager->configure($sName, $mValue);

		$this->aSettings[$sName] = $mValue;
	}

	/*
		Function: configureMany
		
		Set an array of configuration options.
		
		$aOptions - (array): Associative array of configuration settings
	*/
	function configureMany($aOptions)
	{
		foreach ($aOptions as $sName => $mValue)
			$this->configure($sName, $mValue);
	}

	/*
		Function: getConfiguration
		
		Get the current value of a configuration setting that was previously set
		via <xajax->configure> or <xajax->configureMany>
		
		Returns:
		
		$mValue - (mixed):  The value of the setting if set, null otherwise.
	*/
	function getConfiguration($sName)
	{
		if (isset($this->aSettings[$sName]))
			return $this->aSettings[$sName];
		return NULL;
	}

	/*
		Function: canProcessRequest
		
		Determines if a call is a xajax request or a page load request.
		
		Return:
		
		boolean - True if this is a xajax request, false otherwise.
	*/
	function canProcessRequest()
	{
		return $this->objPluginManager->canProcessRequest();
	}

	/*
		Function: processRequest

		If this is a xajax request (see <xajax->canProcessRequest>), call the
		requested PHP function, build the response and send it back to the
		browser.

		This is the main server side engine for xajax.  It handles all the
		incoming requests, including the firing of events and handling of the
		response.  If your RequestURI is the same as your web page, then this
		function should be called before ANY headers or HTML is output from
		your script.

		This function may exit, if a request is processed.  See <xajax->bAllowExit>
	*/
	function processRequest()
	{
//SkipDebug
		// Check to see if headers have already been sent out, in which case we can't do our job
		if (headers_sent($filename, $linenumber)) {
			echo "Output has already been sent to the browser at {$filename}:{$linenumber}.\n";
			echo 'Please make sure the command $xajax->processRequest() is placed before this.';
			exit();
		}
//EndSkipDebug

		if ($this->canProcessRequest())
		{
			// Use xajax error handler if necessary
			if ($this->bErrorHandler) {
				$GLOBALS['xajaxErrorHandlerText'] = "";
				set_error_handler("xajaxErrorHandler");
			}
			
			$mResult = true;

			// handle beforeProcessing event
			if (isset($this->aProcessingEvents[XAJAX_PROCESSING_EVENT_BEFORE]))
			{
				$bEndRequest = false;
				$this->aProcessingEvents[XAJAX_PROCESSING_EVENT_BEFORE]->call(array(&$bEndRequest));
				$mResult = (false === $bEndRequest);
			}

			if (true === $mResult)
				$mResult = $this->objPluginManager->processRequest();

			if (true === $mResult)
			{
				if ($this->bCleanBuffer) {
					$er = error_reporting(0);
					while (ob_get_level() > 0) ob_end_clean();
					error_reporting($er);
				}

				// handle afterProcessing event
				if (isset($this->aProcessingEvents[XAJAX_PROCESSING_EVENT_AFTER]))
				{
					$bEndRequest = false;
					$this->aProcessingEvents[XAJAX_PROCESSING_EVENT_AFTER]->call(array(&$bEndRequest));
					if (true === $bEndRequest)
					{
						$this->objResponseManager->clear();
						$this->objResponseManager->append($aResult[1]);
					}
				}
			}
			else if (is_string($mResult))
			{
				if ($this->bCleanBuffer) {
					$er = error_reporting(0);
					while (ob_get_level() > 0) ob_end_clean();
					error_reporting($er);
				}

				// $mResult contains an error message
				// the request was missing the cooresponding handler function
				// or an error occurred while attempting to execute the
				// handler.  replace the response, if one has been started
				// and send a debug message.

				$this->objResponseManager->clear();
				$this->objResponseManager->append(new xajaxResponse());

				// handle invalidRequest event
				if (isset($this->aProcessingEvents[XAJAX_PROCESSING_EVENT_INVALID]))
					$this->aProcessingEvents[XAJAX_PROCESSING_EVENT_INVALID]->call();
				else
					$this->objResponseManager->debug($mResult);
			}

			if ($this->bErrorHandler) {
				$sErrorMessage = $GLOBALS['xajaxErrorHandlerText'];
				if (!empty($sErrorMessage)) {
					if (0 < strlen($this->sLogFile)) {
						$fH = @fopen($this->sLogFile, "a");
						if (NULL != $fH) {
							fwrite(
								$fH, 
								$this->objLanguageManager->getText('LOGHDR:01')
								. strftime("%b %e %Y %I:%M:%S %p") 
								. $this->objLanguageManager->getText('LOGHDR:02')
								. $sErrorMessage 
								. $this->objLanguageManager->getText('LOGHDR:03')
								);
							fclose($fH);
						} else {
							$this->objResponseManager->debug(
								$this->objLanguageManager->getText('LOGERR:01') 
								. $this->sLogFile
								);
						}
					}
					$this->objResponseManager->debug(
						$this->objLanguageManager->getText('LOGMSG:01') 
						. $sErrorMessage
						);
				}
			}

			$this->objResponseManager->send();

			if ($this->bErrorHandler) restore_error_handler();

			if ($this->bExitAllowed) exit();
		}
	}

	/*
		Function: printJavascript
		
		Prints the xajax Javascript header and wrapper code into your page.
		This should be used to print the javascript code between the HEAD
		and /HEAD tags at the top of the page.
		
		The javascript code output by this function is dependent on the plugins
		that are included and the functions that are registered.
		
		$sJsURI - (string, optional, deprecated): the path to the xajax javascript file(s)
			This option is deprecated and will be removed in future versions; instead
			please use <xajax->configure> with the option name 'javascript URI'
		$aJsFiles - (array, optional, deprecated): an array of xajax javascript files
			that will be loaded via SCRIPT tags.  This option is deprecated and will
			be removed in future versions; please use <xajax->configure> with the 
			option name 'javascript files' instead.
	*/
	function printJavascript($sJsURI="", $aJsFiles=array())
	{
		if (0 < strlen($sJsURI))
			$this->configure("javascript URI", $sJsURI);

		if (0 < count($aJsFiles))
			$this->configure("javascript files", $aJsFiles);

		$this->objPluginManager->generateClientScript();
	}

	/*
		Function: getJavascript
		
		See <xajax->printJavascript> for more information.
	*/
	function getJavascript($sJsURI='', $aJsFiles=array())
	{
		ob_start();
		$this->printJavascript($sJsURI, $aJsFiles);
		return ob_get_clean();
	}

	/*
		Function: autoCompressJavascript

		Creates a new xajax_core, xajax_debug, etc... file out of the
		_uncompressed file with a similar name.  This strips out the
		comments and extraneous whitespace so the file is as small as
		possible without modifying the function of the code.

		sJsFullFilename - (string):  The relative path and name of the file
			to be compressed.
		bAlways - (boolean):  Compress the file, even if it already exists.
	*/
	function autoCompressJavascript($sJsFullFilename=NULL, $bAlways=false)
	{
		$sJsFile = 'xajax_js/xajax_core.js';

		if ($sJsFullFilename) {
			$realJsFile = $sJsFullFilename;
		}
		else {
			$realPath = realpath(dirname(dirname(__FILE__)));
			$realJsFile = $realPath . '/'. $sJsFile;
		}

		// Create a compressed file if necessary
		if (!file_exists($realJsFile) || true == $bAlways) {
			$srcFile = str_replace('.js', '_uncompressed.js', $realJsFile);
			if (!file_exists($srcFile)) {
				trigger_error(
					$this->objLanguageManager->getText('CMPRSJS:RDERR:01') 
					. dirname($realJsFile) 
					. $this->objLanguageManager->getText('CMPRSJS:RDERR:02')
					, E_USER_ERROR
					);
			}
			require_once(dirname(__FILE__) . '/xajaxCompress.inc.php');
			$javaScript = implode('', file($srcFile));
			$compressedScript = xajaxCompressFile($javaScript);
			$fH = @fopen($realJsFile, 'w');
			if (!$fH) {
				trigger_error(
					$this->objLanguageManager->getText('CMPRSJS:WTERR:01') 
					. dirname($realJsFile) 
					. $this->objLanguageManager->getText('CMPRSJS:WTERR:02')
					, E_USER_ERROR
					);
			}
			else {
				fwrite($fH, $compressedScript);
				fclose($fH);
			}
		}
	}
	
	function _compressSelf($sFolder=null)
	{
		if (null == $sFolder)
			$sFolder = dirname(dirname(__FILE__));
			
		require_once(dirname(__FILE__) . '/xajaxCompress.inc.php');

		if ($handle = opendir($sFolder)) {
			while (!(false === ($sName = readdir($handle)))) {
				if ('.' != $sName && '..' != $sName && is_dir($sFolder . '/' . $sName)) {
					$this->_compressSelf($sFolder . '/' . $sName);
				} else if (8 < strlen($sName) && 0 == strpos($sName, '.compressed')) {
					if ('.inc.php' == substr($sName, strlen($sName) - 8, 8)) {
						$sName = substr($sName, 0, strlen($sName) - 8);
						$sPath = $sFolder . '/' . $sName . '.inc.php';
						if (file_exists($sPath)) {
							
							$aParsed = array();
							$aFile = file($sPath);
							$nSkip = 0;
							foreach (array_keys($aFile) as $sKey)
								if ('//SkipDebug' == $aFile[$sKey])
									++$nSkip;
								else if ('//EndSkipDebug' == $aFile[$sKey])
									--$nSkip;
								else if (0 == $nSkip)
									$aParsed[] = $aFile[$sKey];
							unset($aFile);
							
							$compressedScript = xajaxCompressFile(implode('', $aParsed));
							
							$sNewPath = $sPath;
							$fH = @fopen($sNewPath, 'w');
							if (!$fH) {
								trigger_error(
									$this->objLanguageManager->getText('CMPRSPHP:WTERR:01') 
									. $sNewPath 
									. $this->objLanguageManager->getText('CMPRSPHP:WTERR:02')
									, E_USER_ERROR
									);
							}
							else {
								fwrite($fH, $compressedScript);
								fclose($fH);
							}
						}
					}
				}
			}
			
			closedir($handle);
		}
	}
	
	function _compile($sFolder=null, $bWriteFile=true)
	{
		if (null == $sFolder)
			$sFolder = dirname(__FILE__);
			
		require_once(dirname(__FILE__) . '/xajaxCompress.inc.php');
		
		$aOutput = array();

		if ($handle = opendir($sFolder)) {
			while (!(false === ($sName = readdir($handle)))) {
				if ('.' != $sName && '..' != $sName && is_dir($sFolder . '/' . $sName)) {
					$aOutput[] = $this->_compile($sFolder . '/' . $sName, false);
				} else if (8 < strlen($sName)) {
					if ('.inc.php' == substr($sName, strlen($sName) - 8, 8)) {
						$sName = substr($sName, 0, strlen($sName) - 8);
						$sPath = $sFolder . '/' . $sName . '.inc.php';
						if (
							'xajaxAIO' != $sName && 
							'legacy' != $sName && 
							'xajaxCompress' != $sName
							) {
							if (file_exists($sPath)) {
								
								$aParsed = array();
								$aFile = file($sPath);
								$nSkip = 0;
								foreach (array_keys($aFile) as $sKey)
									if ('//SkipDebug' == substr($aFile[$sKey], 0, 11))
										++$nSkip;
									else if ('//EndSkipDebug' == substr($aFile[$sKey], 0, 14))
										--$nSkip;
									else if ('//SkipAIO' == substr($aFile[$sKey], 0, 9))
										++$nSkip;
									else if ('//EndSkipAIO' == substr($aFile[$sKey], 0, 12))
										--$nSkip;
									else if ('<'.'?php' == substr($aFile[$sKey], 0, 5)) {}
									else if ('?'.'>' == substr($aFile[$sKey], 0, 2)) {}
									else if (0 == $nSkip)
										$aParsed[] = $aFile[$sKey];
								unset($aFile);
								
								$aOutput[] = xajaxCompressFile(implode('', $aParsed));
							}
						}
					}
				}
			}
			
			closedir($handle);
		}
		
		if ($bWriteFile)
		{
			$fH = @fopen($sFolder . '/xajaxAIO.inc.php', 'w');
			if (!$fH) {
				trigger_error(
					$this->objLanguageManager->getText('CMPRSAIO:WTERR:01') 
					. $sFolder 
					. $this->objLanguageManager->getText('CMPRSAIO:WTERR:02')
					, E_USER_ERROR
					);
			}
			else {
				fwrite($fH, '<'.'?php ');
				fwrite($fH, implode('', $aOutput));
				fclose($fH);
			}
		}
		
		return implode('', $aOutput);
	}

	/*
		Function: _detectURI

		Returns the current requests URL based upon the SERVER vars.

		Returns:

		string - The URL of the current request.
	*/
	function _detectURI() {
		$aURL = array();

		// Try to get the request URL
		if (!empty($_SERVER['REQUEST_URI'])) {
		
			$_SERVER['REQUEST_URI'] = str_replace(
				array('"',"'",'<','>'), 
				array('%22','%27','%3C','%3E'), 
				$_SERVER['REQUEST_URI']
				);
				
			$aURL = parse_url($_SERVER['REQUEST_URI']);
		}

		// Fill in the empty values
		if (empty($aURL['scheme'])) {
			if (!empty($_SERVER['HTTP_SCHEME'])) {
				$aURL['scheme'] = $_SERVER['HTTP_SCHEME'];
			} else {
				$aURL['scheme'] = 
					(!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off') 
					? 'https' 
					: 'http';
			}
		}

		if (empty($aURL['host'])) {
			if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
				if (strpos($_SERVER['HTTP_X_FORWARDED_HOST'], ':') > 0) {
					list($aURL['host'], $aURL['port']) = explode(':', $_SERVER['HTTP_X_FORWARDED_HOST']);
				} else {
					$aURL['host'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
				}
			} else if (!empty($_SERVER['HTTP_HOST'])) {
				if (strpos($_SERVER['HTTP_HOST'], ':') > 0) {
					list($aURL['host'], $aURL['port']) = explode(':', $_SERVER['HTTP_HOST']);
				} else {
					$aURL['host'] = $_SERVER['HTTP_HOST'];
				}
			} else if (!empty($_SERVER['SERVER_NAME'])) {
				$aURL['host'] = $_SERVER['SERVER_NAME'];
			} else {
				print $this->objLanguageManager->getText('DTCTURI:01');
				print $this->objLanguageManager->getText('DTCTURI:02');
				exit();
			}
		}

		if (empty($aURL['port']) && !empty($_SERVER['SERVER_PORT'])) {
			$aURL['port'] = $_SERVER['SERVER_PORT'];
		}

		if (!empty($aURL['path']))
			if (0 == strlen(basename($aURL['path'])))
				unset($aURL['path']);
		
		if (empty($aURL['path'])) {
			$sPath = array();
			if (!empty($_SERVER['PATH_INFO'])) {
				$sPath = parse_url($_SERVER['PATH_INFO']);
			} else {
				$sPath = parse_url($_SERVER['PHP_SELF']);
			}
			if (isset($sPath['path']))
				$aURL['path'] = str_replace(array('"',"'",'<','>'), array('%22','%27','%3C','%3E'), $sPath['path']);
			unset($sPath);
		}

		if (empty($aURL['query']) && !empty($_SERVER['QUERY_STRING'])) {
			$aURL['query'] = $_SERVER['QUERY_STRING'];
		}

		if (!empty($aURL['query'])) {
			$aURL['query'] = '?'.$aURL['query'];
		}

		// Build the URL: Start with scheme, user and pass
		$sURL = $aURL['scheme'].'://';
		if (!empty($aURL['user'])) {
			$sURL.= $aURL['user'];
			if (!empty($aURL['pass'])) {
				$sURL.= ':'.$aURL['pass'];
			}
			$sURL.= '@';
		}

		// Add the host
		$sURL.= $aURL['host'];

		// Add the port if needed
		if (!empty($aURL['port']) 
			&& (($aURL['scheme'] == 'http' && $aURL['port'] != 80) 
			|| ($aURL['scheme'] == 'https' && $aURL['port'] != 443))) {
			$sURL.= ':'.$aURL['port'];
		}

		// Add the path and the query string
		$sURL.= $aURL['path'].@$aURL['query'];

		// Clean up
		unset($aURL);
		
		$aURL = explode("?", $sURL);
		
		if (1 < count($aURL))
		{
			$aQueries = explode("&", $aURL[1]);

			foreach ($aQueries as $sKey => $sQuery)
			{
				if ("xjxGenerate" == substr($sQuery, 0, 11))
					unset($aQueries[$sKey]);
			}
			
			$sQueries = implode("&", $aQueries);
			
			$aURL[1] = $sQueries;
			
			$sURL = implode("?", $aURL);
		}

		return $sURL;
	}


	/*
		Deprecated functions
	*/

	/*
		Function: setCharEncoding

		Sets the character encoding that will be used for the HTTP output.
		Typically, you will not need to use this method since the default
		character encoding can be configured using the constant
		<XAJAX_DEFAULT_CHAR_ENCODING>.

		sEncoding - (string):  The encoding to use.
			- examples include (UTF-8, ISO-8859-1)

		deprecated - This function will be removed in future versions.  Please
			use <xajax->configure> instead.
	*/
	function setCharEncoding($sEncoding)
	{
		$this->configure('characterEncoding', $sEncoding);
	}

	/*
		Function: getCharEncoding

		Returns the current character encoding.  See also <xajax->setCharEncoding>
		and <XAJAX_DEFAULT_CHAR_ENCODING>

		Returns:

		string - The character encoding.

		deprecated - This function will be removed in future versions.  Please
			use <xajax->getConfiguration> instead.
	*/
	function getCharEncoding()
	{
		return $this->getConfiguration('characterEncoding');
	}

	/*
		Function: setFlags

		Sets a series of flags.  See also, <xajax->setFlag>.

		flags - (array):  An associative array containing the name of the flag
			and the value to set.

		deprecated - This function will be removed in future versions.  Please
			use <xajax->configureMany> instead.
	*/
	function setFlags($flags)
	{
		foreach ($flags as $name => $value) {
			$this->configure($name, $value);
		}
	}

	/*
		Function: setFlag

		Sets a single flag (boolean true or false).

		Available flags are as follows (flag, default value):
			- debug, false
			- verbose, false
			- statusMessages, false
			- waitCursor, true
			- scriptDeferral, false
			- exitAllowed, true
			- errorHandler, false
			- cleanBuffer, false
			- decodeUTF8Input, false
			- outputEntities, false
			- allowBlankResponse, false
			- allowAllResponseTypes, false
			- generateStubs, true

		name - (string): The name of the flag to set.
		value - (boolean):  The value to set.

		deprecated - This function will be removed in future versions.  Please
			use <xajax->configure> instead.
	*/
	function setFlag($name, $value)
	{
		$this->configure($name, $value);

	}

	/*
		Function: getFlag

		Returns the current value of the flag.  See also <xajax->setFlag>.

		name - (string):  The name of the flag.

		Returns:

		boolean - The value currently associated with the flag.

		deprecated - This function will be removed in future versions.  Instead,
			use <xajax->getConfiguration>.
	*/
	function getFlag($name)
	{
		return $this->getConfiguration($name);
	}

	/*
		Function: setRequestURI

		Sets the URI to which requests will be sent.

		sRequestURI - (string):  The URI

		Note: Usage

		$xajax->setRequestURI("http://www.xajaxproject.org");

		deprecated - This function will be removed in future versions.  Please
			use <xajax->configure> instead.
	*/
	function setRequestURI($sRequestURI)
	{
		$this->configure('requestURI', $sRequestURI);
	}

	/*
		Function: getRequestURI

		Returns:

		string - The current request URI that will be configured on the client
			side.  This is the default URI for all requests made from the current
			page.  See <xajax->setRequestURI>.

		deprecated - This function will be removed in future versions.  Please
			use <xajax->getConfiguration> instead.
	*/
	function getRequestURI()
	{
		return $this->getConfiguration('requestURI');
	}

	/*
		Function: setDefaultMode

		Sets the default mode for requests from the browser.

		sDefaultMode - (string):  The mode to set as the default.

			- 'synchronous'
			- 'asynchronous'

		Example:

		$xajax->setDefaultMode("synchronous");

		deprecated - This function will be removed in future versions.  Please
			use <xajax->configure> instead.
	*/
	function setDefaultMode($sDefaultMode)
	{
		$this->configure('defaultMode', $sDefaultMode);
	}

	/*
		Function: getDefaultMode

		Get the default request mode that will be used by the browser
		for submitting requests to the server.  See also <xajax->setDefaultMode>

		Returns:

		string - The default mode to be used by the browser for each
			request.

		deprecated - This function will be removed in future versions.  Please
			use <xajax->getConfiguration> instead.
	*/
	function getDefaultMode()
	{
		return $this->getConfiguration('defaultMode');
	}

	/*
		Function: setDefaultMethod

		Sets the default method for making xajax requests:

		sMethod - (string):  The name of the method.

			- 'GET'
			- 'POST'

		deprecated - This function will be removed in future versions.  Please
			use <xajax->configure> instead.
	*/
	function setDefaultMethod($sMethod)
	{
		$this->configure('defaultMethod', $sMethod);
	}

	/*
		Function: getDefaultMethod

		Gets the default method for making xajax requests.

		Returns:

		string - The current method configured.

		deprecated - This function will be removed in future versions.  Please
			use <xajax->getConfiguration> instead.
	*/
	function getDefaultMethod()
	{
		return $this->getConfiguration('defaultMethod');
	}

	/*
		Function: setWrapperPrefix

		Sets the prefix that will be prepended to the javascript wrapper
		functions.  This allows a little flexibility in setting the naming
		for the wrapper functions.

		sPrefix - (string):  The prefix to be used.
			- default is 'xajax_'

		deprecated - This function will be removed in future versions.  Please
			use <xajax->configure> instead.
	*/
	function setWrapperPrefix($sPrefix)
	{
		$this->configure('wrapperPrefix', $sPrefix);
	}

	/*
		Function: getWrapperPrefix

		Gets the current javascript wrapper prefix.  See also, <xajax->setWrapperPrefix>

		Returns:

		string - The current wrapper prefix.

		deprecated - This function will be removed in future versions.  Please
			use <xajax->getConfiguration> instead.
	*/
	function getWrapperPrefix()
	{
		return $this->getConfiguration('wrapperPrefix');
	}

	/*
		Function: setLogFile

		Specifies a log file that will be written to by xajax during a
		request.  This is only used by the error handling system at this
		point.  If you do not invoke this method or you pass in an empty
		string, then no log file will be written to.

		sFilename - (string):  The full or reletive path to the log file.

		deprecated - This function will be removed in future versions.  Please
			use <xajax->configure> instead.
	*/
	function setLogFile($sFilename)
	{
		$this->configure('logFile', $sFilename);
	}

	/*
		Function: getLogFile

		Returns the current log file path.  See also <xajax->setLogFile>.

		Returns:

		string - The log file path.

		deprecated - This function will be removed in future versions.  Please
			use <xajax->getConfiguration> instead.
	*/
	function getLogFile()
	{
		return $this->getConfiguration('logFile');
	}

	/*
		Function: registerFunction

		Registers a PHP function or method with the xajax request processor.  This
		makes the function available to the browser via an asynchronous
		(or synchronous) javascript call.

		mFunction - (string or array):  The string containing the function name
			or an array containing the following:
			- (string) The function name as it will be called from javascript.
			- (object, by reference) A reference to an instance of a class
				containing the specified function.
			- (string) The function as it is found in the class passed in the second
				parameter.
		sIncludeFile - (string, optional):  The server path to the PHP file to
			include when calling this function.  This will enable xajax to load
			only the include file that is needed for this function call, thus
			reducing server load.

		Examples:
			- $xajax->registerFunction("myFunction");
			- $xajax->registerFunction(array("myFunctionName", &$myObject, "myMethod"));

		deprecated - This function will be removed in future versions.  Please
			use <xajax->register> instead.
	*/
	function registerFunction($mFunction, $sIncludeFile=null)
	{
		$xuf =& new xajaxUserFunction($mFunction, $sIncludeFile);
		return $this->register(XAJAX_FUNCTION, $xuf);
	}

	/*
		Function: registerCallableObject

		Registers an object whose methods will be searched for a match to the
		incoming request.  If more than one callable object is registered, the
		first on that contains the requested method will be used.

		oObject - (object, by reference):  The object whose methods will be
			registered.

		deprecated - This function will be removed in future versions.  Please
			use <xajax->register> instead.
	*/
	function registerCallableObject(&$oObject)
	{
		$mResult = false;
		
		if (0 > version_compare(PHP_VERSION, '5.0'))
			// for PHP4; using eval because PHP5 will complain it is deprecated
			eval('$mResult = $this->register(XAJAX_CALLABLE_OBJECT, &$oObject);');
		else
			// for PHP5
			$mResult = $this->register(XAJAX_CALLABLE_OBJECT, $oObject);
			
		return $mResult;
	}

	/*
		Function: registerEvent

		Assigns a callback function with the specified xajax event.  Events
		are triggered during the processing of a request.

		List: Available events:
			- beforeProcessing: triggered before the request is processed.
			- afterProcessing: triggered after the request is processed.
			- invalidRequest: triggered if no matching function/method is found.

		mCallback - (function): The function or object callback to be assigned.
		sEventName - (string): The name of the event.

		deprecated - This function will be removed in future versions.  Please
			use <xajax->register> instead.
	*/
	function registerEvent($sEventName, $mCallback)
	{
		$this->register(XAJAX_PROCESSING_EVENT, $sEventName, $mCallback);
	}

}

/*
	Section: Global functions
*/

/*
	Function xajaxErrorHandler

	This function is registered with PHP's set_error_handler if the xajax
	error handling system is enabled.

	See <xajax->bUserErrorHandler>
*/
function xajaxErrorHandler($errno, $errstr, $errfile, $errline)
{
	$errorReporting = error_reporting();
	if (($errno & $errorReporting) == 0) return;
	
	if ($errno == E_NOTICE) {
		$errTypeStr = 'NOTICE';
	}
	else if ($errno == E_WARNING) {
		$errTypeStr = 'WARNING';
	}
	else if ($errno == E_USER_NOTICE) {
		$errTypeStr = 'USER NOTICE';
	}
	else if ($errno == E_USER_WARNING) {
		$errTypeStr = 'USER WARNING';
	}
	else if ($errno == E_USER_ERROR) {
		$errTypeStr = 'USER FATAL ERROR';
	}
	else if (defined('E_STRICT') && $errno == E_STRICT) {
		return;
	}
	else {
		$errTypeStr = 'UNKNOWN: ' . $errno;
	}
	
	$sCrLf = "\n";
	
	ob_start();
	print $GLOBALS['xajaxErrorHandlerText'];
	print $sCrLf;
	print '----';
	print $sCrLf;
	print '[';
	print $errTypeStr;
	print '] ';
	print $errstr;
	print $sCrLf;
	print 'Error on line ';
	print $errline;
	print ' of file ';
	print $errfile;
	$GLOBALS['xajaxErrorHandlerText'] = ob_get_clean();
}

