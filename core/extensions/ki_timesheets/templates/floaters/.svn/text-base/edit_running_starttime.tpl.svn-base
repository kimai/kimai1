{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {


        $('#startday').datepicker();

          $('#edit_running_starttime').ajaxForm( { 'beforeSubmit' :function() { 

                if (!$('#starttime').val().match(ts_timeFormatExp)) {
                  alert("{/literal}{$kga.lang.TimeDateInputError}{literal}");
                  return false;
                }
                // make sure day is not in the future
                var currentDate = new Date();
                var outVals= Array(currentDate.getDate(),currentDate.getMonth()+1,currentDate.getFullYear());
                var inDayMatches = $('#startday').val().match(ts_dayFormatExp);
                for (var i = 3;i>=1;i--) {
                  var inVal = inDayMatches[i];
                  var outVal = outVals[i-1];

                  inVal = parseInt(inVal);
                  outval = parseInt(outVal);
                  
                  if (inVal == undefined)
                    inVal = 0;
                  if (outVal == undefined)
                    outVal = 0;
                  
                  if (inVal > outVal) {
                    alert("{/literal}{$kga.lang.DateTimeNotInFuture}{literal}");
                    return false;
                  }
                  else if (inVal < outVal)
                    break; // if this part is smaller we don't care for the other parts
                }

                if (inDayMatches[0] == strftime("%d.%m.%Y",currentDate)) {
                  // make sure time is not in the future
                  var outVals= Array(currentDate.getHours(),currentDate.getMinutes(),currentDate.getSeconds());
                  var timeMatches = $('#starttime').val().match(ts_timeFormatExp);
                    for (var i = 1;i<=3;i++) {
                      var inVal = timeMatches[i];
                      var outVal = outVals[i-1];
                      
                      if (inVal[0] == ":")
                        inVal = inVal.substr(1);

                      inVal = parseInt(inVal);
                      
                      if (inVal == undefined)
                        inVal = 0;
                      alert
                      if (inVal > outVal) {
                        alert("{/literal}{$kga.lang.DateTimeNotInFuture}{literal}");
                        return false;
                      }
                      else if (inVal < outVal)
                        break; // if this part is smaller we don't care for the other parts
                    }
                 }

                floaterClose();
                return true;
            },
              'success' : function(data) {
                parsed = parseInt(data);
                if (parsed != Number.NaN)
                  startsec = parsed;
                ts_ext_reload();
              }
            });

        }); 
    </script>
{/literal}

<div id="floater_innerwrap">
    
    <div id="floater_handle">
        <span id="floater_title">{$kga.lang.starttime}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
        </div>       
    </div>

    <div class="floater_content">

    {* send to CORE (!!!) processor *}
    
        <form id="edit_running_starttime" action="../extensions/ki_timesheets/processor.php" method="post"> 
            <fieldset>

                <ul>
                
                    <li>
                        <label for="starttime">{$kga.lang.starttime}:</label>
                        <input id='startday' type='text' name='startday' value='{$startday|escape:'html'}' maxlength='10'  size='8' tabindex='12' />
                        <input id='starttime' type='text' name='starttime' value='{$starttime|escape:'html'}' maxlength='8'  size='8' tabindex='13' />
                   </li>

                </ul>
 
                <input name="axAction"     type="hidden" value="edit_running_starttime" />   
                <input name="axValue"      type="hidden" value="{$id}" />     
                                             
                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}' />
                </div>
                
            </fieldset>
        </form>
        
    </div>
</div>
