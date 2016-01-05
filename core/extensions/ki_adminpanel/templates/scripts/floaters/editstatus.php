<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->kga['lang']['editstatus'] ?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->kga['lang']['close'] ?></a>
        </div>
    </div>
    <div class="floater_content">
        <form id="adminPanel_extension_form_editstatus" action="../extensions/ki_adminpanel/processor.php" method="post">
            <input type="hidden" name="id" value="<?php echo $this->status_details['statusID'] ?>"/>
            <input type="hidden" name="axAction" value="sendEditStatus"/>
            <fieldset>
                <ul>
                    <li>
                        <label for="status"><?php echo $this->kga['lang']['status'] ?>:</label>
                        <input class="formfield" type="text" name="status" id="status" value="<?php echo $this->escape($this->status_details['status']) ?>" size=35/>
                    </li>
                    <li>
                        <label for="default"><?php echo $this->kga['lang']['default'] ?>:</label>
                        <input class="formfield" type="checkbox" name="default" id="status" value="1" <?php if ($this->status_details['statusID'] == $this->kga['conf']['defaultStatusID']) echo 'checked="checked"' ?>/>
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