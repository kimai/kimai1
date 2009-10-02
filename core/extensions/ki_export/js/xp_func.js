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
        clicktest();
    });

	$('#xp_ext_export_xls').click(function(){
        clicktest();
    });

	$('#xp_ext_print').click(function(){
        clicktest();
    });

	xp_ext_select_timeformat();
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
    
    zef_w = pageWidth()-24;
    zef_h = pageHeight()-224-headerHeight()-28;
}



function xp_ext_resize() {
    // xp_ext_set_tableWrapperWidths();
    // xp_ext_set_heightTop();
}

function xp_ext_set_tableWrapperWidths() {
    xp_ext_get_dimensions();
    // zef: set width of table and faked table head  
    $("#zef_head,#zef").css("width",zef_w);
    xp_ext_set_TableWidths();
}

function xp_ext_set_heightTop() {
    xp_ext_get_dimensions();
    if (!extShrinkMode) {
        $("#zef").css("height", zef_h);
    } else {
        $("#zef").css("height", "70px");
    }
    
    xp_ext_set_TableWidths();
}

function xp_ext_set_TableWidths() {
    xp_ext_get_dimensions();
    // set table widths   
    ($("#zef").innerHeight()-$("#zef table").outerHeight()>0)?scr=0:scr=scroller_width; // width of zef table depending on scrollbar or not
    $("#zef table").css("width",zef_w-scr);
    // stretch customer column in faked zef table head
    $("#zef_head > table > tbody > tr > td.knd").css("width", $("div#zef > div > table > tbody > tr > td.knd").width());    
    // stretch project column in faked zef table head
    $("#zef_head > table > tbody > tr > td.pct").css("width", $("div#zef > div > table > tbody > tr > td.pct").width());
}

function xp_ext_triggerchange() {
    if (ts_tss_hook_flag) {
        xp_ext_reload();
        xp_chk_hook_flag = 0;
        xp_chp_hook_flag = 0;
        xp_che_hook_flag = 0;
    }
    if (ts_chk_hook_flag) {
        xp_ext_triggerCHK();
        xp_chp_hook_flag = 0;
        xp_che_hook_flag = 0;
    }
    if (ts_chp_hook_flag) {
        xp_ext_triggerCHP();
    }
    if (ts_che_hook_flag) {
        xp_ext_triggerCHE();
    }
    
    xp_tss_hook_flag = 0;
    xp_rec_hook_flag = 0;
    xp_stp_hook_flag = 0;
    xp_chk_hook_flag = 0;
    xp_chp_hook_flag = 0;
    xp_che_hook_flag = 0;
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
            $.post(xp_ext_path + "processor.php", { axAction: "reload_zef", axValue: filterUsr.join(":")+'|'+filterKnd.join(":")+'|'+filterPct.join(":"), id: 0 },
                function(data) { 
                    $("#zef").html(data);
                
                    // set zef table width
                    ($("#zef").innerHeight()-$("#zef table").outerHeight() > 0 ) ? scr=0 : scr=scroller_width; // width of zef table depending on scrollbar or not
                    $("#zef table").css("width",zef_w-scr);
                    // stretch customer column in faked zef table head
                    $("#zef_head > table > tbody > tr > td.knd").css("width", $("div#zef > div > table > tbody > tr > td.knd").width());
                    // stretch project column in faked zef table head
                    $("#zef_head > table > tbody > tr > td.pct").css("width", $("div#zef > div > table > tbody > tr > td.pct").width());
                }
            );
}



