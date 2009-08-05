
var SWFUpload=function(settings){this.initSWFUpload(settings);};SWFUpload.prototype.initSWFUpload=function(settings){try{this.customSettings={};this.settings=settings;this.eventQueue=[];this.movieName="SWFUpload_"+SWFUpload.movieCount++;this.movieElement=null;SWFUpload.instances[this.movieName]=this;this.initSettings();this.loadFlash();this.displayDebugInfo();}catch(ex){delete SWFUpload.instances[this.movieName];throw ex;}
};SWFUpload.instances={};SWFUpload.movieCount=0;SWFUpload.version="2.1.0 beta 1";SWFUpload.QUEUE_ERROR={QUEUE_LIMIT_EXCEEDED:-100,
FILE_EXCEEDS_SIZE_LIMIT:-110,
ZERO_BYTE_FILE:-120,
INVALID_FILETYPE:-130
};SWFUpload.UPLOAD_ERROR={HTTP_ERROR:-200,
MISSING_UPLOAD_URL:-210,
IO_ERROR:-220,
SECURITY_ERROR:-230,
UPLOAD_LIMIT_EXCEEDED:-240,
UPLOAD_FAILED:-250,
SPECIFIED_FILE_ID_NOT_FOUND:-260,
FILE_VALIDATION_FAILED:-270,
FILE_CANCELLED:-280,
UPLOAD_STOPPED:-290
};SWFUpload.FILE_STATUS={QUEUED:-1,
IN_PROGRESS:-2,
ERROR:-3,
COMPLETE:-4,
CANCELLED:-5
};SWFUpload.prototype.initSettings=function(){this.ensureDefault=function(settingName,defaultValue){this.settings[settingName]=(this.settings[settingName]==undefined)? defaultValue:this.settings[settingName];};this.ensureDefault("upload_url","");this.ensureDefault("file_post_name","Filedata");this.ensureDefault("post_params",{});this.ensureDefault("use_query_string",false);this.ensureDefault("requeue_on_error",false);this.ensureDefault("file_types","*.*");this.ensureDefault("file_types_description","All Files");this.ensureDefault("file_size_limit",0);this.ensureDefault("file_upload_limit",0);this.ensureDefault("file_queue_limit",0);this.ensureDefault("flash_url","swfupload_f9.swf");this.ensureDefault("flash_color","#FFFFFF");this.ensureDefault("debug",false);this.settings.debug_enabled=this.settings.debug;this.settings.return_upload_start_handler=this.returnUploadStart;this.ensureDefault("swfupload_loaded_handler",null);this.ensureDefault("file_dialog_start_handler",null);this.ensureDefault("file_queued_handler",null);this.ensureDefault("file_queue_error_handler",null);this.ensureDefault("file_dialog_complete_handler",null);this.ensureDefault("upload_start_handler",null);this.ensureDefault("upload_progress_handler",null);this.ensureDefault("upload_error_handler",null);this.ensureDefault("upload_success_handler",null);this.ensureDefault("upload_complete_handler",null);this.ensureDefault("debug_handler",this.debugMessage);this.ensureDefault("custom_settings",{});this.customSettings=this.settings.custom_settings;delete this.ensureDefault;};SWFUpload.prototype.loadFlash=function(){var targetElement,container;if(document.getElementById(this.movieName)!==null){throw "ID "+this.movieName+" is already in use. The Flash Object could not be added";}
targetElement=document.getElementsByTagName("body")[0];if(targetElement==undefined){throw "Could not find the 'body' element.";}
container=document.createElement("div");container.style.width="1px";container.style.height="1px";targetElement.appendChild(container);container.innerHTML=this.getFlashHTML();};SWFUpload.prototype.getFlashHTML=function(){return ['<object id="',this.movieName,'" type="application/x-shockwave-flash" data="',this.settings.flash_url,'" width="1" height="1" style="-moz-user-focus: ignore;">',
'<param name="movie" value="',this.settings.flash_url,'" />',
'<param name="bgcolor" value="',this.settings.flash_color,'" />',
'<param name="quality" value="high" />',
'<param name="menu" value="false" />',
'<param name="allowScriptAccess" value="always" />',
'<param name="flashvars" value="'+this.getFlashVars()+'" />',
'</object>'].join("");};SWFUpload.prototype.getFlashVars=function(){var paramString=this.buildParamString();return ["movieName=",encodeURIComponent(this.movieName),
"&amp;uploadURL=",encodeURIComponent(this.settings.upload_url),
"&amp;useQueryString=",encodeURIComponent(this.settings.use_query_string),
"&amp;requeueOnError=",encodeURIComponent(this.settings.requeue_on_error),
"&amp;params=",encodeURIComponent(paramString),
"&amp;filePostName=",encodeURIComponent(this.settings.file_post_name),
"&amp;fileTypes=",encodeURIComponent(this.settings.file_types),
"&amp;fileTypesDescription=",encodeURIComponent(this.settings.file_types_description),
"&amp;fileSizeLimit=",encodeURIComponent(this.settings.file_size_limit),
"&amp;fileUploadLimit=",encodeURIComponent(this.settings.file_upload_limit),
"&amp;fileQueueLimit=",encodeURIComponent(this.settings.file_queue_limit),
"&amp;debugEnabled=",encodeURIComponent(this.settings.debug_enabled)].join("");};SWFUpload.prototype.getMovieElement=function(){if(this.movieElement==undefined){this.movieElement=document.getElementById(this.movieName);}
if(this.movieElement===null){throw "Could not find Flash element";}
return this.movieElement;};SWFUpload.prototype.buildParamString=function(){var postParams=this.settings.post_params;var paramStringPairs=[];if(typeof(postParams)==="object"){for(var name in postParams){if(postParams.hasOwnProperty(name)){paramStringPairs.push(encodeURIComponent(name.toString())+"="+encodeURIComponent(postParams[name].toString()));}
}
}
return paramStringPairs.join("&amp;");};SWFUpload.prototype.displayDebugInfo=function(){this.debug(
[
"---SWFUpload Instance Info---\n",
"Version: ",SWFUpload.version,"\n",
"Movie Name: ",this.movieName,"\n",
"Settings:\n",
"\t","upload_url:             ",this.settings.upload_url,"\n",
"\t","use_query_string:       ",this.settings.use_query_string.toString(),"\n",
"\t","file_post_name:         ",this.settings.file_post_name,"\n",
"\t","post_params:            ",this.settings.post_params.toString(),"\n",
"\t","file_types:             ",this.settings.file_types,"\n",
"\t","file_types_description: ",this.settings.file_types_description,"\n",
"\t","file_size_limit:        ",this.settings.file_size_limit,"\n",
"\t","file_upload_limit:      ",this.settings.file_upload_limit,"\n",
"\t","file_queue_limit:       ",this.settings.file_queue_limit,"\n",
"\t","flash_url:              ",this.settings.flash_url,"\n",
"\t","flash_color:            ",this.settings.flash_color,"\n",
"\t","debug:                  ",this.settings.debug.toString(),"\n",
"\t","custom_settings:        ",this.settings.custom_settings.toString(),"\n",
"Event Handlers:\n",
"\t","swfupload_loaded_handler assigned:  ",(typeof(this.settings.swfupload_loaded_handler)==="function").toString(),"\n",
"\t","file_dialog_start_handler assigned: ",(typeof(this.settings.file_dialog_start_handler)==="function").toString(),"\n",
"\t","file_queued_handler assigned:       ",(typeof(this.settings.file_queued_handler)==="function").toString(),"\n",
"\t","file_queue_error_handler assigned:  ",(typeof(this.settings.file_queue_error_handler)==="function").toString(),"\n",
"\t","upload_start_handler assigned:      ",(typeof(this.settings.upload_start_handler)==="function").toString(),"\n",
"\t","upload_progress_handler assigned:   ",(typeof(this.settings.upload_progress_handler)==="function").toString(),"\n",
"\t","upload_error_handler assigned:      ",(typeof(this.settings.upload_error_handler)==="function").toString(),"\n",
"\t","upload_success_handler assigned:    ",(typeof(this.settings.upload_success_handler)==="function").toString(),"\n",
"\t","upload_complete_handler assigned:   ",(typeof(this.settings.upload_complete_handler)==="function").toString(),"\n",
"\t","debug_handler assigned:             ",(typeof(this.settings.debug_handler)==="function").toString(),"\n"
].join("")
);};SWFUpload.prototype.addSetting=function(name,value,default_value){if(value==undefined){return(this.settings[name]=default_value);}else{return(this.settings[name]=value);}
};SWFUpload.prototype.getSetting=function(name){if(this.settings[name]!=undefined){return this.settings[name];}
return "";};SWFUpload.prototype.callFlash=function(functionName,withTimeout,argumentArray){withTimeout=!!withTimeout||false;argumentArray=argumentArray||[];var self=this;var callFunction=function(){var movieElement=self.getMovieElement();var returnValue;if(typeof(movieElement[functionName])==="function"){if(argumentArray.length===0){returnValue=movieElement[functionName]();}else if(argumentArray.length===1){returnValue=movieElement[functionName](argumentArray[0]);}else if(argumentArray.length===2){returnValue=movieElement[functionName](argumentArray[0],argumentArray[1]);}else if(argumentArray.length===3){returnValue=movieElement[functionName](argumentArray[0],argumentArray[1],argumentArray[2]);}else{throw "Too many arguments";}
if(returnValue!=undefined&&typeof(returnValue.post)==="object"){returnValue=self.unescapeFilePostParams(returnValue);}
return returnValue;}else{throw "Invalid function name";}
};if(withTimeout){setTimeout(callFunction,0);}else{return callFunction();}
};SWFUpload.prototype.selectFile=function(){this.callFlash("SelectFile");};SWFUpload.prototype.selectFiles=function(){this.callFlash("SelectFiles");};SWFUpload.prototype.startUpload=function(fileID){this.callFlash("StartUpload",false,[fileID]);};SWFUpload.prototype.cancelUpload=function(fileID){this.callFlash("CancelUpload",false,[fileID]);};SWFUpload.prototype.stopUpload=function(){this.callFlash("StopUpload");};SWFUpload.prototype.getStats=function(){return this.callFlash("GetStats");};SWFUpload.prototype.setStats=function(statsObject){this.callFlash("SetStats",false,[statsObject]);};SWFUpload.prototype.setCredentials=function(name,password){this.callFlash("SetCrednetials",false,[name,password]);};SWFUpload.prototype.getFile=function(fileID){if(typeof(fileID)==="number"){return this.callFlash("GetFileByIndex",false,[fileID]);}else{return this.callFlash("GetFile",false,[fileID]);}
};SWFUpload.prototype.addFileParam=function(fileID,name,value){return this.callFlash("AddFileParam",false,[fileID,name,value]);};SWFUpload.prototype.removeFileParam=function(fileID,name){this.callFlash("RemoveFileParam",false,[fileID,name]);};SWFUpload.prototype.setUploadURL=function(url){this.settings.upload_url=url.toString();this.callFlash("SetUploadURL",false,[url]);};SWFUpload.prototype.setPostParams=function(paramsObject){this.settings.post_params=paramsObject;this.callFlash("SetPostParams",false,[paramsObject]);};SWFUpload.prototype.addPostParam=function(name,value){this.settings.post_params[name]=value;this.callFlash("SetPostParams",false,[this.settings.post_params]);};SWFUpload.prototype.removePostParam=function(name){delete this.settings.post_params[name];this.callFlash("SetPostParams",false,[this.settings.post_params]);};SWFUpload.prototype.setFileTypes=function(types,description){this.settings.file_types=types;this.settings.file_types_description=description;this.callFlash("SetFileTypes",false,[types,description]);};SWFUpload.prototype.setFileSizeLimit=function(fileSizeLimit){this.settings.file_size_limit=fileSizeLimit;this.callFlash("SetFileSizeLimit",false,[fileSizeLimit]);};SWFUpload.prototype.setFileUploadLimit=function(fileUploadLimit){this.settings.file_upload_limit=fileUploadLimit;this.callFlash("SetFileUploadLimit",false,[fileUploadLimit]);};SWFUpload.prototype.setFileQueueLimit=function(fileQueueLimit){this.settings.file_queue_limit=fileQueueLimit;this.callFlash("SetFileQueueLimit",false,[fileQueueLimit]);};SWFUpload.prototype.setFilePostName=function(filePostName){this.settings.file_post_name=filePostName;this.callFlash("SetFilePostName",false,[filePostName]);};SWFUpload.prototype.setUseQueryString=function(useQueryString){this.settings.use_query_string=useQueryString;this.callFlash("SetUseQueryString",false,[useQueryString]);};SWFUpload.prototype.setRequeueOnError=function(requeueOnError){this.settings.requeue_on_error=requeueOnError;this.callFlash("SetRequeueOnError",false,[requeueOnError]);};SWFUpload.prototype.setDebugEnabled=function(debugEnabled){this.settings.debug_enabled=debugEnabled;this.callFlash("SetDebugEnabled",false,[debugEnabled]);};SWFUpload.prototype.queueEvent=function(handlerName,argumentArray){if(argumentArray==undefined){argumentArray=[];}else if(!(argumentArray instanceof Array)){argumentArray=[argumentArray];}
var self=this;if(typeof(this.settings[handlerName])==="function"){this.eventQueue.push(function(){this.settings[handlerName].apply(this,argumentArray);});setTimeout(function(){self.executeNextEvent();},0);}else if(this.settings[handlerName]!==null){throw "Event handler "+handlerName+" is unknown or is not a function";}
};SWFUpload.prototype.executeNextEvent=function(){var f=this.eventQueue.shift();f.apply(this);};SWFUpload.prototype.unescapeFilePostParams=function(file){var reg=/[$]([0-9a-f]{4})/i;var unescapedPost={};var uk;for(var k in file.post){if(file.post.hasOwnProperty(k)){uk=k;var match;while((match=reg.exec(uk))!==null){uk=uk.replace(match[0],String.fromCharCode(parseInt("0x"+match[1],16)));}
unescapedPost[uk]=file.post[k];}
}
file.post=unescapedPost;return file;};SWFUpload.prototype.flashReady=function(){var movieElement=this.getMovieElement();if(typeof(movieElement.StartUpload)!=="function"){throw "ExternalInterface methods failed to initialize.";}
this.queueEvent("swfupload_loaded_handler");};SWFUpload.prototype.fileDialogStart=function(){this.queueEvent("file_dialog_start_handler");};SWFUpload.prototype.fileQueued=function(file){file=this.unescapeFilePostParams(file);this.queueEvent("file_queued_handler",file);};SWFUpload.prototype.fileQueueError=function(file,errorCode,message){file=this.unescapeFilePostParams(file);this.queueEvent("file_queue_error_handler",[file,errorCode,message]);};SWFUpload.prototype.fileDialogComplete=function(numFilesSelected,numFilesQueued){this.queueEvent("file_dialog_complete_handler",[numFilesSelected,numFilesQueued]);};SWFUpload.prototype.uploadStart=function(file){file=this.unescapeFilePostParams(file);this.queueEvent("return_upload_start_handler",file);};SWFUpload.prototype.returnUploadStart=function(file){var returnValue;if(typeof(this.settings.upload_start_handler)==="function"){file=this.unescapeFilePostParams(file);returnValue=this.settings.upload_start_handler.call(this,file);}else if(this.settings.upload_start_handler!=undefined){throw "upload_start_handler must be a function";}
if(returnValue===undefined){returnValue=true;}
returnValue=!!returnValue;this.callFlash("ReturnUploadStart",false,[returnValue]);};SWFUpload.prototype.uploadProgress=function(file,bytesComplete,bytesTotal){file=this.unescapeFilePostParams(file);this.queueEvent("upload_progress_handler",[file,bytesComplete,bytesTotal]);};SWFUpload.prototype.uploadError=function(file,errorCode,message){file=this.unescapeFilePostParams(file);this.queueEvent("upload_error_handler",[file,errorCode,message]);};SWFUpload.prototype.uploadSuccess=function(file,serverData){file=this.unescapeFilePostParams(file);this.queueEvent("upload_success_handler",[file,serverData]);};SWFUpload.prototype.uploadComplete=function(file){file=this.unescapeFilePostParams(file);this.queueEvent("upload_complete_handler",file);};SWFUpload.prototype.debug=function(message){this.queueEvent("debug_handler",message);};SWFUpload.prototype.debugMessage=function(message){if(this.settings.debug){var exceptionMessage,exceptionValues=[];if(typeof(message)==="object"&&typeof(message.name)==="string"&&typeof(message.message)==="string"){for(var key in message){if(message.hasOwnProperty(key)){exceptionValues.push(key+": "+message[key]);}
}
exceptionMessage=exceptionValues.join("\n")||"";exceptionValues=exceptionMessage.split("\n");exceptionMessage="EXCEPTION: "+exceptionValues.join("\nEXCEPTION: ");SWFUpload.Console.writeLine(exceptionMessage);}else{SWFUpload.Console.writeLine(message);}
}
};SWFUpload.Console={};SWFUpload.Console.writeLine=function(message){var console,documentForm;try{console=document.getElementById("SWFUpload_Console");if(!console){documentForm=document.createElement("form");document.getElementsByTagName("body")[0].appendChild(documentForm);console=document.createElement("textarea");console.id="SWFUpload_Console";console.style.fontFamily="monospace";console.setAttribute("wrap","off");console.wrap="off";console.style.overflow="auto";console.style.width="700px";console.style.height="350px";console.style.margin="5px";documentForm.appendChild(console);}
console.value+=message+"\n";console.scrollTop=console.scrollHeight-console.clientHeight;}catch(ex){alert("Exception: "+ex.name+" Message: "+ex.message);}
};