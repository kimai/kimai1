<?php
class legacyXajaxResponse extends xajaxResponse {
	function outputEntitiesOn()		{ $this->setOutputEntities(true); }
	function outputEntitiesOff()	{ $this->setOutputEntities(false); }
	function addConfirmCommands()	{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'confirmCommands'), $temp); }
	function addAssign()			{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'assign'), $temp); }
	function addAppend()			{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'append'), $temp); }
	function addPrepend()			{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'prepend'), $temp); }
	function addReplace()			{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'replace'), $temp); }
	function addClear()				{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'clear'), $temp); }
	function addAlert()				{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'alert'), $temp); }
	function addRedirect()			{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'redirect'), $temp); }
	function addScript()			{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'script'), $temp); }
	function addScriptCall()		{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'call'), $temp); }
	function addRemove()			{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'remove'), $temp); }
	function addCreate()			{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'create'), $temp); }
	function addInsert()			{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'insert'), $temp); }
	function addInsertAfter()		{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'insertAfter'), $temp); }
	function addCreateInput()		{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'createInput'), $temp); }
	function addInsertInput()		{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'insertInput'), $temp); }
	function addInsertInputAfter()	{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'insertInputAfter'), $temp); }
	function addRemoveHandler()		{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'removeHandler'), $temp); }
	function addIncludeScript()		{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'includeScript'), $temp); }
	function addIncludeCSS()		{ $temp=func_get_args(); return call_user_func_array(array(&$this, 'includeCSS'), $temp); }
	function &getXML()				{ return $this; }
}

class legacyXajax extends xajax {
	function legacyXajax($sRequestURI='', $sWrapperPrefix='xajax_', $sEncoding=XAJAX_DEFAULT_CHAR_ENCODING, $bDebug=false)
	{
		parent::xajax();
		$this->configure('requestURI', $sRequestURI);
		$this->configure('wrapperPrefix', $sWrapperPrefix);
		$this->configure('characterEncoding', $sEncoding);
		$this->configure('debug', $bDebug);
	}
	function registerExternalFunction($mFunction, $sInclude)
	{
		$xuf =& new xajaxUserFunction($mFunction, $sInclude);
		$this->register(XAJAX_FUNCTION, $xuf);
	}
	function registerCatchAllFunction($mFunction)
	{
		if (is_array($mFunction)) array_shift($mFunction);
		$this->register(XAJAX_PROCESSING_EVENT, XAJAX_PROCESSING_EVENT_INVALID, $mFunction);
	}
	function registerPreFunction($mFunction)
	{
		if (is_array($mFunction)) array_shift($mFunction);
		$this->register(XAJAX_PROCESSING_EVENT, XAJAX_PROCESSING_EVENT_BEFORE, $mFunction);
	}
	function canProcessRequests()			{ return $this->canProcessRequest(); }
	function processRequests()				{ return $this->processRequest(); }
	function setCallableObject(&$oObject)	{ return $this->register(XAJAX_CALLABLE_OBJECT, $oObject); }
	function debugOn()						{ return $this->configure('debug',true); }
	function debugOff()						{ return $this->configure('debug',false); }
	function statusMessagesOn()				{ return $this->configure('statusMessages',true); }
	function statusMessagesOff()			{ return $this->configure('statusMessages',false); }
	function waitCursorOn()					{ return $this->configure('waitCursor',true); }
	function waitCursorOff()				{ return $this->configure('waitCursor',false); }
	function exitAllowedOn()				{ return $this->configure('exitAllowed',true); }
	function exitAllowedOff()				{ return $this->configure('exitAllowed',false); }
	function errorHandlerOn()				{ return $this->configure('errorHandler',true); }
	function errorHandlerOff()				{ return $this->configure('errorHandler',false); }
	function cleanBufferOn()				{ return $this->configure('cleanBuffer',true); }
	function cleanBufferOff()				{ return $this->configure('cleanBuffer',false); }
	function decodeUTF8InputOn()			{ return $this->configure('decodeUTF8Input',true); }
	function decodeUTF8InputOff()			{ return $this->configure('decodeUTF8Input',false); }
	function outputEntitiesOn()				{ return $this->configure('outputEntities',true); }
	function outputEntitiesOff()			{ return $this->configure('outputEntities',false); }
	function allowBlankResponseOn()			{ return $this->configure('allowBlankResponse',true); }
	function allowBlankResponseOff()		{ return $this->configure('allowBlankResponse',false); }
}
