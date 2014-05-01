    <script type="text/javascript"> 
        $(document).ready(function() {
            $('#adminPanel_extension_form_editstatus').ajaxForm( {
              'beforeSubmit' :function() { 
                clearFloaterErrorMessages();

                if ($('#adminPanel_extension_form_editstatus').attr('submitting')) {
                  return false;
                }
                else {
                  $('#adminPanel_extension_form_editstatus').attr('submitting', true);
                  return true;
                }
              },
              'success': function (result) {
                $('#adminPanel_extension_form_editstatus').removeAttr('submitting');

                for (var fieldName in result.errors)
                  setFloaterErrorMessage(fieldName,result.errors[fieldName]);
                
                if (result.errors.length == 0) {
                  floaterClose();
                  adminPanel_extension_refreshSubtab('status');
                }
            },
            'error': function() {
              $('#adminPanel_extension_form_editstatus').removeAttr('submitting');
            }});
        }); 
    </script>

<?php
echo $this->floater()
    ->setTitle($this->translate('editstatus'))
    ->setFormAction('../extensions/ki_adminpanel/processor.php')
    ->setFormId('adminPanel_extension_form_editstatus')
    ->floaterBegin();
?>

        <ul>
            <li>
                <label for="status"><?php echo $this->kga['lang']['status']?>:</label>
                <input class="formfield" type="text" name="status" value="<?php echo $this->escape($this->status_details['status'])?>" size=35 />
            </li>
            <li>
                <label for="default"><?php echo $this->kga['lang']['default']?>:</label>
                <input class="formfield" type="checkbox" name="default" value="1" <?php if($this->status_details['statusID'] == $this->kga['conf']['defaultStatusID']) echo 'checked="checked"'?>/>
            </li>
        </ul>
        <input name="id" type="hidden" value="<?php echo $this->status_details['statusID']?>" />
        <input name="axAction" type="hidden" value="sendEditStatus" />
    
<?php echo $this->floater()->floaterEnd(); ?>