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


class httpAuth extends AuthBase {
  /**
   * support autologin
   */
  public function autoLoginPossible() {
    return true;
  }

 /**
   * return true if autologin was successful, and put the username in $userId
   */
  public function performAutoLogin(&$userId) {
        global $kga;
        $username = $this->getHTTPAuthUsername();
        if ($username != "") {
                $result = mysql_query(sprintf("SELECT * FROM %susr WHERE usr_name ='%s';",$kga['server_prefix'], mysql_real_escape_string($username)));
                  if (mysql_num_rows($result) == 1) {
                          $row    = mysql_fetch_assoc($result);
      $pass    = $row['pw'];
      $ban     = $row['ban'];
      $banTime = $row['banTime'];
      $userId  = $row['usr_ID'];


                        return true;
                  }
        }
        return false;
  }

  public function authenticate($username,$plainPassword,&$userId) {
    return false;
  }
  private function getHTTPAuthUsername() {
	$username = "";
	if ( isset($_SERVER["PHP_AUTH_USER"]) ) {
		$username = $_SERVER["PHP_AUTH_USER"];
	} elseif ( isset( $_SERVER['REMOTE_USER'] ) ) {
		$username = $_SERVER['REMOTE_USER'] ;
	} elseif ( isset( $_SERVER["REDIRECT_REMOTE_USER"] ) ) {
		$username = $_SERVER["REDIRECT_REMOTE_USER"] ;
	}
   return $username;
  }
}
