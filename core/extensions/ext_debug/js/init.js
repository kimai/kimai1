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


// set path of extension
var deb_ext_path = "../extensions/ext_debug/";
var deb_ext_refreshTimer = null;

$(document).ready(function(){
	
	var deb_ext_resizeTimer = null;
	
	$(window).bind('resize', function() {
	   if (deb_ext_resizeTimer) clearTimeout(deb_ext_resizeTimer);
	   deb_ext_resizeTimer = setTimeout(deb_ext_resize, 500);
	});
	
});
