<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->kga['lang']['editGroup'] ?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->kga['lang']['close'] ?></a>
        </div>
    </div>
    <div class="floater_content">
        <form id="adminPanel_extension_form_editGroup" action="../extensions/ki_adminpanel/processor.php" method="post">
            <input type="hidden" name="id" value="<?php echo $this->group_details['groupID'] ?>"/>
            <input type="hidden" name="axAction" value="sendEditGroup"/>
            <fieldset>
                <ul>
                    <li>
                        <label for="name"><?php echo $this->kga['lang']['groupname'] ?>:</label>
                        <input class="formfield" type="text" name="name" id="name" value="<?php echo $this->escape($this->group_details['name']) ?>" size=35/>
                    </li>
                </ul>
                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='<?php echo $this->kga['lang']['cancel'] ?>' onclick='floaterClose();return false;'/>
                    <input class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['submit'] ?>'/>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var $adminPanel_extension_form_editGroup = $('#adminPanel_extension_form_editGroup');
        $adminPanel_extension_form_editGroup.ajaxForm({
            'beforeSubmit': function () {
                clearFloaterErrorMessages();
                if ($adminPanel_extension_form_editGroup.attr('submitting')) {
                    return false;
                }
                else {
                    $adminPanel_extension_form_editGroup.attr('submitting', true);
                    return true;
                }
            },
            'success': function (result) {
                $adminPanel_extension_form_editGroup.removeAttr('submitting');
                for (var fieldName in result.errors) {
                    setFloaterErrorMessage(fieldName, result.errors[fieldName]);
                }
                if (result.errors.length == 0) {
                    floaterClose();
                    adminPanel_extension_refreshSubtab('groups');
                    adminPanel_extension_refreshSubtab('users');
                }
            },
            'error': function () {
                $adminPanel_extension_form_editGroup.removeAttr('submitting');
            }
        });
    });
</script>