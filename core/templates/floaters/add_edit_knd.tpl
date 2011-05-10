{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            $('#add_edit_knd').ajaxForm(function() { 

                if ($('#knd_grps').val() == null) {
                  alert("{/literal}{$kga.lang.atLeastOneGroup}{literal}");
                  return;
                }

                floaterClose();
                hook_chgKnd();
            });
             $('#floater_innerwrap').tabs({ selected: 0 });
        }); 
    </script>
{/literal}

<div id="floater_innerwrap">
    
    <div id="floater_handle">
        <span id="floater_title">{if $id}{$kga.lang.edit}: {$kga.lang.knd}{else}{$kga.lang.new_knd}{/if}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
        </div>       
    </div>
    
    <div class="menuBackground">

      <ul class="menu tabSelection">
          <li class="tab norm"><a href="#general">
                      <span class="aa">&nbsp;</span>
                      <span class="bb">{$kga.lang.general}</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
          <li class="tab norm"><a href="#address">
                      <span class="aa">&nbsp;</span>
                      <span class="bb">{$kga.lang.address}</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
          <li class="tab norm"><a href="#contact">
                      <span class="aa">&nbsp;</span>
                      <span class="bb">{$kga.lang.contact}</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
{if $sel_grp_IDs|@count gt 1}
          <li class="tab norm"><a href="#groups">
                      <span class="aa">&nbsp;</span>
                      <span class="bb">{$kga.lang.groups}</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
{/if}
          <li class="tab norm"><a href="#comment">
                      <span class="aa">&nbsp;</span>
                      <span class="bb">{$kga.lang.comment}</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
      </ul>
    </div>
    
    <form id="add_edit_knd" action="processor.php" method="post"> 
                
    <input name="knd_filter"   type="hidden" value="0" />

    <input name="axAction"     type="hidden" value="add_edit_KndPctEvt" />   
    <input name="axValue"      type="hidden" value="knd" />   
    <input name="id"           type="hidden" value="{$id}" />   

    <div id="floater_tabs" class="floater_content">

            <fieldset id="general">

                <ul>
                
                    <li>
                        <label for="knd_name" >{$kga.lang.knd}:</label>
                        <input type="text" name="knd_name" id="focus" value="{$knd_name|escape:'html'}" />
                    </li>

                    <li>
                        <label for="knd_vat" >{$kga.lang.vat}:</label>
                        <input type="text" name="knd_vat"  value="{$knd_vat|escape:'html'}" />
                    </li> 

                    <li>
                         <label for="knd_visible">{$kga.lang.visibility}:</label>
                         <input name="knd_visible" type="checkbox" value='1' {if $knd_visible || !$id }checked="checked"{/if} />
                    </li>   

                    <li>
                         <label for="knd_password">{$kga.lang.password}:</label>
                         <input type="password" name='knd_password' cols='30' rows='3' value=""/>
                   
        {if !$knd_password}
        
                        <br/>
                        <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/caution_mini.png" alt="Caution" valign=middle />
                        <strong style="color:red">{$kga.lang.nopassword}</strong>
        {/if}
                    </li> 

                </ul>
                
            </fieldset>

            <fieldset id="comment">

                <ul>

                    <li>
                         <label for="knd_comment">{$kga.lang.comment}:</label>
                         <textarea class='comment' name='knd_comment' cols='30' rows='3' >{$knd_comment|escape:'html'}</textarea>
                    </li>   

                </ul>
                
            </fieldset>

{if $sel_grp_IDs|@count gt 1}
            <fieldset id="groups">

                <ul>
                    
                    <li>
                        <label for="knd_grp" >{$kga.lang.groups}:</label>
                        <select class="formfield" id ="knd_grps" name="knd_grp[]" multiple size='3' style="width:255px">
                            {html_options values=$sel_grp_IDs output=$sel_grp_names selected=$grp_selection}
                        </select>
                    </li>

                </ul>
                
            </fieldset>
{else}
                    <input id="knd_grps" name="knd_grp[]" type="hidden" value="{$grp_selection.0|escape:'html'}" />
{/if}  

            <fieldset id="address">

                <ul>

                    <li>
                        <label for="knd_company" >{$kga.lang.company}:</label>
                        <input type="text" name="knd_company"  value="{$knd_company|escape:'html'}" />
                    </li>

                    <li>
                        <label for="knd_contact" >{$kga.lang.contactPerson}:</label>
                        <input type="text" name="knd_contact"  value="{$knd_contact|escape:'html'}" />
                    </li>
                                      
                    <li>
                        <label for="knd_street" >{$kga.lang.street}:</label>
                        <input type="text" name="knd_street"  value="{$knd_street|escape:'html'}" />
                    </li>
                          
                    <li>
                        <label for="knd_zipcode" >{$kga.lang.zipcode}:</label>
                        <input type="text" name="knd_zipcode"  value="{$knd_zipcode|escape:'html'}" />
                    </li>
                          
                    <li>
                        <label for="knd_city" >{$kga.lang.city}:</label>
                        <input type="text" name="knd_city"  value="{$knd_city|escape:'html'}" />
                    </li>     

                </ul>
                
            </fieldset>

            <fieldset id="contact">

                <ul>   
                          
                    <li>
                        <label for="knd_tel" >{$kga.lang.telephon}:</label>
                        <input type="text" name="knd_tel"  value="{$knd_tel|escape:'html'}" />
                    </li>        
                          
                    <li>
                        <label for="knd_fax" >{$kga.lang.fax}:</label>
                        <input type="text" name="knd_fax"  value="{$knd_fax|escape:'html'}" />
                    </li>        
                          
                    <li>
                        <label for="knd_mobile" >{$kga.lang.mobilephone}:</label>
                        <input type="text" name="knd_mobile"  value="{$knd_mobile|escape:'html'}" />
                    </li>        
                          
                    <li>
                        <label for="knd_mail" >{$kga.lang.mail}:</label>
                        <input type="text" name="knd_mail"  value="{$knd_mail|escape:'html'}" />
                    </li>        
                          
                    <li>
                        <label for="knd_homepage" >{$kga.lang.homepage}:</label>
                        <input type="text" name="knd_homepage"  value="{$knd_homepage|escape:'html'}" />
                    </li>        

                </ul>
                
            </fieldset>
        
    </div>
                                             
                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}' />
                </div>
        </form>
        
</div>
