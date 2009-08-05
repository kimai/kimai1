/*
	File: xajax_core.js
	
	This file contains the definition of the main xajax javascript core.
	
	This is the client side code which runs on the web browser or similar 
	web enabled application.  Include this in the HEAD of each page for
	which you wish to use xajax.
	
	Title: xajax core javascript library
	
	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: xajax_core_uncompressed.js 327 2007-02-28 16:55:26Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Class: xajax.config
	
	This class contains all the default configuration settings.  These
	are application level settings; however, they can be overridden
	by including a xajax.config definition prior to including the
	<xajax_core.js> file, or by specifying the appropriate configuration
	options on a per call basis.
*/
try {
	if ('undefined' == typeof xajax.config) xajax.config = {};
} catch (e) {
	xajax = {};
	xajax.config = {};
}

/*
	Function: setDefault
	
	This function will set a default configuration option if it is 
	not already set.
	
	option - (string):
		The name of the option that will be set.
		
	defaultValue - (unknown):
		The value to use if a value was not already set.
*/
xajax.config.setDefault = function(option, defaultValue) {
	if ('undefined' == typeof xajax.config[option])
		xajax.config[option] = defaultValue;
}

/*
	Object: commonHeaders
	
	An array of header entries where the array key is the header
	option name and the associated value is the value that will
	set when the request object is initialized.
	
	These headers will be set for both POST and GET requests.
*/
xajax.config.setDefault('commonHeaders', {
	'If-Modified-Since': 'Sat, 1 Jan 2000 00:00:00 GMT'
	});

/*
	Object: postHeaders
	
	An array of header entries where the array key is the header
	option name and the associated value is the value that will
	set when the request object is initialized.
*/
xajax.config.setDefault('postHeaders', {});

/*
	Object: getHeaders
	
	An array of header entries where the array key is the header
	option name and the associated value is the value that will
	set when the request object is initialized.
*/
xajax.config.setDefault('getHeaders', {});

/*
	Boolean: waitCursor
	
	true - xajax should display a wait cursor when making a request
	false - xajax should not show a wait cursor during a request
*/
xajax.config.setDefault('waitCursor', false);

/*
	Boolean: statusMessages
	
	true - xajax should update the status bar during a request
	false - xajax should not display the status of the request
*/
xajax.config.setDefault('statusMessages', false);

/*
	Object: baseDocument
	
	The base document that will be used throughout the code for
	locating elements by ID.
*/
xajax.config.setDefault('baseDocument', document);

/*
	String: requestURI
	
	The URI that requests will be sent to.
*/
xajax.config.setDefault('requestURI', xajax.config.baseDocument.URL);

/*
	String: defaultMode
	
	The request mode.
	
	'asynchronous' - The request will immediately return, the
		response will be processed when (and if) it is received.
		
	'synchronous' - The request will block, waiting for the
		response.  This option allows the server to return
		a value directly to the caller.
*/
xajax.config.setDefault('defaultMode', 'asynchronous');

/*
	String: defaultHttpVersion
	
	The Hyper Text Transport Protocol version designated in the 
	header of the request.
*/
xajax.config.setDefault('defaultHttpVersion', 'HTTP/1.1');

/*
	String: defaultContentType
	
	The content type designated in the header of the request.
*/
xajax.config.setDefault('defaultContentType', 'application/x-www-form-urlencoded');

/*
	Integer: defaultResponseDelayTime
	
	The delay time, in milliseconds, associated with the 
	<xajax.callback.global.onRequestDelay> event.
*/
xajax.config.setDefault('defaultResponseDelayTime', 1000);

/*
	Integer: defaultExpirationTime
	
	The amount of time to wait, in milliseconds, before a request
	is considered expired.  This is used to trigger the
	<xajax.callback.global.onExpiration event.
*/
xajax.config.setDefault('defaultExpirationTime', 10000);

/*
	String: defaultMethod
	
	The method used to send requests to the server.
	
	'POST' - Generate a form POST request
	'GET' - Generate a GET request; parameters are appended
		to the <xajax.config.requestURI> to form a URL.
*/
xajax.config.setDefault('defaultMethod', 'POST');	// W3C: Method is case sensitive

/*
	Integer: defaultRetry
	
	The number of times a request should be retried
	if it expires.
*/
xajax.config.setDefault('defaultRetry', 5);

/*
	Object: defaultReturnValue
	
	The value returned by <xajax.call> when in asynchronous
	mode, or when a syncrhonous call does not specify the
	return value.
*/
xajax.config.setDefault('defaultReturnValue', false);

/*
	Integer: maxObjectDepth
	
	The maximum depth of recursion allowed when serializing
	objects to be sent to the server in a request.
*/
xajax.config.setDefault('maxObjectDepth', 20);

/*
	Integer: maxObjectSize
	
	The maximum number of members allowed when serializing
	objects to be sent to the server in a request.
*/
xajax.config.setDefault('maxObjectSize', 2000);

/*
	Class: xajax.config.status
	
	Provides support for updating the browser's status bar during
	the request process.  By splitting the status bar functionality
	into an object, the xajax developer has the opportunity to
	customize the status bar messages prior to sending xajax requests.
*/
xajax.config.status = {
	/*
		Function: update
		
		Constructs and returns a set of event handlers that will be
		called by the xajax framework to set the status bar messages.
	*/
	update: function() {
		return {
			onRequest: function() {
				window.status = 'Sending Request...';
			},
			onWaiting: function() {
				window.status = 'Waiting for Response...';
			},
			onProcessing: function() {
				window.status = 'Processing...';
			},
			onComplete: function() {
				window.status = 'Done.';
			}
		}
	},
	/*
		Function: dontUpdate
		
		Constructs and returns a set of event handlers that will be
		called by the xajax framework where status bar updates
		would normally occur.
	*/
	dontUpdate: function() {
		return {
			onRequest: function() {},
			onWaiting: function() {},
			onProcessing: function() {},
			onComplete: function() {}
		}
	}
}

/*
	Class: xajax.config.cursor
	
	Provides the base functionality for updating the browser's cursor
	during requests.  By splitting this functionalityh into an object
	of it's own, xajax developers can now customize the functionality 
	prior to submitting requests.
*/
xajax.config.cursor = {
	/*
		Function: update
		
		Constructs and returns a set of event handlers that will be
		called by the xajax framework to effect the status of the 
		cursor during requests.
	*/
	update: function() {
		return {
			onWaiting: function() {
				if (xajax.config.baseDocument.body)
					xajax.config.baseDocument.body.style.cursor = 'wait';
			},
			onComplete: function() {
				xajax.config.baseDocument.body.style.cursor = 'auto';
			}
		}
	},
	/*
		Function: dontUpdate
		
		Constructs and returns a set of event handlers that will
		be called by the xajax framework where cursor status changes
		would typically be made during the handling of requests.
	*/
	dontUpdate: function() {
		return {
			onWaiting: function() {},
			onComplete: function() {}
		}
	}
}

/*
	Class: xajax.tools
	
	This contains utility functions which are used throughout
	the xajax core.
*/
xajax.tools = {}

/*
	Function: $

	Shorthand for finding a uniquely named element within 
	the document.
	
	sId - (string):
		The unique name of the element (specified by the 
		ID attribute), not to be confused with the name
		attribute on form elements.
		
	Returns:
	
	object - The element found or null.
	
	Note:
		This function uses the <xajax.config.baseDocument>
		which allows <xajax> to operate on the main window
		document as well as documents from contained
		iframes and child windows.
	
	See also:
		<xajax.$> and <xjx.$>
*/
xajax.tools.$ = function(sId) {
	if (!sId)
		return null;
	
	var oDoc = xajax.config.baseDocument;

	var obj = oDoc.getElementById(sId);
	if (obj)
		return obj;
		
	if (oDoc.all)
		return oDoc.all[sId];

	return obj;
}

/*
	Function arrayContainsValue
	
	Looks for a value within the specified array and, if found, 
	returns true; otherwise it returns false.
	
	array - (object):
		The array to be searched.
		
	valueToCheck - (object):
		The value to search for.
		
	Returns:
	
	true - The value is one of the values contained in the 
		array.
		
	false - The value was not found in the specified array.
*/
xajax.tools.arrayContainsValue = function(array, valueToCheck) {
	var i = 0;
	var l = array.length;
	while (i < l) {
		if (array[i] == valueToCheck)
			return true;
		++i;
	}
	return false;
}

/*
	Function: doubleQuotes
	
	Replace all occurances of the single quote character with a double
	quote character.
	
	haystack - The source string to be scanned.
	
	Returns:
	
	string - A new string with the modifications applied.
*/
xajax.tools.doubleQuotes = function(haystack) {
	return haystack.replace(new RegExp("'", 'g'), '"');
}

/*
	Function: singleQuotes
	
	Replace all occurances of the double quote character with a single
	quote character.
	
	haystack - The source string to be scanned.
	
	Returns:
	
	string - A new string with the modification applied.
*/
xajax.tools.singleQuotes = function(haystack) {
	return haystack.replace(new RegExp('"', 'g'), "'");
}

/*
	Function: _escape
	
	Determine if the specified value contains special characters and
	create a CDATA section so the value can be safely transmitted.
	
	data - (string or other):
		The source string value to be evaluated or an object of unknown
		type.
		
	Returns:
	
	string - The string value, escaped if necessary or the object provided
		if it is not a string.
		
	Note:
		When the specified object is NOT a string, the value is returned
		as is.
*/
xajax.tools._escape = function(data) {
	if ('undefined' == typeof data)
		return data;
	
	// 'object' is handled elsewhere, 
	// 'string' handled below, 
	// 'number' will be returned here
	// 'boolean' will be returned here
	if ('string' != typeof data)
		return data;
	
	var needCDATA = false;
	
	if (encodeURIComponent(data) != data) {
		needCDATA = true;
		
		var segments = data.split('<![CDATA[');
		var segLen = segments.length;
		data = [];
		for (var i = 0; i < segLen; ++i) {
			var segment = segments[i];
			var fragments = segment.split(']]>');
			var fragLen = fragments.length;
			segment = '';
			for (var j = 0; j < fragLen; ++j) {
				if (0 != j)
					segment += ']]]]><![CDATA[>';
				segment += fragments[j];
			}
			if (0 != i)
				data.push('<![]]><![CDATA[CDATA[');
			data.push(segment);
		}
		data = data.join('');
	}
	
	if (needCDATA)
		data = '<![CDATA[' + data + ']]>';
	
	return data;
}

/*
	Function: _objectToXML
	
	Convert a javascript object or array into XML suitable for
	transmission to the server.
	
	obj - The object or array to convert.
	
	guard - An object used to track the level of recursion
		when encoding javascript objects.  When an object
		contains a reference to it's parent and the parent
		contains a reference to the child, an infinite
		recursion will cause some browsers to crash.
		
	Returns:
	
	string - the xml representation of the object or array.
	
	See also:
	
	<xajax.config.maxObjectDepth> and <xajax.config.maxObjectSize>
*/
xajax.tools._objectToXML = function(obj, guard) {
	var aXml = [];
	aXml.push('<xjxobj>');
	for (var key in obj) {
		++guard.size;
		if (guard.maxSize < guard.size)
			return aXml.join('');
		if ('undefined' != typeof obj[key]) {
			if ('constructor' == key)
				continue;
			if ('function' == typeof obj[key])
				continue;
			aXml.push('<e><k>');
			var val = xajax.tools._escape(key);
			aXml.push(val);
			aXml.push('</k><v>');
			if ('object' == typeof obj[key]) {
				++guard.depth;
				if (guard.maxDepth > guard.depth) {
					try {
						aXml.push(xajax.tools._objectToXML(obj[key], guard));
					} catch (e) {
						// do nothing, if the debug module is installed
						// it will catch the exception and handle it
					}
				}
				--guard.depth;
			} else {
				var val = xajax.tools._escape(obj[key]);
				if ('undefined' == typeof val || null == val) {
					aXml.push('*');
				} else {
					var sType = typeof val;
					if ('string' == sType)
						aXml.push('S');
					else if ('boolean' == sType)
						aXml.push('B');
					else if ('number' == sType)
						aXml.push('N');
					aXml.push(val);
				}
			}
				
			aXml.push('</v></e>');
		}
	}
	aXml.push('</xjxobj>');
	
	return aXml.join('');
}

/*
	Function: _enforceDataType
	
	Ensure that the javascript variable created is of the correct data type.

	Returns:
		
		(unknown) - The value provided converted to the correct data type.
*/
xajax.tools._enforceDataType = function(value) {
	value = new String(value);
	var type = value.substr(0, 1);
	value = value.substr(1);

	if ('*' == type)
		value = null;
	else if ('N' == type)
		value = value - 0;
	else if ('B' == type)
		value = !!value;
//	else if ('S' == type)
//		value = new String(value);

	return value;
}

/*
	Function: _nodeToObject
	
	Deserialize a javascript object from an XML node.
	
	node - A node, likely from the xml returned by the server.
	
	Returns:
	
		object - The object extracted from the xml node.
*/
xajax.tools._nodeToObject = function(node) {
	if (null == node)
		return '';
		
	if ('undefined' != typeof node.nodeName) {
		if ('#cdata-section' == node.nodeName || '#text' == node.nodeName) {
			var data = '';
			do if (node.data) data += node.data; while (node = node.nextSibling);
			return xajax.tools._enforceDataType(data);
		} else if ('xjxobj' == node.nodeName) {
			var key = null;
			var value = null;
			var data = new Array;
			var child = node.firstChild;
			while (child) {
				if ('e' == child.nodeName) {
					var grandChild = child.firstChild;
					while (grandChild) {
						if ('k' == grandChild.nodeName)
							// Don't support objects here, only number, string, etc...
							key = xajax.tools._enforceDataType(grandChild.firstChild.data);
						else ('v' == grandChild.nodeName)
							// Value might be object, string, number, boolean... even null or undefined
							value = xajax.tools._nodeToObject(grandChild.firstChild);
						grandChild = grandChild.nextSibling;
					}
					// Allow the value to be null or undefined (or a value)
					if (null != key) { // && null != value) {
						data[key] = value;
						key = value = null;
					}
				}
				child = child.nextSibling;
			}
			return data;
		}
	}
	
	throw { code: 10001, data: node.nodeName };
}

/*
	Function: getRequestObject
	
	Construct an XMLHttpRequest object dependent on the capabilities
	of the browser.
	
	Returns:
	
	object - Javascript XHR object.
*/
xajax.tools.getRequestObject = function() {
	if ('undefined' != typeof XMLHttpRequest) {
		xajax.tools.getRequestObject = function() {
			return new XMLHttpRequest();
		}
	} else if ('undefined' != typeof ActiveXObject) {
		xajax.tools.getRequestObject = function() {
			try {
				return new ActiveXObject('Msxml2.XMLHTTP.4.0');
			} catch (e) {
				xajax.tools.getRequestObject = function() {
					try {
						return new ActiveXObject('Msxml2.XMLHTTP');
					} catch (e2) {
						xajax.tools.getRequestObject = function() {
							return new ActiveXObject('Microsoft.XMLHTTP');
						}
						return xajax.tools.getRequestObject();
					}
				}
				return xajax.tools.getRequestObject();
			}
		}
	} else if (window.createRequest) {
		xajax.tools.getRequestObject = function() {
			return window.createRequest();
		}
	} else {
		xajax.tools.getRequestObject = function() {
			throw { code: 10002 };
		}
	}
	
	// this would seem to cause an infinite loop, however, the function should
	// be reassigned by now and therefore, it will not loop.
	return xajax.tools.getRequestObject();
}

/*
	Function: getBrowserHTML
	
	Insert the specified string of HTML into the document, then 
	extract it.  This gives the browser the ability to validate
	the code and to apply any transformations it deems appropriate.
	
	sValue - (string):
		A block of html code or text to be inserted into the
		browser's document.
		
	Returns:
	
	The (potentially modified) html code or text.
*/
xajax.tools.getBrowserHTML = function(sValue) {
	var oDoc = xajax.config.baseDocument;
	if (!oDoc.body)
		return '';
		
	var elWorkspace = xajax.$('xajax_temp_workspace');
	if (!elWorkspace)
	{
		elWorkspace = oDoc.createElement('div');
		elWorkspace.setAttribute('id', 'xajax_temp_workspace');
		elWorkspace.style.display = 'none';
		elWorkspace.style.visibility = 'hidden';
		oDoc.body.appendChild(elWorkspace);
	}
	elWorkspace.innerHTML = sValue;
	var browserHTML = elWorkspace.innerHTML;
	elWorkspace.innerHTML = '';	
	
	return browserHTML;
}

/*
	Function: willChange
	
	Tests to see if the specified data is the same as the current
	value of the element's attribute.
	
	element - (string or object):
		The element or it's unique name (specified by the ID attribute)
		
	attribute - (string):
		The name of the attribute.
		
	newData - (string):
		The value to be compared with the current value of the specified
		element.
		
	Returns:
	
	true - The specified value differs from the current attribute value.
	false - The specified value is the same as the current value.
*/
xajax.tools.willChange = function(element, attribute, newData) {
	if ('string' == typeof element)
		element = xajax.$(element);
	if (element) {
		var oldData;		
		eval('oldData=element.'+attribute);
		return (newData != oldData);
	}

	return false;
}

/*
	Function: getFormValues
	
	Build an associative array of form elements and their values from
	the specified form.
	
	element - (string): The unique name (id) of the form to be processed.
	disabled - (boolean, optional): Include form elements which are currently disabled.
	prefix - (string, optional): A prefix used for selecting form elements.

	Returns:
	
	An associative array of form element id and value.
*/
xajax.tools.getFormValues = function(parent) {
	var submitDisabledElements = false;
	if (arguments.length > 1 && arguments[1] == true)
		submitDisabledElements = true;
	
	var prefix = '';
	if(arguments.length > 2)
		prefix = arguments[2];
	
	if ('string' == typeof parent)
		parent = xajax.$(parent);
	
	var aFormValues = {};
	
//		JW: Removing these tests so that form values can be retrieved from a specified
//		container element like a DIV, regardless of whether they exist in a form or not.
//
//		if (parent.tagName)
//			if ('FORM' == parent.tagName.toUpperCase())
	if (parent)
		if (parent.childNodes)
			xajax.tools._getFormValues(aFormValues, parent.childNodes, submitDisabledElements, prefix);
	
	return aFormValues;
}

/*
	Function: _getFormValues
	
	Used internally by <xajax.tools.getFormValues> to recursively get the value
	of form elements.  This function will extract all form element values 
	regardless of the depth of the element within the form.
*/
xajax.tools._getFormValues = function(aFormValues, children, submitDisabledElements, prefix)
{
	var iLen = children.length;
	for (var i = 0; i < iLen; ++i) {
		var child = children[i];
		if ('undefined' != typeof child.childNodes)
			xajax.tools._getFormValues(aFormValues, child.childNodes, submitDisabledElements, prefix);
		xajax.tools._getFormValue(aFormValues, child, submitDisabledElements, prefix);
	}
}

/*
	Function: _getFormValue
	
	Used internally by <xajax.tools._getFormValues> to extract a single form value.
	This will detect the type of element (radio, checkbox, multi-select) and 
	add it's value(s) to the form values array.
*/
xajax.tools._getFormValue = function(aFormValues, child, submitDisabledElements, prefix)
{
	if (!child.name)
		return;
		
	if (child.disabled)
		if (true == child.disabled)
			if (false == submitDisabledElements)
				return;
				
	if (prefix != child.name.substring(0, prefix.length))
		return;
		
	if (child.type)
		if (child.type == 'radio' || child.type == 'checkbox')
			if (false == child.checked)
				return;

	var name = child.name;

	var values = [];

 	if ('select-multiple' == child.type) {
 		var jLen = child.length;
 		for (var j = 0; j < jLen; ++j) {
 			var option = child.options[j];
 			if (true == option.selected)
 				values.push(option.value);
 		}
 	} else {
 		values = child.value;
 	}
	var keyBegin = name.indexOf('[');
	if (0 <= keyBegin) {
		var n = name;
		var k = n.substr(0, n.indexOf('['));
		var a = n.substr(n.indexOf('['));
		if (typeof aFormValues[k] == 'undefined') 
			aFormValues[k] = {};
		var p = aFormValues; // pointer reset
		while (a.length != 0) {
			var sa = a.substr(0, a.indexOf(']')+1);
			
			var lk = k; //save last key
			var lp = p; //save last pointer
			
			a = a.substr(a.indexOf(']')+1);
			p = p[k];
			k = sa.substr(1, sa.length-2);
			if (k == '') {
				if ('select-multiple' == child.type) {
					k = lk; //restore last key
					p = lp;
				} else {
					var i = 0;
					for (afoo in p) i++;
					k = i; 
				}
			}
			if (typeof p[k] == 'undefined') {
				p[k] = {}; 
			}
		}
		p[k] = values;
	} else {
		aFormValues[name] = values;
	}
}

/*
	Function: stripOnPrefix
	
	Detect, and if found, remove the prefix 'on' from the specified 
	string.  This is used while working with event handlers.
	
	sEventName - (string): The string to be modified.
	
	Returns:
	
	string - The modified string.
*/
xajax.tools.stripOnPrefix = function(sEventName) {
	sEventName = sEventName.toLowerCase();
	if (0 == sEventName.indexOf('on'))
		sEventName = sEventName.replace(/on/,'');
	
	return sEventName;
}

/*
	Function: addOnPrefix
	
	Detect, and add if not found, the prefix 'on' from the specified 
	string.  This is used while working with event handlers.
	
	sEventName - (string): The string to be modified.
	
	Returns:
	
	string - The modified string.
*/
xajax.tools.addOnPrefix = function(sEventName) {
	sEventName = sEventName.toLowerCase();
	if (0 != sEventName.indexOf('on'))
		sEventName = 'on' + sEventName;
	
	return sEventName;
}

/*
	Class: xajax.tools.xml
	
	An object that contains utility function for processing
	xml response packets.
*/
xajax.tools.xml = {};

/*
	Function: parseAttributes
	
	Take the parameters passed in the command of the XML response
	and convert them to parameters of the args object.  This will 
	serve as the command object which will be stored in the 
	response command queue.
	
	child - (object):  The xml child node which contains the 
		attributes for the current response command.
		
	obj - (object):  The current response command that will have the
		attributes applied.
*/
xajax.tools.xml.parseAttributes = function(child, obj) {
	var iLen = child.attributes.length;
	for (var i = 0; i < iLen; ++i) {
		var attr = child.attributes[i];
		obj[attr.name] = attr.value;
	}
}

/*
	Function: parseChildren
	
	Parses the child nodes of the command of the response XML.  Generally,
	the child nodes contain the data element of the command; this member
	may be an object, which will be deserialized by <xajax._nodeToObject>
	
	child - (object):   The xml node that contains the child (data) for
		the current response command object.
		
	obj - (object):  The response command object.
*/
xajax.tools.xml.parseChildren = function(child, obj) {
	obj.data = '';
	if (0 < child.childNodes.length) {
		if (1 < child.childNodes.length) {
			var grandChild = child.firstChild;
			do {
				if ('#cdata-section' == grandChild.nodeName || '#text' == grandChild.nodeName) {
					obj.data += grandChild.data;
				}
			} while (grandChild = grandChild.nextSibling);
		} else {
			var grandChild = child.firstChild;
			if ('xjxobj' == grandChild.nodeName) {
				obj.data = xajax.tools._nodeToObject(grandChild);
				return;
			} else if ('#cdata-section' == grandChild.nodeName || '#text' == grandChild.nodeName) {
				obj.data = grandChild.data;
			}
		}
	} else if ('undefined' != typeof child.data) {
		obj.data = child.data;
	}
	
	obj.data = xajax.tools._enforceDataType(obj.data);
}

/*
	Function: processFragment
	
	xmlNode - (object):  The first xml node in the xml fragment.
	seq - (number):  A counter used to keep track of the sequence
		of this command in the response.
	oRet - (object):  A variable that is used to return the request
		"return value" for use with synchronous requests.
*/
xajax.tools.xml.processFragment = function(xmlNode, seq, oRequest) {
	var xx = xajax;
	var xt = xx.tools;
	
	while (xmlNode) {
		if ('cmd' == xmlNode.nodeName) {
			var obj = {};
			obj.fullName = '*unknown*';
			obj.sequence = seq;
			obj.request = oRequest;
			obj.context = oRequest.context;
			
			xt.xml.parseAttributes(xmlNode, obj);
			xt.xml.parseChildren(xmlNode, obj);
			
			xt.queue.push(xx.response, obj);
		} else if ('xjxrv' == xmlNode.nodeName) {
			oRequest.returnValue = xt._nodeToObject(xmlNode.firstChild);
		} else if ('debugmsg' == xmlNode.nodeName) {
			// txt = xt._nodeToObject(xmlNode.firstChild);
		} else 
			throw { code: 10004, data: xmlNode.nodeName }
			
		++seq;
		xmlNode = xmlNode.nextSibling;
	}
}

/*
	Class: xajax.tools.queue
	
	This contains the code and variables for building, populating
	and processing First In Last Out (FILO) buffers.
*/
xajax.tools.queue = {}

/*
	Function: create
	
	Construct and return a new queue object.
	
	size - (integer):
		The number of entries the queue will be able to hold.
*/
xajax.tools.queue.create = function(size) {
	return {
		start: 0,
		size: size,
		end: 0,
		commands: [],
		timeout: null
	}
}

/*
	Function: retry
	
	Maintains a retry counter for the given object.
	
	obj - (object):
		The object to track the retry count for.
		
	count - (integer):
		The number of times the operation should be attempted
		before a failure is indicated.
		
	Returns:
	
	true - The object has not exhausted all the retries.
	false - The object has exhausted the retry count specified.
*/
xajax.tools.queue.retry = function(obj, count) {
	var retries = obj.retries;
	if (retries) {
		--retries;
		if (1 > retries)
			return false;
	} else retries = count;
	obj.retries = retries;
	return true;
}

/*
	Function: rewind
	
	Rewind the buffer head pointer, effectively reinserting the 
	last retrieved object into the buffer.
	
	theQ - (object):
		The queue to be rewound.
*/
xajax.tools.queue.rewind = function(theQ) {
	if (0 < theQ.start)
		--theQ.start;
	else
		theQ.start = theQ.size;
}

/*
	Function: setWakeup
	
	Set or reset a timeout that is used to restart processing
	of the queue.  This allows the queue to asynchronously wait
	for an event to occur (giving the browser time to process
	pending events, like loading files)
	
	theQ - (object):
		The queue to process upon timeout.
		
	when - (integer):
		The number of milliseconds to wait before starting/
		restarting the processing of the queue.
*/
xajax.tools.queue.setWakeup = function(theQ, when) {
	if (null != theQ.timeout) {
		clearTimeout(theQ.timeout);
		theQ.timeout = null;
	}
	theQ.timout = setTimeout(function() { xajax.tools.queue.process(theQ); }, when);
}

/*
	Function: process
	
	While entries exist in the queue, pull and entry out and
	process it's command.  When a command returns false, the
	processing is halted.
	
	theQ - (object): The queue object to process.  This should
		have been crated by calling <xajax.tools.queue.create>.
	
	Returns:

	true - The queue was fully processed and is now empty.
	false - The queue processing was halted before the 
		queue was fully processed.
		
	Notes:
	
	- Use <xajax.tools.queue.setWakeup> or call this function to 
	cause the queue processing to continue.

	- This will clear the associated timeout, this function is not 
	designed to be reentrant.
	
	- When an exception is caught, do nothing; if the debug module 
	is installed, it will catch the exception and handle it.
*/
xajax.tools.queue.process = function(theQ) {
	if (null != theQ.timeout) {
		clearTimeout(theQ.timeout);
		theQ.timeout = null;
	}
	var obj = xajax.tools.queue.pop(theQ);
	while (null != obj) {
		try {
			if (false == xajax.executeCommand(obj)) 
				return false;
		} catch (e) {
		}
		delete obj;
		
		obj = xajax.tools.queue.pop(theQ);
	}
	return true;
}

/*
	Function: push
	
	Push a new object into the tail of the buffer maintained by the
	specified queue object.
	
	theQ - (object):
		The queue in which you would like the object stored.
		
	obj - (object):
		The object you would like stored in the queue.
*/
xajax.tools.queue.push = function(theQ, obj) {
	var next = theQ.end + 1;
	if (next > theQ.size)
		next = 0;
	if (next != theQ.start) {				
		theQ.commands[theQ.end] = obj;
		theQ.end = next;
	} else
		throw { code: 10003 }
}

/*
	Function: pushFront
	
	Push a new object into the head of the buffer maintained by 
	the specified queue object.  This effectively pushes an object
	to the front of the queue... it will be processed first.
	
	theQ - (object):
		The queue in which you would like the object stored.
		
	obj - (object):
		The object you would like stored in the queue.
*/
xajax.tools.queue.pushFront = function(theQ, obj) {
	xajax.tools.queue.rewind(theQ);
	theQ.commands[theQ.start] = obj;
}

/*
	Function: pop
	
	Attempt to pop an object off the head of the queue.
	
	theQ - (object):
		The queue object you would like to modify.
		
	Returns:
	
	object - The object that was at the head of the queue or
		null if the queue was empty.
*/
xajax.tools.queue.pop = function(theQ) {
	var next = theQ.start;
	if (next == theQ.end)
		return null;
	next++;
	if (next > theQ.size)
		next = 0;
	var obj = theQ.commands[theQ.start];
	delete theQ.commands[theQ.start];
	theQ.start = next;
	return obj;
}

/*
	Class: xajax.responseProcessor
*/
xajax.responseProcessor = {};

/*
	Function: xml
	
	Parse the response XML into a series of commands.  The commands
	are constructed by calling <xajax.tools.xml.parseAttributes> and 
	<xajax.tools.xml.parseChildren>.
	
	oRequest - (object):  The request context object.
*/
xajax.responseProcessor.xml = function(oRequest) {
	var xx = xajax;
	var xt = xx.tools;
	var xcb = xx.callback;
	var gcb = xcb.global;
	var lcb = oRequest.callback;
	
	
	if (xt.arrayContainsValue(xx.responseSuccessCodes, oRequest.request.status)) {
		xcb.execute([gcb, lcb], 'onSuccess', oRequest);
		var seq = 0;
		if (oRequest.request.responseXML) {
			var responseXML = oRequest.request.responseXML;
			if (responseXML.documentElement) {
				oRequest.status.onProcessing();
				
				var child = responseXML.documentElement.firstChild;
				xt.xml.processFragment(child, seq, oRequest);
			}
		}
		
		var obj = {};
		obj.fullName = 'Response Complete';
		obj.sequence = seq;
		obj.request = oRequest;
		obj.context = oRequest.context;
		obj.cmd = 'rcmplt';
		xt.queue.push(xx.response, obj);
		
		// do not re-start the queue if a timeout is set
		if (null == xx.response.timeout)
			xt.queue.process(xx.response);
	} else if (xt.arrayContainsValue(xx.responseRedirectCodes, oRequest.request.status)) {
		xcb.execute([gcb, lcb], 'onRedirect', oRequest);
		window.location = oRequest.request.getResponseHeader('location');
		xx.completeResponse(oRequest);
	} else if (xt.arrayContainsValue(xx.responseErrorsForAlert, oRequest.request.status)) {
		xcb.execute([gcb, lcb], 'onFailure', oRequest);
		xx.completeResponse(oRequest);
	}
	
	return oRequest.returnValue;
}

/*
	Class: xajax.js
	
	Contains the functions for javascript file and function
	manipulation.
*/
xajax.js = {}

/*
	Function: includeOnce
	
	Add a reference to the specified script file if one does not
	already exist in the HEAD of the current document.
	
	This will effecitvely cause the script file to be loaded in
	the browser.
	
	fileName - (string):  The URI of the file.
	
	Returns:
	
	true - The reference exists or was added.
*/
xajax.js.includeScriptOnce = function(command) {
	command.fullName = 'includeScriptOnce';
	var fileName = command.data;
	// Check for existing script tag for this file.
	var oDoc = xajax.config.baseDocument;
    var loadedScripts = oDoc.getElementsByTagName('script');
	var iLen = loadedScripts.length;
    for (var i = 0; i < iLen; ++i) {
		var script = loadedScripts[i];
        if (script.src) {
			if (0 <= script.src.indexOf(fileName))
				return true;
		}
    }
	return xajax.js.includeScript({data:fileName});
}

/*
	Function: includeScript
	
	Adds a SCRIPT tag referencing the specified file.  This
	effectively causes the script to be loaded in the browser.
	
	fileName - (string):  The URI of the file.
	
	Returns:
	
	true - The reference was added.
*/
xajax.js.includeScript = function(command) {
	command.fullName = 'includeScript';
	var fileName = command.data;

	var oDoc = xajax.config.baseDocument;
	var objHead = oDoc.getElementsByTagName('head');
	var objScript = oDoc.createElement('script');
	objScript.type = 'text/javascript';
	objScript.src = fileName;
	if ('function' == command.onload) {
		objScript.onload.command.onload;	
	}
	objHead[0].appendChild(objScript);
	return true;
}

/*
	Function: removeScript
	
	Locates a SCRIPT tag in the HEAD of the document which references
	the specified file and removes it.
	
	fileName - (string):  The URI of the script file.
	unload - (function, optional):  The function to call just before
		the file reference is removed.  This can be used to clean up
		objects that reference code from that script file.
		
	Returns:
	
	true - The script was not found or was removed.
*/
xajax.js.removeScript = function(command) {
	command.fullName = 'removeScript';
	var fileName = command.data;
	var unload = command.unld;
	var oDoc = xajax.config.baseDocument;
	var loadedScripts = oDoc.getElementsByTagName('script');
	var iLen = loadedScripts.length;
	for (var i = 0; i < iLen; ++i) {
		var script = loadedScripts[i];
		if (script.src) {
			if (0 <= script.src.indexOf(fileName)) {
				if ('undefined' != typeof unload) {
					var args = {};
					args.data = unload;
					args.context = window;
					xajax.js.execute(args);
				}
				var parent = script.parentNode;
				parent.removeChild(script);
			}
		}
	}
	return true;
}

/*
	Function: sleep
	
	Causes the processing of items in the queue to be delayed
	for the specified amount of time.  This is an asynchronous
	operation, therefore, other operations will be given an
	opportunity to execute during this delay.
	
	args - (object):  The response command containing the following
		parameters.
		- args.prop: The number of 10ths of a second to sleep.
	
	Returns:
	
	true - The sleep operation completed.
	false - The sleep time has not yet expired, continue sleeping.
*/
xajax.js.sleep = function(command) {
	command.fullName = 'sleep';
	// inject a delay in the queue processing
	// handle retry counter
	if (xajax.tools.queue.retry(command, command.prop)) {
		xajax.tools.queue.setWakeup(xajax.response, 100);
		return false;
	}
	// wake up, continue processing queue
	return true;
}

/*
	Function: confirmCommands
	
	Prompt the user with the specified text, if the user responds by clicking
	cancel, then skip the specified number of commands in the response command
	queue.  If the user clicks Ok, the command processing resumes normal
	operation.
	
	msg - (string):  The message to display to the user.
	numberOfCommands - (integer):  The number of commands to skip if the user
		clicks Cancel.
		
	Returns:
	
	true - The operation completed successfully.
*/
xajax.js.confirmCommands = function(command) {
	command.fullName = 'confirmCommands';
	var msg = command.data;
	var numberOfCommands = command.id;
	if (false == confirm(msg)) {
		while (0 < numberOfCommands) {
			xajax.tools.queue.pop(xajax.response);
			--numberOfCommands;
		}
	}
	return true;
}

/*
	Function: execute
	
	Execute the specified string of javascript code, using the current
	script context.
	
	args - The response command object containing the following:
		- args.data: (string):  The javascript to be evaluated.
		- args.context: (object):  The javascript object that to be
			referenced as 'this' in the script.
			
	Returns:
	
	unknown - A value set by the script using 'returnValue = '
	true - If the script does not set a returnValue.
*/
xajax.js.execute = function(args) {
	args.fullName = 'execute Javascript';
	var returnValue = true;
	args.context.xajaxDelegateCall = function() {
		eval(args.data);
	}
	args.context.xajaxDelegateCall();
	return returnValue;
}

/*
	Function: waitFor
	
	Test for the specified condition, using the current script
	context; if the result is false, sleep for 1/10th of a
	second and try again.
	
	args - The response command object containing the following:
	
		- args.data: (string):  The javascript to evaluate.
		- args.prop: (integer):  The number of 1/10ths of a
			second to wait before giving up.
		- args.context: (object):  The current script context object
			which is accessable in the javascript being evaulated
			via the 'this' keyword.
	
	Returns:
	
	false - The condition evaulates to false and the sleep time
		has not expired.
	true - The condition evaluates to true or the sleep time has
		expired.
*/
xajax.js.waitFor = function(args) {
	args.fullName = 'waitFor';

	var bResult = false;
	var cmdToEval = 'bResult = (';
	cmdToEval += args.data;
	cmdToEval += ');';
	try {
		args.context.xajaxDelegateCall = function() {
			eval(cmdToEval);
		}
		args.context.xajaxDelegateCall();
	} catch (e) {
	}
	if (false == bResult) {
		// inject a delay in the queue processing
		// handle retry counter
		if (xajax.tools.queue.retry(args, args.prop)) {
			xajax.tools.queue.setWakeup(xajax.response, 100);
			return false;
		}
		// give up, continue processing queue
	}
	return true;
}

/*
	Function: call
	
	Call a javascript function with a series of parameters using 
	the current script context.
	
	args - The response command object containing the following:
		- args.data: (array):  The parameters to pass to the function.
		- args.func: (string):  The name of the function to call.
		- args.context: (object):  The current script context object
			which is accessable in the function name via the 'this'
			keyword.
			
	Returns:
	
	true - The call completed successfully.
*/
xajax.js.call = function(args) {
	args.fullName = 'call js function';
	
	var parameters = args.data;
	
	var scr = new Array();
	scr.push(args.func);
	scr.push('(');
	if ('undefined' != typeof parameters) {
		if ('object' == typeof parameters) {
			var iLen = parameters.length;
			if (0 < iLen) {
				scr.push('parameters[0]');
				for (var i = 1; i < iLen; ++i)
					scr.push(', parameters[' + i + ']');
			}
		}
	}
	scr.push(');');
	args.context.xajaxDelegateCall = function() {
		eval(scr.join(''));
	}
	args.context.xajaxDelegateCall();
	return true;
}

/*
	Function: setFunction

	Constructs the specified function using the specified javascript
	as the body of the function.
	
	args - The response command object which contains the following:
	
		- args.func: (string):  The name of the function to construct.
		- args.data: (string):  The script that will be the function body.
		- args.context: (object):  The current script context object
			which is accessable in the script name via the 'this' keyword.
			
	Returns:
	
	true - The function was constructed successfully.
*/
xajax.js.setFunction = function(args) {
	args.fullName = 'setFunction';

	var code = new Array();
	code.push(args.func);
	code.push(' = function(');
	if ('object' == typeof args.prop) {
		var separator = '';
		for (var m in args.prop) {
			code.push(separator);
			code.push(args.prop[m]);
			separator = ',';
		}
	} else code.push(args.prop);
	code.push(') { ');
	code.push(args.data);
	code.push(' }');
	args.context.xajaxDelegateCall = function() {
		eval(code.join(''));
	}
	args.context.xajaxDelegateCall();
	return true;
}

/*
	Function: wrapFunction
	
	Construct a javascript function which will call the original function with 
	the same name, potentially executing code before and after the call to the
	original function.
	
	args - (object):  The response command object which will contain 
		the following:
		
		- args.func: (string):  The name of the function to be wrapped.
		- args.prop: (string):  List of parameters used when calling the function.
		- args.data: (array):  The portions of code to be called before, after
			or even between calls to the original function.
		- args.context: (object):  The current script context object which is 
			accessable in the function name and body via the 'this' keyword.
			
	Returns:
	
	true - The wrapper function was constructed successfully.
*/
xajax.js.wrapFunction = function(args) {
	args.fullName = 'wrapFunction';

	var code = new Array();
	code.push(args.func);
	code.push(' = xajax.js.makeWrapper(');
	code.push(args.func);
	code.push(', args.prop, args.data, args.type, args.context);');
	args.context.xajaxDelegateCall = function() {
		eval(code.join(''));
	}
	args.context.xajaxDelegateCall();
	return true;
}

/*
	Function: makeWrapper
	
	Helper function used in the wrapping of an existing javascript function.
	
	origFun - (string):  The name of the original function.
	args - (string):  The list of parameters used when calling the function.
	codeBlocks - (array):  Array of strings of javascript code to be executed
		before, after and perhaps between calls to the original function.
	returnVariable - (string):  The name of the variable used to retain the
		return value from the call to the original function.
	context - (object):  The current script context object which is accessable
		in the function name and body via the 'this' keyword.
		
	Returns:
	
	object - The complete wrapper function.
*/
xajax.js.makeWrapper = function(origFun, args, codeBlocks, returnVariable, context) {
	var originalCall = '';
	if (0 < returnVariable.length) {
		originalCall += returnVariable;
		originalCall += ' = ';
	}
	var originalCall = 	'origFun(';
	originalCall += args;
	originalCall += '); ';
	
	var code = 'wrapper = function(';
	code += args;
	code += ') { ';
	
	if (0 < returnVariable.length) {
		code += ' var ';
		code += returnVariable;
		code += ' = null;';
	}
	var separator = '';
	var bLen = codeBlocks.length;
	for (var b = 0; b < bLen; ++b) {
		code += separator;
		code += codeBlocks[b];
		separator = originalCall;
	}
	if (0 < returnVariable.length) {
		code += ' return ';
		code += returnVariable;
		code += ';';
	}
	code += ' } ';
	
	var wrapper = null;
	context.xajaxDelegateCall = function() {
		eval(code);
	}
	context.xajaxDelegateCall();
	return wrapper;
}

/*
	Class: xajax.dom
*/
xajax.dom = {}

/*
	Function: assign
	
	Assign an element's attribute to the specified value.
	
	element - (object):  The HTML element to effect.
	property - (string):  The name of the attribute to set.
	data - (string):  The new value to be applied.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.dom.assign = function(element, property, data) {
	if ('string' == typeof element)
		element = xajax.$(element);
	
	switch (property) {
	case 'innerHTML':
			element.innerHTML = data;
		break;
	case 'outerHTML':
		if ('undefined' == typeof element.outerHTML) {
			var r = xajax.config.baseDocument.createRange();
			r.setStartBefore(element);
			var df = r.createContextualFragment(data);
			element.parentNode.replaceChild(df, element);
		} else element.outerHTML = data;
		break;
	default:
		if (xajax.tools.willChange(element, property, data))
			eval('element.' + property + ' = data;');
		break;
	}
	return true;
}

/*
	Function: append
	
	Append the specified value to an element's attribute.
	
	element - (object):  The HTML element to effect.
	property - (string):  The name of the attribute to append to.
	data - (string):  The new value to be appended.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.dom.append = function(element, property, data) {
	if ('string' == typeof element)
		element = xajax.$(element);
	
	eval('element.' + property + ' += data;');
	return true;
}

/*
	Function: prepend
	
	Prepend the specified value to an element's attribute.
	
	element - (object):  The HTML element to effect.
	property - (string):  The name of the attribute.
	data - (string):  The new value to be prepended.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.dom.prepend = function(element, property, data) {
	if ('string' == typeof element)
		element = xajax.$(element);
	
	eval('element.' + property + ' = data + element.' + property);
	return true;
}

/*
	Function: replace
	
	Search and replace the specified text.
	
	element - (string or object):  The name of, or the element itself which is
		to be modified.
	sAttribute - (string):  The name of the attribute to be set.
	aData - (array):  The search text and replacement text.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.dom.replace = function(element, sAttribute, aData) {
	var sSearch = aData['s'];
	var sReplace = aData['r'];
	
	if (sAttribute == 'innerHTML')
		sSearch = xajax.tools.getBrowserHTML(sSearch);
	
	if ('string' == typeof element)
		element = xajax.$(element);
	
	eval('var txt = element.' + sAttribute);
	
	var bFunction = false;
	if ('function' == typeof txt) {
        txt = txt.join('');
        bFunction = true;
    }
	
	var start = txt.indexOf(sSearch);
	if (start > -1) {
		var newTxt = [];
		while (start > -1) {
			var end = start + sSearch.length;
			newTxt.push(txt.substr(0, start));
			newTxt.push(sReplace);
			txt = txt.substr(end, txt.length - end);
			start = txt.indexOf(sSearch);
		}
		newTxt.push(txt);
		newTxt = newTxt.join('');
		
		if (bFunction) {
			eval('element.' + sAttribute + '=newTxt;');
		} else if (xajax.tools.willChange(element, sAttribute, newTxt)) {
			eval('element.' + sAttribute + '=newTxt;');
		}
	}
	return true;
}

/*
	Function: remove
	
	Delete an element.
	
	element - (string or object):  The name of, or the element itself which
		will be deleted.
		
	Returns:
	
	true - The operation completed successfully.
*/
xajax.dom.remove = function(element) {
	if ('string' == typeof element)
		element = xajax.$(element);
	
	if (element && element.parentNode && element.parentNode.removeChild)
		element.parentNode.removeChild(element);

	return true;
}

/*
	Function: create
	
	Create a new element and append it to the specified parent element.
	
	objParent - (string or object):  The name of, or the element itself
		which will contain the new element.
	sTag - (string):  The tag name for the new element.
	sId - (string):  The value to be assigned to the id attribute of
		the new element.
		
	Returns:
	
	true - The operation completed successfully.
*/
xajax.dom.create = function(objParent, sTag, sId) {
	if ('string' == typeof objParent)
		objParent = xajax.$(objParent);
	var target = xajax.config.baseDocument.createElement(sTag);
	target.setAttribute('id', sId);
	if (objParent)
		objParent.appendChild(target);
	return true;
}

/*
	Function: insert
	
	Insert a new element before the specified element.

	objSibling - (string or object):  The name of, or the element itself
		that will be used as the reference point for insertion.
	sTag - (string):  The tag name for the new element.
	sId - (string):  The value that will be assigned to the new element's
		id attribute.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.dom.insert = function(objSibling, sTag, sId) {
	if ('string' == typeof objSibling)
		objSibling = xajax.$(objSibling);
	var target = xajax.config.baseDocument.createElement(sTag);
	target.setAttribute('id', sId);
	objSibling.parentNode.insertBefore(target, objSibling);
	return true;
}

/*
	Function: insertAfter
	
	Insert a new element after the specified element.

	objSibling - (string or object):  The name of, or the element itself
		that will be used as the reference point for insertion.
	sTag - (string):  The tag name for the new element.
	sId - (string):  The value that will be assigned to the new element's
		id attribute.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.dom.insertAfter = function(objSibling, sTag, sId) {
	if ('string' == typeof objSibling)
		objSibling = xajax.$(objSibling);
	var target = xajax.config.baseDocument.createElement(sTag);
	target.setAttribute('id', sId);
	objSibling.parentNode.insertBefore(target, objSibling.nextSibling);
	return true;
}

/*
	Function: contextAssign
	
	Assign a value to a named member of the current script context object.
	
	args - (object):  The response command object which will contain the
		following:
		
		- args.prop: (string):  The name of the member to assign.
		- args.data: (string or object):  The value to assign to the member.
		- args.context: (object):  The current script context object which
			is accessable via the 'this' keyword.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.dom.contextAssign = function(args) {
	args.fullName = 'context assign';

	var code = [];
	code.push('this.');
	code.push(args.prop);
	code.push(' = data;');
	code = code.join('');
	args.context.xajaxDelegateCall = function(data) {
		eval(code);
	}
	args.context.xajaxDelegateCall(args.data);
	return true;
}

/*
	Function: contextAppend
	
	Appends a value to a named member of the current script context object.
	
	args - (object):  The response command object which will contain the
		following:
		
		- args.prop: (string):  The name of the member to append to.
		- args.data: (string or object):  The value to append to the member.
		- args.context: (object):  The current script context object which
			is accessable via the 'this' keyword.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.dom.contextAppend = function(args) {
	args.fullName = 'context append';

	var code = [];
	code.push('this.');
	code.push(args.prop);
	code.push(' += data;');
	code = code.join('');
	args.context.xajaxDelegateCall = function(data) {
		eval(code);
	}
	args.context.xajaxDelegateCall(args.data);
	return true;
}

/*
	Function: contextPrepend
	
	Prepend a value to a named member of the current script context object.
	
	args - (object):  The response command object which will contain the
		following:
		
		- args.prop: (string):  The name of the member to prepend to.
		- args.data: (string or object):  The value to prepend to the member.
		- args.context: (object):  The current script context object which
			is accessable via the 'this' keyword.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.dom.contextPrepend = function(args) {
	args.fullName = 'context prepend';

	var code = [];
	code.push('this.');
	code.push(args.prop);
	code.push(' = data + this.');
	code.push(args.prop);
	code.push(';');
	code = code.join('');
	args.context.xajaxDelegateCall = function(data) {
		eval(code);
	}
	args.context.xajaxDelegateCall(args.data);
	return true;
}


/*
	Class: xajax.css
*/
xajax.css = {}

/*
	Function: add
	
	Add a LINK reference to the specified .css file if it does not
	already exist in the HEAD of the current document.
	
	filename - (string):  The URI of the .css file to reference.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.css.add = function(filename) {
	var oDoc = xajax.config.baseDocument;
	var oHeads = oDoc.getElementsByTagName('head');
	var oHead = oHeads[0];
	var oLinks = oHead.getElementsByTagName('link');
	
	var found = false;
	var iLen = oLinks.length;
	for (var i = 0; i < iLen && false == found; ++i)
		if (0 < oLinks[i].href.indexOf(filename))
			found = true;
	
	if (false == found) {
		var oCSS = oDoc.createElement('link');
		oCSS.rel = 'stylesheet';
		oCSS.type = 'text/css';
		oCSS.href = filename;
		oHead.appendChild(oCSS);
	}
	
	return true;
}

/*
	Function: remove
	
	Locate and remove a LINK reference from the current document's
	HEAD.
	
	filename - (string):  The URI of the .css file.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.css.remove = function(filename) {
	var oDoc = xajax.config.baseDocument;
	var oHeads = oDoc.getElementsByTagName('head');
	var oHead = oHeads[0];
	var oLinks = oHead.getElementsByTagName('link');
	
	var i = 0;
	while (i < oLinks.length)
		if (0 <= oLinks[i].href.indexOf(filename))
			oHead.removeChild(oLinks[i]);
		else ++i;
	
	return true;
}

/*
	Function: waitForCSS
	
	Attempt to detect when all .css files have been loaded once
	they are referenced by a LINK tag in the HEAD of the current
	document.
	
	args - (object):  The response command object which will contain
		the following:
		
		- args.prop - (integer):  The number of 1/10ths of a second
			to wait before giving up.
	
	Returns:
	
	true - The .css files appear to be loaded.
	false - The .css files do not appear to be loaded and the timeout
		has not expired.
*/
xajax.css.waitForCSS = function(args) {
	var oDocSS = xajax.config.baseDocument.styleSheets;
	var ssEnabled = [];
	var iLen = oDocSS.length;
	for (var i = 0; i < iLen; ++i) {
		ssEnabled[i] = 0;
		try {
			ssEnabled[i] = oDocSS[i].cssRules.length;
		} catch (e) {
			try {
				ssEnabled[i] = oDocSS[i].rules.length;
			} catch (e) {
			}
		}
	}
	
	var ssLoaded = true;
	var iLen = ssEnabled.length;
	for (var i = 0; i < iLen; ++i)
		if (0 == ssEnabled[i])
			ssLoaded = false;
	
	if (false == ssLoaded) {
		// inject a delay in the queue processing
		// handle retry counter
		if (xajax.tools.queue.retry(args, args.prop)) {
			xajax.tools.queue.setWakeup(xajax.response, 10);
			return false;
		}
		// give up, continue processing queue
	}
	return true;
}


/*
	Class: xajax.forms
*/
xajax.forms = {}

/*
	Function: getInput
	
	Create and return a form input element with the specified parameters.
	
	type - (string):  The type of input element desired.
	name - (string):  The value to be assigned to the name attribute.
	id - (string):  The value to be assigned to the id attribute.
	
	Returns:
	
	object - The new input element.
*/
xajax.forms.getInput = function(type, name, id) {
	if ('undefined' == typeof window.addEventListener) {
		xajax.forms.getInput = function(type, name, id) {
			return xajax.config.baseDocument.createElement('<input type="'+type+'" name="'+name+'" id="'+id+'">');
		}
	} else {
		xajax.forms.getInput = function(type, name, id) {
			var oDoc = xajax.config.baseDocument;
			var Obj = oDoc.createElement('input');
			Obj.setAttribute('type', type);
			Obj.setAttribute('name', name);
			Obj.setAttribute('id', id);
			return Obj;
		}
	}
	return xajax.forms.getInput(type, name, id);
}

/*
	Function: createInput
	
	Create a new input element under the specified parent.
	
	objParent - (string or object):  The name of, or the element itself
		that will be used as the reference for the insertion.
	sType - (string):  The value to be assigned to the type attribute.
	sName - (string):  The value to be assigned to the name attribute.
	sId - (string):  The value to be assigned to the id attribute.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.forms.createInput = function(command) {
	command.fullName = 'createInput';
	var objParent = command.id;
	var sType = args.type;
	var sName = args.data;
	var sId = args.prop;
	if ('string' == typeof objParent)
		objParent = xajax.$(objParent);
	var target = xajax.forms.getInput(sType, sName, sId);
	if (objParent && target)
		objParent.appendChild(target);
	return true;
}

/*
	Function: insertInput
	
	Insert a new input element before the specified element.
	
	objSibling - (string or object):  The name of, or the element itself
		that will be used as the reference for the insertion.
	sType - (string):  The value to be assigned to the type attribute.
	sName - (string):  The value to be assigned to the name attribute.
	sId - (string):  The value to be assigned to the id attribute.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.forms.insertInput = function(command) {
	command.fullName = 'insertInput';
	var objSibling = command.id;
	var sType = command.type;
	var sName = command.data;
	var sId = command.prop;
	if ('string' == typeof objSibling)
		objSibling = xajax.$(objSibling);
	var target = xajax.forms.getInput(sType, sName, sId);
	if (target && objSibling && objSibling.parentNode)
		objSibling.parentNode.insertBefore(target, objSibling);
	return true;
}

/*
	Function: insertInputAfter

	Insert a new input element after the specified element.
	
	objSibling - (string or object):  The name of, or the element itself
		that will be used as the reference for the insertion.
	sType - (string):  The value to be assigned to the type attribute.
	sName - (string):  The value to be assigned to the name attribute.
	sId - (string):  The value to be assigned to the id attribute.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.forms.insertInputAfter = function(command) {
	command.fullName = 'insertInputAfter';
	var objSibling = command.id;
	var sType = command.type;
	var sName = command.data;
	var sId = command.prop;
	if ('string' == typeof objSibling)
		objSibling = xajax.$(objSibling);
	var target = xajax.forms.getInput(sType, sName, sId);
	if (target && objSibling && objSibling.parentNode)
		objSibling.parentNode.insertBefore(target, objSibling.nextSibling);
	return true;
}

/*
	Class: xajax.events
*/
xajax.events = {}

/*
	Function: setEvent
	
	Set an event handler.
	
	element - (string or object):  The name of, or the object itself.
	event - (string):  The name of the event to set.
	code - (string):  The javascript code to be assigned to this event.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.events.setEvent = function(command) {
	command.fullName = 'addEvent';
	var element = command.id;
	var sEvent = command.prop;
	var code = command.data;
	if ('string' == typeof element)
		element = xajax.$(element);
	sEvent = xajax.tools.addOnPrefix(sEvent);
	code = xajax.tools.doubleQuotes(code);
	eval('element.' + sEvent + ' = function() { ' + code + '; }');
	return true;
}

/*
	Function: addHandler
	
	Add an event handler to the specified element.
	
	element - (string or object):  The name of, or the element itself
		which will have the event handler assigned.
	sEvent - (string):  The name of the event.
	fun - (string):  The function to be called.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.events.addHandler = function(element, sEvent, fun) {
	if (window.addEventListener) {
		xajax.events.addHandler = function(command) {
			command.fullName = 'addHandler';
			var element = command.id;
			var sEvent = command.prop;
			var fun = command.data;
			if ('string' == typeof element)
				element = xajax.$(element);
			sEvent = xajax.tools.stripOnPrefix(sEvent);
			eval('element.addEventListener("' + sEvent + '", ' + fun + ', false);');
			return true;
		}
	} else {
		xajax.events.addHandler = function(command) {
			command.fullName = 'addHandler';
			var element = command.id;
			var sEvent = command.prop;
			var fun = command.data;
			if ('string' == typeof element)
				element = xajax.$(element);
			sEvent = xajax.tools.addOnPrefix(sEvent);
			eval('element.attachEvent("' + sEvent + '", ' + fun + ', false);');
			return true;
		}
	}
	return xajax.events.addHandler(element, sEvent, fun);
}

/*
	Function: removeHandler
	
	Remove an event handler from an element.
	
	element - (string or object):  The name of, or the element itself which
		will have the event handler removed.
	event - (string):  The name of the event for which this handler is 
		associated.
	fun - The function to be removed.
	
	Returns:
	
	true - The operation completed successfully.
*/
xajax.events.removeHandler = function(element, sEvent, fun) {
	if (window.removeEventListener) {
		xajax.events.removeHandler = function(command) {
			command.fullName = 'removeHandler';
			var element = command.id;
			var sEvent = command.prop;
			var fun = command.data;
			if ('string' == typeof element)
				element = xajax.$(element);
			sEvent = xajax.tools.stripOnPrefix(sEvent);
			eval('element.removeEventListener("' + sEvent + '", ' + fun + ', false);');
			return true;
		}
	} else {
		xajax.events.removeHandler = function(command) {
			command.fullName = 'removeHandler';
			var element = command.id;
			var sEvent = command.prop;
			var fun = command.data;
			if ('string' == typeof element)
				element = xajax.$(element);
			sEvent = xajax.tools.addOnPrefix(sEvent);
			eval('element.detachEvent("' + sEvent + '", ' + fun + ', false);');
			return true;
		}
	}
	return xajax.events.removeHandler(element, sEvent, fun);
}

/*
	Class: xajax.callback
*/
xajax.callback = {}

/*
	Function: create
	
	Create a blank callback object.  Two optional arguments let you 
	set the delay time for the onResponseDelay and onExpiration events.
	
	Returns:
	
	object - The callback object.
*/
xajax.callback.create = function() {
	var xx = xajax;
	var xc = xx.config;
	var xcb = xx.callback;
	
	var oCB = {}
	oCB.timers = {};
	
	oCB.timers.onResponseDelay = xcb.setupTimer(
		(arguments.length > 0) 
			? arguments[0] 
			: xc.defaultResponseDelayTime);
	
	oCB.timers.onExpiration = xcb.setupTimer(
		(arguments.length > 1) 
			? arguments[1] 
			: xc.defaultExpirationTime);

	oCB.onRequest = null;
	oCB.onResponseDelay = null;
	oCB.onExpiration = null;
	oCB.beforeResponseProcessing = null;
	oCB.onFailure = null;
	oCB.onRedirect = null;
	oCB.onSuccess = null;
	oCB.onComplete = null;
	
	return oCB;
}

/*
	Function: setupTimer
	
	Create a timer to fire an event in the future.  This will
	be used fire the onRequestDelay and onExpiration events.
	
	iDelay - (integer):  The amount of time in milliseconds to delay.
	
	Returns:
	
	object - A callback timer object.
*/
xajax.callback.setupTimer = function(iDelay)
{
	return { timer: null, delay: iDelay };
}

/*
	Function: clearTimer
	
	Clear a callback timer for the specified function.
	
	oCallback - (object):  The callback object (or objects) that
		contain the specified function timer to be cleared.
	sFunction - (string):  The name of the function associated
		with the timer to be cleared.
*/
xajax.callback.clearTimer = function(oCallback, sFunction)
{
	if ('undefined' != typeof oCallback.timers) {
		if ('undefined' != typeof oCallback.timers[sFunction]) {
			clearTimeout(oCallback.timers[sFunction].timer);
		}
	} else if ('object' == typeof oCallback) {
		var iLen = oCallback.length;
		for (var i = 0; i < iLen; ++i)
			xajax.callback.clearTimer(oCallback[i], sFunction);
	}
}

/*
	Function: execute
	
	Execute a callback event.
	
	oCallback - (object):  The callback object (or objects) which 
		contain the event handlers to be executed.
	sFunction - (string):  The name of the event to be triggered.
	args - (object):  The request object for this request.
*/
xajax.callback.execute = function(oCallback, sFunction, args) {
	if ('undefined' != typeof oCallback[sFunction]) {
		var func = oCallback[sFunction];
		if ('function' == typeof func) {
			if ('undefined' != typeof oCallback.timers[sFunction]) {
				oCallback.timers[sFunction].timer = setTimeout(function() { 
					func(args);
				}, oCallback.timers[sFunction].delay);
			}
			else {
				func(args);
			}
		}
	} else if ('object' == typeof oCallback) {
		var iLen = oCallback.length;
		for (var i = 0; i < iLen; ++i)
			xajax.callback.execute(oCallback[i], sFunction, args);
	}
}

/*
	Class: xajax.callback.global
	
	The global callback object which is active for every request.
*/
xajax.callback.global = xajax.callback.create();

/*
	Class: xajax
*/

/*
	Object: response
	
	The response queue that holds response commands, once received
	from the server, until they are processed.
*/	
xajax.response = xajax.tools.queue.create(1000);

/*
	Object: responseSuccessCodes
	
	This array contains a list of codes which will be returned from the 
	server upon successful completion of the server portion of the 
	request.
	
	These values should match those specified in the HTTP standard.
*/
xajax.responseSuccessCodes = ['0', '200'];

// 10.4.1 400 Bad Request
// 10.4.2 401 Unauthorized
// 10.4.3 402 Payment Required
// 10.4.4 403 Forbidden
// 10.4.5 404 Not Found
// 10.4.6 405 Method Not Allowed
// 10.4.7 406 Not Acceptable
// 10.4.8 407 Proxy Authentication Required
// 10.4.9 408 Request Timeout
// 10.4.10 409 Conflict
// 10.4.11 410 Gone
// 10.4.12 411 Length Required
// 10.4.13 412 Precondition Failed
// 10.4.14 413 Request Entity Too Large
// 10.4.15 414 Request-URI Too Long
// 10.4.16 415 Unsupported Media Type
// 10.4.17 416 Requested Range Not Satisfiable
// 10.4.18 417 Expectation Failed
// 10.5 Server Error 5xx
// 10.5.1 500 Internal Server Error
// 10.5.2 501 Not Implemented
// 10.5.3 502 Bad Gateway
// 10.5.4 503 Service Unavailable
// 10.5.5 504 Gateway Timeout
// 10.5.6 505 HTTP Version Not Supported

/*
	Object: responseErrorsForAlert
	
	This array contains a list of status codes returned by
	the server to indicate that the request failed for some
	reason.
*/
xajax.responseErrorsForAlert = ['400','401','402','403','404','500','501','502','503'];

// 10.3.1 300 Multiple Choices
// 10.3.2 301 Moved Permanently
// 10.3.3 302 Found
// 10.3.4 303 See Other
// 10.3.5 304 Not Modified
// 10.3.6 305 Use Proxy
// 10.3.7 306 (Unused)
// 10.3.8 307 Temporary Redirect

/*
	Object: responseRedirectCodes
	
	An array of status codes returned from the server to
	indicate a request for redirect to another URL.
	
	Typically, this is used by the server to send the browser
	to another URL.  This does not typically indicate that
	the xajax request should be sent to another URL.
*/
xajax.responseRedirectCodes = ['301','302','307'];

/*
	Class: xajax.command
	
	The object that manages commands and command handlers.
*/
if ('undefined' == typeof xajax.command)
	xajax.command = {};

/*
	Function: create
	
	Creates a new command (object) that will be populated with
	command parameters and eventually passed to the command handler.
*/
xajax.command.create = function(sequence, request, context) {
	var newCmd = {};
	newCmd.cmd = '*';
	newCmd.fullName = '* unknown command name *';
	newCmd.sequence = sequence;
	newCmd.request = request;
	newCmd.context = context;
	return newCmd;
}

/*
	Class: xajax.command.handler
	
	The object that manages command handlers.
*/
if ('undefined' == typeof xajax.command.handler)
	xajax.command.handler = {};

/*
	Object: handlers
	
	An array that is used internally in the xajax.command.handler object
	to keep track of command handlers that have been registered.
*/
if ('undefined' == typeof xajax.command.handler.handlers)
	xajax.command.handler.handlers = [];

/*
	Function: register
	
	Registers a new command handler.
*/
xajax.command.handler.register = function(shortName, func) {
	xajax.command.handler.handlers[shortName] = func;
}

/*
	Function: unregister
	
	Unregisters and returns a command handler.
*/
xajax.command.handler.unregister = function(shortName) {
	var func = xajax.command.handler.handlers[shortName];
	delete xajax.command.handler.handlers[shortName];
	return func;
}

/*
	Function: isRegistered
	
	Returns true or false depending on whether a command handler has 
	been created for the specified command (object).
*/
xajax.command.handler.isRegistered = function(command) {
	var shortName = command.cmd;
	if (xajax.command.handler.handlers[shortName])
		return true;
	return false;
}

/*
	Function: call
	
	Calls the registered command handler for the specified command
	(you should always check isRegistered before calling this function)
*/
xajax.command.handler.call = function(command) {
	var shortName = command.cmd;
	return xajax.command.handler.handlers[shortName](command);
}

xajax.command.handler.register('rcmplt', function(args) {
	xajax.completeResponse(args.request);
	return true;
});

xajax.command.handler.register('css', function(args) {
	args.fullName = 'includeCSS';
	return xajax.css.add(args.data);
});
xajax.command.handler.register('rcss', function(args) {
	args.fullName = 'removeCSS';
	return xajax.css.remove(args.data);
});
xajax.command.handler.register('wcss', function(args) {
	args.fullName = 'waitForCSS';
	return xajax.css.waitForCSS(args);
});

xajax.command.handler.register('as', function(args) {
	args.fullName = 'assign/clear';
	try {
		return xajax.dom.assign(args.target, args.prop, args.data);
	} catch (e) {
		// do nothing, if the debug module is installed it will
		// catch and handle the exception
	}
	return true;
});
xajax.command.handler.register('ap', function(args) {
	args.fullName = 'append';
	return xajax.dom.append(args.target, args.prop, args.data);
});
xajax.command.handler.register('pp', function(args) {
	args.fullName = 'prepend';
	return xajax.dom.prepend(args.target, args.prop, args.data);
});
xajax.command.handler.register('rp', function(args) {
	args.fullName = 'replace';
	return xajax.dom.replace(args.id, args.prop, args.data);
});
xajax.command.handler.register('rm', function(args) {
	args.fullName = 'remove';
	return xajax.dom.remove(args.id);
});
xajax.command.handler.register('ce', function(args) {
	args.fullName = 'create';
	return xajax.dom.create(args.id, args.data, args.prop);
});
xajax.command.handler.register('ie', function(args) {
	args.fullName = 'insert';
	return xajax.dom.insert(args.id, args.data, args.prop);
});
xajax.command.handler.register('ia', function(args) {
	args.fullName = 'insertAfter';
	return xajax.dom.insertAfter(args.id, args.data, args.prop);
});

xajax.command.handler.register('c:as', xajax.dom.contextAssign);
xajax.command.handler.register('c:ap', xajax.dom.contextAppend);
xajax.command.handler.register('c:pp', xajax.dom.contextPrepend);

xajax.command.handler.register('s', xajax.js.sleep);
xajax.command.handler.register('ino', xajax.js.includeScriptOnce);
xajax.command.handler.register('in', xajax.js.includeScript);
xajax.command.handler.register('rjs', xajax.js.removeScript);
xajax.command.handler.register('wf', xajax.js.waitFor);
xajax.command.handler.register('js', xajax.js.execute);
xajax.command.handler.register('jc', xajax.js.call);
xajax.command.handler.register('sf', xajax.js.setFunction);
xajax.command.handler.register('wpf', xajax.js.wrapFunction);
xajax.command.handler.register('al', function(args) {
	args.fullName = 'alert';
	alert(args.data);
	return true;
});
xajax.command.handler.register('cc', xajax.js.confirmCommands);

xajax.command.handler.register('ci', xajax.forms.createInput);
xajax.command.handler.register('ii', xajax.forms.insertInput);
xajax.command.handler.register('iia', xajax.forms.insertInputAfter);

xajax.command.handler.register('ev', xajax.events.setEvent);

xajax.command.handler.register('ah', xajax.events.addHandler);
xajax.command.handler.register('rh', xajax.events.removeHandler);

xajax.command.handler.register('dbg', function(args) {
	args.fullName = 'debug message';
	return true;
});

/*
	Function: initializeRequest
	
	Initialize a request object, populating default settings, where
	call specific settings are not already provided.
	
	oRequest - (object):  An object that specifies call specific settings
		that will, in addition, be used to store all request related
		values.  This includes temporary values used internally by xajax.
*/
xajax.initializeRequest = function(oRequest) {
	var xx = xajax;
	var xc = xx.config;
	
	oRequest.append = function(opt, def) {
		if ('undefined' != typeof this[opt]) {
			for (var itmName in def)
				if ('undefined' == typeof this[opt][itmName])
					this[opt][itmName] = def[itmName];
		} else this[opt] = def;
	}
	
	oRequest.append('commonHeaders', xc.commonHeaders);
	oRequest.append('postHeaders', xc.postHeaders);
	oRequest.append('getHeaders', xc.getHeaders);

	oRequest.set = function(option, defaultValue) {
		if ('undefined' == typeof this[option])
			this[option] = defaultValue;
	}
	
	oRequest.set('statusMessages', xc.statusMessages);
	oRequest.set('waitCursor', xc.waitCursor);
	oRequest.set('mode', xc.defaultMode);
	oRequest.set('method', xc.defaultMethod);
	oRequest.set('URI', xc.requestURI);
	oRequest.set('httpVersion', xc.defaultHttpVersion);
	oRequest.set('contentType', xc.defaultContentType);
	oRequest.set('retry', xc.defaultRetry);
	oRequest.set('returnValue', xc.defaultReturnValue);
	oRequest.set('maxObjectDepth', xc.maxObjectDepth);
	oRequest.set('maxObjectSize', xc.maxObjectSize);
	oRequest.set('context', window);
	
	var xcb = xx.callback;
	var gcb = xcb.global;
	var lcb = xcb.create();
	
	lcb.take = function(frm, opt) {
		if ('undefined' != typeof frm[opt]) {
			lcb[opt] = frm[opt];
			lcb.hasEvents = true;
		}
		delete frm[opt];
	}
	
	lcb.take(oRequest, 'onRequest');
	lcb.take(oRequest, 'onResponseDelay');
	lcb.take(oRequest, 'onExpiration');
	lcb.take(oRequest, 'beforeResponseProcessing');
	lcb.take(oRequest, 'onFailure');
	lcb.take(oRequest, 'onRedirect');
	lcb.take(oRequest, 'onSuccess');
	lcb.take(oRequest, 'onComplete');
	
	if ('undefined' != typeof oRequest.callback) {
		if (lcb.hasEvents)
			oRequest.callback = [oRequest.callback, lcb];
	} else
		oRequest.callback = lcb;
	
	oRequest.status = (oRequest.statusMessages) 
		? xc.status.update() 
		: xc.status.dontUpdate();
	
	oRequest.cursor = (oRequest.waitCursor) 
		? xc.cursor.update() 
		: xc.cursor.dontUpdate();
	
	oRequest.method = oRequest.method.toUpperCase();
	if ('GET' != oRequest.method)
		oRequest.method = 'POST';	// W3C: Method is case sensitive
	
	oRequest.requestRetry = oRequest.retry;
	
	oRequest.append('postHeaders', {
		'content-type': oRequest.contentType
		});
		
	delete oRequest['append'];
	delete oRequest['set'];
	delete oRequest['take'];

	if ('undefined' == typeof oRequest.URI)
		throw { code: 10005 }
}

/*
	Function: processParameters
	
	Processes request specific parameters and generates the temporary 
	variables needed by xajax to initiate and process the request.
	
	oRequest - A request object, created initially by a call to
		<xajax.initializeRequest>
		
	This is called once per request; upon a request failure, this 
	will not be called for additional retries.
*/
xajax.processParameters = function(oRequest) {
	var xx = xajax;
	var xt = xx.tools;
	
	var rd = [];
	
	var separator = '';
	for (var sCommand in oRequest.functionName) {
		if ('constructor' != sCommand) {
			rd.push(separator);
			rd.push(sCommand);
			rd.push('=');
			rd.push(encodeURIComponent(oRequest.functionName[sCommand]));
			separator = '&';
		}
	}
	var dNow = new Date();
	rd.push('&xjxr=');
	rd.push(dNow.getTime());
	delete dNow;

	if (oRequest.parameters) {
		var i = 0;
		var iLen = oRequest.parameters.length;
		while (i < iLen) {
			var oVal = oRequest.parameters[i];
			if ('object' == typeof oVal && null != oVal) {
				try {
					var oGuard = {};
					oGuard.depth = 0;
					oGuard.maxDepth = oRequest.maxObjectDepth;
					oGuard.size = 0;
					oGuard.maxSize = oRequest.maxObjectSize;
					oVal = xt._objectToXML(oVal, oGuard);
				} catch (e) {
					oVal = '';
					// do nothing, if the debug module is installed
					// it will catch the exception and handle it
				}
				rd.push('&xjxargs[]=');
				oVal = encodeURIComponent(oVal);
				rd.push(oVal);
				++i;
			} else {
				rd.push('&xjxargs[]=');
				oVal = xt._escape(oVal);
				if ('undefined' == typeof oVal || null == oVal) {
					rd.push('*');
				} else {
					var sType = typeof oVal;
					if ('string' == sType)
						rd.push('S');
					else if ('boolean' == sType)
						rd.push('B');
					else if ('number' == sType)
						rd.push('N');
					oVal = encodeURIComponent(oVal);
					rd.push(oVal);
				}
				++i;
			}
		}
	}
	
	oRequest.requestURI = oRequest.URI;
	
	if ('GET' == oRequest.method) {
		oRequest.requestURI += oRequest.requestURI.indexOf('?')== -1 ? '?' : '&';
		oRequest.requestURI += rd.join('');
		rd = [];
	}
	
	oRequest.requestData = rd.join('');
}

/*
	Function: prepareRequest
	
	Prepares the XMLHttpRequest object for this xajax request.
	
	oRequest - (object):  An object created by a call to <xajax.initializeRequest>
		which already contains the necessary parameters and temporary variables
		needed to initiate and process a xajax request.
		
	This is called each time a request object is being prepared for a 
	call to the server.  If the request is retried, the request must be
	prepared again.
*/
xajax.prepareRequest = function(oRequest) {
	var xx = xajax;
	var xt = xx.tools;
	
	oRequest.request = xt.getRequestObject();
	
	oRequest.setRequestHeaders = function(headers) {
	 	if ('object' == typeof headers) {
			for (var optionName in headers)
				this.request.setRequestHeader(optionName, headers[optionName]);
		}
	}
	oRequest.setCommonRequestHeaders = function() {
		this.setRequestHeaders(this.commonHeaders);
	}
	oRequest.setPostRequestHeaders = function() {
		this.setRequestHeaders(this.postHeaders);
	}
	oRequest.setGetRequestHeaders = function() {
		this.setRequestHeaders(this.getHeaders);
	}
	
	if ('asynchronous' == oRequest.mode) {
		// references inside this function should be expanded
		// IOW, don't use shorthand references like xx for xajax
		oRequest.request.onreadystatechange = function() {
			if (oRequest.request.readyState != 4)
				return;
			xajax.responseReceived(oRequest);
		}
		oRequest.finishRequest = function() {
			return this.returnValue;
		}
	} else {
		oRequest.finishRequest = function() {
			return xajax.responseReceived(oRequest);
		}
	}
	
	if ('undefined' != typeof oRequest.userName && 'undefined' != typeof oRequest.password) {
		oRequest.open = function() {
			this.request.open(
				this.method, 
				this.requestURI, 
				'asynchronous' == this.mode, 
				oRequest.userName, 
				oRequest.password);
		}
	} else {
		oRequest.open = function() {
			this.request.open(
				this.method, 
				this.requestURI, 
				'asynchronous' == this.mode);
		}
	}
	
	if ('POST' == oRequest.method) {	// W3C: Method is case sensitive
		oRequest.applyRequestHeaders = function() {
			this.setCommonRequestHeaders();
			try {
				this.setPostRequestHeaders();
			} catch (e) {
				this.method = 'GET';
				this.requestURI += this.requestURI.indexOf('?')== -1 ? '?' : '&';
				this.requestURI += this.requestData;
				this.requestData = '';
				if (0 == this.requestRetry) this.requestRetry = 1;
				throw e;
			}
		}
	} else {
		oRequest.applyRequestHeaders = function() {
			this.setCommonRequestHeaders();
			this.setGetRequestHeaders();
		}
	}
}

/*
	Function: request
*/
xajax.request = function() {
	var numArgs = arguments.length;
	if (0 == numArgs)
		return false;
	
	var oRequest = {}
	if (1 < numArgs)
		oRequest = arguments[1];
	
	oRequest.functionName = arguments[0];
	
	var xx = xajax;
	
	xx.initializeRequest(oRequest);
	xx.processParameters(oRequest);
	while (0 < oRequest.requestRetry) {
		try {
			--oRequest.requestRetry;
			xx.prepareRequest(oRequest);
			return xx.submitRequest(oRequest);
		} catch (e) {
			xajax.callback.execute(
				[xajax.callback.global, oRequest.callback], 
				'onFailure', oRequest);
			if (0 == oRequest.requestRetry)
				throw e;
		}
	}
}

/*
	Function: call
	
	Initiates a call to the server.
	
	sFunctionName - (string):  The name of the function to execute
		on the server.
		
	oRequestOptions - (object, optional):  A request object which 
		may contain call specific parameters.  This object will be
		used by xajax to store all the request parameters as well
		as temporary variables needed during the processing of the
		request.
		
	Returns:
	
	unknown - For asynchronous calls, the return value will always
		be the value set for <xajax.config.defaultReturnValue>
*/
xajax.call = function() {
	var numArgs = arguments.length;
	if (0 == numArgs)
		return false;
	
	var oRequest = {}
	if (1 < numArgs)
		oRequest = arguments[1];
	
	oRequest.functionName = { xjxfun: arguments[0] };
	
	var xx = xajax;
	
	xx.initializeRequest(oRequest);
	xx.processParameters(oRequest);
	
	while (0 < oRequest.requestRetry) {
		try {
			--oRequest.requestRetry;
			xx.prepareRequest(oRequest);
			return xx.submitRequest(oRequest);
		} catch (e) {
			xajax.callback.execute(
				[xajax.callback.global, oRequest.callback], 
				'onFailure', oRequest);
			if (0 == oRequest.requestRetry)
				throw e;
		}
	}
}

/*
	Function: submitRequest
	
	Create a request object and submit the request using the specified
	request type; all request parameters should be finalized by this 
	point.  Upon failure of a POST, this function will fall back to a 
	GET request.
	
	oRequest - (object):  The request context object.
*/
xajax.submitRequest = function(oRequest) {
	oRequest.status.onRequest();
	
	var xcb = xajax.callback;
	var gcb = xcb.global;
	var lcb = oRequest.callback;
	
	xcb.execute([gcb, lcb], 'onResponseDelay', oRequest);
	xcb.execute([gcb, lcb], 'onExpiration', oRequest);
	xcb.execute([gcb, lcb], 'onRequest', oRequest);
	
	oRequest.open();
	oRequest.applyRequestHeaders();

	oRequest.cursor.onWaiting();
	oRequest.status.onWaiting();
	
	xajax._internalSend(oRequest);
	
	// synchronous mode causes response to be processed immediately here
	return oRequest.finishRequest();
}

/*
	Function: _internalSend
	
	This function is used internally by xajax to initiate a request to the
	server.
	
	oRequest - (object):  The request context object.
*/
xajax._internalSend = function(oRequest) {
	// this may block if synchronous mode is selected
	oRequest.request.send(oRequest.requestData);
}

/*
	Function: abortRequest
	
	Abort the request.
	
	oRequest - (object):  The request context object.
*/
xajax.abortRequest = function(oRequest)
{
	oRequest.aborted = true;
	oRequest.request.abort();
	xajax.completeResponse(oRequest);
}

/*
	Function: responseReceived
	
	Process the response.
	
	oRequest - (object):  The request context object.
*/
xajax.responseReceived = function(oRequest) {
	var xx = xajax;
	var xcb = xx.callback;
	var gcb = xcb.global;
	var lcb = oRequest.callback;
	
	// sometimes the responseReceived gets called when the
	// request is aborted
	if (oRequest.aborted)
		return;
	
	xcb.clearTimer([gcb, lcb], 'onExpiration');
	xcb.clearTimer([gcb, lcb], 'onResponseDelay');
	
	xcb.execute([gcb, lcb], 'beforeResponseProcessing', oRequest);
	
	var fProc = xx.getResponseProcessor(oRequest);
	if ('undefined' == typeof fProc) {
		xcb.execute([gcb, lcb], 'onFailure', oRequest);
		xx.completeResponse(oRequest);
		return;
	}
	
	return fProc(oRequest);
}

/*
	Function: getResponseProcessor
	
	This function attempts to determine, based on the content type of the
	reponse, what processor should be used for handling the response data.
	
	The default xajax response will be text/xml which will invoke the
	xajax xml response processor.  Other response processors may be added
	in the future.  The user can specify their own response processor on
	a call by call basis.
	
	oRequest - (object):  The request context object.
*/
xajax.getResponseProcessor = function(oRequest) {
	var fProc;
	
	if ('undefined' == typeof oRequest.responseProcessor) {
		var cTyp = oRequest.request.getResponseHeader('content-type');
		if (cTyp) {
			if (0 <= cTyp.indexOf('text/xml')) {
				fProc = xajax.responseProcessor.xml;
	//		} else if (0 <= cTyp.indexOf('application/json')) {
	//			fProc = xajax.responseProcessor.json;
			}
		}
	} else fProc = oRequest.responseProcessor;
	
	return fProc;
}

/*
	Function: executeCommand
	
	Perform a lookup on the command specified by the response command
	object passed in the first parameter.  If the command exists, the
	function checks to see if the command references a DOM object by
	ID; if so, the object is located within the DOM and added to the 
	command data.  The command handler is then called.
	
	If the command handler returns true, it is assumed that the command 
	completed successfully.  If the command handler returns false, then the
	command is considered pending; xajax enters a wait state.  It is up
	to the command handler to set an interval, timeout or event handler
	which will restart the xajax response processing.
	
	obj - (object):  The response command to be executed.
	
	Returns:
	
	true - The command completed successfully.
	false - The command signalled that it needs to pause processing.
*/
xajax.executeCommand = function(command) {
	if (xajax.command.handler.isRegistered(command)) {
		// it is important to grab the element here as the previous command
		// might have just created the element
		if (command.id)
			command.target = xajax.$(command.id);
		// process the command
		if (false == xajax.command.handler.call(command)) {
			xajax.tools.queue.pushFront(xajax.response, command);
			return false;
		}
	}
	return true;
}

/*
	Function: completeResponse
	
	Called by the response command queue processor when all commands have 
	been processed.
	
	oRequest - (object):  The request context object.
*/
xajax.completeResponse = function(oRequest) {
	xajax.callback.execute(
		[xajax.callback.global, oRequest.callback], 
		'onComplete', oRequest);
	oRequest.cursor.onComplete();
	oRequest.status.onComplete();
	// clean up -- these items are restored when the request is initiated
	delete oRequest['functionName'];
	delete oRequest['requestURI'];
	delete oRequest['requestData'];
	delete oRequest['requestRetry'];
	delete oRequest['request'];
	delete oRequest['set'];
	delete oRequest['open'];
	delete oRequest['setRequestHeaders'];
	delete oRequest['setCommonRequestHeaders'];
	delete oRequest['setPostRequestHeaders'];
	delete oRequest['setGetRequestHeaders'];
	delete oRequest['applyRequestHeaders'];
	delete oRequest['finishRequest'];
	delete oRequest['status'];	
	delete oRequest['cursor'];	
}

/*
	Function: $
	
	Shortcut to <xajax.tools.$>.
*/
xajax.$ = xajax.tools.$;

/*
	Function: getFormValues
	
	Shortcut to <xajax.tools.getFormValues>.
*/
xajax.getFormValues = xajax.tools.getFormValues;

/*
	Boolean: isLoaded
	
	true - xajax module is loaded.
*/
xajax.isLoaded = true;


/*
	Class: xjx
	
	Contains shortcut's to frequently used functions.
*/
xjx = {}

/*
	Function: $
	
	Shortcut to <xajax.tools.$>.
*/
xjx.$ = xajax.tools.$;

/*
	Function: getFormValues
	
	Shortcut to <xajax.tools.getFormValues>.
*/
xjx.getFormValues = xajax.tools.getFormValues;

/*
	Function: call
	
	Shortcut to <xajax.call>.
*/
xjx.call = xajax.call;

xjx.request = xajax.request;
