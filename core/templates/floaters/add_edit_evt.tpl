{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
             $('#add_edit_evt').ajaxForm(function() {

                if ($('#evt_grps').val() == null) {
                  alert("{/literal}{$kga.lang.atLeastOneGroup}{literal}");
                  return;
                }

                 floaterClose();
                 hook_chgEvt();
             });
             $('#floater_innerwrap').tabs({ selected: 0 });
         }); 
    </script>
{/literal}

<div id="floater_innerwrap">
    
    <div id="floater_handle">
        <span id="floater_title">{if $id}{$kga.lang.edit}: {$kga.lang.evt}{else}{$kga.lang.new_evt}{/if}</span>
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
          <li class="tab norm"><a href="#projects">
                      <span class="aa">&nbsp;</span>
                      <span class="bb">{$kga.lang.pcts}</span>
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
    
        <form id="add_edit_evt" action="processor.php" method="post"> 
                
                <input name="evt_filter"   type="hidden" value="0" />
 
                <input name="axAction" type="hidden" value="add_edit_KndPctEvt" />   
                <input name="axValue" type="hidden" value="evt" />   
                <input name="id" type="hidden" value="{$id}" />   


    <div id="floater_tabs" class="floater_content">


            <fieldset id="general">

                <ul>
                
                    <li>
                        <label for="evt_name" >{$kga.lang.evt}:</label>
                        <input type="text" name="evt_name" id="focus" value="{$evt_name|escape:'html'}" />
                    </li>
                
                    <li>
                        <label for="evt_default_rate" >{$kga.lang.default_rate}:</label>
                        <input type="text" name="evt_default_rate" value="{$evt_default_rate|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
                    </li>
                
                    <li>
                        <label for="evt_my_rate" >{$kga.lang.my_rate}:</label>
                        <input type="text" name="evt_my_rate" id="focus" value="{$evt_my_rate|replace:'.':$kga.conf.decimalSeparator|escape:'html'}" />
                    </li>

                    <li>
                         <label for="evt_visible">{$kga.lang.visibility}:</label>
                         <input name="evt_visible" type="checkbox" value='1' {if $evt_visible || !$id }checked="checked"{/if} />
                    </li>
                 </ul>

            </fieldset>

            <fieldset id="comment">
                <ul>
                    <li>
                         <label for="evt_comment">{$kga.lang.comment}:</label>
                         <textarea class='comment' name='evt_comment' cols='30' rows='5' >{$evt_comment|escape:'html'}</textarea>
                    </li>
                </ul>
            </fieldset>

{if $sel_grp_IDs|@count gt 1}   
    <fieldset id="groups">
                <ul>
                 
                    <li>
                        <label for="evt_grp" >{$kga.lang.groups}:</label>
                        <select class="formfield" id="evt_grps" name="evt_grp[]" multiple size='5' style="width:255px">
                            {html_options values=$sel_grp_IDs output=$sel_grp_names selected=$grp_selection}
                        </select>
                    </li>      
                </ul>
            </fieldset>
{else}
                    <input id="evt_grps" name="evt_grp[]" type="hidden" value="{$grp_selection.0|escape:'html'}" />
{/if}        

    <fieldset id="projects">
                <ul>

                    <li>
                        <label for="evt_pct" >{$kga.lang.pcts}:</label>
                        <select class="formfield" name="evt_pct[]" multiple size='5' style="width:255px">
                            {html_options values=$sel_pct_IDs output=$sel_pct_names selected=$pct_selection}
                        </select>
                    </li>


                    
                </ul>
      </fieldset>
        
    </div>
                                             
                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}'/>
                </div>
        </form>
</div>
