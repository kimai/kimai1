<?php
/*
	File: group.inc.php

	HTML Control Library - Group Level Tags

	Title: xajax HTML control class library

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: group.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Section: Description
	
	This file contains the class declarations for the following HTML Controls:
	
	- ol, ul, li
	- dl, dt, dd
	- table, caption, colgroup, col, thead, tfoot, tbody, tr, td, th
	
	The following tags are deprecated as of HTML 4.01, therefore, they will not
	be supported:
	
	- dir, menu
*/

class clsList extends xajaxControlContainer
{
	function clsList($sTag, $aConfiguration=array())
	{
		$this->clearEvent_AddItem();
			
		xajaxControlContainer::xajaxControlContainer($sTag, $aConfiguration);

		$this->sClass = '%block';
	}
	
	function addItem($mItem, $mConfiguration=null)
	{
		if (null != $this->eventAddItem) {
			$objItem =& call_user_func($this->eventAddItem, $mItem, $mConfiguration);
			$this->addChild($objItem);
		} else {
			$objItem =& $this->_onAddItem($mItem, $mConfiguration);
			$this->addChild($objItem);
		}
	}
	
	function addItems($aItems, $mConfiguration=null)
	{
		foreach ($aItems as $mItem)
			$this->addItem($mItem, $mConfiguration);
	}
	
	function clearEvent_AddItem()
	{
		$this->eventAddItem = null;
	}
	
	function setEvent_AddItem($mFunction)
	{
		$this->eventAddItem = $mFunction;
	}
	
	function &_onAddItem($mItem, $mConfiguration)
	{
		$objItem =& new clsLI(array(
			'child' => new clsLiteral($mItem)
			));
		return $objItem;
	}
}

class clsUL extends clsList
{
	function clsUL($aConfiguration=array())
	{
		clsList::clsList('ul', $aConfiguration);
	}
}

class clsOL extends clsList
{
	function clsOL($aConfiguration=array())
	{
		clsList::clsList('ol', $aConfiguration);
	}
}

class clsLI extends xajaxControlContainer
{
	function clsLI($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('li', $aConfiguration);

		$this->sClass = '%flow';
		$this->sEndTag = 'optional';
	}
}

class clsDl extends xajaxControlContainer
{
	function clsDl($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('dl', $aConfiguration);

		$this->sClass = '%block';
	}
}

class clsDt extends xajaxControlContainer
{
	function clsDt($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('dt', $aConfiguration);

		$this->sClass = '%block';
		$this->sEndTag = 'optional';
	}
}

class clsDd extends xajaxControlContainer
{
	function clsDd($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('dd', $aConfiguration);

		$this->sClass = '%flow';
		$this->sEndTag = 'optional';
	}
}

class clsTableRowContainer extends xajaxControlContainer
{
	var $eventAddRow;
	var $eventAddRowCell;
	
	function clsTableRowContainer($sTag, $aConfiguration=array())
	{
		$this->clearEvent_AddRow();
		$this->clearEvent_AddRowCell();
			
		xajaxControlContainer::xajaxControlContainer($sTag, $aConfiguration);

		$this->sClass = '%block';
	}

	function addRow($aCells, $mConfiguration=null)
	{
		if (null != $this->eventAddRow) {
			$objRow =& call_user_func($this->eventAddRow, $aCells, $mConfiguration);
			$this->addChild($objRow);
		} else {
			$objRow =& $this->_onAddRow($aCells, $mConfiguration);
			$this->addChild($objRow);
		}
	}
		
	function addRows($aRows, $mConfiguration=null)
	{
		foreach ($aRows as $aCells)
			$this->addRow($aCells, $mConfiguration);
	}
	
	function clearEvent_AddRow()
	{
		$this->eventAddRow = null;
	}
	function clearEvent_AddRowCell()
	{
		$this->eventAddRowCell = null;
	}
	
	function setEvent_AddRow($mFunction)
	{
		$mPrevious = $this->eventAddRow;
		$this->eventAddRow = $mFunction;
		return $mPrevious;
	}
	function setEvent_AddRowCell($mFunction)
	{
		$mPrevious = $this->eventAddRowCell;
		$this->eventAddRowCell = $mFunction;
		return $mPrevious;
	}
	
	function &_onAddRow($aCells, $mConfiguration=null)
	{
		$objTableRow =& new clsTr();
		if (null != $this->eventAddRowCell)
			$objTableRow->setEvent_AddCell($this->eventAddRowCell);
		$objTableRow->addCells($aCells, $mConfiguration);
		return $objTableRow;
	}
}

/*
	Class: clsTable
	
	A <xajaxControlContainer> derived class that aids in the construction of HTML 
	tables.  Inherently, <xajaxControl> and <xajaxControlContainer> derived classes 
	support <xajaxRequest> based events using the <xajaxControl->setEvent> method.
*/
class clsTable extends xajaxControlContainer
{
	var $eventAddHeader;
	var $eventAddHeaderRow;
	var $eventAddHeaderRowCell;
	var $eventAddBody;
	var $eventAddBodyRow;
	var $eventAddBodyRowCell;
	var $eventAddFooter;
	var $eventAddFooterRow;
	var $eventAddFooterRowCell;
	
	/*
		Function: clsTable
		
		Constructs and initializes an instance of the class.
	*/
	function clsTable($aConfiguration=array())
	{
		$this->clearEvent_AddHeader();
		$this->clearEvent_AddHeaderRow();
		$this->clearEvent_AddHeaderRowCell();
		$this->clearEvent_AddBody();
		$this->clearEvent_AddBodyRow();
		$this->clearEvent_AddBodyRowCell();
		$this->clearEvent_AddFooter();
		$this->clearEvent_AddFooterRow();
		$this->clearEvent_AddFooterRowCell();
		
		xajaxControlContainer::xajaxControlContainer('table', $aConfiguration);

		$this->sClass = '%block';
	}

	function addHeader($aRows, $mConfiguration=null)
	{
		if (null != $this->eventAddHeader) {
			$objHeader =& call_user_func($this->eventAddHeader, $aRows, $mConfiguration);
			$this->addChild($objHeader);
		} else {
			$objHeader =& $this->_onAddHeader($aRows, $mConfiguration);
			$this->addChild($objHeader);
		}
	}
	function addBody($aRows, $mConfiguration=null)
	{
		if (null != $this->eventAddBody) {
			$objBody =& call_user_func($this->eventAddBody, $aRows, $mConfiguration);
			$this->addChild($objBody);
		} else {
			$objBody =& $this->_onAddBody($aRows, $mConfiguration);
			$this->addChild($objBody);
		}
	}
	function addFooter($aRows, $mConfiguration=null)
	{
		if (null != $this->eventAddFooter) {
			$objFooter =& call_user_func($this->eventAddFooter, $aRows, $mConfiguration);
			$this->addChild($objFooter);
		} else {
			$objFooter =& $this->_onAddFooter($aRows, $mConfiguration);
			$this->addChild($objFooter);
		}
	}
		
	function addBodies($aBodies, $mConfiguration=null)
	{
		foreach ($aBodies as $aRows)
			$this->addBody($aRows, $mConfiguration);
	}

	function clearEvent_AddHeader()
	{
		$this->eventAddHeader = null;
	}
	function clearEvent_AddHeaderRow()
	{
		$this->eventAddHeaderRow = null;
	}
	function clearEvent_AddHeaderRowCell()
	{
		$this->eventAddHeaderRowCell = null;
	}
	function clearEvent_AddBody()
	{
		$this->eventAddBody = null;
	}
	function clearEvent_AddBodyRow()
	{
		$this->eventAddBodyRow = null;
	}
	function clearEvent_AddBodyRowCell()
	{
		$this->eventAddBodyRowCell = null;
	}
	function clearEvent_AddFooter()
	{
		$this->eventAddFooter = null;
	}
	function clearEvent_AddFooterRow()
	{
		$this->eventAddFooterRow = null;
	}
	function clearEvent_AddFooterRowCell()
	{
		$this->eventAddFooterRowCell = null;
	}
	
	function setEvent_AddHeader($mFunction)
	{
		$mPrevious = $this->eventAddHeader;
		$this->eventAddHeader = $mFunction;
		return $mPrevious;
	}
	function setEvent_AddHeaderRow($mFunction)
	{
		$mPrevious = $this->eventAddHeaderRow;
		$this->eventAddHeaderRow = $mFunction;
		return $mPrevious;
	}
	function setEvent_AddHeaderRowCell($mFunction)
	{
		$mPrevious = $this->eventAddHeaderRowCell;
		$this->eventAddHeaderRowCell = $mFunction;
		return $mPrevious;
	}
	function setEvent_AddBody($mFunction)
	{
		$mPrevious = $this->eventAddBody;
		$this->eventAddBody = $mFunction;
		return $mPrevious;
	}
	function setEvent_AddBodyRow($mFunction)
	{
		$mPrevious = $this->eventAddBodyRow;
		$this->eventAddBodyRow = $mFunction;
		return $mPrevious;
	}
	function setEvent_AddBodyRowCell($mFunction)
	{
		$mPrevious = $this->eventAddBodyRowCell;
		$this->eventAddBodyRowCell = $mFunction;
		return $mPrevious;
	}
	function setEvent_AddFooter($mFunction)
	{
		$mPrevious = $this->eventAddFooter;
		$this->eventAddFooter = $mFunction;
		return $mPrevious;
	}
	function setEvent_AddFooterRow($mFunction)
	{
		$mPrevious = $this->eventAddFooterRow;
		$this->eventAddFooterRow = $mFunction;
		return $mPrevious;
	}
	function setEvent_AddFooterRowCell($mFunction)
	{
		$mPrevious = $this->eventAddFooterRowCell;
		$this->eventAddFooterRowCell = $mFunction;
		return $mPrevious;
	}
	
	function &_onAddHeader($aRows, $mConfiguration)
	{
		$objTableHeader =& new clsThead();
		if (null != $this->eventAddHeaderRow)
			$objTableHeader->setEvent_AddRow($this->eventAddHeaderRow);
		if (null != $this->eventAddHeaderRowCell)
			$objTableHeader->setEvent_AddRowCell($this->eventAddHeaderRowCell);
		$objTableHeader->addRows($aRows, $mConfiguration);
		return $objTableHeader;
	}
	function &_onAddBody($aRows, $mConfiguration)
	{
		$objTableBody =& new clsTbody();
		if (null != $this->eventAddBodyRow)
			$objTableBody->setEvent_AddRow($this->eventAddBodyRow);
		if (null != $this->eventAddBodyRowCell)
			$objTableBody->setEvent_AddRowCell($this->eventAddBodyRowCell);
		$objTableBody->addRows($aRows, $mConfiguration);
		return $objTableBody;
	}
	function &_onAddFooter($aRows, $mConfiguration)
	{
		$objTableFooter =& new clsTfoot();
		if (null != $this->eventAddFooterRow)
			$objTableFooter->setEvent_AddRow($this->eventAddFooterRow);
		if (null != $this->eventAddFooterRowCell)
			$objTableFooter->setEvent_AddRowCell($this->eventAddFooterRowCell);
		$objTableFooter->addRows($aRows, $mConfiguration);
		return $objTableFooter;
	}
}

class clsCaption extends xajaxControlContainer
{
	function clsCaption($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('caption', $aConfiguration);

		$this->sClass = '%block';
	}
}

class clsColgroup extends xajaxControlContainer
{
	function clsColgroup($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('colgroup', $aConfiguration);

		$this->sClass = '%block';
		$this->sEndTag = 'optional';
	}
}

class clsCol extends xajaxControl
{
	function clsCol($aConfiguration=array())
	{
		xajaxControl::xajaxControl('col');

		$this->sClass = '%block';
	}
}

/*
	Class: clsThead
*/
class clsThead extends clsTableRowContainer
{
	/*
		Function: clsThead
		
		Constructs and initializes an instance of the class.
	*/
	function clsThead($aConfiguration=array())
	{
		clsTableRowContainer::clsTableRowContainer('thead', $aConfiguration);
	}
}

/*
	Class: clsTbody
*/
class clsTbody extends clsTableRowContainer
{
	/*
		Function: clsTbody
		
		Constructs and initializes an instance of the class.
	*/
	function clsTbody($aConfiguration=array())
	{
		clsTableRowContainer::clsTableRowContainer('tbody', $aConfiguration);
	}
}

/*
	Class: clsTfoot
*/
class clsTfoot extends clsTableRowContainer
{
	/*
		Function: clsTfoot
		
		Constructs and initializes an instance of the class.
	*/
	function clsTfoot($aConfiguration=array())
	{
		clsTableRowContainer::clsTableRowContainer('tfoot', $aConfiguration);
	}
}

/*
	Class: clsTr
*/
class clsTr extends xajaxControlContainer
{
	var $eventAddCell;
	
	/*
		Function: clsTr
		
		Constructs and initializes an instance of the class.
	*/
	function clsTr($aConfiguration=array())
	{
		$this->clearEvent_AddCell();
			
		xajaxControlContainer::xajaxControlContainer('tr', $aConfiguration);

		$this->sClass = '%block';
	}
	
	function addCell($mCell, $mConfiguration=null)
	{
		if (null != $this->eventAddCell) {
			$objCell =& call_user_func($this->eventAddCell, $mCell, $mConfiguration);
			$this->addChild($objCell);
		} else {
			$objCell =& $this->_onAddCell($mCell, $mConfiguration);
			$this->addChild($objCell);
		}
	}
	
	function addCells($aCells, $mConfiguration=null)
	{
		foreach ($aCells as $mCell)
			$this->addCell($mCell, $mConfiguration);
	}
	
	function clearEvent_AddCell()
	{
		$this->eventAddCell = null;
	}
	
	function setEvent_AddCell($mFunction)
	{
		$mPrevious = $this->eventAddCell;
		$this->eventAddCell = $mFunction;
		return $mPrevious;
	}
	
	function &_onAddCell($mCell, $mConfiguration=null)
	{
		return new clsTd(array(
			'child' => new clsLiteral($mCell)
			));
	}
}

/*
	Class: clsTd
*/
class clsTd extends xajaxControlContainer
{
	/*
		Function: clsTd
		
		Constructs and initializes an instance of the class.
	*/
	function clsTd($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('td', $aConfiguration);

		$this->sClass = '%flow';
	}
}

/*
	Class: clsTh
*/
class clsTh extends xajaxControlContainer
{
	/*
		Function: clsTh
		
		Constructs and initializes an instance of the class.
	*/
	function clsTh($aConfiguration=array())
	{
		xajaxControlContainer::xajaxControlContainer('th', $aConfiguration);

		$this->sClass = '%flow';
	}
}
