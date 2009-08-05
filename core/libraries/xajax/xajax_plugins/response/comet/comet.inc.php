<?php
		define('FILE_APPEND', 1);

/*
	File: tableUpdater.inc.php

	Contains a class that can be used to invoke DOM calls on the browser which
	will create or update an HTML table.

	Title: clsTableUpdater class

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

if (false == class_exists('xajaxPlugin') || false == class_exists('xajaxPluginManager'))
{
	$sBaseFolder = dirname(dirname(dirname(__FILE__)));
	$sXajaxCore = $sBaseFolder . '/xajax_core';

	if (false == class_exists('xajaxPlugin'))
		require $sXajaxCore . '/xajaxPlugin.inc.php';
	if (false == class_exists('xajaxPluginManager'))
		require $sXajaxCore . '/xajaxPluginManager.inc.php';
}

//require_once dirname(__FILE__) . '/xajaxCometPlugin.inc.php';

/*
	Class: clsTableUpdater
*/
class clsCometStreaming extends xajaxResponsePlugin
{
	/*
		String: sDefer
		
		Used to store the state of the scriptDeferral configuration setting.  When
		script deferral is desired, this member contains 'defer' which will request
		that the browser defer loading of the javascript until the rest of the page 
		has been loaded.
	*/
	var $sDefer;
	
	/*
		String: sJavascriptURI
		
		Used to store the base URI for where the javascript files are located.  This
		enables the plugin to generate a script reference to it's javascript file
		if the javascript code is NOT inlined.
	*/
	var $sJavascriptURI;
	
	/*
		Boolean: bInlineScript
		
		Used to store the value of the inlineScript configuration option.  When true,
		the plugin will return it's javascript code as part of the javascript header
		for the page, else, it will generate a script tag referencing the file by
		using the <clsTableUpdater->sJavascriptURI>.
	*/
	var $bInlineScript;
	
	/*
		Function: clsTableUpdater
		
		Constructs and initializes an instance of the table updater class.
	*/
	function clsCometStreaming()
	{
		$this->sDefer = '';
		$this->sJavascriptURI = '';
		$this->bInlineScript = false;
	}
	
	/*
		Function: configure
		
		Receives configuration settings set by <xajax> or user script calls to 
		<xajax->configure>.
		
		sName - (string):  The name of the configuration option being set.
		mValue - (mixed):  The value being associated with the configuration option.
	*/
	function configure($sName, $mValue)
	{
		if ('scriptDeferral' == $sName) {
			if (true === $mValue || false === $mValue) {
				if ($mValue) $this->sDefer = 'defer ';
				else $this->sDefer = '';
			}
		} else if ('javascript URI' == $sName) {
			$this->sJavascriptURI = $mValue;
		} else if ('inlineScript' == $sName) {
			if (true === $mValue || false === $mValue)
				$this->bInlineScript = $mValue;
		}
	}
	
	/*
		Function: generateClientScript
		
		Called by the <xajaxPluginManager> during the script generation phase.  This
		will either inline the script or insert a script tag which references the
		<tableUpdater.js> file based on the value of the <clsTableUpdater->bInlineScript>
		configuration option.
	*/
	function generateClientScript()
	{
		if ($this->bInlineScript)
		{
			echo "\n<script type='text/javascript' " . $this->sDefer . "charset='UTF-8'>\n";
			echo "/* <![CDATA[ */\n";

			include(dirname(__FILE__) . 'xajax_plugins/response/comet/comet.js');

			echo "/* ]]> */\n";
			echo "</script>\n";
		} else {
			echo "\n<script type='text/javascript' src='" . $this->sJavascriptURI . "xajax_plugins/response/comet/comet.js' " . $this->sDefer . "charset='UTF-8'></script>\n";
		}
	}
	
	
}

class xajaxCometResponse extends xajaxResponse 
{
	var $bHeaderSent = false;


	/*
		Function: xajaxCometResponse
		
		calls  parent function xajaxResponse();
	*/
	
	
	function xajaxCometResponse()
	{
		parent::xajaxResponse();		
	}

	/*
		Function: printOutput
		
		override the original printOutput function. It's no longer needed since the output is already sent.
	*/

	function printOutput()
	{
		if ( "HTML5DRAFT" == $_GET['xjxstreaming']) {

			$response = "";
		  $response .= "Event: xjxendstream\n";
	    $response .=  "data: done\n";
	    $response .= "\n";
			print $response;
			
		}
		
	}

	/*
		Function: flush_XHR
		
		Flushes the command queue for comet browsers.
	*/

	function flush_XHR() 
	{
		
		if (!$this->bHeaderSent) 
		{
			$this->_sendHeaders();
			$this->bHeaderSent=true;
		}
		
		ob_start();
		$this->_printResponse_XML();
		$c = ob_get_contents();
		ob_get_clean();
		$c = str_replace(chr(1)," ",$c);
		$c = str_replace(chr(2)," ",$c);
		$c = str_replace(chr(31)," ",$c);
		$c = str_replace(""," ",$c);
		if ($c == "<xjx></xjx>") return false;
		print $c;
		ob_flush();
		flush();
		$this->sleep(1.1);
	}
	

	/*
		Function: flush_activeX
		
		Flushes the command queue for ActiveX browsers.
	*/

	function flush_activeX() 
	{
		ob_start();
		$this->_printResponse_XML();
		$c = ob_get_contents();
		ob_get_clean();
		
		$c = '<?xml version="1.0" ?>'.$c;
		$c = str_replace('"','\"',$c);
		$c = str_replace("\n",'\n',$c);
		$c = str_replace("\r",'\r',$c);

		$response = "";
		$response .= "<script>top.document.callback(\"";
		$response .= $c;
		$response .= "\");</script>";
		
		print $response;
		ob_flush();
		flush();
		$this->sleep(0.99);
	}

	/*
		Function: flush_HTML5DRAFT
		
		Flushes the command queue for HTML5DRAFT browsers.
	*/

	function flush_HTML5DRAFT() 
	{


		if (!$this->bHeaderSent) 
		{
			header("Content-Type: application/x-dom-event-stream");
			$this->bHeaderSent=1;
		}
		
		ob_start();
		$this->_printResponse_XML();
		$c = ob_get_contents();
		ob_get_clean();
		$c = str_replace("\n",'\n',$c);
		$c = str_replace("\r",'\r',$c);
		$response = "";
	  $response .= "Event: xjxstream\n";
    $response .=  "data: $c\n";
    $response .= "\n";
		print $response;
		ob_flush();
		flush();
		$this->sleep(1);
		
	}


	/*
		Function: flush
		
		Determines which browser is wating for a response and calls the according flush function.
	*/
	function flush() 
	{
		if (0 == count($this->aCommands)) return false;
		if ("xhr" == $_SERVER['HTTP_STREAMING']) 
		{
			$this->flush_XHR();
		} 
		elseif ( "HTML5DRAFT" == $_GET['xjxstreaming'])
		{
			$this->flush_HTML5DRAFT();
		}
		else
		{
			$this->flush_activeX();
		}
		$this->aCommands=array();
	}
 
	/*
		Function: sleep
		
		Very accurate sleep function.
	*/
	function sleep($seconds) 
	{
	   usleep(floor($seconds*1000000));
	}
	

	function file_put_contents($n, $d, $flag = false) 
	{
	    $mode = ($flag == FILE_APPEND || strtoupper($flag) == 'FILE_APPEND') ? 'a' : 'w';
	    $f = @fopen($n, $mode);
	    if ($f === false) 
	    {
	        return 0;
	    } else 
	    {
	        if (is_array($d)) $d = implode($d);
	        $bytes_written = fwrite($f, $d);
	        fclose($f);
	        return $bytes_written;
	    }
	}



}

$objPluginManager =& xajaxPluginManager::getInstance();
$objPluginManager->registerPlugin(new clsCometStreaming());
