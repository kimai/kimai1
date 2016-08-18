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

/**
 * Authenticator to be used with Microsofts Active Directory.
 *
 * Supports the "Enhanced Identity Privacy" option, see:
 * https://technet.microsoft.com/en-us/library/f351e0e3-6c78-49dc-9b0f-2b24e1b7411c
 *
 * To activate this Authentication Adapter, add the following line to the
 * file ```includes/autoconf.php```
 *
 *     $authenticator = 'activeDirectory';
 *
 * To activate "Enhanced Identity Privacy" create the file includes/auth.php and
 * activate at least the setting 'enhancedIdentityPrivacy':
 *
 *      return array('enhancedIdentityPrivacy' => true);
 *
 */
class Kimai_Auth_ActiveDirectory extends Kimai_Auth_Ldapadvanced
{
    protected $enhancedIdentityPrivacy = false;

    protected function createCheckUsername($username, $uidAttribute)
    {
        if ($this->enhancedIdentityPrivacy) {
            return $this->forceLowercase ? strtolower($username) : $username;
        }
        return parent::createCheckUsername($username, $uidAttribute);
    }
}
