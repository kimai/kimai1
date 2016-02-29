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

if (!defined('KIMAI_UPDATER_RUNNING')) {
    die('You cannot call this file directly');
}

if (empty($title)) { $title = 'Update error'; }
if (empty($message)) { $message = 'An unknown error occured during the update'; }
if (empty($message2)) { $message2 = 'Sorry!'; }

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="robots" content="noindex,nofollow"/>
    <title>Kimai Update</title>
    <style type="text/css" media="screen">
        body {
            background: #46E715 url('ki_twitter_bg.jpg') no-repeat;
            font-family: sans-serif;
            color: #333;
        }
        div.outer {
            background-image: url('../skins/standard/grfx/floaterborder.png');
            position: absolute;
            top: 50%;
            left: 50%;
            width: 500px;
            height: 270px;
            margin-left: -250px;
            margin-top: -125px;
            border: 6px solid white;
            padding: 10px;
        }
    </style>
</head>
<body>
<div class="outer" align="center">
    <img src="caution.png" width="70" height="63" alt="Caution"><br/>
    <h1><?php echo $title; ?></h1>
    <div class="update_message">
        <p><?php echo $message; ?></p>
        <p><?php echo $message2; ?></p>
    </div>
</div>
</body>
</html>