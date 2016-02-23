<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php 
            if (isset($this->id)) {
                echo $this->kga['lang']['edit'];
            } else {
                echo $this->kga['lang']['add'];
            } 
            ?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->kga['lang']['close'] ?></a>
            <a href="#" class="help" onclick="$(this).blur(); $('#help').slideToggle();"><?php echo $this->kga['lang']['help'] ?></a>
        </div>
    </div>
    <div id="help">
        <div class="content"><?php echo $this->kga['lang']['dateAndTimeHelp'] ?></div>
    </div>
    <div class="menuBackground">
        <ul class="menu tabSelection">
            <li class="tab norm"><a href="#general">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->kga['lang']['general'] ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
            <li class="tab norm"><a href="#extended">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->kga['lang']['advanced'] ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
            <li class="tab norm"><a href="#budget">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->kga['lang']['budget'] ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
        </ul>
    </div>

    <form id="ts_ext_form_add_edit_timeSheetEntry" action="../extensions/ki_timesheets/processor.php" method="post">
    <input type="hidden" name="id" value="<?php echo $this->id?>" />
    <input type="hidden" name="axAction" value="add_edit_timeSheetEntry" />
	<input type="hidden" id="stepMinutes" value="<?php echo $this->kga['conf']['roundMinutes']?>" />
	<input type="hidden" id="stepSeconds" value="<?php echo $this->kga['conf']['roundSeconds']?>" />
	<input type="hidden" id="roundTimesheetEntries" value="<?php echo $this->kga['conf']['roundTimesheetEntries']?>" />
        <div id="floater_tabs" class="floater_content">
            <fieldset id="general">
                <ul>
                    <li>
                        <label for="projectID"><?php echo $this->kga['lang']['project'] ?>:</label>
                        <div class="multiFields">
                            <?php echo $this->formSelect('projectID', $this->projectID, array(
                                'size' => '5',
                                'id' => 'add_edit_timeSheetEntry_projectID',
                                'class' => 'formfield',
                                'style' => 'width:400px',
                                'tabindex' => '1',
                                'onChange' => "ts_ext_reload_activities($('#add_edit_timeSheetEntry_projectID').val(),undefined,$('#add_edit_timeSheetEntry_activityID').val(), $('input[name=\'id\']').val());"
                            ), $this->projects); ?>
                            <br/>
                            <input type="text" style="width:395px;margin-top:3px" tabindex="2" size="10" name="filter" id="filter" onkeyup="filter_selects('add_edit_timeSheetEntry_projectID', this.value);"/>
                        </div>
                    </li>
                    <li>
                        <label for="activityID"><?php echo $this->kga['lang']['activity'] ?>:</label>
                        <div class="multiFields">
                            <?php echo $this->formSelect('activityID', $this->activityID, array(
                                'size' => '5',
                                'id' => 'add_edit_timeSheetEntry_activityID',
                                'class' => 'formfield',
                                'style' => 'width:400px',
                                'tabindex' => '3',
                                'onChange' => "getBestRates();"
                            ), $this->activities); ?>
                            <br/>
                            <input type="text" style="width:395px;margin-top:3px" tabindex="4" size="10" name="filter" id="filter" onkeyup="filter_selects('add_edit_timeSheetEntry_activityID', this.value);"/>
                        </div>
                    </li>
                    <li>
                        <label for="description"><?php echo $this->kga['lang']['description'] ?>:</label>
                        <textarea tabindex="5" style="width:395px" cols='40' rows='5' name="description" id="description"><?php echo $this->escape($this->description) ?></textarea>
                    </li>

                    <li>
                        <label for="start_day"><?php echo $this->kga['lang']['day'] ?>:</label>
                        <input id='start_day' type='text' name='start_day' value='<?php echo $this->escape($this->start_day) ?>' maxlength='10' size='10' tabindex='6'
                               onChange="ts_timeToDuration();" <?php if ($this->kga['conf']['autoselection']): ?> onclick="this.select();" <?php endif; ?> />
                        -
                        <input id='end_day' type='text' name='end_day' value='<?php echo $this->escape($this->end_day) ?>' maxlength='10' size='10' tabindex='7'
                               onChange="ts_timeToDuration();" <?php if ($this->kga['conf']['autoselection']): ?> onclick="this.select();" <?php endif; ?> />
                    </li>
                    <li>
                        <label for="start_time"><?php echo $this->kga['lang']['timelabel'] ?>:</label>
                        <input id='start_time' type='text' name='start_time'
                               value='<?php echo $this->escape($this->start_time) ?>' maxlength='8' size='8' tabindex='8'
                               onChange="ts_timeToDuration();" <?php if ($this->kga['conf']['autoselection']): ?> onclick="this.select();" <?php endif; ?> />
                        -
                        <input id='end_time' type='text' name='end_time' value='<?php echo $this->escape($this->end_time) ?>' maxlength='8' size='8' tabindex='9'
                               onChange="ts_timeToDuration();" <?php if ($this->kga['conf']['autoselection']): ?> onclick="this.select();" <?php endif; ?> />
                        <a id="currentTime" href="#" onclick="pasteNow(); ts_timeToDuration(); $(this).blur(); return false;"><?php echo $this->kga['lang']['now'] ?></a>
                    </li>
                    <li>
                        <label for="duration"><?php echo $this->kga['lang']['durationlabel'] ?>:</label>
                        <input id='duration' type='text' name='duration' value='' onChange="ts_durationToTime();" maxlength='8' size='8'
                               tabindex='10' <?php if ($this->kga['conf']['autoselection']): ?> onclick="this.select();"<?php endif; ?> />
                    </li>
                </ul>
            </fieldset>
            <fieldset id="extended">
                <ul>
                    <li>
                        <label for="location"><?php echo $this->kga['lang']['location'] ?>:</label>
                        <input id='location' type='text' name='location' value='<?php echo $this->escape($this->location) ?>' maxlength='50' size='20'
                               tabindex='11' <?php if ($this->kga['conf']['autoselection']): ?> onclick="this.select();"<?php endif; ?> />
                    </li>
                    <?php if ($this->kga['show_TrackingNr']): ?>
                        <li>
                            <label for="trackingNumber"><?php echo $this->kga['lang']['trackingNumber'] ?>:</label>
                            <input id='trackingNumber' type='text' name='trackingNumber' value='<?php echo $this->escape($this->trackingNumber) ?>' maxlength='20' size='20'
                                   tabindex='12' <?php if ($this->kga['conf']['autoselection']): ?> onclick="this.select();"<?php endif; ?> />
                        </li>
                    <?php endif; ?>
                    <li>
                        <label for="comment"><?php echo $this->kga['lang']['comment'] ?>:</label>
                        <textarea id='comment' style="width:395px" class='comment' name='comment' cols='40' rows='5' tabindex='13'><?php echo $this->escape($this->comment) ?></textarea>
                    </li>
                    <li>
                        <label for="commentType"><?php echo $this->kga['lang']['commentType'] ?>:</label>
                        <?php echo $this->formSelect('commentType', $this->commentType, array(
                            'id' => 'commentType',
                            'class' => 'formfield',
                            'tabindex' => '14'), $this->commentTypes); ?>
                    </li>
                    <?php if (count($this->users) > 0): ?>
                        <li>
                            <label for="userID"><?php echo $this->kga['lang']['user'] ?>:</label>
                            <?php echo $this->formSelect(
                                isset($this->id) ? 'userID' : 'userID[]',
                                $this->userID,
                                array(
                                    'id' => 'userID',
                                    'class' => 'formfield',
                                    'multiple' => isset($this->id) ? '' : 'multiple',
                                    'tabindex' => '14'),
                                $this->users); ?>
                        </li>
                    <?php else: ?>
                        <input type="hidden" name="userID" value="<?php echo $this->kga['user']['userID']; ?>"/>
                    <?php endif; ?>
                    <li>
                        <label for="erase"><?php echo $this->kga['lang']['erase'] ?>:</label>
                        <input type='checkbox' id='erase' name='erase' tabindex='15'/>
                    </li>
                    <li>
                        <label for="cleared"><?php echo $this->kga['lang']['cleared'] ?>:</label>
                        <input type='checkbox' id='cleared' name='cleared' <?php if ($this->cleared): ?> checked="checked" <?php endif; ?> tabindex='16'/>
                    </li>
                </ul>
            </fieldset>
            <fieldset id="budget">
                <ul>
                    <li>
                        <label for="budget_val"><?php echo $this->kga['lang']['budget'] ?>:</label>
                        <input id='budget_val' type='text' name='budget' value='<?php echo $this->escape($this->budget) ?>' maxlength='50' size='20'
                               tabindex='11' <?php if ($this->kga['conf']['autoselection']): ?> onclick="this.select();"<?php endif; ?> />
                    </li>
                    <li>
                        <label for="approved"><?php echo $this->kga['lang']['approved'] ?>:</label>
                        <input id='approved' type='text' name='approved' value='<?php echo $this->escape($this->approved) ?>' maxlength='50' size='20'
                               tabindex='11' <?php if ($this->kga['conf']['autoselection']): ?> onclick="this.select();"<?php endif; ?> />
                    </li>
                    <li>
                        <label for="statusID"><?php echo $this->kga['lang']['status'] ?>:</label>
                        <?php echo $this->formSelect('statusID', $this->statusID, array(
                            'id' => 'statusID',
                            'class' => 'formfield',
                            'tabindex' => '15'), $this->status); ?>
                    </li>
                    <li>
                        <label for="billable"><?php echo $this->kga['lang']['billable'] ?>:</label>
                        <?php echo $this->formSelect('billable', $this->billable_active, array(
                            'id' => 'billable',
                            'class' => 'formfield',
                            'tabindex' => '16'), $this->billable); ?>
                    </li>
                    <?php if ($this->showRate): ?>
                        <li>
                            <label for="rate"><?php echo $this->kga['lang']['rate'] ?>:</label>
                            <input id='rate' type='text' name='rate' value='<?php echo $this->escape($this->rate) ?>' size='5' tabindex='10'/>
                            <label for="fixedRate" style="float: none; margin-left: 60px;"><?php echo $this->kga['lang']['fixedRate'] ?>:</label>
                            <input id='fixedRate' type='text' name='fixedRate' value='<?php echo $this->escape($this->fixedRate) ?>' size='5'
                                   tabindex='10' <?php if ($this->kga['conf']['autoselection']): ?> onclick="this.select();"<?php endif; ?> />
                        </li>
                    <?php endif; ?>
                    <li>
                        <table>
                            <tr>
                                <td align="right"><?php echo $this->kga['lang']['budget_activity'] ?>:</td>
                                <td><span id="budget_activity"><?php echo $this->budget_activity ?></span></td>
                            </tr>
                            <tr>
                                <td align="right"><?php echo $this->kga['lang']['budget_activity_used'] ?>:</td>
                                <td><span id="budget_activity_used"><?php echo $this->budget_activity_used ?></span></td>
                            </tr>
                            <tr>
                                <td align="right"><?php echo $this->kga['lang']['budget_activity_approved'] ?>:</td>
                                <td><span id="budget_activity_approved"><?php echo $this->approved_activity ?></span></td>
                            </tr>
                        </table>
                    </li>
                    <?php if (isset($this->id)) { ?>
                        <li>
                            <div id="chart"></div>
                        </li>
                    <?php } ?>
                </ul>
            </fieldset>
        </div>
        <div id="formbuttons">
            <input class='btn_norm' type='button' value='<?php echo $this->kga['lang']['cancel'] ?>' onclick='floaterClose();return false;'/>
            <input class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['submit'] ?>'/>
        </div>
    </form>
</div>
<script type="text/javascript">
    var previousBudget = $('#budget').val();
    var previousUsed = 0;
    var previousApproved = 0;
    $(document).ready(function () {
        $('#help').hide();
        $('#floater_innerwrap').tabs({selected: 0});

        // only save the value, the update will happen automatically because we trigger a changed
        // activity on "edit_out_time"
        $('#currentTime, #end_day, #start_day').click(function () {
            saveDuration();
        });

        $("#approved").focus(function () {
            previousApproved = this.value;
        }).change(function () {
            if (isNaN($(this).val()) || $(this).val() == '') {
                $(this).val(0);
            }
            $('#budget_activity_approved').text(parseFloat($('#budget_activity_approved').text()) - previousApproved + parseFloat($(this).val()));
            return false;
        });
        if ($('#roundTimesheetEntries').val().length > 0) {
            var step = $('#stepMinutes').val();
            var stepSeconds = $('#stepSeconds').val();
            if (isNaN(stepSeconds) || stepSeconds <= 0) {
                var configuration = {showPeriodLabels: false};
                if (!isNaN(step) && step > 0 && step < 60) {
                    configuration.step = parseInt(step);
                }

                $('#start_time').timepicker(configuration);
                $('#end_time').timepicker(configuration);
            }
        }

        // #rate already has an activity on click, so treat it below
        $("#eduration, #eend_time, #start_time").focus(function () {
            saveDuration();
        }).change(function () {
            updateDuration();
            generateChart();
            return false;
        });

        $('#add_edit_timeSheetEntry_activityID').change(function () {
            $.getJSON("../extensions/ki_timesheets/processor.php", {
                    axAction: "budgets",
                    project_id: $("#add_edit_timeSheetEntry_projectID").val(),
                    activity_id: $("#add_edit_timeSheetEntry_activityID").val(),
                    timeSheetEntryID: $('input[name="id"]').val()
                },
                function (data) {
                    ts_ext_updateBudget(data);
                }
            );
        });

        $("#budget").focus(function () {
            previousBudget = this.value;
        }).change(function () {
            $('#activityBudget').text(parseFloat($('#activityBudget').text()) - previousBudget + $(this).val());
            generateChart();
            return false;
        });

        $('#start_day').datepicker({
            onSelect: function (dateText, instance) {
                $('#end_day').datepicker("option", "minDate", $('#start_day').datepicker("getDate"));
                ts_timeToDuration();
            }
        });

        $('#end_day').datepicker({
            onSelect: function (dateText, instance) {
                $('#start_day').datepicker("option", "maxDate", $('#end_day').datepicker("getDate"));
                ts_timeToDuration();
            }
        });

        <?php if ($this->showRate): ?>
        $("#rate").click(function () {
            saveDuration();
            $("#rate").autocomplete("search", 0);
        });

        $("#rate").change(function () {
            updateDuration();
        });

        $("#rate").autocomplete({
            width: "200px",
            source: function (req, add) {
                $.getJSON("../extensions/ki_timesheets/processor.php", {
                        axAction: "allFittingRates",
                        project: $("#add_edit_timeSheetEntry_projectID").val(),
                        activity: $("#add_edit_timeSheetEntry_activityID").val()
                    },
                    function (data) {
                        if (data.errors.length != 0) {
                            return;
                        }
                        add(data.rates);
                    }
                );
            },
            select: function (activity, ui) {
                $("#rate").val(ui.item.value);

                return false;
            }
        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li></li>")
                .data("item.autocomplete", item)
                .append("<a>" + item.desc + "</a>")
                .appendTo(ul);
        };

        $("#fixedRate").click(function () {
            $("#fixedRate").autocomplete("search", 0);
        });

        $("#fixedRate").autocomplete({
            width: "200px",
            source: function (req, add) {
                $.getJSON("../extensions/ki_timesheets/processor.php", {
                        axAction: "allFittingFixedRates",
                        project: $("#add_edit_timeSheetEntry_projectID").val(),
                        activity: $("#add_edit_timeSheetEntry_activityID").val()
                    },
                    function (data) {
                        if (data.errors.length != 0) {
                            return;
                        }
                        add(data.rates);
                    }
                );
            },
            select: function (activity, ui) {
                $("#fixedRate").val(ui.item.value);

                return false;
            }
        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li></li>")
                .data("item.autocomplete", item)
                .append("<a>" + item.desc + "</a>")
                .appendTo(ul);
        };
        <?php endif; ?>

        $('#ts_ext_form_add_edit_timeSheetEntry').ajaxForm({
            'beforeSubmit': function () {
                clearFloaterErrorMessages();
                if (!$('#start_day').val().match(ts_dayFormatExp) ||
                    ( !$('#end_day').val().match(ts_dayFormatExp) && $('#end_day').val() != '') || !$('#start_time').val().match(ts_timeFormatExp) ||
                    ( !$('#end_time').val().match(ts_timeFormatExp) && $('#end_time').val() != '')) {
                    alert("<?php echo $this->kga['lang']['TimeDateInputError']?>");
                    return false;
                }

                var endTimeSet = $('#end_day').val() != '' || $('#end_time').val() != '';

                if (!endTimeSet) {
                    return true;
                } // no need to validate timerange if end time is not set

                // test if start day is before end day
                var inDayMatches = $('#start_day').val().match(ts_dayFormatExp);
                var outDayMatches = $('#end_day').val().match(ts_dayFormatExp);
                for (var i = 3; i >= 1; i--) {
                    var inVal = inDayMatches[i];
                    var outVal = outDayMatches[i];

                    inVal = parseInt(inVal);
                    outval = parseInt(outVal);

                    if (inVal == undefined) {
                        inVal = 0;
                    }
                    if (outVal == undefined) {
                        outVal = 0;
                    }

                    if (inVal > outVal) {
                        alert("<?php $this->kga['lang']['StartTimeBeforeEndTime']?>");
                        return false;
                    }
                    else if (inVal < outVal) {
                        break;
                    } // if this part is smaller we don't care for the other parts
                }
                if (inDayMatches[0] == outDayMatches[0]) {
                    // test if start time is before end time if it's the same day
                    var inTimeMatches = $('#start_time').val().match(ts_timeFormatExp);
                    var outTimeMatches = $('#end_time').val().match(ts_timeFormatExp);
                    for (var i = 1; i <= 3; i++) {
                        var inVal = inTimeMatches[i];
                        var outVal = outTimeMatches[i];

                        if (inVal[0] == ":") {
                            inVal = inVal.substr(1);
                        }
                        if (outVal[0] == ":") {
                            outVal = outVal.substr(1);
                        }

                        inVal = parseInt(inVal);
                        outval = parseInt(outVal);

                        if (inVal == undefined) {
                            inVal = 0;
                        }
                        if (outVal == undefined) {
                            outVal = 0;
                        }

                        if (inVal > outVal) {
                            alert("<?php echo $this->kga['lang']['StartTimeBeforeEndTime']?>");
                            return false;
                        }
                        else if (inVal < outVal) {
                            break;
                        } // if this part is smaller we don't care for the other parts
                    }
                }

                var edit_in_time = $('#start_day').val() + $('#start_time').val();
                var edit_out_time = $('#end_day').val() + $('#end_time').val();
                var deleted = $('#erase').is(':checked');


                if ($('#ts_ext_form_add_edit_timeSheetEntry').attr('submitting')) {
                    return false;
                }
                else {
                    $('#ts_ext_form_add_edit_timeSheetEntry').attr('submitting', true);
                    return true;
                }
            },
            'success': function (result) {
                $('#ts_ext_form_add_edit_timeSheetEntry').removeAttr('submitting');
                for (var fieldName in result.errors) {
                    setFloaterErrorMessage(fieldName, result.errors[fieldName]);
                }

                if (result.errors.length == 0) {
                    floaterClose();
                    ts_ext_reload();
                }
            },

            'error': function () {
                $('#ts_ext_form_add_edit_timeSheetEntry').removeAttr('submitting');
            }
        });
        <?php if (isset($this->id)) { ?>
        ts_ext_reload_activities(<?php echo $this->projectID?>, true);
        <?php } else { ?>
        $("#add_edit_timeSheetEntry_projectID").val(selected_project);
        $("#add_edit_timeSheetEntry_activityID").val(selected_activity);
        ts_ext_reload_activities(selected_project);
        <?php } ?>
        
        ts_timeToDuration();
        // ts_timeToDuration will set the value of duration. The first time, the value
        // will be set and the duration is added to the budgetUsed eventhough it shouldn't
        // so maually subtract the value again
        var durationArray = new Array();
        durationArray = $("#duration").val().split(/:|\./);
        if (durationArray.length > 0 && durationArray.length < 4) {
            secs = durationArray[0] * 3600;
            if (durationArray.length > 1) {
                secs += (durationArray[1] * 60);
            }
            if (durationArray.length > 2) {
                secs += parseInt(durationArray[2]);
            }
            <?php if ($this->showRate): ?>
            var rate = $('#rate').val();
            <?php else: ?>
            var rate = 0;
            <?php endif; ?>
            var budgetCalculatedTwice = secs / 3600 * rate;
            $('#budget_activity_used').text(Math.round(parseFloat($('#budget_activity_used').text()) - budgetCalculatedTwice), 2);
        }
        <?php if (isset($this->id)) { ?>
        //TODO: chart will not be generated..WHY??
        //generateChart();
        <?php } ?>
    });
    // document ready

    function saveDuration() {
        var durationArray = $("#duration").val().split(/:|\./);
        var secs = 0;
        if (durationArray.length > 0 && durationArray.length < 4) {
            secs = durationArray[0] * 3600;
            if (durationArray.length > 1) {
                secs += (durationArray[1] * 60);
            }
            if (durationArray.length > 2) {
                secs += parseInt(durationArray[2]);
            }
        }
        <?php if ($this->showRate): ?>
        var rate = $('#rate').val();
        <?php else: ?>
        var rate = 0;
        <?php endif; ?>
        previousUsed = secs / 3600 * rate;
    }

    function updateDuration() {
        var durationArray = $("#duration").val().split(/:|\./);
        var secs = 0;
        if (durationArray.length > 0 && durationArray.length < 4) {
            secs = durationArray[0] * 3600;
            if (durationArray.length > 1) {
                secs += (durationArray[1] * 60);
            }
            if (durationArray.length > 2) {
                secs += parseInt(durationArray[2]);
            }
        }
        <?php if ($this->showRate): ?>
        var rate = $('#rate').val();
        <?php else: ?>
        var rate = 0;
        <?php endif; ?>
        var used = secs / 3600 * rate;
        $('#budget_activity_used').text(Math.round(parseFloat($('#budget_activity_used').text()) - previousUsed + used), 2);
    }
    function generateChart() {
        var durationArray = $("#duration").val().split(/:|\./);
        var secs = 0;
        if (durationArray.length > 0 && durationArray.length < 4) {
            secs = durationArray[0] * 3600;
            if (durationArray.length > 1) {
                secs += (durationArray[1] * 60);
            }
            if (durationArray.length > 2) {
                secs += parseInt(durationArray[2]);
            }
        }
        <?php if ($this->showRate): ?>
        var rate = $('#rate').val();
        <?php else: ?>
        var rate = 0;
        <?php endif; ?>
        var budget = $('#budget_val').val();
        var used = secs / 3600 * rate;
        var usedString = '<?php echo $this->kga['lang']['used']?>';
        var budgetString = '<?php echo $this->kga['lang']['budget_available']?>';
        var chartdata = [[usedString, used], [budgetString, budget - used]];

        try {
            $.jqplot('chart', [chartdata], {
                seriesDefaults: {
                    renderer: $.jqplot.PieRenderer,
                    rendererOptions: {
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
                grid: {background: 'white', borderWidth: 0, shadow: false}
            });
        }
        catch (err) {
            // probably no data, so remove the chart
            $('#chart').remove();
        }
    }
</script>