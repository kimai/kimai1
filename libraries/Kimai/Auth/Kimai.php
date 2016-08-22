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
 * Class Kimai_Auth_Kimai
 */
class Kimai_Auth_Kimai extends Kimai_Auth_Abstract
{
    /**
     * @param string $username
     * @param string $password
     * @param int $userId
     * @return bool
     */
    public function authenticate($username, $password, &$userId)
    {
        $kga = $this->getKga();
        $database = $this->getDatabase();

        $userId = $database->user_name2id($username);

        if ($userId === false) {
            return false;
        }

        $passCrypt = encode_password($password);
        $userData = $database->user_get_data($userId);
        $pass = $userData['password'];
        $userId = $userData['userID'];

        return $pass == $passCrypt && $username != '';
    }

    /**
     * @param string $name
     * @return string
     * @throws \Zend_Mail_Exception
     */
    public function forgotPassword($name)
    {
        $kga = $this->getKga();
        $database = $this->getDatabase();

        $is_customer = $database->is_customer_name($name);

        $mail = new Zend_Mail('utf-8');
        $mail->setFrom($kga['conf']['adminmail'], 'Kimai - Open Source Time Tracking');
        $mail->setSubject($kga['lang']['passwordReset']['mailSubject']);
        
        $transport = new Zend_Mail_Transport_Sendmail();

        $passwordResetHash = str_shuffle(MD5(microtime()));

        if ($is_customer) {
            $customerId = $database->customer_nameToID($name);
            $customer = $database->customer_get_data($customerId);

            $database->customer_edit($customerId, array('passwordResetHash' => $passwordResetHash));

            $mail->addTo($customer['mail']);
        } else {
            $userId = $database->user_name2id($name);
            $user = $database->user_get_data($userId);
            
            $database->user_edit($userId, array('passwordResetHash' => $passwordResetHash));

            $mail->addTo($user['mail']);
        }
        
        Kimai_Logger::logfile('password reset: ' . $name . ($is_customer ? ' as customer' : ' as user'));

        $ssl = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
        $url = ($ssl ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']) . '/forgotPassword.php?name=' . urlencode($name) . '&key=' . $passwordResetHash;

        $message = $kga['lang']['passwordReset']['mailMessage'];
        $message = str_replace('%{URL}', $url, $message);

        $mail->setBodyText($message);

        try {
            $mail->send($transport);

            return $kga['lang']['passwordReset']['mailConfirmation'];
        } catch (Zend_Mail_Transport_Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $key
     * @return array
     */
    public function resetPassword($username, $password, $key)
    {
        $kga = $this->getKga();
        $database = $this->getDatabase();

        $is_customer = $database->is_customer_name($username);

        if ($is_customer) {
            $customerId = $database->customer_nameToID($username);
            $customer = $database->customer_get_data($customerId);
            
            if ($key != $customer['passwordResetHash']) {
                return array(
                    'message' => $kga['lang']['passwordReset']['invalidKey']
                );
            }

            $data = array(
                'password' => encode_password($password),
                'passwordResetHash' => null
            );
            $database->customer_edit($customerId, $data);
        } else {
            $userId = $database->user_name2id($username);
            $user = $database->user_get_data($userId);

            if ($key != $user['passwordResetHash']) {
                return array(
                    'message' => $kga['lang']['passwordReset']['invalidKey']
                );
            }

            $data = array(
                'password' => encode_password($password),
                'passwordResetHash' => null
            );
            $database->user_edit($userId, $data);
        }
        
        return array(
            'message' => $kga['lang']['passwordReset']['success'],
            'showLoginLink' => true,
        );
    }
}
