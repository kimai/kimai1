/*
	File: comet.js
	
	Title: Comet plugin for xajax
	
*/

/*
	@package comet plugin
	@version $Id: 
	@copyright Copyright (c) 2007 by Steffen Konerow (IE)
	@license http://www.xajaxproject.org/bsd_license.txt BSD License
*/

/*
	Class: xajax.ext.comet
	
	This class contains all functions for using comet streaming with xajax.
	
*/

	try {
		if (undefined == xajax.ext)
			xajax.ext = {};
	} catch (e) {
	}

	try {
		if (undefined == xajax.ext.comet)
			xajax.ext.comet = {};
	} catch (e) {
		alert("Could not create xajax.ext.comet namespace");
	}

	// create Shorthand for xajax.ext.comet
	xjxEc = xajax.ext.comet;
	
// -------------------------------------------------------------------------------------------------------------------------------------
/*
  Function: detectSupport

	Detects browser for using fallback methods instead of multipart XHR responses.
*/	
	xjxEc.detectSupport = function() 
	{
	
	
		var agt=navigator.userAgent.toLowerCase();
		if (agt.indexOf("opera") != -1) return 'Opera';
		if (agt.indexOf("staroffice") != -1) return 'Star Office';
		if (agt.indexOf("webtv") != -1) return 'WebTV';
		if (agt.indexOf("beonex") != -1) return 'Beonex';
		if (agt.indexOf("chimera") != -1) return 'Chimera';
		if (agt.indexOf("netpositive") != -1) return 'NetPositive';
		if (agt.indexOf("phoenix") != -1) return 'Phoenix';
		if (agt.indexOf("firefox") != -1) return 'Firefox';
		if (agt.indexOf("safari") != -1) return 'Safari';
		if (agt.indexOf("skipstone") != -1) return 'SkipStone';
		if (agt.indexOf("msie") != -1) return 'Internet Explorer';
		if (agt.indexOf("netscape") != -1) return 'Netscape';
		if (agt.indexOf("mozilla/5.0") != -1) return 'Mozilla';
		if (agt.indexOf('\/') != -1) 
		{
			if (agt.substr(0,agt.indexOf('\/')) != 'mozilla') 
			{
				return navigator.userAgent.substr(0,agt.indexOf('\/'));
			}
			else return 'Netscape';
		} 
		else if (agt.indexOf(' ') != -1) return navigator.userAgent.substr(0,agt.indexOf(' '));
		else return navigator.userAgent;
	
//		if (navigator.appVersion.indexOf("MSIE")!=-1)
//		{
//			var version,temp;
//			temp=navigator.appVersion.split("MSIE")
//			version=parseFloat(temp[1])
//			if (version>=5.5) return "MSIE";
//		}
//		if ( "undefined" != typeof window.opera ) 
//		{
//			return "OPERA";
//		}
//		if ( "undefined" != typeof window.Iterator ) 
//		{
//			return "FF2";
//		}
		
		return false;
	}
	

// -------------------------------------------------------------------------------------------------------------------------------------

/*
	Function: prepareRequestXHR
	
	Prepares the XMLHttpRequest object for this xajax request in FF/Safari browsers.
	
*/
	
	xjxEc.prepareRequestXHR = function (oRequest) 
	{
		if (true == oRequest.comet) 
		{
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


		oRequest.applyRequestHeaders = function() {
		}

			oRequest.setCommonRequestHeaders = function() {
				this.request.setRequestHeader('If-Modified-Since', 'Sat, 1 Jan 2000 00:00:00 GMT');
				this.request.setRequestHeader('streaming', 'xhr');

			 	if (typeof(oRequest.header) == "object") 
			 	{
			 	  for (a in oRequest.header)
			 			this.request.setRequestHeader(a, oRequest.header[a]);
				}
			}
			oRequest.comet = {};
			oRequest.comet.LastPosition = 0;
			
			var pollLatestResponse = function() {
				xjxEc.responseProcessor.XHR(oRequest);
			}
			oRequest.pollTimer = setInterval(pollLatestResponse, 80);
			oRequest.request.onreadystatechange = function() 
			{
				if (oRequest.request.readyState < 3)
					return;

				if (oRequest.request.readyState == 4) 
				{
					clearInterval(oRequest.pollTimer);
					xjxEc.responseProcessor.XHR(oRequest);

					//xajax.responseReceived(oRequest);
					xajax.completeResponse(oRequest);
					return;
				} 
			}
			oRequest.finishRequest = function() 
			{
				return this.returnValue;
			}
		
			if ('undefined' != typeof oRequest.userName && 'undefined' != typeof oRequest.password) 
			{
				oRequest.open = function() 
				{
					this.request.open(
						this.method, 
						this.requestURI, 
						true, 
						oRequest.userName, 
						oRequest.password);
				}
			} else 
			{
				oRequest.open = function() 
				{
					this.request.open(
						this.method, 
						this.requestURI, 
						true);
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
			return;
		}
		return xjxEc.prepareRequest(oRequest);
	}
	
// -------------------------------------------------------------------------------------------------------------------------------------
/*
  Function: connect_htmlfile

	Create a hidden iframe for IE

*/
	xjxEc.connect_htmlfile = function (url, callback,oRequest) 
	{

    try {
	    xjxEc.transferDoc = new ActiveXObject("htmlfile");
			xjxEc.transferDoc.open();
			xjxEc.transferDoc.write("<html>");
			xjxEc.transferDoc.write("<script>document.domain='http://192.168.1.21/';</script>");
			xjxEc.transferDoc.write("</html>");
			xjxEc.transferDoc.close();
	    xjxEc.ifrDiv = xjxEc.transferDoc.createElement("div");
	    xjxEc.transferDoc.body.appendChild(xjxEc.ifrDiv);
	    xjxEc.ifrDiv.innerHTML = "<iframe src='" + url + "'></iframe>";
	    xjxEc.transferDoc.callback = function (response) {
    															callback(response,oRequest);
    															};
	  } catch (ex) {
	  	
	  }
}
	
// -------------------------------------------------------------------------------------------------------------------------------------

/*
	Function: prepareRequestActiveX
	
	Prepares the Iframe for streaming with active X
	
*/

  xjxEc.prepareRequestActiveX = function(oRequest) {
		if (true == oRequest.comet) {
			var xx = xajax;
			var xt = xx.tools;
			oRequest.requestURI += oRequest.requestURI.indexOf('?')== -1 ? '?' : '&';
			oRequest.requestURI += oRequest.requestData;
			oRequest.requestData = '';
			try {			
				xjxEc.connect_htmlfile(oRequest.requestURI,xjxEc.responseProcessor.ActiveX,oRequest);
				if (0 < oRequest.requestRetry) oRequest.requestRetry = 0;
		  } catch (ex) {
		  }
			return;
		}  	
  	return xjxEc.prepareRequest(oRequest);

  }

// -------------------------------------------------------------------------------------------------------------------------------------

/*
	Function: prepareRequestHTMLDRAFT
	
	Prepares streaming with HTML 5 Draft
	
*/

  xjxEc.prepareRequestHTMLDRAFT = function(oRequest) {
		if (true == oRequest.comet) {
			var xx = xajax;
			var xt = xx.tools;
			oRequest.requestURI += oRequest.requestURI.indexOf('?')== -1 ? '?' : '&';
			oRequest.requestURI += oRequest.requestData;
			oRequest.requestURI += "&xjxstreaming=HTML5DRAFT";
			try {	
					var uri = oRequest.requestURI;
					var es = document.createElement("event-source");
					es.setAttribute("src", uri);
					es.setAttribute("width", 200);
					es.setAttribute("height", 200);
					es.style.display="block";
					callback =  function(event) 
											{
												xjxEc.responseProcessor.HTMLDRAFT(event.data,oRequest);
											};
					remove = function() {
						es.removeEventListener("xjxstream",callback,false);
						es.removeEventListener("xjxendstream",remove,false);
						//document.body.removeChild(es);
					}					
					
					es.addEventListener("xjxstream",callback,false);
					es.addEventListener("xjxendstream",remove,false);
					document.body.appendChild(es);
				if (0 < oRequest.requestRetry) oRequest.requestRetry = 0;
		  } 
		  catch (ex) 
		  {
		  }
			return;
		}  	
  	return xjxEc.prepareRequest(oRequest);

  }
// -------------------------------------------------------------------------------------------------------------------------------------
/*
  Function: responseProcessor.XHR

	Processes the streaming response for FF/Safari

*/
	xajax.debug={};
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
	xjxEc.responseProcessor={}

	
	xjxEc.responseProcessor.XHR = function(oRequest) {
	var xx = xajax;
	var xt = xx.tools;
	var xcb = xx.callback;
	var gcb = xcb.global;
	var lcb = oRequest.callback;
	var oRet = oRequest.returnValue;
		if ("" == oRequest.request.responseText) return;
    var allMessages = oRequest.request.responseText;
    do {
      var unprocessed = allMessages.substring(oRequest.comet.LastPosition);
      var messageXMLEndIndex = unprocessed.indexOf("</xjx>");
      if (messageXMLEndIndex!=-1) {
        var endOfFirstMessageIndex = messageXMLEndIndex + "</xjx>".length;
        var anUpdate = unprocessed.substring(0, endOfFirstMessageIndex);
				
				var cmd = (new DOMParser()).parseFromString(anUpdate, "text/xml");
				try {
					var seq = 0;
					var child = cmd.documentElement.firstChild;
					xt.xml.processFragment(child, seq, oRequest);
				} catch (ex) {
				}        
				xt.queue.process(xx.response);        
        oRequest.comet.LastPosition += endOfFirstMessageIndex;
      }
    } while (messageXMLEndIndex != -1);
	
		return oRet;
	}
// -------------------------------------------------------------------------------------------------------------------------------------
/*
  Function: responseProcessor.ActiveX

	Processes the streaming response for IE

*/
	
	xjxEc.responseProcessor.ActiveX = function(response,oRequest) {

		response.replace('\"','"');
		var xx = xajax;
		var xt = xx.tools;
		var xcb = xx.callback;
		var gcb = xcb.global;
		var lcb = oRequest.callback;
		var oRet = oRequest.returnValue;
		if (response) {
			var cmd = (new DOMParser()).parseFromString(response, "text/xml");
			var seq=0;
			var child = cmd.documentElement.firstChild;
			xt.xml.processFragment(child, seq, oRequest);

			if (null == xx.response.timeout)
				xt.queue.process(xx.response);
		}
		return oRet;
	}

// -------------------------------------------------------------------------------------------------------------------------------------
/*
  Function: responseProcessor.HTMLDRAFT

	Processes the streaming response for HTML 5 Draft Browsers (Opera 9+)

*/
	
	xjxEc.responseProcessor.HTMLDRAFT = function(response,oRequest) {
		var xx = xajax;
		var xt = xx.tools;
		var xcb = xx.callback;
		var gcb = xcb.global;
		var lcb = oRequest.callback;
		var oRet = oRequest.returnValue;
		if (response) {
			var cmd = (new DOMParser()).parseFromString(response, "text/xml");
			var seq=0;
			var child = cmd.documentElement.firstChild;
			xt.xml.processFragment(child, seq, oRequest);

			if (null == xx.response.timeout)
				xt.queue.process(xx.response);
		
		}
		return oRet;
	}

// -------------------------------------------------------------------------------------------------------------------------------------
/*

	Function: submitRequestActiveX
	
	Supresses the xajax.submitRequest() function call for IE in streaming calls.

*/

xjxEc.submitRequestActiveX = function(oRequest) {
	if (true == oRequest.comet) return;
	xjxEc.submitRequest(oRequest);
}

// -------------------------------------------------------------------------------------------------------------------------------------
/*

	variable setup. Detects IE and replaces the according functions

*/


	xjxEc.prepareRequest = xajax.prepareRequest;

	xjxEc.stream_support = xjxEc.detectSupport();
	switch (xjxEc.stream_support) 
	{
		case "Internet Explorer" : 
									xajax.prepareRequest = xjxEc.prepareRequestActiveX;
									xjxEc.submitRequest=xajax.submitRequest;
									xajax.submitRequest=xjxEc.submitRequestActiveX;
									break;
		case "Firefox" :
		case "Safari" :
									xajax.prepareRequest = xjxEc.prepareRequestXHR;
									break;


		case "Opera" :
									xajax.prepareRequest = xjxEc.prepareRequestHTMLDRAFT;
									xjxEc.submitRequest=xajax.submitRequest;
									xajax.submitRequest=xjxEc.submitRequestActiveX;
									break;
		default : alert("Xajax.Ext.Comet: Your browser does not support comet streaming or is not yet supported by this plugin!");

	}	
	
// -------------------------------------------------------------------------------------------------------------------------------------
/*

	Function: DOMParser
	
	Prototype DomParser for IE/Opera

*/
if (typeof DOMParser == "undefined") {
   DOMParser = function () {}

   DOMParser.prototype.parseFromString = function (str, contentType) {
      if (typeof ActiveXObject != "undefined") {
         	var d = new ActiveXObject("Microsoft.XMLDOM");
         	d.loadXML(str);
         return d;
      } else if (typeof XMLHttpRequest != "undefined") {
         var req = new XMLHttpRequest;
         req.open("GET", "data:" + (contentType || "application/xml") +
                         ";charset=utf-8," + encodeURIComponent(str), false);
         if (req.overrideMimeType) {
            req.overrideMimeType(contentType);
         }
         req.send(null);
         return req.responseXML;
      }
   }
}
// -------------------------------------------------------------------------------------------------------------------------------------
