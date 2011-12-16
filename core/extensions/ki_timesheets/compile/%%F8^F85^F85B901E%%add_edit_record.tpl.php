<?php /* Smarty version 2.6.20, created on 2011-12-09 13:07:48
         compiled from add_edit_record.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'add_edit_record.tpl', 380, false),array('modifier', 'escape', 'add_edit_record.tpl', 406, false),)), $this); ?>
<?php echo '    
    <script type="text/javascript"> 
    	var previousBudget = $(\'#budget\').val();
        var previousUsed = 0;
        var previousApproved = 0;
        $(document).ready(function() {
            $(\'#help\').hide();

            // only save the value, the update will happen automatically because we trigger a changed
            // event on "edit_out_time"
			$(\'#currentTime, #edit_out_day, #edit_in_day\').click(function() {
    			saveDuration();
			});

            $("#approved").focus(function () {
            	previousApproved = this.value;
            }).change(function() {
                if(isNaN($(this).val()) || $(this).val() == \'\') {
					$(this).val(0);
                }
            	$(\'#budget_event_approved\').text(parseFloat($(\'#budget_event_approved\').text())-previousApproved+parseFloat($(this).val()));
     			return false;
            });
            if($(\'#roundTimesheetEntries\').val().length > 0) {
	            var step = $(\'#stepMinutes\').val();
	            var stepSeconds = $(\'#stepSeconds\').val();
	            if(isNaN(stepSeconds) || stepSeconds <= 0) {
		            if(!isNaN(step) && step > 0 && step < 60) {
		            $(\'#edit_in_time\').timePicker({step: parseInt(step)});
		            $(\'#edit_out_time\').timePicker({step: parseInt(step)});
		            } else {
		                $(\'#edit_in_time\').timePicker();
		                $(\'#edit_out_time\').timePicker();
		            }
	            }
            }
 
            // #rate already has an event on click, so treat it below
            $("#edit_duration, #edit_out_time, #edit_in_time").focus(function() {
    			saveDuration();
            }).change(function() {
            	updateDuration();
    			generateChart();
     			return false;
            });

            $(\'#add_edit_zef_evt_ID\').change(function() {
                $.getJSON("../extensions/ki_timesheets/processor.php", {
                    axAction: "budgets",
                    project_id: $("#add_edit_zef_pct_ID").val(),
                    event_id: $("#add_edit_zef_evt_ID").val(),
                    zef_id: $(\'input[name="id"]\').val()
                  },
                  function(data) {
                	  ts_ext_updateBudget(data);
                  }
                 );
            });

            $("#budget").focus(function () {
            	previousBudget = this.value;
            }).change(function() {
            	$(\'#eventBudget\').text(parseFloat($(\'#eventBudget\').text())-previousBudget+$(this).val());
     			generateChart();
     			return false;
            });
 			
            $(\'#edit_in_day\').datepicker({
              onSelect: function(dateText, instance) {
                $(\'#edit_out_day\').datepicker( "option", "minDate", $(\'#edit_in_day\').datepicker("getDate") );
                ts_timeToDuration();
              }
            });

            $(\'#edit_out_day\').datepicker({
              onSelect: function(dateText, instance) {
                $(\'#edit_in_day\').datepicker( "option", "maxDate", $(\'#edit_out_day\').datepicker("getDate") );
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
                    project: $("#add_edit_zef_pct_ID").val(),
                    task: $("#add_edit_zef_evt_ID").val()
                  },
                  function(data) {
                    add(data);
                  }
                );  
              },
              select: function( event, ui ) {
                $( "#rate" ).val( ui.item.value );

                return false;
              }
            }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )
                        .data( "item.autocomplete", item )
                        .append( "<a>" + item.desc + "</a>" )
                        .appendTo( ul );
            };

            $( "#fixed_rate" ).click(function() {
              $( "#fixed_rate").autocomplete("search",0);
            });
            
            $( "#fixed_rate" ).autocomplete({
              width:"200px",
              source: function(req, add){  
                $.getJSON("../extensions/ki_timesheets/processor.php", {
                    axAction: "allFittingFixedRates",
                    project: $("#add_edit_zef_pct_ID").val(),
                    task: $("#add_edit_zef_evt_ID").val()
                  },
                  function(data) {
                    add(data);
                  }
                );  
              },
              select: function( event, ui ) {
                $( "#fixed_rate" ).val( ui.item.value );

                return false;
              }
            }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )
                        .data( "item.autocomplete", item )
                        .append( "<a>" + item.desc + "</a>" )
                        .appendTo( ul );
            };

            $(\'#ts_ext_form_add_edit_record\').ajaxForm( { \'beforeSubmit\' :function() { 

                if (!$(\'#edit_in_day\').val().match(ts_dayFormatExp) ||
                    !$(\'#edit_out_day\').val().match(ts_dayFormatExp) ||
                    !$(\'#edit_in_time\').val().match(ts_timeFormatExp) ||
                    !$(\'#edit_out_time\').val().match(ts_timeFormatExp)) {
                  alert("'; ?>
<?php echo $this->_tpl_vars['kga']['lang']['TimeDateInputError']; ?>
<?php echo '");
                  return false;
                }

                // test if start day is before end day
                var inDayMatches = $(\'#edit_in_day\').val().match(ts_dayFormatExp);
                var outDayMatches = $(\'#edit_out_day\').val().match(ts_dayFormatExp);
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
                    alert("'; ?>
<?php echo $this->_tpl_vars['kga']['lang']['StartTimeBeforeEndTime']; ?>
<?php echo '");
                    return false;
                  }
                  else if (inVal < outVal)
                    break; // if this part is smaller we don\'t care for the other parts
                }
                if (inDayMatches[0] == outDayMatches[0]) {
                  // test if start time is before end time if it\'s the same day
                  var inTimeMatches = $(\'#edit_in_time\').val().match(ts_timeFormatExp);
                  var outTimeMatches = $(\'#edit_out_time\').val().match(ts_timeFormatExp);
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
                      alert("'; ?>
<?php echo $this->_tpl_vars['kga']['lang']['StartTimeBeforeEndTime']; ?>
<?php echo '");
                      return false;
                    }
                    else if (inVal < outVal)
                      break; // if this part is smaller we don\'t care for the other parts
                  }
                }
                
                var edit_in_time = $(\'#edit_in_day\').val()+$(\'#edit_in_time\').val();
                var edit_out_time = $(\'#edit_out_day\').val()+$(\'#edit_out_time\').val();
                var deleted = $(\'#erase\').is(\':checked\');
                
                if (!deleted && edit_in_time == edit_out_time) {
                    alert("'; ?>
<?php echo $this->_tpl_vars['kga']['lang']['timediff_warn']; ?>
<?php echo '");
                    return false;
                }

              return true;
            },
              \'success\' : function(data) {
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
            '; ?>
<?php if ($this->_tpl_vars['id']): ?>
            ts_ext_reload_evt(<?php echo $this->_tpl_vars['pres_pct']; ?>
,true);
            <?php else: ?><?php echo '
            $("#add_edit_zef_pct_ID").selectOptions(""+selected_pct+"");
            $("#add_edit_zef_evt_ID").selectOptions(""+selected_evt+"");
            ts_ext_reload_evt(selected_pct);
            '; ?>
<?php endif; ?><?php echo '

            $(\'#floater_innerwrap\').tabs({ selected: 0 });
            ts_timeToDuration();
            // ts_timeToDuration will set the value of duration. The first time, the value
            // will be set and the duration is added to the budgetUsed eventhough it shouldn\'t
            // so maually subtract the value again
            var durationArray= new Array();
            durationArray = $("#edit_duration").val().split(/:|\\./);
            if(durationArray.length > 0 && durationArray.length < 4) {
                secs = durationArray[0]*3600;
                if(durationArray.length > 1)
                    secs += (durationArray[1]*60);
                if(durationArray.length > 2)
                    secs += parseInt(durationArray[2]);
        		var rate = $(\'#rate\').val();
        		var budgetCalculatedTwice = secs/3600*rate;
            $(\'#budget_event_used\').text(Math.round(parseFloat($(\'#budget_event_used\').text())-budgetCalculatedTwice),2);
            }
            //TODO: chart will not be generated..WHY??
//            generateChart();
        }); 

        function saveDuration() {
			var durationArray=$("#edit_duration").val().split(/:|\\./);
			var secs = 0;
		    if(durationArray.length > 0 && durationArray.length < 4) {
		        secs = durationArray[0]*3600;
		        if(durationArray.length > 1)
		            secs += (durationArray[1]*60);
		        if(durationArray.length > 2)
		            secs += parseInt(durationArray[2]);
		    }
			var rate = $(\'#rate\').val();
			previousUsed = secs/3600*rate;
        }

        function updateDuration() {
        	var durationArray=$("#edit_duration").val().split(/:|\\./);
			var secs = 0;
		    if(durationArray.length > 0 && durationArray.length < 4) {
		        secs = durationArray[0]*3600;
		        if(durationArray.length > 1)
		            secs += (durationArray[1]*60);
		        if(durationArray.length > 2)
		            secs += parseInt(durationArray[2]);
		    }
			var rate = $(\'#rate\').val();
			var used = secs/3600*rate;
        	$(\'#budget_event_used\').text(Math.round(parseFloat($(\'#budget_event_used\').text())-previousUsed+used),2);
        }
        function generateChart() {
			var durationArray=$("#edit_duration").val().split(/:|\\./);
			var secs = 0;
		    if(durationArray.length > 0 && durationArray.length < 4) {
		        secs = durationArray[0]*3600;
		        if(durationArray.length > 1)
		            secs += (durationArray[1]*60);
		        if(durationArray.length > 2)
		            secs += parseInt(durationArray[2]);
		    }
			var rate = $(\'#rate\').val();
			var budget = $(\'#budget_val\').val();
			var used = secs/3600*rate;
			var usedString = \''; ?>
<?php echo $this->_tpl_vars['kga']['lang']['used']; ?>
<?php echo '\';
			var budgetString = \''; ?>
<?php echo $this->_tpl_vars['kga']['lang']['budget_available']; ?>
<?php echo '\';
			var chartdata = [[usedString, used], [budgetString, budget-used]];

            $.jqplot(\'chart\',  [chartdata], {              
                seriesDefaults:{renderer:$.jqplot.PieRenderer,
                    rendererOptions: {padding:10,
                        showDataLabels: true,
//                        // By default, data labels show the percentage of the donut/pie.
//                        // You can show the data \'value\' or data \'label\' instead.
                        dataLabels: \'value\'
                    }
                },
                    // Show the legend and put it outside the grid, but inside the
                    // plot container, shrinking the grid to accomodate the legend.
                    // A value of "outside" would not shrink the grid and allow
                    // the legend to overflow the container.
                    legend: {
                        show: true,
                        placement: \'insideGrid\'
                    },
                grid:{background: \'white\', borderWidth:0, shadow:false}
            });
        }
    </script>
'; ?>



<div id="floater_innerwrap">

    <div id="floater_handle">
        <span id="floater_title"><?php if ($this->_tpl_vars['id']): ?><?php echo $this->_tpl_vars['kga']['lang']['edit']; ?>
<?php else: ?><?php echo $this->_tpl_vars['kga']['lang']['add']; ?>
<?php endif; ?></span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();"><?php echo $this->_tpl_vars['kga']['lang']['close']; ?>
</a>
            <a href="#" class="help" onClick="$(this).blur(); $('#help').slideToggle();"><?php echo $this->_tpl_vars['kga']['lang']['help']; ?>
</a>
        </div>  
    </div>

    <div id="help">
        <div class="content">        
            <?php echo $this->_tpl_vars['kga']['lang']['dateAndTimeHelp']; ?>

        </div>
    </div>
    
    <div class="menuBackground">

      <ul class="menu tabSelection">
          <li class="tab norm"><a href="#general">
                      <span class="aa">&nbsp;</span>
                      <span class="bb"><?php echo $this->_tpl_vars['kga']['lang']['general']; ?>
</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
          <li class="tab norm"><a href="#extended">
                      <span class="aa">&nbsp;</span>
                      <span class="bb"><?php echo $this->_tpl_vars['kga']['lang']['advanced']; ?>
</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
          <li class="tab norm"><a href="#budget">
                      <span class="aa">&nbsp;</span>
                      <span class="bb"><?php echo $this->_tpl_vars['kga']['lang']['budget']; ?>
</span>
                      <span class="cc">&nbsp;</span>
                      </a></li>
      </ul>
    </div>

    <form id="ts_ext_form_add_edit_record" action="../extensions/ki_timesheets/processor.php" method="post"> 
    <input name="id" type="hidden" value="<?php echo $this->_tpl_vars['id']; ?>
" />
    <input name="axAction" type="hidden" value="add_edit_record" />
	<input id="stepMinutes" type="hidden" value="<?php echo $this->_tpl_vars['kga']['conf']['roundMinutes']; ?>
" />
	<input id="stepSeconds" type="hidden" value="<?php echo $this->_tpl_vars['kga']['conf']['roundSeconds']; ?>
" />
	<input id="roundTimesheetEntries" type="hidden" value="<?php echo $this->_tpl_vars['kga']['conf']['roundTimesheetEntries']; ?>
" />

    <div id="floater_tabs" class="floater_content">
            <fieldset id="general">
                
                <ul>
                
                   <li>
                       <label for="pct_ID"><?php echo $this->_tpl_vars['kga']['lang']['pct']; ?>
:</label>
                       <div class="multiFields">
                        <select size = "5" name="pct_ID" id="add_edit_zef_pct_ID" class="formfield" style="width:400px" tabindex="1" onChange="ts_ext_reload_evt($('#add_edit_zef_pct_ID').val(),undefined,$('#add_edit_zef_evt_ID').val(), $('input[name=\'id\']').val());" >
                            <?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['sel_pct_IDs'],'output' => $this->_tpl_vars['sel_pct_names'],'selected' => $this->_tpl_vars['pres_pct']), $this);?>

                        </select>
                        <br/>
                        <input type="input" style="width:395px;margin-top:3px" tabindex="2" size="10" name="filter" id="filter" onkeyup="filter_selects('add_edit_zef_pct_ID', this.value); ts_add_edit_validate();"/>
                       </div>
                   </li>
                   


                   <li>
                       <label for="evt_ID"><?php echo $this->_tpl_vars['kga']['lang']['evt']; ?>
:</label>
                       <div class="multiFields">
                        <select size = "5" name="evt_ID" id="add_edit_zef_evt_ID" class="formfield" style="width:400px" tabindex="3" onChange="getBestRates();ts_add_edit_validate();" >
                            <?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['sel_evt_IDs'],'output' => $this->_tpl_vars['sel_evt_names'],'selected' => $this->_tpl_vars['pres_evt']), $this);?>

                        </select>
                        <br/>
                        <input type="input" style="width:395px;margin-top:3px" tabindex="4" size="10" name="filter" id="filter" onkeyup="filter_selects('add_edit_zef_evt_ID', this.value); ts_add_edit_validate();" />
                      </div>
                   </li>
                
 



                   <li>
                       <label for="description"><?php echo $this->_tpl_vars['kga']['lang']['description']; ?>
:</label>
                        <textarea tabindex="5" style="width:395px" cols='40' rows='5' name="description" id="description"><?php echo ((is_array($_tmp=$this->_tpl_vars['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</textarea>
                   </li>

                <li>
                     <label for="edit_in_day"><?php echo $this->_tpl_vars['kga']['lang']['day']; ?>
:</label>
                     <input id='edit_in_day' type='text' name='edit_in_day' value='<?php echo ((is_array($_tmp=$this->_tpl_vars['edit_in_day'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' maxlength='10' size='10' tabindex='6' onChange="ts_timeToDuration();" <?php if ($this->_tpl_vars['kga']['conf']['autoselection']): ?>onClick="this.select();"<?php endif; ?> />
                     -
                     <input id='edit_out_day' type='text' name='edit_out_day' value='<?php echo ((is_array($_tmp=$this->_tpl_vars['edit_out_day'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' maxlength='10' size='10' tabindex='7' onChange="ts_timeToDuration();" <?php if ($this->_tpl_vars['kga']['conf']['autoselection']): ?>onClick="this.select();"<?php endif; ?> />
                </li>


              
                   <li>
                       <label for="time"><?php echo $this->_tpl_vars['kga']['lang']['timelabel']; ?>
:</label>
                        <input id='edit_in_time' type='text' name='edit_in_time' value='<?php echo ((is_array($_tmp=$this->_tpl_vars['edit_in_time'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' maxlength='8'  size='8'  tabindex='8' onChange="ts_timeToDuration();" <?php if ($this->_tpl_vars['kga']['conf']['autoselection']): ?>onClick="this.select();"<?php endif; ?> />
                        -
                        <input id='edit_out_time' type='text' name='edit_out_time' value='<?php echo ((is_array($_tmp=$this->_tpl_vars['edit_out_time'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' maxlength='8'  size='8'  tabindex='9' onChange="ts_timeToDuration();" <?php if ($this->_tpl_vars['kga']['conf']['autoselection']): ?>onClick="this.select();"<?php endif; ?> />
                        <a id="currentTime" href="#" onClick="pasteNow(); ts_timeToDuration(); $(this).blur(); return false;"><?php echo $this->_tpl_vars['kga']['lang']['now']; ?>
</a>
                   </li>
                   <li>
                       <label for="duration"><?php echo $this->_tpl_vars['kga']['lang']['durationlabel']; ?>
:</label>
                        <input id='edit_duration' type='text' name='edit_duration' value='' onChange="ts_durationToTime();" maxlength='8'  size='8'  tabindex='10' <?php if ($this->_tpl_vars['kga']['conf']['autoselection']): ?>onClick="this.select();"<?php endif; ?> />
                   </li>
               </ul>
             </fieldset>
       
            <fieldset id="extended">
                
                <ul>

                   <li>
                        <label for="zlocation"><?php echo $this->_tpl_vars['kga']['lang']['zlocation']; ?>
:</label>
                        <input id='zlocation' type='text' name='zlocation' value='<?php echo ((is_array($_tmp=$this->_tpl_vars['zlocation'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' maxlength='50' size='20' tabindex='11' <?php if ($this->_tpl_vars['kga']['conf']['autoselection']): ?>onClick="this.select();"<?php endif; ?> />
                   </li>

				<?php if ($this->_tpl_vars['kga']['show_TrackingNr']): ?>
                   <li>
                        <label for="trackingnr"><?php echo $this->_tpl_vars['kga']['lang']['trackingnr']; ?>
:</label>
                        <input id='trackingnr' type='text' name='trackingnr' value='<?php echo ((is_array($_tmp=$this->_tpl_vars['trackingnr'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' maxlength='20' size='20' tabindex='12' <?php if ($this->_tpl_vars['kga']['conf']['autoselection']): ?>onClick="this.select();"<?php endif; ?> />
                   </li>
				<?php endif; ?>
                        <label for="comment"><?php echo $this->_tpl_vars['kga']['lang']['comment']; ?>
:</label>
                        <textarea id='comment' style="width:395px" class='comment' name='comment' cols='40' rows='5' tabindex='13'><?php echo ((is_array($_tmp=$this->_tpl_vars['comment'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</textarea>
                   </li>
                   
                   <li>
                       <label for="comment_type"><?php echo $this->_tpl_vars['kga']['lang']['comment_type']; ?>
:</label>
                       <select id="comment_type" class="formfield" name="comment_type" tabindex="14" >
                           <?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['comment_values'],'output' => $this->_tpl_vars['comment_types'],'selected' => $this->_tpl_vars['comment_active']), $this);?>

                       </select>
                   </li>
                   
                    <li>
                        <label for="erase"><?php echo $this->_tpl_vars['kga']['lang']['erase']; ?>
:</label>
                        <input type='checkbox' id='erase' name='erase' tabindex='15'/>
                   </li>

                    <li>
                        <label for="cleared"><?php echo $this->_tpl_vars['kga']['lang']['cleared']; ?>
:</label>
                        <input type='checkbox' id='cleared' name='cleared' <?php if ($this->_tpl_vars['cleared']): ?> checked="checked" <?php endif; ?> tabindex='16'/>
                           
                   </li>
        
                </ul>

 
            </fieldset>            
            <fieldset id="budget">
                
                <ul>

                   <li>
                        <label for="budget"><?php echo $this->_tpl_vars['kga']['lang']['budget']; ?>
:</label>
                        <input id='budget_val' type='text' name='budget' value='<?php echo ((is_array($_tmp=$this->_tpl_vars['budget'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' maxlength='50' size='20' tabindex='11' <?php if ($this->_tpl_vars['kga']['conf']['autoselection']): ?>onClick="this.select();"<?php endif; ?> />
                   </li>
                   <li>
                        <label for="approved"><?php echo $this->_tpl_vars['kga']['lang']['approved']; ?>
:</label>
                        <input id='approved' type='text' name='approved' value='<?php echo ((is_array($_tmp=$this->_tpl_vars['approved'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' maxlength='50' size='20' tabindex='11' <?php if ($this->_tpl_vars['kga']['conf']['autoselection']): ?>onClick="this.select();"<?php endif; ?> />
                   </li>
                   
                   <li>
                       <label for="status"><?php echo $this->_tpl_vars['kga']['lang']['status']; ?>
:</label>
                       <select id="status" class="formfield" name="status" tabindex="15" >
                           <?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['statusIds'],'output' => $this->_tpl_vars['status'],'selected' => $this->_tpl_vars['status_active']), $this);?>

                       </select>
                   </li>
                   
                   <li>
                       <label for="billable"><?php echo $this->_tpl_vars['kga']['lang']['billable']; ?>
:</label>
                       <select id="billable" class="formfield" name="billable" tabindex="16" >
                           <?php echo smarty_function_html_options(array('values' => $this->_tpl_vars['billableValues'],'output' => $this->_tpl_vars['billable'],'selected' => $this->_tpl_vars['billable_active']), $this);?>

                       </select>
                   </li>
                   <li>
                        <label for="rate"><?php echo $this->_tpl_vars['kga']['lang']['rate']; ?>
:</label>
                        <input id='rate' type='text' name='rate' value='<?php echo ((is_array($_tmp=$this->_tpl_vars['rate'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' size='5' tabindex='10' />
                        </select>
                        <label for="fixed_rate" style="float: none; margin-left: 60px;"><?php echo $this->_tpl_vars['kga']['lang']['fixed_rate']; ?>
:</label>
                        <input id='fixed_rate' type='text' name='fixed_rate' value='<?php echo ((is_array($_tmp=$this->_tpl_vars['fixed_rate'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' size='5' tabindex='10' <?php if ($this->_tpl_vars['kga']['conf']['autoselection']): ?>onClick="this.select();"<?php endif; ?> />
                        </select>
                   </li>
                   
                   <li>
                   <table><tr><td align="right"><?php echo $this->_tpl_vars['kga']['lang']['budget_event']; ?>
:</td><td>
                        <span id="budget_event"><?php echo $this->_tpl_vars['budget_event']; ?>
</span></td></tr>
                        <tr><td align="right"><?php echo $this->_tpl_vars['kga']['lang']['budget_event_used']; ?>
:</td><td>
                        <span id="budget_event_used"><?php echo $this->_tpl_vars['budget_event_used']; ?>
</span></td></tr>
                        <tr><td align="right"><?php echo $this->_tpl_vars['kga']['lang']['budget_event_approved']; ?>
:</td><td>
                        <span id="budget_event_approved"><?php echo $this->_tpl_vars['approved_event']; ?>
</span></td></tr>
                        </table>
                   </li>
        <li id="chart">
        </li>
                </ul>

 
            </fieldset>

        </div>

                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='<?php echo $this->_tpl_vars['kga']['lang']['cancel']; ?>
' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='<?php echo $this->_tpl_vars['kga']['lang']['submit']; ?>
' />
                </div>

 

        </form>

</div>