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
        }); 
        
    </script>
{/literal}


<div id="floater_innerwrap">

    <div id="floater_handle">
        <span id="floater_title">{if $id}{$kga.lang.edit}{else}{$kga.lang.add}{/if} {$pres_evt}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
            <a href="#" class="help" onClick="$(this).blur(); $('#help').slideToggle();">{$kga.lang.help}</a>
        </div>  
    </div>

    <div id="help">
        <div class="content">        
            <strong>Times and dates can be entered in short notations:</strong><br />
            Dates: 5 &rarr; 05.{$smarty.now|date_format:"%m"}.{$smarty.now|date_format:"%Y"} &nbsp;&nbsp; 
            1004 &rarr; 10.04.{$smarty.now|date_format:"%Y"} &nbsp;&nbsp; 
            100406 &rarr; 10.04.2006<br />
            Times: 7 &rarr; 07:00:00  &nbsp;&nbsp;
            14 &rarr; 14:00:00  &nbsp;&nbsp;
            0910 &rarr; 09:10:00  &nbsp;&nbsp;
            091020 &rarr; 09:10:20 &nbsp;&nbsp;
        </div>
    </div>


    <div id="floater_content"><div id="floater_dimensions">
        
{*        This function is *currently* under development! *}





        <form id="exp_ext_form_add_edit_record" action="../extensions/ki_expenses/processor.php" method="post"> 
            <fieldset>
                
                <ul>
                
                   <li>
                       <label for="pct_ID">{$kga.lang.pct}:</label>
                       <select size = "5" name="pct_ID" id="add_edit_exp_pct_ID" class="formfield" style="width:400px" tabindex="1" >
                           {html_options values=$sel_pct_IDs output=$sel_pct_names selected=$pres_pct}
                       </select>
                       <br/>
                       <input type="input" style="margin-left:115px;width:395px;margin-top:3px" tabindex="2" size="10" maxlength="10" name="filter" id="filter" onkeyup="filter_selects('add_edit_exp_pct_ID', this.value);"/>
                   </li>
                
{* -------------------------------------------------------------------- *} 

                <li>
                     <label for="edit_day">{$kga.lang.day}:</label>
                     <input id='edit_day' type='text' name='edit_day' value='{$edit_day}' maxlength='10' size='10' tabindex='5' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                </li>


              
                   <li>
                       <label for="edit_time">{$kga.lang.timelabel}:</label>
                        <input id='edit_time' type='text' name='edit_time' value='{$edit_time}' maxlength='8'  size='8'  tabindex='7' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                        <a href="#" onClick="exp_pasteNow(); $(this).blur(); return false;">{$kga.lang.now}</a>
                   </li>

                   
{* -------------------------------------------------------------------- *}


                   <li>
                       <label for="edit_value">{$kga.lang.expense}:</label>
                        <input id='edit_value' type='text' name='edit_value' value='{$edit_value}' maxlength='8'  size='8'  tabindex='9' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                   </li>

                   
{* -------------------------------------------------------------------- *}


                   <li>
                       <label for="designation">{$kga.lang.designation}:</label>
                        <input id='designation' type='text' name='designation' value='{$designation}' maxlength='20'  size='20'  tabindex='10' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                   </li>

                   
{* -------------------------------------------------------------------- *}       
        
                   <li>
                        <label for="comment">{$kga.lang.comment}:</label>
                        <textarea id='comment' style="width:395px" class='comment' name='comment' cols='40' rows='5' tabindex='12'>{$comment}</textarea>
                   </li>
                   
                   <li>
                       <label for="comment_type">{$kga.lang.comment_type}:</label>
                       <select id="comment_type" class="formfield" name="comment_type" tabindex="13" >
                           {html_options values=$comment_values output=$comment_types selected=$comment_active}
                       </select>
                   </li>
                   
                    <li>
                        <label for="erase">{$kga.lang.erase}:</label>
                        <input type='checkbox' id='erase' name='erase' tabindex='14'/>
                   </li>
        
                </ul>

{* -------------------------------------------------------------------- *} 

                <input name="id" type="hidden" value="{$id}" />
                <input name="axAction" type="hidden" value="add_edit_record" />

                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}' />
                </div>

{* -------------------------------------------------------------------- *} 

            </fieldset>
        </form>

    </div></div>
</div>