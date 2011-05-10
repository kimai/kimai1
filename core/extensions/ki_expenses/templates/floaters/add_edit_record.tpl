{literal}    
    <script type="text/javascript"> 
        
        $(document).ready(function() {
            $('#help').hide();
            $('#exp_ext_form_add_edit_record').ajaxForm(function() { 
                    floaterClose();
                    exp_ext_reload();
             
            });
            {/literal}{if $id}{literal}
            {/literal}{else}{literal}
            $("#add_edit_exp_pct_ID").selectOptions(""+selected_pct+"");
            {/literal}{/if}{literal}
            $('#floater_innerwrap').tabs({ selected: 0 });
        }); 
        
    </script>
{/literal}


<div id="floater_innerwrap">

    <div id="floater_handle">
        <span id="floater_title">{if $id}{$kga.lang.edit}{else}{$kga.lang.add}{/if} {$pres_evt|escape:'html'}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
            <a href="#" class="help" onClick="$(this).blur(); $('#help').slideToggle();">{$kga.lang.help}</a>
        </div>  
    </div>

    <div id="help">
        <div class="content">        
            {$kga.lang.dateAndTimeHelp}
        </div>
    </div>
    
    <div class="menuBackground">

      <ul class="menu tabSelection">
          <li class="tab norm"><a href="#general">
                      <span class="aa">&nbsp;</span>
                      <span class="bb">{$kga.lang.general}</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
          <li class="tab norm"><a href="#extended">
                      <span class="aa">&nbsp;</span>
                      <span class="bb">{$kga.lang.advanced}</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
      </ul>
    </div>

    <form id="exp_ext_form_add_edit_record" action="../extensions/ki_expenses/processor.php" method="post"> 
                <input name="id" type="hidden" value="{$id}" />
                <input name="axAction" type="hidden" value="add_edit_record" />


    <div id="floater_tabs" class="floater_content">

            <fieldset id="general">
                
                <ul>
                
                   <li>
                       <label for="pct_ID">{$kga.lang.pct}:</label>
                       <select size = "5" name="pct_ID" id="add_edit_exp_pct_ID" class="formfield" style="width:400px" tabindex="1" onchange="exp_add_edit_validate();">
                           {html_options values=$sel_pct_IDs output=$sel_pct_names selected=$pres_pct}
                       </select>
                       <br/>
                       <input type="input" style="margin-left:115px;width:395px;margin-top:3px" tabindex="2" size="10" name="filter" id="filter" onkeyup="filter_selects('add_edit_exp_pct_ID', this.value); exp_add_edit_validate();"/>
                   </li>
                
{* -------------------------------------------------------------------- *} 

                <li>
                     <label for="edit_day">{$kga.lang.day}:</label>
                     <input id='edit_day' type='text' name='edit_day' value='{$edit_day|escape:'html'}' maxlength='10' size='10' tabindex='5' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                </li>


              
                   <li>
                       <label for="edit_time">{$kga.lang.timelabel}:</label>
                        <input id='edit_time' type='text' name='edit_time' value='{$edit_time|escape:'html'}' maxlength='8'  size='8'  tabindex='7' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                        <a href="#" onClick="exp_pasteNow(); $(this).blur(); return false;">{$kga.lang.now}</a>
                   </li>

                   
{* -------------------------------------------------------------------- *}


                   <li>
                       <label for="multiplier">{$kga.lang.multiplier}:</label>
                        <input id='multiplier' type='text' name='multiplier' value='{$multiplier|escape:'html'}' maxlength='8'  size='8'  tabindex='9' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                   </li>

                   
{* -------------------------------------------------------------------- *}


                   <li>
                       <label for="edit_value">{$kga.lang.expense}:</label>
                        <input id='edit_value' type='text' name='edit_value' value='{$edit_value|escape:'html'}' maxlength='8'  size='8'  tabindex='10' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                   </li>

                   
{* -------------------------------------------------------------------- *}


                   <li>
                       <label for="designation">{$kga.lang.designation}:</label>
                        <input id='designation' type='text' name='designation' value='{$designation|escape:'html'}' maxlength='20'  size='20'  tabindex='11' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                   </li>

                   
{* -------------------------------------------------------------------- *}


                   
          </ul>
          </fieldset>
{* -------------------------------------------------------------------- *}    
          <fieldset id="extended">
            <ul>
          <li>
                        <label for="erase">{$kga.lang.refundable_long}:</label>
                        <input type='checkbox' id='refundable' name='refundable' {if $refundable} checked="checked" {/if} tabindex='12'/>
                   </li>

                   <li>
                        <label for="comment">{$kga.lang.comment}:</label>
                        <textarea id='comment' style="width:395px" class='comment' name='comment' cols='40' rows='5' tabindex='13'>{$comment|escape:'html'}</textarea>
                   </li>
                   
                   <li>
                       <label for="comment_type">{$kga.lang.comment_type}:</label>
                       <select id="comment_type" class="formfield" name="comment_type" tabindex="14" >
                           {html_options values=$comment_values output=$comment_types selected=$comment_active}
                       </select>
                   </li>
                   
                    <li>
                        <label for="erase">{$kga.lang.erase}:</label>
                        <input type='checkbox' id='erase' name='erase' tabindex='15'/>
                   </li>
        
                </ul>
            </fieldset>

{* -------------------------------------------------------------------- *} 

    </div>


                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}' />
                </div>

{* -------------------------------------------------------------------- *} 

        </form>
</div>