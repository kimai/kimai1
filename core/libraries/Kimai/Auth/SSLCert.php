<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * Copyright (C) 2014 Michael Gissing
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
 * This class authenticates a Kimai user based on an SSL client certificate.
 *
 * Currently this authentication method supports Apache's httpd together with
 * its mod_ssl. See http://httpd.apache.org/docs/2.2/mod/mod_ssl.html for
 * the mod_ssl documentation.
 *
 * In order to get the needed information inside PHP Apache has to be configured
 * to pass on the information. Therefore
 *
 *      SSLOptions +StdEnvVars
 *
 * has to be set in the host's configuration.
 *
 */
class Kimai_Auth_SSLCert extends Kimai_Auth_Abstract {

    // TODO this option is not used yet
    /** Whether SSL client cert is required */
    private $SSL_AUTH_CERT_REQUIRED = true;

    /** Whether a new user is automatically created when not yet existing */
    private $SSL_AUTH_AUTO_CREATE_USER = true;

    /**
     * @var Kimai_Auth_Kimai|null
     */
    private $kimaiAuth = null;

    const KEY_EMAIL       = 'SSL_CLIENT_S_DN_Email';
    const KEY_VERIFY      = 'SSL_CLIENT_VERIFY';
    const KEY_NAME        = 'SSL_CLIENT_S_DN';

    public function __construct($database = null, $kga = null) {
        parent::__construct($database, $kga);
        $this->kimaiAuth = new Kimai_Auth_Kimai($database, $kga);
    }

    public function autoLoginPossible() {
        return true;
    }

    public function performAutoLogin(&$userId) {
        $userId = false;

        if (!$this->certIsValid()) return false;

        $username = $this->getUsername();

        if ($username === null) return false;

        $kga = $this->getKga();
        $db  = $this->getDatabase();

        $id = $db->user_name2id($username);

        if ($id === false && $this->SSL_AUTH_AUTO_CREATE_USER) {
            // User not in DB - auto create
            // TODO the 'mail' and 'password' fields are currently useless, see kimai issue #381
            $id = $db->user_create(array(
                'name' => $username,
                'mail' => $this->getEMail(),
                // TODO set dummy password in proper format for purely esthetical reasons
                'password' => md5('no password set'),
                'globalRoleID' => $this->getDefaultGlobalRole(),
                'active' => 1
            ));
            $db->setGroupMemberships($id, array($this->getDefaultGroups()));
        }

        $userId = $id;
        return $id !== false;
    }

    public function authenticate($username, $password, &$userId) {
        // TODO better handling of DB based logins
        // TODO admin user might get renamed

        if ($username === 'admin') {
            // permit DB based login of admin
            return $this->kimaiAuth->authenticate($username, $password, $userId);
        }

        $userId = false;
        return false;
    }

    /**
     * Returns the value of $_SERVER[$key] if set, null otherwise.
     *
     * If the second parameter is set to <code>true</code> the method
     * will also check whether the certificate was validated by the server. If
     * it was not verified, null is returned.
     *
     * @param $key the key of the value which should be obtained from
     *     <code>$_SERVER</code>.
     * @param $verify if this parameter is true than this function will return
     *     <code>null</code> instead of the value if the certificate was not
     *     verified by the server.
     * @return the value or <code>null</code>.
     */
    private function getVar($key, $verify=true) {
        if ($verify && !$this->certIsValid()) return null;
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }

    /**
     * Checks whether the client provided a valid certificate.
     *
     * @return <code>true</code> if the certificate is valid.
     */
    private function certIsValid() {
        return $this->getVar(self::KEY_VERIFY, false) === 'SUCCESS';
    }

    /**
     * Get the Kimai user name based on the client certificate.
     *
     * @return the user name.
     */
    private function getUsername() {
        return $this->getEMail();
    }

    /**
     * Get the user's real name based on the client certificate.
     *
     * @return the user's name.
     */
    private function getRealName() {
        return $this->getVar(self::KEY_NAME);
    }

    /**
     * Get user's email address based on the client certificate.
     *
     * @return the email address.
     */
   private function getEMail() {
        return $this->getVar(self::KEY_EMAIL);
   }
}
