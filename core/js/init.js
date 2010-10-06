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

// =====================================================================
// = Runs when the DOM of the Kimai GUI is loaded => MAIN init script! =
// =====================================================================


var usr_w;
var knd_w;
var pct_w;
var evt_w;

var currentDay = (new Date()).getDate();

var fading_enabled = true;

var extShrinkMode = 0; // 0 = show, 1 = hide
var kndShrinkMode = 0; 
var usrShrinkMode = 0; 

var filterUsr = new Array();
var filterKnd = new Array();
var filterPct = new Array();
var filterEvt = new Array();

var lists_visibility = new Array();

var lists_ann_usr = new Array();
var lists_ann_knd = new Array();
var lists_ann_pct = new Array();
var lists_ann_evt = new Array();

$(document).ready(function() {
  
    var preselected_knd = 0;

    if (usr_ID) {
      // automatic tab-change on reload
      ki_active_tab_target  = $.cookie('ki_active_tab_target_'+usr_ID);
      ki_active_tab_path    = $.cookie('ki_active_tab_path_'+usr_ID);
    }
    else {
      ki_active_tab_target  = null;
      ki_active_tab_path    = null;
    }
    if (ki_active_tab_target && ki_active_tab_path ) {
      changeTab(ki_active_tab_target,ki_active_tab_path);
    } else {
      changeTab(0,'ki_timesheets/init.php');
    }
    
    $("#main_tools_button").hoverIntent({
        sensitivity: 7, interval: 300, over: showTools, timeout: 6000, out: hideTools
    });

    $('#main_credits_button').click(function(){
        this.blur();
        floaterShow("floaters.php","credits",0,0,650,400);
    });
    
    $('#main_prefs_button').click(function(){
        this.blur();
        floaterShow("floaters.php","prefs",0,0,450,530);
    });

    
    $('#buzzer').click(function(){
      buzzer();
    });

    if (recstate==1 || (selected_knd && selected_pct && selected_evt)) {
      $('#buzzer').removeClass('disabled');
    }
 
    n_uhr();
    
    if (recstate==1) {
        show_stopwatch();
    } else {
        show_selectors();
    }

    var lists_resizeTimer = null;
    $(window).bind('resize', function() {

	resize_menu();

       if (lists_resizeTimer) clearTimeout(lists_resizeTimer);
       lists_resizeTimer = setTimeout(lists_resize, 500);
    });
    
    // Implement missing method for browsers like IE.
    // thanks to http://stellapower.net/content/javascript-support-and-arrayindexof-ie
    if (!Array.indexOf) {
      Array.prototype.indexOf = function (obj, start) {
        for (var i = (start || 0); i < this.length; i++) {
          if (this[i] == obj) {
            return i;
          }
        }
        return -1;
      }
    }

});



