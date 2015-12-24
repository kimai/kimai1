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
    ts_ext_applyHoverIntent();
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

    (customerShrinkMode)?subtableCount=2:subtableCount=3;
    subtableWidth = (pageWidth()-10)/subtableCount-7 ;

    timeSheet_width = pageWidth()-24;
    timeSheet_height = pageHeight()-224-headerHeight()-28;
}

/**
 * Hover a row if the mouse is over it for more than half a second.
 */
function ts_ext_applyHoverIntent() {
    $('#timeSheet tr').hoverIntent({
        sensitivity: 1,
        interval: 500,
        over:
          function() {
              $('#timeSheet tr').removeClass('hover');
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
    $("#timeSheet_head,#timeSheet").css("width",timeSheet_width);
    ts_ext_set_TableWidths();
}

/**
 * If the extension is being shrinked so the sublists are shown larger
 * adjust to that.
 */
function ts_ext_set_heightTop() {
    ts_ext_get_dimensions();
    if (!extensionShrinkMode) {
        $("#timeSheet").css("height", timeSheet_height);
    } else {
        $("#timeSheet").css("height", "70px");
    }

    ts_ext_set_TableWidths();
}

/**
 * Set the width of the table.
 */
function ts_ext_set_TableWidths() {
    ts_ext_get_dimensions();
    // set table widths
    ($("#timeSheet").innerHeight()-$("#timeSheet table").outerHeight()>0)?scr=0:scr=scroller_width; // width of timeSheet table depending on scrollbar or not
    $("#timeSheet table").css("width",timeSheet_width-scr);
    $("div#timeSheet > div > table > tbody > tr > td.trackingnumber").css("width", $("#timeSheet_head > table > tbody > tr > td.trackingnumber").width());
    // stretch duration column in faked timeSheet table head
    $("#timeSheet_head > table > tbody > tr > td.time").css("width", $("div#timeSheet > div > table > tbody > tr > td.time").width());
    // stretch customer column in faked timeSheet table head
    $("#timeSheet_head > table > tbody > tr > td.customer").css("width", $("div#timeSheet > div > table > tbody > tr > td.customer").width());
    // stretch project column in faked timeSheet table head
    $("#timeSheet_head > table > tbody > tr > td.project").css("width", $("div#timeSheet > div > table > tbody > tr > td.project").width());
    // stretch activity column in faked timeSheet table head
    $("#timeSheet_head > table > tbody > tr > td.activity").css("width", $("div#timeSheet > div > table > tbody > tr > td.activity").width());
}

function timesheet_extension_tab_changed() {
    $('#display_total').html(ts_total);
    if (timesheet_timeframe_changed_hook_flag) {
        ts_ext_reload();
        timesheet_customers_changed_hook_flag = 0;
        timesheet_projects_changed_hook_flag = 0;
        timesheet_activities_changed_hook_flag = 0;
    }
    if (timesheet_customers_changed_hook_flag) {
        timesheet_extension_customers_changed();
        timesheet_projects_changed_hook_flag = 0;
        timesheet_activities_changed_hook_flag = 0;
    }
    if (timesheet_projects_changed_hook_flag) {
        timesheet_extension_projects_changed();
    }
    if (timesheet_activities_changed_hook_flag) {
        timesheet_extension_activities_changed();
    }

    timesheet_timeframe_changed_hook_flag = 0;
    timesheet_customers_changed_hook_flag = 0;
    timesheet_projects_changed_hook_flag = 0;
    timesheet_activities_changed_hook_flag = 0;
}

function timesheet_extension_timeframe_changed() {
    if ($('.ki_timesheet').css('display') == "block") {
        ts_ext_reload();
    } else {
        timesheet_timeframe_changed_hook_flag++;
    }
}
function timesheet_extension_customers_changed() {
    if ($('.ki_timesheet').css('display') == "block") {
        ts_ext_reload();
    } else {
        timesheet_customers_changed_hook_flag++;
    }
}

function timesheet_extension_projects_changed() {
    if ($('.ki_timesheet').css('display') == "block") {
        ts_ext_reload();
    } else {
        timesheet_projects_changed_hook_flag++;
    }
}

function timesheet_extension_activities_changed() {
    if ($('.ki_timesheet').css('display') == "block") {
        ts_ext_reload();
    } else {
        timesheet_activities_changed_hook_flag++;
    }
}


// ----------------------------------------------------------------------------------------
// reloads timesheet, customer, project and activity tables
//
function ts_ext_reload() {
            $.post(ts_ext_path + "processor.php", { axAction: "reload_timeSheet", axValue: filterUsers.join(":")+'|'+filterCustomers.join(":")+'|'+filterProjects.join(":")+'|'+filterActivities.join(":"), id: 0,
                first_day: new Date($('#pick_in').val()).getTime()/1000, last_day: new Date($('#pick_out').val()).getTime()/1000  },
                function(data) {
                    $("#timeSheet").html(data);

                    ts_ext_set_TableWidths()
                    ts_ext_applyHoverIntent();
                }
            );
}


// ----------------------------------------------------------------------------------------
// reloads timesheet, customer, project and activity tables
//
function ts_ext_reload_activities(project,noUpdateRate, activity, timeSheetEntry) {
  var selected_activity = $('#add_edit_timeSheetEntry_activityID').val();
            $.post(ts_ext_path + "processor.php", { axAction: "reload_activities_options", axValue: 0, id: 0, project:project },
                function(data) {
          delete window['__cacheselect_add_edit_timeSheetEntry_activityID'];
                    $("#add_edit_timeSheetEntry_activityID").html(data);
                    $("#add_edit_timeSheetEntry_activityID").val(selected_activity);
                    if (noUpdateRate == undefined)
                    getBestRates();
                    if(activity > 0) {
                      $.getJSON("../extensions/ki_timesheets/processor.php", {
                          axAction: "budgets",
                          project_id: project,
                          activity_id: activity,
                          timeSheetEntryID: timeSheetEntry
                        },
                        function(data) {
                          ts_ext_updateBudget(data);
                        }
                       );
                    }
                }
            );
}

//----------------------------------------------------------------------------------------
//reloads budget
//
// everything in data['timeSheetEntry'] has to be subtracted in case the time sheet entry is in the db already
// part of this activity. In other cases, we already took case on server side that the values are 0
function ts_ext_updateBudget(data) {
  var budget = data['activityBudgets']['budget'];
  // that is the case if we changed the project and no activity is selected
  if(isNaN(budget)) {
    budget = 0;
  }
  if($('#budget_val').val() != '') {
    budget+= parseFloat($('#budget_val').val());
  }
  budget-= data['timeSheetEntry']['budget'];
  $('#budget_activity').text(budget);
  var approved = data['activityBudgets']['approved'];
  // that is the case if we changed the project and no activity is selected
  if(isNaN(approved)) {
    approved = 0;
  }
  if($('#approved').val() != '') {
    approved+= parseFloat($('#approved').val());
  }
  approved-= data['timeSheetEntry']['approved'];
  $('#budget_activity_approved').text(approved);
  var budgetUsed = data['activityUsed'];
  if(isNaN(budgetUsed)) {
    budgetUsed = 0;
  }
    var durationArray= new Array();
    durationArray = $("#duration").val().split(/:|\./);
    if(end!=null && durationArray.length > 0 && durationArray.length < 4) {
        secs = durationArray[0]*3600;
        if(durationArray.length > 1)
            secs += (durationArray[1]*60);
        if(durationArray.length > 2)
          secs += parseInt(durationArray[2]);
    var rate = $('#rate').val();
    if(rate != '') {
        budgetUsed+= secs/3600*rate;
      budgetUsed-=data['timeSheetEntry']['duration']/3600*data['timeSheetEntry']['rate'];
    }
    }
  $('#budget_activity_used').text(Math.round(budgetUsed,2));
}

// ----------------------------------------------------------------------------------------
// this function is attached to the little green arrows in front of each timesheet record
// and starts recording that activity anew
//
function ts_ext_recordAgain(project,activity,id) {
    $('#timeSheetEntry'+id+'>td>a').blur();

    if (currentRecording > -1) {
        stopRecord();
    }

    $('#timeSheetEntry'+id+'>td>a.recordAgain>img').attr("src","../skins/"+skin+"/grfx/loading13.gif");
    hour=0;min=0;sec=0;
    now = Math.floor(((new Date()).getTime())/1000);
    offset = now;
    startsec = 0;
    show_stopwatch();
    $('#timeSheetEntry'+id+'>td>a').removeAttr('onclick');

    $.post(ts_ext_path + "processor.php", { axAction: "record", axValue: 0, id: id },
        function(data) {
          if (data.errors.length > 0)
            return;

          customer = data.customer;
          customerName = data.customerName;
          projectName = data.projectName;
          activityName = data.activityName;
          currentRecording = data.currentRecording;

          ts_ext_reload();
          buzzer_preselect_project(project,projectName,customer,customerName,false);
          buzzer_preselect_activity(activity,activityName,0,0,false);
          $("#ticker_customer").html(customerName);
          $("#ticker_project").html(projectName);
          $("#ticker_activity").html(activityName);
        }
    );
}


// ----------------------------------------------------------------------------------------
// this function is attached to the little green arrows in front of each timesheet record
// and starts recording that activity anew
//
function ts_ext_stopRecord(id) {
    ticktack_off();
    show_selectors();
    if (id) {
        $('#timeSheetEntry'+id+'>td').css( "background-color", "#F00" );
        $('#timeSheetEntry'+id+'>td>a.stop>img').attr("src","../skins/"+skin+"/grfx/loading13_red.gif");
        $('#timeSheetEntry'+id+'>td>a').blur();
        $('#timeSheetEntry'+id+'>td>a').removeAttr('onclick');
        $('#timeSheetEntry'+id+'>td').css( "color", "#FFF" );
    }
    $.post(ts_ext_path + "processor.php", { axAction: "stop", axValue: 0, id: id },
        function(data) {
                ts_ext_reload();
        }
    );
}


// ----------------------------------------------------------------------------------------
// delete a timesheet record immediately
//
function quickdelete(id) {
    $('#timeSheetEntry'+id+'>td>a').blur();

    if (confirmText != undefined) {
      var check = confirm(confirmText);
      if (check == false) return;
    }

    $('#timeSheetEntry'+id+'>td>a').removeAttr('onclick');
    $('#timeSheetEntry'+id+'>td>a.quickdelete>img').attr("src","../skins/"+skin+"/grfx/loading13.gif");

    $.post(ts_ext_path + "processor.php", { axAction: "quickdelete", axValue: 0, id: id },
        function(result){
            if (result.errors.length == 0) {
                ts_ext_reload();
            } else {
              var messages = [];
              for (var index in result.errors)
                messages.push(result.errors[index]);
              alert(messages.join("\n"));
            }
        }
    );
}

// ----------------------------------------------------------------------------------------
// edit a timesheet record
//
function editRecord(id) {
    floaterShow(ts_ext_path + "floaters.php","add_edit_timeSheetEntry",0,id,650);
}

//----------------------------------------------------------------------------------------
//edit a timesheet quick note
//
function editQuickNote(id) {
    floaterShow(ts_ext_path + "floaters.php","add_edit_timeSheetQuickNote",0,id,650);
}

// ----------------------------------------------------------------------------------------
// refresh the rate with a new value, if this is a new entry
//
function getBestRates() {
    $.getJSON(ts_ext_path + "processor.php", { axAction: "bestFittingRates", axValue: 0,
        project_id: $("#add_edit_timeSheetEntry_projectID").val(), activity_id: $("#add_edit_timeSheetEntry_activityID").val()},
        function(data){
            if (data.errors.length > 0)
              return;

            if (data.hourlyRate == false) {
              //TODO: why does Kimai do this? If we already set a rate
              // we might want to keep it, not just reset it to empty..?
//              $("#ts_ext_form_add_edit_timeSheetEntry #rate").val('');
              } else {
              $("#ts_ext_form_add_edit_timeSheetEntry #rate").val(data.hourlyRate);
              }
            if (data.fixedRate == false) {
              $("#ts_ext_form_add_edit_timeSheetEntry #fixedRate").val('');
            } else {
              $("#ts_ext_form_add_edit_timeSheetEntry #fixedRate").val(data.fixedRate);
            }
        }
    );
}



// ----------------------------------------------------------------------------------------
// pastes the current date and time in the outPoint field of the
// change dialog for timesheet entries
//
//         $view->pasteValue = date("d.m.Y - H:i:s",$kga['now']);
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

    $("#end_time").val(time);
    $('#end_time').trigger('change');

    $("#end_day").datepicker( "setDate" , now );
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
    return ts_getDateFromStrings($("#start_day").val(),$("#start_time").val());
}

// ----------------------------------------------------------------------------------------
// Gets the end Date, while editing a timesheet record
//
function ts_getEndDate() {
    return ts_getDateFromStrings($("#end_day").val(),$("#end_time").val());
}

// ----------------------------------------------------------------------------------------
// Change the end time field, based on the duration, while editing a timesheet record
//
function ts_durationToTime() {
    end = ts_getEndDate();
    durationArray=$("#duration").val().split(/:|\./);
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

        $("#start_time").val(H + ":" + i + ":" + s);

        var d = begin.getDate();
        var m = begin.getMonth() + 1;
        var y = begin.getFullYear();
        if (d<10) d = "0"+d;
        if (m<10) m = "0"+m;

        $("#start_day").val(d + "." + m + "." + y);
    }
}

// ----------------------------------------------------------------------------------------
// Change the duration field, based on the time, while editing a timesheet record
//
function ts_timeToDuration() {
    begin = ts_getStartDate();
    end = ts_getEndDate();
    if(begin==null || end==null) {
        $("#duration").val("");
    } else {
        beginSecs = Math.floor(begin.getTime() / 1000);
        endSecs = Math.floor(end.getTime() / 1000);
        durationSecs = endSecs - beginSecs;
        if(durationSecs<0) {
            $("#duration").val("");
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
            $("#duration").val(hours+":"+mins+":"+secs);
            $('#duration').trigger('change');
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
