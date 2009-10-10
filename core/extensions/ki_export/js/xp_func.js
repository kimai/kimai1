/**
 * This file is part of 
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2009 Kimai-Development-Team
 * 
 * Kimai is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; Version 3, 29 June 2007
 * 
 * Kimai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Kimai; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 */

// ============
// XP EXT funcs
// ============


function clicktest() {
	alert("clicked!");
}


function xp_ext_onload() {
    xp_ext_resize();
    $("#loader").hide();
    lists_visible(true);

	$('#xp_ext_select_filter').click(function(){
        xp_ext_select_filter();
    });

	$('#xp_ext_select_location').click(function(){
        xp_ext_select_location();
    });

	$('#xp_ext_select_timeformat').click(function(){
        xp_ext_select_timeformat();
    });

	$('#xp_ext_export_pdf').click(function(){
        floaterShow('../extensions/ki_export/floaters.php','PDF',0,0,600,570);
    });

	$('#xp_ext_export_xls').click(function(){
        floaterShow('../extensions/ki_export/floaters.php','XLS',0,0,600,570);
    });
	$('#xp_ext_export_csv').click(function(){
        floaterShow('../extensions/ki_export/floaters.php','CSV',0,0,600,570);
    });

	$('#xp_ext_print').click(function(){
        floaterShow('../extensions/ki_export/floaters.php','print',0,0,600,570);
    });

	xp_ext_select_filter();
}




function xp_ext_select_filter()
{
	$('#xp_ext_select_filter').addClass("pressed");
	$('#xp_ext_tab_filter').css("display","block");
	
	$('#xp_ext_select_location').removeClass("pressed");
	$('#xp_ext_tab_location').css("display","none");
	$('#xp_ext_select_timeformat').removeClass("pressed");
	$('#xp_ext_tab_timeformat').css("display","none");
	
}

function xp_ext_select_location()
{
	$('#xp_ext_select_location').addClass("pressed");
	$('#xp_ext_tab_location').css("display","block");

	$('#xp_ext_select_filter').removeClass("pressed");
	$('#xp_ext_tab_filter').css("display","none");
	$('#xp_ext_select_timeformat').removeClass("pressed");
	$('#xp_ext_tab_timeformat').css("display","none");
}

function xp_ext_select_timeformat()
{
	$('#xp_ext_select_timeformat').addClass("pressed");
	$('#xp_ext_tab_timeformat').css("display","block");

	$('#xp_ext_select_filter').removeClass("pressed");
	$('#xp_ext_tab_filter').css("display","none");
	$('#xp_ext_select_location').removeClass("pressed");
	$('#xp_ext_tab_location').css("display","none");
	
}

function xp_ext_export_pdf()
{
	
}

function xp_ext_export_xls()
{
	
}

function xp_ext_print()
{
	
}





/////////////////////////////////////////////////////
// mitgebracht von ts_ext:






function xp_ext_get_dimensions() {
    scroller_width = 17;
    if (navigator.platform.substr(0,3)=='Mac') {
        scroller_width = 16;
    }

    (kndShrinkMode)?subtableCount=2:subtableCount=3;
    subtableWidth = (pageWidth()-10)/subtableCount-7 ;
    
    xp_w = pageWidth()-24;
    xp_h = pageHeight()-274-headerHeight()-28;
}



function xp_ext_resize() {
     xp_ext_set_tableWrapperWidths();
     xp_ext_set_heightTop();
}

function xp_ext_set_tableWrapperWidths() {
    xp_ext_get_dimensions();
    // zef: set width of table and faked table head  
    $("#xp_head,#xp").css("width",xp_w);
    xp_ext_set_TableWidths();
}

function xp_ext_set_heightTop() {
    xp_ext_get_dimensions();
    if (!extShrinkMode) {
        $("#xp").css("height", xp_h);
    } else {
        $("#xp").css("height", "20px");
    }
    
    xp_ext_set_TableWidths();
}

function xp_ext_set_TableWidths() {
    xp_ext_get_dimensions();
    // set table widths   

    ($("#xp").innerHeight()-$("#xp table").outerHeight()>0)?scr=0:scr=scroller_width; // width of zef table depending on scrollbar or not
    $("#xp table").css("width",xp_w-scr);

	$("#xp_head > table").css("width", "100%");


    cashWidth = $("div#xp > div > table > tbody > tr > td.rate").width();
	cashWidth += $("div#xp > div > table > tbody > tr > td.dec_time").width();
	cashWidth +=3;
    $("#xp_head > table > tbody > tr > td.cash").css("width", cashWidth);


	headerWidth = $("#xp_head > table > tbody > tr > td.knd").width();
	contentWidth = $("div#xp > div > table > tbody > tr > td.knd").width();
	if (headerWidth>contentWidth) 
	{
		$("div#xp > div > table > tbody > tr > td.knd").css("width", headerWidth);   
	} else {
		$("#xp_head > table > tbody > tr > td.knd").css("width", contentWidth);
	}

	headerWidth = $("#xp_head > table > tbody > tr > td.pct").width();
	contentWidth = $("div#xp > div > table > tbody > tr > td.pct").width();
	if (headerWidth>contentWidth) 
	{
		$("div#xp > div > table > tbody > tr > td.pct").css("width", headerWidth);   
	} else {
		$("#xp_head > table > tbody > tr > td.pct").css("width", contentWidth);
	}

	headerWidth = $("#xp_head > table > tbody > tr > td.evt").width();
	contentWidth = $("div#xp > div > table > tbody > tr > td.evt").width();
	if (headerWidth>contentWidth) 
	{
		$("div#xp > div > table > tbody > tr > td.evt").css("width", headerWidth);   
	} else {
		$("#xp_head > table > tbody > tr > td.evt").css("width", contentWidth);
	}
       




	// headerWidth = $("#xp_head > table > tbody > tr > td.comment").width();
	// contentWidth = $("div#xp > div > table > tbody > tr > td.comment").width();
	// 
	// if ((headerWidth < 250) && (contentWidth < 250))
	// {
	// 	if (headerWidth>contentWidth) 
	// 	{
	// 		$("div#xp > div > table > tbody > tr > td.comment").css("width", headerWidth);   
	// 	} else {
	// 		$("#xp_head > table > tbody > tr > td.comment").css("width", contentWidth);
	// 	}
	// } else {
	// 	$("#xp_head > table > tbody > tr > td.comment").css("width", 250);
	// 	$("div#xp > div > table > tbody > tr > td.comment").css("width", 250);
	// }
	// 
	// 
	
       



 

    
    
    // headerWidth = $("#xp_head > table > tbody > tr > td.location").width();
    // contentWidth = $("div#xp > div > table > tbody > tr > td.location").width();
    // $("#xp_head > table > tbody > tr > td.location").css("width", Math.max(headerWidth,contentWidth));
    // $("div#xp > div > table > tbody > tr > td.location").css("width", Math.max(headerWidth,contentWidth));
    // 
    // headerWidth = $("#xp_head > table > tbody > tr > td.trackingnr").width();
    // contentWidth = $("div#xp > div > table > tbody > tr > td.trackingnr").width();
    // $("#xp_head > table > tbody > tr > td.trackingnr").css("width", Math.max(headerWidth,contentWidth));
    // $("div#xp > div > table > tbody > tr > td.trackingnr").css("width", Math.max(headerWidth,contentWidth));
    // 
    // headerWidth = $("#xp_head > table > tbody > tr > td.user").width();
    // contentWidth = $("div#xp > div > table > tbody > tr > td.user").width();
    // $("#xp_head > table > tbody > tr > td.user").css("width", Math.max(headerWidth,contentWidth));
    // $("div#xp > div > table > tbody > tr > td.user").css("width", Math.max(headerWidth,contentWidth));
    
}

function xp_ext_triggerchange() {
    if (xp_tss_hook_flag) {
        xp_ext_reload();
        xp_chk_hook_flag = 0;
        xp_chp_hook_flag = 0;
        xp_che_hook_flag = 0;
    }
    if (xp_chk_hook_flag) {
        xp_ext_triggerCHK();
        xp_chp_hook_flag = 0;
        xp_che_hook_flag = 0;
    }
    if (xp_chp_hook_flag) {
        xp_ext_triggerCHP();
    }
    if (xp_che_hook_flag) {
        xp_ext_triggerCHE();
    }
    
    xp_tss_hook_flag = 0;
    xp_rec_hook_flag = 0;
    xp_stp_hook_flag = 0;
    xp_chk_hook_flag = 0;
    xp_chp_hook_flag = 0;
    xp_che_hook_flag = 0;
    xp_ext_reload();
}

function xp_ext_triggerTSS() {
    if ($('.ki_export').css('display') == "block") {
        xp_ext_reload();
    } else {
        xp_tss_hook_flag++;
    }
}


function xp_ext_triggerCHK() {
    if ($('.ki_export').css('display') == "block") {
        xp_ext_reload();
    } else {
        xp_chk_hook_flag++;
    }
}

function xp_ext_triggerCHP() {
    if ($('.ki_export').css('display') == "block") {
        xp_ext_reload();
    } else {
        xp_chp_hook_flag++;
    }
}

function xp_ext_triggerCHE() {
    if ($('.ki_export').css('display') == "block") {
        xp_ext_reload();
    } else {
        xp_che_hook_flag++;
    }
}



// ----------------------------------------------------------------------------------------
// reloads timesheet, customer, project and event tables
//
function xp_ext_reload() {
            $.post(xp_ext_path + "processor.php", { axAction: "reload", axValue: filterUsr.join(":")+'|'+filterKnd.join(":")+'|'+filterPct.join(":"), id: 0,timeformat: $("#xp_ext_timeformat").val(), dateformat: $("#xp_ext_dateformat").val(), default_location: $("#xp_ext_default_location").val() },
                function(data) { 
                    $("#xp").html(data);
                
                    // set zef table width
                    ($("#xp").innerHeight()-$("#xp table").outerHeight() > 0 ) ? scr=0 : scr=scroller_width; // width of zef table depending on scrollbar or not
                    $("#xp table").css("width",xp_w-scr);
                    // stretch customer column in faked zef table head
                    $("#xp_head > table > tbody > tr > td.knd").css("width", $("div#xp > div > table > tbody > tr > td.knd").width());
                    // stretch project column in faked zef table head
                    $("#xp_head > table > tbody > tr > td.pct").css("width", $("div#xp > div > table > tbody > tr > td.pct").width());
                xp_ext_resize();
                }
            );
}



function xp_toggle_column(name) {
  if ($("#xp_head > table > tbody > tr > td."+name).hasClass('disabled')) {
    $("#xp_head > table > tbody > tr > td."+name).removeClass('disabled');
    $("div#xp > div > table > tbody > tr > td."+name).removeClass('disabled');
  }
  else {
    $("#xp_head > table > tbody > tr > td."+name).addClass('disabled');
    $("div#xp > div > table > tbody > tr > td."+name).addClass('disabled');
  }
}

function xp_toggle_cleared(id) {
  path = "#xp"+id+">td.cleared>a";
  if ($(path).hasClass("is_cleared")) {
    returnfunction = new Function("data","if (data!=1) return;\
                    $('"+path+"').removeClass('is_cleared');\
                    $('"+path+"').addClass('isnt_cleared');");
    $.post(xp_ext_path + "processor.php", { axAction: "set_cleared", axValue: 0, id: id },
                returnfunction
            );
    
  }
  else {
    returnfunction = new Function("data","if (data!=1) return;\
                    $('"+path+"').removeClass('isnt_cleared');\
                    $('"+path+"').addClass('is_cleared');");
    $.post(xp_ext_path + "processor.php", { axAction: "set_cleared", axValue: 1, id: id },
                returnfunction
            );
  }
  $(path).blur();
}
