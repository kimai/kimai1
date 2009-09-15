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

// =====================================================================
// = Runs when the DOM of the Kimai GUI is loaded => MAIN init script! =
// =====================================================================


var usr_w;
var knd_w;
var pct_w;
var evt_w;

var extShrinkMode = 0; // 0 = show, 1 = hide
var kndShrinkMode = 0; 
var usrShrinkMode = 1; 

var filterUsr = new Array();
var filterKnd = new Array();
var filterPct = new Array();
var filterEvt = new Array();

var lists_visibility = new Array();

$(document).ready(function() {
  
    var preselected_knd = 0;

    // automatic tab-change on reload
    ki_active_tab_target  = $.cookie('ki_active_tab_target_'+usr_ID);
    ki_active_tab_path    = $.cookie('ki_active_tab_path_'+usr_ID);
    if (ki_active_tab_target && ki_active_tab_path ) {
      changeTab(ki_active_tab_target,ki_active_tab_path);
    } else {
      changeTab(0,'ki_timesheets/init.php');
    }
    
    $("#main_tools_button").hoverIntent({
        sensitivity: 7, interval: 300, over: showTools, timeout: 6000, out: hideTools
    });

    $('#main_credits_button').click(function(){
        floaterShow("floaters.php","credits",0,0,650,400);
    });
    
    $('#main_prefs_button').click(function(){
        floaterShow("floaters.php","prefs",0,0,450,300);
    });
    
    $('#buzzer').click(function(){
        buzzer();
    });
 
    n_uhr();
    
    if (recstate==1) {
        show_stopwatch();
    } else {
        show_selectors();
    }

    var lists_resizeTimer = null;
    $(window).bind('resize', function() {
       if (lists_resizeTimer) clearTimeout(lists_resizeTimer);
       lists_resizeTimer = setTimeout(lists_resize, 500);
    });

});



