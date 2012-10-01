    <script type="text/javascript"> 
        $(document).ready(function() {
            $('#adminPanel_extension_form_editstatus').ajaxForm( { 'beforeSubmit' :function() { 
                floaterClose();
                return true;
            },
            'success': function () {
                adminPanel_extension_refreshSubtab('status');
            }}); 
        }); 
    </script>

<div id="floater_innerwrap">
    
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->kga['lang']['editstatus']?></span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();"><?php echo $this->kga['lang']['close']?></a>
        </div>       
    </div>

    <div class="floater_content">
        <form id="adminPanel_extension_form_editstatus" action="../extensions/ki_adminpanel/processor.php" method="post"> 
            <fieldset>
                <ul>
                    <li>
                        <label for="groupName"><?php echo $this->kga['lang']['status']?>:</label>
                        <input class="formfield" type="text" name="status" value="<?php echo $this->escape($this->status_details['status'])?>" size=35 />
                    </li>
                                                
                </ul>
                <input name="id" type="hidden" value="<?php echo $this->status_details['statusID']?>" />
                <input name="axAction" type="hidden" value="sendEditStatus" />
                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='<?php echo $this->kga['lang']['cancel']?>' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['submit']?>' />
                </div>
            </fieldset>
        </form>
    </div>
</div>
