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
// shows floating dialog windows based on processor data
//
function floaterShow(phpFile, axAction, axValue, id, width, height) {
    if ($('#floater').css("display") == "block") {
        $("#floater").fadeOut(fading_enabled?500:0, function() {
            floaterLoadContent(phpFile, axAction, axValue, id, width, height);
        });
    } else {
            floaterLoadContent(phpFile, axAction, axValue, id, width, height);
    }
}    
function floaterLoadContent(phpFile, axAction, axValue, id, width, height) {
    $("#floater").load(phpFile,
        {
            axAction: axAction,
            axValue: axValue,
            id: id
        },
        function() {
          
          $('#floater').css({width: width+"px"});
          
          $('#floater_tabs').css({height: height+"px"});
          
          x = ($(document).width()-(width+10))/2;
          y = ($(document).height()-(height+80))/2;
          if (y<0) y=0;
          if (x<0) x=0;
          $("#floater").css({left:x+"px",top:y+"px"});
          $("#floater").fadeIn(fading_enabled?200:0);
          
          $('#focus').focus();
          $('.extended').hide();
          $('#floater_content').css("height",$('#floater_dimensions').outerHeight()+5);
 
          // toggle class of the proberbly existing extended options button
          $(".options").toggle(function(){
              el = $(this);
              el.addClass("up");
              el.removeClass("down");
              return false;
          },function(){
              el = $(this);
              el.addClass("down");
              el.removeClass("up");
              return false;
          });
          
          
        }
    );  
}

// ----------------------------------------------------------------------------------------

// ----------------------------------------------------------------------------------------
// hides dialog again
//
function floaterClose() {
    //$('#floater').draggable('destroy');
    $("#floater").fadeOut(fading_enabled?500:0);
}


// ----------------------------------------------------------------------------------------
// change extension by tab
//
function changeTab(target,path) {
    
    kill_reg_timeouts();

  
  if ($("#loader").is(':hidden')) {
    // if previous extension was loaded save visibility of lists
    lists_visibility[$('#fliptabs li.act').attr('id')] = $('body>.lists').is(':visible');
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
        if (usr_ID) {
	  $.cookie('ki_active_tab_target_'+usr_ID, target);
	  $.cookie('ki_active_tab_path_'+usr_ID, path);
	}
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
          $('#n_date').html(weekdayNames[Jetzt.getDay()] + " " + strftime(timespaceDateFormat,Jetzt));
          currentDay = Jetzt.getDate();
          
          // If the difference to the datepicker end date is less than one and a half day.
          // One day is exactly when we need to switch. Some more time is given (but not 2 full days).
          if (Jetzt-$('#pick_out').datepicker("getDate") < 1.5*24*60*60*1000) {
            setTimespace(undefined,Jetzt);
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


function logfile(entry) {
    $.post("processor.php", { axAction: "logfile", axValue: entry, id: 0 });
}




// ----------------------------------------------------------------------------------------
// grabs entered timespace and writes it to database
// after that it reloads all tables
//
function setTimespace(fromDate,toDate) {
    
    timespace = '';
    
    if (fromDate != undefined) {
      setTimespaceStart(fromDate);
      timespace += strftime('%m-%d-%Y',fromDate);
    }
    else {
      timespace += "0-0-0";
    }
    
    timespace += "|";
    
    if (toDate != undefined) {
      setTimespaceEnd(toDate);
      timespace += strftime('%m-%d-%Y',toDate);
    }
    else {
      timespace += "0-0-0";
    }
    
    $.post("processor.php", { axAction: "setTimespace", axValue: timespace, id: 0 }, 
        function(response) {
            hook_tss();
        }
    );
    
    updateTimespaceWarning();
}

function setTimespaceStart(fromDate) {
  $('#ts_in').html(strftime(timespaceDateFormat,fromDate));
  $('#pick_in').val(strftime('%m/%d/%Y',fromDate));
  $('#pick_out').datepicker( "option", "minDate", fromDate );
}

function setTimespaceEnd(toDate) {
  $('#ts_out').html(strftime(timespaceDateFormat,toDate));
  $('#pick_out').val(strftime('%m/%d/%Y',toDate));
  $('#pick_in').datepicker( "option", "maxDate", toDate );
}

function updateTimespaceWarning() {
    
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
// starts a new recording task when the start-buzzer is hidden
//
function startRecord(pct_ID,evt_ID,user_ID) {
    hour=0;min=0;sec=0;
    now = Math.floor(((new Date()).getTime())/1000);
    offset = now;
    startsec = 0;
    show_stopwatch();
    value = pct_ID +"|"+ evt_ID;
    $.post("processor.php", { axAction: "startRecord", axValue: value, id: user_ID},
        function(response){
            ts_ext_reload();
            $("#stopwatch_edit_comment").show();
        }
    );
}



// ----------------------------------------------------------------------------------------
// stops the current recording task when the stop-buzzer is hidden
//
function stopRecord() {
    $("#zeftable>table>tbody>tr>td>a.stop>img").attr("src","../skins/"+skin+"/grfx/loading13_red.gif");
    $("#zeftable>table>tbody>tr:first-child>td").css( "background-color", "#F00" );
    $("#zeftable>table>tbody>tr:first-child>td").css( "color", "#FFF" );
    show_selectors();
    $.post("processor.php", { axAction: "stopRecord", axValue: 0, id: 0},
        function(){
            if (recstate == 0) {
              ts_ext_reload();
              document.title = default_title;
            }
        }
    );
}

function show_stopwatch() {
    $("#selector").css('display','none');
    $("#stopwatch").css('display','block');
    $("#stopwatch_ticker").css('display','block');
    $("#buzzer").addClass("act");
    $("#ticker_knd").html($("#sel_knd").html());
    $("#ticker_pct").html($("#sel_pct").html());
    $("#ticker_evt").html($("#sel_evt").html());
    $("ul#ticker").newsticker();
    ticktac();
}

function show_selectors() {
    ticktack_off();
    $("#selector").css('display','block');
    $("#stopwatch").css('display','none');
    $("#stopwatch_edit_comment").css('display','none');
    $("#stopwatch_ticker").css('display','none');
    $("#buzzer").removeClass("act");
    if (!(selected_knd && selected_pct && selected_evt)) {
      $('#buzzer').addClass('disabled');
    }
}

function edit_running_comment() {
  floaterShow('../extensions/ki_timesheets/floaters.php',
      'edit_running_comment',0,0,600,200);
}

function buzzer() {
    if ( recstate!=1 && $('#buzzer').hasClass('disabled') ) return;


    if (recstate) {
        recstate=0;
        stopRecord();
    } else {
        setTimespace(undefined,new Date());
        startRecord(selected_pct,selected_evt,usr_ID);
        recstate=1;
    }
}

// preselections for buzzer
function buzzer_preselect(subject,id,name,kndID,kndName,updateRecording) {
  
    if (updateRecording == undefined) {
      updateRecording = true;
    }
    
    switch (subject) {
        case "knd":
        // TODO: build filter for project selection (by customer)
            $("#sel_knd").html("select project");
            $("#sel_knd").addClass("none");
        break;
        case "pct":
            selected_knd = kndID;
            selected_pct = id;
            $.post("processor.php", { axAction: "saveBuzzerPreselection", project:id});
            $("#sel_knd").html(kndName);
            $("#sel_pct").html(name);
            $("#sel_knd").removeClass("none");
        break;
        case "evt":
            selected_evt = id;
            $.post("processor.php", { axAction: "saveBuzzerPreselection", event:id});
            $("#sel_evt").html(name);
        break;
    }
    $('#'+subject+'>table>tbody>tr>td>a.preselect>img').attr('src','../skins/'+skin+'/grfx/preselect_off.png');
    $('#'+subject+'>table>tbody>tr>td>a.preselect#ps'+id+'>img').attr('src','../skins/'+skin+'/grfx/preselect_on.png');
    $('#'+subject+'>table>tbody>tr>td>a.preselect#ps'+id).blur();
    
    if (selected_knd && selected_pct && selected_evt) {
      $('#buzzer').removeClass('disabled');
    }

    if (recstate && updateRecording) {


      switch (subject) {
          case "pct":
              $.post("../extensions/ki_timesheets/processor.php", { axAction: "edit_running_project", project:id},
                function(data) {
                    ts_ext_reload();
                  }
                );
          break;
          case "evt":
              $.post("../extensions/ki_timesheets/processor.php", { axAction: "edit_running_task", task:id},
                function(data) {
                    ts_ext_reload();
                  }
              );
          break;
      }

      $("#ticker_knd").html($("#sel_knd").html());
      $("#ticker_pct").html($("#sel_pct").html());
      $("#ticker_evt").html($("#sel_evt").html());
    }
}

// ----------------------------------------------------------------------------------------
// runs the stopwatch
// modified version by x-tin
// I would have added more credits - but you didn't leave any personal information on the forum ...
// ... so just THX! ;)

function ticktac() {
    sek   = Math.floor((new Date()).getTime()/1000)-startsec-offset;
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
// shows dialogue for editing an item in either customer, project or event list
//
function editSubject(subject,id) {
    var height = 180;
    if (subject == 'pct')
      height = 210;
    floaterShow('floaters.php','add_edit_'+subject,0,id,450,height); return false;
}


// ----------------------------------------------------------------------------------------
// filters project and task fields in add/edit record dialog

function filter_selects(id, needle) {
  var n = new RegExp(needle, 'i');
  
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
    if(cs[i].text.match(n) !== null) $('#'+id).addOption(cs[i].value, cs[i].text);
  }
  $('#'+id).val(selectedValue);
}

// -----------------------------------------------------------------------------

function lists_visible(visible) {
  if (visible) {
    lists_resize();
    $('body>.lists').show();
    lists_resize();
  }
  else
    $('body>.lists').hide();
}

function lists_extShrinkShow() {
    $('#extShrink').css("background-color","red");
}

function lists_extShrinkHide() {
    $('#extShrink').css("background-color","transparent");
}

function lists_kndShrinkShow() {
    $('#kndShrink').css("background-color","red");
}

function lists_kndShrinkHide() {
    $('#kndShrink').css("background-color","transparent");
}

function lists_usrShrinkShow() {
    $('#usrShrink').css("background-color","red");
}

function lists_usrShrinkHide() {
    $('#usrShrink').css("background-color","transparent");
}

function lists_shrinkExtToggle() {
    logfile("extshrink");
    (extShrinkMode)?extShrinkMode=0:extShrinkMode=1;
    if (extShrinkMode) {
        $('#extShrink').css("background-image","url('../skins/"+skin+"/grfx/zefShrink_down.png')");
    } else {
        $('#extShrink').css("background-image","url('../skins/"+skin+"/grfx/zefShrink_up.png')");
    }
    lists_set_heightTop();
    hook_resize();
}

function lists_shrinkKndToggle() {
    logfile("kndshrink");
    (kndShrinkMode)?kndShrinkMode=0:kndShrinkMode=1;
    if (kndShrinkMode) {
        $('#knd, #knd_head, #knd_foot').fadeOut(fading_enabled?"slow":0,lists_set_tableWrapperWidths);
        $('#kndShrink').css("background-image","url('../skins/"+skin+"/grfx/kndShrink_right.png')");
    } else {
		lists_set_tableWrapperWidths();
        $('#knd, #knd_head, #knd_foot').fadeIn(fading_enabled?"slow":0);
        $('#kndShrink').css("background-image","url('../skins/"+skin+"/grfx/kndShrink_left.png')");
		lists_resize();
    }
}

function lists_shrinkUsrToggle() {
    logfile("usrshrink");
    (usrShrinkMode)?usrShrinkMode=0:usrShrinkMode=1;
    if (usrShrinkMode) {
        $('#usr, #usr_head, #usr_foot').fadeOut(fading_enabled?"slow":0,lists_set_tableWrapperWidths);
        $('#usrShrink').css("background-image","url('../skins/"+skin+"/grfx/kndShrink_right.png')");
    } else {
        $('#usr, #usr_head, #usr_foot').fadeIn(fading_enabled?"slow":0);
    lists_set_tableWrapperWidths();
        $('#usrShrink').css("background-image","url('../skins/"+skin+"/grfx/kndShrink_left.png')");
    }
}

function lists_get_dimensions() {
    scroller_width = 17;
    if (navigator.platform.substr(0,3)=='Mac') {
        scroller_width = 16;
    }

    subtableCount=4;
    if (kndShrinkMode) {
      subtableCount--;
    }
    if (usrShrinkMode) {
      subtableCount--;
    }
    subtableWidth = (pageWidth()-10)/subtableCount-7;

    usr_w = subtableWidth-5;
    knd_w = subtableWidth-5; // subtract the space between the panels
    pct_w = subtableWidth-6;
    evt_w = subtableWidth-5;
}

function lists_resize() {
    lists_set_tableWrapperWidths();
    lists_set_heightTop();
}

function lists_set_tableWrapperWidths() {
    lists_get_dimensions();
    $('#extShrink').css("width",pageWidth()-22);
    // set width of faked table heads of subtables -----------------
    $("#usr_head, #usr_foot").css("width",usr_w-5);
    $("#knd_head, #knd_foot").css("width",knd_w-5); // subtract the left padding inside the header
    $("#pct_head, #pct_foot").css("width",pct_w-5); // which is 5px
    $("#evt_head, #evt_foot").css("width",evt_w-5);
    $("#usr").css("width",usr_w);
    $("#knd").css("width",knd_w);
    $("#pct").css("width",pct_w);
    $("#evt").css("width",evt_w);
    lists_set_left();
    lists_set_TableWidths();
}

function lists_set_left() {
    
    // push pct/evt subtables in place LEFT

    leftmargin=0;
    rightmargin=0;
    usrShrinkPos=0;
    if (usrShrinkMode==0) {
      leftmargin+=subtableWidth;
      rightmargin+=7;
      usrShrinkPos+=subtableWidth+7;
    }

    $("#knd, #knd_head, #knd_foot").css("left",leftmargin+rightmargin+10);
    $('#usrShrink').css("left",usrShrinkPos);
    
    kndShrinkPos=usrShrinkPos;

    if (kndShrinkMode==0) {
      leftmargin+=subtableWidth;
      rightmargin+=7;
      kndShrinkPos+=subtableWidth+7;
    }

    $("#pct, #pct_head, #pct_foot").css("left",leftmargin+rightmargin+10);
    
    $("#evt, #evt_head, #evt_foot").css("left",subtableWidth+leftmargin+rightmargin+15); //22
    $('#kndShrink').css("left",kndShrinkPos);
    
}

function lists_set_heightTop() {
    lists_get_dimensions();
    if (!extShrinkMode) {
        $('#gui>div').css("height",pageHeight()-headerHeight()-150-40);
        $("#usr,#knd,#pct,#evt").css("height","160px");
        $("#usr_foot, #knd_foot, #pct_foot, #evt_foot").css("top",pageHeight()-30);
        $('#usrShrink').css("height","211px");
        $('#kndShrink').css("height","211px");
        // push knd/pct/evt subtables in place TOP
        var subs = pageHeight()-headerHeight()-90+25;
        $("#usr,#knd,#pct,#evt").css("top",subs);
        // push faked table heads of subtables in place
        var subs = pageHeight()-headerHeight()-90;    
        $("#usr_head,#knd_head,#pct_head,#evt_head").css("top",subs);
        $('#extShrink').css("top",subs-10);
        $('#usrShrink').css("top",subs);
        $('#kndShrink').css("top",subs);
    } else {
        $("#gui>div").css("height","105px");
        $("#usr_head,#knd_head,#pct_head,#evt_head").css("top",headerHeight()+107);
        $("#usr,#knd,#pct,#evt").css("top",headerHeight()+135);
        $("#usr,#knd,#pct,#evt").css("height",pageHeight()-headerHeight()-165);
        $('#kndShrink').css("height",pageHeight()-headerHeight()-110);
        $('#usrShrink').css("height",pageHeight()-headerHeight()-110);
        $('#extShrink').css("top",headerHeight()+97);
        $('#kndShrink').css("top",headerHeight()+105);
        $('#usrShrink').css("top",headerHeight()+105);
    }
    
    lists_set_TableWidths();
}

function lists_set_TableWidths() {
    lists_get_dimensions();
    // set table widths   
    ($("#usr").innerHeight()-$("#usr table").outerHeight()>0)?scr=0:scr=scroller_width; // same goes for subtables ....
    $("#usr table").css("width",usr_w-scr);
    ($("#knd").innerHeight()-$("#knd table").outerHeight()>0)?scr=0:scr=scroller_width; // same goes for subtables ....
    $("#knd table").css("width",knd_w-scr);
    ($("#pct").innerHeight()-$("#pct table").outerHeight()>0)?scr=0:scr=scroller_width;
    $("#pct table").css("width",pct_w-scr);
    ($("#evt").innerHeight()-$("#evt table").outerHeight()>0)?scr=0:scr=scroller_width;
    $("#evt table").css("width",evt_w-scr);
}

// ----------------------------------------------------------------------------------------
// reloads timesheet, customer, project and event tables
//
function lists_reload(subject) {
    switch (subject) {
        case "usr":
            $.post("processor.php", { axAction: "reload_usr", axValue: 0, id: 0 },
                function(data) {
                    $("#usr").html(data);
                    ($("#usr").innerHeight()-$("#usr table").outerHeight()>0)?scr=0:scr=scroller_width;
                    $("#usr table").css("width",knd_w-scr);
                    lists_live_filter('usr', $('#filt_usr').val());
		    lists_write_annotations('usr');
                }
            );
    break;
        case "knd":
            $.post("processor.php", { axAction: "reload_knd", axValue: 0, id: 0 },
                function(data) {
                    $("#knd").html(data);
                    ($("#knd").innerHeight()-$("#knd table").outerHeight()>0)?scr=0:scr=scroller_width;
                    $("#knd table").css("width",knd_w-scr);
                    lists_live_filter('knd', $('#filt_knd').val());
		    lists_write_annotations('knd');
                }
            );
    break;
        case "pct": 
            $.post("processor.php", { axAction: "reload_pct", axValue: 0, id: 0 },
                function(data) { 
                    $("#pct").html(data);
                    ($("#pct").innerHeight()-$("#pct table").outerHeight()>0)?scr=0:scr=scroller_width;
                    $("#pct table").css("width",pct_w-scr);
                    $('#pct>table>tbody>tr>td>a.preselect#ps'+selected_pct+'>img').attr('src','../skins/'+skin+'/grfx/preselect_on.png');
                    lists_live_filter('pct', $('#filt_pct').val());
		    lists_write_annotations('pct');
                }
            );
    break;
        case "evt": 
            $.post("processor.php", { axAction: "reload_evt", axValue: 0, id: 0, pct:selected_pct },
                function(data) { 
                    $("#evt").html(data);
                    ($("#evt").innerHeight()-$("#evt table").outerHeight()>0)?scr=0:scr=scroller_width;
                    $("#evt table").css("width",evt_w-scr);
                    $('#evt>table>tbody>tr>td>a.preselect#ps'+selected_evt+'>img').attr('src','../skins/'+skin+'/grfx/preselect_on.png');
                    lists_live_filter('evt', $('#filt_evt').val());
		    lists_write_annotations('evt');
        if ($('#row_evt'+selected_evt).length == 0) {
          $('#buzzer').addClass('disabled');
        }
        else {
          $('#buzzer').removeClass('disabled');
        }
                }
            );
    break;
    }
}

// ----------------------------------------------------------------------------------------
//  Live Filter by The One And Only T.C. (TOAOTC) - THX - WOW! ;)
// 
function lists_live_filter(div_list, needle) {
   var n = new RegExp(needle, 'i');
   $('#'+div_list+' tr ').filter(function(index) {
       return ($(this).children('td:nth-child(2)').text().match(n) === null);
   }).css('display','none');
   $('#'+div_list+' tr ').filter(function(index) {
       return ($(this).children('td:nth-child(2)').text().match(n) !== null);
   }).css('display','');
}


function lists_knd_prefilter(knd,type) {
    if (type=="highlight") {
        
        $(".knd").removeClass("filterPctForPreselection");
        $(".pct").removeClass("filterPctForPreselection");
        $("#pct .knd"+knd).addClass("filterPctForPreselection");
        $("#pct .pct").removeClass("TableRowInvisible");

        
    } else {
        
        $(".knd").removeClass("filterPctForPreselection");      
        $(".pct").removeClass("filterPctForPreselection");
        $("#knd .knd"+knd).addClass("filterPctForPreselection");
        $("#pct .pct").removeClass("highlightPctForPreselection");
        if (knd > 0) {
          $("#pct .pct").addClass("TableRowInvisible");
          $("#pct .knd"+knd).removeClass("TableRowInvisible");
        }
        else {
          $("#pct .pct").removeClass("TableRowInvisible");
        }
        
    }
}


// ----------------------------------------------------------------------------------------
//  table row changes color on rollover - preselection link on whole row
//
function lists_change_color(tableRow,highLight) {
  if (highLight) {
    $(tableRow).parents("tr").addClass("highlightPctForPreselection");
  } else {
    $(tableRow).parents("tr").removeClass("highlightPctForPreselection");
  }
}

function lists_update_annotations(id,usr,knd,pct,evt)
{
  lists_ann_usr[id] = usr;
  lists_ann_knd[id] = knd;
  lists_ann_pct[id] = pct;
  lists_ann_evt[id] = evt;

  if ($('.menu li#exttab_'+id).hasClass('act'))
    lists_write_annotations();
}

function lists_write_annotations(part)
{
  var id = parseInt($('#fliptabs li.act').attr('id').substring(7));

  if (!part || part == 'usr') {
    $('#usr>table>tbody td.annotation').html("");
    if (lists_ann_usr[id] != null)
      for (var i in lists_ann_usr[id])
        $('#row_usr'+i+'>td.annotation').html(lists_ann_usr[id][i]);
  }
  if (!part || part == 'knd') {
    $('#knd>table>tbody td.annotation').html("");
    if (lists_ann_knd[id] != null)
      for (var i in lists_ann_knd[id])
        $('#row_knd'+i+'>td.annotation').html(lists_ann_knd[id][i]);
  }
  if (!part || part == 'pct') {
    $('#pct>table>tbody td.annotation').html("");
    if (lists_ann_pct[id] != null)
      for (var i in lists_ann_pct[id])
        $('#row_pct'+i+'>td.annotation').html(lists_ann_pct[id][i]);
  }
  if (!part || part == 'evt') {
    $('#evt>table>tbody td.annotation').html("");
    if (lists_ann_evt[id] != null)
      for (var i in lists_ann_evt[id])
        $('#row_evt'+i+'>td.annotation').html(lists_ann_evt[id][i]);
  }
}

function lists_filter_select_all(subject) {
  $('#'+subject+' tr').each(function(index) {
    if ( !$(this).hasClass('fhighlighted') )
      lists_toggle_filter(subject,parseInt($(this).attr('id').substring(7)));
  });
    hook_filter();
}
function lists_filter_deselect_all(subject) {
  $('#'+subject+' tr').each(function(index) {
    if ( $(this).hasClass('fhighlighted') )
      lists_toggle_filter(subject,parseInt($(this).attr('id').substring(7)));
  });
    hook_filter();
}

function lists_filter_select_invert(subject) {
  $('#'+subject+' tr').each(function(index) {
    lists_toggle_filter(subject,parseInt($(this).attr('id').substring(7)));
  });
    hook_filter();
}

function lists_toggle_filter(subject,id) {
    alreadySelected = $('#row_'+subject+id).hasClass('fhighlighted');
    $('#row_'+subject+id).removeClass('fhighlighted');
    if (alreadySelected) {
        switch (subject) {
        case 'usr':
          filterUsr.splice(filterUsr.indexOf(id),1);
        break;
        case 'knd':
          filterKnd.splice(filterKnd.indexOf(id),1);
          lists_knd_prefilter(0,'filter');
        break;
        case 'pct':
          filterPct.splice(filterPct.indexOf(id),1);
        break;
        case 'evt':
          filterEvt.splice(filterEvt.indexOf(id),1);
        break;
      }
    }
    else
    {
      $('#row_'+subject+id).addClass('fhighlighted');
      switch (subject) {
        case 'usr':
          filterUsr.push(id);
        break;
        case 'knd':
          filterKnd.push(id);
          lists_knd_prefilter(id,'filter');
        break;
        case 'pct':
          filterPct.push(id);
        break;
        case 'evt':
          filterEvt.push(id);
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

function resize_menu() {
  $('#menu').css('width',
    $('#display').position()['left']
    -$('#menu').position()['left']
    -20
    +parseInt($('#display').css('margin-left')));
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
