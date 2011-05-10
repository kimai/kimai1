{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            $('#ap_ext_form_editgrp').ajaxForm(function() { 
                floaterClose();
                ap_ext_refreshSubtab('grp');
                ap_ext_refreshSubtab('usr');
            }); 
        }); 
    </script>
{/literal}

<div id="floater_innerwrap">
    
    <div id="floater_handle">
        <span id="floater_title">{$kga.lang.editgrp}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
        </div>       
    </div>

    <div class="floater_content">
        <form id="ap_ext_form_editgrp" action="../extensions/ki_adminpanel/processor.php" method="post"> 
            <fieldset>
                <ul>
                    <li>
                        <label for="grp_name">{$kga.lang.groupname}:</label>
                        <input class="formfield" type="text" name="grp_name" value="{$grp_details.grp_name|escape:'html'}" size=35 />
                    </li>

                    <li>
                        <label for="grp_leader" >{$kga.lang.groupleader}:</label>
                        <select class="formfield" name="grp_leader[]" multiple size='5' style="width:255px">
                            {html_options values=$arr_usr_ID output=$arr_usr_name selected=$grp_selection}
                        </select>
                    </li>
                                                
                </ul>
                <input name="id" type="hidden" value="{$grp_details.grp_ID}" />
                <input name="axAction" type="hidden" value="sendEditGrp" />
                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}' />
                </div>
            </fieldset>
        </form>
    </div>
</div>
