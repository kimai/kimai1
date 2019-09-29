<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // https://www.kimai.org
 * (c) Kimai-Development-Team since 2006
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

$isCoreProcessor = 0;
$dir_templates = 'templates/';
require '../../includes/kspi.php';
require 'private_db_layer_mysql.php';

$database = Kimai_Registry::getDatabase();

function expenseAccessAllowed($entry, $action, &$errors)
{
    $kga = Kimai_Registry::getConfig();
    $database = Kimai_Registry::getDatabase();

    if (!isset($kga['user'])) {
        $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
        return false;
    }

    // check if expense is too far in the past to allow editing (or deleting)
    if ($kga->isEditLimit() && time() - $entry['timestamp'] > $kga->getEditLimit()) {
        $errors[''] = $kga['lang']['editLimitError'];
        return false;
    }

    $groups = $database->getGroupMemberships($entry['userID']);

    if ($entry['userID'] == $kga['user']['userID']) {
        $permissionName = 'ki_expenses-ownEntry-' . $action;
        if ($database->global_role_allows($kga['user']['globalRoleID'], $permissionName)) {
            return true;
        } else {
            Kimai_Logger::logfile("missing global permission $permissionName for user " . $kga['user']['name'] . " to access expense");
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
            return false;
        }
    }

    $assignedOwnGroups = array_intersect($groups, $database->getGroupMemberships($kga['user']['userID']));

    if (count($assignedOwnGroups) > 0) {
        $permissionName = 'ki_expenses-otherEntry-ownGroup-' . $action;
        if ($database->checkMembershipPermission($kga['user']['userID'], $assignedOwnGroups, $permissionName)) {
            return true;
        } else {
            Kimai_Logger::logfile("missing membership permission $permissionName of own group(s) " . implode(
                ", ",
                    $assignedOwnGroups
            ) . " for user " . $kga['user']['name']);
            $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
            return false;
        }
    }

    $permissionName = 'ki_expenses-otherEntry-otherGroup-' . $action;
    if ($database->global_role_allows($kga['user']['globalRoleID'], $permissionName)) {
        return true;
    } else {
        Kimai_Logger::logfile("missing global permission $permissionName for user " . $kga['user']['name'] . " to access expense");
        $errors[''] = $kga['lang']['errorMessages']['permissionDenied'];
        return false;
    }
}

switch ($axAction) {

    // ===========================================
    // = Load expense data from DB and return it =
    // ===========================================
    case 'reload_exp':
        $filters = explode('|', $axValue);
        if (empty($filters[0])) {
            $filterUsers = [];
        } else {
            $filterUsers = explode(':', $filters[0]);
        }

        $filterCustomers = array_map(
            function ($customer) {
                return $customer['customerID'];
            },
            $database->get_customers($kga['user']['groups'])
        );

        if (!empty($filters[1])) {
            $filterCustomers = array_intersect($filterCustomers, explode(':', $filters[1]));
        }

        $filterProjects = array_map(
            function ($project) {
                return $project['projectID'];
            },
            $database->get_projects($kga['user']['groups'])
        );

        if (!empty($filters[2])) {
            $filterProjects = array_intersect($filterProjects, explode(':', $filters[2]));
        }

        // if no userfilter is set, set it to current user
        if (isset($kga['user']) && count($filterUsers) == 0) {
            array_push($filterUsers, $kga['user']['userID']);
        }

        if (isset($kga['customer'])) {
            $filterCustomers = [$kga['customer']['customerID']];
        }

        $view->assign('expenses', get_expenses($in, $out, $filterUsers, $filterCustomers, $filterProjects, 1));
        $view->assign('total', Kimai_Format::formatCurrency(
            array_reduce(
                $view->expenses,
                function ($sum, $expense) {
                    return $sum + $expense['multiplier'] * $expense['value'];
                },
                0
            )
        ));

        $ann = expenses_by_user($in, $out, $filterUsers, $filterCustomers, $filterProjects);
        $ann = Kimai_Format::formatCurrency($ann);
        $view->assign('user_annotations', $ann);

        // TODO: function for loops or convert it in template with new function
        $ann = expenses_by_customer($in, $out, $filterUsers, $filterCustomers, $filterProjects);
        $ann = Kimai_Format::formatCurrency($ann);
        $view->assign('customer_annotations', $ann);

        $ann = expenses_by_project($in, $out, $filterUsers, $filterCustomers, $filterProjects);
        $ann = Kimai_Format::formatCurrency($ann);
        $view->assign('project_annotations', $ann);

        $view->assign('activity_annotations', []);

        if (isset($kga['user'])) {
            $view->assign('hideComments', !$kga->getSettings()->isShowComments());
        } else {
            $view->assign('hideComments', true);
        }

        echo $view->render('expenses.php');
        break;

    // =======================================
    // = Erase expense entry via quickdelete =
    // =======================================
    case 'quickdelete':
        $errors = [];

        $data = expense_get($id);

        expenseAccessAllowed($data, 'delete', $errors);

        if (count($errors) == 0) {
            expense_delete($id);
        }

        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'errors' => $errors
        ]);
        break;

    // =============================
    // = add / edit expense record =
    // =============================
    case 'add_edit_record':
        header('Content-Type: application/json;charset=utf-8');
        $errors = [];

        // determine action for permission check
        $action = 'add';

        if ($id) {
            $action = 'edit';
        }

        if (isset($_REQUEST['erase'])) {
            $action = 'delete';
        }

        if ($id) {
            $data = expense_get($id);

            // check if editing or deleting with the old values would be allowed
            if (!expenseAccessAllowed($data, $action, $errors)) {
                echo json_encode(['errors' => $errors]);
                break;
            }
        }

        // delete now because next steps don't need to be taken for deleted entries
        if (isset($_REQUEST['erase'])) {
            expense_delete($id);
            echo json_encode(['errors' => $errors]);
            break;
        }

        if (!isset($_REQUEST['projectID']) || empty($_REQUEST['projectID']) || !is_numeric($_REQUEST['projectID'])) {
            $errors['projectID'] = $kga['lang']['errorMessages']['noProjectSelected'];
        }

        if (!isset($_REQUEST['designation']) || empty($_REQUEST['designation'])) {
            $errors['designation'] = sprintf(
                $kga['lang']['errorMessages']['emptyField'],
                $kga['lang']['designation']
            );
        } elseif (!is_numeric($_REQUEST['edit_value'])) {
            $errors['edit_value'] = $kga['lang']['errorMessages']['wrongData'];
        }

        if (!isset($_REQUEST['edit_value']) || empty($_REQUEST['edit_value'])) {
            $errors['edit_value'] = sprintf(
                $kga['lang']['errorMessages']['emptyField'],
                $kga['lang']['expense']
            );
        }

        if (!isset($_REQUEST['edit_day']) || empty($_REQUEST['edit_day'])) {
            $errors['edit_day'] = sprintf(
                $kga['lang']['errorMessages']['emptyField'],
                $kga['lang']['day']
            );
        }

        if (!isset($_REQUEST['edit_time']) || empty($_REQUEST['edit_time'])) {
            $errors['edit_time'] = sprintf(
                $kga['lang']['errorMessages']['emptyField'],
                $kga['lang']['timelabel']
            );
        }

        if (!isset($_REQUEST['multiplier']) || empty($_REQUEST['multiplier'])) {
            $errors['multiplier'] = sprintf(
                $kga['lang']['errorMessages']['emptyField'],
                $kga['lang']['multiplier']
            );
        }

        if (count($errors) > 0) {
            echo json_encode(['errors' => $errors]);
            break;
        }

        // get new data
        $data['projectID'] = $_REQUEST['projectID'];
        $data['designation'] = $_REQUEST['designation'];
        $data['comment'] = (isset($_REQUEST['comment']) && !empty($_REQUEST['comment'])) ? $_REQUEST['comment'] : '';
        $data['commentType'] = $_REQUEST['commentType'];
        $data['cleared'] = isset($_REQUEST['cleared']);
        $data['refundable'] = getRequestBool('refundable');
        $data['multiplier'] = getRequestDecimal($_REQUEST['multiplier']);
        $data['value'] = getRequestDecimal($_REQUEST['edit_value']);
        $data['userID'] = $kga['user']['userID'];

        if (!is_numeric($data['multiplier']) || $data['multiplier'] <= 0) {
            $errors['multiplier'] = $kga['lang']['errorMessages']['multiplierNegative'];
        }

        // parse new day and time
        $edit_day = DateTime::createFromFormat($kga->getDateFormat(3), $_REQUEST['edit_day'])->format('d.m.Y');
        $edit_time = Kimai_Format::expand_time_shortcut($_REQUEST['edit_time']);

        // validate day and time
        $new = "${edit_day}-${edit_time}";
        if (!Kimai_Format::check_time_format($new)) {
            $errors[''] = $kga['lang']['TimeDateInputError'];
        }

        // convert to internal time format
        $new_time = convert_time_strings($new, $new);
        $data['timestamp'] = $new_time['in'];

        expenseAccessAllowed($data, $action, $errors);

        if (count($errors) > 0) {
            echo json_encode(['errors' => $errors]);
            break;
        }

        $result = false;
        if ($id) {
            if (expense_edit($id, $data) === false) {
                $errors[''] = $kga['lang']['error'];
            }
        } else {
            if (expense_create($kga['user']['userID'], $data) === false) {
                $errors[''] = $kga['lang']['error'];
            }
        }

        echo json_encode(['errors' => $errors]);
        break;

}
