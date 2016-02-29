<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
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

// ============================================
// === Functions used by the updater script ===
// ============================================

/**
 * Function to exit the updater immediately, while displaying a user-friendly error message.
 *
 * @param $message
 */
function exitUpdater($title, $message, $message2)
{
    include 'update_error.php';
    exit;
}

/**
 * Execute an sql query in the database. The correct database connection
 * will be chosen and the query will be logged with the success status.
 *
 * As third parameter an alternative query can be passed, which should be
 * displayed instead of the executed query. This prevents leakage of
 * confidential information like password salts. The logfile will still
 * contain the executed query.
 *
 * @param $query query to execute as string
 * @param bool $errorProcessing true if it's an error when the query fails.
 * @param null $displayQuery
 */
function exec_query($query, $errorProcessing = true, $displayQuery = null)
{
    global $database, $kga, $errors, $executed_queries;

    $conn = $database->getConnectionHandler();

    $executed_queries++;
    $success = $conn->Query($query);

    Kimai_Logger::logfile($query);

    $err = $conn->Error();

    $query = htmlspecialchars($query);
    $displayQuery = htmlspecialchars($displayQuery);

    if ($success) {
        $level = 'green';
    } else {
        if ($errorProcessing) {
            $level = 'red';
            $errors++;
        } else {
            $level = 'orange'; // something went wrong but it's not an error
        }
    }

    printLine($level, ($displayQuery == null ? $query : $displayQuery), $err);

    if (!$success) {
        Kimai_Logger::logfile("An error has occured in query [$query]: " . $conn->Error());
    }
}

/**
 * @param string $level
 * @param string $text
 * @param string $errorInfo
 */
function printLine($level, $text, $errorInfo = '')
{
    echo "<tr>";
    echo "<td>" . $text . "<br/>";
    echo "<span class='error_info'>" . $errorInfo . "</span>";
    echo "</td>";

    switch ($level) {
        case 'green':
            echo "<td class='green'>&nbsp;&nbsp;</td>";
            break;
        case 'red':
            echo "<td class='red'>!</td>";
            break;
        case 'orange':
            echo "<td class='orange'>&nbsp;&nbsp;</td>";
            break;
    }

    echo "</tr>";
}

/**
 * @param $input
 * @return string
 */
function quoteForSql($input)
{
    global $database;
    return "'" . $database->getConnectionHandler()->SQLFix($input) . "'";
}
