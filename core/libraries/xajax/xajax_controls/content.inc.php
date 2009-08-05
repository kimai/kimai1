<?php
/*
	File: content.inc.php

	HTML Control Library - Content Level Tags

	Title: xajax HTML control class library

	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: content.inc.php 362 2007-05-29 15:32:24Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Section: Description
	
	This file contains class declarations for the following HTML tags:
	
	- literal
	- br, hr
	- sub, sup, q, em, strong, cite, dfn, code, samp, kbd, var, abbr, acronym, tt, i, b, big, small, ins, del
	- h1 ... h6, address, p, blockquote, pre
	
	The following tags are deprecated as of HTML 4.01 and therefore they are not supported.
	
	- font, strike, s, u, center
*/

class clsLiteral extends xajaxControl
{
	function clsLiteral($sText)
	{
		xajaxControl::xajaxControl('CDATA');

		$this->sClass = '%inline';
		$this->sText = $sText;
	}

	function printHTML($sIndent='')
	{
		echo $this->sText;
	}
}

class clsBr extends xajaxControl
{
	function clsBr($aConfiguration=array())
	{
		xajaxControl::xajaxControl('br', $aConfiguration);
		
		$this->sClass = '%inline';
		$this->sEndTag = 'optional';
	}
}

class clsHr extends xajaxControl
{
	function clsHr($aConfiguration=array())
	{
		xajaxControl::xajaxControl('hr', $aConfiguration);
		
		$this->sClass = '%inline';
		$this->sEndTag = 'optional';
	}
}

class clsInlineContainer extends xajaxControlContainer
{
	function clsInlineContainer($sTag, $aConfiguration)
	{
		xajaxControlContainer::xajaxControlContainer($sTag, $aConfiguration);
		
		$this->sClass = '%inline';
		$this->sEndTag = 'required';
	}
}

class clsSub extends clsInlineContainer
{
	function clsSub($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('sub', $aConfiguration);
	}
}

class clsSup extends xajaxControlContainer
{
	function clsSup($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('sup', $aConfiguration);
	}
}

class clsEm extends clsInlineContainer
{
	function clsEm($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('em', $aConfiguration);
	}
}

class clsStrong extends clsInlineContainer
{
	function clsStrong($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('strong', $aConfiguration);
	}
}

class clsCite extends clsInlineContainer
{
	function clsCite($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('cite', $aConfiguration);
	}
}

class clsDfn extends clsInlineContainer
{
	function clsDfn($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('dfn', $aConfiguration);
	}
}

class clsCode extends clsInlineContainer
{
	function clsCode($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('code', $aConfiguration);
	}
}

class clsSamp extends clsInlineContainer
{
	function clsSamp($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('samp', $aConfiguration);
	}
}

class clsKbd extends clsInlineContainer
{
	function clsKbd($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('kbd', $aConfiguration);
	}
}

class clsVar extends clsInlineContainer
{
	function clsVar($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('var', $aConfiguration);
	}
}

class clsAbbr extends clsInlineContainer
{
	function clsAbbr($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('abbr', $aConfiguration);
	}
}

class clsAcronym extends clsInlineContainer
{
	function clsAcronym($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('acronym', $aConfiguration);
	}
}

class clsTt extends clsInlineContainer
{
	function clsTt($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('tt', $aConfiguration);
	}
}

class clsItalic extends clsInlineContainer
{
	function clsItalic($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('i', $aConfiguration);
	}
}

class clsBold extends clsInlineContainer
{
	function clsBold($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('b', $aConfiguration);
	}
}

class clsBig extends clsInlineContainer
{
	function clsBig($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('big', $aConfiguration);
	}
}


class clsSmall extends clsInlineContainer
{
	function clsSmall($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('small', $aConfiguration);
	}
}

class clsIns extends clsInlineContainer
{
	function clsIns($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('ins', $aConfiguration);
	}
}

class clsDel extends clsInlineContainer
{
	function clsDel($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('del', $aConfiguration);
	}
}

class clsHeadline extends xajaxControlContainer
{
	function clsHeadline($sType, $aConfiguration=array())
	{
		if (0 < strpos($sType, '123456'))
			trigger_error('Invalid type for headline control; should be 1,2,3,4,5 or 6.'
				. $this->backtrace(),
				E_USER_ERROR
				);
		
		xajaxControlContainer::xajaxControlContainer('h' . $sType, $aConfiguration);

		$this->sClass = '%inline';
	}
}

class clsAddress extends clsInlineContainer
{
	function clsAddress($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('address', $aConfiguration);
	}
}

class clsParagraph extends clsInlineContainer
{
	function clsParagraph($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('p', $aConfiguration);
	}
}

class clsBlockquote extends clsInlineContainer
{
	function clsBlockquote($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('blockquote', $aConfiguration);
	}
}

class clsPre extends clsInlineContainer
{
	function clsPre($aConfiguration=array())
	{
		clsInlineContainer::clsInlineContainer('pre', $aConfiguration);
	}
}
