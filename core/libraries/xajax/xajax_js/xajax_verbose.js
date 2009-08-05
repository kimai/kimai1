/*
	File: xajax_verbose.js
	
	The xajax verbose debugging module.  This is an optional module, include in
	your project with care. :)
	
	Title: xajax verbose debugging module
	
	Please see <copyright.inc.php> for a detailed description, copyright
	and license information.
*/

/*
	@package xajax
	@version $Id: xajax_verbose_uncompressed 327 2007-02-28 16:55:26Z calltoconstruct $
	@copyright Copyright (c) 2005-2006 by Jared White & J. Max Wilson
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

try {
	if (undefined == xajax)
		throw { name: 'SequenceError', message: 'Error: xajax was not detected, verbose module disabled.' }
	if (undefined == xajax.debug)
		throw { name: 'SequenceError', message: 'Error: xajax debugger was not detected, verbose module disabled.' }
	
	/*
		Class: xajax.debug.verbose
		
		Provide a high level of detail which can be used to debug hard to find
		problems.
	*/
	xajax.debug.verbose = {}

	/*
		Function: expandObject
		
		Generate a debug message expanding all the first level
		members found therein.
		
		obj - (object):  The object to be enumerated.
		
		Returns:
		
		string - The textual representation of all the first
			level members.
	*/	
	xajax.debug.verbose.expandObject = function(obj) {
		var rec = true;
		if (1 < arguments.length)
			rec = arguments[1];
		if ('function' == typeof (obj)) {
			return '[Function]';
		} else if ('object' == typeof (obj)) {
			if (true == rec) {
				var t = ' { ';
				var separator = '';
				for (var m in obj) {
					t += separator;
					t += m;
					t += ': ';
					try {
						t += xajax.debug.verbose.expandObject(obj[m], false);
					} catch (e) {
						t += '[n/a]';
					}
					separator = ', ';
				}
				t += ' } ';
				return t;
			} else return '[Object]';
		} else return '"' + obj + '"';
	}
	
	/*
		Function: makeFunction
		
		Generate a wrapper function around the specified function.
		
		obj - (object):  The object that contains the function to be
			wrapped.
		name - (string):  The name of the function to be wrapped.
		
		Returns:
		
		function - The wrapper function.
	*/		
	xajax.debug.verbose.makeFunction = function(obj, name) {
		return function() {
			var fun = name;
			fun += '(';

			var separator = '';
			var pLen = arguments.length;
			for (var p = 0; p < pLen; ++p) {
				fun += separator;
				fun += xajax.debug.verbose.expandObject(arguments[p]);
				separator = ',';
			}
			
			fun += ');';
			
			var msg = '--> ';
			msg += fun;

			xajax.debug.writeMessage(msg);

			var returnValue = true;
			var code = 'returnValue = obj(';
			separator = '';
			for (var p = 0; p < pLen; ++p) {
				code += separator;
				code += 'arguments[' + p + ']';
				separator = ',';
			}
			code += ');';

			eval(code);
			
			msg = '<-- ';
			msg += fun;
			msg += ' returns ';
			msg += xajax.debug.verbose.expandObject(returnValue);
			
			xajax.debug.writeMessage(msg);
			
			return returnValue;
		}
	}
	
	/*
		Function: hook
		
		Generate a wrapper function around each of the functions
		contained within the specified object.
		
		x - (object):  The object to be scanned.
		base - (string):  The base reference to be prepended to the
			generated wrapper functions.
	*/
	xajax.debug.verbose.hook = function(x, base) {
		for (var m in x) {
			if ('function' == typeof (x[m])) {
				x[m] = xajax.debug.verbose.makeFunction(x[m], base + m);
			}
		}
	}
	
	xajax.debug.verbose.hook(xajax, 'xajax.');
	xajax.debug.verbose.hook(xajax.callback, 'xajax.callback.');
	xajax.debug.verbose.hook(xajax.css, 'xajax.css.');
	xajax.debug.verbose.hook(xajax.dom, 'xajax.dom.');
	xajax.debug.verbose.hook(xajax.events, 'xajax.events.');
	xajax.debug.verbose.hook(xajax.forms, 'xajax.forms.');
	xajax.debug.verbose.hook(xajax.js, 'xajax.js.');
	xajax.debug.verbose.hook(xajax.tools, 'xajax.tools.');
	xajax.debug.verbose.hook(xajax.tools.queue, 'xajax.tools.queue.');
	xajax.debug.verbose.hook(xajax.command, 'xajax.command.');
	xajax.debug.verbose.hook(xajax.command.handler, 'xajax.command.handler.');
	
	/*
		Boolean: isLoaded
		
		true - indicates that the verbose debugging module is loaded.
	*/
	xajax.debug.verbose.isLoaded = true;
} catch (e) {
	alert(e.name + ': ' + e.message);
}
