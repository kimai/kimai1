/* -*- Mode: jQuery; tab-width: 4; indent-tabs-mode: nil -*- */
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

// =====================================================================
// = Runs when the DOM of the Kimai GUI is loaded => MAIN init script! =
// =====================================================================

var userColumnWidth;
var customerColumnWidth;
var projectColumnWidth;
var activityColumnWidth;

var currentDay = (new Date()).getDate();

var fading_enabled = true;

var extensionShrinkMode = 0; // 0 = show, 1 = hide
var customerShrinkMode = 0;
var userShrinkMode = 0;

var filterUsers = [];
var filterCustomers = [];
var filterProjects = [];
var filterActivities = [];

var lists_visibility = [];

var lists_user_annotations = {};
var lists_customer_annotations = {};
var lists_project_annotations = {};
var lists_activity_annotations = {};

$(document).ready(function () {

	var preselected_customer = 0;

	if (userID) {
		// automatic tab-change on reload
		ki_active_tab_target = $.cookie('ki_active_tab_target_' + userID);
		ki_active_tab_path = $.cookie('ki_active_tab_path_' + userID);
	}
	else {
		ki_active_tab_target = null;
		ki_active_tab_path = null;
	}
	if (ki_active_tab_target && ki_active_tab_path) {
		changeTab(ki_active_tab_target, ki_active_tab_path);
	} else {
		changeTab(0, 'ki_timesheets/init.php');
	}

	$('#main_tools_button').hoverIntent({
		sensitivity: 7, interval: 300, over: showTools, timeout: 6000, out: hideTools
	});

	$('#main_credits_button').click(function () {
		this.blur();
		floaterShow('floaters.php', 'credits', 0, 0, 650);
		return false;
	});

	$('#main_prefs_button').click(function () {
		this.blur();
		floaterShow('floaters.php', 'prefs', 0, 0, 450);
		return false;
	});


	$('#buzzer').click(function () {
		buzzer();
	});

	if (currentRecording > -1 || (selected_customer && selected_project && selected_activity)) {
		$('#buzzer').removeClass('disabled');
	}

	n_uhr();

	if (currentRecording > -1) {
		show_stopwatch();
	} else {
		show_selectors();
	}

	var lists_resizeTimer = null;
	$(window).bind('resize', function () {
		resize_menu();
		resize_floater();

        if (lists_resizeTimer) {
            clearTimeout(lists_resizeTimer);
        }
        lists_resizeTimer = setTimeout(lists_resize, 500);
	});

	// Implement missing method for browsers like IE.
	// thanks to http://stellapower.net/content/javascript-support-and-arrayindexof-ie
	if (!Array.indexOf) {
		Array.prototype.indexOf = function (obj, start) {
			for (var i = (start || 0); i < this.length; i++) {
				if (this[i] == obj) {
					return i;
				}
			}
			return -1;
		}
	}
});