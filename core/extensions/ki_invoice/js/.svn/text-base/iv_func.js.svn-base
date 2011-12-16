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

function iv_ext_onload() {
        
    iv_ext_resize();
    $("#loader").hide(); 
    
}

function iv_ext_resize() {

	scroller_width = 17;
	if (navigator.platform.substr(0,3)=='Mac') {
	    scroller_width = 16;
	}
	pagew = pageWidth()-15;

    $("#iv_ext_header").css("width", pagew-27);
    $("#iv_ext_header").css("top", headerHeight());
    $("#iv_ext_header").css("left", 10);
    
    $("#iv_ext_wrap").css("top", headerHeight()+30);
    $("#iv_ext_wrap").css("left", 10);
    $("#iv_ext_wrap").css("width", pagew-7);
    $("#iv_ext").css("height", pageHeight()-headerHeight()-64);
}

function iv_ext_triggerchange() {
	iv_ext_resize();
}

function iv_ext_triggerTSS() {
   $.post(iv_ext_path + "processor.php", { axAction: "reload_timespan" },
                function(data) { 
                    $("#iv_timespan").html(data);
                }
            );
}