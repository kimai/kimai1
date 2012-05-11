<?php
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
// ================
// = AP PROCESSOR =
// ================
// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";
require ("../../includes/kspi.php");
switch ($axAction) {
	case "createUsr" :
		// create new user account
		$usr_data['name'] = trim($axValue);
		$usr_data['status'] = 2;
		$usr_data['active'] = 0;
		$userId = $database->user_create($usr_data);
		$database->setGroupMemberships($userId, $kga['usr']['groups']);
		echo $userId;
		break;
	case "createStatus" :
		// create new status
		$status_data['status'] = trim($axValue);
		$new_status_id = $database->status_create($status_data);
		break;
	case "createGrp" :
		// create new group
		$grp_data['name'] = trim($axValue);
		$new_grp_id = $database->group_create($grp_data);
		if ($new_grp_id != false) {
			$database->assign_groupToGroupleaders($new_grp_id, array($kga['usr']['userID']));
		}
		break;
	case "refreshSubtab" :
		// builds either user/group/advanced/DB subtab
		$tpl->assign('curr_user', $kga['usr']['name']);
		if ($kga['usr']['status'] == 0)
			$tpl->assign('arr_grp', $database->get_arr_groups(get_cookie('ap_ext_show_deleted_groups', 0)));
		else
			$tpl->assign('arr_grp', $database->get_arr_groups_by_leader($kga['usr']['userID'], get_cookie('ap_ext_show_deleted_groups', 0)));
		if ($kga['usr']['status'] == 0)
			$arr_usr = $database->get_arr_users(get_cookie('ap_ext_show_deleted_users', 0));
		else
			$arr_usr = $database->get_arr_watchable_users($kga['usr']);
			// get group names
		foreach ($arr_usr as &$user) {
			$groups = $database->getGroupMemberships($user['userID']);
			if(is_array($groups)) {
			foreach ($groups as $group) {
				$groupData = $database->group_get_data($group);
				$user['groups'][] = $groupData['name'];
			}
			}
		}
		$arr_status = $database->get_arr_statuses();
		$tpl->assign('arr_usr', $arr_usr);
		$tpl->assign('arr_status', $arr_status);
		$tpl->assign('showDeletedGroups', get_cookie('ap_ext_show_deleted_groups', 0));
		$tpl->assign('showDeletedUsers', get_cookie('ap_ext_show_deleted_users', 0));
		switch ($axValue) {
			case "usr" :
				$tpl->display("users.tpl");
				break;
			case "grp" :
				$tpl->display("groups.tpl");
				break;
			case "status" :
				$tpl->display("status.tpl");
				break;
			case "adv" :
				if ($kga['conf']['editLimit'] != '-') {
					$tpl->assign('editLimitEnabled', true);
					$editLimit = $kga['conf']['editLimit'] / (60 * 60); // convert to hours
					$tpl->assign('editLimitDays', (int) ($editLimit / 24));
					$tpl->assign('editLimitHours', (int) ($editLimit % 24));
				}
				else {
					$tpl->assign('editLimitEnabled', false);
					$tpl->assign('editLimitDays', '');
					$tpl->assign('editLimitHours', '');
				}
				$tpl->display("advanced.tpl");
				break;
			case "db" :
				$tpl->display("database.tpl");
				break;
			case "knd" :
				if ($kga['usr']['status'] == 0)
					$arr_knd = $database->get_arr_customers();
				else
					$arr_knd = $database->get_arr_customers($kga['usr']['groups']);
				foreach ($arr_knd as $row => $knd_data) {
					$grp_names = array();
					$groups = $database->customer_get_groupIDs($knd_data['customerID']);
					if ($groups !== false) {
						foreach ($groups as $groupID) {
							$data = $database->group_get_data($groupID);
							$grp_names[] = $data['name'];
						}
						$arr_knd[$row]['groups'] = implode(", ", $grp_names);
					}
				}
				if (count($arr_knd) > 0) {
					$tpl->assign('arr_knd', $arr_knd);
				}
				else {
					$tpl->assign('arr_knd', '0');
				}
				$tpl->display("knd.tpl");
				break;
			case "pct" :
				if ($kga['usr']['status'] == 0)
					$arr_pct = $database->get_arr_projects();
				else
					$arr_pct = $database->get_arr_projects($kga['usr']['groups']);
				foreach ($arr_pct as $row => $pct_data) {
					$grp_names = array();
					foreach ($database->project_get_groupIDs($pct_data['projectID']) as $groupID) {
						$data = $database->group_get_data($groupID);
						$grp_names[] = $data['name'];
					}
					$arr_pct[$row]['groups'] = implode(", ", $grp_names);
				}
				if (count($arr_pct) > 0) {
					$tpl->assign('arr_pct', $arr_pct);
				}
				else {
					$tpl->assign('arr_pct', '0');
				}
				$tpl->display("pct.tpl");
				break;
			case "evt" :
				if ($kga['usr']['status'] == 0)
					$groups = null;
				else
					$groups = $kga['usr']['groups'];
				if (! isset($_REQUEST['filter']))
					$arr_evt = $database->get_arr_activities($groups);
				else
					switch ($_REQUEST['filter']) {
						case - 1 :
							$arr_evt = $database->get_arr_activities($groups);
							break;
						case - 2 :
						// -2 is to get unassigned events. As -2 is never
						// an id of a project this will give us all unassigned
						// events.
						default :
							$arr_evt = $database->get_arr_activities_by_project($_REQUEST['filter'], $groups);
					}
				foreach ($arr_evt as $row => $evt_data) {
					$grp_names = array();
					foreach ($database->activity_get_groups($evt_data['activityID']) as $grp_id) {
						$data = $database->group_get_data($grp_id);
						$grp_names[] = $data['name'];
					}
					$arr_evt[$row]['groups'] = implode(", ", $grp_names);
				}
				if (count($arr_evt) > 0) {
					$tpl->assign('arr_evt', $arr_evt);
				}
				else {
					$tpl->assign('arr_evt', '0');
				}
				$arr_pct = $database->get_arr_projects($groups);
				$tpl->assign('arr_pct', $arr_pct);
				$tpl->assign('selected_evt_filter', $_REQUEST['filter']);
				$tpl->display("evt.tpl");
				break;
		}
		break;
	case "deleteUsr" :
		// set the trashflag of a user
		switch ($axValue) {
			case 0 :
				// Fire JavaScript confirm when a user is about to be deleted
				echo $kga['lang']['sure'];
				break;
			case 1 :
				// If the confirmation is returned the user gets the trash-flag. 
				// TODO: Users with trashflag can be deleted by 'empty trashcan' or so ...
				$database->user_delete($id);
				break;
		}
		break;
	case "deleteGrp" :
		// set the trashflag of a group
		switch ($axValue) {
			case 0 :
				// Fire JavaScript confirm when a group is about to be deleted
				echo $kga['lang']['sure'];
				break;
			case 1 :
				// If the confirmation is returned the group gets the trash-flag. 
				// TODO: Users with trashflag can be deleted by 'empty trashcan' or so ...
				$database->group_delete($id);
				break;
		}
		break;
	case "deleteStatus" :
		// set the trashflag of a group
		switch ($axValue) {
			case 0 :
				// Fire JavaScript confirm when a status is about to be deleted
				echo $kga['lang']['sure'];
				break;
			case 1 :
				// If the confirmation is returned the status gets deleted. 
				$database->status_delete($id);
				break;
		}
		break;
	case "deletePct" :
		// set the trashflag of a project
		switch ($axValue) {
			case 0 :
				// Fire JavaScript confirm when a project is about to be deleted
				echo $kga['lang']['sure'];
				break;
			case 1 :
				// If the confirmation is returned the project gets the trash-flag. 
				$database->project_delete($id);
				break;
		}
		break;
	case "deleteKnd" :
		// set the trashflag of a customer
		switch ($axValue) {
			case 0 :
				// Fire JavaScript confirm when a customer is about to be deleted
				echo $kga['lang']['sure'];
				break;
			case 1 :
				// If the confirmation is returned the customer gets the trash-flag. 
				$database->customer_delete($id);
				break;
		}
		break;
	case "deleteEvt" :
		// set the trashflag of an event
		switch ($axValue) {
			case 0 :
				// Fire JavaScript confirm when an event is about to be deleted
				echo $kga['lang']['sure'];
				break;
			case 1 :
				// If the confirmation is returned the event gets the trash-flag. 
				$database->activity_delete($id);
				break;
		}
		break;
	case "banUsr" :
		// Ban a user from login
		$sts['active'] = 0;
		$database->user_edit($id, $sts);
		echo sprintf("<img border='0' title='%s' alt='%s' src='../skins/%s/grfx/lock.png' width='16' height='16' />", $kga['lang']['bannedusr'], $kga['lang']['bannedusr'], $kga['conf']['skin']);
		break;
	case "unbanUsr" :
		// Unban a user from login
		$sts['active'] = 1;
		$database->user_edit($id, $sts);
		echo sprintf("<img border='0' title='%s' alt='%s' src='../skins/%s/grfx/jipp.gif' width='16' height='16' />", $kga['lang']['activeusr'], $kga['lang']['activeusr'], $kga['conf']['skin']);
		break;
	case "sendEditUsr" :
		// process editUsr form
		$usr_data['name'] = trim($_REQUEST['name']);
		$usr_data['sts'] = $_REQUEST['status'];
		$usr_data['mail'] = $_REQUEST['mail'];
		$usr_data['alias'] = $_REQUEST['alias'];
		$usr_data['rate'] = $_REQUEST['rate'];
		// if password field is empty => password unchanged (not overwritten with "")
		if ($_REQUEST['password'] != "") {
			$usr_data['password'] = md5($kga['password_salt'] . $_REQUEST['password'] . $kga['password_salt']);
		}
		$database->user_edit($id, $usr_data);
		$database->setGroupMemberships($id, $_REQUEST['groups']);
		break;
	case "sendEditGrp" :
		// process editGrp form
		$grp_data['name'] = trim($_REQUEST['name']);
		$database->group_edit($id, $grp_data);
		$ldrs = $_REQUEST['leaders'];
		$database->assign_groupToGroupleaders($id, $ldrs);
		break;
	case "sendEditStatus" :
		// process editStatus form
		$status_data['status'] = trim($_REQUEST['status']);
		$database->status_edit($id, $status_data);
		break;
	case "sendEditAdvanced" :
		// process AdvancedOptions form
		$var_data['adminmail'] = $_REQUEST['adminmail'];
		$var_data['loginTries'] = $_REQUEST['logintries'];
		$var_data['loginBanTime'] = $_REQUEST['loginbantime'];
		$var_data['show_sensible_data'] = isset($_REQUEST['show_sensible_data']);
		$var_data['show_update_warn'] = isset($_REQUEST['show_update_warn']);
		$var_data['check_at_startup'] = isset($_REQUEST['check_at_startup']);
		$var_data['show_daySeperatorLines'] = isset($_REQUEST['show_daySeperatorLines']);
		$var_data['show_gabBreaks'] = isset($_REQUEST['show_gabBreaks']);
		$var_data['show_RecordAgain'] = isset($_REQUEST['show_RecordAgain']);
		$var_data['show_TrackingNr'] = isset($_REQUEST['show_TrackingNr']);
		$var_data['currency_name'] = $_REQUEST['currency_name'];
		$var_data['currency_sign'] = $_REQUEST['currency_sign'];
		$var_data['currency_first'] = isset($_REQUEST['currency_first']);
		$var_data['date_format_0'] = $_REQUEST['date_format_0'];
		$var_data['date_format_1'] = $_REQUEST['date_format_1'];
		$var_data['date_format_2'] = $_REQUEST['date_format_2'];
		$var_data['language'] = $_REQUEST['language'];
		if(is_array($_REQUEST['status'])) {
			$var_data['status'] = implode(',', $_REQUEST['status']);
		}
		$var_data['roundPrecision'] = $_REQUEST['roundPrecision'];
		$var_data['roundMinutes'] = $_REQUEST['roundMinutes'];
		$var_data['roundSeconds'] = $_REQUEST['roundSeconds'];
		$var_data['roundTimesheetEntries'] = $_REQUEST['roundTimesheetEntries'];
		$var_data['decimalSeparator'] = $_REQUEST['decimalSeparator'];
		$var_data['durationWithSeconds'] = isset($_REQUEST['durationWithSeconds']);
		$var_data['defaultTimezone'] = $_REQUEST['defaultTimezone'];
		$var_data['exactSums'] = isset($_REQUEST['exactSums']);
		$editLimit = false;
		if (isset($_REQUEST['editLimitEnabled'])) {
			$hours = (int) $_REQUEST['editLimitHours'];
			$days = (int) $_REQUEST['editLimitDays'];
			$editLimit = $hours + $days * 24;
			$editLimit *= 60 * 60; // convert to seconds
		}
		if ($editLimit === false || $editLimit === 0)
			$var_data['editLimit'] = '-';
		else
			$var_data['editLimit'] = $editLimit;
		$success = $database->configuration_edit($var_data);
		
//		if(strlen($_REQUEST['new_status']) > 0) {
//			$status = $_REQUEST['new_status'];
//			if(stristr($status, ',')) {
//				$status = explode(',', $status);
//			} else {
//				$status = array($status);
//			}
//			$database->add_status($status);
//		}
//		
		// do whatever you like
		// and return one of these:
		echo $success ? "ok" : $kga['lang']['error'];
		break;
	case "toggleDeletedUsers" :
		setcookie("ap_ext_show_deleted_users", $axValue);
		break;
}
?>
