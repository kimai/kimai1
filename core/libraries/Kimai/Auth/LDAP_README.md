# LDAP-Authentication

## Usage

To use LDAP-Authentication in kimai you will have to do one of the following:

 * create a new class "Kimai_Auth_Yourname" (where "yourname has to be lowercase ecept for the first character) that extends Kiami_Auth_Ldap and overwrite the properties of Kimai_Auth_Ldap with your data or
 * Simply configure the stuff in the Kimai_Auth_Ldap class by overwriting the defaults for the properties

Then you will have to add the following line to your ```includes/autofonf.php```-file:

    $authenticator = 'yourname';

*yourname* is the last part of the classname, this time completely in lowercase. If you overwrote the properties from Kimai_Auth_Ldap it is simply ```ldap```

## Configuration-parameters

 * **host:** This is the URI to connect with your LDAP-Server. This can be something like ```ldap://ldap.example.com``` or ``` ldaps://ldap.example.com:1234```
 * **bindDN:** This is the DN of a user with read access to the LDAP. Leave empty when your LDAP supports anonymous bind
 * **bindPW:** the password for the user with read access to the LDAP. Leave empty also for anonymous bind
 * **searchBase:** Where do your searches start in the ldap. This is normaly something like ```o=example,c=org```
 * **userFilter:** What filter shall be used to search for a user. The string ```%1$s``` will be replaced with what the user entered as login name. You can use that string multiple times to enable login by UID **and** email. The filter would then be ```(|(uid=%1$s)(mail=%1$s))```
 * **groupFilter:** What filter shall be used to heck for group memberships. The string ```%1$s``` will be replaced by the value of the attribute defined by ```usernameAttribute``` of the user-entry. The string ```%2$s``` will be replaced by the DN of the users entry;
 * **usernameAttribute:** The attribute to be sed to check for group memberships **as well as** retrieving the username to be used by kimai
 * **commonNameAttribute:** This attribute defines the alias of the user in kimai
 * **groupidAttribute:** This attribute contains the value that is represented in the ```allowedGroupIds```
 * **mailAttribute:** This attribute holds the users email-address that will be ported to kimai
 * **allowedGroupIds:** An array of values defined by ```groupidAttribute```. Members of the LDAP-groups referenced here will be allowed access to kimai!
 * **forceLowercase:** Whether the username for kimai shall be lowercased or not.
 * **nonLdapAcounts:** A list of kimai-usernames that shall **not** be autehnticated via LDAP but via the default kimai-authentication-adapter
 * **autocreateUsers:** Shall uses authenticated via LDAP be created automatically in kimai. If set to false the users have to be added manually to kimai and only password-verification will be handled via LDAP
 * **defaultGlobalRoleName:** The name of the default role newly created users will be associated with
 * **defaultGroupMemberships:** An array of group=>role mappings the user shall also be associated with
 


