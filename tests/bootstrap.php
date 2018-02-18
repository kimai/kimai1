<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking
 * (c) Kimai-Development-Team since 2006
 * https://www.kimai.org
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

// TODO: check include path
ini_set('date.timezone', 'Europe/Berlin');

if (!file_exists(__DIR__ . '/../libraries/autoload.php')) {
    die('You have to execute "composer install" or "composer update" before executing unit tests!');
}
require_once __DIR__ . '/../libraries/autoload.php';
require_once __DIR__ . '/TestCase.php';

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../') . '/');
