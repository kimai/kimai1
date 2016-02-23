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

/**
 * Query the Kimai project server for information about a new version.
 * The response will simply be passed through.
 */
error_reporting(-1);
require('../includes/basics.php');

header('Content-Type: text/html; charset=utf-8');

$check = new Kimai_Update_Check();
$result = $check->checkForUpdate($kga['version'], $kga['revision']);

if ($result == Kimai_Update_Check::RELEASE) {
    echo $kga['lang']['updatecheck']['release'];
} else if ($result == Kimai_Update_Check::BETA) {
    echo $kga['lang']['updatecheck']['beta'];
} else if ($result == Kimai_Update_Check::CURRENT) {
    echo $kga['lang']['updatecheck']['current'];
}
