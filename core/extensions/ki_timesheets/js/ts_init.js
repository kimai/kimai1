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

// ===========
// TS EXT init
// ===========

// set path of extension
var ts_ext_path = "../extensions/ki_timesheets/";
var ts_total = '';

var scroller_width;
var drittel;
var zef_w;
var zef_h;

var ts_tss_hook_flag = 0;
var ts_rec_hook_flag = 0;
var ts_stp_hook_flag = 0;
var ts_chk_hook_flag = 0;
var ts_chp_hook_flag = 0;
var ts_che_hook_flag = 0;



var ts_dayFormatExp = new RegExp("^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{2,4})$");
var ts_timeFormatExp = new RegExp("^([0-9]{1,2})(:[0-9]{1,2})?(:[0-9]{1,2})?$");

$(document).ready(function(){

    var ts_resizeTimer = null;
    $(window).bind('resize', function() {
       if (ts_resizeTimer) clearTimeout(ts_resizeTimer);
       ts_resizeTimer = setTimeout(ts_ext_resize, 500);
    });

    
});
