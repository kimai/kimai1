<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->translate('editstatus') ?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->translate('close') ?></a>
        </div>
    </div>
    <div class="floater_content">
        <form id="adminPanel_extension_form_editstatus" action="../extensions/ki_adminpanel/processor.php" method="post">
            <input type="hidden" name="id" value="<?php echo $this->status_details['statusID'] ?>"/>
            <input type="hidden" name="axAction" value="sendEditStatus"/>
            <fieldset>
                <ul>
                    <li>
                        <label for="status"><?php echo $this->translate('status') ?>:</label>
                        <input class="formfield" type="text" name="status" id="status" value="<?php echo $this->escape($this->status_details['status']) ?>" size=35/>
                    </li>
                    <li>
                        <label for="default"><?php echo $this->translate('default') ?>:</label>
                        <input class="formfield" type="checkbox" name="default" id="status" value="1" <?php if ($this->status_details['statusID'] == $this->kga->getDefaultStatus()) echo 'checked="checked"' ?>/>
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
        var $adminPanel_extension_form_editstatus = $('#adminPanel_extension_form_editstatus');
        $adminPanel_extension_form_editstatus.ajaxForm({
            'beforeSubmit': function () {
                clearFloaterErrorMessages();
                if ($adminPanel_extension_form_editstatus.attr('submitting')) {
                    return false;
                }
                else {
                    $adminPanel_extension_form_editstatus.attr('submitting', true);
                    return true;
                }
            },
            'success': function (result) {
                $adminPanel_extension_form_editstatus.removeAttr('submitting');

                for (var fieldName in result.errors) {
                    setFloaterErrorMessage(fieldName, result.errors[fieldName]);
                }
                if (result.errors.length == 0) {
                    floaterClose();
                    adminPanel_extension_refreshSubtab('status');
                }
            },
            'error': function () {
                $adminPanel_extension_form_editstatus.removeAttr('submitting');
            }
        });
    });
</script>