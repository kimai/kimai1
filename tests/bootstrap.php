<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking
 * (c) Kimai-Development-Team since 2006
 * http://www.kimai.org
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
ini_set ( 'date.timezone', 'Europe/Berlin' );

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

ini_set('include_path', ini_get('include_path')
    . PATH_SEPARATOR . __DIR__ . '/library'
    . PATH_SEPARATOR . __DIR__ . '/../core/libraries'
);

class UnitTestHelper
{
    /**
     * Access protected or private methods
     *
     * use the following code to access any protected or private class method
     * $obj = new MyClass();
     * $method = UnitTestHelper::getMethod($obj, 'nameOfMethod');
     * $result = $method->invoke($obj,'param1','param2');
     *
     * @param Object|string $obj
     * @param string $name
     *
     * @return method
     */
    public static function getMethod($obj, $name) {
        $class = new ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
