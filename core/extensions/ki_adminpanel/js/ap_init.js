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
var adminPanel_extension_path = "../extensions/ki_adminpanel/";

var adminPanel_customers_changed_hook_flag = 0;
var adminPanel_projects_changed_hook_flag = 0;
var adminPanel_activities_changed_hook_flag = 0;
var adminPanel_users_changed_hook_flag = 0;

$(document).ready(function(){
	
	var adminPanel_extension_resizeTimer = null;
	
	$(window).bind('resize', function() {
	   if (adminPanel_extension_resizeTimer) clearTimeout(adminPanel_extension_resizeTimer);
	   adminPanel_extension_resizeTimer = setTimeout(adminPanel_extension_resize, 500);
	});
	
});
