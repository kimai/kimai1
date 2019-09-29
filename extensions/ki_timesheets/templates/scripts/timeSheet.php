<?php

$dateFormat = $this->kga->getDateFormat(1);

if ($this->showBillability) {
    $billableValues = $this->kga['billable'];
    $billableText = [];
    foreach ($billableValues as $billableValue) {
        $billableText[] = $billableValue . '%';
    }
    $this->billable = array_combine($billableValues, $billableText);
}

if ($this->timeSheetEntries) {
    ?>
    <div id="timeSheetTable">
        <table>
            <colgroup>
                <col class="option" />
                <col class="date" />
                <col class="from" />
                <col class="to" />
                <col class="time" />
                <?php if ($this->showBillability): ?>
                    <col class="billable"/>
                <?php endif; ?>
                <?php if ($this->showRates): ?>
                    <col class="wage"/>
                <?php endif; ?>
                <col class="client" />
                <col class="project" />
                <col class="activity" />
                <col class="description" />
                <?php if ($this->showTrackingNumber): ?>
                    <col class="trackingnumber"/>
                <?php endif; ?>
                <col class="username" />
            </colgroup>
            <tbody>
            <?php
            $day_buffer = 0; // last day entry
            $time_buffer = 0; // last time entry
            $end_buffer = 0; // last time entry
            $ts_buffer = 0; // current time entry

            foreach ($this->timeSheetEntries as $rowIndex => $row) {
                //Assign initial value to time buffer which must be larger than or equal to "end"
                if ($time_buffer == 0) {
                    $time_buffer = $row['end'];
                }

                if ($end_buffer == 0) {
                    $end_buffer = $row['end'];
                }

                $start = strftime("%d", $row['start']);
                $end = (isset($row['end']) && $row['end']) ? $row['end'] : 0;
                $ts_buffer = strftime("%H%M", $end);

                $tdClass = '';

                if ($this->showOverlapLines && $end > $time_buffer) {
                    $tdClass = " time_overlap";
                } elseif ($this->kga->isShowDaySeperatorLines() && $start != $day_buffer) {
                    $tdClass = " break_day";
                } elseif ($this->kga->isShowGabBreaks() && (strftime("%H%M", $time_buffer) - strftime("%H%M", $row['end']) > 1)) {
                    $tdClass = " break_gap";
                } ?>
                <?php if ($row['end']): ?>
                    <tr id="timeSheetEntry<?php echo $row['timeEntryID']?>" class="<?php echo $this->cycle(["odd", "even"])->next()?>">
                <?php else: ?>
                    <tr id="timeSheetEntry<?php echo $row['timeEntryID']?>" class="<?php echo $this->cycle(["odd", "even"])->next()?> active">
                <?php endif; ?>
                <td nowrap class="option <?php echo $tdClass; ?>">
                    <?php if (isset($this->kga['user'])): // only users can see options ?>
                        <?php if ($row['end']): // Stop oder Record Button? ?>
                            <?php if ($this->kga->isShowRecordAgain()): ?>
                            <a onclick="ts_ext_recordAgain(<?php echo $row['projectID']?>,<?php echo $row['activityID']?>,<?php echo $row['timeEntryID']?>); return false;"
                               href="#" class="recordAgain"><img src="<?php echo $this->skin('grfx/button_recordthis.gif'); ?>"
                                                                 width="13" height="13" alt='<?php echo $this->translate('recordAgain')?>' title="<?php echo $this->translate('recordAgain')?> (ID:<?php echo $row['timeEntryID']?>)" border="0" /></a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="#" class="stop" onclick="ts_ext_stopRecord(<?php echo $row['timeEntryID']?>); return false;"><img
                                        src="<?php echo $this->skin('grfx/button_stopthis.gif'); ?>" width="13"
                                        height="13" alt='<?php echo $this->translate('stop')?>' title="<?php echo $this->translate('stop')?> (ID: <?php echo $row['timeEntryID']?>)" border="0" /></a>
                        <?php endif; ?>
                        <?php if ($this->entryCanBeEdited($row)): ?>
                            <a href="#" onclick="editRecord(<?php echo $row['timeEntryID']?>); $(this).blur(); return false;"
                               title="<?php echo $this->translate('edit')?>"><img
                                        src="<?php echo $this->skin('grfx/edit2.gif'); ?>" width="13" height="13"
                                        alt="<?php echo $this->translate('edit')?>" title="<?php echo $this->translate('edit')?>" border="0" /></a>
                            <?php if ($this->kga->getSettings()->isShowQuickNote()): ?>
                                <a href="#" onclick="editQuickNote(<?php echo $row['timeEntryID']?>); $(this).blur(); return false;"
                                   title='<?php echo $this->translate('editNote')?>'><img
                                            src="<?php echo $this->skin('grfx/editor_icon.png'); ?>" width="14" height="14"
                                            alt="<?php echo $this->translate('editNote')?>" title="<?php echo $this->translate('editNote')?>" border="0" /></a>
                            <?php endif; ?>
                            <?php if ($this->kga->getSettings()->isShowQuickDelete()): ?>
                                <a href="#" class="quickdelete" onclick="quickdelete(<?php echo $row['timeEntryID']?>); return false;"><img
                                            src="<?php echo $this->skin('grfx/button_trashcan.png'); ?>" width="13"
                                            height="13" alt="<?php echo $this->translate('quickdelete')?>" title="<?php echo $this->translate('quickdelete')?>"
                                            border="0" /></a>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td class="date <?php echo $tdClass; ?>"><?php
	                echo $this->escape(strftime($dateFormat, $row['start']));
	                ?></td>
                <td class="from <?php echo $tdClass; ?>"><?php
	                echo $this->escape(strftime("%H:%M", $row['start']));
	                ?></td>
                <td class="to <?php echo $tdClass; ?>"><?php
                    if ($row['end']) {
                        echo $this->escape(strftime("%H:%M", $row['end']));
                    } else {
                        echo "&ndash;&ndash;:&ndash;&ndash;";
                    }
                    ?></td>
                <td class="time <?php echo $tdClass; ?>"><?php
                    if (isset($row['duration'])) {
                        echo $row['formattedDuration'];
                    } else {
                        echo "&ndash;:&ndash;&ndash;";
                    }
                    ?></td>
                <?php if ($this->showBillability): ?>
                    <td class="billable <?php echo $tdClass; ?>"><?php
                        if (isset($row['billable'])) {
                            if (isset($row['duration'])) {
                                // billability dropdown
                                echo $this->formSelect(
                                    'billable',
                                    $row['billable'],
                                    [
                                        'id' => 'billable_' . $row['timeEntryID'],
                                        'class' => 'formfield',
                                        'onchange' => 'ts_updateBillability(' . $row['timeEntryID'] . ')'
                                    ],
                                    $this->billable
                                );

                                // effective billable time
                                echo "&nbsp;(";
                                if ($row['billable'] == 100) {
                                    echo $row['formattedDuration'];
                                } else {
                                    if ($row['billable'] >= 0) {
                                        $billableMinutes = ($row['duration'] / 60) * ($row['billable'] / 100);
                                        $hours = ceil(floor($billableMinutes / 60 / 24)) + ceil(floor($billableMinutes / 60));
                                        $minutes = ceil($billableMinutes - floor($billableMinutes / 60) * 60);
                                        echo $hours . ":" . sprintf('%02d', $minutes);
                                    } else {
                                        echo "&ndash;:&ndash;&ndash;";
                                    }
                                }
                                echo ')';
                            }
                        } else {
                            if (isset($row['duration'])) {
                                echo $row['formattedDuration'];
                            } else {
                                echo "&ndash;:&ndash;&ndash;";
                            }
                        }
                        ?></td>
                <?php endif; ?>
                <?php if ($this->showRates): ?>
                    <td class="wage <?php echo $tdClass; ?>"><?php
                        if (isset($row['wage'])) {
                            echo $this->escape(str_replace('.', $this->kga['conf']['decimalSeparator'], $row['wage']));
                        } else {
                            echo "&ndash;";
                        }
                        ?></td>
                <?php endif; ?>
                <td class="customer <?php echo $tdClass; ?>"><?php
	                echo $this->escape($row['customerName']);
	                ?></td>
                <td class="project <?php echo $tdClass; ?>">
                    <a href="#" class="preselect_lnk"
                       onclick="buzzer_preselect_project(<?php echo $row['projectID']?>,'<?php echo $this->jsEscape($row['projectName']); ?>',<?php echo $this->jsEscape($row['customerID'])?>,'<?php echo $this->jsEscape($row['customerName'])?>');
                               return false;">
                        <?php echo $this->escape($row['projectName'])?>
                        <?php if ($this->kga->getSettings()->isShowProjectComment() && $row['projectComment']): ?>
                            <span class="lighter">(<?php echo $this->escape($row['projectComment']); ?>)</span>
                        <?php endif; ?>
                    </a>
                </td>
                <td class="activity <?php echo $tdClass; ?>">
                    <a href="#" class="preselect_lnk"
                       onclick="buzzer_preselect_activity(<?php echo $row['activityID']?>,'<?php echo $this->jsEscape($row['activityName'])?>',0,0);
                               return false;">
                        <?php echo $this->escape($row['activityName']); ?>
                    </a>
                    <?php if ($row['comment']): ?>
                        <?php if ($row['commentType'] == '0'): ?>
                            <a href="#" onclick="ts_comment(<?php echo $row['timeEntryID']?>); $(this).blur(); return false;"><img src="<?php echo $this->skin('grfx/blase.gif'); ?>" width="12" height="13" title='<?php echo $this->escape($row['comment'])?>' border="0" /></a>
                        <?php elseif ($row['commentType'] == '1'): ?>
                            <a href="#" onclick="ts_comment(<?php echo $row['timeEntryID']?>); $(this).blur(); return false;"><img src="<?php echo $this->skin('grfx/blase_sys.gif'); ?>" width="12" height="13" title='<?php echo $this->escape($row['comment'])?>' border="0" /></a>
                        <?php elseif ($row['commentType'] == '2'): ?>
                            <a href="#" onclick="ts_comment(<?php echo $row['timeEntryID']?>); $(this).blur(); return false;"><img src="<?php echo $this->skin('grfx/blase_caution.gif'); ?>" width="12" height="13" title='<?php echo $this->escape($row['comment'])?>' border="0" /></a>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td class="description <?php echo $tdClass; ?>"><?php
	                if ($this->inlineEditingOfDescriptions): ?>
                        <textarea rows="1" style="width: 100%; resize:none" id="description_<?php echo $row['timeEntryID']; ?>" onfocus="$(this).attr('rows',3);" onfocusout="$(this).attr('rows',1);" onchange="ts_updateDescription(<?php echo $row['timeEntryID']; ?>, 0)"><?php echo htmlspecialchars($row['description']); ?></textarea>
                    <?php else: ?>
                        <?php echo $this->escape($this->truncate($row['description'], 50, '...')) ?>
                        <?php if ($row['description']): ?>
                            <a href="#" onclick="$(this).blur(); return false;" ><img src="<?php echo $this->skin('grfx/blase_sys.gif'); ?>" width="12" height="13" title='<?php echo $this->escape($row['description'])?>' border="0" /></a>
                        <?php endif; ?>
                    <?php endif;
                    ?></td>
                <?php if ($this->showTrackingNumber): ?>
                    <td class="trackingnumber <?php echo $tdClass; ?>"><?php
	                    echo $this->escape($row['trackingNumber']);
	                    ?></td>
                <?php endif; ?>
                <td class="username <?php echo $tdClass; ?>">
                    <?php if ($row['userAlias']): ?>
                        <?php echo $this->escape($row['userAlias']) . ' (' . $this->escape($row['userName']) . ')'; ?>
                    <?php else: ?>
                        <?php echo $this->escape($row['userName']); ?>
                    <?php endif; ?>
                </td>
                </tr>
                <?php if ($row['comment']): ?>
                    <tr id="c<?php echo $row['timeEntryID']?>" class="comm<?php echo $this->escape($row['commentType'])?>" <?php if ($this->hideComments): ?> style="display:none" <?php endif; ?> >
                        <td colspan="11"><?php echo nl2br($this->escape($row['comment']))?></td>
                    </tr>
                <?php endif; ?>
                <?php
                $day_buffer = strftime("%d", $row['start']);
                $time_buffer = $row['start'];
                $end_buffer = $row['end'];
            } ?>
            </tbody>
        </table>
    </div>
    <?php
} else {
    echo $this->error();
}
?>
<script type="text/javascript">
    ts_user_annotations = <?php echo json_encode($this->user_annotations); ?>;
    ts_customer_annotations = <?php echo json_encode($this->customer_annotations) ?>;
    ts_project_annotations = <?php echo json_encode($this->project_annotations) ?>;
    ts_activity_annotations = <?php echo json_encode($this->activity_annotations) ?>;
    ts_total = '<?php echo $this->total?>';

    lists_update_annotations(parseInt($('#gui div.ki_timesheet').attr('id').substring(7)),ts_user_annotations,ts_customer_annotations,ts_project_annotations,ts_activity_annotations);
    $('#display_total').html(ts_total);

    <?php if ($this->latest_running_entry == null): ?>
    updateRecordStatus(<?php echo time(); ?>, false);
    <?php else: ?>
    updateRecordStatus(
        <?php echo time(); ?>,
        <?php echo $this->latest_running_entry['timeEntryID']?>,
        <?php echo $this->latest_running_entry['start']?>,
        <?php echo $this->latest_running_entry['customerID']?>,
        '<?php echo $this->jsEscape($this->latest_running_entry['customerName'])?>',
        <?php echo $this->latest_running_entry['projectID']?>,
        '<?php echo $this->jsEscape($this->latest_running_entry['projectName'])?>',
        <?php echo $this->latest_running_entry['activityID']?>,
        '<?php echo $this->jsEscape($this->latest_running_entry['activityName'])?>'
    );
    <?php endif; ?>
</script>
