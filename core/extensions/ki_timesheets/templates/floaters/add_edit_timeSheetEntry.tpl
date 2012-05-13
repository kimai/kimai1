{literal}    
    <script type="text/javascript"> 
    	var previousBudget = $('#budget').val();
        var previousUsed = 0;
        var previousApproved = 0;
        $(document).ready(function() {
            $('#help').hide();

            // only save the value, the update will happen automatically because we trigger a changed
            // activity on "edit_out_time"
			$('#currentTime, #end_day, #start_day').click(function() {
    			saveDuration();
			});

            $("#approved").focus(function () {
            	previousApproved = this.value;
            }).change(function() {
                if(isNaN($(this).val()) || $(this).val() == '') {
					$(this).val(0);
                }
            	$('#budget_activity_approved').text(parseFloat($('#budget_activity_approved').text())-previousApproved+parseFloat($(this).val()));
     			return false;
            });
            if($('#roundTimesheetEntries').val().length > 0) {
	            var step = $('#stepMinutes').val();
	            var stepSeconds = $('#stepSeconds').val();
	            if(isNaN(stepSeconds) || stepSeconds <= 0) {
		            if(!isNaN(step) && step > 0 && step < 60) {
		            $('#start_time').timePicker({step: parseInt(step)});
		            $('#end_time').timePicker({step: parseInt(step)});
		            } else {
		                $('#start_time').timePicker();
		                $('#end_time').timePicker();
		            }
	            }
            }
 
            // #rate already has an activity on click, so treat it below
            $("#eduration, #eend_time, #start_time").focus(function() {
    			saveDuration();
            }).change(function() {
            	updateDuration();
    			generateChart();
     			return false;
            });

            $('#add_edit_timeSheetEntry_activityID').change(function() {
                $.getJSON("../extensions/ki_timesheets/processor.php", {
                    axAction: "budgets",
                    project_id: $("#add_edit_timeSheetEntry_projectID").val(),
                    activity_id: $("#add_edit_timeSheetEntry_activityID").val(),
                    timeSheetEntryID: $('input[name="id"]').val()
                  },
                  function(data) {
                	  ts_ext_updateBudget(data);
                  }
                 );
            });

            $("#budget").focus(function () {
            	previousBudget = this.value;
            }).change(function() {
            	$('#activityBudget').text(parseFloat($('#activityBudget').text())-previousBudget+$(this).val());
     			generateChart();
     			return false;
            });
 			
            $('#start_day').datepicker({
              onSelect: function(dateText, instance) {
                $('#end_day').datepicker( "option", "minDate", $('#start_day').datepicker("getDate") );
                ts_timeToDuration();
              }
            });

            $('#end_day').datepicker({
              onSelect: function(dateText, instance) {
                $('#start_day').datepicker( "option", "maxDate", $('#end_day').datepicker("getDate") );
                ts_timeToDuration();
              }
            });

            $( "#rate" ).click(function() {
    			saveDuration();
              $( "#rate").autocomplete("search",0);
            });

            $( "#rate" ).change(function() {
                updateDuration();
            });
            
            $( "#rate" ).autocomplete({
              width:"200px",
              source: function(req, add){  
                $.getJSON("../extensions/ki_timesheets/processor.php", {
                    axAction: "allFittingRates",
                    project: $("#add_edit_timeSheetEntry_projectID").val(),
                    task: $("#add_edit_timeSheetEntry_activityID").val()
                  },
                  function(data) {
                    add(data);
                  }
                );  
              },
              select: function( activity, ui ) {
                $( "#rate" ).val( ui.item.value );

                return false;
              }
            }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )
                        .data( "item.autocomplete", item )
                        .append( "<a>" + item.desc + "</a>" )
                        .appendTo( ul );
            };

            $( "#fixedRate" ).click(function() {
              $( "#fixedRate").autocomplete("search",0);
            });
            
            $( "#fixedRate" ).autocomplete({
              width:"200px",
              source: function(req, add){  
                $.getJSON("../extensions/ki_timesheets/processor.php", {
                    axAction: "allFittingFixedRates",
                    project: $("#add_edit_timeSheetEntry_projectID").val(),
                    task: $("#add_edit_timeSheetEntry_activityID").val()
                  },
                  function(data) {
                    add(data);
                  }
                );  
              },
              select: function( activity, ui ) {
                $( "#fixedRate" ).val( ui.item.value );

                return false;
              }
            }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )
                        .data( "item.autocomplete", item )
                        .append( "<a>" + item.desc + "</a>" )
                        .appendTo( ul );
            };

            $('#ts_ext_form_add_edit_timeSheetEntry').ajaxForm( { 'beforeSubmit' :function() { 
                if (!$('#start_day').val().match(ts_dayFormatExp) ||
                    ( !$('#end_day').val().match(ts_dayFormatExp) && $('#end_day').val() != '') ||
                    !$('#start_time').val().match(ts_timeFormatExp) ||
                    ( !$('#end_time').val().match(ts_timeFormatExp) && $('#end_time').val() != '')) {
                  alert("{/literal}{$kga.lang.TimeDateInputError}{literal}");
                  return false;
                }

                var endTimeSet = $('#end_day').val() != '' || $('#end_time').val() != '';

                if (!endTimeSet)
                  return true; // no need to validate timerange if end time is not set

                // test if start day is before end day
                var inDayMatches = $('#start_day').val().match(ts_dayFormatExp);
                var outDayMatches = $('#end_day').val().match(ts_dayFormatExp);
                for (var i = 3;i>=1;i--) {
                  var inVal = inDayMatches[i];
                  var outVal = outDayMatches[i];

                  inVal = parseInt(inVal);
                  outval = parseInt(outVal);
                  
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
                if (inDayMatches[0] == outDayMatches[0]) {
                  // test if start time is before end time if it's the same day
                  var inTimeMatches = $('#start_time').val().match(ts_timeFormatExp);
                  var outTimeMatches = $('#end_time').val().match(ts_timeFormatExp);
                  for (var i = 1;i<=3;i++) {
                    var inVal = inTimeMatches[i];
                    var outVal = outTimeMatches[i];
                    
                    if (inVal[0] == ":")
                      inVal = inVal.substr(1);
                    if (outVal[0] == ":")
                      outVal = outVal.substr(1);

                    inVal = parseInt(inVal);
                    outval = parseInt(outVal);
                    
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
                }
                
                var edit_in_time = $('#start_day').val()+$('#start_time').val();
                var edit_out_time = $('#end_day').val()+$('#end_time').val();
                var deleted = $('#erase').is(':checked');
                
                if (!deleted && edit_in_time == edit_out_time) {
                    alert("{/literal}{$kga.lang.timediff_warn}{literal}");
                    return false;
                }

              return true;
            },
              'success' : function(data) {
                var result = jQuery.parseJSON(data);
                if (result.result == "ok") {
                  floaterClose();
                  ts_ext_reload();
                }
                else {
                  alert(result.message);
                }
              }
            });
            {/literal}{if $id}
            ts_ext_reload_activities({$preselected_project},true);
            {else}{literal}
            $("#add_edit_timeSheetEntry_projectID").selectOptions(""+selected_project+"");
            $("#add_edit_timeSheetEntry_activityID").selectOptions(""+selected_activity+"");
            ts_ext_reload_activities(selected_project);
            {/literal}{/if}{literal}

            $('#floater_innerwrap').tabs({ selected: 0 });
            ts_timeToDuration();
            // ts_timeToDuration will set the value of duration. The first time, the value
            // will be set and the duration is added to the budgetUsed eventhough it shouldn't
            // so maually subtract the value again
            var durationArray= new Array();
            durationArray = $("#duration").val().split(/:|\./);
            if(durationArray.length > 0 && durationArray.length < 4) {
                secs = durationArray[0]*3600;
                if(durationArray.length > 1)
                    secs += (durationArray[1]*60);
                if(durationArray.length > 2)
                    secs += parseInt(durationArray[2]);
        		var rate = $('#rate').val();
        		var budgetCalculatedTwice = secs/3600*rate;
            $('#budget_activity_used').text(Math.round(parseFloat($('#budget_activity_used').text())-budgetCalculatedTwice),2);
            }
            //TODO: chart will not be generated..WHY??
//            generateChart();
        }); 

        function saveDuration() {
			var durationArray=$("#duration").val().split(/:|\./);
			var secs = 0;
		    if(durationArray.length > 0 && durationArray.length < 4) {
		        secs = durationArray[0]*3600;
		        if(durationArray.length > 1)
		            secs += (durationArray[1]*60);
		        if(durationArray.length > 2)
		            secs += parseInt(durationArray[2]);
		    }
			var rate = $('#rate').val();
			previousUsed = secs/3600*rate;
        }

        function updateDuration() {
        	var durationArray=$("#duration").val().split(/:|\./);
			var secs = 0;
		    if(durationArray.length > 0 && durationArray.length < 4) {
		        secs = durationArray[0]*3600;
		        if(durationArray.length > 1)
		            secs += (durationArray[1]*60);
		        if(durationArray.length > 2)
		            secs += parseInt(durationArray[2]);
		    }
			var rate = $('#rate').val();
			var used = secs/3600*rate;
        	$('#budget_activity_used').text(Math.round(parseFloat($('#budget_activity_used').text())-previousUsed+used),2);
        }
        function generateChart() {
			var durationArray=$("#edit_duration").val().split(/:|\./);
			var secs = 0;
		    if(durationArray.length > 0 && durationArray.length < 4) {
		        secs = durationArray[0]*3600;
		        if(durationArray.length > 1)
		            secs += (durationArray[1]*60);
		        if(durationArray.length > 2)
		            secs += parseInt(durationArray[2]);
		    }
			var rate = $('#rate').val();
			var budget = $('#budget_val').val();
			var used = secs/3600*rate;
			var usedString = '{/literal}{$kga.lang.used}{literal}';
			var budgetString = '{/literal}{$kga.lang.budget_available}{literal}';
			var chartdata = [[usedString, used], [budgetString, budget-used]];

            $.jqplot('chart',  [chartdata], {              
                seriesDefaults:{renderer:$.jqplot.PieRenderer,
                    rendererOptions: {padding:10,
                        showDataLabels: true,
//                        // By default, data labels show the percentage of the donut/pie.
//                        // You can show the data 'value' or data 'label' instead.
                        dataLabels: 'value'
                    }
                },
                    // Show the legend and put it outside the grid, but inside the
                    // plot container, shrinking the grid to accomodate the legend.
                    // A value of "outside" would not shrink the grid and allow
                    // the legend to overflow the container.
                    legend: {
                        show: true,
                        placement: 'insideGrid'
                    },
                grid:{background: 'white', borderWidth:0, shadow:false}
            });
        }
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
          <li class="tab norm"><a href="#budget">
                      <span class="aa">&nbsp;</span>
                      <span class="bb">{$kga.lang.budget}</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
      </ul>
    </div>

    <form id="ts_ext_form_add_edit_timeSheetEntry" action="../extensions/ki_timesheets/processor.php" method="post"> 
    <input name="id" type="hidden" value="{$id}" />
    <input name="axAction" type="hidden" value="add_edit_timeSheetEntry" />
	<input id="stepMinutes" type="hidden" value="{$kga.conf.roundMinutes}" />
	<input id="stepSeconds" type="hidden" value="{$kga.conf.roundSeconds}" />
	<input id="roundTimesheetEntries" type="hidden" value="{$kga.conf.roundTimesheetEntries}" />

    <div id="floater_tabs" class="floater_content">
            <fieldset id="general">
                
                <ul>
                
                   <li>
                       <label for="projectID">{$kga.lang.project}:</label>
                       <div class="multiFields">
                        <select size = "5" name="projectID" id="add_edit_timeSheetEntry_projectID" class="formfield" style="width:400px" tabindex="1" onChange="ts_ext_reload_activities($('#add_edit_timeSheetEntry_projectID').val(),undefined,$('#add_edit_timeSheetEntry_activityID').val(), $('input[name=\'id\']').val());" >
                            {html_options values=$projectIDs output=$projectNames selected=$projectID}
                        </select>
                        <br/>
                        <input type="input" style="width:395px;margin-top:3px" tabindex="2" size="10" name="filter" id="filter" onkeyup="filter_selects('add_edit_timeSheetEntry_projectID', this.value); ts_add_edit_validate();"/>
                       </div>
                   </li>
                   


                   <li>
                       <label for="activityID">{$kga.lang.activity}:</label>
                       <div class="multiFields">
                        <select size = "5" name="activityID" id="add_edit_timeSheetEntry_activityID" class="formfield" style="width:400px" tabindex="3" onChange="getBestRates();ts_add_edit_validate();" >
                            {html_options values=$activityIDs output=$activityNames selected=$activityID}
                        </select>
                        <br/>
                        <input type="input" style="width:395px;margin-top:3px" tabindex="4" size="10" name="filter" id="filter" onkeyup="filter_selects('add_edit_timeSheetEntry_activityID', this.value); ts_add_edit_validate();" />
                      </div>
                   </li>
                
{* -------------------------------------------------------------------- *} 



                   <li>
                       <label for="description">{$kga.lang.description}:</label>
                        <textarea tabindex="5" style="width:395px" cols='40' rows='5' name="description" id="description">{$description|escape:'html'}</textarea>
                   </li>

                <li>
                     <label>{$kga.lang.day}:</label>
                     <input id='start_day' type='text' name='start_day' value='{$start_day|escape:'html'}' maxlength='10' size='10' tabindex='6' onChange="ts_timeToDuration();" {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                     -
                     <input id='end_day' type='text' name='end_day' value='{$end_day|escape:'html'}' maxlength='10' size='10' tabindex='7' onChange="ts_timeToDuration();" {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                </li>


              
                   <li>
                       <label>{$kga.lang.timelabel}:</label>
                        <input id='start_time' type='text' name='start_time' value='{$start_time|escape:'html'}' maxlength='8'  size='8'  tabindex='8' onChange="ts_timeToDuration();" {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                        -
                        <input id='end_time' type='text' name='end_time' value='{$end_time|escape:'html'}' maxlength='8'  size='8'  tabindex='9' onChange="ts_timeToDuration();" {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                        <a id="currentTime" href="#" onClick="pasteNow(); ts_timeToDuration(); $(this).blur(); return false;">{$kga.lang.now}</a>
                   </li>
                   <li>
                       <label for="duration">{$kga.lang.durationlabel}:</label>
                        <input id='duration' type='text' name='duration' value='' onChange="ts_durationToTime();" maxlength='8'  size='8'  tabindex='10' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                   </li>
               </ul>
             </fieldset>
{* -------------------------------------------------------------------- *}       
            <fieldset id="extended">
                
                <ul>

                   <li>
                        <label for="location">{$kga.lang.location}:</label>
                        <input id='location' type='text' name='location' value='{$location|escape:'html'}' maxlength='50' size='20' tabindex='11' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                   </li>

				{if $kga.show_TrackingNr}
                   <li>
                        <label for="trackingNumber">{$kga.lang.trackingNumber}:</label>
                        <input id='trackingNumber' type='text' name='trackingNumber' value='{$trackingNumber|escape:'html'}' maxlength='20' size='20' tabindex='12' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                   </li>
				{/if}
                        <label for="comment">{$kga.lang.comment}:</label>
                        <textarea id='comment' style="width:395px" class='comment' name='comment' cols='40' rows='5' tabindex='13'>{$comment|escape:'html'}</textarea>
                   </li>
                   
                   <li>
                       <label for="commentType">{$kga.lang.commentType}:</label>
                       <select id="commentType" class="formfield" name="commentType" tabindex="14" >
                           {html_options values=$commentValues output=$commentTypes selected=$commentType}
                       </select>
                   </li>
                   {if $kga.user.status != 2}   
                   <li>
                       <label for="userID">{$kga.lang.user}:</label>
                       <select id="userID" class="formfield" name="userID" tabindex="14" >
                           {html_options values=$userIDs output=$userNames selected=$userID}
                       </select>
                   </li>
                   {/if}
                   
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
            <fieldset id="budget">
                
                <ul>

                   <li>
                        <label for="budget">{$kga.lang.budget}:</label>
                        <input id='budget_val' type='text' name='budget' value='{$budget|escape:'html'}' maxlength='50' size='20' tabindex='11' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                   </li>
                   <li>
                        <label for="approved">{$kga.lang.approved}:</label>
                        <input id='approved' type='text' name='approved' value='{$approved|escape:'html'}' maxlength='50' size='20' tabindex='11' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                   </li>
                   
                   <li>
                       <label for="statusID">{$kga.lang.status}:</label>
                       <select id="statusID" class="formfield" name="statusID" tabindex="15" >
                           {html_options options=$status selected=$status_active}
                       </select>
                   </li>
                   
                   <li>
                       <label for="billable">{$kga.lang.billable}:</label>
                       <select id="billable" class="formfield" name="billable" tabindex="16" >
                           {html_options values=$billableValues output=$billable selected=$billable_active}
                       </select>
                   </li>
                   <li>
                        <label for="rate">{$kga.lang.rate}:</label>
                        <input id='rate' type='text' name='rate' value='{$rate|escape:'html'}' size='5' tabindex='10' />
                        </select>
                        <label for="fixedRate" style="float: none; margin-left: 60px;">{$kga.lang.fixedRate}:</label>
                        <input id='fixedRate' type='text' name='fixedRate' value='{$fixedRate|escape:'html'}' size='5' tabindex='10' {if $kga.conf.autoselection}onClick="this.select();"{/if} />
                        </select>
                   </li>
                   
                   <li>
                   <table><tr><td align="right">{$kga.lang.budget_activity}:</td><td>
                        <span id="budget_activity">{$budget_activity}</span></td></tr>
                        <tr><td align="right">{$kga.lang.budget_activity_used}:</td><td>
                        <span id="budget_activity_used">{$budget_activity_used}</span></td></tr>
                        <tr><td align="right">{$kga.lang.budget_activity_approved}:</td><td>
                        <span id="budget_activity_approved">{$approved_activity}</span></td></tr>
                        </table>
                   </li>
        <li id="chart">
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
