<?php
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

// =====================
// = INVOICE PROCESSOR =
// =====================

$isCoreProcessor = 0;
$dir_templates = 'templates/';
require '../../includes/kspi.php';

$kga = Kimai_Registry::getConfig();
$database = Kimai_Registry::getDatabase();

// ==================
// = handle request =
// ==================
switch ($axAction) {
    // ==========================
    // = Change the default vat =
    // ==========================
    case 'editVat':
        $vat = str_replace($kga['conf']['decimalSeparator'], '.', $_POST['vat']);
        if (!is_numeric($vat)) {
            echo "0";
            return;
        }
        $database->configuration_edit(['defaultVat' => $vat]);
        echo "1";
        break;

    // ==========================
    // = Reload projects        =
    // ==========================
    case 'projects':
        if (isset($kga['customer'])) {
            $db_projects = $database->get_projects_by_customer($kga['customer']['customerID'],
                $kga['customer']['groups']);
        } else {
            $db_projects = $database->get_projects_by_customer($_GET['customerID'], $kga['user']['groups']);
        }

        $js_projects = [];
        foreach ($db_projects as $project) {
            $js_projects[$project['projectID']] = $project['name'];
        }
        header("Content-Type: application/json");
        echo json_encode($js_projects);
        break;
}
