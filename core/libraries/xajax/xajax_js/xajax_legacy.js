
try{if(undefined==xajax.legacy)
xajax.legacy={}
}catch(e){alert('An internal error has occurred: the xajax_core has not been loaded prior to xajax_legacy.');}
xajax.legacy.call=xajax.call;xajax.call=function(sFunction,objParameters){var oOpt={}
oOpt.parameters=objParameters;if(undefined!=xajax.loadingFunction){if(undefined==oOpt.callback)
oOpt.callback={}
oOpt.callback.onResponseDelay=xajax.loadingFunction;}
if(undefined!=xajax.doneLoadingFunction){if(undefined==oOpt.callback)
oOpt.callback={}
oOpt.callback.onComplete=xajax.doneLoadingFunction;}
return xajax.legacy.call(sFunction,oOpt);}
xajax.legacy.isLoaded=true;