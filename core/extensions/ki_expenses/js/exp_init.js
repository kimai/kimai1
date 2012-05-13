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
var expense_extension_path = "../extensions/ki_expenses/";
var expenses_total = '';

var scroller_width;
var drittel;
var expenses_width;
var expenses_height;

var expense_tss_hook_flag = 0;
var expense_rec_hook_flag = 0;
var expense_stp_hook_flag = 0;
var expense_chk_hook_flag = 0;
var expense_chp_hook_flag = 0;
var expense_che_hook_flag = 0;

$(document).ready(function(){

    var expense_resizeTimer = null;
    $(window).bind('resize', function() {
       if (expense_resizeTimer) clearTimeout(expense_resizeTimer);
       expense_resizeTimer = setTimeout(expense_extension_resize, 500);
    });

    
});
