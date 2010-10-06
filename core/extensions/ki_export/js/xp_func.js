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
 * along with Kimai; If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Javascript functions used in the export extension.
 */



/**
 * The extension was loaded, do some setup stuff.
 */
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
        this.blur();
        floaterShow('../extensions/ki_export/floaters.php','PDF',0,0,600,570);
    });

	$('#xp_ext_export_xls').click(function(){
        this.blur();
        floaterShow('../extensions/ki_export/floaters.php','XLS',0,0,600,570);
    });
	$('#xp_ext_export_csv').click(function(){
        this.blur();
        floaterShow('../extensions/ki_export/floaters.php','CSV',0,0,600,570);
    });

	$('#xp_ext_print').click(function(){
        this.blur();
        floaterShow('../extensions/ki_export/floaters.php','print',0,0,600,570);
    });

	$('.helpfloater').click(function(){
        this.blur();
        floaterShow('../extensions/ki_export/floaters.php','help_timeformat',0,0,600,570);
    });

	xp_ext_select_filter();
    xp_ext_reload();
}



/**
 * Show the tab which allows filtering.
 */
function xp_ext_select_filter()
{
	$('#xp_ext_select_filter').addClass("pressed");
	$('#xp_ext_tab_filter').css("display","block");
	
	$('#xp_ext_select_location').removeClass("pressed");
	$('#xp_ext_tab_location').css("display","none");
	$('#xp_ext_select_timeformat').removeClass("pressed");
	$('#xp_ext_tab_timeformat').css("display","none");
	
}

/**
 * Show the tab via which the default location can be set.
 */
function xp_ext_select_location()
{
	$('#xp_ext_select_location').addClass("pressed");
	$('#xp_ext_tab_location').css("display","block");

	$('#xp_ext_select_filter').removeClass("pressed");
	$('#xp_ext_tab_filter').css("display","none");
	$('#xp_ext_select_timeformat').removeClass("pressed");
	$('#xp_ext_tab_timeformat').css("display","none");
}

/**
 * Show the tab which lets the user define the date and time format.
 */
function xp_ext_select_timeformat()
{
	$('#xp_ext_select_timeformat').addClass("pressed");
	$('#xp_ext_tab_timeformat').css("display","block");

	$('#xp_ext_select_filter').removeClass("pressed");
	$('#xp_ext_tab_filter').css("display","none");
	$('#xp_ext_select_location').removeClass("pressed");
	$('#xp_ext_tab_location').css("display","none");
	
}





/////////////////////////////////////////////////////
// mitgebracht von ts_ext:


/**
 * Update the dimension variables to reflect new height and width.
 */
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



/**
 * The window has been resized, we have to adjust to the new space.
 */
function xp_ext_resize() {
     xp_ext_set_tableWrapperWidths();
     xp_ext_set_heightTop();
}

/**
 * Set width of table and faked table head.
 */
function xp_ext_set_tableWrapperWidths() {
    xp_ext_get_dimensions();
    // zef: set width of table and faked table head  
    $("#xp_head,#xp").css("width",xp_w);
    xp_ext_set_TableWidths();
}

/**
 * If the extension is being shrinked so the sublists are shown larger
 * adjust to that.
 */
function xp_ext_set_heightTop() {
    xp_ext_get_dimensions();
    if (!extShrinkMode) {
        $("#xp").css("height", xp_h);
    } else {
        $("#xp").css("height", "20px");
    }
    
    xp_ext_set_TableWidths();
}

/**
 * Set the width of the table.
 */
function xp_ext_set_TableWidths() {
    xp_ext_get_dimensions();
    // set table widths   

    ($("#xp").innerHeight()-$("#xp table").outerHeight()>0)?scr=0:scr=scroller_width; // width of zef table depending on scrollbar or not
    $("#xp table").css("width",xp_w-scr);

	$("#xp_head > table").css("width", "100%");


  $("#xp_head > table > tbody > tr > td.time").css("width", $("div#xp > div > table > tbody > tr > td.time").width());
  $("#xp_head > table > tbody > tr > td.knd").css("width", $("div#xp > div > table > tbody > tr > td.knd").width());
  $("#xp_head > table > tbody > tr > td.pct").css("width", $("div#xp > div > table > tbody > tr > td.pct").width());
  $("#xp_head > table > tbody > tr > td.evt").css("width", $("div#xp > div > table > tbody > tr > td.evt").width());
  $("#xp_head > table > tbody > tr > td.moreinfo").css("width",
      $("div#xp > div > table > tbody > tr > td.comment").width()+
      $("div#xp > div > table > tbody > tr > td.location").width()+
      $("div#xp > div > table > tbody > tr > td.trackingnr").width()
  );
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
    if ($('.ki_export').html() != '')
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
  
  // don't reload if extension is not loaded  
  if ($('.ki_export').html() =='')
      return;

            $.post(xp_ext_path + "processor.php", { axAction: "reload", axValue: filterUsr.join(":")+'|'+filterKnd.join(":")+'|'+filterPct.join(":")+'|'+filterEvt.join(":"),
                  id: 0, timeformat: $("#xp_ext_timeformat").val(), dateformat: $("#xp_ext_dateformat").val(), default_location: $("#xp_ext_default_location").val(),
                  filter_cleared:$('#xp_ext_tab_filter_cleared').attr('value'),
                  filter_refundable:$('#xp_ext_tab_filter_refundable').attr('value'),
                  filter_type:$('#xp_ext_tab_filter_type').attr('value'),
                first_day: new Date($('#pick_in').val()).getTime()/1000, last_day: new Date($('#pick_out').val()).getTime()/1000  },
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



/**
 * Toggle the enabled state of a column.
 */
function xp_toggle_column(name) {
  if ($("#xp_head > table > tbody > tr ."+name).hasClass('disabled')) {
    returnfunction = new Function("data","if (data!=1) return;\
                    $('#xp_head > table > tbody > tr ."+name+"').removeClass('disabled');\
                    $('div#xp > div > table > tbody > tr > td."+name+"').removeClass('disabled'); ");
    $.post(xp_ext_path + "processor.php", { axAction: "toggle_header", axValue: name },
                returnfunction
            );
    
  }
  else {
    returnfunction = new Function("data","if (data!=1) return;\
                    $('#xp_head > table > tbody > tr ."+name+"').addClass('disabled'); \
                    $('div#xp > div > table > tbody > tr > td."+name+"').addClass('disabled'); ");
    $.post(xp_ext_path + "processor.php", { axAction: "toggle_header", axValue: name },
                returnfunction
            );
  }
}

/**
 * Toggle the cleared state of an entry.
 */
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

/**
 * Create a list of enabled columns.
 */
function xp_enabled_columns() {
  columns = new Array('date','from','to','time','dec_time','rate','wage','knd','pct','action','comment','location','trackingnr','user','cleared');
  columnsString = '';
  firstColumn = true;
  $(columns).each(function () {
    if (!$('#xp_head .'+this).hasClass('disabled')) {
    columnsString += (firstColumn?'':'|') + this;
    firstColumn = false;
    }
  });
  return columnsString;
}