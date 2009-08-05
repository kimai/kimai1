
installtabManager=function(){var xjxReady=false;try{if(xajax)
xjxReady=true;}
catch(e){}
if(false==xjxReady){setTimeout('installtabManager();',1000);return;}
try{if(undefined==xajax.ext.tabManager)
xajax.ext.tabManager={};xajax.ext.tabManager.instances={};}
catch(e){xajax.ext={};xajax.ext.tabManager={};xajax.ext.tabManager.instances={};}
xajax.ext.tabPanel=function(config){this.config=config;this._parent=config._parent;this.id=config.id;var element=this;this.events={};this.tabWidth=0;if('undefined'!=typeof config.closeable){this.closeable=config.closeable;}
else{this.closeable=true;}
var parPanel=this._parent.tabPanel;this.tab_item=document.createElement('div');this.tab_item.id="tabPanel_"+this.id;this.tab_item.className="tabPanelItem";this.tab_item.onclick=function(){element._parent.show(element.id);return false;};var tab_item_left=document.createElement('div');tab_item_left.className='tabLeft';tab_item_left.innerHTML='&nbsp;';this.tab_item.appendChild(tab_item_left);var tab_title=document.createElement('div');tab_title.id="tabPanelTitel_"+this.id;tab_title.innerHTML=config.title;tab_title.className='title';this.tab_title=tab_title;this.tab_item.appendChild(tab_title);if(this.closeable){var foo=this;var tab_close=document.createElement('div');tab_close.id="tabPanelClose_"+this.id;tab_close.innerHTML="&nbsp;";tab_close.className='close';tab_close.onclick=function(){foo.fire("close");element._parent.closePanel(element.id);return false;}
this.tab_item.appendChild(tab_close);}
var tab_item_right=document.createElement('div');tab_item_right.className='tabRight';tab_item_right.innerHTML='&nbsp;';this.tab_item.appendChild(tab_item_right);var tab_clear=document.createElement('div');tab_clear.style.clear='both';this.tab_item.appendChild(tab_clear);var tmp_width=$(parPanel).outerWidth();parPanel.appendChild(this.tab_item);var parContent=this._parent.tabContent;this.tab_content=document.createElement('div');this.tab_content.id="tabContent_"+this.id;this.tab_content.className="tabPanelContent";this.tab_content.innerHTML=config.content;this.tab_content.style.display='none';parContent.appendChild(this.tab_content);this.tabWidth=$(this.tab_item).outerWidth()+2;this.getWidth=function(){this.tabWidth=$(this.tab_item).outerWidth()+2;return this.tabWidth;};this.show=function(){this.tab_item.className='tabPanelItemActive';this.tab_content.style.display='block';this.fire("show");};this.hide=function(){this.tab_item.className='tabPanelItem';this.tab_content.style.display='none';this.fire("hide");};this.setTitle=function(title){this.tab_title.innerHTML=title;};this.setContent=function(content){this.tab_content.innerHTML=content;};this.destroy=function(){this.fire("destroy");parPanel.removeChild(this.tab_item);parContent.removeChild(this.tab_content);};this.fire=function(EventName){if('object'==typeof this.events[EventName]){for(a in this.events[EventName])
this.events[EventName][a]();}
}
this.on=function(EventName,EventFunction,EventFunctionId){if('object'!=typeof this.events[EventName])
this.events[EventName]=new Object();if('undefined'==typeof this.events[EventName][EventFunctionId])
this.events[EventName][EventFunctionId]=EventFunction;}
};xajax.ext.tabManager.tabBar=function(config){this.config=config;this.tabBar=xajax.$(config.tabPanel);this.tabContent=xajax.$(config.tabContent);this.tabs={};this.activeTab=null;this.lastActiveTab=null;this.interval=null;this.maxWidth=$(this.tabBar).innerWidth();this.tabWidth=0;this.tabHeight=$(this.tabBar).innerHeight();var leftHandle=document.createElement('div');leftHandle.className="leftHandle";var rightHandle=document.createElement('div');rightHandle.className="rightHandle";var foo=this;leftHandle.onmouseover=function(){foo.scroll(4);}
rightHandle.onmouseover=function(){foo.scroll(-4);}
leftHandle.onmouseout=function(){foo.scrollStop();}
rightHandle.onmouseout=function(){foo.scrollStop();}
leftHandle.style.display='none';rightHandle.style.display='none';this.leftHandle=leftHandle;this.rightHandle=rightHandle;var tabContainer=document.createElement('div');tabContainer.id="tabManager_Container_"+config.tabPanel;tabContainer.style.position="absolute";tabContainer.style.top="0px";tabContainer.style.left="0px";tabContainer.style.right="0px";tabContainer.style.height=this.tabHeight+"px";tabContainer.style.overflow="hidden";var tabPanel=document.createElement('div');tabPanel.style.position="absolute";tabPanel.style.height=this.tabHeight+"px";this.tabBar.appendChild(leftHandle);this.tabBar.appendChild(tabContainer);this.tabBar.appendChild(rightHandle);tabContainer.appendChild(tabPanel);this.tabPanel=tabPanel;this.tabContainer=tabContainer;if('undefined'!=typeof config.cssClass){this.tabPanel.className=this.tabBar.className+" "+config.cssClass;}
this.addPanel=function(config){if('undefined'!=typeof this.tabs[config.id]){if(config.hide)
return;this.tabs[config.id].fire("destroy");this.show(config.id);return;};config._parent=this;this.tabs[config.id]=new xajax.ext.tabPanel(config);if(true!=config.hide)
this.show(config.id);this.tabWidth+=this.tabs[config.id].getWidth();if(this.tabWidth > this.maxWidth){this.leftHandle.style.display='block';this.rightHandle.style.display='block';var lspace=$(this.leftHandle).outerWidth()+1;var rspace=$(this.rightHandle).outerWidth()+1;this.tabContainer.style.left=lspace+"px";this.tabContainer.style.right=rspace+"px";}
};this.show=function(id){if('undefined'==typeof this.tabs[id])
return;if(null!=this.activeTab)
this.activeTab.hide();this.tabs[id].show();this.lastActiveTab=this.activeTab;this.activeTab=this.tabs[id];var pos=$('#tabPanel_'+id).position();var elm_left=pos.left;var elm_max=pos.left+parseInt(this.tabs[id].tabWidth);var container_width=$(this.tabContainer).innerWidth();var panel_pos=$(this.tabPanel).position();var scroll_left=panel_pos.left;var visible_left=0-scroll_left;var visible_right=visible_left+container_width;if(elm_left < visible_left){$(this.tabPanel).animate({left:(0-elm_left)+"px"
});}
else if(elm_max > visible_right){$(this.tabPanel).animate({left:(container_width-elm_max-22)+"px"
});}
};this.setContent=function(id,content){if('undefined'!=typeof this.tabs[id])
return;this.tabs[id].setContent(content);};this.setTitle=function(id,title){if('undefined'==typeof this.tabs[id])
return;this.tabs[id].setTitle(title);};this.updateInnerWidth=function(){var tmpwidth=0;for(a in this.tabs){tmpwidth+=this.tabs[a].getWidth();}
this.tabWidth=tmpwidth+22;};this.closePanel=function(id){if('undefined'==typeof this.tabs[id])
return;if(null!=this.lastActiveTab){if(this.activeTab.id==id)
this.show(this.lastActiveTab.id);}
this.tabs[id].destroy();delete(this.tabs[id]);this.updateInnerWidth();if(this.tabWidth < this.maxWidth){this.leftHandle.style.display='none';this.rightHandle.style.display='none';this.tabContainer.style.left="0px";this.tabContainer.style.right="0px";this.tabPanel.style.left="0px";}
};this.on=function(EventName,id,EventFunction,EventFunctionId){this.tabs[id].on(EventName,EventFunction,EventFunctionId);}
this.scroll=function(value){if(null!=this.interval)
return;var tab=this;var i=value;this.interval=setInterval(function(){var lspace=$(tab.leftHandle).innerWidth()+1;var rspace=$(tab.rightHandle).innerWidth()+1;var sLeft=tab.tabPanel.style.left;var iLeft=sLeft.replace("px","");if(""==iLeft)
iLeft=0;iLeft=parseInt(iLeft);iLeft+=i;var iMin=tab.maxWidth-tab.tabWidth-rspace-lspace;if((iLeft > 0)||((iLeft < iMin)&&(i < 0))){tab.scrollStop();return;}
tab.tabPanel.style.left=iLeft+"px";},25);}
this.scrollStop=function(){if(null==this.interval)
return;clearInterval(this.interval);this.interval=null;}
this.destroy=function(){for(a in this.tabs){this.tabs[a].destroy();delete(this.tabs[a]);}
};};xajax.ext.tabManager.create=function(id,config){if("undefined"==typeof xajax.ext.tabManager.instances[id]){try{xajax.ext.tabManager.instances[id]=new xajax.ext.tabManager.tabBar(config);}
catch(ex){}
}
}
xajax.ext.tabManager.addPanel=function(id,config){try{xajax.ext.tabManager.instances[id].addPanel(config);}
catch(ex){}
}
xajax.ext.tabManager.showPanel=function(id,sPanel){try{xajax.ext.tabManager.instances[id].show(sPanel);}
catch(ex){}
}
xajax.ext.tabManager.setTitle=function(id,sPanel,title){try{xajax.ext.tabManager.instances[id].setTitle(sPanel,title);}
catch(ex){}
}
xajax.ext.tabManager.on=function(id,sEventName,sTarget,sEventFunc,sEventId){xajax.ext.tabManager.instances[id].on(sEventName,sTarget,sEventFunc,sEventId);}
xajax.ext.tabManager.closePanel=function(id,sPanel){xajax.ext.tabManager.instances[id].closePanel(sPanel);}
xajax.ext.tabManager.destroy=function(id){xajax.ext.tabManager.instances[id].destroy();delete(xajax.ext.tabManager.instances[id]);}
xajax.command.handler.register('tm_create',function(args){args.cmdFullName='ext.tabManager.create';xajax.ext.tabManager.create(args.id,args.data);return true;});xajax.command.handler.register('tm_at',function(args){args.cmdFullName='ext.tabManager.addPanel';xajax.ext.tabManager.addPanel(args.id,args.data);return true;});xajax.command.handler.register('tm_on',function(args){try{args.cmdFullName='ext.tabManager.on';eval("var sEvent = "+args.data.e+";");xajax.ext.tabManager.on(args.id,args.data.n,args.data.p,sEvent,args.data.key);}
catch(ex){}
return true;});xajax.command.handler.register('tm_cl',function(args){try{args.cmdFullName='ext.tabManager.closePanel';xajax.ext.tabManager.closePanel(args.id,args.data);}
catch(ex){debugObj(ex);}
return true;});xajax.command.handler.register('tm_sp',function(args){args.cmdFullName='ext.tabManager.showPanel';xajax.ext.tabManager.showPanel(args.id,args.data);return true;});xajax.command.handler.register('tm_st',function(args){try{args.cmdFullName='ext.tabManager.setTitle';xajax.ext.tabManager.setTitle(args.id,args.data.panel,args.data.title);}
catch(ex){}
return true;});xajax.command.handler.register('tm_de',function(args){try{args.cmdFullName='ext.tabManager.destroy';xajax.ext.tabManager.destroy(args.id);}
catch(ex){debugObj(ex);}
return true;});}
installtabManager();