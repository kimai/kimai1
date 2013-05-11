<?php

$latest_running_row_index = -1;

if ($this->timeSheetEntries)
{
    ?>
        <div id="timeSheetTable">
        
          <table>
              
            <colgroup>
              <col class="option" />
              <col class="date" />
              <col class="from" />
              <col class="to" />
              <col class="time" />
<?php if ($this->showRates): ?>
              <col class="wage" />
<?php endif; ?>
              <col class="client" />
              <col class="project" />
              <col class="activity" />
            <?php if ($this->showTrackingNumber) { ?>
              <col class="trackingnumber" />
            <?php } ?>
              <col class="username" />
            </colgroup>

            <tbody>

    <?php
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

        <?php if ($this->kga['user']): // only users can see options ?>

            <?php if ($row['end']): // Stop oder Record Button? ?>

            <?php if ($this->kga['show_RecordAgain']): ?>
              <a onClick="ts_ext_recordAgain(<?php echo $row['projectID']?>,<?php echo $row['activityID']?>,<?php echo $row['timeEntryID']?>); return false;"
                 href ="#" class="recordAgain"><img src='../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/button_recordthis.gif'
                 width='13' height='13' alt='<?php echo $this->kga['lang']['recordAgain']?>' title='<?php echo $this->kga['lang']['recordAgain']?> (ID:<?php echo $row['timeEntryID']?>)' border='0' /></a>
            <?php endif; ?>

        <?php else: ?>

            <a href ='#' class='stop' onClick="ts_ext_stopRecord(<?php echo $row['timeEntryID']?>); return false;"><img
                    src='../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/button_stopthis.gif' width='13'
                    height='13' alt='<?php echo $this->kga['lang']['stop']?>' title='<?php echo $this->kga['lang']['stop']?> (ID:<?php echo $row['timeEntryID']?>)' border='0' /></a>

        <?php endif; ?>


      <?php if ($this->kga['conf']['editLimit'] == "-" || time()-$row['end'] <= $this->kga['conf']['editLimit']):
    //Edit Record Button ?>
        <a href ='#' onClick="editRecord(<?php echo $row['timeEntryID']?>); $(this).blur(); return false;"
           title='<?php echo $this->kga['lang']['edit']?>'><img
           src='../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/edit2.gif' width='13' height='13'
           alt='<?php echo $this->kga['lang']['edit']?>' title='<?php echo $this->kga['lang']['edit']?>' border='0' /></a>
      <?php endif; ?>

      <?php if ($this->kga['conf']['quickdelete'] > 0):
    // quick erase trashcan  ?>
        <a href ='#' class='quickdelete' onClick="quickdelete(<?php echo $row['timeEntryID']?>); return false;"><img
            src='../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/button_trashcan.png' width='13'
            height='13' alt='<?php echo $this->kga['lang']['quickdelete']?>' title='<?php echo $this->kga['lang']['quickdelete']?>'
            border=0 /></a>
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
    ?>

                </tbody>
            </table>
        </div>
    <?php
}
else
{
    echo $this->error();
}
?>

<script type="text/javascript"> 
    ts_user_annotations = null;
    ts_customer_annotations = null;
    ts_project_annotations = null;
    ts_activity_annotations = null;
    ts_total = '<?php echo $this->total?>';

    <?php if ($this->user_annotations): ?>
    ts_user_annotations = new Array();
    <?php foreach ($this->user_annotations as $id => $value): ?>
      ts_user_annotations[<?php echo $id?>] = '<?php echo $value?>';
    <?php endforeach; endif; ?>

    <?php if ($this->customer_annotations): ?>
    ts_customer_annotations = new Array();
    <?php foreach ($this->customer_annotations as $id => $value): ?>
      ts_customer_annotations[<?php echo $id?>] = '<?php echo $value?>';
    <?php endforeach; endif; ?>

    <?php if ($this->project_annotations): ?>
    ts_project_annotations = new Array();
    <?php foreach ($this->project_annotations as $id => $value): ?>
      ts_project_annotations[<?php echo $id?>] = '<?php echo $value?>';
    <?php endforeach; endif; ?>

    <?php if ($this->activity_annotations): ?>
    ts_activity_annotations = new Array();
    <?php foreach ($this->activity_annotations as $id => $value): ?>
      ts_activity_annotations[<?php echo $id?>] = '<?php echo $value?>';
    <?php endforeach; endif; ?>
    
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