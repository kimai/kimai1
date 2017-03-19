<div class="content">
    <div id="adminPanel_extension_output"></div>
    <form id="adminPanel_extension_form_editauth" action="../extensions/ki_adminpanel/processor.php" method="post">
        <input name="axAction" type="hidden" value="sendEditAuthentication" />
        <fieldset class="adminPanel_extension_advanced">
            <div>
                <label><?php echo $this->kga['lang']['authentication'] ?>:</label>
    		<select name="authenticator" id="authenticator" class="">
                    <option id="kimai" value="kimai">kimai</option>
                    <option id="http" value="http" >http</option>
                    <option id="ldap" value="ldap" >ldap</option>
		    <option id="ldapadvanced" value="ldapadvanced" >ldapadvanced</option>
		    <option id="activeDirectory" value="activeDirectory" >activeDirectory</option>
                </select>
            </div>
            <div id="kimai_auth" style="display:none">
                <?php echo $this->escape($this->kga['lang']['authsettings']['kimai_auth_desc']) ?>
            </div>
            <div id="http_auth" style="display:none">
                <?php echo $this->escape($this->kga['lang']['authsettings']['http_auth_desc']) ?>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['allowAutoLogin']) ?>: <input type="checkbox" name="http_allowAutoLogin" value="1" <?php if ($this->escape($this->kga->getHttpAllowAutoLogin())): ?>checked="checked" <?php endif; ?> class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['forceLowercase']) ?>: <input type="checkbox" name="http_forceLowercase" value="1" <?php if ($this->escape($this->kga->getHttpForceLowercase())): ?> checked="checked" <?php endif; ?> class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['autocreateUsers']) ?>: <input type="checkbox" name="http_autocreateUsers" value="1" <?php if ($this->escape($this->kga->getHttpAutocreateUsers())): ?> checked="checked" <?php endif; ?> class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['phpAuthUser']) ?>: <input type="checkbox" name="http_phpAuthUser" value="1" <?php if ($this->escape($this->kga->getHttpPhpAuthUser())): ?> checked="checked" <?php endif; ?> class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['remoteuser']) ?>: <input type="checkbox" name="http_remoteuser" value="1" <?php if ($this->escape($this->kga->getHttpRemoteuser())): ?> checked="checked" <?php endif; ?> class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['redirectRemoteUser']) ?>: <input type="checkbox" name="http_redirectRemoteUser" value="1" <?php if ($this->escape($this->kga->getHttpRedirectRemoteUser())): ?> checked="checked" <?php endif; ?> class="formfield">
            </div>
            </div>
            <div id="ldap_auth" style="display:none">
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['host']) ?>: <input type="text" name="l_ldaphost" size="20" value="<?php echo $this->escape($this->kga->getLdapHost()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['forceLowercase']) ?>: <input type="checkbox" name="l_forceLowercase" value="1" <?php if ($this->escape($this->kga->getLdapForceLowercase())): ?> checked="checked" <?php endif; ?> class="formfield"> 
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['usernameprefix']) ?>: <input type="text" name="l_usernameprefix" size="20" value="<?php echo $this->escape($this->kga->getLdapUsernameprefix()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['usernamepostfix']) ?>: <input type="text" name="l_usernamepostfix" size="20" value="<?php echo $this->escape($this->kga->getLdapUsernamepostfix()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['nonLdapAccounts']) ?>: <input type="text" name="l_nonLdapAccounts" size="20" value="<?php echo $this->escape($this->kga->getLdapNonLdapAccounts()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['autocreateUsers']) ?>: <input type="checkbox" name="l_autocreateUsers" value="1" <?php if ($this->escape($this->kga->getLdapAutocreateUsers())): ?> checked="checked" <?php endif; ?> class="formfield">
            </div>
            </div>
            <div id="ldapadvanced_auth" style="display:none">
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['host']) ?>: <input type="text" name="la_ldaphost" size="20" value="<?php echo $this->escape($this->kga->getLdapAdvHost()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['bindDN']) ?>: <input type="text" name="la_bindDN" size="20" value="<?php echo $this->escape($this->kga->getLdapAdvBindDN()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['bindPW']) ?>: <input type="password" name="la_bindPW" size="20" value="<?php echo $this->escape($this->kga->getLdapAdvBindPW()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['searchBase']) ?>: <input type="text" name="la_searchBase" size="20" value="<?php echo $this->escape($this->kga->getLdapAdvSearchBase()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['userFilter']) ?>: <input type="text" name="la_userFilter" size="20" value="<?php echo $this->escape($this->kga->getLdapAdvUserFilter()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['groupFilter']) ?>: <input type="text" name="la_groupFilter" size="20" value="<?php echo $this->escape($this->kga->getLdapAdvGroupFilter()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['usernameAttribute']) ?>: <input type="text" name="la_usernameAttribute" size="20" value="<?php echo $this->escape($this->kga->getLdapAdvUsernameAttribute()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['commonNameAttribute']) ?>: <input type="text" name="la_commonNameAttribute" size="20" value="<?php echo $this->escape($this->kga->getLdapAdvCommonNameAttribute()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['groupidAttribute']) ?>: <input type="text" name="la_groupidAttribute" size="20" value="<?php echo $this->escape($this->kga->getLdapAdvGroupidAttribute()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['mailAttribute']) ?>: <input type="text" name="la_mailAttribute" size="20" value="<?php echo $this->escape($this->kga->getLdapAdvMailAttribute()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['allowedGroupIds']) ?>: <input type="text" name="la_allowedGroupIds" size="20" value="<?php echo $this->escape($this->kga->getLdapAdvAllowedGroupIds()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['forceLowercase']) ?>: <input type="checkbox" name="la_forceLowercase" value="1" <?php if ($this->escape($this->kga->getLdapAdvForceLowercase())): ?> checked="checked" <?php endif; ?> class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['nonLdapAccounts']) ?>: <input type="text" name="la_nonLdapAccounts" size="20" value="<?php echo $this->escape($this->kga->getLdapAdvNonLdapAccounts()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['autocreateUsers']) ?>: <input type="checkbox" name="la_autocreateUsers" value="1" <?php if ($this->escape($this->kga->getLdapAdvAutocreateUsers())): ?> checked="checked" <?php endif; ?> class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['defaultGlobalRoleName']) ?>: <input type="text" name="la_defaultGlobalRoleName" size="20" value="<?php echo $this->escape($this->kga->getLdapAdvDefaultGlobalRoleName()) ?>" class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['createGroupMembershipsOnLogin']) ?>: <input type="checkbox" name="la_createGroupMembershipsOnLogin" value="1" <?php if ($this->escape($this->kga->getLdapAdvCreateGroupMembershipsOnLogin())): ?> checked="checked" <?php endif; ?> class="formfield">
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['defaultGroupMemberships']) ?>: <input type="text" name="la_defaultGroupMemberships" size="20" value="<?php echo $this->escape($this->kga->getLdapAdvDefaultGroupMemberships()) ?>" class="formfield">
            </div>
            </div>
            <div id="activeDirectory_auth" style="display:none">
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['enhancedIdentityPrivacy']) ?>: <input type="checkbox" name="enhancedIdentityPrivacy" value="1" <?php if ($this->escape($this->kga->getADEnhancedIdentityPrivacy())): ?> checked="checked" <?php endif; ?>  class="formfield">
            </div>
            </div>
            <div id="formbuttons">
                <input id="adminPanel_extension_form_editauth_submit" class="btn_ok" type="submit" value="<?php echo $this->kga['lang']['save']?>" />
            </div>
        </fieldset>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.disableInput').click(function(){
            var input = $(this);
            if (input.is (':checked')) {
                input.parent().removeClass('disabled');
                input.siblings().prop('disabled', false);
            } else {
                input.parent().addClass('disabled');
                input.siblings().prop('disabled', true);
            }
        });

        $('#adminPanel_extension_form_editauth').ajaxForm({
            target: '#adminPanel_extension_output',
            success: function (result) {
                if (result.errors.length == 0) {
                    window.location.reload();
                    return;
                }
                $('#adminPanel_extension_form_editauth_submit').blur();
                var $adminPanel_extension_output = $('#adminPanel_extension_output');
                $adminPanel_extension_output.width($('.adminPanel_extension_panel_header').width() - 22);
                $adminPanel_extension_output.fadeIn(fading_enabled ? 500 : 0, function () {
                    $adminPanel_extension_output.fadeOut(fading_enabled ? 4000 : 0);
                });
            }
        });
        $('#authenticator').val('<?php echo $this->kga->getAuthenticator() ?>');
        $('#authenticator').change(function() {
            // hide all
            $('#kimai_auth, #http_auth, #ldap_auth, #ldapadvanced_auth, #activeDirectory_auth').hide();
            // show current
            if ($(this).find('option:selected').attr('value') == 'activeDirectory') {
                $('#ldapadvanced_auth').show()
            }
            $('#' + $(this).find('option:selected').attr('value') + '_auth').show();
        });
        $('#authenticator').change();
    });
</script>
