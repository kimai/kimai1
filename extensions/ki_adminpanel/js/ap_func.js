/**
 * This file is part of
 * Kimai - Open Source Time Tracking // https://www.kimai.org
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
// ADMIN PANEL
// ===========

function adminPanel_extension_onload() {
	adminPanel_extension_resize();
	subactivelink = '#adminPanel_extension_sub1';
	$('#loader').hide();
}

function adminPanel_extension_resize() {
	scroller_width = 14;
	if (navigator.platform.substr(0, 3) === 'Mac') {
		scroller_width = 16;
	}

	pagew = pageWidth();
	drittel = (pagew - 10) / 3 - 7;

	panel_w = pagew - 24;
	panel_h = pageHeight() - 10 - headerHeight();

	$('.adminPanel_extension_subtab').css('display', 'none');

	$('#adminPanel_extension_panel').css('width', panel_w);
	$('.adminPanel_extension_panel_header').css('width', panel_w);
	$('#adminPanel_extension_panel').css('height', panel_h);

	$('.adminPanel_extension_subtab').css('height', panel_h - (10*25)-20-1);

	adminPanel_extension_subtab_autoexpand();
}

/**
 * Show one of the subtabs. All others are collapsed, so only their header
 * is visible.
 */
function adminPanel_extension_subtab_expand(id) {
	$('div[id^="adminPanel_extension_sub"]').removeClass('active');
	$('.adminPanel_extension_subtab').css('display', 'none');

	sub_id = '#adminPanel_extension_sub' + id;
	$(sub_id).addClass('active');

	subtab = '#adminPanel_extension_s' + id;
	$(subtab).css('display', 'block');

	Cookies.set('adminPanel_extension_activePanel_' + userID, id);
}

/**
 * Show the last subtab, the user has seen. This information is stored in a
 * cookie. If we're unable to read it show the first subtab.
 */
function adminPanel_extension_subtab_autoexpand() {
	adminPanel_extension_activePanel = Cookies.get('adminPanel_extension_activePanel_' + userID);
	if (adminPanel_extension_activePanel) {
		adminPanel_extension_subtab_expand(adminPanel_extension_activePanel);
	} else {
		adminPanel_extension_subtab_expand(1);
	}
}

function adminPanel_extension_tab_changed() {
	if ($('.adminPanel_extension').css('display') === 'block') {
		adminPanel_extension_refreshSubtab('customers');
		adminPanel_extension_refreshSubtab('projects');
		adminPanel_extension_refreshSubtab('activities');
	} else {
		tss_hook_flag++;
	}
	if (adminPanel_customers_changed_hook_flag) {
		adminPanel_extension_customers_changed();
	}
	if (adminPanel_projects_changed_hook_flag) {
		adminPanel_extension_projects_changed();
	}
	if (adminPanel_activities_changed_hook_flag) {
		adminPanel_extension_activities_changed();
	}
	if (adminPanel_users_changed_hook_flag) {
		adminPanel_extension_users_changed();
	}

	adminPanel_customers_changed_hook_flag = 0;
	adminPanel_projects_changed_hook_flag = 0;
	adminPanel_activities_changed_hook_flag = 0;
	adminPanel_users_changed_hook_flag = 0;
}

function adminPanel_extension_customers_changed() {
	if ($('.adminPanel_extension').css('display') === 'block') {
		adminPanel_extension_refreshSubtab('customers');
		adminPanel_extension_refreshSubtab('projects');
	} else {
		adminPanel_customers_changed_hook_flag++;
	}
}

function adminPanel_extension_projects_changed() {
	if ($('.adminPanel_extension').css('display') === 'block') {
		adminPanel_extension_refreshSubtab('projects');
	} else {
		adminPanel_projects_changed_hook_flag++;
	}
}

function adminPanel_extension_activities_changed() {
	if ($('.adminPanel_extension').css('display') === 'block') {
		adminPanel_extension_refreshSubtab('activities');
	} else {
		adminPanel_activities_changed_hook_flag++;
	}
}

function adminPanel_extension_users_changed() {
	if ($('.adminPanel_extension').css('display') === 'block') {
		adminPanel_extension_refreshSubtab('users');
	} else {
		adminPanel_users_changed_hook_flag++;
	}
}

// ----------------------------------------------------------------------------------------
// graps the value of the newUser input field
// and ajaxes it to the createUser function of the processor
//
function adminPanel_extension_newUser() {
	var newuser = $('#newuser').val();
	if (newuser === '') {
		alert(lang_checkUsername);
		return false;
	}
	$.post(adminPanel_extension_path + 'processor.php', { axAction: 'createUser', axValue: newuser, id: 0 },
		function(data) {
			if (data.userId === false) {
				alert(data.errors.join("\n"));
				return;
			}

			adminPanel_extension_refreshSubtab('users');
			adminPanel_extension_editUser(data.userId);
		});
}

function adminPanel_extension_showDeletedUsers() {
	$.post(adminPanel_extension_path + 'processor.php', { axAction: 'toggleDeletedUsers', axValue: 1, id: 0 },
		function(data) {
			adminPanel_extension_refreshSubtab('users');
		});
}

function adminPanel_extension_hideDeletedUsers() {
	$.post(adminPanel_extension_path + 'processor.php', { axAction: 'toggleDeletedUsers', axValue: 0, id: 0 },
		function(data) {
			adminPanel_extension_refreshSubtab('users');
		});
}


// ----------------------------------------------------------------------------------------
// graps the value of the newGroup input field
// and ajaxes it to the createGroup function of the processor
//
function adminPanel_extension_newGroup() {
	var newgroup = $('#newgroup').val();
	if (newgroup === '') {
		alert(lang_checkGroupname);
		return false;
	}
	$.post(adminPanel_extension_path + 'processor.php', { axAction: 'createGroup', axValue: newgroup, id: 0 },
		function(data) {
			adminPanel_extension_refreshSubtab('groups');
		});
}

//----------------------------------------------------------------------------------------
//graps the value of the newGroup input field
//and ajaxes it to the createGroup function of the processor
//
function adminPanel_extension_newStatus() {
	var newstatus = $('#newstatus').val();
	if (newstatus === '') {
		alert(lang_checkStatusname);
		return false;
	}
	$.post(adminPanel_extension_path + 'processor.php', { axAction: 'createStatus', axValue: newstatus, id: 0 },
		function(data) {
			adminPanel_extension_refreshSubtab('status');
		});
}

//----------------------------------------------------------------------------------------
//graps the value of the newGlobalRole input field
//and ajaxes it to the createGlobalRole function of the processor
//
function adminPanel_extension_newGlobalRole() {
	var newGlobalRole = $('#newGlobalRole').val();
	if (newGlobalRole === '') {
		alert(lang_checkGlobalRoleName);
		return false;
	}
	$.post(adminPanel_extension_path + 'processor.php', { axAction: 'createGlobalRole', axValue: newGlobalRole, id: 0 },
		function(data) {
			if (data.errors.length > 0) {
				alert(data.errors.join("\n"));
			} else {
				adminPanel_extension_refreshSubtab('globalRoles');
			}
		});
}

//----------------------------------------------------------------------------------------
//graps the value of the newMembershipRole input field
//and ajaxes it to the createMembershipRole function of the processor
//
function adminPanel_extension_newMembershipRole() {
	var newMembershipRole = $('#newMembershipRole').val();
	if (newMembershipRole === '') {
		alert(lang_checkMembershipRoleName);
		return false;
	}
	$.post(adminPanel_extension_path + 'processor.php', { axAction: 'createMembershipRole', axValue: newMembershipRole, id: 0 },
		function(data) {
			if (data.errors.length > 0) {
				alert(data.errors.join("\n"));
			} else {
				adminPanel_extension_refreshSubtab('membershipRoles');
			}
		});
}

// ----------------------------------------------------------------------------------------
// by clicking on the edit button of a user the edit dialogue pops up
//
function adminPanel_extension_editUser(id) {
	floaterShow(adminPanel_extension_path + 'floaters.php','editUser',0,id,400);
}

// ----------------------------------------------------------------------------------------
// by clicking on the edit button of a group the edit dialogue pops up
//
function adminPanel_extension_editGroup(id) {
	floaterShow(adminPanel_extension_path + 'floaters.php','editGroup',0,id,450);
}

//----------------------------------------------------------------------------------------
//by clicking on the edit button of a status the edit dialogue pops up
//
function adminPanel_extension_editStatus(id) {
	floaterShow(adminPanel_extension_path + 'floaters.php','editStatus',0,id,450);
}

//----------------------------------------------------------------------------------------
//by clicking on the edit button of a global role the edit dialogue pops up
//
function adminPanel_extension_editGlobalRole(id) {
	floaterShow(adminPanel_extension_path + 'floaters.php','editGlobalRole',0,id,550, function() {
		$('.floatingTabLayout').each(doFloatingTabLayout);
	});
}

//----------------------------------------------------------------------------------------
//by clicking on the edit button of a membership role the edit dialogue pops up
//
function adminPanel_extension_editMembershipRole(id) {
	floaterShow(adminPanel_extension_path + 'floaters.php','editMembershipRole',0,id,550, function() {
		$('.floatingTabLayout').each(doFloatingTabLayout);
	});
}

// ----------------------------------------------------------------------------------------
// refreshes either user/group/advanced/DB subtab
//
function adminPanel_extension_refreshSubtab(tab) {
	var options = { axAction: 'refreshSubtab', axValue: tab, id: 0 };
	if (tab === 'activities') {
		options.activity_filter = $('#activity_project_filter').val();
	}
	$.post(adminPanel_extension_path + 'processor.php', options,
		function(data) {
			switch(tab) {
				case 'users': target = '#adminPanel_extension_s1'; break;
				case 'groups': target = '#adminPanel_extension_s2'; break;
				case 'status': target = '#adminPanel_extension_s3'; break;
				case 'database': target = '#adminPanel_extension_s5'; break;
				case 'customers': target = '#adminPanel_extension_s6'; break;
				case 'projects': target = '#adminPanel_extension_s7'; break;
				case 'activities': target = '#adminPanel_extension_s8'; break;
				case 'globalRoles': target = '#adminPanel_extension_s9'; break;
				case 'membershipRoles': target = '#adminPanel_extension_s10'; break;
			}
			$(target).html(data);
		});
}

// ----------------------------------------------------------------------------------------
// delete user
//
function adminPanel_extension_deleteUser(id, trash) {
	if (!confirm(lang_sure)) return;

	$.post(adminPanel_extension_path + 'processor.php', {axAction: 'deleteUser', axValue: trash, id: id },
		function() {
			adminPanel_extension_refreshSubtab('users');
			adminPanel_extension_refreshSubtab('groups');
			hook_users_changed(); }
	);
}

// ----------------------------------------------------------------------------------------
// delete group
//
function adminPanel_extension_deleteGroup(id) {
	if (!confirm(lang_sure)) return;

	$.post(adminPanel_extension_path + 'processor.php', { axAction: 'deleteGroup', id: id },
		function(result) {
			var error = result['errors'][''];

			if (error) {
				alert(error);
			} else {
				adminPanel_extension_refreshSubtab('groups');
			}
		}
	);
}

//----------------------------------------------------------------------------------------
//delete status
//
function adminPanel_extension_deleteStatus(id) {
	if (!confirm(lang_sure)) return;

	$.post(adminPanel_extension_path + 'processor.php', {axAction: 'deleteStatus', id: id },
		function() { adminPanel_extension_refreshSubtab('status'); }
	);
}

// ----------------------------------------------------------------------------------------
// delete project
//
function adminPanel_extension_deleteProject(id) {
	if (!confirm(lang_sure)) return;

	if (currentRecording == -1 && selected_project == id) {
		$('#buzzer').addClass('disabled');
		selected_project = false;
		$('#sel_project').html('');
	}

	$.post(adminPanel_extension_path + 'processor.php', {axAction: 'deleteProject', id: id },
		function() { adminPanel_extension_refreshSubtab('projects');
			hook_projects_changed(); }
	);
}

// ----------------------------------------------------------------------------------------
// delete customer
//
function adminPanel_extension_deleteCustomer(id) {
	if (!confirm(lang_sure)) return;

	if (currentRecording == -1 && selected_customer == id) {
		$('#buzzer').addClass('disabled');
		selected_customer = false;
		$('#sel_customer').html('');
	}

	$.post(adminPanel_extension_path + 'processor.php', {axAction: 'deleteCustomer', id: id },
		function() { adminPanel_extension_refreshSubtab('customers');
			hook_customers_changed(); }
	);
}

// ----------------------------------------------------------------------------------------
// delete activity
//
function adminPanel_extension_deleteActivity(id) {
	if (!confirm(lang_sure)) return;

	if (currentRecording == -1 && selected_activity == id) {
		$('#buzzer').addClass('disabled');
		selected_activity = false;
		$('#selected_activity').html('');
	}

	$.post(adminPanel_extension_path + 'processor.php', {axAction: 'deleteActivity', id: id },
		function() { adminPanel_extension_refreshSubtab('activities');
			hook_activities_changed(); }
	);
}

//----------------------------------------------------------------------------------------
//delete global role
//
function adminPanel_extension_deleteGlobalRole(id) {
	if (!confirm(lang_sure)) return;

	$.post(adminPanel_extension_path + 'processor.php', {axAction: 'deleteGlobalRole', id: id },
		function() { adminPanel_extension_refreshSubtab('globalRoles'); }
	);
}

//----------------------------------------------------------------------------------------
//delete membership role
//
function adminPanel_extension_deleteMembershipRole(id) {
	if (!confirm(lang_sure)) return;

	$.post(adminPanel_extension_path + 'processor.php', {axAction: 'deleteMembershipRole', id: id },
		function() { adminPanel_extension_refreshSubtab('membershipRoles'); }
	);
}

// ----------------------------------------------------------------------------------------
// activates user for login
//
function adminPanel_extension_unbanUser(id) {
    var $banId = $('#ban'+id);
	$banId.blur();
	$banId.html('<img border="0" width="16" height="16" src="../skins/' + skin + '/grfx/loading13.gif"/>');
	$.post(adminPanel_extension_path + 'processor.php', { axAction: 'unbanUser', axValue: 0, id: id },
		function(data) {
			$banId.html(data);
			$banId.attr({ 'onclick': 'adminPanel_extension_banUser("' + id + '"); return false;' });
		}
	);
}

// ----------------------------------------------------------------------------------------
// toggle ban and unban of users in admin panel
//
function adminPanel_extension_banUser(id) {
	var $banId = $('#ban'+id);
	$banId.blur();
	$banId.html('<img border="0" width="16" height="16" src="../skins/' + skin + '/grfx/loading13.gif"/>');
	$.post(adminPanel_extension_path + 'processor.php', { axAction: 'banUser', axValue: 0, id: id },
		function(data) {
			$banId.html(data);
			$banId.attr({ 'onclick': 'adminPanel_extension_unbanUser("' + id + '"); return false;' });
		}
	);
}

function adminPanel_extension_checkupdate() {
	$.post('checkupdate.php',
		function(data) {
			$('#adminPanel_extension_checkupdate').html(data);
		}
	);
}
