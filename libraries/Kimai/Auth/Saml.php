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
 * Saml authentication based on standard Kimai auth
 */
class Kimai_Auth_Saml extends Kimai_Auth_Abstract
{
    /**
     * Shall we have strict SAML checks enforced?
     *
     * @var boolean $saml_strict
     */
    protected $saml_strict = true;

    /**
     * Enable SAML debugging?
     *
     * @var boolean $saml_debug
     */
    protected $saml_debug = true;

    /**
     * The SAML base URL
     *
     * @var string $saml_baseurl
     */
    protected $saml_baseurl = '';

    /**
     * The SAML Service Provider Entity ID
     *
     * @var string $saml_spentityId
     */
    protected $saml_spentityId = 'https://kimai.local/libraries/onelogin/php-saml/demo2/metadata.php';

    /**
     * The SAML Service Provider ACS URL
     *
     * @var string saml_spacsURL
     */
    protected $saml_spacsURL = 'https://kimai.local/index.php';

    /**
     * The SAML Service Provider SLS URL
     *
     * @var string $saml_spslsURL
     */
    protected $saml_spslsURL = 'https://kimai.local/index.php';

    /**
     * The SAML Service Provider x509 Certificate
     *
     * @var string $saml_spx509cert
     */
    protected $saml_spx509cert = '';

    /**
     * The SAML Service Provider private key
     *
     * @var string $saml_spprivateKey
     */
    protected $saml_spprivateKey = '';

    /**
     * The SAML Identity Provider entityId
     *
     * @var string $saml_idpentityId
     */
    protected $saml_idpentityId = 'https://accounts.google.com/o/saml2?idpid=#########';

    /**
     * The SAML Identity Provider SSO URL
     *
     * @var string $saml_idpssoURL
     */
    protected $saml_idpssoURL = 'https://accounts.google.com/o/saml2/idp?idpid=#########';

    /**
     * The SAML Identity Provider SLS URL
     *
     * @var string $saml_idpslsURL
     */
    protected $saml_idpslsURL = '';

    /**
     * The SAML Identity Provider Certificate Fingerprint
     *
     * @var string $saml_idpcertFingerprint
     */
    protected $saml_idpcertFingerprint = 'AA:AA:AA:AA:AA:AA:AA:AA:AA:AA:AA:AA:AA:AA:AA:AA:AA:AA:AA:AA';

    /**
     * The SAML Identity Provider Certificate Fingerprint Algorithim
     *
     * @var string $saml_idpcertFingerprintAlgorithm
     */
    protected $saml_idpcertFingerprintAlgorithm = 'sha1';

    /**
     * Accounts that should be verified locally.
     *
     * All entries in this array will not be checked against the SAML
     *
     * @var array $nonSAMLAccounts
     */
    protected $nonSAMLAcounts = array(
        'admin'
    );

    /**
     * The name of the default global role the user should be added to.
     *
     * @var string $defaultGlobalRoleName
     */
    protected $defaultGlobalRoleName = 'User';

    /**
     * Map of group=>role names for new users
     *
     * @var array $defaultGroupMemberships
     */
    protected $defaultGroupMemberships = array(
        'Users' => 'User',
    );

    /**
     * The text to remove from the beginning of a username.
     *
     * @var string $removeUsernamePrefix
     */
    protected $removeUsernamePrefix = '';

    /**
     * The text to remove from the end of a username.
     *
     * @var string $removeUsernameSuffix
     */
    protected $removeUsernameSuffix = '';

    /**
     * {@inherit}
     */
    public function __construct($database = null, $kga = null)
    {
        parent::__construct($database, $kga);
        $this->kimaiAuth = new Kimai_Auth_Kimai($database, $kga);

        $this->saml_settings = array(
            'strict' => $this->saml_strict,
            'debug' => $this->saml_debug,
            'baseurl' => $this->saml_baseurl,
            'sp' => array(
                'entityId' => $this->saml_spentityId,
                'assertionConsumerService' => array(
                    'url' => $this->saml_spacsURL,
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                ),
                'singleLogoutService' => array(
                    'url' => $this->saml_spslsURL,
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ),
                'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
                'x509cert' => $this->saml_spx509cert,
                'privateKey' => $this->saml_spprivateKey,
            ),
            'idp' => array(
                'entityId' => $this->saml_idpentityId,
                'singleSignOnService' => array(
                    'url' => $this->saml_idpssoURL,
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ),
                'singleLogoutService' => array(
                    'url' => $this->saml_idpslsURL,
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ),
                'certFingerprint' => $this->saml_idpcertFingerprint,
                'certFingerprintAlgorithm' => $this->saml_idpcertFingerprintAlgorithm,
            ),
        );
    }

    /**
     * Decides whether this authentication method should be used to authenticate
     * users before they have provided any credentials.
     *
     * This allows users to be logged in automatically. Mostly used with SSO (single sign on) solutions.
     *
     * @return boolean <code>true</code> if this authentication method can login users without credentials,
     *   <code>false</code> otherwise
     */
    public function autoLoginPossible()
    {
        // This is cannot be set to false for Saml Authentication
        return true;
    }

    /**
     * Try to authenticate the user before he sees the login page.
     *
     * @param int $userId is set to the id of the user. If none exists it will be false
     * @return boolean either true if the user could be authenticated or false otherwise
     */
    public function performAutoLogin(&$userId)
    {
        Kimai_Logger::logfile("SAML: performAutoLogin");

        $userId = false;
        $check_username = '';
        if (!isset($_SESSION['samlUserdata'])) {
            Kimai_Logger::logfile("SAML: samlUserdata not set");
            $settings = new OneLogin_Saml2_Settings($this->saml_settings);
            $authRequest = new OneLogin_Saml2_AuthnRequest($settings);
            $samlRequest = $authRequest->getRequest();

            $parameters = array('SAMLRequest' => $samlRequest);
            $parameters['RelayState'] = OneLogin_Saml2_Utils::getSelfURLNoQuery();

            $idpData = $settings->getIdPData();
            $ssoUrl = $idpData['singleSignOnService']['url'];
            $url = OneLogin_Saml2_Utils::redirect($ssoUrl, $parameters, true);

            header("Location: $url");
        }
        return false;
    }

    public function processResponse($idpResponse, &$userId)
    {
        Kimai_Logger::logfile("SAML: processResponse");
        $auth = new OneLogin_Saml2_Auth($this->saml_settings);
        $auth->processResponse();
        $errors = $auth->getErrors();

        if (!empty($errors)) {
            Kimai_Logger::logfile("SAML: Errors: " . $errors);
            exit();
        }

        $settings = new OneLogin_Saml2_Settings($this->saml_settings);
        $samlResponse = new OneLogin_Saml2_Response($settings, $idpResponse);
        if (!$auth->isAuthenticated()) {
            Kimai_Logger::logfile("SAML: Not authenticated");
            exit();
        }
        if ($samlResponse->isValid()) {
            Kimai_Logger::logfile("SAML: You are: " . $samlResponse->getNameId());
            $check_username = $samlResponse->getNameId();
            $attributes = $samlResponse->getAttributes();
            if (!empty($attributes)) {
                $alias = $attributes['fn'][0] . " " . $attributes['ln'][0];
                $mail = $attributes['mail'][0];
            }
        } else {
            Kimai_Logger::logfile("SAML: Invalid SAML Response");
            return false;
        }
        // Remove the prefix and suffix from the user name
        $lenprefix = strlen($this->removeUsernamePrefix);
        if (substr(strtoupper($check_username), 0, $lenprefix) == strtoupper($this->removeUsernamePrefix)) {
            $check_username = substr($check_username, $lenprefix);
        }

        $suffixStart = strlen($check_username)-strlen($this->removeUsernameSuffix);
        if (substr(strtoupper($check_username), $suffixStart, strlen($check_username)) == strtoupper($this->removeUsernameSuffix)) {
            $check_username = substr($check_username, 0, $suffixStart);
        }

        $userId = $this->database->user_name2id($check_username);

        if ($userId !== false) {
            Kimai_Logger::logfile("SAML: Username: " . $check_username . " exists!");
            return true;
        } else {
            $userId = $this->database->user_create(array(
                'name' => $check_username,
                'globalRoleID' => $this->getDefaultGlobalRole(),
                'active' => 1,
                'password' => encode_password(md5(uniqid(rand(), true)))
            ));

            $this->database->setGroupMemberships($userId, $this->getDefaultGroups());
            // Set a password, to calm kimai down
            $usr_data = array('password' => md5($this->kga['password_salt'] . md5(uniqid(rand(), true)) . $this->kga['password_salt']));
            if ($mail) {
                $usr_data['mail'] = $mail;
            }
            if ($alias) {
                $usr_data['alias'] = $alias;
            }
            $this->database->user_edit($userId, $usr_data);
            return true;
        }
    }

    /**
     * Get the default global role
     *
     * @return integer
     */
    public function getDefaultGlobalRole()
    {
        if ($this->defaultGlobalRoleName) {
            $database = $this->getDatabase();

            $roles = $database->global_roles();

            foreach ($roles as $role) {
                if ($role['name'] == $this->defaultGlobalRoleName) {
                    return $role['globalRoleID'];
                }
            }
        }

        return parent::getDefaultGlobalRole();
    }

    /**
     * Get a map of group=>role associations for new users
     *
     * @return array
     */
    public function getDefaultGroups()
    {
        $groups = array();
        $roles  = array();
        $map    = array();

        $database = $this->getDatabase();
        foreach ($database->membership_roles() as $role) {
            $roles[$role['name']] = $role['membershipRoleID'];
        }

        foreach ($database->get_groups() as $group) {
            $groups[$group['name']] = $group['groupID'];
        }

        foreach ($this->defaultGroupMemberships as $group => $role) {
            if (!isset($groups[$group])) {
                continue;
            }
            if (!isset($roles[$role])) {
                continue;
            }

            $map[$groups[$group]] = $roles[$role];
        }

        return $map;
    }

    /**
     * @param string $username
     * @param string $password
     * @param int $userId
     * @return bool
     */
    public function authenticate($username, $password, &$userId)
    {
        // Check if username should be authenticated locally
        if (in_array($username, $this->nonSAMLAcounts)) {
            Kimai_Logger::logfile("SAML: Local Authentication for: " .$username);
            return $this->kimaiAuth->authenticate($username, $password, $userId);
        }

        return false;
    }
}
