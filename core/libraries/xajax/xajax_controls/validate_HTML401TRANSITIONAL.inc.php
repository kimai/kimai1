<?php

$aAttributes = array(
	'%bodycolors' => array(
		'bgcolor',
		'text',
		'link',
		'vlink',
		'alink'
		),
	'%coreattrs' => array(
		'id',
		'class',
		'style',
		'title'
		),
	'%i18n' => array(
		'lang',
		'dir'
		),
	'%events' => array(
		'onclick',
		'ondblclick',
		'onmousedown',
		'onmouseup',
		'onmouseover',
		'onmousemove',
		'onmouseout',
		'onkeypress',
		'onkeydown',
		'onkeyup'
		),
	'%attrs' => array(
		'%coreattrs',
		'%i18n',
		'%events'
		),
	'%align' => array(
		'left',
		'center',
		'right',
		'justify'
		),
	'%cellhalign' => array(
		'align'
		),
	'%cellvalign' => array(
		'valign'
		),
	'TT' => array('%attrs'),
	'I' => array('%attrs'),
	'B' => array('%attrs'),
	'U' => array('%attrs'),
	'S' => array('%attrs'),
	'STRIKE' => array('%attrs'),
	'BIG' => array('%attrs'),
	'SMALL' => array('%attrs'),
	'EM' => array('%attrs'),
	'STRONG' => array('%attrs'),
	'DFN' => array('%attrs'),
	'CODE' => array('%attrs'),
	'SAMP' => array('%attrs'),
	'KBD' => array('%attrs'),
	'VAR' => array('%attrs'),
	'CITE' => array('%attrs'),
	'ABBR' => array('%attrs'),
	'ACRONYM' => array('%attrs'),
	'SUB' => array('%attrs'),
	'SUP' => array('%attrs'),
	'SPAN' => array(
		'%attrs'
//		,
//		'%reserved'
		),
	'BDO' => array(
		'%coreattrs',
		'lang',
		'dir'
		),
	'BASEFONT' => array(
		'id',
		'size',
		'color',
		'face'
		),
	'FONT' => array(
		'%coreattrs',
		'%i18n',
		'size',
		'color',
		'face'
		),
	'BR' => array(
		'%coreattrs',
		'clear'
		),
	'BODY' => array(
		'%attrs',
		'onload',
		'onunload',
		'background',
		'%bodycolors'
		),
	'ADDRESS' => array('%attrs'),
	'DIV' => array(
		'%attrs',
		'%align'
//		,
//		'%reserved'
		),
	'CENTER' => array('%attrs'),
	'A' => array(
		'%attrs',
		'charset',
		'type',
		'name',
		'href',
		'hreflang',
		'target',
		'rel',
		'rev',
		'accesskey',
		'shape',
		'coords',
		'tabindex',
		'onfocus',
		'onblur'
		),
	'MAP' => array(
		'%attrs',
		'name'
		),
	'AREA' => array(
		'%attrs',
		'shape',
		'coords',
		'href',
		'target',
		'nohref',
		'alt',
		'tabindex',
		'accesskey',
		'onfocus',
		'onblur'
		),
	'LINK' => array(
		'%attrs',
		'charset',
		'href',
		'hreflang',
		'type',
		'rel',
		'rev',
		'media',
		'target'
		),
	'IMG' => array(
		'%attrs',
		'src',
		'alt',
		'longdesc',
		'name',
		'height',
		'width',
		'usemap',
		'ismap',
		'align',
		'border',
		'hspace',
		'vspace'
		),
	'OBJECT' => array(
		'%attrs',
		'declare',
		'classid',
		'codebase',
		'data',
		'type',
		'codetype',
		'archive',
		'standby',
		'height',
		'width',
		'usemap',
		'name',
		'tabindex',
		'align',
		'border',
		'hspace',
		'vspace'
//		,
//		'%reserved'
		),
	'PARAM' => array(
		'id',
		'name',
		'value',
		'valuetype',
		'type'
		),
	'APPLET' => array(
		'%coreattrs',
		'codebase',
		'archive',
		'code',
		'object',
		'alt',
		'name',
		'width',
		'height',
		'align',
		'hspace',
		'vspace'
		),
	'HR' => array(
		'%attrs',
		'align',
		'noshade',
		'size',
		'width'
		),
	'P' => array(
		'%attrs',
		'%align'
		),
	'H1' => array(
		'%attrs',
		'%align'
		),
	'H2' => array(
		'%attrs',
		'%align'
		),
	'H3' => array(
		'%attrs',
		'%align'
		),
	'H4' => array(
		'%attrs',
		'%align'
		),
	'H5' => array(
		'%attrs',
		'%align'
		),
	'H6' => array(
		'%attrs',
		'%align'
		),
	'PRE' => array(
		'%attrs',
		'width'
		),
	'Q' => array(
		'%attrs',
		'cite'
		),
	'BLOCKQUOTE' => array(
		'%attrs',
		'cite'
		),
	'INS' => array(
		'%attrs',
		'cite',
		'datetime'
		),
	'DEL' => array(
		'%attrs',
		'cite',
		'datetime'
		),
	'DL' => array(
		'%attrs',
		'compact'
		),
	'DT' => array('%attrs'),
	'DD' => array('%attrs'),
	'OL' => array(
		'%attrs',
		'type',
		'compact',
		'start'
		),
	'UL' => array(
		'%attrs',
		'type',
		'compact'
		),
	'DIR' => array(
		'%attrs',
		'compact'
		),
	'MENU' => array(
		'%attrs',
		'compact'
		),
	'LI' => array(
		'%attrs',
		'type',
		'value'
		),
	'FORM' => array(
		'%attrs',
		'action',
		'method',
		'enctype',
		'accept',
		'name',
		'onsubmit',
		'onreset',
		'target',
		'accept-charset'
		),
	'LABEL' => array(
		'%attrs',
		'for',
		'accesskey',
		'onfocus',
		'onblur'
		),
	'INPUT' => array(
		'%attrs',
		'type',
		'name',
		'value',
		'checked',
		'disabled',
		'readonly',
		'size',
		'maxlength',
		'src',
		'alt',
		'usemap',
		'ismap',
		'tabindex',
		'accesskey',
		'onfocus',
		'onblur',
		'onselect',
		'onchange',
		'accept',
		'align'
//		,
//		'%reserved'
		),
	'SELECT' => array(
		'%attrs',
		'name',
		'size',
		'multiple',
		'disabled',
		'tabindex',
		'onfocus',
		'onblur',
		'onchange'
//		,
//		'%reserved'
		),
	'OPTGROUP' => array(
		'%attrs',
		'disabled',
		'label'
		),
	'OPTION' => array(
		'%attrs',
		'selected',
		'disabled',
		'label',
		'value'
		),
	'TEXTAREA' => array(
		'%attrs',
		'name',
		'rows',
		'cols',
		'disabled',
		'readonly',
		'tabindex',
		'accesskey',
		'onfocus',
		'onblur',
		'onselect',
		'onchange'
//		,
//		'%reserved'
		),
	'FIELDSET' => array('%attrs'),
	'LEGEND' => array(
		'%attrs',
		'accesskey',
		'align'
		),
	'BUTTON' => array(
		'%attrs',
		'name',
		'value',
		'type',
		'disabled',
		'tabindex',
		'accesskey',
		'onfocus',
		'onblur'
//		,
//		'%reserved'
		),
	'TABLE' => array(
		'%attrs',
		'summary',
		'width',
		'border',
		'frame',
		'rules',
		'cellspacing',
		'cellpadding',
		'align',
		'bgcolor',
//		'%reserved',
		'datapagesize'
		),
	'CAPTION' => array('%attrs', 'align'),
	'COLGROUP' => array(
		'%attrs',
		'span',
		'width',
		'%cellhalign',
		'%cellvalign'
		),
	'COL' => array(
		'%attrs',
		'span',
		'width',
		'%cellhalign',
		'%cellvalign'
		),
	'THEAD' => array(
		'%attrs',
		'%cellhalign',
		'%cellvalign'
		),
	'TBODY' => array(
		'%attrs',
		'%cellhalign',
		'%cellvalign'
		),
	'TFOOT' => array(
		'%attrs',
		'%cellhalign',
		'%cellvalign'
		),
	'TR' => array(
		'%attrs',
		'%cellhalign',
		'%cellvalign',
		'bgcolor'
		),
	'TH' => array(
		'%attrs',
		'abbr',
		'axis',
		'headers',
		'scope',
		'rowspan',
		'colspan',
		'%cellhalign',
		'%cellvalign',
		'nowrap',
		'bgcolor',
		'width',
		'height'
		),
	'TD' => array(
		'%attrs',
		'abbr',
		'axis',
		'headers',
		'scope',
		'rowspan',
		'colspan',
		'%cellhalign',
		'%cellvalign',
		'nowrap',
		'bgcolor',
		'width',
		'height'
		),
	'IFRAME' => array(
		'%coreattrs',
		'longdesc',
		'name',
		'src',
		'frameborder',
		'marginwidth',
		'marginheight',
		'scrolling',
		'align',
		'height',
		'width'
		),
	'NOFRAMES' => array('%attrs'),
	'HEAD' => array(
		'%i18n',
		'profile'
		),
	'TITLE' => array('%i18n'),
	'BASE' => array(
		'href',
		'target'
		),
	'META' => array(
		'%i18n',
		'http-equiv',
		'name',
		'content',
		'scheme'
		),
	'STYLE' => array(
		'%i18n',
		'type',
		'media',
		'title'
		),
	'SCRIPT' => array(
		'charset',
		'type',
		'language',
		'src',
		'defer',
		'event',
		'for'
		),
	'NOSCRIPT' => array('%attrs'),
	'HTML' => array('%i18n') // , '%version')
	);

$aTags = array(
	'%heading' => array(
		'H1',
		'H2',
		'H3',
		'H4',
		'H5',
		'H6'
		),
	'%list' => array(
		'UL',
		'OL',
		'DIR',
		'MENU'
		),
	'%preformatted' => array('PRE'),
	'%fontstyle' => array(
		'TT',
		'I',
		'B',
		'U',
		'S',
		'STRIKE',
		'BIG',
		'SMALL'
		),
	'%phrase' => array(
		'EM',
		'STRONG',
		'DFN',
		'CODE',
		'SAMP',
		'KBD',
		'VAR',
		'CITE',
		'ABBR',
		'ACRONYM'
		),
	'%special' => array(
		'A',
		'IMG',
		'APPLET',
		'OBJECT',
		'FONT',
		'BASEFONT',
		'BR',
		'SCRIPT',
		'MAP',
		'Q',
		'SUB',
		'SUP',
		'SPAN',
		'BDO',
		'IFRAME'
		),
	'%formctrl' => array(
		'INPUT',
		'SELECT',
		'TEXTAREA',
		'LABEL',
		'BUTTON'
		),
	'%inline' => array(
		'CDATA',
		'%fontstyle',
		'%phrase',
		'%special',
		'%formctrl'
		),
	'%block' => array(
		'P',
		'%heading',
		'%list',
		'%preformatted',
		'DL',
		'DIV',
		'CENTER',
		'NOSCRIPT',
		'NOFRAMES',
		'BLOCKQUOTE',
		'INS',
		'DEL',
		'FORM',
		'ISINDEX',
		'HR',
		'TABLE',
		'FIELDSET',
		'ADDRESS'
		),
	'%flow' => array(
		'%block',
		'%inline'
		),
		
	'TT' => array('%inline'),	// fontstyle
	'I' => array('%inline'),
	'B' => array('%inline'),
	'U' => array('%inline'),
	'S' => array('%inline'),
	'STRIKE' => array('%inline'),
	'BIG' => array('%inline'),
	'SMALL' => array('%inline'),
	
	'EM' => array('%inline'),	// phrase
	'STRONG' => array('%inline'),
	'DFN' => array('%inline'),
	'CODE' => array('%inline'),
	'SAMP' => array('%inline'),
	'KBD' => array('%inline'),
	'VAR' => array('%inline'),
	'CITE' => array('%inline'),
	'ABBR' => array('%inline'),
	'ACRONYM' => array('%inline'),
		
	'SUB' => array('%inline'),
	'SUP' => array('%inline'),
	'SPAN' => array('%inline'),
	'BDO' => array('%inline'),
	'BASEFONT' => array(),
	'FONT' => array('%inline'),
	'BR' => array(),
	'BODY' => array(
		'%flow',
		'INS',
		'DEL'
		),
	'ADDRESS' => array(
		'%inline',
		'P'
		),
	'DIV' => array('%flow'),
	'CENTER' => array('%flow'),
	'A' => array('%inline'),
	'MAP' => array(
		'%block',
		'AREA'
		),
	'AREA' => array(),
	'LINK' => array(),
	'IMG' => array(),
	'OBJECT' => array(
		'PARAM',
		'%flow'
		),
	'PARAM' => array(),
	'HR' => array(),
	'P' => array('%inline'),
	'H1' => array('%inline'),
	'H2' => array('%inline'),
	'H3' => array('%inline'),
	'H4' => array('%inline'),
	'H5' => array('%inline'),
	'H6' => array('%inline'),
	'PRE' => array('%inline'),
	'Q' => array('%inline'),
	'BLOCKQUOTE' => array('%flow'),
	'INS' => array('%flow'),
	'DEL' => array('%flow'),
	'DL' => array(
		'DT',
		'DD'
		),
	'DT' => array('%inline'),
	'DD' => array('%flow'),
	'OL' => array('LI'),
	'UL' => array('LI'),
	'DIR' => array('LI'),
	'MENU' => array('LI'),
	'LI' => array('%flow'),
	'FORM' => array('%flow'),
	'LABEL' => array('%inline'),
	'INPUT' => array(),
	'SELECT' => array(
		'OPTGROUP',
		'OPTION'
		),
	'OPTGROUP' => array('OPTION'),
	'OPTION' => array('CDATA'),
	'TEXTAREA' => array('CDATA'),
	'FIELDSET' => array(
		'CDATA',
		'LEGEND',
		'%flow'
		),
	'LEGEND' => array('%inline'),
	'BUTTON' => array('%flow'),
	'TABLE' => array(
		'CAPTION',
		'COL',
		'COLGROUP',
		'THEAD',
		'TFOOT',
		'TBODY'
		),
	'CAPTION' => array('%inline'),
	'THEAD' => array('TR'),
	'TFOOT' => array('TR'),
	'TBODY' => array('TR'),
	'COLGROUP' => array('COL'),
	'COL' => array(),
	'TR' => array(
		'TH',
		'TD'
		),
	'TH' => array('%flow'),
	'TD' => array('%flow'),
	'IFRAME' => array('%flow'),
	'NOFRAMES' => array('%flow'),
	'HEAD' => array(
		'TITLE',
		'BASE',
		'SCRIPT',
		'STYLE',
		'META',
		'LINK',
		'OBJECT'
		),
	'TITLE' => array('CDATA'),
	'META' => array(),
	'STYLE' => array('CDATA'),
	'SCRIPT' => array('CDATA'),
	'NOSCRIPT' => array('%flow'),
	'BASE' => array(),
	'HTML' => array(
		'HEAD',
		'BODY'
		),
	'DOCUMENT' => array(
		'DOCTYPE',
		'HTML'
		)
	);
		
$aExclusions = array(
	'A' => array('A'),
	'PRE' => array(
		'IMG',
		'OBJECT',
		'APPLET',
		'BIG',
		'SMALL',
		'SUB',
		'SUP',
		'FONT',
		'BASEFONT'
		),
	'DIR' => array('%block'),
	'MENU' => array('%block'),
	'FORM' => array('FORM'),
	'LABEL' => array('LABEL'),
	'BUTTON' => array(
		'A',
		'%formctrl',
		'FORM',
		'ISINDEX',
		'FIELDSET',
		'IFRAME'
		)
	);


$aRequiredAttributes = array(
	'style' => array('type'),
	'script' => array('type'),
	'meta' => array('content'),
	'optgroup' => array('label'),
	'textarea' => array('rows', 'cols'),
	'img' => array('src', 'alt')
	);


class clsValidator
{
	var $aTags;
	var $aAttributes;
	var $aRequiredAttributes;
	
	function clsValidator()
	{
		global $aTags;
		global $aAttributes;
		global $aRequiredAttributes;
		
		$this->aTags = array();
		$this->aAttributes = array();
		$this->aRequiredAttributes = array();
		
		foreach (array_keys($aTags) as $sTag)
		{
			$this->aTags[$sTag] = array();
			$this->_expand($this->aTags[$sTag], $aTags[$sTag], $aTags);
		}
		
		foreach (array_keys($aAttributes) as $sAttribute)
		{
			$this->aAttributes[$sAttribute] = array();
			$this->_expand($this->aAttributes[$sAttribute], $aAttributes[$sAttribute], $aAttributes);
		}
		
		foreach (array_keys($aRequiredAttributes) as $sElement)
		{
			$this->aRequiredAttributes[$sElement] = array();
			$this->_expand($this->aRequiredAttributes[$sElement], $aRequiredAttributes[$sElement], $aRequiredAttributes);
		}
	}
	
	function &getInstance()
	{
		static $obj;
		if (!$obj) {
			$obj = new clsValidator();
		}
		return $obj;
	}
	
	function _expand(&$aDestination, &$aSource, &$aDictionary)
	{
		foreach ($aSource as $sChild)
		{
			if ('%' == substr($sChild, 0, 1)) {
				$this->_expand($aDestination, $aDictionary[$sChild], $aDictionary);
			} else
				$aDestination[] = $sChild;
		}
	}
	
	function elementValid($sElement)
	{
		return isset($this->aTags[strtoupper($sElement)]);
	}
	
	function attributeValid($sElement, $sAttribute)
	{
		if (false == isset($this->aAttributes[strtoupper($sElement)]))
			return false;
		return in_array(strtolower($sAttribute), $this->aAttributes[strtoupper($sElement)]);
	}
	
	function childValid($sParent, $sElement)
	{
		if (false == isset($this->aTags[strtoupper($sParent)]))
			return false;
		return in_array(strtoupper($sElement), $this->aTags[strtoupper($sParent)]);
	}
	
	// verify that required attributes have been specified
	function checkRequiredAttributes($sElement, &$aAttributes, &$sMissing)
	{
		if (isset($this->aRequiredAttributes[strtolower($sElement)]))
			foreach ($this->aRequiredAttributes[strtolower($sElement)] as $sRequiredAttribute)
				if (false == isset($aAttributes[$sRequiredAttribute]))
				{
					$sMissing = $sRequiredAttribute;
					return false;
				}
		
		return true;
	}
}
