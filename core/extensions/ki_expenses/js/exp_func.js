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
 * Javascript functions for the timesheet extension are defined here.
 */

/**
 * Called when the extension loaded. Do some initial stuff.
 */
function exp_ext_onload() {
    exp_ext_applyHoverIntent2expRows();
    exp_ext_resize();
    $("#loader").hide();
    lists_visible(true);
}

/**
 * Update the dimension variables to reflect new height and width.
 */
function exp_ext_get_dimensions() {
    scroller_width = 17;
    if (navigator.platform.substr(0,3)=='Mac') {
        scroller_width = 16;
    }

    (kndShrinkMode)?subtableCount=2:subtableCount=3;
    subtableWidth = (pageWidth()-10)/subtableCount-7 ;
    
    exp_w = pageWidth()-24;
    exp_h = pageHeight()-224-headerHeight()-28;
}

/**
 * Hover a row if the mouse is over it for more than half a second.
 */
function exp_ext_applyHoverIntent2expRows() {
    $('#exp tr').hoverIntent({
        sensitivity: 1,
        interval: 500,
        over:
          function() { 
              $('#exp tr').removeClass('hover');
              $(this).addClass('hover');},
        out:
          function() {
              $(this).removeClass('hover');
          }
    });
}

/**
 * The window has been resized, we have to adjust to the new space.
 */
function exp_ext_resize() {
    exp_ext_set_tableWrapperWidths();
    exp_ext_set_heightTop();
}

/**
 * Set width of table and faked table head.
 */
function exp_ext_set_tableWrapperWidths() {
    exp_ext_get_dimensions();
    $("#exp_head,#exp").css("width",exp_w);
    exp_ext_set_TableWidths();
}

/**
 * If the extension is being shrinked so the sublists are shown larger
 * adjust to that.
 */
function exp_ext_set_heightTop() {
    exp_ext_get_dimensions();
    if (!extShrinkMode) {
        $("#exp").css("height", exp_h);
    } else {
        $("#exp").css("height", "70px");
    }
    
    exp_ext_set_TableWidths();
}

/**
 * Set the width of the table.
 */
function exp_ext_set_TableWidths() {
    exp_ext_get_dimensions();
    // set table widths   
    ($("#exp").innerHeight()-$("#exp table").outerHeight()>0)?scr=0:scr=scroller_width; // width of exp table depending on scrollbar or not
    $("#exp table").css("width",exp_w-scr);
    $("div#exp > div > table > tbody > tr > td.refundable").css("width", $("#exp_head > table > tbody > tr > td.refundable").width());  
    // stretch customer column in faked exp table head
    $("#exp_head > table > tbody > tr > td.knd").css("width", $("div#exp > div > table > tbody > tr > td.knd").width());    
    // stretch project column in faked exp table head
    $("#exp_head > table > tbody > tr > td.pct").css("width", $("div#exp > div > table > tbody > tr > td.pct").width());
}

function exp_ext_triggerchange() {
    $('#display_total').html(exp_total);
    if (exp_tss_hook_flag) {
        exp_ext_reload();
        exp_chk_hook_flag = 0;
        exp_chp_hook_flag = 0;
        exp_che_hook_flag = 0;
    }
    if (exp_chk_hook_flag) {
        exp_ext_triggerCHK();
        exp_chp_hook_flag = 0;
        exp_che_hook_flag = 0;
    }
    if (exp_chp_hook_flag) {
        exp_ext_triggerCHP();
    }
    if (exp_che_hook_flag) {
        exp_ext_triggerCHE();
    }
    
    exp_tss_hook_flag = 0;
    exp_rec_hook_flag = 0;
    exp_stp_hook_flag = 0;
    exp_chk_hook_flag = 0;
    exp_chp_hook_flag = 0;
    exp_che_hook_flag = 0;
}

function exp_ext_triggerTSS() {
    if ($('.ki_expenses').css('display') == "block") {
        exp_ext_reload();
    } else {
        exp_tss_hook_flag++;
    }
}

function exp_ext_triggerCHK() {
    if ($('.ki_expenses').css('display') == "block") {
        exp_ext_reload();
    } else {
        exp_chk_hook_flag++;
    }
}

function exp_ext_triggerCHP() {
    if ($('.ki_expenses').css('display') == "block") {
        exp_ext_reload();
    } else {
        exp_chp_hook_flag++;
    }
}

function exp_ext_triggerCHE() {
    if ($('.ki_expenses').css('display') == "block") {
        exp_ext_reload();
    } else {
        exp_che_hook_flag++;
    }
}


// ----------------------------------------------------------------------------------------
// reloads timesheet, customer, project and event tables
//
function exp_ext_reload() {
            $.post(exp_ext_path + "processor.php", { axAction: "reload_exp", axValue: filterUsr.join(":")+'|'+filterKnd.join(":")+'|'+filterPct.join(":"), id: 0,
                first_day: new Date($('#pick_in').val()).getTime()/1000, last_day: new Date($('#pick_out').val()).getTime()/1000 },
                function(data) { 
                    $("#exp").html(data);
                
                    // set exp table width
                    ($("#exp").innerHeight()-$("#exp table").outerHeight() > 0 ) ? scr=0 : scr=scroller_width; // width of exp table depending on scrollbar or not
                    $("#exp table").css("width",exp_w-scr);
                    // stretch refundable column in faked exp table head
                    $("#exp_head > table > tbody > tr > td.refundable").css("width", $("div#exp > div > table > tbody > tr > td.refundable").width());
                    // stretch customer column in faked exp table head
                    $("#exp_head > table > tbody > tr > td.knd").css("width", $("div#exp > div > table > tbody > tr > td.knd").width());
                    // stretch project column in faked exp table head
                    $("#exp_head > table > tbody > tr > td.pct").css("width", $("div#exp > div > table > tbody > tr > td.pct").width());
                    exp_ext_applyHoverIntent2expRows();
                }
            );
}


// ----------------------------------------------------------------------------------------
// delete a timesheet record immediately
//
function exp_quickdelete(id) {
    $('#expEntry'+id+'>td>a').blur();
    
    if (confirmText != undefined) {
      var check = confirm(confirmText);
      if (check == false) return;
    }
    
    $('#expEntry'+id+'>td>a').removeAttr('onClick');
    $('#expEntry'+id+'>td>a.quickdelete>img').attr("src","../skins/standard/grfx/loading13.gif");
    
    $.post(exp_ext_path + "processor.php", { axAction: "quickdelete", axValue: 0, id: id },
        function(data){
            if (data == 1) {
                exp_ext_reload();
            } else {
                alert("~~an error occured!~~")
            }
        }
    );
}

// ----------------------------------------------------------------------------------------
// edit a timesheet record
//
function exp_editRecord(id) {
    floaterShow(exp_ext_path + "floaters.php","add_edit_record",0,id,600,300);
}

// ----------------------------------------------------------------------------------------
// shows comment line for expense entry
//
function exp_comment(id) {
    $('#exp_c'+id).toggle();
    return false;
}
// ----------------------------------------------------------------------------------------
// pastes the current date and time in the outPoint field of the
// change dialog for timesheet entries 
//
//         $tpl->assign('pasteValue', date("d.m.Y - H:i:s",$kga['now']));
//
function exp_pasteNow(value) {
    
    now = new Date();

    H = now.getHours();
    i = now.getMinutes();
    s = now.getSeconds();
    
    if (H<10) H = "0"+H;
    if (i<10) i = "0"+i;
    if (s<10) s = "0"+s;
    
    time  = H + ":" + i + ":" + s;
    
    $("#edit_time").val(time);
}



// ----------------------------------------------------------------------------------------
// check if the input in the add_edit floater is valid and handle it appropriately
//
function exp_add_edit_validate() {
    
    if ($('#add_edit_exp_pct_ID').val() == undefined)
      $('#exp_ext_form_add_edit_record .btn_ok').hide();
    else
      $('#exp_ext_form_add_edit_record .btn_ok').show();
}