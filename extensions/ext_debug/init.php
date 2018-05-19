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

include '../../includes/basics.php';

$kga = Kimai_Registry::getConfig();

$user = checkUser();

$view = new Kimai_View();
$view->addBasePath(__DIR__ . '/templates/');

$view->assign('delete_logfile', $kga['delete_logfile']);
$view->assign(
	'kga_sections',
	[
        'all' => '',
        'plain' => 'plain',
        'lang' => 'translations',
        'user' => 'user',
        'conf' => 'config',
    ]
);
$view->assign('limitText', sprintf($view->translate('debug:lines'), $kga['logfile_lines']));

if ($kga['logfile_lines'] == "@") {
    $view->assign('limitText', "");
}

echo $view->render('index.php');