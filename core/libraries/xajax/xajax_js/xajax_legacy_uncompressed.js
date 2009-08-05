/*
	File: xajax_legacy.js
	
	Provides support for legacy scripts that have not been updated to the
	latest syntax.
	
	Title: xajax legacy support module
	
	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: xajax_legacy_uncompressed.php 327 2007-02-28 16:55:26Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Class: xajax.legacy
*/
try {
	if (undefined == xajax.legacy)
		xajax.legacy = {}
} catch (e) {
	alert('An internal error has occurred: the xajax_core has not been loaded prior to xajax_legacy.');
}

/*
	Function: call
	
	Convert call parameters from the 0.2.x syntax to the new *improved*
	call format.
	
	This is a wrapper function around the standard <xajax.call> function.
*/
xajax.legacy.call = xajax.call;
xajax.call = function(sFunction, objParameters) {
	var oOpt = {}
	oOpt.parameters = objParameters;
	if (undefined != xajax.loadingFunction) {
		if (undefined == oOpt.callback)
			oOpt.callback = {}
		oOpt.callback.onResponseDelay = xajax.loadingFunction;
	}
	if (undefined != xajax.doneLoadingFunction) {
		if (undefined == oOpt.callback)
			oOpt.callback = {}
		oOpt.callback.onComplete = xajax.doneLoadingFunction;
	}
	return xajax.legacy.call(sFunction, oOpt);
}

/*
	Boolean: isLoaded
	
	true - Indicates that the <xajax.legacy> module is loaded.
*/
xajax.legacy.isLoaded = true;
