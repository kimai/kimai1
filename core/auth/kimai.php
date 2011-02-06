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
      global $kga;

      $passCrypt = md5($kga['password_salt'].$password.$kga['password_salt']);

      $result = mysql_query(sprintf("SELECT * FROM %susr WHERE usr_name ='%s';",$kga['server_prefix'],mysql_real_escape_string($username)));
      if (mysql_num_rows($result) != 1) {
        $userId = false;
        return false;
      }

      $row    = mysql_fetch_assoc($result);
      $pass    = $row['pw'];        
      $ban     = $row['ban'];
      $banTime = $row['banTime'];   
      $userId  = $row['usr_ID'];
      
      return $pass==$passCrypt && $username!="";
  }
}

// There should be NO trailing whitespaces.
?>