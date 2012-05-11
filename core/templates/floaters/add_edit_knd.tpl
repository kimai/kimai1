{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
    
            $('.disableInput').click(function(){
              var input = $(this);
              if (input.is (':checked'))
                input.siblings().attr("disabled","disabled");
              else
                input.siblings().attr("disabled","");
            });

            $('#add_edit_knd').ajaxForm(function() { 

                if ($('#customerGroups').val() == null) {
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
{if $groupIDs|@count gt 1}
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
                        <label for="name" >{$kga.lang.knd}:</label>
                        <input type="text" name="name" id="focus" value="{$name|escape:'html'}" />
                    </li>

                    <li>
                        <label for="vat" >{$kga.lang.vat}:</label>
                        <input type="text" name="vat"  value="{$vat|escape:'html'}" />
                    </li> 

                    <li>
                         <label for="visible">{$kga.lang.visibility}:</label>
                         <input name="visible" type="checkbox" value='1' {if $visible || !$id }checked="checked"{/if} />
                    </li>   

                    <li>
                      <label for="password">{$kga.lang.password}:</label>
                      <div class="multiFields">
                        <input type="password" name='password' cols='30' rows='3' value="" {if !$password}disabled="disabled"{/if}/><br/>
                        <input type="checkbox" name="no_password" value="1" class="disableInput" {if !$password}checked="checked"{/if}>{$kga.lang.nopassword}
                      </div>
                    </li> 

                    <li>
                      <label for="timezone">{$kga.lang.timezone}:</label>
                      <select name="timezone">
                        {html_options values=$timezones output=$timezones selected=$timezone}
                      </select>
                    </li>

                </ul>
                
            </fieldset>

            <fieldset id="comment">

                <ul>

                    <li>
                         <label for="comment">{$kga.lang.comment}:</label>
                         <textarea class='comment' name='comment' cols='30' rows='3' >{$comment|escape:'html'}</textarea>
                    </li>   

                </ul>
                
            </fieldset>

{if $groupIDs|@count gt 1}
            <fieldset id="groups">

                <ul>
                    
                    <li>
                        <label for="customerGroups" >{$kga.lang.groups}:</label>
                        <select class="formfield" id ="customerGroups" name="customerGroups[]" multiple size='3' style="width:255px">
                            {html_options values=$groupIDs output=$groupNames selected=$selectedGroups}
                        </select>
                    </li>

                </ul>
                
            </fieldset>
{else}
                    <input id="customerGroups" name="groups[]" type="hidden" value="{$selectedGroups.0|escape:'html'}" />
{/if}  

            <fieldset id="address">

                <ul>

                    <li>
                        <label for="company" >{$kga.lang.company}:</label>
                        <input type="text" name="company"  value="{$company|escape:'html'}" />
                    </li>

                    <li>
                        <label for="contact" >{$kga.lang.contactPerson}:</label>
                        <input type="text" name="contact"  value="{$contact|escape:'html'}" />
                    </li>
                                      
                    <li>
                        <label for="street" >{$kga.lang.street}:</label>
                        <input type="text" name="street"  value="{$street|escape:'html'}" />
                    </li>
                          
                    <li>
                        <label for="zipcode" >{$kga.lang.zipcode}:</label>
                        <input type="text" name="zipcode"  value="{$zipcode|escape:'html'}" />
                    </li>
                          
                    <li>
                        <label for="city" >{$kga.lang.city}:</label>
                        <input type="text" name="city"  value="{$city|escape:'html'}" />
                    </li>     

                </ul>
                
            </fieldset>

            <fieldset id="contact">

                <ul>   
                          
                    <li>
                        <label for="phone" >{$kga.lang.telephon}:</label>
                        <input type="text" name="phone"  value="{$phone|escape:'html'}" />
                    </li>        
                          
                    <li>
                        <label for="fax" >{$kga.lang.fax}:</label>
                        <input type="text" name="fax"  value="{$fax|escape:'html'}" />
                    </li>        
                          
                    <li>
                        <label for="mobile" >{$kga.lang.mobilephone}:</label>
                        <input type="text" name="mobile"  value="{$mobile|escape:'html'}" />
                    </li>        
                          
                    <li>
                        <label for="mail" >{$kga.lang.mail}:</label>
                        <input type="text" name="mail"  value="{$mail|escape:'html'}" />
                    </li>        
                          
                    <li>
                        <label for="homepage" >{$kga.lang.homepage}:</label>
                        <input type="text" name="homepage"  value="{$homepage|escape:'html'}" />
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
