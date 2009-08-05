/****************************************************************************************************
 *
 *      IMPORTANT NOTE:
 *      
 *      If you like a function to run when your extension is loaded into the extension-frame
 *      you CAN'T use the methods you'd normally use when the dom is ready build! So something like:
 *      
 *
 *      $(document).ready(function(){
 *        [do something stupid...]
 *      });
 *      
 *      
 *      ... won't work here. 
 *     This is because the dom is already finished loading BEFORE the extensions are hooked in!
 *     When You want JavaScript or jQuery to do something when your extension is loaded into its
 *     place in the *existing* DOM, you have to put a function-call *into* the content you load.
 *     You can also use the jQuery ready-funktion there!
 *
 ****************************************************************************************************/

 // set path of extension
 var demo_ext_path = "../extensions/ki_demoextension/";

 $(document).ready(function(){

 	var demo_ext_resizeTimer = null;

 	$(window).bind('resize', function() {
 	   if (demo_ext_resizeTimer) clearTimeout(demo_ext_resizeTimer);
 	   demo_ext_resizeTimer = setTimeout(demo_ext_resize, 500);
 	});

 });

