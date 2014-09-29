<?php

/**
 * Copyright (C) 2011 by Skaldrom Y. Sarg of oncode.info
 * Copyright (C) 2014 by Andreas Heigl<andreas@heigl.org>
 *
 * This is free software. Use it however you want
 *
 * To activate this Authentication Adapter, add the following line to the
 * ```includes/autoconf.php```-file:
 *
 *     $authenticator = 'ldapadvanced';
 *
 * To use your own config you can either overwrite the values given in this
 * class or you extend this class with your own class just containing your own
 * configuration. More Information is provided in the LDAP_README.md
 *
 * @author Andreas Heigl<andras@heigl.org>
 * @since  15.08.2014
 */

class Kimai_Auth_Ldapadvanced extends Kimai_Auth_Abstract
{

    /**
     * Your LDAP-Server URI
     *
     * Remember that you can include ldaps scheme or a port number here
     *
     * @var string $host
     */
    protected $host = 'ldap://localhost';

    /**
     * Bind-DN of a user that has read access to the ldap.
     *
     * Leave empty for anonymous bind
     *
     * @var string $bindDN
     */
    protected $bindDN = '';

    /**
     * The password to ue for non anonymous bind
     *
     * @var string $bindPW
     */
    protected $bindPW = '';

    /**
     * Search base to use
     *
     * @var string $searchBase
     */
    protected  $searchBase = 'dc=example,c=org';

    /**
     * The filter to use when searching for a user
     *
     * The string '%s' will be replaced by the string the user provided as username
     *
     * @var string $userFilter
     */
    protected $userFilter = 'uid=%s';

    /**
     * The filter to be used when checking group memberships
     *
     * The string %1$s will be replaced by the content of the attribute self::$usernameAttribute,
     * the string %2$s will be replaced by the DN of the user
     *
     * @param string $groupFilter
     */
    protected $groupFilter = 'memberUid=%1$s';

    /**
     * Which LDAP-Attribute contains the username
     *
     * @param string $usernameAttribute
     */
    protected $usernameAttribute = 'uid';

    /**
     * Which LDAP-Attribute contains a readable username
     *
     * @param string $commonNameAttribute
     */
    protected $commonNameAttribute = 'cn';

    /**
     * Which LDAP-Attribute contains the group-id
     *
     * This is referenced by the entries of self::$allowedGroupIds
     *
     * @var string $groupidAttribute
     */
    protected $groupidAttribute = 'cn';

    /**
     * Which LDAP-Attribute contains the email-address
     *
     * This is just to set the correct mail-address for the user
     *
     * @var string $mailAttribute
     */
    protected $mailAttribute = 'mail';

    /**
     * Members of which LDAP-groups shall have access to kimai
     *
     * @var array $allowedGroupIds
     */
    protected $allowedGroupIds = array(
        'kimai-access',
    );

    /**
     * Shall we force usernames to lowercase?
     *
     * @var boolean $forceLowercase
     */
    protected $forceLowercase = true;

    /**
     * Accounts that sould be verified locally.
     *
     * All entries in this array will not be checked against the LDAP
     *
     * @var array $nonLdapAccounts
     */
    protected $nonLdapAcounts = array(
        'admin'
    );

    /**
     * Automatically create a user in kimai if the login is successful.
     *
     * @var boolean $autocreateUsers
     */
    protected $autocreateUsers = true;

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
     * @var Kimai_Auth_Kimai $kimaiAuth
     */
    private $kimaiAuth;

    /**
     * {@inherit}
     */
    public function __construct($database = null, $kga = null)
    {
        if (! function_exists('ldap_bind')) {
            throw new InvalidArgumentException('LDAP-Extension is not installed');
        }
        parent::__construct($database, $kga);
        $this->kimaiAuth = new Kimai_Auth_Kimai($database, $kga);
    }

    /**
     * {@inherit}
     */
    public function authenticate($username, $password, &$userId)
    {
        // Check if username should be authenticated locally
        if (in_array($username, $this->nonLdapAcounts)) {
            return $this->kimaiAuth->authenticate($username, $password, $userId);
        }


        if (! $username || ! $password) {
            $userId = false;
            return false;
        }

        // Connect to LDAP
        $connect_result = ldap_connect($this->host);

        if (!$connect_result) {
            echo "Cannot connect to ", $this->host;
            $userId = false;
            return false;
        }

        ldap_set_option($connect_result, LDAP_OPT_PROTOCOL_VERSION, 3);

        // Bind to the ldap and query for the given userinformation.
        if ($this->bindDN && $this->bindPW) {
            $bindResult = ldap_bind($connect_result, $this->bindDN, $this->bindPW);
        } else {
            $bindResult = ldap_bind($connect_result);
        }

        if (! $bindResult) {
            echo sprintf(
                "Can't bind to the LDAP with DN %s",
                $this->bindDN
            );
            $userId = false;
            return false;
        }

        $filter = sprintf($this->userFilter, $username);

        $_ldapresults = ldap_search(
            $connect_result,
            $this->searchBase,
            $filter,
            array(
                $this->usernameAttribute,
                $this->mailAttribute,
                $this->commonNameAttribute,
            ),
            0,
            0,
            10
        );
        if (! $_ldapresults) {
            // The server returned no result-set at all.
            echo "No user with that information found";
            $userId = false;
            return false;
        }
        if (1 > ldap_count_entries($connect_result, $_ldapresults)) {
            // The returned result set contains no data.
            echo "No user with that information found";
            $userId = false;
            return false;
        }
        if (1 < ldap_count_entries($connect_result, $_ldapresults)) {
            // The returned result-set contains more than one person. So we
            // can not be sure, that the user is unique.
            echo "More than one user found with that information";
            $userId = false;
            return false;
        }

        $_results = ldap_get_entries($connect_result, $_ldapresults);
        if (false === $_results) {
            // The returned result-set could not be retrieved.
            echo 'no result set found';
            $userId = false;
            return false;
        }
        // Empty the result set. We have the results in a variable so don't
        // bother the server any more.
        ldap_free_result ($_ldapresults);
        $distinguishedName = $_results[0]['dn'];
        $uidAttribute      = $_results[0][$this->usernameAttribute][0];
        $emailAddress      = '';
        $commonName        = '';
        if (isset($_results[0][$this->mailAttribute][0])) {
            $emailAddress = $_results[0][$this->mailAttribute][0];
        }
        if (isset($_results[0][$this->commonNameAttribute][0])) {
            $commonName = $_results[0][$this->commonNameAttribute][0];
        }

        // Now lets try to bind with the returned distinguishedName and the
        // provided passwort to the LDAP.
        $link_id = @ldap_bind($connect_result, $distinguishedName, $password);
        if (false === $link_id) {
            echo 'Password and/or Username mismatch';
            $userId = false;
            return false;
        }

        // Check whether the user is member of one of the required LDAP-groups
        $filter = sprintf($this->groupFilter, $uidAttribute, $distinguishedName);
        $_ldapresults = ldap_search(
            $connect_result,
            $this->searchBase,
            $filter,
            array($this->groupidAttribute),
            0,
            0,
            10
        );
        if (! $_ldapresults) {
            // The server returned no result-set at all.
            echo "No group for the user found";
            $userId = false;
            return false;
        }
        if (1 > ldap_count_entries($connect_result, $_ldapresults)) {
            // The returned result set contains no data.
            echo "No group for that user found";
            $userId = false;
            return false;
        }
        $_results = ldap_get_entries($connect_result, $_ldapresults);
        if (false === $_results) {
            // The returned result-set could not be retrieved.
            echo 'no result set for groups found';
            $userId = false;
            return false;
        }
        ldap_free_result ( $_ldapresults );

        $groups = array();
        foreach ($_results as $result) {
            $resultGroups = array();
            for ($i = 0; $i < $result[$this->groupidAttribute]['count']; $i++) {
                $resultGroups[] = $result[$this->groupidAttribute][$i];
            }
            $groups = array_merge($groups, $resultGroups);
        }

        if (! array_intersect($groups, $this->allowedGroupIds)) {
            // The returned result-set could not be retrieved.
            echo 'no valid groups found';
            $userId = false;
            return false;
        }

        // User is authenticated. Does it exist in Kimai yet?
        $check_username = $this->forceLowercase ? strtolower($uidAttribute) : $uidAttribute;

        $userId = $this->database->user_name2id($check_username);
        if ($userId === false)  {
            // User does not exist (yet)
            if ($this->autocreateUsers) {
                // Create it!
                $userId = $this->database->user_create(array(
                    'name'         => $check_username,
                    'globalRoleID' => $this->getDefaultGlobalRole(),
                    'active'       => 1
                ));

                $this->database->setGroupMemberships($userId, $this->getDefaultGroups());

                // Set a password, to calm kimai down
                $usr_data = array('password' => md5($this->kga['password_salt'] . md5(uniqid(rand(), true)) . $this->kga['password_salt']));
                if ($emailAddress) {
                    $usr_data['mail'] = $emailAddress;
                }
                if ($commonName) {
                    $usr_data['alias'] = $commonName;
                }
                $this->database->user_edit($userId, $usr_data);
            } else {
                $userId = false;
                return false;
            }
        }

        return true;
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
            if (! isset($groups[$group])) {
                continue;
            }
            if (! isset($roles[$role])) {
                continue;
            }

            $map[$groups[$group]] = $roles[$role];
        }

        return $map;
    }
}