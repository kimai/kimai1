{literal}    
    <script type="text/javascript"> 
        
        $(document).ready(function() {
            $('#help').hide();


        $('#edit_in_day').datepicker({
          onSelect: function(dateText, instance) {
            $('#edit_out_day').datepicker( "option", "minDate", $('#edit_in_day').datepicker("getDate") );
            ts_timeToDuration();
          }
         });
        $('#edit_out_day').datepicker({
          onSelect: function(dateText, instance) {
            $('#edit_in_day').datepicker( "option", "maxDate", $('#edit_out_day').datepicker("getDate") );
            ts_timeToDuration();
          }
         });

            $('#ts_ext_form_add_edit_record').ajaxForm( { 'beforeSubmit' :function() { 


                if (!$('#edit_in_day').val().match(ts_dayFormatExp) ||
                    !$('#edit_out_day').val().match(ts_dayFormatExp) ||
                    !$('#edit_in_time').val().match(ts_timeFormatExp) ||
                    !$('#edit_out_time').val().match(ts_timeFormatExp)) {
                  alert("{/literal}{$kga.lang.TimeDateInputError}{literal}");
                  return false;
                }

                // test if start time is before end time
                var inTimeMatches = $('#edit_in_time').val().match(ts_timeFormatExp);
                var outTimeMatches = $('#edit_out_time').val().match(ts_timeFormatExp);
                for (var i = 1;i<=3;i++) {
                  var inVal = inTimeMatches[i];
                  var outVal = outTimeMatches[i];
                  
                  if (inVal[0] == ":")
                    inVal = inVal.substr(1);
                  if (outVal[0] == ":")
                    outVal = outVal.substr(1);
                  
                  if (inVal == undefined)
                    inVal = 0;
                  if (outVal == undefined)
                    outVal = 0;
                  
                  if (inVal > outVal) {
                    alert("{/literal}{$kga.lang.StartTimeBeforeEndTime}{literal}");
                    return false;
                  }
                  else if (inVal < outVal)
                    break; // if this part is smaller we don't care for the other parts
                }
                
                
                
                var edit_in_time = $('#edit_in_day').val()+$('#edit_in_time').val();
                var edit_out_time = $('#edit_out_day').val()+$('#edit_out_time').val();
                var deleted = $('#erase').is(':checked');
                
                if (!deleted && edit_in_time == edit_out_time) {
                    alert("{/literal}{$kga.lang.timediff_warn}{literal}");
                    return false;
                }

              floaterClose();
              return true;
            },
              'success' : ts_ext_reload
            });
            {/literal}{if $id}
            ts_ext_reload_evt({$pres_pct},true);
            {else}{literal}
            $("#add_edit_zef_pct_ID").selectOptions(""+selected_pct+"");
            $("#add_edit_zef_evt_ID").selectOptions(""+selected_evt+"");
            ts_ext_reload_evt(selected_pct);
            {/literal}{/if}{literal}

            $('#floater_innerwrap').tabs({ selected: 0 });
            ts_timeToDuration();
        }); 
        
    </script>
{/literal}


<div id="floater_innerwrap">

    <div id="floater_handle">
        <span id="floater_title">{if $id}{$kga.lang.edit}{else}{$kga.lang.add}{/if}</span>
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

    <form id="ts_ext_form_add_edit_record" action="../extensions/ki_timesheets/processor.php" method="post"> 
    <input name="id" type="hidden" value="{$id}" />
    <input name="axAction" type="hidden" value="add_edit_record" />


    <div id="floater_tabs" class="floater_content">
            <fieldset id="general">
                
                <ul>
                
                   <li>
                       <label for="pct_ID">{$kga.lang.pct}:</label>
                       <select size = "5" name="pct_ID" id="add_edit_zef_pct_ID" class="formfield" style="width:400px" tabindex="1" onChange="ts_ext_reload_evt($('#add_edit_zef_pct_ID').val());" >
                           {html_options values=$sel_pct_IDs output=$sel_pct_names selected=$pres_pct}
                       </select>
                       <br/>
                       <input type="input" style="margin-left:115px;width:395px;margin-top:3px" tabindex="2" size="10" name="filter" id="filter" onkeyup="filter_selects('add_edit_zef_pct_ID', this.value); ts_add_edit_validate();"/>
                   </li>
                   


                   <li>
                       <label for="evt_ID">{$kga.lang.evt}:</label>
                       <select size = "5" name="evt_ID" id="add_edit_zef_evt_ID" class="formfield" style="width:400px" tabindex="3" onChange="getBestRate();ts_add_edit_validate();" >
                           {html_options values=$sel_evt_IDs output=$sel_evt_names selected=$pres_evt}
                       </select>
                       <br/>
                      <input type="input" style="margin-left:115px;width:395px;margin-top:3px" tabindex="4" size="10" name="filter" id="filter" onkeyup="filter_selects('add_edit_zef_evt_ID', this.value); ts_add_edit_validate();" />
                   </li>
                
{* -------------------------------------------------------------------- *} 

                <li>
                     <label for="edit_in_day">{$kga.lang.day}:</label>
                     <input id='edit_in_day' type='text' name='edit_in_day' value='{$edit_in_day|escape:'html'}' maxlength='10' size='10' tabindex='5' onChange="ts_timeToDuration();" {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                     -
                     <input id='edit_out_day' type='text' name='edit_out_day' value='{$edit_out_day|escape:'html'}' maxlength='10' size='10' tabindex='6' onChange="ts_timeToDuration();" {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                </li>


              
                   <li>
                       <label for="time">{$kga.lang.timelabel}:</label>
                        <input id='edit_in_time' type='text' name='edit_in_time' value='{$edit_in_time|escape:'html'}' maxlength='8'  size='8'  tabindex='7' onChange="ts_timeToDuration();" {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                        -
                        <input id='edit_out_time' type='text' name='edit_out_time' value='{$edit_out_time|escape:'html'}' maxlength='8'  size='8'  tabindex='8' onChange="ts_timeToDuration();" {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                        <a href="#" onClick="pasteNow(); ts_timeToDuration(); $(this).blur(); return false;">{$kga.lang.now}</a>
                   </li>
                   <li>
                       <label for="duration">{$kga.lang.durationlabel}:</label>
                        <input id='edit_duration' type='text' name='edit_duration' value='' onChange="ts_durationToTime();" maxlength='8'  size='8'  tabindex='9' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                   </li>
                   <li>
                        <label for="rate">{$kga.lang.rate}:</label>
                        <input id='rate' type='text' name='rate' value='{$rate|escape:'html'}' maxlength='50' size='20' tabindex='10' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                   </li>
               </ul>
             </fieldset>
{* -------------------------------------------------------------------- *}       
            <fieldset id="extended">
                
                <ul>

                   <li>
                        <label for="zlocation">{$kga.lang.zlocation}:</label>
                        <input id='zlocation' type='text' name='zlocation' value='{$zlocation|escape:'html'}' maxlength='50' size='20' tabindex='11' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                   </li>

				{if $kga.show_TrackingNr}
                   <li>
                        <label for="trackingnr">{$kga.lang.trackingnr}:</label>
                        <input id='trackingnr' type='text' name='trackingnr' value='{$trackingnr|escape:'html'}' maxlength='20' size='20' tabindex='12' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                   </li>
				{/if}
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

                    <li>
                        <label for="cleared">{$kga.lang.cleared}:</label>
                        <input type='checkbox' id='cleared' name='cleared' {if $cleared} checked="checked" {/if} tabindex='16'/>
                           
                   </li>
        
                </ul>

{* -------------------------------------------------------------------- *} 
            </fieldset>

        </div>

                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}' />
                </div>

{* -------------------------------------------------------------------- *} 

        </form>

</div>
