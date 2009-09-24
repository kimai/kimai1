{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            $('#add_edit_knd').ajaxForm(function() { 
                floaterClose();
                hook_chgKnd();
            });
        }); 
    </script>
{/literal}

<div id="floater_innerwrap">
    
    <div id="floater_handle">
        <span id="floater_title">{if $id}{$kga.lang.edit}: {$kga.lang.knd}{else}{$kga.lang.new_knd}{/if}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
            <a href="#" class="options down" onClick="floaterOptions();">{$kga.lang.options}</a>
        </div>       
    </div>

    <div id="floater_content"><div id="floater_dimensions">

    {* send to CORE (!!!) processor *}
    
        <form id="add_edit_knd" action="processor.php" method="post"> 
            <fieldset>

                <ul>
                
                    <li>
                        <label for="knd_name" >{$kga.lang.knd}:</label>
                        <input type="text" name="knd_name" id="focus" value="{$knd_name}" />
                    </li>

                    <li class="extended">
                         <label for="knd_visible">{$kga.lang.visibility}:</label>
                         <input name="knd_visible" type="checkbox" value='1' {if $knd_visible || !$id }checked="checked"{/if} />
                    </li>

                    <li class="extended">
                         <label for="knd_comment">{$kga.lang.comment}:</label>
                         <textarea class='comment' name='knd_comment' cols='30' rows='3' >{$knd_comment}</textarea>
                    </li>

                    <li class="extended">
                         <label for="knd_password">{$kga.lang.password}:</label>
                         <input name='knd_password' cols='30' rows='3' value="{$knd_password}"/>
                    </li>
                    
{if $sel_grp_IDs|@count gt 1}
                    <li class="extended">
                        <label for="knd_grp" >{$kga.lang.groups}:</label>
                        <select class="formfield" name="knd_grp[]" multiple size='3' style="width:255px">
                            {html_options values=$sel_grp_IDs output=$sel_grp_names selected=$grp_selection}
                        </select>
                    </li>
{else}
                    <input name="knd_grp[]" type="hidden" value="{$grp_selection.0}" />
{/if}

                    <li class="extended">
                        <label for="knd_company" >{$kga.lang.company}:</label>
                        <input type="text" name="knd_company"  value="{$knd_company}" />
                    </li>
                                      
                    <li class="extended">
                        <label for="knd_street" >{$kga.lang.street}:</label>
                        <input type="text" name="knd_street"  value="{$knd_street}" />
                    </li>
                          
                    <li class="extended">
                        <label for="knd_zipcode" >{$kga.lang.zipcode}:</label>
                        <input type="text" name="knd_zipcode"  value="{$knd_zipcode}" />
                    </li>
                          
                    <li class="extended">
                        <label for="knd_city" >{$kga.lang.city}:</label>
                        <input type="text" name="knd_city"  value="{$knd_city}" />
                    </li>        
                          
                    <li class="extended">
                        <label for="knd_tel" >{$kga.lang.telephon}:</label>
                        <input type="text" name="knd_tel"  value="{$knd_tel}" />
                    </li>        
                          
                    <li class="extended">
                        <label for="knd_fax" >{$kga.lang.fax}:</label>
                        <input type="text" name="knd_fax"  value="{$knd_fax}" />
                    </li>        
                          
                    <li class="extended">
                        <label for="knd_mobile" >{$kga.lang.mobilephone}:</label>
                        <input type="text" name="knd_mobile"  value="{$knd_mobile}" />
                    </li>        
                          
                    <li class="extended">
                        <label for="knd_mail" >{$kga.lang.mail}:</label>
                        <input type="text" name="knd_mail"  value="{$knd_mail}" />
                    </li>        
                          
                    <li class="extended">
                        <label for="knd_homepage" >{$kga.lang.homepage}:</label>
                        <input type="text" name="knd_homepage"  value="{$knd_homepage}" />
                    </li>        

                </ul>
                
                <input name="knd_filter"   type="hidden" value="0" />
                <input name="knd_logo"     type="hidden" value="" />
 
                <input name="axAction"     type="hidden" value="add_edit_KndPctEvt" />   
                <input name="axValue"      type="hidden" value="knd" />   
                <input name="id"           type="hidden" value="{$id}" />   
                                             
                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}' />
                </div>
                
            </fieldset>
        </form>
        
    </div></div>
</div>
