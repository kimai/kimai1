/**
 * Initial javascript code for the timesheet extension.
 * 
 */



// set path of extension
var exp_ext_path = "../extensions/ki_expenses/";

var scroller_width;
var drittel;
var exp_w;
var exp_h;

var exp_tss_hook_flag = 0;
var exp_rec_hook_flag = 0;
var exp_stp_hook_flag = 0;
var exp_chk_hook_flag = 0;
var exp_chp_hook_flag = 0;
var exp_che_hook_flag = 0;

$(document).ready(function(){

    var exp_resizeTimer = null;
    $(window).bind('resize', function() {
       if (exp_resizeTimer) clearTimeout(exp_resizeTimer);
       exp_resizeTimer = setTimeout(exp_ext_resize, 500);
    });

    
});
