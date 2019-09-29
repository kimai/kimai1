<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->translate('editGroup') ?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->translate('close') ?></a>
        </div>
    </div>
    <div class="floater_content">
        <form id="adminPanel_extension_form_editGroup" action="../extensions/ki_adminpanel/processor.php" method="post">
            <input type="hidden" name="id" value="<?php echo $this->group_details['groupID'] ?>"/>
            <input type="hidden" name="axAction" value="sendEditGroup"/>
            <fieldset>
                <ul>
                    <li>
                        <label for="name"><?php echo $this->translate('groupname') ?>:</label>
                        <input class="formfield" type="text" name="name" id="name" value="<?php echo $this->escape($this->group_details['name']) ?>" size=35/>
                    </li>
                </ul>
                <div id="formbuttons">
	                <button type="button" class="btn_norm" onclick="floaterClose();"><?php echo $this->translate('cancel') ?></button>
	                <input type="submit" class="btn_ok" value="<?php echo $this->translate('submit') ?>"/>
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