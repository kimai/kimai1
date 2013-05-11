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
// XP EXT init
// ===========

// set path of extension
var export_extension_path = "../extensions/ki_export/";

var scroller_width;
var drittel;
var export_width;
var export_height;

var xp_timeframe_changed_hook_flag = 0;
var xp_customers_changed_hook_flag = 0;
var xp_projects_changed_hook_flag = 0;
var xp_activities_changed_hook_flag = 0;

$(document).ready(function(){
    var export_resizeTimer = null;
    $(window).bind('resize', function() {
       if (export_resizeTimer) clearTimeout(export_resizeTimer);
       export_resizeTimer = setTimeout(export_extension_resize, 500);
    });
});
