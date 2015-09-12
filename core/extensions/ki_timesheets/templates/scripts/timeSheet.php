<?php

if ($this->timeSheetEntries)
{
    ?>
        <div id="timeSheetTable">
        
          <table>
              
            <colgroup>
<?php
if($this->visibleColumn['option']) echo "                <col class=\"option\" />\n";
if($this->visibleColumn['date']) echo "                <col class=\"date\" />\n";
if($this->visibleColumn['from']) echo "                <col class=\"from\" />\n";
if($this->visibleColumn['to']) echo "                <col class=\"to\" />\n";
if($this->visibleColumn['time']) echo "                <col class=\"time\" />\n";
if($this->visibleColumn['wage']) echo "                <col class=\"wage\" />\n";
if($this->visibleColumn['customer']) echo "                <col class=\"customer\" />\n";
if($this->visibleColumn['project']) echo "                <col class=\"project\" />\n";
if($this->visibleColumn['activity']) echo "                <col class=\"activity\" />\n";
if($this->visibleColumn['trackingnumber']) echo "                <col class=\"trackingnumber\" />\n";
if($this->visibleColumn['username']) echo "                <col class=\"username\" />\n";
?>
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
            <tr id="timeSheetEntry<?php echo $row['timeEntryID']?>" class="<?php echo $this->cycle(array("odd","even"))->next()?> active">
        <?php endif; ?>

<?php if ($this->visibleColumn['option']): ?>
        <td nowrap class="option <?php echo $tdClass; ?>">

        <?php if (isset($this->kga['user'])): // only users can see options ?>

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
<?php endif; ?>

<?php if ($this->visibleColumn['date']): ?>
            <td class="date <?php echo $tdClass; ?>">
                <?php echo $this->escape(strftime($this->kga['date_format'][1],$row['start']));?>
            </td>
<?php endif; ?>

<?php if ($this->visibleColumn['from']): ?>
            <td class="from <?php echo $tdClass; ?>">
                <?php echo $this->escape(strftime("%H:%M",$row['start']));?>
            </td>
<?php endif; ?>

<?php if ($this->visibleColumn['to']): ?>
            <td class="to <?php echo $tdClass; ?>">
            <?php
                if ($row['end']) {
                    echo $this->escape(strftime("%H:%M",$row['end']));
                } else {
                    echo "&ndash;&ndash;:&ndash;&ndash;";
                }
            ?>
            </td>
<?php endif; ?>

<?php if ($this->visibleColumn['time']): ?>
            <td class="time <?php echo $tdClass; ?>">
                <?php
                if (isset($row['duration'])) {
                    echo $row['formattedDuration'];
                } else {
                    echo "&ndash;:&ndash;&ndash;";
                }
                ?>
            </td>
<?php endif; ?>

<?php if ($this->visibleColumn['wage']): ?>
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

<?php if ($this->visibleColumn['customer']): ?>
            <td class="customer <?php echo $tdClass; ?>">
                <?php echo $this->escape($row['customerName']) ?>
            </td>
<?php endif; ?>

<?php if ($this->visibleColumn['project']): ?>
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
<?php endif; ?>

<?php if ($this->visibleColumn['activity']): ?>
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
<?php endif; ?>

<?php if ($this->visibleColumn['trackingnumber']): ?>
            <?php if ($this->showTrackingNumber) { ?>
            <td class="trackingnumber <?php echo $tdClass; ?>">
                <?php echo $this->escape($row['trackingNumber']) ?>
            </td>
            <?php } ?>
<?php endif; ?>

<?php if ($this->visibleColumn['username']): ?>
            <td class="username <?php echo $tdClass; ?>">
                <?php echo $this->escape($row['userName']) ?>
            </td>
<?php endif; ?>


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
    ts_user_annotations = <?php echo json_encode($this->user_annotations); ?>;
    ts_customer_annotations = <?php echo json_encode($this->customer_annotations) ?>;
    ts_project_annotations = <?php echo json_encode($this->project_annotations) ?>;
    ts_activity_annotations = <?php echo json_encode($this->activity_annotations) ?>;
    ts_total = '<?php echo $this->total?>';
    
    lists_update_annotations(parseInt($('#gui div.ki_timesheet').attr('id').substring(7)),ts_user_annotations,ts_customer_annotations,ts_project_annotations,ts_activity_annotations);
    $('#display_total').html(ts_total);
    
  <?php if ($this->latest_running_entry == null): ?>
    updateRecordStatus(false);
  <?php else: ?>

    updateRecordStatus(<?php echo $this->latest_running_entry['timeEntryID']?>,<?php echo $this->latest_running_entry['start']?>,
                             <?php echo $this->latest_running_entry['customerID']?>,'<?php echo $this->jsEscape($this->latest_running_entry['customerName'])?>',
                             <?php echo $this->latest_running_entry['projectID']?> ,'<?php echo $this->jsEscape($this->latest_running_entry['projectName'])?>',
                             <?php echo $this->latest_running_entry['activityID']?>,'<?php echo $this->jsEscape($this->latest_running_entry['activityName'])?>');
  <?php endif; ?>

    function timesheet_hide_column(name) {
        $('.'+name).hide();
    }

    <?php if (!$this->showTrackingNumber) { ?>
        timesheet_hide_column('trackingnumber');
    <?php } ?>

</script>
