<?php

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
class clsPreloader extends xajaxResponsePlugin
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
	
	
	var $aScripts = array();
	var $aImages = array();
	var $aStyles = array();
	/*
		Function: clsTableUpdater
		
		Constructs and initializes an instance of the table updater class.
	*/
	function clsPreloader()
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

			include(dirname(__FILE__) . 'xajax_plugins/response/preloader/preload.js');

			echo "/* ]]> */\n";
			echo "</script>\n";
		} else {
			echo "\n<script type='text/javascript' src='" . $this->sJavascriptURI . "xajax_plugins/response/preloader/preload.js' " . $this->sDefer . "charset='UTF-8'></script>\n";
			echo "\n<script  type='text/javascript'>";
			print "try {";
echo "
try {
	if (undefined == xajax.ext)
		xajax.ext = {};
} catch (e) {
}

try {
	if (undefined == xajax.ext.preloader)
		xajax.ext.preloader = {};
} catch (e) {
	alert('Could not create xajax.ext.preloader namespace');
}

";
			echo "xajax.ext.preloader.aScripts = ".json_encode($this->aScripts).";\n";
			echo "xajax.ext.preloader.aStyles = ".json_encode($this->aStyles).";\n";
			echo "xajax.ext.preloader.aImages = ".json_encode($this->aImages).";\n";
			echo "xajax.ext.preloader.ready=true;";
			echo "} catch(ex) { alert(ex);}";
			echo "</script>";
		}
	}
	
	function addScript($uri) {
		$this->aScripts[] = $uri;		
	}
	function addImages($uri) {
		$this->aImages[] = $uri;		
	}
	function addStyleSheet($uri) {
		$this->aStyles[] = $uri;		
	}


}

$objPluginManager =& xajaxPluginManager::getInstance();
$objPluginManager->registerPlugin(new clsPreloader());
