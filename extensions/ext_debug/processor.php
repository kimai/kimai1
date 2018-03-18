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

// ===================
// = DEBUG PROCESSOR =
// ===================

$isCoreProcessor = 0;
$dir_templates = 'templates/';
require "../../includes/kspi.php";

$kga = Kimai_Registry::getConfig();

switch ($axAction) {
    /**
     * Return the logfile in reverse order, so the last entries are shown first.
     */
    case "reloadLogfile":
        $logdatei = WEBROOT . '/temporary/logfile.txt';
        $fh = fopen($logdatei, 'r');

        $theData = '';
        $i = 0;

        $lines = $kga['logfile_lines'];
        $filearray = [];

        while (!feof($fh)) {
            $filearray[$i] = fgets($fh);
            $i++;
        }

        fclose($fh);

        if ($kga['logfile_lines'] != "@") {
            $start = count($filearray);
            $goal = $start - $lines;
            for ($line = $start - 1; ($line > $goal && $line > 0); $line--) {
                if ($filearray[$line] != "") {
                    $theData .= $filearray[$line] . "<br/>";
                }
            }
        } else {
            foreach ($filearray as $line) {
                if ($line != "") {
                    $theData .= $line . "<br/>";
                }
            }
        }

        echo $theData;
        break;

    /**
     * Empty the logfile.
     */
    case "clearLogfile":
        if ($kga['delete_logfile']) {
            $logdatei = fopen(WEBROOT . "temporary/logfile.txt", "w");
            fwrite($logdatei, "");
            fclose($logdatei);
            echo $kga['lang']['log_delete'];
        } else {
            die();
        }
        break;

    /**
     * Write some message to the logfile.
     */
    case "shoutbox":
        Kimai_Logger::logfile("[" . Kimai_Registry::getUser()->getName() . "] " . $axValue);
        break;

    /**
     * Return the $kga variable (Kimai Global Array). Strip out some sensitive
     * information if not configured otherwise.
     */
    case "reloadKGA":

        $output = $kga;
        $filter = [
            'server_hostname' => "xxx",
            'server_database' => "xxx",
            'server_username' => "xxx",
            'server_password' => "xxx",
            'password_salt' => "xxx",
            'user' => [
                'secure' => "xxx",
                'userID' => "xxx",
                'pw' => "xxx",
                'password' => "xxx",
                'apikey' => "xxx"
            ],
        ];

        switch ($axValue) {
            case 'plain':
                $output = $kga;
                $output['conf'] = '## HIDDEN ##';
                $output['user'] = '## HIDDEN ##';
                $output['lang'] = '## HIDDEN ##';
                break;

            case 'lang':
                $output = $kga['lang'];
                $filter = [];
                break;

            case 'user':
                $output = $kga['user'];
                $filter = $filter['user'];
                break;

            case 'conf':
                $output = $kga['conf'];
                break;
        }

        // clean out some data that is way too private to be shown in the frontend ...
        foreach ($filter as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if (isset($output[$k]) && isset($output[$k][$k2])) {
                        $output[$k][$k2] = $v2;
                    }
                }
            } else {
                $output[$k] = $v;
            }
        }

        print_r($output);

        break;
}
