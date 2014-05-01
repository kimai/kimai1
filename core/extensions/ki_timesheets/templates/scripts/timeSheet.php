<?php

$latest_running_row_index = -1;

if ($this->timeSheetEntries)
{
    // --------------------- prepare timesheet table header ---------------------
    $colgroup = array(
        'option' => '&nbsp;',
        'date' => $this->kga['lang']['datum'],
        'from' => $this->kga['lang']['in'],
        'to' => $this->kga['lang']['out'],
        'time' => $this->kga['lang']['time']
    );

    if ($this->showRates) {
        $colgroup['wage'] = $this->kga['lang']['wage'];
    }

    $colgroup['customer'] = $this->kga['lang']['customer'];
    $colgroup['project'] = $this->kga['lang']['project'];
    $colgroup['activity'] = $this->kga['lang']['activity'];

    if ($this->showTrackingNumber) {
        $colgroup['trackingnumber'] = $this->kga['lang']['trackingNumber'];
    }
    $colgroup['username'] = $this->kga['lang']['username'];

    // attention - same config is in timeSheet.php as well !!!!
    $dataTable = array(
        'header_id'     => 'timeSheet_head',
        'colgroup'      => $colgroup,
        'data_id'       => 'timeSheet'
    );

    echo $this->dataTable($dataTable)->renderDataHeader();

    // --------------------- start calculating all timesheet entry rows ---------------------
    $day_buffer     = 0; // last day entry
    $time_buffer    = 0; // last time entry
    $end_buffer     = 0; // last time entry
    $ts_buffer      = 0; // current time entry

    foreach ($this->timeSheetEntries as $rowIndex => $row)
    {
        //Assign initial value to time buffer which must be larger than or equal to "end"
        if ($time_buffer == 0) {
            $time_buffer = $row['end'];
        }

        if ($end_buffer == 0) {
            $end_buffer = $row['end'];
        }

        $start          = strftime("%d",$row['start']);
        $end            = (isset($row['end']) && $row['end']) ? $row['end'] : 0;
        $ts_buffer      = strftime("%H%M",$end);

        $tdClass = "";
        if ($this->showOverlapLines && $end > $time_buffer) {
            $tdClass = " time_overlap";
        } elseif ($this->kga['show_daySeperatorLines'] && $start != $day_buffer) {
            $tdClass = " break_day";
        } elseif ($this->kga['show_gabBreaks'] && (strftime("%H%M",$time_buffer) - strftime("%H%M",$row['end']) > 1)) {
            $tdClass = " break_gap";
        }
        /*
        if ($row['end'] > $time_buffer                      && $this->showOverlapLines)              echo "time_overlap";
        elseif (strftime("%d",$row['start']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day";
        elseif ($row['end'] != $start_buffer                && $this->kga['show_gabBreaks'])         echo "break_gap";
        */

        ?>

        <?php if ($row['end']): ?>
            <tr id="timeSheetEntry<?php echo $row['timeEntryID']?>" class="<?php echo $this->cycle(array("odd","even"))->next()?>">
        <?php else: ?>
            <?php if ($latest_running_row_index == -1) { $latest_running_row_index = $rowIndex; } ?>
            <tr id="timeSheetEntry<?php echo $row['timeEntryID']?>" class="<?php echo $this->cycle(array("odd","even"))->next()?> active">
        <?php endif; ?>

        <td nowrap class="option <?php echo $tdClass; ?>">

        <?php if (isset($this->kga['user'])): // only users can see options ?>

            <?php if ($row['end']): // Stop oder Record Button? ?>

            <?php if ($this->kga['show_RecordAgain']): ?>
              <a onClick="ts_ext_recordAgain(<?php echo $row['projectID']?>,<?php echo $row['activityID']?>,<?php echo $row['timeEntryID']?>); return false;"
                 href ="#" class="recordAgain">
                  <?php echo $this->icons('recordAgain', array('title' => $this->kga['lang']['recordAgain'] . ' (ID:'.$row['timeEntryID'].')')); ?></a>
            <?php endif; ?>

        <?php else: ?>

            <a href ='#' class='stop' onClick="ts_ext_stopRecord(<?php echo $row['timeEntryID']?>); return false;">
                <?php echo $this->icons('stop', array('title' => $this->kga['lang']['stop'] . ' (ID:'.$row['timeEntryID'].')')); ?></a>

        <?php endif; ?>


      <?php if ($this->kga['conf']['editLimit'] == "-" || time()-$row['end'] <= $this->kga['conf']['editLimit']):
    //Edit Record Button ?>
        <a href ='#' onClick="editRecord(<?php echo $row['timeEntryID']?>); $(this).blur(); return false;"
           title='<?php echo $this->kga['lang']['edit']?>'>
            <?php echo $this->icons('edit'); ?></a>
      <?php endif; ?>

      <?php if ($this->kga['conf']['quickdelete'] > 0):
    // quick erase trashcan  ?>
        <a href ='#' class='quickdelete' onClick="quickdelete(<?php echo $row['timeEntryID']?>); return false;">
            <?php echo $this->icons('quickdelete'); ?></a>
      <?php endif; ?>

    <?php endif; ?>

            </td>

            <td class="date <?php echo $tdClass; ?>">
                <?php echo $this->escape(strftime($this->kga['date_format'][1],$row['start']));?>
            </td>

            <td class="from <?php echo $tdClass; ?>">
                <?php echo $this->escape(strftime("%H:%M",$row['start']));?>
            </td>

            <td class="to <?php echo $tdClass; ?>">
            <?php
                if ($row['end']) {
                    echo $this->escape(strftime("%H:%M",$row['end']));
                } else {
                    echo "&ndash;&ndash;:&ndash;&ndash;";
                }
            ?>
            </td>

            <td class="time <?php echo $tdClass; ?>">
                <?php
                if (isset($row['duration'])) {
                    echo $row['formattedDuration'];
                } else {
                    echo "&ndash;:&ndash;&ndash;";
                }
                ?>
            </td>

            <?php if ($this->showRates): ?>
            <td class="wage <?php echo $tdClass; ?> ">
            <?php
                if (isset($row['wage'])) {
                    echo $this->escape(str_replace('.',$this->kga['conf']['decimalSeparator'], $row['wage']));
                } else {
                    echo "&ndash;";
                }
            ?>
            </td>
            <?php endif; ?>

            <td class="customer <?php echo $tdClass; ?>">
                <?php echo $this->escape($row['customerName']) ?>
            </td>

            <td class="project <?php echo $tdClass; ?>">
                <a href ="#" class="preselect_lnk"
                    onClick="buzzer_preselect_project(<?php echo $row['projectID']?>,'<?php echo $this->jsEscape($row['projectName'])?>',<?php echo $this->jsEscape($row['customerID'])?>,'<?php echo $this->jsEscape($row['customerName'])?>');
                    return false;">
                    <?php echo $this->escape($row['projectName'])?>
                    <?php if ($this->kga['conf']['project_comment_flag'] == 1 && $row['projectComment']): ?>
                        <span class="lighter">(<?php echo $this->escape($row['projectComment'])?>)</span>
                    <?php endif; ?>
                </a>
            </td>

            <td class="activity <?php echo $tdClass; ?>">
                <a href ="#" class="preselect_lnk"
                    onClick="buzzer_preselect_activity(<?php echo $row['activityID']?>,'<?php echo $this->jsEscape($row['activityName'])?>',0,0);
                    return false;">
                    <?php echo $this->escape($row['activityName'])?>
                </a>

                <?php if ($row['comment']): ?>
                    <?php if ($row['commentType'] == '0'): ?>
                                        <a href="#" onClick="ts_comment(<?php echo $row['timeEntryID']?>); $(this).blur(); return false;"><img src='../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/blase.gif' width="12" height="13" title='<?php echo $this->escape($row['comment'])?>' border="0" /></a>
                    <?php elseif ($row['commentType'] == '1'): ?>
                                        <a href="#" onClick="ts_comment(<?php echo $row['timeEntryID']?>); $(this).blur(); return false;"><img src='../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/blase_sys.gif' width="12" height="13" title='<?php echo $this->escape($row['comment'])?>' border="0" /></a>
                    <?php elseif ($row['commentType'] == '2'): ?>
                                        <a href="#" onClick="ts_comment(<?php echo $row['timeEntryID']?>); $(this).blur(); return false;"><img src='../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/blase_caution.gif' width="12" height="13" title='<?php echo $this->escape($row['comment'])?>' border="0" /></a>
                    <?php endif; ?>
                <?php endif; ?>
            </td>

            <?php if ($this->showTrackingNumber) { ?>
            <td class="trackingnumber <?php echo $tdClass; ?>">
                <?php echo $this->escape($row['trackingNumber']) ?>
            </td>
            <?php } ?>

            <td class="username <?php echo $tdClass; ?>">
                <?php echo $this->escape($row['userName']) ?>
            </td>

        </tr>

        <?php if ($row['comment']): ?>
            <tr id="c<?php echo $row['timeEntryID']?>" class="comm<?php echo $this->escape($row['commentType'])?>" <?php if ($this->hideComments): ?> style="display:none" <?php endif; ?> >
                        <td colspan="11"><?php echo nl2br($this->escape($row['comment']))?></td>
            </tr>
        <?php endif; ?>

                <?php
                $day_buffer = strftime("%d",$row['start']);
                $time_buffer = $row['start'];
                $end_buffer = $row['end'];
    }

    echo $this->dataTable()->renderDataFooter();

}
else
{
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
    
  <?php if ($latest_running_row_index == -1): ?>
    updateRecordStatus(false);
  <?php else: ?>

    updateRecordStatus(<?php echo $this->timeSheetEntries[$latest_running_row_index]['timeEntryID']?>,<?php echo $this->timeSheetEntries[$latest_running_row_index]['start']?>,
                             <?php echo $this->timeSheetEntries[$latest_running_row_index]['customerID']?>,'<?php echo $this->jsEscape($this->timeSheetEntries[$latest_running_row_index]['customerName'])?>',
                             <?php echo $this->timeSheetEntries[$latest_running_row_index]['projectID']?> ,'<?php echo $this->jsEscape($this->timeSheetEntries[$latest_running_row_index]['projectName'])?>',
                             <?php echo $this->timeSheetEntries[$latest_running_row_index]['activityID']?>,'<?php echo $this->jsEscape($this->timeSheetEntries[$latest_running_row_index]['activityName'])?>');
  <?php endif; ?>

    function timesheet_hide_column(name) {
        $('.'+name).hide();
    }

    <?php if (!$this->showTrackingNumber) { ?>
        timesheet_hide_column('trackingnumber');
    <?php } ?>

</script>
