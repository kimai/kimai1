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

function demo_ext_onload() {
    $("#loader").hide();
}

function demo_ext_resize() {
	// function must exist, so init can register it as callback
}

var background 	= new Array("#000000","#FFFF00","#76EE00","#CD3333","#B23AEE","#CDBE70");
var text		= new Array("#FFFFFF","#000000","#000000","#FFFFFF","#FFFFFF","#000000");

function demo_ext_triggerchange() {
    $("#testdiv").append(" This has been put here on tab change.");

    array_target = Math.round(Math.random()*background.length);
    
    $("#testdiv").css("color",text[array_target]);
    $("#testdiv").css("background-color",background[array_target]);
}

function demo_ext_triggerTSS() {
    $.post(demo_ext_path + "processor.php", { axAction: "test", axValue: 0, id: 0 }, 
    function(data) {
        $('#demo_timespace > span.timespace_target').html(data);
    });
}

function demo_ext_triggerREC() {
    logfile("trigger REC demo_ext");
}

function demo_ext_triggerSTP() {
    logfile("trigger STP demo_ext");
}