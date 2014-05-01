<script type="text/javascript">
    $(document).ready(function () {
        $('#adminPanel_extension_form_editGroup').ajaxForm({
            'beforeSubmit': function () {
                clearFloaterErrorMessages();

                if ($('#adminPanel_extension_form_editGroup').attr('submitting')) {
                    return false;
                }
                else {
                    $('#adminPanel_extension_form_editGroup').attr('submitting', true);
                    return true;
                }
            },
            'success': function (result) {
                $('#adminPanel_extension_form_editGroup').removeAttr('submitting');

                for (var fieldName in result.errors)
                    setFloaterErrorMessage(fieldName, result.errors[fieldName]);

                if (result.errors.length == 0) {
                    floaterClose();
                    adminPanel_extension_refreshSubtab('groups');
                    adminPanel_extension_refreshSubtab('users');
                }
            },
            'error': function () {
                $('#adminPanel_extension_form_editGroup').removeAttr('submitting');
            }});
    });
</script>

<?php
echo $this->floater()
    ->setTitle($this->translate('editGroup'))
    ->setFormAction('../extensions/ki_adminpanel/processor.php')
    ->setFormId('adminPanel_extension_form_editGroup')
    ->floaterBegin();
?>

<ul>
    <li>
        <label for="name"><?php echo $this->kga['lang']['groupname'] ?>:</label>
        <input class="formfield" type="text" name="name"
               value="<?php echo $this->escape($this->group_details['name']) ?>" size=35/>
    </li>

</ul>
<input name="id" type="hidden" value="<?php echo $this->group_details['groupID'] ?>"/>
<input name="axAction" type="hidden" value="sendEditGroup"/>

<?php echo $this->floater()->floaterEnd(); ?>