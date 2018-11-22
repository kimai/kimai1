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

if (!defined('KIMAI_UPDATER_RUNNING')) {
    die('You cannot call this file directly');
}

?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Kimai Update <?php echo $kga['version'] . "." . $kga['revision']; ?></title>
    <style type="text/css" media="screen">
        html {
            font-family: sans-serif;
            font-size: 80%;
        }

        .red {
            background-color: #f00;
            color: #fff;
            font-weight: bold;
        }

        .green {
            background-color: #0f0;
        }

        .orange {
            background-color: #1FA100;
        }

        .machtnix {
            color: #1FA100;
        }

        .error_info {
            color: #888;
        }

        .abst {
            padding: 10px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        table {
            padding: 2px;
        }

        td {
            border-top: 1px solid #eee;
            border-bottom: 1px dotted black;
            padding: 5px 0;
        }

        .success {
            border: 4px solid #0f0;
            padding: 10px;
            width: 300px;
            margin-bottom: 10px;
        }

        .fail {
            border: 4px solid #f00;
            padding: 10px;
            width: 300px;
            margin-bottom: 10px;
        }

        .red, .green, .orange {
            width: 30px;
            text-align: center;
        }

        #queries {
            background-color: #0f0;
            color: white;
            font-weight: bold;
            padding: 10px;
            margin-bottom: 20px;
        }

        #important_message {
            background-color: red;
            color: white;
            font-weight: bold;
            padding: 10px;
            margin-bottom: 20px;
            display: none;
        }

        .important_block_head {
            background-color: red;
            color: white;
            font-weight: bold;
            padding: 10px;
        }

        a {
            color: #0f0;
            text-decoration: none;
            padding: 5px;
            border: 1px dotted gray;
        }

        a:hover {
            color: white;
            background-color: #0f0;
            border: 1px solid black;
        }

        #logo {
            width: 135px;
            height: 52px;
            position: absolute;
            top: 10px;
            right: 10px;
            background-image: url('logo.png');
        }

        #restore {
            display: block;
            margin-bottom: 15px;
            width: 100px;
        }
    </style>
    <script type="text/javascript" src="../libraries/jQuery/jquery-1.12.4.min.js"></script>
</head>
<body>
<h1>Kimai Auto Updater v<?php echo $kga['version'] . "." . $kga['revision']; ?></h1>
<div id="logo">&nbsp;</div>
<div id="link">&nbsp;</div>
<a href="db_restore.php" id="restore" title="db_restore">Database Utility</a>
<div id="queries"></div>
<div id="important_message"></div>
<table>
    <tr>
        <td colspan='2'>
            <strong><?php echo $kga['lang']['updater'][10]; ?></strong>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo $kga['lang']['updater'][20]; ?>
        </td>
        <td class='green'>
            &nbsp;&nbsp;
        </td>
    </tr>
    <tr>
        <td>
            <?php echo $kga['lang']['updater'][30]; ?>
        </td>
        <td class='orange'>
            &nbsp;&nbsp;
        </td>
    </tr>
    <tr>
        <td>
            <?php echo $kga['lang']['updater'][40]; ?>
        </td>
        <td class='red'>
            !
        </td>
    </tr>
</table>
<br/>
<br/>
<table cellspacing='0' cellpadding='2'>
