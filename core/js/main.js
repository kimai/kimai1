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
 * along with Kimai; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
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
// makes floating dialog windows dragable
//
function floaterDragable() {
    $('#floater').DraggableDestroy();
	$('#floater').Draggable({  
			zIndex:20,
			ghosting:false,
			opacity:0.7,
			handle:'#floater_handle'
		});  	
}



// ----------------------------------------------------------------------------------------
// shows floating dialog windows based on processor data
//
function floaterShow(phpFile, axAction, axValue, id, width, height) {
    $('a').blur();    
    if ($('#floater').css("display") == "block") {
        $("#floater").fadeOut(500, function() {
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
          $('a').blur();
          floaterDragable();
          
          $('#floater').css({width: width+"px"});
          $('#floater_content').css({height: height+"px"});
                   
          // width = $('#floater_dimensions').innerWidth()+20;
          // height = $('#floater_dimensions').outerHeight()+5;
          // $('#floater').css({width: width+"px"});
          // $('#floater_content').css({height: height+"px"});
          
          x = ($(document).width()-(width+10))/2 +"px";
          y = ($(document).height()-(height+80))/2 +"px";
          if (y<0) y=0;
          if (x<0) x=0;
          $("#floater").css({left:x,top:y});
          $("#floater").fadeIn(200);
          
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

function floaterOptions() {
    $('a').blur();
    $('.extended').toggle();
    height = $('#floater_dimensions').outerHeight()+5;
    $('#floater_content').css("height",height);
    y = ($(document).height()-(height+80))/2 +"px";
    if (y<0) y=0;
    if (x<0) x=0;
    $("#floater").css({top:y});
}

// ----------------------------------------------------------------------------------------

// ----------------------------------------------------------------------------------------
// hides dialog again
//
function floaterClose() {
    $('#floater').DraggableDestroy();
    $("#floater").fadeOut(500);
}


// ----------------------------------------------------------------------------------------
// shows comment line for timesheet entry
//
function comment(id) {
    $('a').blur();
    $('#c'+id).toggle();
    return false;
}



// ----------------------------------------------------------------------------------------
// change extension by tab
//
function changeTab(target,path) {
    
    kill_reg_timeouts();
    
	$('dd').removeClass('act');
	$('dd').addClass('norm');
	
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
    	path = '../extensions/' + path.replace('../extensions/','') ;
    	$(div).load(path);
	} else {
	    $("#loader").hide();
	}

	$.cookie('ki_active_tab_target', target);
	$.cookie('ki_active_tab_path', path);
}

function kill_timeout(to) {
    evalstring = "try {if (" + to + ") clearTimeout(" + to + ")}catch(e){}";
    // alert(evalstring);
    eval(evalstring);
}


function showTools() {
  $('#main_tools_menu').fadeIn(200);
}

function hideTools() {
  $('#main_tools_menu').fadeOut(200);
}

// $('#main_tools_button').click(function(){
//     $('a').blur();
//     $('#main_tools_menu').fadeIn(200, function() {
//         $("#main_tools_button > img").attr({ src: "../skins/"+skin+"/grfx/g3_menu_dropdown_close.png", width:"16"});
//         $('#main_tools_button').click(function(){
//             $('#main_tools_menu').fadeOut(200);
//         });
//     });
// });




// ----------------------------------------------------------------------------------------
// checks if a new stable Kimai version is available for download
//
function checkupdate(path){
    $.post(path+'checkupdate.php', { versionping: 1 },
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
        
        // um 00:00 neues Datum zeigen
        trigger = Stunden+Minuten+Sekunden;
        if (trigger==0) {
            $('#display_day').html(nextday);
        }
        
        ZeitString = Stunden;
        
        if (Stunden < 10) {
            ZeitString = "0" + Stunden;
        }
        
        if (Sekunden%2==0) {
            ZeitString += ((Minuten < 10) ? n_seperator + "0" : n_seperator) + Minuten;
        } else {
            ZeitString += ((Minuten < 10) ? ":0" : ":") + Minuten;
        }

        $('#n_uhr').html(ZeitString);
        setTimeout("n_uhr()", 1000);
}


function logfile(entry) {
    $.post("processor.php", { axAction: "logfile", axValue: entry, id: 0 });
}




// ----------------------------------------------------------------------------------------
// grabs entered timespace and writes it to database
// after that it reloads all 4 tables
//
function setTimespace(fromDay,fromMonth,fromYear,toDay,toMonth,toYear) {
    $('a').blur();
    timespace = fromMonth +"-"+ fromDay +"-"+ fromYear +"|"+ toMonth +"-"+ toDay +"-"+ toYear;
    $.post("processor.php", { axAction: "setTimespace", axValue: timespace, id: 0 }, 
        function(response) {
            $("#display").html(response);
        }
    );
}





// ----------------------------------------------------------------------------------------
// starts a new recording task when the start-buzzer is hidden
//
function startRecord(pct_ID,evt_ID,usr_ID) {
    hour=0;min=0;sec=0;
    now = Math.floor(((new Date()).getTime())/1000);
    offset = now;
    startsec = 0;
    show_stopwatch();
    value = pct_ID +"|"+ evt_ID;
    $.post("processor.php", { axAction: "startRecord", axValue: value, id: usr_ID},
        function(response){
            ts_ext_reloadSubject('zef');
            $('#noclick').hide();
        }
    );
}



// ----------------------------------------------------------------------------------------
// stops the current recording task when the stop-buzzer is hidden
//
function stopRecord() {
    $("#zeftable>table>tbody>tr>td>a.stop>img").attr("src","../skins/standard/grfx/loading13_red.gif");
    $("#zeftable>table>tbody>tr:first-child>td").css( "background-color", "#F00" );
    $("#zeftable>table>tbody>tr:first-child>td").css( "color", "#FFF" );
    $.post("processor.php", { axAction: "stopRecord", axValue: 0, id: 0},
        function(){
            show_selectors();
            ts_ext_reloadAllTables();
            $('#noclick').hide();
            document.title = default_title;
        }
    );
}

function show_stopwatch() {
    $("#selector").css('display','none');
    $("#stopwatch").css('display','block');
    $("#stopwatch_ticker").css('display','block');
    // $("#h").html("00");
    // $("#m").html("00");
    // $("#s").html("00");
    // $("#button_comment").css('display','block');  
    $("#buzzer").addClass("act");
    $("#ticker_knd").html($("#sel_knd").html());
    $("#ticker_pct").html($("#sel_pct").html());
    $("#ticker_evt").html($("#sel_evt").html());
    $("ul#ticker").newsticker();
    ticktac();
}

function show_selectors() {
    ticktack_off();
    // $("#h").html("00");
    // $("#m").html("00");
    // $("#s").html("00");
    $("#selector").css('display','block');
    $("#stopwatch").css('display','none');
    $("#stopwatch_ticker").css('display','none');
    // $("#button_comment").css('display','none');
    $("#buzzer").removeClass("act");
}

function buzzer() {
    $('#noclick').show();
    if (recstate) {
        stopRecord();
        recstate=0;
    } else {
        startRecord(selected_pct,selected_evt,usr_ID);
        recstate=1;
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
    if (min==0) $("m").html("00");
    else {
        $("#m").html(((min<10)?"0":"")+min);
     }
    if (hour==0) $("h").html("00");
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
        floaterShow('floaters.php','add_edit_'+subject,0,id,450,200); return false;
     // floaterShow('phpFile', 'axAction', axValue, id, width, height)
}

// // ----------------------------------------------------------------------------------------
// // called up when the add/edit evt floater sends data
// //
// function edit_evt_success() {
//     floaterClose();
//     hook_chgEvt();
//     return false;
// }

/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
// everything below this line needs revision for 0.8
/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
/*
// ----------------------------------------------------------------------------------------
// displays the hourglass in the upper right corner when the buzzer is hidden
//
function Eieruhr() {
    $("#buzzer").attr("onClick","return false;");
    $("#buzzer").blur();
    $("#buzzer").css( { background: "url(../skins/" + skin + "/grfx/eieruhr.gif) no-repeat" } )
}
// ----------------------------------------------------------------------------------------
// process hide/show toggle in specify dialogue
//
function hide_item(subject,id) {
    $("#item"+id).blur();
    $("#item"+id).html("<img border='0' width='16' height='13' src='../skins/"+skin+"/grfx/auge_half.png'/>");
    $.post("processor.php", {ax: "specify", subject: subject, hide: id}, 
        function(data) {
            $("#item"+id).html(data);
            $("#item"+id).attr({ onclick: "show_item('"+subject+"',"+id+"); return false;" });
        }
    );
}
function show_item(subject,id) {
    $("#item"+id).blur();
    $("#item"+id).html("<img border='0' width='16' height='13' src='../skins/"+skin+"/grfx/auge_half.png'/>");
    $.post("processor.php", {ax: "specify", subject: subject, show: id}, 
        function(data) {
            $("#item"+id).html(data);
            $("#item"+id).attr({ onclick: "hide_item('"+subject+"',"+id+"); return false;" });
        }
    );
}
// ----------------------------------------------------------------------------------------
// opens dialogue to toggle item visibility in customer, project and event lists 
//
// overlay_show(source,id,width,height)
// in this case the 'id' parameter is abused to commit the 'subject'!
// TODO: change 'id' to 'parameter' or 'value' in overlay_show funktion ...
//
function specify(subject) {
    $('a').blur();
    overlay_show('specList',subject,600,380);
}
*/