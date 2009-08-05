<?php
/*
	File: TabManager.inc.php

	Contains a class that can be used to invoke DOM calls on the browser which
	will create or update an HTML table.

	Title: clsTabManager class

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

if ( false == class_exists( 'xajaxPlugin' ) || false == class_exists( 'xajaxPluginManager' ) )
{
	$sBaseFolder = dirname( dirname( dirname( __FILE__ ) ) );
	$sXajaxCore = $sBaseFolder.'/xajax_core';

	if ( false == class_exists( 'xajaxPlugin' ) )
		require $sXajaxCore.'/xajaxPlugin.inc.php';

	if ( false == class_exists( 'xajaxPluginManager' ) )
		require $sXajaxCore.'/xajaxPluginManager.inc.php';
}

/*
	Class: clsTabManager
*/
class clsTabManager
	extends xajaxResponsePlugin
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
		using the <clsTabManager->sJavascriptURI>.
	*/
	var $bInlineScript;

	/*
		Function: clsTabManager
		
		Constructs and initializes an instance of the table updater class.
	*/
	function clsTabManager()
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
	function configure( $sName, $mValue )
	{
		if ( 'scriptDeferral' == $sName )
		{
			if ( true === $mValue || false === $mValue )
			{
				if ( $mValue )
					$this->sDefer = 'defer ';
				else
					$this->sDefer = '';
			}
		}
		else if ( 'javascript URI' == $sName )
		{
			$this->sJavascriptURI = $mValue;
		}else if ( 'inlineScript' == $sName )
		{
			if ( true === $mValue || false === $mValue )
				$this->bInlineScript = $mValue;
		}
	}

	/*
		Function: generateClientScript
		
		Called by the <xajaxPluginManager> during the script generation phase.  This
		will either inline the script or insert a script tag which references the
		<TabManager.js> file based on the value of the <clsTabManager->bInlineScript>
		configuration option.
	*/
	function generateClientScript()
	{
		if ( $this->bInlineScript )
		{
			echo "\n<script type='text/javascript' ".$this->sDefer."charset='UTF-8'>\n";

			echo "/* <![CDATA[ */\n";

			include( dirname( __FILE__ ).'/TabManager.js' );

			echo "/* ]]> */\n";

			echo "</script>\n";
		}else
		{
			echo "\n<script type='text/javascript' src='".$this->sJavascriptURI."xajax_plugins/response/TabManager/TabManager.js' ".$this->sDefer."charset='UTF-8'></script>\n";
		}
	}

	function getName()
	{
		return get_class( $this );
	}

	function create( $id, $config )
	{
		$command = array
		(
			'cmd' => 'tm_create',
			'id' => $id
		);

		$this->addCommand( $command, $config );
	}

	function addPanel( $id, $config )
	{
		$command = array
		(
			'cmd' => 'tm_at',
			'id' => $id
		);

		$this->addCommand( $command, $config );
	}

	function on( $eventName, $target, $panel, $event )
	{
		$command = array
		(
			'cmd' => 'tm_on',
			'id' => $target
		);

		$this->addCommand( $command, array
		(
			"n" => $eventName,
			"p" => $panel,
			"e" => $event,
			"key" => crc32($event)
		));
	}

	function close( $id, $panel )
	{
		$command = array
		(
			'cmd' => 'tm_cl',
			'id' => $id
		);

		$this->addCommand( $command, $panel );
	}

	function showPanel( $id, $panel )
	{
		$command = array
		(
			'cmd' => 'tm_sp',
			'id' => $id
		);

		$this->addCommand( $command, $panel );
	}

	function setTitle( $id, $panel, $title )
	{
		$command = array
		(
			'cmd' => 'tm_st',
			'id' => $id
		);

		$this->addCommand( $command, array
		(
			"panel" => $panel,
			"title" => $title
		));
	}

	function destroy( $id )
	{
		$command = array
		(
			'cmd' => 'tm_de',
			'id' => $id
		);

		$this->addCommand( $command, array ());
	}
}

$objPluginManager = &xajaxPluginManager::getInstance();
$objPluginManager->registerPlugin( new clsTabManager() );
?>