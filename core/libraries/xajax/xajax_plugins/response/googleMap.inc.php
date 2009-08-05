<?php
/*
	File: googleMap.inc.php

	Contains a class that can be used to invoke DOM calls on the browser which
	will create or update a google map.

	Title: clsGoogleMap class

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

//$sBaseFolder = dirname(dirname(dirname(__FILE__)));
//$sXajaxCore = $sBaseFolder . '/xajax_core';

//require $sXajaxCore . '/xajaxPlugin.inc.php';
//require $sXajaxCore . '/xajaxPluginManager.inc.php';

/*
	Class: clsGoogleMap
*/
class clsGoogleMap extends xajaxResponsePlugin
{
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
		String: sGoogleSiteKey
		
		The key that google has assigned to your site.  Set this with <clsGoogleMap->setKey>
	*/
	
	/*
		Function: clsTableUpdater
		
		Constructs and initializes an instance of the table updater class.
	*/
	function clsGoogleMap()
	{
		$this->sJavascriptURI = '';
		$this->bInlineScript = true;
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
		if ('javascript URI' == $sName) {
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
		echo "\n<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=";
		echo $this->sGoogleSiteKey;
		echo "' type='text/javascript'>\n</script>\n";

		echo "\n<script type='text/javascript' charset='UTF-8'>\n";
		echo "/* <![CDATA[ */\n";

		echo "maps = {};\n";
		
		echo "xajax.commands['gm:cr'] = function(args) {\n";
		echo "\tmaps[args.data] = new GMap2(args.objElement);\n";
		echo "\tvar ptCenter = new GLatLng(0, 10);\n";
		echo "\tmaps[args.data].setCenter(ptCenter, 10);\n";
		echo "\tmaps[args.data].addControl(new GSmallMapControl());\n";
		echo "\tmaps[args.data].addControl(new GMapTypeControl());\n";
		echo "\tmaps[args.data].setMapType(maps[args.data].getMapTypes()[2]);\n";
		echo "}\n";
		
		echo "xajax.commands['gm:zm'] = function(args) {\n";
		echo "\tmaps[args.id].setZoom(parseInt(args.data));\n";
		echo "}\n";
		
		echo "xajax.commands['gm:sm'] = function(args) {\n";
		echo "\tvar ptCenter = new GLatLng(args.data[0], args.data[1]);\n";
		echo "\tvar markerNew = new GMarker(ptCenter);\n";
		echo "\tmarkerNew.text = args.data[2];\n";
		echo "\tmaps[args.id].addOverlay(markerNew);\n";
		echo "\tGEvent.addListener(maps[args.id], 'click', function(marker, point) {\n";
		echo "\t\tif (marker && undefined != marker.openInfoWindowHtml) {\n";
		echo "\t\t\tmarker.openInfoWindowHtml(marker.text);\n";
		echo "\t\t}\n";
		echo "\t} );\n";
		echo "}\n";

		echo "/* ]]> */\n";
		echo "</script>\n";
	}
	
	function getName()
	{
		return get_class($this);
	}
	
	function setGoogleSiteKey($sKey)
	{
		$this->sGoogleSiteKey = $sKey;
	}
	
	function create($sMap, $sParentId)
	{
		$command = array('n'=>'gm:cr', 't'=>$sParentId);
		$this->addCommand($command, $sMap);	
	}
	function zoom($sMap, $nZoom) {
		$command = array('n'=>'gm:zm', 't'=>$sMap);
		$this->addCommand($command, $nZoom);
	}
	function setMarker($sMap, $nLat, $nLon, $sText) {
		$this->addCommand(
			array('n'=>'gm:sm', 't'=>$sMap), 
			array($nLat, $nLon, $sText)
			);
	}
	function moveTo($sMap, $nLat, $nLon) {
//	39.928005, 
//	-82.70784, 
//	15);
		$command = array('n'=>'et_ar', 't'=>$parent);
		if (null != $position)
			$command['p'] = $position;
		$this->addCommand($command, $row);
	}
}

$objPluginManager =& xajaxPluginManager::getInstance();
$objPluginManager->registerPlugin(new clsGoogleMap());
