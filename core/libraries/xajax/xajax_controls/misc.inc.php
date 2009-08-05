<?php
/*
	File: misc.inc.php

	HTML Control Library - Miscellaneous Tags

	Title: xajax HTML control class library

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: misc.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Section: Description
	
	This file contains the class declarations for the following HTML Controls:
	
	(whatever does not fit elsewhere)
	
	- object, param
	- a, img, button
	- area, map
	
	The following tags are deprecated as of HTML 4.01, therefore, they will not be
	supported:
	
	- applet, embed
*/

class clsObject extends xajaxControlContainer
{
	function clsObject($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('object', $aConfiguration);

		$this->sClass = '%block';
		$this->sEndTag = 'required';
	}
}

class clsParam extends xajaxControl
{
	function clsParam($aConfiguration=array())
	{
		xajaxControl::xajaxControl('param', $aConfiguration);

		$this->sClass = '%block';
	}
}

class clsAnchor extends xajaxControlContainer
{
	function clsAnchor($aConfiguration=array())
	{
		if (false == isset($aConfiguration['attributes']))
			$aConfiguration['attributes'] = array();
		if (false == isset($aConfiguration['attributes']['href']))
			$aConfiguration['attributes']['href'] = '#';
		
		xajaxControlContainer::xajaxControlContainer('a', $aConfiguration);

		$this->sClass = '%inline';
		$this->sEndTag = 'required';
	}

	function setEvent($sEvent, &$objRequest, $aParameters=array(), $sBeforeRequest='if (false == ', $sAfterRequest=') return false; ')
	{
		xajaxControl::setEvent($sEvent, $objRequest, $aParameters, $sBeforeRequest, $sAfterRequest);
	}
}

class clsImg extends xajaxControl
{
	function clsImg($aConfiguration=array())
	{
		xajaxControl::xajaxControl('img', $aConfiguration);

		$this->sClass = '%inline';
	}
}

class clsButton extends xajaxControlContainer
{
	function clsButton($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('button', $aConfiguration);

		$this->sClass = '%inline';
	}
}

class clsArea extends xajaxControl
{
	function clsArea($aConfiguration=array())
	{
		xajaxControl::xajaxControl('area', $aConfiguration);

		$this->sClass = '%block';
	}
}

class clsMap extends xajaxControlContainer
{
	function clsMap($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('map', $aConfiguration);

		$this->sClass = '%block';
	}
}
