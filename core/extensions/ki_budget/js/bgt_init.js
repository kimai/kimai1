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

// ============
// BGT EXT init
// ============

// set path of extension
var bgt_ext_path = "../extensions/ki_budget/";

var bgt_w;
var bgt_h;

var chartColors;

$(document).ready(function(){

    var bgt_resizeTimer = null;
    $(window).bind('resize', function() {
       if (bgt_resizeTimer) clearTimeout(bgt_resizeTimer);
       bgt_resizeTimer = setTimeout(bgt_ext_resize, 500);
    });

    $.jqplot.config.enablePlugins = true;

    
});
