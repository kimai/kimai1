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
        
        $timespace = get_timespace();
        $tpl->assign('in', $timespace[0]);
        $tpl->assign('out', $timespace[1]);

        $tpl->display("timespan.tpl");
    break;

    // ==========================
    // = Change the default vat =
    // ==========================
    case 'editVat':
        
        $vat = str_replace($kga['conf']['decimalSeparator'],'.',$_REQUEST['vat']);
        
        if (!is_numeric($vat)) {
          echo "0";
          return;
        }

        var_edit(array('defaultVat'=>$vat));
        echo "1";
    break;

}

?>