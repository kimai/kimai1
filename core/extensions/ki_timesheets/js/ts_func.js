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
 * Javascript functions used in the timesheet extension.
 */

/**
 * Called when the extension loaded. Do some initial stuff.
 */
function ts_ext_onload() {
    ts_ext_applyHoverIntent2zefRows();
    ts_ext_resize();
    $("#loader").hide();
    lists_visible(true);
}

/**
 * Update the dimension variables to reflect new height and width.
 */
function ts_ext_get_dimensions() {
    scroller_width = 17;
    if (navigator.platform.substr(0,3)=='Mac') {
        scroller_width = 16;
    }

    (kndShrinkMode)?subtableCount=2:subtableCount=3;
    subtableWidth = (pageWidth()-10)/subtableCount-7 ;
    
    zef_w = pageWidth()-24;
    zef_h = pageHeight()-224-headerHeight()-28;
}

/**
 * Hover a row if the mouse is over it for more than half a second.
 */
function ts_ext_applyHoverIntent2zefRows() {
    $('#zef tr').hoverIntent({
        sensitivity: 1,
        interval: 500,
        over:
          function() { 
              $('#zef tr').removeClass('hover');
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
function ts_ext_resize() {
    ts_ext_set_tableWrapperWidths();
    ts_ext_set_heightTop();
}

/**
 * Set width of table and faked table head.
 */
function ts_ext_set_tableWrapperWidths() {
    ts_ext_get_dimensions();
    // zef: set width of table and faked table head  
    $("#zef_head,#zef").css("width",zef_w);
    ts_ext_set_TableWidths();
}

/**
 * If the extension is being shrinked so the sublists are shown larger
 * adjust to that.
 */
function ts_ext_set_heightTop() {
    ts_ext_get_dimensions();
    if (!extShrinkMode) {
        $("#zef").css("height", zef_h);
    } else {
        $("#zef").css("height", "70px");
    }
    
    ts_ext_set_TableWidths();
}

/**
 * Set the width of the table.
 */
function ts_ext_set_TableWidths() {
    ts_ext_get_dimensions();
    // set table widths   
    ($("#zef").innerHeight()-$("#zef table").outerHeight()>0)?scr=0:scr=scroller_width; // width of zef table depending on scrollbar or not
    $("#zef table").css("width",zef_w-scr);
    // stretch duration column in faked zef table head
    $("#zef_head > table > tbody > tr > td.time").css("width", $("div#zef > div > table > tbody > tr > td.time").width());    
    // stretch customer column in faked zef table head
    $("#zef_head > table > tbody > tr > td.knd").css("width", $("div#zef > div > table > tbody > tr > td.knd").width());    
    // stretch project column in faked zef table head
    $("#zef_head > table > tbody > tr > td.pct").css("width", $("div#zef > div > table > tbody > tr > td.pct").width());
    // stretch event column in faked zef table head
    $("#zef_head > table > tbody > tr > td.evt").css("width", $("div#zef > div > table > tbody > tr > td.evt").width());
}

function ts_ext_triggerchange() {
    $('#display_total').html(ts_total);
    if (ts_tss_hook_flag) {
        ts_ext_reload();
        ts_chk_hook_flag = 0;
        ts_chp_hook_flag = 0;
        ts_che_hook_flag = 0;
    }
    if (ts_chk_hook_flag) {
        ts_ext_triggerCHK();
        ts_chp_hook_flag = 0;
        ts_che_hook_flag = 0;
    }
    if (ts_chp_hook_flag) {
        ts_ext_triggerCHP();
    }
    if (ts_che_hook_flag) {
        ts_ext_triggerCHE();
    }
    
    ts_tss_hook_flag = 0;
    ts_rec_hook_flag = 0;
    ts_stp_hook_flag = 0;
    ts_chk_hook_flag = 0;
    ts_chp_hook_flag = 0;
    ts_che_hook_flag = 0;
}

function ts_ext_triggerTSS() {
    if ($('.ki_timesheet').css('display') == "block") {
        ts_ext_reload();
    } else {
        ts_tss_hook_flag++;
    }
}
function ts_ext_triggerCHK() {
    if ($('.ki_timesheet').css('display') == "block") {
        ts_ext_reload();
    } else {
        ts_chk_hook_flag++;
    }
}

function ts_ext_triggerCHP() {
    if ($('.ki_timesheet').css('display') == "block") {
        ts_ext_reload();
    } else {
        ts_chp_hook_flag++;
    }
}

function ts_ext_triggerCHE() {
    if ($('.ki_timesheet').css('display') == "block") {
        ts_ext_reload();
    } else {
        ts_che_hook_flag++;
    }
}


// ----------------------------------------------------------------------------------------
// reloads timesheet, customer, project and event tables
//
function ts_ext_reload() {
            $.post(ts_ext_path + "processor.php", { axAction: "reload_zef", axValue: filterUsr.join(":")+'|'+filterKnd.join(":")+'|'+filterPct.join(":")+'|'+filterEvt.join(":"), id: 0,
                first_day: new Date($('#pick_in').val()).getTime()/1000, last_day: new Date($('#pick_out').val()).getTime()/1000  },
                function(data) { 
                    $("#zef").html(data);
                
                    ts_ext_set_TableWidths()
                    ts_ext_applyHoverIntent2zefRows();
                }
            );
}


// ----------------------------------------------------------------------------------------
// reloads timesheet, customer, project and event tables
//
function ts_ext_reload_evt(pct,noUpdateRate) {
  var selected_evt = $('#add_edit_zef_evt_ID').val();
            $.post(ts_ext_path + "processor.php", { axAction: "reload_evt_options", axValue: 0, id: 0, pct:pct },
                function(data) { 
                    $("#add_edit_zef_evt_ID").html(data);
                    $("#add_edit_zef_evt_ID").val(selected_evt);
                    if (noUpdateRate == undefined)
		      getBestRate();
                    ts_add_edit_validate();
                }
            );
}

// ----------------------------------------------------------------------------------------
// this function is attached to the little green arrows in front of each timesheet record
// and starts recording that event anew
//
function ts_ext_recordAgain(pct,evt,id) {
    $('#zefEntry'+id+'>td>a').blur();

    if (recstate) {
        stopRecord();
    }

    $('#zefEntry'+id+'>td>a.recordAgain>img').attr("src","../skins/"+skin+"/grfx/loading13.gif");
    hour=0;min=0;sec=0;
    now = Math.floor(((new Date()).getTime())/1000);
    offset = now;
    startsec = 0;
    recstate=1;
    show_stopwatch();
    $('#zefEntry'+id+'>td>a').removeAttr('onClick');
 
    $.post(ts_ext_path + "processor.php", { axAction: "record", axValue: 0, id: id },
        function(data) {
                eval(data);
                ts_ext_reload();
                $("#stopwatch_edit_comment").show();
                buzzer_preselect('pct',pct,pct_name,knd,knd_name,false);
                buzzer_preselect('evt',evt,evt_name,0,0,false);
                $("#ticker_knd").html(knd_name);
                $("#ticker_pct").html(pct_name);
                $("#ticker_evt").html(evt_name);
        }
    );
}


// ----------------------------------------------------------------------------------------
// this function is attached to the little green arrows in front of each timesheet record
// and starts recording that event anew
//
function ts_ext_stopRecord(id) {
    recstate=0;
    ticktack_off();
    show_selectors();
    if (id) {
        $('#zefEntry'+id+'>td').css( "background-color", "#F00" );
        $('#zefEntry'+id+'>td>a.stop>img').attr("src","../skins/"+skin+"/grfx/loading13_red.gif");     
        $('#zefEntry'+id+'>td>a').blur();
        $('#zefEntry'+id+'>td>a').removeAttr('onClick');
        $('#zefEntry'+id+'>td').css( "color", "#FFF" );
    }
    $.post(ts_ext_path + "processor.php", { axAction: "stop", axValue: 0, id: 0 },
        function(data) {
            if (data == 1) {
                ts_ext_reload();
            } else {
                alert("~~an error occured!~~")
            }
        }
    );
}


// ----------------------------------------------------------------------------------------
// delete a timesheet record immediately
//
function quickdelete(id) {
    $('#zefEntry'+id+'>td>a').blur();
    
    if (confirmText != undefined) {
      var check = confirm(confirmText);
      if (check == false) return;
    }
    
    $('#zefEntry'+id+'>td>a').removeAttr('onClick');
    $('#zefEntry'+id+'>td>a.quickdelete>img').attr("src","../skins/"+skin+"/grfx/loading13.gif");
    
    $.post(ts_ext_path + "processor.php", { axAction: "quickdelete", axValue: 0, id: id },
        function(data){
            if (data == 1) {
                ts_ext_reload();
            } else {
                alert("~~an error occured!~~")
            }
        }
    );
}

// ----------------------------------------------------------------------------------------
// edit a timesheet record
//
function editRecord(id) {
    floaterShow(ts_ext_path + "floaters.php","add_edit_record",0,id,700,350);
}

// ----------------------------------------------------------------------------------------
// refresh the rate with a new value, if this is a new entry
//
function getBestRate() {
    $.post(ts_ext_path + "processor.php", { axAction: "bestFittingRate", axValue: 0,
        project_id: $("#add_edit_zef_pct_ID").val(), event_id: $("#add_edit_zef_evt_ID").val()},
        function(data){
            if (data != -1) {
                $("#ts_ext_form_add_edit_record #rate").val(data);
            }
        }
    );
}



// ----------------------------------------------------------------------------------------
// pastes the current date and time in the outPoint field of the
// change dialog for timesheet entries 
//
//         $tpl->assign('pasteValue', date("d.m.Y - H:i:s",$kga['now']));
//
function pasteNow(value) {
    
    now = new Date();

    H = now.getHours();
    i = now.getMinutes();
    s = now.getSeconds();
    
    if (H<10) H = "0"+H;
    if (i<10) i = "0"+i;
    if (s<10) s = "0"+s;
    
    time  = H + ":" + i + ":" + s;
    
    $("#edit_out_time").val(time);
    
    $("#edit_out_day").datepicker( "setDate" , now );
}

//
// Thanks to Tijl Vercaemer for the time duration field !
//

// ----------------------------------------------------------------------------------------
// Returns a Date object, based on 2 strings
//
function ts_getDateFromStrings(dateStr,timeStr) {
    result = new Date();
    dateArray=dateStr.split(/\./);
    timeArray=timeStr.split(/:|\./);
    if(dateArray.length != 3 || timeArray.length < 1 || timeArray.length > 3) {
        return null;
    }
    result.setFullYear(dateArray[2],dateArray[1]-1,dateArray[0]);
    if (timeArray[0].length > 2) {
      result.setHours(timeArray[0].substring(0,2));
      result.setMinutes(timeArray[0].substring(2,4));
    }
    else
      result.setHours(timeArray[0]);
    if(timeArray.length>1)
        result.setMinutes(timeArray[1]);
    else
        result.setMinutes(0);
    if(timeArray.length>2)
        result.setSeconds(timeArray[2]);
    else
        result.setSeconds(0);
    return result;
}

// ----------------------------------------------------------------------------------------
// Gets the begin Date, while editing a timesheet record
//
function ts_getStartDate() {
    return ts_getDateFromStrings($("#edit_in_day").val(),$("#edit_in_time").val());
}

// ----------------------------------------------------------------------------------------
// Gets the end Date, while editing a timesheet record
//
function ts_getEndDate() {
    return ts_getDateFromStrings($("#edit_out_day").val(),$("#edit_out_time").val());
}

// ----------------------------------------------------------------------------------------
// Change the end time field, based on the duration, while editing a timesheet record
//
function ts_durationToTime() {
    end = ts_getEndDate();
    durationArray=$("#edit_duration").val().split(/:|\./);
    if(end!=null && durationArray.length > 0 && durationArray.length < 4) {
        secs = durationArray[0]*3600;
        if(durationArray.length > 1)
            secs += (durationArray[1]*60);
        if(durationArray.length > 2)
            secs += parseInt(durationArray[2]);
        begin = new Date();
        begin.setTime(end.getTime()-(secs*1000));


        var H = begin.getHours();
        var i = begin.getMinutes();
        var s = begin.getSeconds();

        if (H<10) H = "0"+H;
        if (i<10) i = "0"+i;
        if (s<10) s = "0"+s;

        $("#edit_in_time").val(H + ":" + i + ":" + s);

        var d = begin.getDate();
        var m = begin.getMonth() + 1;
        var y = begin.getFullYear();
        if (d<10) d = "0"+d;
        if (m<10) m = "0"+m;

        $("#edit_in_day").val(d + "." + m + "." + y);
    }
}

// ----------------------------------------------------------------------------------------
// Change the duration field, based on the time, while editing a timesheet record
//
function ts_timeToDuration() {
    begin = ts_getStartDate();
    end = ts_getEndDate();
    if(begin==null || end==null) {
        $("#edit_duration").val("");
    } else {
        beginSecs = Math.floor(begin.getTime() / 1000);
        endSecs = Math.floor(end.getTime() / 1000);
        durationSecs = endSecs - beginSecs;
        if(durationSecs<0) {
            $("#edit_duration").val("");
        } else {
            secs = durationSecs%60;
            if(secs<10)
                secs="0"+secs;
            durationSecs = Math.floor(durationSecs/60);
            mins = durationSecs%60;
            if(mins<10)
                mins="0"+mins;
            hours = Math.floor(durationSecs / 60);
            if(hours<10)
                hours="0"+hours;
            $("#edit_duration").val(hours+":"+mins+":"+secs);
        }
    }
}



// ----------------------------------------------------------------------------------------
// shows comment line for timesheet entry
//
function ts_comment(id) {
    $('#c'+id).toggle();
    return false;
}


// ----------------------------------------------------------------------------------------
// check if the input in the add_edit floater is valid and handle it appropriately
//
function ts_add_edit_validate() {
    
    if ($('#add_edit_zef_pct_ID').val() == undefined ||
        $('#add_edit_zef_evt_ID').val() == undefined)
      $('#ts_ext_form_add_edit_record .btn_ok').hide();
    else
      $('#ts_ext_form_add_edit_record .btn_ok').show();
}
