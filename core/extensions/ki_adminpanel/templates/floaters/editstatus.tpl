{literal}    
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
{/literal}

<div id="floater_innerwrap">
    
    <div id="floater_handle">
        <span id="floater_title">{$kga.lang.editstatus}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
        </div>       
    </div>

    <div class="floater_content">
        <form id="adminPanel_extension_form_editstatus" action="../extensions/ki_adminpanel/processor.php" method="post"> 
            <fieldset>
                <ul>
                    <li>
                        <label for="groupName">{$kga.lang.status}:</label>
                        <input class="formfield" type="text" name="status" value="{$status_details.status|escape:'html'}" size=35 />
                    </li>
                                                
                </ul>
                <input name="id" type="hidden" value="{$status_details.status_id}" />
                <input name="axAction" type="hidden" value="sendEditStatus" />
                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}' />
                </div>
            </fieldset>
        </form>
    </div>
</div>
