<?php

$aAttributes = array(
	'%coreattrs' => array(
		'id',
		'class',
		'style',
		'title'
		),
	'%i18n' => array(
		'lang',
		'xml:lang',
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
	'%focus' => array(
		'accesskey',
		'tabindex',
		'onfocus',
		'onblur'
		),
	'%attrs' => array(
		'%coreattrs',
		'%i18n',
		'%events'
		),
	'%TextAlign' => array('align'),
	'html' => array(
		'%i18n',
		'id',
		'xmlns'
		),
	'head' => array(
		'%i18n',
		'id',
		'profile'
		),
	'title' => array(
		'%i18n',
		'id'
		),
	'base' => array(
		'id',
		'href',
		'target'
		),
	'meta' => array(
		'%i18n',
		'id',
		'http-equiv',
		'name',
		'content',
		'scheme'
		),
	'link' => array(
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
	'style' => array(
		'%i18n',
		'id',
		'type',
		'media',
		'title',
		'xml:space'
		),
	'script' => array(
		'id',
		'charset',
		'type',
		'language',
		'src',
		'defer',
		'xml:space'
		),
	'noscript' => array('%attrs'),
	'iframe' => array(
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
	'noframes' => array('%attrs'),
	'body' => array(
		'%attrs',
		'onload',
		'onunload',
		'background',
		'bgcolor',
		'text',
		'link',
		'vlink',
		'alink'
		),
	'div' => array(
		'%attrs',
		'%TextAlign'
		),
	'p' => array(
		'%attrs',
		'%TextAlign'
		),
	'h1' => array(
		'%attrs',
		'%TextAlign'
		),
	'h2' => array(
		'%attrs',
		'%TextAlign'
		),
	'h3' => array(
		'%attrs',
		'%TextAlign'
		),
	'h4' => array(
		'%attrs',
		'%TextAlign'
		),
	'h5' => array(
		'%attrs',
		'%TextAlign'
		),
	'h6' => array(
		'%attrs',
		'%TextAlign'
		),
	'ul' => array(
		'%attrs',
		'type',
		'compact'
		),
	'ol' => array(
		'%attrs',
		'type',
		'compact',
		'start'
		),
	'menu' => array(
		'%attrs',
		'compact'
		),
	'dir' => array(
		'%attrs',
		'compact'
		),
	'li' => array(
		'%attrs',
		'type',
		'value'
		),
	'dl' => array(
		'%attrs',
		'compact'
		),
	'dt' => array('%attrs'),
	'dd' => array('%attrs'),
	'address' => array(
		'%attrs'
		),
	'hr' => array(
		'%attrs',
		'align',
		'noshade',
		'size',
		'width'
		),
	'pre' => array(
		'%attrs',
		'width',
		'xml:space'
		),
	'blockquote' => array(
		'%attrs',
		'cite'
		),
	'center' => array('%attrs'),
	'ins' => array(
		'%attrs',
		'cite',
		'datetime'
		),
	'del' => array(
		'%attrs',
		'cite',
		'datetime'
		),
	'a' => array(
		'%attrs',
		'%focus',
		'charset',
		'type',
		'name',
		'href',
		'hreflang',
		'rel',
		'rev',
		'shape',
		'coords',
		'target'
		),
	'span' => array('%attrs'),
	'bdo' => array(
		'%coreattrs',
		'%events',
		'lang',
		'xml:lang',
		'dir'
		),
	'br' => array(
		'%coreattrs',
		'clear'
		),
	'em' => array('%attrs'),
	'strong' => array('%attrs'),
	'dfn' => array('%attrs'),
	'code' => array('%attrs'),
	'samp' => array('%attrs'),
	'kbd' => array('%attrs'),
	'var' => array('%attrs'),
	'cite' => array('%attrs'),
	'abbr' => array('%attrs'),
	'acronym' => array('%attrs'),
	'q' => array('%attrs','cite'),
	'sub' => array('%attrs'),
	'sup' => array('%attrs'),
	'tt' => array('%attrs'),
	'i' => array('%attrs'),
	'b' => array('%attrs'),
	'big' => array('%attrs'),
	'small' => array('%attrs'),
	'u' => array('%attrs'),
	's' => array('%attrs'),
	'strike' => array('%attrs'),
	'basefont' => array(
		'id',
		'size',
		'color',
		'face'
		),
	'font' => array(
		'%coreattrs',
		'%i18n',
		'size',
		'color',
		'face'
		),
	'object' => array(
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
		),
	'param' => array(
		'id',
		'name',
		'value',
		'valuetype',
		'type'
		),
	'applet' => array(
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
	'img' => array(
		'%attrs',
		'src',
		'alt',
		'name',
		'longdesc',
		'height',
		'width',
		'usemap',
		'ismap',
		'align',
		'border',
		'hspace',
		'vspace'
		),
	'map' => array(
		'%i18n',
		'%events',
		'id',
		'class',
		'style',
		'title',
		'name'
		),
	'area' => array(
		'%attrs',
		'%focus',
		'shape',
		'coords',
		'href',
		'nohref',
		'alt',
		'target'
		),
	'form' => array(
		'%attrs',
		'action',
		'method',
		'name',
		'enctype',
		'onsubmit',
		'onreset',
		'accept',
		'accept-charset',
		'target'
		),
	'label' => array(
		'%attrs',
		'for',
		'accesskey',
		'onfocus',
		'onblur'
		),
	'input' => array(
		'%attrs',
		'%focus',
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
		'onselect',
		'onchange',
		'accept',
		'align'
		),
	'select' => array(
		'%attrs',
		'name',
		'size',
		'multiple',
		'disabled',
		'tabindex',
		'onfocus',
		'onblur',
		'onchange'
		),
	'optgroup' => array(
		'%attrs',
		'disabled',
		'label'
		),
	'option' => array(
		'%attrs',
		'selected',
		'disabled',
		'label',
		'value'
		),
	'textarea' => array(
		'%attrs',
		'%focus',
		'name',
		'rows',
		'cols',
		'disabled',
		'readonly',
		'onselect',
		'onchange'
		),
	'fieldset' => array('%attrs'),
	'legend' => array(
		'%attrs',
		'accesskey',
		'align'
		),
	'button' => array(
		'%attrs',
		'%focus',
		'name',
		'value',
		'type',
		'disabled'
		),
	'isindex' => array(
		'%coreattrs',
		'%i18n',
		'prompt'
		),
	'table' => array(
		'%attrs',
		'summary',
		'width',
		'border',
		'frame',
		'rules',
		'cellspacing',
		'cellpadding',
		'align',
		'bgcolor'
		),
	'caption' => array(
		'%attrs',
		'align'
		),
	'colgroup' => array(
		'%attrs',
		'span',
		'width',
		'%cellhalign',
		'%cellvalign'
		),
	'col' => array(
		'%attrs',
		'span',
		'width',
		'%cellhalign',
		'%cellvalign'
		),
	'thead' => array(
		'%attrs',
		'%cellhalign',
		'%cellvalign'
		),
	'tfoot' => array(
		'%attrs',
		'%cellhalign',
		'%cellvalign'
		),
	'tbody' => array(
		'%attrs',
		'%cellhalign',
		'%cellvalign'
		),
	'tr' => array(
		'%attrs',
		'%cellhalign',
		'%cellvalign',
		'bgcolor'
		),
	'th' => array(
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
	'td' => array(
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
	);


$aTags = array(
	'%special.extra' => array(
		'object',
		'applet',
		'img',
		'map',
		'iframe'
		),
	'%special.basic' => array(
		'br',
		'span',
		'bdo'
		),
	'%special' => array(
		'%special.basic',
		'%special.extra'
		),
	'%fontstyle.extra' => array(
		'big',
		'small',
		'font',
		'basefont'
		),
	'%fontstyle.basic' => array(
		'tt',
		'i',
		'b',
		'u',
		's',
		'strike'
		),
	'%fontstyle' => array(
		'%fontstyle.basic',
		'%fontstyle.extra'
		),
	'%phrase.extra' => array(
		'sub',
		'sup'
		),
	'%phrase.basic' => array(
		'em',
		'strong',
		'dfn',
		'code',
		'q',
		'samp',
		'kbd',
		'var',
		'cite',
		'abbr',
		'acronym'
		),
	'%phrase' => array(
		'%phrase.basic',
		'%phrase.extra'
		),
	'%inline.forms' => array(
		'input',
		'select',
		'textarea',
		'label',
		'button'
		),
	'%misc.inline' => array(
		'ins',
		'del',
		'script'
		),
	'%misc' => array(
		'noscript',
		'%misc.inline'
		),
	'%inline' => array(
		'a',
		'%special',
		'%fontstyle',
		'%phrase',
		'%inline.forms'
		),
	'%Inline' => array(
		'cdata',
		'%inline',
		'%misc.inline'
		),
	'%heading' => array(
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6'
		),
	'%lists' => array(
		'ul',
		'ol',
		'dl',
		'menu',
		'dir'
		),
	'%blocktext' => array(
		'pre',
		'hr',
		'blockquote',
		'address',
		'center',
		'noframes'
		),
	'%block' => array(
		'p',
		'%heading',
		'div',
		'%lists',
		'%blocktext',
		'isindex',
		'fieldset',
		'table'
		),
	'%Flow' => array(
		'cdata',
		'%block',
		'form',
		'%inline',
		'%misc'
		),
	'%a.content' => array(
		'cdata',
		'%special',
		'%fontstyle',
		'%phrase',
		'%inline.forms',
		'%misc.inline'
		),
	'%pre.content' => array(
		'cdata',
		'a',
		'%special.basic',
		'%fontstyle.basic',
		'%phrase.basic',
		'%inline.forms',
		'%misc.inline'
		),
	'%form.content' => array(
		'cdata',
		'%block',
		'%inline',
		'%misc'
		),
	'%button.content' => array(
		'cdata',
		'p',
		'%heading',
		'div',
		'%lists',
		'%blocktext',
		'table',
		'br',
		'span',
		'bdo',
		'object',
		'applet',
		'img',
		'map',
		'%fontstyle',
		'%phrase',
		'%misc'
		),
	'%head.misc' => array(
		'script',
		'style',
		'meta',
		'link',
		'object'
		),
	'html' => array(
		'head',
		'body'
		),
	'head' => array(
		'%head.misc',
		'title',
		'base'
		),
	'title' => array('cdata'),
	'base' => array(),
	'meta' => array(),
	'link' => array(),
	'style' => array('cdata'),
	'script' => array('cdata'),
	'noscript' => array('%Flow'),
	'iframe' => array('%Flow'),
	'noframes' => array('%Flow'),
	'body' => array('%Flow'),
	'div' => array('%Flow'),
	'p' => array('%Inline'),
	'h1' => array('%Inline'),
	'h2' => array('%Inline'),
	'h3' => array('%Inline'),
	'h4' => array('%Inline'),
	'h5' => array('%Inline'),
	'h6' => array('%Inline'),
	'ul' => array('li'),
	'ol' => array('li'),
	'menu' => array('li'),
	'dir' => array('li'),
	'li' => array('%Flow'),
	'dl' => array(
		'dt',
		'dd'
		),
	'dt' => array('%Inline'),
	'dd' => array('%Flow'),
	'address' => array('cdata', '%inline', '%misc.inline', 'p'),
	'hr' => array(),
	'pre' => array('%pre.content'),
	'blockquote' => array('%Flow'),
	'center' => array('%Flow'),
	'ins' => array('%Flow'),
	'del' => array('%Flow'),
	'a' => array('%a.content'),
	'span' => array('%Inline'),
	'bdo' => array('%Inline'),
	'br' => array(),
	'em' => array('%Inline'),
	'strong' => array('%Inline'),
	'dfn' => array('%Inline'),
	'code' => array('%Inline'),
	'samp' => array('%Inline'),
	'kbd' => array('%Inline'),
	'var' => array('%Inline'),
	'cite' => array('%Inline'),
	'abbr' => array('%Inline'),
	'acronym' => array('%Inline'),
	'q' => array('%Inline'),
	'sub' => array('%Inline'),
	'sup' => array('%Inline'),
	'tt' => array('%Inline'),
	'i' => array('%Inline'),
	'b' => array('%Inline'),
	'big' => array('%Inline'),
	'small' => array('%Inline'),
	'u' => array('%Inline'),
	's' => array('%Inline'),
	'strike' => array('%Inline'),
	'basefont' => array(),
	'font' => array('%Inline'),
	'object' => array(
		'cdata',
		'param',
		'%block',
		'form',
		'%inline',
		'%misc'
		),
	'param' => array(),
	'applet' => array(
		'cdata',
		'param',
		'%block',
		'form',
		'%inline',
		'%misc'
		),
	'img' => array(),
	'map' => array(
		'%block',
		'form',
		'%misc',
		'area'
		),
	'area' => array(),
	'form' => array('%form.content'),
	'label' => array('%Inline'),
	'input' => array(),
	'select' => array('optgroup','option'),
	'optgroup' => array('option'),
	'option' => array('cdata'),
	'textarea' => array('cdata'),
	'fieldset' => array('cdata','legend','%block','form','%inline','%misc'),
	'legend' => array('%Inline'),
	'button' => array('%button.content'),
	'isindex' => array(),
	'table' => array('caption','col','colgroup','thead','tfoot','tbody','tr'),
	'caption' => array('%Inline'),
	'thead' => array('tr'),
	'tfoot' => array('tr'),
	'tbody' => array('tr'),
	'colgroup' => array('col'),
	'col' => array(),
	'tr' => array('th','td'),
	'th' => array('%Flow'),
	'td' => array('%Flow'),
	'document' => array(
		'doctype',
		'html'
		),
	'doctype' => array(
		'cdata'
		)
	);

$aRequiredAttributes = array(
	'html' => array(
		'xmlns',
		'xml:lang',
		'lang'
		),
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
			if ('%' == substr($sChild, 0, 1))
				$this->_expand($aDestination, $aDictionary[$sChild], $aDictionary);
			else
				$aDestination[] = $sChild;
		}
	}
	
	function elementValid($sElement)
	{
		return isset($this->aTags[strtolower($sElement)]);
	}
	
	function attributeValid($sElement, $sAttribute)
	{
		if (false == isset($this->aAttributes[strtolower($sElement)]))
			return false;
		return in_array(strtolower($sAttribute), $this->aAttributes[strtolower($sElement)]);
	}
	
	function childValid($sParent, $sElement)
	{
		if (false == isset($this->aTags[strtolower($sParent)]))
			return false;
		return in_array(strtolower($sElement), $this->aTags[strtolower($sParent)]);
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
