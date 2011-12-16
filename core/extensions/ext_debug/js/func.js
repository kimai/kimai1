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

function deb_ext_onload() {
    
    // thx to joern zaefferer! ;) - www.bassistance.de -
    $("#deb_ext_shoutbox_field").focus(function() {
    	if( this.value == this.defaultValue ) {
    		this.value = "";
    	}
    }).blur(function() {
    	if( !this.value.length ) {
    		this.value = this.defaultValue;
    	}
    });
    
    $('#deb_ext_shoutbox').ajaxForm(function() { 
        $('#deb_ext_shoutbox_field').val('');
    });
    
    deb_ext_reloadLogfileLoop();

    deb_ext_resize();
    $("#loader").hide(); 
    
}

function deb_ext_resize() {

	scroller_width = 17;
	if (navigator.platform.substr(0,3)=='Mac') {
	    scroller_width = 16;
	}
	
	pagew = pageWidth();
    halb = (pagew-10)/2-10;

    $("#deb_ext_kga_header").css("width", halb-27);
    $("#deb_ext_kga_header").css("top", headerHeight());
    $("#deb_ext_kga_header").css("left", 10);
    
    $("#deb_ext_kga_wrap").css("top", headerHeight()+30);
    $("#deb_ext_kga_wrap").css("left", 10);
    $("#deb_ext_kga_wrap").css("width", halb-7);
    $("#deb_ext_kga").css("height", pageHeight()-headerHeight()-64);

    $("#deb_ext_logfile_header").css("width", halb-17);
    $("#deb_ext_logfile_header").css("top", headerHeight());
    $("#deb_ext_logfile_header").css("left", halb+15);

    $("#deb_ext_logfile_wrap").css("top", headerHeight()+20);
    $("#deb_ext_logfile_wrap").css("left", halb+5);
    $("#deb_ext_logfile_wrap").css("width", halb+5);
    $("#deb_ext_logfile").css("height", pageHeight()-headerHeight()-64);

}

function deb_ext_triggerchange() {
    deb_ext_reloadLogfileLoop();
}

function deb_ext_reloadLogfileLoop() {
    deb_ext_refreshTimer = setTimeout(deb_ext_reloadLogfileLoop, 2000);
    deb_ext_reloadLogfileOnce();
}

function deb_ext_reloadLogfileOnce() {
    $('a').blur();
    $.post(deb_ext_path + "processor.php", { axAction: "reloadLogfile", axValue: 0, id: 0 }, 
    function(data) {
        $("#deb_ext_logfile").html(data);
    });
}

function deb_ext_reloadKGA() {
    $('a').blur();
    $.post(deb_ext_path + "processor.php", { axAction: "reloadKGA", axValue: 0, id: 0 }, 
    function(data) {
        $("#deb_ext_kga").html(data);
    });
}

function deb_ext_clearTimeout() {
    clearTimeout(deb_ext_refreshTimer);
}

function deb_ext_clearLogfile() {
    $('a').blur();
    $.post(deb_ext_path + "processor.php", { axAction: "clearLogfile", axValue: 0, id: 0 }, 
    function(data) {
        $("#deb_ext_logfile").html(data);
    });
}

