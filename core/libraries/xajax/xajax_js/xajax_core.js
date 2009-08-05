
try{if('undefined'==typeof xajax.config)xajax.config={};}catch(e){xajax={};xajax.config={};}
xajax.config.setDefault=function(option,defaultValue){if('undefined'==typeof xajax.config[option])
xajax.config[option]=defaultValue;}
xajax.config.setDefault('commonHeaders',{'If-Modified-Since':'Sat, 1 Jan 2000 00:00:00 GMT'
});xajax.config.setDefault('postHeaders',{});xajax.config.setDefault('getHeaders',{});xajax.config.setDefault('waitCursor',false);xajax.config.setDefault('statusMessages',false);xajax.config.setDefault('baseDocument',document);xajax.config.setDefault('requestURI',xajax.config.baseDocument.URL);xajax.config.setDefault('defaultMode','asynchronous');xajax.config.setDefault('defaultHttpVersion','HTTP/1.1');xajax.config.setDefault('defaultContentType','application/x-www-form-urlencoded');xajax.config.setDefault('defaultResponseDelayTime',1000);xajax.config.setDefault('defaultExpirationTime',10000);xajax.config.setDefault('defaultMethod','POST');xajax.config.setDefault('defaultRetry',5);xajax.config.setDefault('defaultReturnValue',false);xajax.config.setDefault('maxObjectDepth',20);xajax.config.setDefault('maxObjectSize',2000);xajax.config.status={update:function(){return{onRequest:function(){window.status='Sending Request...';},
onWaiting:function(){window.status='Waiting for Response...';},
onProcessing:function(){window.status='Processing...';},
onComplete:function(){window.status='Done.';}
}
},
dontUpdate:function(){return{onRequest:function(){},
onWaiting:function(){},
onProcessing:function(){},
onComplete:function(){}
}
}
}
xajax.config.cursor={update:function(){return{onWaiting:function(){if(xajax.config.baseDocument.body)
xajax.config.baseDocument.body.style.cursor='wait';},
onComplete:function(){xajax.config.baseDocument.body.style.cursor='auto';}
}
},
dontUpdate:function(){return{onWaiting:function(){},
onComplete:function(){}
}
}
}
xajax.tools={}
xajax.tools.$=function(sId){if(!sId)
return null;var oDoc=xajax.config.baseDocument;var obj=oDoc.getElementById(sId);if(obj)
return obj;if(oDoc.all)
return oDoc.all[sId];return obj;}
xajax.tools.arrayContainsValue=function(array,valueToCheck){var i=0;var l=array.length;while(i < l){if(array[i]==valueToCheck)
return true;++i;}
return false;}
xajax.tools.doubleQuotes=function(haystack){return haystack.replace(new RegExp("'",'g'),'"');}
xajax.tools.singleQuotes=function(haystack){return haystack.replace(new RegExp('"','g'),"'");}
xajax.tools._escape=function(data){if('undefined'==typeof data)
return data;if('string'!=typeof data)
return data;var needCDATA=false;if(encodeURIComponent(data)!=data){needCDATA=true;var segments=data.split('<![CDATA[');var segLen=segments.length;data=[];for(var i=0;i < segLen;++i){var segment=segments[i];var fragments=segment.split(']]>');var fragLen=fragments.length;segment='';for(var j=0;j < fragLen;++j){if(0!=j)
segment+=']]]]><![CDATA[>';segment+=fragments[j];}
if(0!=i)
data.push('<![]]><![CDATA[CDATA[');data.push(segment);}
data=data.join('');}
if(needCDATA)
data='<![CDATA['+data+']]>';return data;}
xajax.tools._objectToXML=function(obj,guard){var aXml=[];aXml.push('<xjxobj>');for(var key in obj){++guard.size;if(guard.maxSize < guard.size)
return aXml.join('');if('undefined'!=typeof obj[key]){if('constructor'==key)
continue;if('function'==typeof obj[key])
continue;aXml.push('<e><k>');var val=xajax.tools._escape(key);aXml.push(val);aXml.push('</k><v>');if('object'==typeof obj[key]){++guard.depth;if(guard.maxDepth > guard.depth){try{aXml.push(xajax.tools._objectToXML(obj[key],guard));}catch(e){}
}
--guard.depth;}else{var val=xajax.tools._escape(obj[key]);if('undefined'==typeof val||null==val){aXml.push('*');}else{var sType=typeof val;if('string'==sType)
aXml.push('S');else if('boolean'==sType)
aXml.push('B');else if('number'==sType)
aXml.push('N');aXml.push(val);}
}
aXml.push('</v></e>');}
}
aXml.push('</xjxobj>');return aXml.join('');}
xajax.tools._enforceDataType=function(value){value=new String(value);var type=value.substr(0,1);value=value.substr(1);if('*'==type)
value=null;else if('N'==type)
value=value-0;else if('B'==type)
value=!!value;return value;}
xajax.tools._nodeToObject=function(node){if(null==node)
return '';if('undefined'!=typeof node.nodeName){if('#cdata-section'==node.nodeName||'#text'==node.nodeName){var data='';do if(node.data)data+=node.data;while(node=node.nextSibling);return xajax.tools._enforceDataType(data);}else if('xjxobj'==node.nodeName){var key=null;var value=null;var data=new Array;var child=node.firstChild;while(child){if('e'==child.nodeName){var grandChild=child.firstChild;while(grandChild){if('k'==grandChild.nodeName)
key=xajax.tools._enforceDataType(grandChild.firstChild.data);else('v'==grandChild.nodeName)
value=xajax.tools._nodeToObject(grandChild.firstChild);grandChild=grandChild.nextSibling;}
if(null!=key){data[key]=value;key=value=null;}
}
child=child.nextSibling;}
return data;}
}
throw{code:10001,data:node.nodeName};}
xajax.tools.getRequestObject=function(){if('undefined'!=typeof XMLHttpRequest){xajax.tools.getRequestObject=function(){return new XMLHttpRequest();}
}else if('undefined'!=typeof ActiveXObject){xajax.tools.getRequestObject=function(){try{return new ActiveXObject('Msxml2.XMLHTTP.4.0');}catch(e){xajax.tools.getRequestObject=function(){try{return new ActiveXObject('Msxml2.XMLHTTP');}catch(e2){xajax.tools.getRequestObject=function(){return new ActiveXObject('Microsoft.XMLHTTP');}
return xajax.tools.getRequestObject();}
}
return xajax.tools.getRequestObject();}
}
}else if(window.createRequest){xajax.tools.getRequestObject=function(){return window.createRequest();}
}else{xajax.tools.getRequestObject=function(){throw{code:10002};}
}
return xajax.tools.getRequestObject();}
xajax.tools.getBrowserHTML=function(sValue){var oDoc=xajax.config.baseDocument;if(!oDoc.body)
return '';var elWorkspace=xajax.$('xajax_temp_workspace');if(!elWorkspace){elWorkspace=oDoc.createElement('div');elWorkspace.setAttribute('id','xajax_temp_workspace');elWorkspace.style.display='none';elWorkspace.style.visibility='hidden';oDoc.body.appendChild(elWorkspace);}
elWorkspace.innerHTML=sValue;var browserHTML=elWorkspace.innerHTML;elWorkspace.innerHTML='';return browserHTML;}
xajax.tools.willChange=function(element,attribute,newData){if('string'==typeof element)
element=xajax.$(element);if(element){var oldData;eval('oldData=element.'+attribute);return(newData!=oldData);}
return false;}
xajax.tools.getFormValues=function(parent){var submitDisabledElements=false;if(arguments.length > 1&&arguments[1]==true)
submitDisabledElements=true;var prefix='';if(arguments.length > 2)
prefix=arguments[2];if('string'==typeof parent)
parent=xajax.$(parent);var aFormValues={};if(parent)
if(parent.childNodes)
xajax.tools._getFormValues(aFormValues,parent.childNodes,submitDisabledElements,prefix);return aFormValues;}
xajax.tools._getFormValues=function(aFormValues,children,submitDisabledElements,prefix){var iLen=children.length;for(var i=0;i < iLen;++i){var child=children[i];if('undefined'!=typeof child.childNodes)
xajax.tools._getFormValues(aFormValues,child.childNodes,submitDisabledElements,prefix);xajax.tools._getFormValue(aFormValues,child,submitDisabledElements,prefix);}
}
xajax.tools._getFormValue=function(aFormValues,child,submitDisabledElements,prefix){if(!child.name)
return;if(child.disabled)
if(true==child.disabled)
if(false==submitDisabledElements)
return;if(prefix!=child.name.substring(0,prefix.length))
return;if(child.type)
if(child.type=='radio'||child.type=='checkbox')
if(false==child.checked)
return;var name=child.name;var values=[];if('select-multiple'==child.type){var jLen=child.length;for(var j=0;j < jLen;++j){var option=child.options[j];if(true==option.selected)
values.push(option.value);}
}else{values=child.value;}
var keyBegin=name.indexOf('[');if(0 <=keyBegin){var n=name;var k=n.substr(0,n.indexOf('['));var a=n.substr(n.indexOf('['));if(typeof aFormValues[k]=='undefined')
aFormValues[k]={};var p=aFormValues;while(a.length!=0){var sa=a.substr(0,a.indexOf(']')+1);var lk=k;var lp=p;a=a.substr(a.indexOf(']')+1);p=p[k];k=sa.substr(1,sa.length-2);if(k==''){if('select-multiple'==child.type){k=lk;p=lp;}else{var i=0;for(afoo in p)i++;k=i;}
}
if(typeof p[k]=='undefined'){p[k]={};}
}
p[k]=values;}else{aFormValues[name]=values;}
}
xajax.tools.stripOnPrefix=function(sEventName){sEventName=sEventName.toLowerCase();if(0==sEventName.indexOf('on'))
sEventName=sEventName.replace(/on/,'');return sEventName;}
xajax.tools.addOnPrefix=function(sEventName){sEventName=sEventName.toLowerCase();if(0!=sEventName.indexOf('on'))
sEventName='on'+sEventName;return sEventName;}
xajax.tools.xml={};xajax.tools.xml.parseAttributes=function(child,obj){var iLen=child.attributes.length;for(var i=0;i < iLen;++i){var attr=child.attributes[i];obj[attr.name]=attr.value;}
}
xajax.tools.xml.parseChildren=function(child,obj){obj.data='';if(0 < child.childNodes.length){if(1 < child.childNodes.length){var grandChild=child.firstChild;do{if('#cdata-section'==grandChild.nodeName||'#text'==grandChild.nodeName){obj.data+=grandChild.data;}
}while(grandChild=grandChild.nextSibling);}else{var grandChild=child.firstChild;if('xjxobj'==grandChild.nodeName){obj.data=xajax.tools._nodeToObject(grandChild);return;}else if('#cdata-section'==grandChild.nodeName||'#text'==grandChild.nodeName){obj.data=grandChild.data;}
}
}else if('undefined'!=typeof child.data){obj.data=child.data;}
obj.data=xajax.tools._enforceDataType(obj.data);}
xajax.tools.xml.processFragment=function(xmlNode,seq,oRequest){var xx=xajax;var xt=xx.tools;while(xmlNode){if('cmd'==xmlNode.nodeName){var obj={};obj.fullName='*unknown*';obj.sequence=seq;obj.request=oRequest;obj.context=oRequest.context;xt.xml.parseAttributes(xmlNode,obj);xt.xml.parseChildren(xmlNode,obj);xt.queue.push(xx.response,obj);}else if('xjxrv'==xmlNode.nodeName){oRequest.returnValue=xt._nodeToObject(xmlNode.firstChild);}else if('debugmsg'==xmlNode.nodeName){}else
throw{code:10004,data:xmlNode.nodeName}
++seq;xmlNode=xmlNode.nextSibling;}
}
xajax.tools.queue={}
xajax.tools.queue.create=function(size){return{start:0,
size:size,
end:0,
commands:[],
timeout:null
}
}
xajax.tools.queue.retry=function(obj,count){var retries=obj.retries;if(retries){--retries;if(1 > retries)
return false;}else retries=count;obj.retries=retries;return true;}
xajax.tools.queue.rewind=function(theQ){if(0 < theQ.start)
--theQ.start;else
theQ.start=theQ.size;}
xajax.tools.queue.setWakeup=function(theQ,when){if(null!=theQ.timeout){clearTimeout(theQ.timeout);theQ.timeout=null;}
theQ.timout=setTimeout(function(){xajax.tools.queue.process(theQ);},when);}
xajax.tools.queue.process=function(theQ){if(null!=theQ.timeout){clearTimeout(theQ.timeout);theQ.timeout=null;}
var obj=xajax.tools.queue.pop(theQ);while(null!=obj){try{if(false==xajax.executeCommand(obj))
return false;}catch(e){}
delete obj;obj=xajax.tools.queue.pop(theQ);}
return true;}
xajax.tools.queue.push=function(theQ,obj){var next=theQ.end+1;if(next > theQ.size)
next=0;if(next!=theQ.start){theQ.commands[theQ.end]=obj;theQ.end=next;}else
throw{code:10003}
}
xajax.tools.queue.pushFront=function(theQ,obj){xajax.tools.queue.rewind(theQ);theQ.commands[theQ.start]=obj;}
xajax.tools.queue.pop=function(theQ){var next=theQ.start;if(next==theQ.end)
return null;next++;if(next > theQ.size)
next=0;var obj=theQ.commands[theQ.start];delete theQ.commands[theQ.start];theQ.start=next;return obj;}
xajax.responseProcessor={};xajax.responseProcessor.xml=function(oRequest){var xx=xajax;var xt=xx.tools;var xcb=xx.callback;var gcb=xcb.global;var lcb=oRequest.callback;if(xt.arrayContainsValue(xx.responseSuccessCodes,oRequest.request.status)){xcb.execute([gcb,lcb],'onSuccess',oRequest);var seq=0;if(oRequest.request.responseXML){var responseXML=oRequest.request.responseXML;if(responseXML.documentElement){oRequest.status.onProcessing();var child=responseXML.documentElement.firstChild;xt.xml.processFragment(child,seq,oRequest);}
}
var obj={};obj.fullName='Response Complete';obj.sequence=seq;obj.request=oRequest;obj.context=oRequest.context;obj.cmd='rcmplt';xt.queue.push(xx.response,obj);if(null==xx.response.timeout)
xt.queue.process(xx.response);}else if(xt.arrayContainsValue(xx.responseRedirectCodes,oRequest.request.status)){xcb.execute([gcb,lcb],'onRedirect',oRequest);window.location=oRequest.request.getResponseHeader('location');xx.completeResponse(oRequest);}else if(xt.arrayContainsValue(xx.responseErrorsForAlert,oRequest.request.status)){xcb.execute([gcb,lcb],'onFailure',oRequest);xx.completeResponse(oRequest);}
return oRequest.returnValue;}
xajax.js={}
xajax.js.includeScriptOnce=function(command){command.fullName='includeScriptOnce';var fileName=command.data;var oDoc=xajax.config.baseDocument;var loadedScripts=oDoc.getElementsByTagName('script');var iLen=loadedScripts.length;for(var i=0;i < iLen;++i){var script=loadedScripts[i];if(script.src){if(0 <=script.src.indexOf(fileName))
return true;}
}
return xajax.js.includeScript({data:fileName});}
xajax.js.includeScript=function(command){command.fullName='includeScript';var fileName=command.data;var oDoc=xajax.config.baseDocument;var objHead=oDoc.getElementsByTagName('head');var objScript=oDoc.createElement('script');objScript.type='text/javascript';objScript.src=fileName;if('function'==command.onload){objScript.onload.command.onload;}
objHead[0].appendChild(objScript);return true;}
xajax.js.removeScript=function(command){command.fullName='removeScript';var fileName=command.data;var unload=command.unld;var oDoc=xajax.config.baseDocument;var loadedScripts=oDoc.getElementsByTagName('script');var iLen=loadedScripts.length;for(var i=0;i < iLen;++i){var script=loadedScripts[i];if(script.src){if(0 <=script.src.indexOf(fileName)){if('undefined'!=typeof unload){var args={};args.data=unload;args.context=window;xajax.js.execute(args);}
var parent=script.parentNode;parent.removeChild(script);}
}
}
return true;}
xajax.js.sleep=function(command){command.fullName='sleep';if(xajax.tools.queue.retry(command,command.prop)){xajax.tools.queue.setWakeup(xajax.response,100);return false;}
return true;}
xajax.js.confirmCommands=function(command){command.fullName='confirmCommands';var msg=command.data;var numberOfCommands=command.id;if(false==confirm(msg)){while(0 < numberOfCommands){xajax.tools.queue.pop(xajax.response);--numberOfCommands;}
}
return true;}
xajax.js.execute=function(args){args.fullName='execute Javascript';var returnValue=true;args.context.xajaxDelegateCall=function(){eval(args.data);}
args.context.xajaxDelegateCall();return returnValue;}
xajax.js.waitFor=function(args){args.fullName='waitFor';var bResult=false;var cmdToEval='bResult = (';cmdToEval+=args.data;cmdToEval+=');';try{args.context.xajaxDelegateCall=function(){eval(cmdToEval);}
args.context.xajaxDelegateCall();}catch(e){}
if(false==bResult){if(xajax.tools.queue.retry(args,args.prop)){xajax.tools.queue.setWakeup(xajax.response,100);return false;}
}
return true;}
xajax.js.call=function(args){args.fullName='call js function';var parameters=args.data;var scr=new Array();scr.push(args.func);scr.push('(');if('undefined'!=typeof parameters){if('object'==typeof parameters){var iLen=parameters.length;if(0 < iLen){scr.push('parameters[0]');for(var i=1;i < iLen;++i)
scr.push(', parameters['+i+']');}
}
}
scr.push(');');args.context.xajaxDelegateCall=function(){eval(scr.join(''));}
args.context.xajaxDelegateCall();return true;}
xajax.js.setFunction=function(args){args.fullName='setFunction';var code=new Array();code.push(args.func);code.push(' = function(');if('object'==typeof args.prop){var separator='';for(var m in args.prop){code.push(separator);code.push(args.prop[m]);separator=',';}
}else code.push(args.prop);code.push(') { ');code.push(args.data);code.push(' }');args.context.xajaxDelegateCall=function(){eval(code.join(''));}
args.context.xajaxDelegateCall();return true;}
xajax.js.wrapFunction=function(args){args.fullName='wrapFunction';var code=new Array();code.push(args.func);code.push(' = xajax.js.makeWrapper(');code.push(args.func);code.push(', args.prop, args.data, args.type, args.context);');args.context.xajaxDelegateCall=function(){eval(code.join(''));}
args.context.xajaxDelegateCall();return true;}
xajax.js.makeWrapper=function(origFun,args,codeBlocks,returnVariable,context){var originalCall='';if(0 < returnVariable.length){originalCall+=returnVariable;originalCall+=' = ';}
var originalCall='origFun(';originalCall+=args;originalCall+='); ';var code='wrapper = function(';code+=args;code+=') { ';if(0 < returnVariable.length){code+=' var ';code+=returnVariable;code+=' = null;';}
var separator='';var bLen=codeBlocks.length;for(var b=0;b < bLen;++b){code+=separator;code+=codeBlocks[b];separator=originalCall;}
if(0 < returnVariable.length){code+=' return ';code+=returnVariable;code+=';';}
code+=' } ';var wrapper=null;context.xajaxDelegateCall=function(){eval(code);}
context.xajaxDelegateCall();return wrapper;}
xajax.dom={}
xajax.dom.assign=function(element,property,data){if('string'==typeof element)
element=xajax.$(element);switch(property){case 'innerHTML':
element.innerHTML=data;break;case 'outerHTML':
if('undefined'==typeof element.outerHTML){var r=xajax.config.baseDocument.createRange();r.setStartBefore(element);var df=r.createContextualFragment(data);element.parentNode.replaceChild(df,element);}else element.outerHTML=data;break;default:
if(xajax.tools.willChange(element,property,data))
eval('element.'+property+' = data;');break;}
return true;}
xajax.dom.append=function(element,property,data){if('string'==typeof element)
element=xajax.$(element);eval('element.'+property+' += data;');return true;}
xajax.dom.prepend=function(element,property,data){if('string'==typeof element)
element=xajax.$(element);eval('element.'+property+' = data + element.'+property);return true;}
xajax.dom.replace=function(element,sAttribute,aData){var sSearch=aData['s'];var sReplace=aData['r'];if(sAttribute=='innerHTML')
sSearch=xajax.tools.getBrowserHTML(sSearch);if('string'==typeof element)
element=xajax.$(element);eval('var txt = element.'+sAttribute);var bFunction=false;if('function'==typeof txt){txt=txt.join('');bFunction=true;}
var start=txt.indexOf(sSearch);if(start >-1){var newTxt=[];while(start >-1){var end=start+sSearch.length;newTxt.push(txt.substr(0,start));newTxt.push(sReplace);txt=txt.substr(end,txt.length-end);start=txt.indexOf(sSearch);}
newTxt.push(txt);newTxt=newTxt.join('');if(bFunction){eval('element.'+sAttribute+'=newTxt;');}else if(xajax.tools.willChange(element,sAttribute,newTxt)){eval('element.'+sAttribute+'=newTxt;');}
}
return true;}
xajax.dom.remove=function(element){if('string'==typeof element)
element=xajax.$(element);if(element&&element.parentNode&&element.parentNode.removeChild)
element.parentNode.removeChild(element);return true;}
xajax.dom.create=function(objParent,sTag,sId){if('string'==typeof objParent)
objParent=xajax.$(objParent);var target=xajax.config.baseDocument.createElement(sTag);target.setAttribute('id',sId);if(objParent)
objParent.appendChild(target);return true;}
xajax.dom.insert=function(objSibling,sTag,sId){if('string'==typeof objSibling)
objSibling=xajax.$(objSibling);var target=xajax.config.baseDocument.createElement(sTag);target.setAttribute('id',sId);objSibling.parentNode.insertBefore(target,objSibling);return true;}
xajax.dom.insertAfter=function(objSibling,sTag,sId){if('string'==typeof objSibling)
objSibling=xajax.$(objSibling);var target=xajax.config.baseDocument.createElement(sTag);target.setAttribute('id',sId);objSibling.parentNode.insertBefore(target,objSibling.nextSibling);return true;}
xajax.dom.contextAssign=function(args){args.fullName='context assign';var code=[];code.push('this.');code.push(args.prop);code.push(' = data;');code=code.join('');args.context.xajaxDelegateCall=function(data){eval(code);}
args.context.xajaxDelegateCall(args.data);return true;}
xajax.dom.contextAppend=function(args){args.fullName='context append';var code=[];code.push('this.');code.push(args.prop);code.push(' += data;');code=code.join('');args.context.xajaxDelegateCall=function(data){eval(code);}
args.context.xajaxDelegateCall(args.data);return true;}
xajax.dom.contextPrepend=function(args){args.fullName='context prepend';var code=[];code.push('this.');code.push(args.prop);code.push(' = data + this.');code.push(args.prop);code.push(';');code=code.join('');args.context.xajaxDelegateCall=function(data){eval(code);}
args.context.xajaxDelegateCall(args.data);return true;}
xajax.css={}
xajax.css.add=function(filename){var oDoc=xajax.config.baseDocument;var oHeads=oDoc.getElementsByTagName('head');var oHead=oHeads[0];var oLinks=oHead.getElementsByTagName('link');var found=false;var iLen=oLinks.length;for(var i=0;i < iLen&&false==found;++i)
if(0 < oLinks[i].href.indexOf(filename))
found=true;if(false==found){var oCSS=oDoc.createElement('link');oCSS.rel='stylesheet';oCSS.type='text/css';oCSS.href=filename;oHead.appendChild(oCSS);}
return true;}
xajax.css.remove=function(filename){var oDoc=xajax.config.baseDocument;var oHeads=oDoc.getElementsByTagName('head');var oHead=oHeads[0];var oLinks=oHead.getElementsByTagName('link');var i=0;while(i < oLinks.length)
if(0 <=oLinks[i].href.indexOf(filename))
oHead.removeChild(oLinks[i]);else++i;return true;}
xajax.css.waitForCSS=function(args){var oDocSS=xajax.config.baseDocument.styleSheets;var ssEnabled=[];var iLen=oDocSS.length;for(var i=0;i < iLen;++i){ssEnabled[i]=0;try{ssEnabled[i]=oDocSS[i].cssRules.length;}catch(e){try{ssEnabled[i]=oDocSS[i].rules.length;}catch(e){}
}
}
var ssLoaded=true;var iLen=ssEnabled.length;for(var i=0;i < iLen;++i)
if(0==ssEnabled[i])
ssLoaded=false;if(false==ssLoaded){if(xajax.tools.queue.retry(args,args.prop)){xajax.tools.queue.setWakeup(xajax.response,10);return false;}
}
return true;}
xajax.forms={}
xajax.forms.getInput=function(type,name,id){if('undefined'==typeof window.addEventListener){xajax.forms.getInput=function(type,name,id){return xajax.config.baseDocument.createElement('<input type="'+type+'" name="'+name+'" id="'+id+'">');}
}else{xajax.forms.getInput=function(type,name,id){var oDoc=xajax.config.baseDocument;var Obj=oDoc.createElement('input');Obj.setAttribute('type',type);Obj.setAttribute('name',name);Obj.setAttribute('id',id);return Obj;}
}
return xajax.forms.getInput(type,name,id);}
xajax.forms.createInput=function(command){command.fullName='createInput';var objParent=command.id;var sType=args.type;var sName=args.data;var sId=args.prop;if('string'==typeof objParent)
objParent=xajax.$(objParent);var target=xajax.forms.getInput(sType,sName,sId);if(objParent&&target)
objParent.appendChild(target);return true;}
xajax.forms.insertInput=function(command){command.fullName='insertInput';var objSibling=command.id;var sType=command.type;var sName=command.data;var sId=command.prop;if('string'==typeof objSibling)
objSibling=xajax.$(objSibling);var target=xajax.forms.getInput(sType,sName,sId);if(target&&objSibling&&objSibling.parentNode)
objSibling.parentNode.insertBefore(target,objSibling);return true;}
xajax.forms.insertInputAfter=function(command){command.fullName='insertInputAfter';var objSibling=command.id;var sType=command.type;var sName=command.data;var sId=command.prop;if('string'==typeof objSibling)
objSibling=xajax.$(objSibling);var target=xajax.forms.getInput(sType,sName,sId);if(target&&objSibling&&objSibling.parentNode)
objSibling.parentNode.insertBefore(target,objSibling.nextSibling);return true;}
xajax.events={}
xajax.events.setEvent=function(command){command.fullName='addEvent';var element=command.id;var sEvent=command.prop;var code=command.data;if('string'==typeof element)
element=xajax.$(element);sEvent=xajax.tools.addOnPrefix(sEvent);code=xajax.tools.doubleQuotes(code);eval('element.'+sEvent+' = function() { '+code+'; }');return true;}
xajax.events.addHandler=function(element,sEvent,fun){if(window.addEventListener){xajax.events.addHandler=function(command){command.fullName='addHandler';var element=command.id;var sEvent=command.prop;var fun=command.data;if('string'==typeof element)
element=xajax.$(element);sEvent=xajax.tools.stripOnPrefix(sEvent);eval('element.addEventListener("'+sEvent+'", '+fun+', false);');return true;}
}else{xajax.events.addHandler=function(command){command.fullName='addHandler';var element=command.id;var sEvent=command.prop;var fun=command.data;if('string'==typeof element)
element=xajax.$(element);sEvent=xajax.tools.addOnPrefix(sEvent);eval('element.attachEvent("'+sEvent+'", '+fun+', false);');return true;}
}
return xajax.events.addHandler(element,sEvent,fun);}
xajax.events.removeHandler=function(element,sEvent,fun){if(window.removeEventListener){xajax.events.removeHandler=function(command){command.fullName='removeHandler';var element=command.id;var sEvent=command.prop;var fun=command.data;if('string'==typeof element)
element=xajax.$(element);sEvent=xajax.tools.stripOnPrefix(sEvent);eval('element.removeEventListener("'+sEvent+'", '+fun+', false);');return true;}
}else{xajax.events.removeHandler=function(command){command.fullName='removeHandler';var element=command.id;var sEvent=command.prop;var fun=command.data;if('string'==typeof element)
element=xajax.$(element);sEvent=xajax.tools.addOnPrefix(sEvent);eval('element.detachEvent("'+sEvent+'", '+fun+', false);');return true;}
}
return xajax.events.removeHandler(element,sEvent,fun);}
xajax.callback={}
xajax.callback.create=function(){var xx=xajax;var xc=xx.config;var xcb=xx.callback;var oCB={}
oCB.timers={};oCB.timers.onResponseDelay=xcb.setupTimer(
(arguments.length > 0)
? arguments[0]
:xc.defaultResponseDelayTime);oCB.timers.onExpiration=xcb.setupTimer(
(arguments.length > 1)
? arguments[1]
:xc.defaultExpirationTime);oCB.onRequest=null;oCB.onResponseDelay=null;oCB.onExpiration=null;oCB.beforeResponseProcessing=null;oCB.onFailure=null;oCB.onRedirect=null;oCB.onSuccess=null;oCB.onComplete=null;return oCB;}
xajax.callback.setupTimer=function(iDelay){return{timer:null,delay:iDelay};}
xajax.callback.clearTimer=function(oCallback,sFunction){if('undefined'!=typeof oCallback.timers){if('undefined'!=typeof oCallback.timers[sFunction]){clearTimeout(oCallback.timers[sFunction].timer);}
}else if('object'==typeof oCallback){var iLen=oCallback.length;for(var i=0;i < iLen;++i)
xajax.callback.clearTimer(oCallback[i],sFunction);}
}
xajax.callback.execute=function(oCallback,sFunction,args){if('undefined'!=typeof oCallback[sFunction]){var func=oCallback[sFunction];if('function'==typeof func){if('undefined'!=typeof oCallback.timers[sFunction]){oCallback.timers[sFunction].timer=setTimeout(function(){func(args);},oCallback.timers[sFunction].delay);}
else{func(args);}
}
}else if('object'==typeof oCallback){var iLen=oCallback.length;for(var i=0;i < iLen;++i)
xajax.callback.execute(oCallback[i],sFunction,args);}
}
xajax.callback.global=xajax.callback.create();xajax.response=xajax.tools.queue.create(1000);xajax.responseSuccessCodes=['0','200'];xajax.responseErrorsForAlert=['400','401','402','403','404','500','501','502','503'];xajax.responseRedirectCodes=['301','302','307'];if('undefined'==typeof xajax.command)
xajax.command={};xajax.command.create=function(sequence,request,context){var newCmd={};newCmd.cmd='*';newCmd.fullName='* unknown command name *';newCmd.sequence=sequence;newCmd.request=request;newCmd.context=context;return newCmd;}
if('undefined'==typeof xajax.command.handler)
xajax.command.handler={};if('undefined'==typeof xajax.command.handler.handlers)
xajax.command.handler.handlers=[];xajax.command.handler.register=function(shortName,func){xajax.command.handler.handlers[shortName]=func;}
xajax.command.handler.unregister=function(shortName){var func=xajax.command.handler.handlers[shortName];delete xajax.command.handler.handlers[shortName];return func;}
xajax.command.handler.isRegistered=function(command){var shortName=command.cmd;if(xajax.command.handler.handlers[shortName])
return true;return false;}
xajax.command.handler.call=function(command){var shortName=command.cmd;return xajax.command.handler.handlers[shortName](command);}
xajax.command.handler.register('rcmplt',function(args){xajax.completeResponse(args.request);return true;});xajax.command.handler.register('css',function(args){args.fullName='includeCSS';return xajax.css.add(args.data);});xajax.command.handler.register('rcss',function(args){args.fullName='removeCSS';return xajax.css.remove(args.data);});xajax.command.handler.register('wcss',function(args){args.fullName='waitForCSS';return xajax.css.waitForCSS(args);});xajax.command.handler.register('as',function(args){args.fullName='assign/clear';try{return xajax.dom.assign(args.target,args.prop,args.data);}catch(e){}
return true;});xajax.command.handler.register('ap',function(args){args.fullName='append';return xajax.dom.append(args.target,args.prop,args.data);});xajax.command.handler.register('pp',function(args){args.fullName='prepend';return xajax.dom.prepend(args.target,args.prop,args.data);});xajax.command.handler.register('rp',function(args){args.fullName='replace';return xajax.dom.replace(args.id,args.prop,args.data);});xajax.command.handler.register('rm',function(args){args.fullName='remove';return xajax.dom.remove(args.id);});xajax.command.handler.register('ce',function(args){args.fullName='create';return xajax.dom.create(args.id,args.data,args.prop);});xajax.command.handler.register('ie',function(args){args.fullName='insert';return xajax.dom.insert(args.id,args.data,args.prop);});xajax.command.handler.register('ia',function(args){args.fullName='insertAfter';return xajax.dom.insertAfter(args.id,args.data,args.prop);});xajax.command.handler.register('c:as',xajax.dom.contextAssign);xajax.command.handler.register('c:ap',xajax.dom.contextAppend);xajax.command.handler.register('c:pp',xajax.dom.contextPrepend);xajax.command.handler.register('s',xajax.js.sleep);xajax.command.handler.register('ino',xajax.js.includeScriptOnce);xajax.command.handler.register('in',xajax.js.includeScript);xajax.command.handler.register('rjs',xajax.js.removeScript);xajax.command.handler.register('wf',xajax.js.waitFor);xajax.command.handler.register('js',xajax.js.execute);xajax.command.handler.register('jc',xajax.js.call);xajax.command.handler.register('sf',xajax.js.setFunction);xajax.command.handler.register('wpf',xajax.js.wrapFunction);xajax.command.handler.register('al',function(args){args.fullName='alert';alert(args.data);return true;});xajax.command.handler.register('cc',xajax.js.confirmCommands);xajax.command.handler.register('ci',xajax.forms.createInput);xajax.command.handler.register('ii',xajax.forms.insertInput);xajax.command.handler.register('iia',xajax.forms.insertInputAfter);xajax.command.handler.register('ev',xajax.events.setEvent);xajax.command.handler.register('ah',xajax.events.addHandler);xajax.command.handler.register('rh',xajax.events.removeHandler);xajax.command.handler.register('dbg',function(args){args.fullName='debug message';return true;});xajax.initializeRequest=function(oRequest){var xx=xajax;var xc=xx.config;oRequest.append=function(opt,def){if('undefined'!=typeof this[opt]){for(var itmName in def)
if('undefined'==typeof this[opt][itmName])
this[opt][itmName]=def[itmName];}else this[opt]=def;}
oRequest.append('commonHeaders',xc.commonHeaders);oRequest.append('postHeaders',xc.postHeaders);oRequest.append('getHeaders',xc.getHeaders);oRequest.set=function(option,defaultValue){if('undefined'==typeof this[option])
this[option]=defaultValue;}
oRequest.set('statusMessages',xc.statusMessages);oRequest.set('waitCursor',xc.waitCursor);oRequest.set('mode',xc.defaultMode);oRequest.set('method',xc.defaultMethod);oRequest.set('URI',xc.requestURI);oRequest.set('httpVersion',xc.defaultHttpVersion);oRequest.set('contentType',xc.defaultContentType);oRequest.set('retry',xc.defaultRetry);oRequest.set('returnValue',xc.defaultReturnValue);oRequest.set('maxObjectDepth',xc.maxObjectDepth);oRequest.set('maxObjectSize',xc.maxObjectSize);oRequest.set('context',window);var xcb=xx.callback;var gcb=xcb.global;var lcb=xcb.create();lcb.take=function(frm,opt){if('undefined'!=typeof frm[opt]){lcb[opt]=frm[opt];lcb.hasEvents=true;}
delete frm[opt];}
lcb.take(oRequest,'onRequest');lcb.take(oRequest,'onResponseDelay');lcb.take(oRequest,'onExpiration');lcb.take(oRequest,'beforeResponseProcessing');lcb.take(oRequest,'onFailure');lcb.take(oRequest,'onRedirect');lcb.take(oRequest,'onSuccess');lcb.take(oRequest,'onComplete');if('undefined'!=typeof oRequest.callback){if(lcb.hasEvents)
oRequest.callback=[oRequest.callback,lcb];}else
oRequest.callback=lcb;oRequest.status=(oRequest.statusMessages)
? xc.status.update()
:xc.status.dontUpdate();oRequest.cursor=(oRequest.waitCursor)
? xc.cursor.update()
:xc.cursor.dontUpdate();oRequest.method=oRequest.method.toUpperCase();if('GET'!=oRequest.method)
oRequest.method='POST';oRequest.requestRetry=oRequest.retry;oRequest.append('postHeaders',{'content-type':oRequest.contentType
});delete oRequest['append'];delete oRequest['set'];delete oRequest['take'];if('undefined'==typeof oRequest.URI)
throw{code:10005}
}
xajax.processParameters=function(oRequest){var xx=xajax;var xt=xx.tools;var rd=[];var separator='';for(var sCommand in oRequest.functionName){if('constructor'!=sCommand){rd.push(separator);rd.push(sCommand);rd.push('=');rd.push(encodeURIComponent(oRequest.functionName[sCommand]));separator='&';}
}
var dNow=new Date();rd.push('&xjxr=');rd.push(dNow.getTime());delete dNow;if(oRequest.parameters){var i=0;var iLen=oRequest.parameters.length;while(i < iLen){var oVal=oRequest.parameters[i];if('object'==typeof oVal&&null!=oVal){try{var oGuard={};oGuard.depth=0;oGuard.maxDepth=oRequest.maxObjectDepth;oGuard.size=0;oGuard.maxSize=oRequest.maxObjectSize;oVal=xt._objectToXML(oVal,oGuard);}catch(e){oVal='';}
rd.push('&xjxargs[]=');oVal=encodeURIComponent(oVal);rd.push(oVal);++i;}else{rd.push('&xjxargs[]=');oVal=xt._escape(oVal);if('undefined'==typeof oVal||null==oVal){rd.push('*');}else{var sType=typeof oVal;if('string'==sType)
rd.push('S');else if('boolean'==sType)
rd.push('B');else if('number'==sType)
rd.push('N');oVal=encodeURIComponent(oVal);rd.push(oVal);}
++i;}
}
}
oRequest.requestURI=oRequest.URI;if('GET'==oRequest.method){oRequest.requestURI+=oRequest.requestURI.indexOf('?')==-1 ? '?':'&';oRequest.requestURI+=rd.join('');rd=[];}
oRequest.requestData=rd.join('');}
xajax.prepareRequest=function(oRequest){var xx=xajax;var xt=xx.tools;oRequest.request=xt.getRequestObject();oRequest.setRequestHeaders=function(headers){if('object'==typeof headers){for(var optionName in headers)
this.request.setRequestHeader(optionName,headers[optionName]);}
}
oRequest.setCommonRequestHeaders=function(){this.setRequestHeaders(this.commonHeaders);}
oRequest.setPostRequestHeaders=function(){this.setRequestHeaders(this.postHeaders);}
oRequest.setGetRequestHeaders=function(){this.setRequestHeaders(this.getHeaders);}
if('asynchronous'==oRequest.mode){oRequest.request.onreadystatechange=function(){if(oRequest.request.readyState!=4)
return;xajax.responseReceived(oRequest);}
oRequest.finishRequest=function(){return this.returnValue;}
}else{oRequest.finishRequest=function(){return xajax.responseReceived(oRequest);}
}
if('undefined'!=typeof oRequest.userName&&'undefined'!=typeof oRequest.password){oRequest.open=function(){this.request.open(
this.method,
this.requestURI,
'asynchronous'==this.mode,
oRequest.userName,
oRequest.password);}
}else{oRequest.open=function(){this.request.open(
this.method,
this.requestURI,
'asynchronous'==this.mode);}
}
if('POST'==oRequest.method){oRequest.applyRequestHeaders=function(){this.setCommonRequestHeaders();try{this.setPostRequestHeaders();}catch(e){this.method='GET';this.requestURI+=this.requestURI.indexOf('?')==-1 ? '?':'&';this.requestURI+=this.requestData;this.requestData='';if(0==this.requestRetry)this.requestRetry=1;throw e;}
}
}else{oRequest.applyRequestHeaders=function(){this.setCommonRequestHeaders();this.setGetRequestHeaders();}
}
}
xajax.request=function(){var numArgs=arguments.length;if(0==numArgs)
return false;var oRequest={}
if(1 < numArgs)
oRequest=arguments[1];oRequest.functionName=arguments[0];var xx=xajax;xx.initializeRequest(oRequest);xx.processParameters(oRequest);while(0 < oRequest.requestRetry){try{--oRequest.requestRetry;xx.prepareRequest(oRequest);return xx.submitRequest(oRequest);}catch(e){xajax.callback.execute(
[xajax.callback.global,oRequest.callback],
'onFailure',oRequest);if(0==oRequest.requestRetry)
throw e;}
}
}
xajax.call=function(){var numArgs=arguments.length;if(0==numArgs)
return false;var oRequest={}
if(1 < numArgs)
oRequest=arguments[1];oRequest.functionName={xjxfun:arguments[0]};var xx=xajax;xx.initializeRequest(oRequest);xx.processParameters(oRequest);while(0 < oRequest.requestRetry){try{--oRequest.requestRetry;xx.prepareRequest(oRequest);return xx.submitRequest(oRequest);}catch(e){xajax.callback.execute(
[xajax.callback.global,oRequest.callback],
'onFailure',oRequest);if(0==oRequest.requestRetry)
throw e;}
}
}
xajax.submitRequest=function(oRequest){oRequest.status.onRequest();var xcb=xajax.callback;var gcb=xcb.global;var lcb=oRequest.callback;xcb.execute([gcb,lcb],'onResponseDelay',oRequest);xcb.execute([gcb,lcb],'onExpiration',oRequest);xcb.execute([gcb,lcb],'onRequest',oRequest);oRequest.open();oRequest.applyRequestHeaders();oRequest.cursor.onWaiting();oRequest.status.onWaiting();xajax._internalSend(oRequest);return oRequest.finishRequest();}
xajax._internalSend=function(oRequest){oRequest.request.send(oRequest.requestData);}
xajax.abortRequest=function(oRequest){oRequest.aborted=true;oRequest.request.abort();xajax.completeResponse(oRequest);}
xajax.responseReceived=function(oRequest){var xx=xajax;var xcb=xx.callback;var gcb=xcb.global;var lcb=oRequest.callback;if(oRequest.aborted)
return;xcb.clearTimer([gcb,lcb],'onExpiration');xcb.clearTimer([gcb,lcb],'onResponseDelay');xcb.execute([gcb,lcb],'beforeResponseProcessing',oRequest);var fProc=xx.getResponseProcessor(oRequest);if('undefined'==typeof fProc){xcb.execute([gcb,lcb],'onFailure',oRequest);xx.completeResponse(oRequest);return;}
return fProc(oRequest);}
xajax.getResponseProcessor=function(oRequest){var fProc;if('undefined'==typeof oRequest.responseProcessor){var cTyp=oRequest.request.getResponseHeader('content-type');if(cTyp){if(0 <=cTyp.indexOf('text/xml')){fProc=xajax.responseProcessor.xml;}
}
}else fProc=oRequest.responseProcessor;return fProc;}
xajax.executeCommand=function(command){if(xajax.command.handler.isRegistered(command)){if(command.id)
command.target=xajax.$(command.id);if(false==xajax.command.handler.call(command)){xajax.tools.queue.pushFront(xajax.response,command);return false;}
}
return true;}
xajax.completeResponse=function(oRequest){xajax.callback.execute(
[xajax.callback.global,oRequest.callback],
'onComplete',oRequest);oRequest.cursor.onComplete();oRequest.status.onComplete();delete oRequest['functionName'];delete oRequest['requestURI'];delete oRequest['requestData'];delete oRequest['requestRetry'];delete oRequest['request'];delete oRequest['set'];delete oRequest['open'];delete oRequest['setRequestHeaders'];delete oRequest['setCommonRequestHeaders'];delete oRequest['setPostRequestHeaders'];delete oRequest['setGetRequestHeaders'];delete oRequest['applyRequestHeaders'];delete oRequest['finishRequest'];delete oRequest['status'];delete oRequest['cursor'];}
xajax.$=xajax.tools.$;xajax.getFormValues=xajax.tools.getFormValues;xajax.isLoaded=true;xjx={}
xjx.$=xajax.tools.$;xjx.getFormValues=xajax.tools.getFormValues;xjx.call=xajax.call;xjx.request=xajax.request;