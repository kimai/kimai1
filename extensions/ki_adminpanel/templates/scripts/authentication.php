<div class="content">
    <div id="adminPanel_extension_output"></div>
    <form id="adminPanel_extension_form_editadv" action="../extensions/ki_adminpanel/processor.php" method="post">
        <input name="axAction" type="hidden" value="sendEditAdvanced" />
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
                <?php echo $this->escape($this->kga['lang']['authsettings']['allowAutoLogin']) ?>: <input type="text" name="allowAutoLogin" size="20" value="<?php echo $this->escape($this->kga['allowAutoLogin']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['allowAutoLogin_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['forceLowercase']) ?>: <input type="text" name="forceLowercase" size="20" value="<?php echo $this->escape($this->kga['forceLowercase']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['forceLowercase_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['autocreateUsers']) ?>: <input type="text" name="autocreateUsers" size="20" value="<?php echo $this->escape($this->kga['autocreateUsers']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['autocreateUsers_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['phpAuthUser']) ?>: <input type="text" name="phpAuthUser" size="20" value="<?php echo $this->escape($this->kga['phpAuthUser']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['phpAuthUser_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['remoteuser']) ?>: <input type="text" name="remoteuser" size="20" value="<?php echo $this->escape($this->kga['remoteuser']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['remoteuser_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['redirectRemoteUser']) ?>: <input type="text" name="redirectRemoteUser" size="20" value="<?php echo $this->escape($this->kga['redirectRemoteUser']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['redirectRemoteUser_desc']?>
            </div>
            </div>
            <div id="ldap_auth" style="display:none">
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['host']) ?>: <input type="text" name="host" size="20" value="<?php echo $this->escape($this->kga['host']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['host_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['forceLowercase']) ?>: <input type="text" name="forceLowercase" size="20" value="<?php echo $this->escape($this->kga['forceLowercase']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['forceLowercase_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['usernameprefix']) ?>: <input type="text" name="usernameprefix" size="20" value="<?php echo $this->escape($this->kga['usernameprefix']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['usernameprefix_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['usernamepostfix']) ?>: <input type="text" name="usernamepostfix" size="20" value="<?php echo $this->escape($this->kga['usernamepostfix']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['usernamepostfix_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['nonLdapAccounts']) ?>: <input type="text" name="nonLdapAccounts" size="20" value="<?php echo $this->escape($this->kga['nonLdapAccounts']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['nonLdapAccounts_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['autocreateUsers']) ?>: <input type="text" name="autocreateUsers" size="20" value="<?php echo $this->escape($this->kga['autocreateUsers']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['autocreateUsers_desc']?>
            </div>
            </div>
            <div id="ldapadvanced_auth" style="display:none">
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['host']) ?>: <input type="text" name="host" size="20" value="<?php echo $this->escape($this->kga['host']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['host_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['bindDN']) ?>: <input type="text" name="bindDN" size="20" value="<?php echo $this->escape($this->kga['bindDN']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['bindDN_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['bindPW']) ?>: <input type="text" name="bindPW" size="20" value="<?php echo $this->escape($this->kga['bindPW']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['bindPW_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['searchBase']) ?>: <input type="text" name="searchBase" size="20" value="<?php echo $this->escape($this->kga['searchBase']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['searchBase_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['userFilter']) ?>: <input type="text" name="userFilter" size="20" value="<?php echo $this->escape($this->kga['userFilter']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['userFilter_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['groupFilter']) ?>: <input type="text" name="groupFilter" size="20" value="<?php echo $this->escape($this->kga['groupFilter']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['groupFilter_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['usernameAttribute']) ?>: <input type="text" name="usernameAttribute" size="20" value="<?php echo $this->escape($this->kga['usernameAttribute']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['usernameAttribute_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['commonNameAttribute']) ?>: <input type="text" name="commonNameAttribute" size="20" value="<?php echo $this->escape($this->kga['commonNameAttribute']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['commonNameAttribute_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['groupidAttribute']) ?>: <input type="text" name="groupidAttribute" size="20" value="<?php echo $this->escape($this->kga['groupidAttribute']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['groupidAttribute_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['mailAttribute']) ?>: <input type="text" name="mailAttribute" size="20" value="<?php echo $this->escape($this->kga['mailAttribute']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['mailAttribute_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['allowedGroupIds']) ?>: <input type="text" name="allowedGroupIds" size="20" value="<?php echo $this->escape($this->kga['allowedGroupIds']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['allowedGroupIds_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['forceLowercase']) ?>: <input type="text" name="forceLowercase" size="20" value="<?php echo $this->escape($this->kga['forceLowercase']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['forceLowercase_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['nonLdapAccounts']) ?>: <input type="text" name="nonLdapAccounts" size="20" value="<?php echo $this->escape($this->kga['nonLdapAccounts']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['nonLdapAccounts_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['autocreateUsers']) ?>: <input type="text" name="autocreateUsers" size="20" value="<?php echo $this->escape($this->kga['autocreateUsers']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['autocreateUsers_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['defaultGlobalRoleName']) ?>: <input type="text" name="defaultGlobalRoleName" size="20" value="<?php echo $this->escape($this->kga['defaultGlobalRoleName']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['defaultGlobalRoleName_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['createGroupMembershipsOnLogin']) ?>: <input type="text" name="createGroupMembershipsOnLogin" size="20" value="<?php echo $this->escape($this->kga['createGroupMembershipsOnLogin']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['createGroupMembershipsOnLogin_desc']?>
            </div>
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['defaultGroupMemberships']) ?>: <input type="text" name="defaultGroupMemberships" size="20" value="<?php echo $this->escape($this->kga['defaultGroupMemberships']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['defaultGroupMemberships_desc']?>
            </div>
            </div>
            <div id="activeDirectory_auth" style="display:none">
            <div>
                <?php echo $this->escape($this->kga['lang']['authsettings']['enhancedIdentityPrivacy']) ?>: <input type="text" name="enhancedIdentityPrivacy" size="20" value="<?php echo $this->escape($this->kga['enhancedIdentityPrivacy']) ?>" class="formfield"> <?php echo $this->kga['lang']['authsettings']['enhancedIdentityPrivacy']?>
            </div>
            </div>
            <div id="formbuttons">
                <input id="adminPanel_extension_form_editadv_submit" class="btn_ok" type="submit" value="<?php echo $this->kga['lang']['save']?>" />
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

        $('#adminPanel_extension_form_editadv').ajaxForm({
            target: '#adminPanel_extension_output',
            success: function (result) {
                if (result.errors.length == 0) {
                    window.location.reload();
                    return;
                }
                $('#adminPanel_extension_form_editadv_submit').blur();
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
