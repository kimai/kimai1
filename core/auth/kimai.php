<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
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

require(WEBROOT.'auth/base.php');


class KimaiAuth extends AuthBase {


  public function authenticate($username,$password,&$userId) {
      global $kga, $database;

      $id = $database->usr_name2id($username);

      if ($id === false) {
        $userId = false;
        return false;
      }
      
      $passCrypt = md5($kga['password_salt'].$password.$kga['password_salt']);

      $userData = $database->usr_get_data($id);

      $pass    = $userData['pw'];     
      $userId  = $userData['usr_ID'];
      
      return $pass==$passCrypt && $username!="";
  }
}

// There should be NO trailing whitespaces.
?>