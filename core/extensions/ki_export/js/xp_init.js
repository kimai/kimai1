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
var xp_ext_path = "../extensions/ki_export/";

var scroller_width;
var drittel;
var xp_w;
var xp_h;

var xp_tss_hook_flag = 0;
var xp_rec_hook_flag = 0;
var xp_stp_hook_flag = 0;
var xp_chk_hook_flag = 0;
var xp_chp_hook_flag = 0;
var xp_che_hook_flag = 0;

$(document).ready(function(){
    var xp_resizeTimer = null;
    $(window).bind('resize', function() {
       if (xp_resizeTimer) clearTimeout(xp_resizeTimer);
       xp_resizeTimer = setTimeout(xp_ext_resize, 500);
    });
});
