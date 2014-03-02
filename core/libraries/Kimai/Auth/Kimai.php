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

class Kimai_Auth_Kimai extends Kimai_Auth_Abstract
{
    /**
     * @param $username
     * @param $password
     * @param $userId
     * @return bool
     */
    public function authenticate($username, $password, &$userId)
    {
        $kga      = $this->getKga();
        $database = $this->getDatabase();

        $id = $database->user_name2id($username);

        if ($id === false) {
            $userId = false;
            return false;
        }

        $passCrypt = md5($kga['password_salt'].$password.$kga['password_salt']);
        $userData  = $database->user_get_data($id);
        $pass      = $userData['password'];
        $userId    = $userData['userID'];

        return $pass==$passCrypt && $username!="";
    }

    public function forgotPassword($name)
    {
      $kga      = $this->getKga();
      $database = $this->getDatabase();

      $id = $database->user_name2id($name);

      $user = $database->user_get_data($id);
      $passwordResetHash = str_shuffle(MD5(microtime()));

      $database->user_edit($id, array('passwordResetHash' => $passwordResetHash));

      $ssl = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
      $url = ($ssl? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']) . '/forgotPassword.php?name=' . urlencode($name). '&key=' . $passwordResetHash;

      $message = $kga['lang']['passwordReset']['mailMessage'];
      $message = str_replace('%{URL}', $url, $message);
      error_log($user['mail']);
      mail($user['mail'], 
        $kga['lang']['passwordReset']['mailSubject'], 
        $message);

      return $kga['lang']['passwordReset']['mailConfirmation'];
    }

    public function resetPassword($username, $password, $key)
    {
      $kga      = $this->getKga();
      $database = $this->getDatabase();

      $id = $database->user_name2id($username);
      $user = $database->user_get_data($id);
      if ($key != $user['passwordResetHash']) {
        return array(
          "message" => $kga['lang']['passwordReset']['invalidKey']
        );
      }

      $data = array();
      $data['password'] = md5($kga['password_salt'].$password.$kga['password_salt']);
      $data['passwordResetHash'] = null;
      $database->user_edit($id, $data);

      return array(
        "message" => $kga['lang']['passwordReset']['success'],
        "showLoginLink" => true,
      );
    }

}
