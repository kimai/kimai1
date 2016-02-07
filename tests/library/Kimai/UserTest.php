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

namespace KimaiTest;

use PHPUnit_Framework_TestCase;
use Kimai_User;

/**
 * Class UserTest
 *
 * @package KimaiTest
 */
class UserTest extends PHPUnit_Framework_TestCase
{

    public function testIsAdmin()
    {
        $user = new Kimai_User(array('status' => Kimai_User::ADMIN));
        $this->assertTrue($user->isAdmin());
    }

}