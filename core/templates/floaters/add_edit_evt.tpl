{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
             $('#add_edit_evt').ajaxForm(function() {
                 floaterClose();
                 hook_chgEvt();
             });
         }); 
    </script>
{/literal}

<div id="floater_innerwrap">
    
    <div id="floater_handle">
        <span id="floater_title">{if $id}{$kga.lang.edit}: {$kga.lang.evt}{else}{$kga.lang.new_evt}{/if}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
            <a href="#" class="options down" onClick="floaterOptions(); $(this).blur();">{$kga.lang.options}</a>
        </div>       
    </div>

    <div id="floater_content"><div id="floater_dimensions">

    {* send to CORE (!!!) processor *}
    
        <form id="add_edit_evt" action="processor.php" method="post"> 
            <fieldset>

                <ul>
                
                    <li>
                        <label for="evt_name" >{$kga.lang.evt}:</label>
                        <input type="text" name="evt_name" id="focus" value="{$evt_name}" />
                    </li>
                
                    <li>
                        <label for="evt_default_rate" >{$kga.lang.default_rate}:</label>
                        <input type="text" name="evt_default_rate" value="{$evt_default_rate}" />
                    </li>
                
                    <li>
                        <label for="evt_my_rate" >{$kga.lang.my_rate}:</label>
                        <input type="text" name="evt_my_rate" id="focus" value="{$evt_my_rate}" />
                    </li>

                    <li class="extended">
                         <label for="evt_visible">{$kga.lang.visibility}:</label>
                         <input name="evt_visible" type="checkbox" value='1' {if $evt_visible || !$id }checked="checked"{/if} />
                    </li>

                    <li class="extended">
                         <label for="evt_comment">{$kga.lang.comment}:</label>
                         <textarea class='comment' name='evt_comment' cols='30' rows='5' >{$evt_comment}</textarea>
                    </li>

{if $sel_grp_IDs|@count gt 1}                    
                    <li class="extended">
                        <label for="evt_grp" >{$kga.lang.groups}:</label>
                        <select class="formfield" name="evt_grp[]" multiple size='5' style="width:255px">
                            {html_options values=$sel_grp_IDs output=$sel_grp_names selected=$grp_selection}
                        </select>
                    </li>
{else}
                    <input name="evt_grp[]" type="hidden" value="{$grp_selection.0}" />
{/if}


                    
                </ul>
                
                <input name="evt_filter"   type="hidden" value="0" />
                <input name="evt_logo"     type="hidden" value="" />
 
                <input name="axAction" type="hidden" value="add_edit_KndPctEvt" />   
                <input name="axValue" type="hidden" value="evt" />   
                <input name="id" type="hidden" value="{$id}" />   
                                             
                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}'/>
                </div>
                
            </fieldset>
        </form>
        
    </div></div>
</div>
