<?php
/*
	File: form.inc.php

	HTML Control Library - Form Level Tags

	Title: xajax HTML control class library

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: form.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Section: Description
	
	This file contains the class declarations for the following HTML Controls:
	
	- form
	- input, textarea, select, optgroup, option
	- label
	- fieldset, legend
	
	The following tags are deprecated as of HTML 4.01, therefore, they will not
	be supported:
	
	- isindex
*/

class clsForm extends xajaxControlContainer
{
	function clsForm($aConfiguration=array())
	{
		if (false == isset($aConfiguration['attributes']))
			$aConfiguration['attributes'] = array();
		if (false == isset($aConfiguration['attributes']['method']))
			$aConfiguration['attributes']['method'] = 'POST';
		if (false == isset($aConfiguration['attributes']['action']))
			$aConfiguration['attributes']['action'] = '#';

		xajaxControlContainer::xajaxControlContainer('form', $aConfiguration);
	}
}

class clsInput extends xajaxControl
{
	function clsInput($aConfiguration=array())
	{
		xajaxControl::xajaxControl('input', $aConfiguration);
	}
}

class clsInputWithLabel extends clsInput
{
	var $objLabel;
	var $sWhere;
	var $objBreak;
	
	function clsInputWithLabel($sLabel, $sWhere, $aConfiguration=array())
	{
		clsInput::clsInput($aConfiguration);
		
		$this->objLabel =& new clsLabel(array(
			'child' => new clsLiteral($sLabel)
			));
		$this->objLabel->setControl($this);
		
		$this->sWhere = $sWhere;
		
		$this->objBreak =& new clsBr();
	}
	
	function printHTML($sIndent='')
	{
		if ('left' == $this->sWhere || 'above' == $this->sWhere)
			$this->objLabel->printHTML($sIndent);
		if ('above' == $this->sWhere)
			$this->objBreak->printHTML($sIndent);
		
		clsInput::printHTML($sIndent);

		if ('below' == $this->sWhere)
			$this->objBreak->printHTML($sIndent);
		if ('right' == $this->sWhere || 'below' == $this->sWhere)
			$this->objLabel->printHTML($sIndent);
	}
}

/*
	Class: clsSelect
	
	A <xajaxControlContainer> derived class that assists in the construction
	of an HTML select control.
	
	This control can only accept <clsOption> controls as children.
*/
class clsSelect extends xajaxControlContainer
{
	/*
		Function: clsSelect
		
		Construct and initialize an instance of the class.  See <xajaxControlContainer>
		for details regarding the aConfiguration parameter.
	*/
	function clsSelect($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('select', $aConfiguration);
	}
	
	/*
		Function: addOption
		
		Used to add a single option to the options list.
		
		sValue - (string):  The value that is returned as the form value
			when this option is the selected option.
		sText - (string):  The text that is displayed in the select box when
			this option is the selected option.
	*/
	function addOption($sValue, $sText)
	{
		$optionNew =& new clsOption();
		$optionNew->setValue($sValue);
		$optionNew->setText($sText);
		$this->addChild($optionNew);
	}
	
	/*
		Function: addOptions
		
		Used to add a list of options.
		
		aOptions - (associative array):  A list of key/value pairs that will
			be passed to <clsSelect->addOption>.
	*/
	function addOptions($aOptions, $aFields=array())
	{
		if (0 == count($aFields))
			foreach ($aOptions as $sValue => $sText)
				$this->addOption($sValue, $sText);
		else if (1 < count($aFields))
			foreach ($aOptions as $aOption)
				$this->addOption($aOption[$aFields[0]], $aOption[$aFields[1]]);
		else
			trigger_error('Invalid list of fields passed to clsSelect::addOptions; should be array of two strings.'
				. $this->backtrace(),
				E_USER_ERROR
				);
	}
}

/*
	Class: clsOptionGroup
	
	A <xajaxControlContainer> derived class that can be used around a list of <clsOption>
	objects to help the user find items in a select list.
*/
class clsOptionGroup extends xajaxControlContainer
{
	function clsOptionGroup($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('optgroup', $aConfiguration);
	}
	
	/*
		Function: addOption
		
		Used to add a single option to the options list.
		
		sValue - (string):  The value that is returned as the form value
			when this option is the selected option.
		sText - (string):  The text that is displayed in the select box when
			this option is the selected option.
	*/
	function addOption($sValue, $sText)
	{
		$optionNew =& new clsOption();
		$optionNew->setValue($sValue);
		$optionNew->setText($sText);
		$this->addChild($optionNew);
	}
	
	/*
		Function: addOptions
		
		Used to add a list of options.
		
		aOptions - (associative array):  A list of key/value pairs that will
			be passed to <clsSelect->addOption>.
	*/
	function addOptions($aOptions, $aFields=array())
	{
		if (0 == count($aFields))
			foreach ($aOptions as $sValue => $sText)
				$this->addOption($sValue, $sText);
		else if (1 < count($aFields))
			foreach ($aOptions as $aOption)
				$this->addOption($aOption[$aFields[0]], $aOption[$aFields[1]]);
		else
			trigger_error('Invalid list of fields passed to clsOptionGroup::addOptions; should be array of two strings.'
				. $this->backtrace(),
				E_USER_ERROR
				);
	}
}

/*
	Class: clsOption
	
	A <xajaxControlContainer> derived class that assists with the construction
	of HTML option tags that will be assigned to an HTML select tag.

	This control can only accept <clsLiteral> objects as children.
*/
class clsOption extends xajaxControlContainer
{
	/*
		Function: clsOption
		
		Constructs and initializes an instance of this class.  See <xajaxControlContainer>
		for more information regarding the aConfiguration parameter.
	*/
	function clsOption($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('option', $aConfiguration);
	}
	
	/*
		Function: setValue
		
		Used to set the value associated with this option.  The value is sent as the
		value of the select control when this is the selected option.
	*/
	function setValue($sValue)
	{
		$this->setAttribute('value', $sValue);
	}
	
	/*
		Function: setText
		
		Sets the text to be shown in the select control when this is the 
		selected option.
	*/
	function setText($sText)
	{
		$this->clearChildren();
		$this->addChild(new clsLiteral($sText));
	}
}

class clsTextArea extends xajaxControlContainer
{
	function clsTextArea($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('textarea', $aConfiguration);
		
		$this->sClass = '%block';
	}
}

class clsLabel extends xajaxControlContainer
{
	var $objFor;

	function clsLabel($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('label', $aConfiguration);
	}

	function setControl(&$objControl)
	{
		if (false == is_a($objControl, 'xajaxControl'))
			trigger_error(
				'Invalid control passed to clsLabel::setControl(); should be xajaxControl.'
				. $this->backtrace(),
				E_USER_ERROR);

		$this->objFor =& $objControl;
	}

	function printHTML($sIndent='')
	{
		$this->aAttributes['for'] = $this->objFor->aAttributes['id'];
		
		xajaxControlContainer::printHTML($sIndent);
	}
}

class clsFieldset extends xajaxControlContainer
{
	function clsFieldset($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('fieldset', $aConfiguration);
		
		$this->sClass = '%block';
	}
}

class clsLegend extends xajaxControlContainer
{
	function clsLegend($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('legend', $aConfiguration);
		
		$this->sClass = '%inline';
	}
}
