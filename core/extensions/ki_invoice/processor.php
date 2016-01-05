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

// =====================
// = INVOICE PROCESSOR =
// =====================

// insert KSPI
$isCoreProcessor = 0;
$dir_templates = "templates/";
require("../../includes/kspi.php");

// ==================
// = handle request =
// ==================
switch ($axAction) {

    // =====================================
    // = Reload the timespan and return it =
    // =====================================
    case 'reload_timespan':
        $timeframe = get_timeframe();
        $view->assign('timeframe', $timeframe);
        echo $view->render("timespan.php");
        break;

    // ==========================
    // = Change the default vat =
    // ==========================
    case 'editVat':
        $vat = str_replace($kga['conf']['decimalSeparator'], '.', $_POST['vat']);
        if (!is_numeric($vat)) {
            echo "0";
            return;
        }
        $database->configuration_edit(array('defaultVat' => $vat));
        echo "1";
        break;

    // ==========================
    // = Change the default vat =
    // ==========================
    case 'projects':
        if (isset($kga['customer'])) {
            $db_projects = $database->get_projects_by_customer($kga['customer']['customerID'], $kga['customer']['groups']);
        } else {
            $db_projects = $database->get_projects_by_customer($_GET['customerID'], $kga['user']['groups']);
        }
        $js_projects = array();
        foreach ($db_projects as $project) {
            $js_projects[$project['projectID']] = $project['name'];
        }
        header("Content-Type: application/json");
        echo json_encode($js_projects);
        break;
}
