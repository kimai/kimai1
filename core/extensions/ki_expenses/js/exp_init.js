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
 * Initial javascript code for the timesheet extension.
 * 
 */



// set path of extension
var exp_ext_path = "../extensions/ki_expenses/";
var exp_total = '';

var scroller_width;
var drittel;
var exp_w;
var exp_h;

var exp_tss_hook_flag = 0;
var exp_rec_hook_flag = 0;
var exp_stp_hook_flag = 0;
var exp_chk_hook_flag = 0;
var exp_chp_hook_flag = 0;
var exp_che_hook_flag = 0;

$(document).ready(function(){

    var exp_resizeTimer = null;
    $(window).bind('resize', function() {
       if (exp_resizeTimer) clearTimeout(exp_resizeTimer);
       exp_resizeTimer = setTimeout(exp_ext_resize, 500);
    });

    
});
