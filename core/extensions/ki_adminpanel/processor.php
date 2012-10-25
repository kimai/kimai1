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

switch ($axAction)
{
	case "createUser" :
		// create new user account
		$userData['name'] = trim($axValue);
		$userData['status'] = 2;
		$userData['active'] = 0;

                // validate data
                $error = false;
                if ($database->customer_nameToID($userData['name']) !== false)
                  $error = $kga['lang']['errorMessages']['customerWithSameName'];

                $userId = false;
                if ($error === false) {
                  $userId = $database->user_create($userData);
                  $database->setGroupMemberships($userId, $kga['user']['groups']);
                }

                header('Content-Type: application/json;charset=utf-8');
                echo json_encode(array(
                  'error' => $error,
                  'userId' => $userId));
		break;

	case "createStatus" :
		// create new status
		$status_data['status'] = trim($axValue);
		$new_status_id = $database->status_create($status_data);
		break;

	case "createGroup" :
		// create new group
		$group['name'] = trim($axValue);
		$newGroupID = $database->group_create($group);
		if ($newGroupID != false) {
			$database->assign_groupToGroupleaders($newGroupID, array($kga['user']['userID']));
		}
		break;

	case "refreshSubtab" :
		// builds either user/group/advanced/DB subtab
		$view->curr_user = $kga['user']['name'];
		if ($kga['user']['status'] == 0)
			$view->groups = $database->get_groups(get_cookie('adminPanel_extension_show_deleted_groups', 0));
		else
			$view->groups = $database->get_groups_by_leader($kga['user']['userID'], get_cookie('adminPanel_extension_show_deleted_groups', 0));
		if ($kga['user']['status'] == 0)
			$users = $database->get_users(get_cookie('adminPanel_extension_show_deleted_users', 0));
		else
			$users = $database->get_watchable_users($kga['user']);
			// get group names
		foreach ($users as &$user) {
			$groups = $database->getGroupMemberships($user['userID']);
			if(is_array($groups)) {
			foreach ($groups as $group) {
				$groupData = $database->group_get_data($group);
				$user['groups'][] = $groupData['name'];
			}
			}
		}
		$arr_status = $database->get_statuses();
		$view->users = $users;
		$view->arr_status = $arr_status;
		$view->showDeletedGroups = get_cookie('adminPanel_extension_show_deleted_groups', 0);
		$view->showDeletedUsers = get_cookie('adminPanel_extension_show_deleted_users', 0);

		switch ($axValue) {
			case "users" :
				echo $view->render('users.php');
				break;

			case "groups" :
				echo $view->render('groups.php');
				break;

			case "status" :
				echo $view->render('status.php');
				break;

			case "advanced" :
				if ($kga['conf']['editLimit'] != '-') {
					$view->editLimitEnabled = true;
					$editLimit = $kga['conf']['editLimit'] / (60 * 60); // convert to hours
					$view->editLimitDays = (int) ($editLimit / 24);
					$view->editLimitHours = (int) ($editLimit % 24);
				}
				else {
					$view->editLimitEnabled = false;
					$view->editLimitDays = '';
					$view->editLimitHours = '';
				}
				echo $view->render('advanced.php');
				break;

			case "database" :
				echo $view->render('database.php');
				break;

			case "customers" :
				if ($kga['user']['status'] == 0)
					$customers = $database->get_customers();
				else
					$customers = $database->get_customers($kga['user']['groups']);
				foreach ($customers as $row => $data) {
					$groupNames = array();
					$groups = $database->customer_get_groupIDs($data['customerID']);
					if ($groups !== false) {
						foreach ($groups as $groupID) {
							$data = $database->group_get_data($groupID);
							$groupNames[] = $data['name'];
						}
						$customers[$row]['groups'] = implode(", ", $groupNames);
					}
				}
				if (count($customers) > 0) {
					$view->customers = $customers;
				}
				else {
					$view->customers = '0';
				}
				echo $view->render('customers.php');
				break;

			case "projects" :
				if ($kga['user']['status'] == 0) {
					$projects = $database->get_projects();
                } else {
					$projects = $database->get_projects($kga['user']['groups']);
                }

                if ($projects !== null && is_array($projects))
                {
                    foreach ($projects as $row => $project) {
                        $groupNames = array();
                        foreach ($database->project_get_groupIDs($project['projectID']) as $groupID) {
                            $data = $database->group_get_data($groupID);
                            $groupNames[] = $data['name'];
                        }
                        $projects[$row]['groups'] = implode(", ", $groupNames);
                    }
                    $view->projects = $projects;
                }

				echo $view->render('projects.php');
				break;

			case "activities" :
				if ($kga['user']['status'] == 0)
					$groups = null;
				else
					$groups = $kga['user']['groups'];
				if (! isset($_REQUEST['activity_filter'])) {
					$activities = $database->get_activities($groups);
                } else {
					switch ($_REQUEST['activity_filter']) {
						case - 1 :
							$activities = $database->get_activities($groups);
							break;
						case - 2 :
						// -2 is to get unassigned activities. As -2 is never
						// an id of a project this will give us all unassigned
						// activities.
						default :
							$activities = $database->get_activities_by_project($_REQUEST['activity_filter'], $groups);
					}
                }

				foreach ($activities as $row => $activity) {
					$groupNames = array();
					foreach ($database->activity_get_groups($activity['activityID']) as $groupID) {
						$data = $database->group_get_data($groupID);
						$groupNames[] = $data['name'];
					}
					$activities[$row]['groups'] = implode(", ", $groupNames);
				}
				if (count($activities) > 0) {
					$view->activities = $activities;
				}
				else {
					$view->activities = '0';
				}
				$projects = $database->get_projects($groups);
				$view->projects = $projects;
				$view->selected_activity_filter = isset($_REQUEST['activity_filter']) ? $_REQUEST['activity_filter'] : -2;
				echo $view->render('activities.php');
				break;
		}
		break;

	case "deleteUser" :
		// set the trashflag of a user
		switch ($axValue) {
			case 0 :
				// Fire JavaScript confirm when a user is about to be deleted
				echo $kga['lang']['sure'];
				break;
			case 1 :
				// If the confirmation is returned the user gets the trash-flag. 
				$database->user_delete($id, true);
				break;
            case 2 :
                // User is finally deleted after confirmed through trash view
                $database->user_delete($id, false);
                break;
		}
		break;

	case "deleteGroup" :
		// removes a group
		switch ($axValue) {
			case 0 :
				// Fire JavaScript confirm when a group is about to be deleted
				echo $kga['lang']['sure'];
				break;
			case 1 :
				// If the confirmation is returned the group is deleted.
				$database->group_delete($id);
				break;
		}
		break;

	case "deleteStatus" :
		// asks for confirmation and deletes a status
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

	case "deleteProject" :
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

	case "deleteCustomer" :
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

	case "deleteActivity" :
		// set the trashflag of an activity
		switch ($axValue) {
			case 0 :
				// Fire JavaScript confirm when an activity is about to be deleted
				echo $kga['lang']['sure'];
				break;
			case 1 :
				// If the confirmation is returned the activity gets the trash-flag. 
				$database->activity_delete($id);
				break;
		}
		break;

	case "banUser" :
		// Ban a user from login
		$sts['active'] = 0;
		$database->user_edit($id, $sts);
		echo sprintf("<img border='0' title='%s' alt='%s' src='../skins/%s/grfx/lock.png' width='16' height='16' />", $kga['lang']['banneduser'], $kga['lang']['banneduser'], $kga['conf']['skin']);
		break;

	case "unbanUser" :
		// Unban a user from login
		$sts['active'] = 1;
		$database->user_edit($id, $sts);
		echo sprintf("<img border='0' title='%s' alt='%s' src='../skins/%s/grfx/jipp.gif' width='16' height='16' />", $kga['lang']['activeuser'], $kga['lang']['activeuser'], $kga['conf']['skin']);
		break;

	case "sendEditUser" :
		// process editUser form
		$userData['name'] = trim($_REQUEST['name']);
		$userData['status'] = $_REQUEST['status'];
		$userData['mail'] = $_REQUEST['mail'];
		$userData['alias'] = $_REQUEST['alias'];
                $userData['rate'] = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['rate']);
		// if password field is empty => password unchanged (not overwritten with "")
		if ($_REQUEST['password'] != "") {
			$userData['password'] = md5($kga['password_salt'] . $_REQUEST['password'] . $kga['password_salt']);
		}

                // validate data
                $errorMessages = array();

                if ($database->customer_nameToID($userData['name']) !== false)
                  $errorMessages['name'] = $kga['lang']['errorMessages']['customerWithSameName'];

                $success = false;
                if (count($errorMessages) == 0) {
                  $database->user_edit($id, $userData);
                  $database->setGroupMemberships($id, $_REQUEST['groups']);
                  $success = true;
                }

                header('Content-Type: application/json;charset=utf-8');
                echo json_encode(array(
                  'errors' => $errorMessages,
                  'success' => $success));
		break;

	case "sendEditGroup" :
		// process editGroup form
		$group['name'] = trim($_REQUEST['name']);
		$database->group_edit($id, $group);
		$leaders = $_REQUEST['leaders'];
		$database->assign_groupToGroupleaders($id, $leaders);
		break;

	case "sendEditStatus" :
		// process editStatus form
		$status_data['status'] = trim($_REQUEST['status']);
		$database->status_edit($id, $status_data);
		break;

	case "sendEditAdvanced" :
		// process AdvancedOptions form
		$config_data['adminmail'] = $_REQUEST['adminmail'];
		$config_data['loginTries'] = $_REQUEST['logintries'];
		$config_data['loginBanTime'] = $_REQUEST['loginbantime'];
		$config_data['show_sensible_data'] = isset($_REQUEST['show_sensible_data']);
		$config_data['show_update_warn'] = isset($_REQUEST['show_update_warn']);
		$config_data['check_at_startup'] = isset($_REQUEST['check_at_startup']);
		$config_data['show_daySeperatorLines'] = isset($_REQUEST['show_daySeperatorLines']);
		$config_data['show_gabBreaks'] = isset($_REQUEST['show_gabBreaks']);
		$config_data['show_RecordAgain'] = isset($_REQUEST['show_RecordAgain']);
		$config_data['show_TrackingNr'] = isset($_REQUEST['show_TrackingNr']);
		$config_data['currency_name'] = $_REQUEST['currency_name'];
		$config_data['currency_sign'] = $_REQUEST['currency_sign'];
		$config_data['currency_first'] = isset($_REQUEST['currency_first']);
		$config_data['date_format_0'] = $_REQUEST['date_format_0'];
		$config_data['date_format_1'] = $_REQUEST['date_format_1'];
		$config_data['date_format_2'] = $_REQUEST['date_format_2'];
		$config_data['language'] = $_REQUEST['language'];
		if(isset($_REQUEST['status']) && is_array($_REQUEST['status'])) {
			$config_data['status'] = implode(',', $_REQUEST['status']);
		}
		$config_data['roundPrecision'] = $_REQUEST['roundPrecision'];
		$config_data['roundMinutes'] = $_REQUEST['roundMinutes'];
		$config_data['roundSeconds'] = $_REQUEST['roundSeconds'];
		$config_data['roundTimesheetEntries'] = $_REQUEST['roundTimesheetEntries'];
		$config_data['decimalSeparator'] = $_REQUEST['decimalSeparator'];
		$config_data['durationWithSeconds'] = isset($_REQUEST['durationWithSeconds']);
		$config_data['exactSums'] = isset($_REQUEST['exactSums']);
		$editLimit = false;
		if (isset($_REQUEST['editLimitEnabled'])) {
			$hours = (int) $_REQUEST['editLimitHours'];
			$days = (int) $_REQUEST['editLimitDays'];
			$editLimit = $hours + $days * 24;
			$editLimit *= 60 * 60; // convert to seconds
		}
		if ($editLimit === false || $editLimit === 0)
			$config_data['editLimit'] = '-';
		else
			$config_data['editLimit'] = $editLimit;
		$success = $database->configuration_edit($config_data);
		write_config_file(
                $kga['server_database'],
                $kga['server_hostname'],
                $kga['server_username'],
                $kga['server_password'],
                $kga['server_conn'],
                $kga['server_type'],
                $kga['server_prefix'],
                $kga['language'],
                $kga['password_salt'],
                $_REQUEST['defaultTimezone']);
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
		setcookie("adminPanel_extension_show_deleted_users", $axValue);
		break;
}
