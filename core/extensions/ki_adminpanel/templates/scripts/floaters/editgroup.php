    <script type="text/javascript"> 
        $(document).ready(function() {
            $('#adminPanel_extension_form_editGroup').ajaxForm( {
              'beforeSubmit' :function() { 
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
                  setFloaterErrorMessage(fieldName,result.errors[fieldName]);
                
                if (result.errors.length == 0) {
                  floaterClose();
                  adminPanel_extension_refreshSubtab('groups');
                  adminPanel_extension_refreshSubtab('users');
                }
            },
            'error': function() {
              $('#adminPanel_extension_form_editGroup').removeAttr('submitting');
            }});
        }); 
    </script>

<div id="floater_innerwrap">
    
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->kga['lang']['editGroup']?></span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();"><?php echo $this->kga['lang']['close']?></a>
        </div>       
    </div>

    <div class="floater_content">
        <form id="adminPanel_extension_form_editGroup" action="../extensions/ki_adminpanel/processor.php" method="post"> 
            <fieldset>
                <ul>
                    <li>
                        <label for="name"><?php echo $this->kga['lang']['groupname']?>:</label>
                        <input class="formfield" type="text" name="name" value="<?php echo $this->escape($this->group_details['name'])?>" size=35 />
                    </li>
                                                
                </ul>
                <input name="id" type="hidden" value="<?php echo $this->group_details['groupID']?>" />
                <input name="axAction" type="hidden" value="sendEditGroup" />
                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='<?php echo $this->kga['lang']['cancel']?>' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['submit']?>' />
                </div>
            </fieldset>
        </form>
    </div>
</div>
