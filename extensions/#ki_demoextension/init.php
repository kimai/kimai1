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

include('../../includes/basics.php');

$dir_templates = 'templates/';
$datasrc = 'config.ini';
$settings = parse_ini_file($datasrc);
$dir_ext = $settings['EXTENSION_DIR'];

$user = checkUser();
// =========================================
// = Get the currently displayed timeframe =
// =========================================
$timeframe = get_timeframe();
$in = $timeframe[0];
$out = $timeframe[1];

$view = new Zend_View();
$view->setBasePath(WEBROOT . 'extensions/' . $dir_ext . '/' . $dir_templates);

echo $view->render('index.php');
