/* -*- Mode: jQuery; tab-width: 4; indent-tabs-mode: nil -*- */
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


function logfile(entry) {
    $.post("processor.php", { axAction: "logfile", axValue: entry, id: 0 });
}

// ----------------------------------------------------------------------------------------
// returns the dimensions of the document/page
// (unfortunately those are needed even though the jQ dimensions plugin is loaded by default)
//
function pageWidth() {
    pw = window.innerWidth != null ? window.innerWidth: document.documentElement && document.documentElement.clientWidth ? document.documentElement.clientWidth:document.body != null? document.body.clientWidth:null;
    // the dimensions plugin seems not to return very accurate results when the window is resized SMALLER ... 
    // often the right margin becomes to thick then
    // return $(window).width();
    
    minwidth = $('html').css("min-width");
    minwidth = minwidth.replace(/px/,"");

    if (minwidth > 0) { 
        if (pw < minwidth) {
            return minwidth;
        } else {
            return pw;
        }
    } else {
        return pw;
    }
}
function pageHeight() {
    return window.innerHeight != null ? window.innerHeight: document.documentElement && document.documentElement.clientHeight ? document.documentElement.clientHeight:document.body != null? document.body.clientHeight:null;
    // same is true for the page bottom margin
    // return $(window).height();
}

// ----------------------------------------------------------------------------------------
// returns the amount of space the Header and the Tabbar are currently taking
//
function headerHeight() {
    header = 90;
    tabbar = 25;
    /* always plus 10 pixels of horizontal padding */
    return header + tabbar + 10;
}

// ----------------------------------------------------------------------------------------
// change extension by tab
//
function changeTab(target,path) {
    
    if ($("#loader").is(':hidden')) {
        // if previous extension was loaded save visibility of lists
        lists_visibility[$('#fliptabs li.act').attr('id')] = $('.lists').is(':visible');
    }
    
	$('#fliptabs li').removeClass('act');
	$('#fliptabs li').addClass('norm');
	
	tab='#exttab_'+target;
	$(tab).removeClass('norm');
	$(tab).addClass('act');
	
	$('.ext').css('display','none');
	
	div='#extdiv_'+target;
	$(div).css('display','block');

    // we don't want to load the tab content every time the tab is changed ...
    is_extension_loaded = $(div).html();    
	if (!is_extension_loaded) {
	    $("#loader").show();
      lists_visible(false);
    	path = '../extensions/' + path.replace('../extensions/','') ;
    	$(div).load(path);
	} else {
	    $("#loader").hide();
      // restore visibility of lists
      lists_visible(lists_visibility[$('#fliptabs li.act').attr('id')]);
      lists_write_annotations();
	}

    if (userID) {
	  $.cookie('ki_active_tab_target_'+userID, target);
	  $.cookie('ki_active_tab_path_'+userID, path);
	}

    $.publish('tabs', [tabIdToExtensionId(target), target]);
}

function kill_timeout(to) {
    evalstring = "try {if (" + to + ") clearTimeout(" + to + ")}catch(e){}";
    // alert(evalstring);
    eval(evalstring);
}

function showTools() {
  $('#main_tools_menu').fadeIn(fading_enabled?200:0);
}

function hideTools() {
  $('#main_tools_menu').fadeOut(fading_enabled?200:0);
}

function generic_extension_resize(extId, extHeaderId, extWrap) {
    scroller_width = 17;
    if (navigator.platform.substr(0, 3) == 'Mac') {
        scroller_width = 16;
    }

    pagew = pageWidth() - 15;

    $('#'+extHeaderId).css("width", pagew - 27);
    $('#'+extHeaderId).css("top", headerHeight());
    $('#'+extHeaderId).css("left", 10);

    $('#'+extWrap).css("top", headerHeight() + 30);
    $('#'+extWrap).css("left", 10);
    $('#'+extWrap).css("width", pagew - 7);

    $('#'+extId).css("height", pageHeight() - headerHeight() - 64);
}


// ----------------------------------------------------------------------------------------
// checks if a new stable Kimai version is available for download
//
function checkupdate(path){
    $.post('core/checkupdate.php',
        function(response){
            $('#checkupdate').html(response);
        }
    );
}

// ----------------------------------------------------------------------------------------
// runs the normal watch
// 
var ZeitString, DatumsString = "";
function n_uhr() {
        n_seperator = "<span style=\"color:#EAEAD7;\">:</span>";
        Jetzt = new Date();
        //aktuelle Uhrzeit
        Stunden = Jetzt.getHours();
        Minuten = Jetzt.getMinutes();
        Sekunden = Jetzt.getSeconds();
        
        if (currentDay != Jetzt.getDate()) {
          // it's the next day
          $('#n_date').html(weekdayNames[Jetzt.getDay()] + " " + strftime(timeframeDateFormat,Jetzt));
          currentDay = Jetzt.getDate();
          
          // If the difference to the datepicker end date is less than one and a half day.
          // One day is exactly when we need to switch. Some more time is given (but not 2 full days).
          if (Jetzt-$('#pick_out').datepicker("getDate") < 1.5*24*60*60*1000) {
            setTimeframe(undefined,Jetzt);
          }
        }
        
        var ZeitString = "";
        
        if (Stunden < 10) {
            ZeitString += "0" + Stunden;
        }
        else {
            ZeitString += Stunden;
        }
        
        if (Sekunden%2==0) {
          ZeitString += n_seperator;
        }
        else {
          ZeitString += ":";
        }
        
        if (Minuten < 10) {
          ZeitString += "0" + Minuten;
        } else {
          ZeitString +=  Minuten;
        }

        $('#n_uhr').html(ZeitString);
        setTimeout("n_uhr()", 1000);
}

// ----------------------------------------------------------------------------------------
// grabs entered timeframe and writes it to database
// after that it reloads all tables
//
function setTimeframe(fromDate,toDate) {
    
    timeframe = '';
    
    if (fromDate != undefined) {
      setTimeframeStart(fromDate);
      timeframe += strftime('%m-%d-%Y',fromDate);
    }
    else {
      timeframe += "0-0-0";
    }
    
    timeframe += "|";
    
    if (toDate != undefined) {
      setTimeframeEnd(toDate);
      timeframe += strftime('%m-%d-%Y',toDate);
    }
    else {
      timeframe += "0-0-0";
    }
    
    $.post("processor.php", { axAction: "setTimeframe", axValue: timeframe, id: 0 }, 
        function(response) {
            hook_timeframe_changed(timeframe);
        }
    );
    
    updateTimeframeWarning();
}

function setTimeframeStart(fromDate) {
  $('#ts_in').html(strftime(timeframeDateFormat,fromDate));
  $('#pick_in').val(strftime('%m/%d/%Y',fromDate));
  $('#pick_out').datepicker( "option", "minDate", fromDate );
}

function setTimeframeEnd(toDate) {
  $('#ts_out').html(strftime(timeframeDateFormat,toDate));
  $('#pick_out').val(strftime('%m/%d/%Y',toDate));
  $('#pick_in').datepicker( "option", "maxDate", toDate );
}

function updateTimeframeWarning() {
    
    today = new Date();
    today.setMilliseconds(0);
    today.setSeconds(0);
    today.setMinutes(0);
    today.setHours(0);
      
    if (new Date($('#pick_out').val()) < today) {
      $('#ts_out').addClass('datewarning')
    }
    else {
      $('#ts_out').removeClass('datewarning')
    }
  
}





// ----------------------------------------------------------------------------------------
// starts a new recording when the start-buzzer is hidden
//
function startRecord(projectID,activityID,userID) {
    hour=0;min=0;sec=0;
    now = Math.floor(((new Date()).getTime())/1000);
    offset = 0;
    startsec = now;
    show_stopwatch();
    value = projectID +"|"+ activityID;
    $.post("processor.php", { axAction: "startRecord", axValue: value, id: userID, startTime: now},
        function(response){
            var data = jQuery.parseJSON(response);
            currentRecording = data['id'];
            $('#buzzer').removeClass('disabled');
            ts_ext_reload();
        }
    );
}



// ----------------------------------------------------------------------------------------
// stops the current recording when the stop-buzzer is hidden
//
function stopRecord() {
    $("#timeSheetTable>table>tbody>tr>td>a.stop>img").attr("src","../skins/"+skin+"/grfx/loading13_red.gif");
    $("#timeSheetTable>table>tbody>tr:first-child>td").css( "background-color", "#F00" );
    $("#timeSheetTable>table>tbody>tr:first-child>td").css( "color", "#FFF" );
    show_selectors();
    $.post("processor.php", { axAction: "stopRecord", axValue: 0, id: currentRecording},
        function(){
              ts_ext_reload();
              document.title = default_title;
              if (openAfterRecorded)
                editRecord(currentRecording);
        }
    );
}

function updateRecordStatus(record_ID, record_startTime, customerID, customerName, projectID, projectName, activityID, activityName) {
  if (record_ID == false) {
    // no recording is running anymore
    currentRecording = -1;
    show_selectors();
    return;
  }
  
  startsec = record_startTime;
  
  if (selected_project != projectID)
    buzzer_preselect_project(projectID, projectName, customerID, customerName, false);
}

function show_stopwatch() {
    $("#selector").css('display','none');
    $("#stopwatch").css('display','block');
    $("#stopwatch_ticker").css('display','block');
    $("#buzzer").addClass("act");
    $("#ticker_customer").html($("#selected_customer").html());
    $("#ticker_project").html($("#selected_project").html());
    $("#ticker_activity").html($("#selected_activity").html());
    $("ul#ticker").newsticker();
    ticktac();
}

function show_selectors() {
    ticktack_off();
    $("#selector").css('display','block');
    $("#stopwatch").css('display','none');
    $("#stopwatch_ticker").css('display','none');
    $("#buzzer").removeClass("act");
    if (!(selected_customer && selected_project && selected_activity)) {
      $('#buzzer').addClass('disabled');
    }
}

function buzzer() {
  if ( currentRecording == -1 && $('#buzzer').hasClass('disabled') ) return;


  if (currentRecording > -1) {
      currentRecording=0;
      stopRecord();
    } else {
        setTimeframe(undefined,new Date());
        startRecord(selected_project,selected_activity,userID);
        $('#buzzer').addClass('disabled');
    }
}

function buzzer_preselect_project(projectID,projectName,customerID,customerName,updateRecording) {
  selected_customer = customerID;
  selected_project = projectID;
  $.post("processor.php", { axAction: "saveBuzzerPreselection", project:projectID});
  $("#selected_customer").html(customerName);
  $("#selected_project").html(projectName);
  $("#selected_customer").removeClass("none");
  
  lists_reload('activity', function() {
    buzzer_preselect_update_ui('projects', projectID, updateRecording);
  }); 
}

function buzzer_preselect_activity(activityID,activityName,updateRecording) {
    selected_activity = activityID;
    $.post("processor.php", { axAction: "saveBuzzerPreselection", activity:activityID});
    $("#selected_activity").html(activityName);
    buzzer_preselect_update_ui('activities', activityID, updateRecording);
}

function buzzer_preselect_update_ui(selector,selectedID,updateRecording) {
  
  if (updateRecording == undefined) {
    updateRecording = true;
  }
    
  $('#'+selector+'>table>tbody>tr>td>a.preselect>img').attr('src','../skins/'+skin+'/grfx/preselect_off.png');
  $('#'+selector+'>table>tbody>tr>td>a.preselect#ps'+selectedID+'>img').attr('src','../skins/'+skin+'/grfx/preselect_on.png');
  $('#'+selector+'>table>tbody>tr>td>a.preselect#ps'+selectedID).blur();
  
  if (selected_project && selected_activity && $('#activities>table>tbody>tr>td>a.preselect>img[src$="preselect_on.png"]').length > 0) {
    $('#buzzer').removeClass('disabled');
  }
  else
    return;
    
  $("#ticker_customer").html($("#selected_customer").html());
  $("#ticker_project").html($("#selected_project").html());
  $("#ticker_activity").html($("#selected_activity").html());
  
  if (currentRecording > -1 && updateRecording) {
    $.post("../extensions/ki_timesheets/processor.php", { axAction: "edit_running", id: currentRecording, project:selected_project, activity:selected_activity},
      function(data) {
        ts_ext_reload();
      }
    );
  }
}

// ----------------------------------------------------------------------------------------
// runs the stopwatch
// modified version by x-tin
// I would have added more credits - but you didn't leave any personal information on the forum ...
// ... so just THX! ;)

function ticktac() {
    startsecoffset = startsec ? startsec : offset;
    sek   = Math.floor((new Date()).getTime()/1000)-startsecoffset;
    hour  = Math.floor(sek / 3600);
    min   = Math.floor((sek-hour*3600) / 60);
    sec   = Math.floor(sek-hour*3600-min*60);
    
    if (sec==60) { sec=0; min++; }
    if (min > 59) { min = 0; hour++; }
    if (sec==0) $("#s").html("00");
    else {
        $("#s").html(((sec<10)?"0":"")+sec);
    }
    if (min==0) $("#m").html("00");
    else {
        $("#m").html(((min<10)?"0":"")+min);
     }
    if (hour==0) $("#h").html("00");
    else {
        $("#h").html(((hour<10)?"0":"")+hour);
    }

    htmp = $("#h").html();
    mtmp = $("#m").html();
    stmp = $("#s").html();
    titleclock = htmp + ":" + mtmp  + ":" + stmp;
    document.title = titleclock;
    timeoutTicktack = setTimeout("ticktac()", 1000);
}

function ticktack_off() {
    if (timeoutTicktack) {
        clearTimeout(timeoutTicktack);
        timeoutTicktack = 0;
        $("#h").html("00");
        $("#m").html("00");
        $("#s").html("00");
    }
}


// ----------------------------------------------------------------------------------------
// shows dialogue for editing an item in either customer, project or activity list
//
function editSubject(subject,id) {
    var width = 450;
    if (subject == 'project') {
      width = 650;
    }
    floaterShow('floaters.php','add_edit_'+subject,0,id,width); return false;
}


// ----------------------------------------------------------------------------------------
// filters project and activity fields in add/edit record dialog

function filter_selects(id, needle) {
  // cache initialisieren
  if(typeof window['__cacheselect_'+id] == "undefined") {
    window['__cacheselect_'+id] = [];
    $('#'+id+' option ').each(function(index) {
      window['__cacheselect_'+id].push({
        'value':$(this).val()
        , 'text':$(this).text()
      })
    })
  }
  
  var selectedValue = $('#'+id).val();
  $('#'+id).removeOption(/./);
  
  var i, cs = window['__cacheselect_'+id];
  for(i=0; i<cs.length; ++i) {
    if(cs[i].text.toLowerCase().indexOf(needle.toLowerCase()) !== -1) $('#'+id).addOption(cs[i].value, cs[i].text);
  }
  $('#'+id).val(selectedValue);
}

// -----------------------------------------------------------------------------

function lists_visible(visible) {
  if (visible) {
    lists_resize();
    $('.lists').show();
    lists_resize();
  }
  else {
    $('.lists').hide();
  }
}

// ----------------------------------------------------------------------------------------
// reloads timesheet, customer, project and activity tables
//
function lists_reload(subject, callback) {
    switch (subject) {
        case "user":
            $.post("processor.php", { axAction: "reload_users", axValue: 0, id: 0 },
                function(data) {
                    $("#users").html(data);
                    ($("#users").innerHeight()-$("#users table").outerHeight()>0)?scr=0:scr=scroller_width;
                    $("#users table").css("width",customerColumnWidth-scr);
                    lists_live_filter('user', $('#filt_user').val());
		    lists_write_annotations('user');
                    if (typeof(callback) != "undefined")
                      callback();
                }
            );
    break;
        case "customer":
            $.post("processor.php", { axAction: "reload_customers", axValue: 0, id: 0 },
                function(data) {
                    $("#customers").html(data);
                    ($("#customers").innerHeight()-$("#customers table").outerHeight()>0)?scr=0:scr=scroller_width;
                    $("#customers table").css("width",customerColumnWidth-scr);
                    lists_live_filter('customer', $('#filter_customer').val());
                    lists_write_annotations('customer');
                    if (typeof(callback) != "undefined")
                      callback();
                }
            );
    break;
        case "project": 
            $.post("processor.php", { axAction: "reload_projects", axValue: 0, id: 0 },
                function(data) { 
                    $("#projects").html(data);
                    ($("#projects").innerHeight()-$("#projects table").outerHeight()>0)?scr=0:scr=scroller_width;
                    $("#projects table").css("width",projectColumnWidth-scr);
                    $('#projects>table>tbody>tr>td>a.preselect#ps'+selected_project+'>img').attr('src','../skins/'+skin+'/grfx/preselect_on.png');
                    lists_live_filter('project', $('#filter_project').val());
                    lists_write_annotations('project');
                    if (typeof(callback) != "undefined")
                      callback();
                }
            );
    break;
        case "activity": 
            $.post("processor.php", { axAction: "reload_activities", axValue: 0, id: 0, project:selected_project },
                function(data) { 
                    $("#activities").html(data);
                    ($("#activities").innerHeight()-$("#activities table").outerHeight()>0)?scr=0:scr=scroller_width;
                    $("#activities table").css("width",activityColumnWidth-scr);
                    $('#activities>table>tbody>tr>td>a.preselect#ps'+selected_activity+'>img').attr('src','../skins/'+skin+'/grfx/preselect_on.png');
                    lists_live_filter('activity', $('#filter_activity').val());
		    lists_write_annotations('activity');
        if ($('#row_activity[data-id="'+selected_activity+'"]').length == 0) {
          $('#buzzer').addClass('disabled');
        }
        else {
          $('#buzzer').removeClass('disabled');
        }
        if (typeof(callback) != "undefined")
          callback();
                }
            );
    break;
    }
}

// ----------------------------------------------------------------------------------------
//  Live Filter by The One And Only T.C. (TOAOTC) - THX - WOW! ;)
// 
function lists_live_filter(div_list, needle) {
  $('#'+div_list+' tr ').filter(function(index) {
    return ($(this).children('td:nth-child(2)').text().toLowerCase().indexOf(needle.toLowerCase()) === -1);
  }).css('display','none');
  $('#'+div_list+' tr ').filter(function(index) {
    return ($(this).children('td:nth-child(2)').text().toLowerCase().indexOf(needle.toLowerCase()) !== -1);
  }).css('display','');
}

function lists_customer_highlight(customer) {
  $(".customer").removeClass("filterProjectForPreselection");
  $(".project").removeClass("filterProjectForPreselection");
  $("#projects .customer"+customer).addClass("filterProjectForPreselection");
  $("#projects .project").removeClass("TableRowInvisible");
}

function lists_customer_prefilter(customer, filter, singleFilter) {
  if (singleFilter && filter)
      $("#projects .project").addClass("TableRowInvisible");

  if (filter)
    $("#projects .customer"+customer).removeClass("TableRowInvisible");
  else
    $("#projects .customer"+customer).addClass("TableRowInvisible");

  if (singleFilter && !filter)
      $("#projects .project").removeClass("TableRowInvisible");
}


// ----------------------------------------------------------------------------------------
//  table row changes color on rollover - preselection link on whole row
//
function lists_change_color(tableRow,highLight) {
  if (highLight) {
    $(tableRow).parents("tr").addClass("highlightProjectForPreselection");
  } else {
    $(tableRow).parents("tr").removeClass("highlightProjectForPreselection");
  }
}

function lists_update_annotations(id,user,customer,project,activity)
{
  lists_user_annotations[id] = user;
  lists_customer_annotations[id] = customer;
  lists_project_annotations[id] = project;
  lists_activity_annotations[id] = activity;

  if ($('.menu li#exttab_'+id).hasClass('act'))
    lists_write_annotations();
}

function lists_write_annotations(part)
{
  var id = parseInt($('#fliptabs li.act').attr('id').substring(7));

  if (!part || part == 'user') {
    $('#users>table>tbody td.annotation').html("");
    if (lists_user_annotations[id] != null)
      for (var i in lists_user_annotations[id])
        $('#row_user[data-id="'+i+'"]>td.annotation').html(lists_user_annotations[id][i]);
  }
  if (!part || part == 'customer') {
    $('#customers>table>tbody td.annotation').html("");
    if (lists_customer_annotations[id] != null)
      for (var i in lists_customer_annotations[id])
        $('#row_customer[data-id="'+i+'"]>td.annotation').html(lists_customer_annotations[id][i]);
  }
  if (!part || part == 'project') {
    $('#projects>table>tbody td.annotation').html("");
    if (lists_project_annotations[id] != null)
      for (var i in lists_project_annotations[id])
        $('#row_project[data-id="'+i+'"]>td.annotation').html(lists_project_annotations[id][i]);
  }
  if (!part || part == 'activity') {
    $('#activities>table>tbody td.annotation').html("");
    if (lists_activity_annotations[id] != null)
      for (var i in lists_activity_annotations[id])
        $('#row_activity[data-id="'+i+'"]>td.annotation').html(lists_activity_annotations[id][i]);
  }
}

function lists_filter_select_all(subjectPlural) {
  $('#'+subjectPlural+' tr').each(function(index) {
    if ( $(this).hasClass('fhighlighted') ) return;

    var subjectSingular = $(this).attr('id').substring(4);
    lists_toggle_filter(subjectSingular,parseInt($(this).attr('data-id')));
  });
    hook_filter();
}
function lists_filter_deselect_all(subjectPlural) {
  $('#'+subjectPlural+' tr').each(function(index) {
    if (! $(this).hasClass('fhighlighted') ) return;
                            
    var subjectSingular = $(this).attr('id').substring(4);
    lists_toggle_filter(subjectSingular,parseInt($(this).attr('data-id')));
  });
    hook_filter();
}

function lists_filter_select_invert(subjectPlural) {
  $('#'+subjectPlural+' tr').each(function(index) {
    var subjectSingular = $(this).attr('id').substring(4);
    lists_toggle_filter(subjectSingular,parseInt($(this).attr('data-id')));
  });
    hook_filter();
}

function lists_toggle_filter(subject,id) {
    var rowElement = $('#row_'+subject+'[data-id="'+id+'"]');
    
    if (rowElement.hasClass('fhighlighted')) {
        rowElement.removeClass('fhighlighted');
        switch (subject) {
        case 'user':
          filterUsers.splice(filterUsers.indexOf(id),1);
        break;
        case 'customer':
          filterCustomers.splice(filterCustomers.indexOf(id),1);
          var singleFilter = $('.fhighlighted',rowElement.parent()).length == 0;
          lists_customer_prefilter(id, false, singleFilter);
        break;
        case 'project':
          filterProjects.splice(filterProjects.indexOf(id),1);
        break;
        case 'activity':
          filterActivities.splice(filterActivities.indexOf(id),1);
        break;
      }
    }
    else
    {
      rowElement.addClass('fhighlighted');
      switch (subject) {
        case 'user':
          filterUsers.push(id);
        break;
        case 'customer':
          filterCustomers.push(id);
          var singleFilter = $('.fhighlighted',rowElement.parent()).length == 1;
          lists_customer_prefilter(id, true, singleFilter);
        break;
        case 'project':
          filterProjects.push(id);
        break;
        case 'activity':
          filterActivities.push(id);
        break;
      }
    }
}

function lists_update_filter(subject,id) {
    lists_toggle_filter(subject,id);
    // let tab update its data
    hook_filter();
    // finally update timetable
}

function validatePassword(password,retypePassword) {
    if (password != retypePassword) {
        alert(lang_passwordsDontMatch);
        return false;
    }
    else if (password.length < 5) {
        alert(lang_passwordTooShort);
        return false;
    }
    else
        return true;
}

function setFloaterErrorMessage(fieldName,message) {
  if (fieldName == '')
    fieldName = "floater_tabs";
    
  var li = $("#floater_innerwrap #"+fieldName).closest('li');
  if (li.length == 0) {
      li = $("#floater_innerwrap [name='"+fieldName+"']").closest('li');
  }
  if (li.length == 0) {
      li = $("#floater_innerwrap form");
  }
  li.prepend('<div class="errorMessage">'+message+'</div>');
  li.addClass('errorField');
  
  // indicate in tab header
  var id = li.closest('fieldset').attr('id');
  $("#floater_innerwrap .menu a[href='#" + id + "']").addClass("tabError");
}

function clearFloaterErrorMessages() {
  $("#floater_innerwrap .errorMessage").remove();
  $("#floater_tabs li").removeClass("errorField");
  $("#floater_innerwrap .menu a").removeClass("tabError");
}
