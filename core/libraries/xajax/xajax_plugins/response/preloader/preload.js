try {
	if (undefined == xajax.ext)
		xajax.ext = {};
} catch (e) {
}

try {
	if (undefined == xajax.ext.preloader)
		xajax.ext.preloader = {};
} catch (e) {
	alert("Could not create xajax.ext.preloader namespace");
}


xajax.ext.preloader.aScripts = [];
xajax.ext.preloader.aImages = [];
xajax.ext.preloader.aStyles = [];
xajax.ext.preloader.ready = false;

xajax.ext.preloader.run = function() {
	if (!xajax.ext.preloader.ready) 
	{
		window.setTimout(xajax.ext.preloader.run,200);
		return;
	}

	
	var splash = document.createElement("div");
	splash.id = "inhalt";
	document.body.appendChild(splash);
	
	var l =  xajax.ext.preloader.aScripts.length;
	for (i=0;i<l;++i) 
	{
		var command = 
		{
			data : xajax.ext.preloader.aScripts[i],
			onload : function() {alert(aScripts[i]+ " loaded");}
		}
		xajax.js.includeScript(command);
		splash.innerHTML += xajax.ext.preloader.aScripts[i]+"\n<br />\n";
	}
}


window.onload=xajax.ext.preloader.run;
