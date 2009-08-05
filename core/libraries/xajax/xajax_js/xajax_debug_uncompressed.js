/*
	File: xajax_debug.js
	
	This optional file contains the debugging module for use with xajax.  If
	you include this module after the standard <xajax_core.js> module, you
	will receive debugging messages, including errors, that occur during
	the processing of your xajax requests.
	
	Title: xajax debugging module
	
	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: xajax_debug_uncompressed.js 327 2007-02-28 16:55:26Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Class: xajax.debug

	This object contains the variables and functions used to display process state
	messages and to trap error conditions and report them to the user via
	a secondary browser window or alert messages as necessary.
*/
try {
	if ('undefined' == typeof xajax.debug)
		xajax.debug = {}
} catch (e) {
	alert('An internal error has occurred: the xajax_core has not been loaded prior to xajax_debug.');
}

/*
	String: workId
	
	Stores a 'unique' identifier for this session so that an existing debugging
	window can be detected, else one will be created.
*/
xajax.debug.workId = 'xajaxWork'+ new Date().getTime();

/*
	String: windowSource
	
	The default URL that is given to the debugging window upon creation.
*/
xajax.debug.windowSource = 'about:blank';

/*
	String: windowID
	
	A 'unique' name used to identify the debugging window that is attached
	to this xajax session.
*/
xajax.debug.windowID = 'xajax_debug_'+xajax.debug.workId;

/*
	String: windowStyle
	
	The parameters that will be used to create the debugging window.
*/
if ('undefined' == typeof xajax.debug.windowStyle)
	xajax.debug.windowStyle = 
		'width=800,' +
		'height=600,' +
		'scrollbars=yes,' +
		'resizable=yes,' +
		'status=yes';
		
/*
	String: windowTemplate
	
	The HTML template and CSS style information used to populate the
	debugging window upon creation.
*/
if ('undefined' == typeof xajax.debug.windowTemplate)
	xajax.debug.windowTemplate = 
		'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' +
		'<html><head>' +
		'<title>xajax debug output</title>' +
		'<style type="text/css">' +
		'/* <![CDATA[ */' +
		'.debugEntry { margin: 3px; padding: 3px; border-top: 1px solid #999999; } ' +
		'.debugDate { font-weight: bold; margin: 2px; } ' +
		'.debugText { margin: 2px; } ' +
		'.warningText { margin: 2px; font-weight: bold; } ' +
		'.errorText { margin: 2px; font-weight: bold; color: #ff7777; }' +
		'/* ]]> */' +
		'</style>' +
		'</head><body>' +
		'<h2>xajax debug output</h2>' +
		'<div id="debugTag"></div>' +
		'</body></html>';

/*
	Object: window
	
	A reference to the debugging window, once constructed, where messages will
	be displayed throughout the request process.  This is constructed internally
	as needed.
*/

/*
	Array: text
*/
xajax.debug.text = [];
xajax.debug.text[100] = 'WARNING: ';
xajax.debug.text[101] = 'ERROR: ';
xajax.debug.text[102] = 'XAJAX DEBUG MESSAGE:\n';
xajax.debug.text[103] = '...\n[LONG RESPONSE]\n...';
xajax.debug.text[104] = 'SENDING REQUEST';
xajax.debug.text[105] = 'SENT [';
xajax.debug.text[106] = ' bytes]';
xajax.debug.text[107] = 'CALLING: ';
xajax.debug.text[108] = 'URI: ';
xajax.debug.text[109] = 'INITIALIZING REQUEST';
xajax.debug.text[110] = 'PROCESSING PARAMETERS [';
xajax.debug.text[111] = ']';
xajax.debug.text[112] = 'NO PARAMETERS TO PROCESS';
xajax.debug.text[113] = 'PREPARING REQUEST';
xajax.debug.text[114] = 'STARTING XAJAX CALL (deprecated: use xajax.request instead)';
xajax.debug.text[115] = 'STARTING XAJAX REQUEST';
xajax.debug.text[116] = 'No response processor is available to process the response from the server.\n';
xajax.debug.text[117] = '.\nCheck for error messages from the server.';
xajax.debug.text[118] = 'RECEIVED [status: ';
xajax.debug.text[119] = ', size: ';
xajax.debug.text[120] = ' bytes, time: ';
xajax.debug.text[121] = 'ms]:\n';
xajax.debug.text[122] = 'The server returned the following HTTP status: ';
xajax.debug.text[123] = '\nRECEIVED:\n';
xajax.debug.text[124] = 'The server returned a redirect to:<br />';
xajax.debug.text[125] = 'DONE [';
xajax.debug.text[126] = 'ms]';
xajax.debug.text[127] = 'INITIALIZING REQUEST OBJECT';

/*
	Array: exceptions
*/
xajax.debug.exceptions = [];
xajax.debug.exceptions[10001] = 'Invalid response XML: The response contains an unknown tag: {data}.';
xajax.debug.exceptions[10002] = 'GetRequestObject: XMLHttpRequest is not available, xajax is disabled.';
xajax.debug.exceptions[10003] = 'Queue overflow: Cannot push object onto queue because it is full.';
xajax.debug.exceptions[10004] = 'Invalid response XML: The response contains an unexpected tag or text: {data}.';
xajax.debug.exceptions[10005] = 'Invalid request URI: Invalid or missing URI; autodetection failed; please specify a one explicitly.';
xajax.debug.exceptions[10006] = 'Invalid response command: Malformed response command received.';
xajax.debug.exceptions[10007] = 'Invalid response command: Command [{data}] is not a known command.';
xajax.debug.exceptions[10008] = 'Element with ID [{data}] not found in the document.';
xajax.debug.exceptions[10009] = 'Invalid request: Missing function name parameter.';
xajax.debug.exceptions[10010] = 'Invalid request: Missing function object parameter.';

/*
	Function: getExceptionText
*/
xajax.debug.getExceptionText = function(e) {
	if ('undefined' != typeof e.code) {
		if ('undefined' != typeof xajax.debug.exceptions[e.code]) {
			var msg = xajax.debug.exceptions[e.code];
			if ('undefined' != typeof e.data) {
				msg.replace('{data}', e.data);
			}
			return msg;
		}
	} else if ('undefined' != typeof e.name) {
		var msg = e.name;
		if ('undefined' != typeof e.message) {
			msg += ': ';
			msg += e.message;
		}
		return msg;
	}
	return 'An unknown error has occurred.';
}

/*
	Function: writeMessage
	
	Output a debug message to the debug window if available or send to an
	alert box.  If the debug window has not been created, attempt to 
	create it.
	
	text - (string):  The text to output.
	
	prefix - (string):  The prefix to use; this is prepended onto the 
		message; it should indicate the type of message (warning, error)
		
	cls - (stirng):  The className that will be applied to the message;
		invoking a style from the CSS provided in 
		<xajax.debug.windowTemplate>.  Should be one of the following:
		- warningText
		- errorText
*/
xajax.debug.writeMessage = function(text, prefix, cls) {
	try {
		var xd = xajax.debug;
		if ('undefined' == typeof xd.window || true == xd.window.closed) {
			xd.window = window.open(xd.windowSource, xd.windowID, xd.windowStyle);
			if ("about:blank" == xd.windowSource)
				xd.window.document.write(xd.windowTemplate);
		}
		var xdw = xd.window;
		var xdwd = xdw.document;
		if ('undefined' == typeof prefix)
			prefix = '';
		if ('undefined' == typeof cls)
			cls = 'debugText';
		
		text = xajax.debug.prepareDebugText(text);
		
		var debugTag = xdwd.getElementById('debugTag');
		var debugEntry = xdwd.createElement('div');
		var debugDate = xdwd.createElement('span');
		var debugText = xdwd.createElement('pre');
		
		debugDate.innerHTML = new Date().toString();
		debugText.innerHTML = prefix + text;
		
		debugEntry.appendChild(debugDate);
		debugEntry.appendChild(debugText);
		debugTag.insertBefore(debugEntry, debugTag.firstChild);
		// don't allow 'style' issues to hinder the debug output
		try {
			debugEntry.className = 'debugEntry';
			debugDate.className = 'debugDate';
			debugText.className = cls;
		} catch (e) {
		}
	} catch (e) {
		if (text.length > 1000) text = text.substr(0,1000) + xajax.debug.text[102];
		alert(xajax.debug.text[102] + text);
	}
}

/*
	Function: prepareDebugText
	
	Convert special characters to their HTML equivellents so they
	will show up in the <xajax.debug.window>.
*/
xajax.debug.prepareDebugText = function(text) {
	try {
		text = text.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/\n/g, '<br />');
		return text;
	} catch (e) {
		xajax.debug.stringReplace = function(haystack, needle, newNeedle) {
			var segments = haystack.split(needle);
			haystack = '';
			for (var i = 0; i < segments.length; ++i) {
				if (0 != i)
					haystack += newNeedle;
				haystack += segments[i];
			}
			return haystack;
		}
		xajax.debug.prepareDebugText = function(text) {
			text = xajax.debug.stringReplace(text, '&', '&amp;');
			text = xajax.debug.stringReplace(text, '<', '&lt;');
			text = xajax.debug.stringReplace(text, '>', '&gt;');
			text = xajax.debug.stringReplace(text, '\n', '<br />');
			return text;
		}
		xajax.debug.prepareDebugText(text);
	}
}

/*
	Function: executeCommand
	
	Catch any exceptions that are thrown by a response command handler
	and display a message in the debugger.
	
	This is a wrapper function which surrounds the standard 
	<xajax.executeCommand> function.
*/
xajax.debug.executeCommand = xajax.executeCommand;
xajax.executeCommand = function(args) {
	try {
		if ('undefined' == typeof args.cmd)
			throw { code: 10006 };
		if (false == xajax.command.handler.isRegistered(args))
			throw { code: 10007, data: args.cmd };
		return xajax.debug.executeCommand(args);
	} catch(e) {
		var msg = 'ExecuteCommand (';
		if ('undefined' != typeof args.sequence) {
			msg += '#';
			msg += args.sequence;
			msg += ', ';
		}
		if ('undefined' != typeof args.cmdFullName) {
			msg += '"';
			msg += args.cmdFullName;
			msg += '"';
		}
		msg += '):\n';
		msg += xajax.debug.getExceptionText(e);
		msg += '\n';
		xajax.debug.writeMessage(msg, xajax.debug.text[101], 'errorText');
	}
	return true;
}

/*
	Function: parseAttributes
	
	Catch any exception thrown during the parsing of response
	command attributes and display an appropriate debug message.
	
	This is a wrapper around the standard <xajax.parseAttributes>
	function.
*/
xajax.debug.parseAttributes = xajax.parseAttributes;
xajax.parseAttributes = function(child, obj) {
	try {
		xajax.debug.parseAttributes(child, obj);
	} catch(e) {
		var msg = 'ParseAttributes:\n';
		msg += xajax.debug.getExceptionText(e);
		msg += '\n';
		xajax.debug.writeMessage(msg, xajax.debug.text[101], 'errorText');
	}
}

xajax.debug.commandHandler = xajax.command.handler.unregister('dbg');
xajax.command.handler.register('dbg', function(args) {
	args.cmdFullName = 'debug message';
	xajax.debug.writeMessage(args.data, xajax.debug.text[100], 'warningText');
	return xajax.debug.commandHandler(args);
});


/*
	Function: $
	
	Catch any exceptions thrown while attempting to locate an
	HTML element by it's unique name.
	
	This is a wrapper around the standard <xajax.tools.$> function.
*/
xajax.debug.$ = xajax.tools.$;
xajax.tools.$ = function(sId) {
	try {
		var returnValue = xajax.debug.$(sId);
		if ('object' != typeof returnValue)
			throw { code: 10008 };
	}
	catch (e) {
		var msg = '$:';
		msg += xajax.debug.getExceptionText(e);
		msg += '\n';
		xajax.debug.writeMessage(msg, xajax.debug.text[100], 'warningText');
	}
	return returnValue;
}

/*
	Function: _objectToXML
	
	Generate a message indicating that a javascript object is
	being converted to xml.  Indicate the max depth and size.  Then
	display the size of the object upon completion.  Catch any 
	exceptions thrown during the conversion process.
	
	This is a wrapper around the standard <xajax.tools._objectToXML>
	function.
*/
xajax.debug._objectToXML = xajax.tools._objectToXML;
xajax.tools._objectToXML = function(obj, guard) {
	try {
		if (0 == guard.size) {
			var msg = 'OBJECT TO XML: maxDepth = ';
			msg += guard.maxDepth;
			msg += ', maxSize = ';
			msg += guard.maxSize;
			xajax.debug.writeMessage(msg);
		}
		var r = xajax.debug._objectToXML(obj, guard);
		if (0 == guard.depth) {
			var msg = 'OBJECT TO XML: size = ';
			msg += guard.size;
			xajax.debug.writeMessage(msg);
		}
		return r;
	} catch(e) {
		var msg = 'ObjectToXML: ';
		msg += xajax.debug.getExceptionText(e);
		msg += '\n';
		xajax.debug.writeMessage(msg, xajax.debug.text[101], 'errorText');
	}
	return '';
}

/*
	Function: _internalSend
	
	Generate a message indicating that the xajax request is
	about the be sent to the server.
	
	This is a wrapper around the standard <xajax._internalSend> 
	function.
*/
xajax.debug._internalSend = xajax._internalSend;
xajax._internalSend = function(oRequest) {
	try {
		xajax.debug.writeMessage(xajax.debug.text[104]);
		xajax.debug.writeMessage(
			xajax.debug.text[105] + 
			oRequest.requestData.length + 
			xajax.debug.text[106]
			);
		oRequest.beginDate = new Date();
		xajax.debug._internalSend(oRequest);
	} catch (e) {
		var msg = 'InternalSend: ';
		msg += xajax.debug.getExceptionText(e);
		msg += '\n';
		xajax.debug.writeMessage(msg, xajax.debug.text[101], 'errorText');
		throw e;
	}
}

/*
	Function: submitRequest
	
	Generate a message indicating that a request is ready to be 
	submitted; providing the URL and the function being invoked.
	
	Catch any exceptions thrown and display a message.
	
	This is a wrapper around the standard <xajax.submitRequest>
	function.
*/
xajax.debug.submitRequest = xajax.submitRequest;
xajax.submitRequest = function(oRequest) {
	var msg = oRequest.method;
	msg += ': ';
	text = decodeURIComponent(oRequest.requestData);
	text = text.replace(new RegExp('&xjx', 'g'), '\n&xjx');
	text = text.replace(new RegExp('<xjxobj>', 'g'), '\n<xjxobj>');
	text = text.replace(new RegExp('<e>', 'g'), '\n<e>');
	text = text.replace(new RegExp('</xjxobj>', 'g'), '\n</xjxobj>\n');
	msg += text;
	xajax.debug.writeMessage(msg);
	msg = xajax.debug.text[107];
	var separator = '\n';
	for (var mbr in oRequest.functionName) {
		msg += separator;
		msg += mbr;
		msg += ': ';
		msg += oRequest.functionName[mbr];
		separator = '\n';
	}
	msg += separator;
	msg += xajax.debug.text[108];
	msg += separator;
	msg += oRequest.URI;
	xajax.debug.writeMessage(msg);
	
	try {
		return xajax.debug.submitRequest(oRequest);
	} catch (e) {
		xajax.debug.writeMessage(e.message);
		if (0 < oRequest.retry)
			throw e;
	}
}

/*
	Function: initializeRequest
	
	Generate a message indicating that the request object is
	being initialized.
	
	This is a wrapper around the standard <xajax.initializeRequest>
	function.
*/
xajax.debug.initializeRequest = xajax.initializeRequest;
xajax.initializeRequest = function(oRequest) {
	try {
		var msg = xajax.debug.text[109];
		xajax.debug.writeMessage(msg);
		return xajax.debug.initializeRequest(oRequest);
	} catch (e) {
		var msg = 'InitializeRequest: ';
		msg += xajax.debug.getExceptionText(e);
		msg += '\n';
		xajax.debug.writeMessage(msg, xajax.debug.text[101], 'errorText');
		throw e;
	}
}

/*
	Function: processParameters
	
	Generate a message indicating that the request object is
	being populated with the parameters provided.
	
	This is a wrapper around the standard <xajax.processParameters>
	function.
*/
xajax.debug.processParameters = xajax.processParameters;
xajax.processParameters = function(oRequest) {
	try {
		if ('undefined' != typeof oRequest.parameters) {
			var msg = xajax.debug.text[110];
			msg += oRequest.parameters.length;
			msg += xajax.debug.text[111];
			xajax.debug.writeMessage(msg);
		} else {
			var msg = xajax.debug.text[112];
			xajax.debug.writeMessage(msg);
		}
		return xajax.debug.processParameters(oRequest);
	} catch (e) {
		var msg = 'ProcessParameters: ';
		msg += xajax.debug.getExceptionText(e);
		msg += '\n';
		xajax.debug.writeMessage(msg, xajax.debug.text[101], 'errorText');
		throw e;
	}
}

/*
	Function: prepareRequest
	
	Generate a message indicating that the request is being
	prepared.  This may occur more than once for a request
	if it errors and a retry is attempted.
	
	This is a wrapper around the standard <xajax.prepareRequest>
*/
xajax.debug.prepareRequest = xajax.prepareRequest;
xajax.prepareRequest = function(oRequest) {
	try {
		var msg = xajax.debug.text[113];
		xajax.debug.writeMessage(msg);
		return xajax.debug.prepareRequest(oRequest);
	} catch (e) {
		var msg = 'PrepareRequest: ';
		msg += xajax.debug.getExceptionText(e);
		msg += '\n';
		xajax.debug.writeMessage(msg, xajax.debug.text[101], 'errorText');
		throw e;
	}
}

/*
	Function: call
	
	Validates that a function name was provided, generates a message 
	indicating that a xajax call is starting and sets a flag in the
	request object indicating that debugging is enabled for this call.
	
	This is a wrapper around the standard <xajax.call> function.
*/
xajax.debug.call = xajax.call;
xajax.call = function() {
	try {
		xajax.debug.writeMessage(xajax.debug.text[114]);
		
		var numArgs = arguments.length;
		
		if (0 == numArgs)
			throw { code: 10009 };
		
		var functionName = arguments[0];
		var oOptions = {}
		if (1 < numArgs)
			oOptions = arguments[1];
		
		oOptions.debugging = true;
		
		return xajax.debug.call(functionName, oOptions);
	} catch (e) {
		var msg = 'Call: ';
		msg += xajax.debug.getExceptionText(e);
		msg += '\n';
		xajax.debug.writeMessage(msg, xajax.debug.text[101], 'errorText');
		throw e;
	}
}

/*
	Function: request
	
	Validates that a function name was provided, generates a message 
	indicating that a xajax request is starting and sets a flag in the
	request object indicating that debugging is enabled for this request.
	
	This is a wrapper around the standard <xajax.request> function.
*/
xajax.debug.request = xajax.request;
xajax.request = function() {
	try {
		xajax.debug.writeMessage(xajax.debug.text[115]);
		
		var numArgs = arguments.length;
		
		if (0 == numArgs)
			throw { code: 10010 };
		
		var oFunction = arguments[0];
		var oOptions = {}
		if (1 < numArgs)
			oOptions = arguments[1];
		
		oOptions.debugging = true;
		
		return xajax.debug.request(oFunction, oOptions);
	} catch (e) {
		var msg = 'Request: ';
		msg += xajax.debug.getExceptionText(e);
		msg += '\n';
		xajax.debug.writeMessage(msg, xajax.debug.text[101], 'errorText');
		throw e;
	}
}

/*
	Function: getResponseProcessor
	
	Generate an error message when no reponse processor is available
	to process the type of response returned from the server.
	
	This is a wrapper around the standard <xajax.getResponseProcessor>
	function.
*/
xajax.debug.getResponseProcessor = xajax.getResponseProcessor;
xajax.getResponseProcessor = function(oRequest) {
	try {
		var fProc = xajax.debug.getResponseProcessor(oRequest);
		
		if ('undefined' == typeof fProc) { 
			var msg = xajax.debug.text[116];
			try {
				var contentType = oRequest.request.getResponseHeader('content-type');
				msg += "Content-Type: ";
				msg += contentType;
				if ('text/html' == contentType) {
					msg += xajax.debug.text[117];
				}
			} catch (e) {
			}
			xajax.debug.writeMessage(msg, xajax.debug.text[101], 'errorText');
		}
		
		return fProc;
	} catch (e) {
		var msg = 'GetResponseProcessor: ';
		msg += xajax.debug.getExceptionText(e);
		msg += '\n';
		xajax.debug.writeMessage(msg, xajax.debug.text[101], 'errorText');
		throw e;
	}
}

/*
	Function: responseReceived
	
	Generate a message indicating that a response has been received
	from the server; provide some statistical data regarding the
	response and the response time.
	
	Catch any exceptions that are thrown during the processing of
	the response and generate a message.
	
	This is a wrapper around the standard <xajax.responseReceived>
	function.
*/
xajax.debug.responseReceived = xajax.responseReceived;
xajax.responseReceived = function(oRequest) {
	var xx = xajax;
	var xt = xx.tools;
	var xd = xx.debug;
	
	var oRet;
	
	try {
		var status = oRequest.request.status;
		if (xt.arrayContainsValue(xx.responseSuccessCodes, status)) {
			var packet = oRequest.request.responseText;
			packet = packet.replace(new RegExp('<cmd', 'g'), '\n<cmd');
			packet = packet.replace(new RegExp('<xjx>', 'g'), '\n<xjx>');
			packet = packet.replace(new RegExp('<xjxobj>', 'g'), '\n<xjxobj>');
			packet = packet.replace(new RegExp('<e>', 'g'), '\n<e>');
			packet = packet.replace(new RegExp('</xjxobj>', 'g'), '\n</xjxobj>\n');
			packet = packet.replace(new RegExp('</xjx>', 'g'), '\n</xjx>');
			oRequest.midDate = new Date();
			var msg = xajax.debug.text[118];
			msg += oRequest.request.status;
			msg += xajax.debug.text[119];
			msg += packet.length;
			msg += xajax.debug.text[120];
			msg += (oRequest.midDate - oRequest.beginDate);
			msg += xajax.debug.text[121];
			msg += packet;
			xd.writeMessage(msg);
		} else if (xt.arrayContainsValue(xx.responseErrorsForAlert, status)) {
			var msg = xajax.debug.text[122];
			msg += status;
			msg += xajax.debug.text[123];
			msg += oRequest.request.responseText;
			xd.writeMessage(msg, xajax.debug.text[101], 'errorText');
		} else if (xt.arrayContainsValue(xx.responseRedirectCodes, status)) {
			var msg = xajax.debug.text[124];
			msg += oRequest.request.getResponseHeader('location');
			xd.writeMessage(msg);
		}
		oRet = xd.responseReceived(oRequest);
	} catch (e) {
		var msg = 'ResponseReceived: ';
		msg += xajax.debug.getExceptionText(e);
		msg += '\n';
		xd.writeMessage(msg, xajax.debug.text[101], 'errorText');
	}
	
	return oRet;
}

/*
	Function: completeRequest
	
	Generate a message indicating that the request has completed
	and provide some statistics regarding the request and response.
	
	This is a wrapper around the standard <xajax.completeResponse>
	function.
*/
xajax.debug.completeResponse = xajax.completeResponse;
xajax.completeResponse = function(oRequest) {
	try {
		var returnValue = xajax.debug.completeResponse(oRequest);
		oRequest.endDate = new Date();
		var msg = xajax.debug.text[125];
		msg += (oRequest.endDate - oRequest.beginDate);
		msg += xajax.debug.text[126];
		xajax.debug.writeMessage(msg);
		return returnValue;
	} catch (e) {
		var msg = 'CompleteResponse: ';
		msg += xajax.debug.getExceptionText(e);
		msg += '\n';
		xajax.debug.writeMessage(msg, xajax.debug.text[101], 'errorText');
		throw e;
	}
}

/*
	Function: getRequestObject
	
	Generate a message indicating that the request object is 
	being initialized.
	
	Catch any exceptions that are thrown during the process or
	initializing a new request object.
	
	This is a wrapper around the standard <xajax.getRequestObject>
	function.
*/
xajax.debug.getRequestObject = xajax.tools.getRequestObject;
xajax.tools.getRequestObject = function() {
	try {
		xajax.debug.writeMessage(xajax.debug.text[127]);
		return xajax.debug.getRequestObject();
	} catch (e) {
		var msg = 'GetRequestObject: ';
		msg += xajax.debug.getExceptionText(e);
		msg += '\n';
		xajax.debug.writeMessage(msg, xajax.debug.text[101], 'errorText');
		throw e;
	}
}

/*
	Function: assign
	
	Catch any exceptions thrown during the assignment and 
	display an error message.
	
	This is a wrapper around the standard <xajax.dom.assign>
	function.
*/
if (xajax.dom.assign) {
	xajax.debug.assign = xajax.dom.assign;
	xajax.dom.assign = function(element, id, property, data) {
		try {
			return xajax.debug.assign(element, id, property, data);
		} catch (e) {
			var msg = 'xajax.dom.assign: ';
			msg += xajax.debug.getExceptionText(e);
			msg += '\n';
			msg += 'Eval: element.';
			msg += property;
			msg += ' = data;\n';
			xajax.debug.writeMessage(msg, xajax.debug.text[101], 'errorText');
		}
		return true;
	}
}

/*
	Function: xajax.tools.queue.retry
*/
if (xajax.tools) {
	if (xajax.tools.queue) {
		if (xajax.tools.queue.retry) {
			if ('undefined' == typeof xajax.debug.tools)
				xajax.debug.tools = {};
			if ('undefined' == typeof xajax.debug.tools.queue)
				xajax.debug.tools.queue = {};
			xajax.debug.tools.queue.retry = xajax.tools.queue.retry;
			xajax.tools.queue.retry = function(obj, count) {
				if (xajax.debug.tools.queue.retry(obj, count))
					return true;
				// no 'exceeded' message for sleep command
				if (obj.cmd && 's' == obj.cmd)
					return false;
				xajax.debug.writeMessage('Retry count exceeded.');
				return false;
			}
		}
	}
}

/*
	Boolean: isLoaded
	
	true - indicates that the debugging module is loaded
*/
xajax.debug.isLoaded = true;

/*
	Section: Redefine shortcuts.
	
	Must redefine these shortcuts so they point to the new debug (wrapper) versions:
	- <xjx.$>
	- <xjx.getFormValues>
	- <xjx.call>

	Must redefine these shortcuts as well:
	- <xajax.$>
	- <xajax.getFormValues>
*/
xjx = {}

xjx.$ = xajax.tools.$;
xjx.getFormValues = xajax.tools.getFormValues;
xjx.call = xajax.call;
xjx.request = xajax.request;

xajax.$ = xajax.tools.$;
xajax.getFormValues = xajax.tools.getFormValues;
