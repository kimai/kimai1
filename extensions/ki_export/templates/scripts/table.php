<?php if (count($this->exportData) > 0): ?>
    <div id="xptable">
        <table>
            <colgroup>
                <col class="date"/>
                <col class="from"/>
                <col class="to"/>
                <col class="time"/>
                <col class="dec_time"/>
                <col class="rate"/>
                <col class="wage"/>
                <col class="budget"/>
                <col class="approved"/>
                <col class="status"/>
                <col class="billable"/>
                <col class="client"/>
                <col class="project"/>
                <col class="activity"/>
                <col class="description"/>
                <col class="comment"/>
                <col class="location"/>
                <col class="trackingNumber"/>
                <col class="user"/>
                <col class="cleared"/>
            </colgroup>
            <tbody>
            <?php
            $day_buffer = 0;
            $time_in_buffer = 0;
            ?>

            <?php foreach ($this->exportData as $row):
                $isExpense = $row['type'] == "expense"; ?>
                <tr id="xp<?php echo $row['type'], $row['id'] ?>" class="<?php echo $this->cycle(array("odd", "even"))->next() ?> <?php if (!$row['time_out']): ?>active<?php endif; ?>
 <?php if ($isExpense): ?> expense<?php endif; ?>">
                    <td class="date <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['date'])) echo "disabled"; ?>
                    ">
                        <?php echo $this->escape(strftime($this->dateformat, $row['time_in'])) ?>
                    </td>
                    <td class="from <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['from'])) echo "disabled"; ?>
                    ">
                        <?php echo $this->escape(strftime($this->timeformat, $row['time_in'])) ?>
                    </td>
                    <td class="to <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['to'])) echo "disabled"; ?>
                    ">
                        <?php if ($row['time_out']) echo $this->escape(strftime($this->timeformat, $row['time_out']));
                        else echo "&ndash;&ndash;:&ndash;&ndash;" ?>
                    </td>
                    <td class="time <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['time'])) echo "disabled"; ?>
                    ">
                        <?php if ($row['duration']) echo $row['formattedDuration'];
                        else echo "&ndash;:&ndash;&ndash;"; ?>
                    </td>
                    <td class="dec_time <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['dec_time'])) echo "disabled"; ?>
                    ">
                        <?php if ($row['decimalDuration']) echo $this->escape(str_replace('.', $this->kga['conf']['decimalSeparator'], $row['decimalDuration']));
                        else echo "&ndash;:&ndash;&ndash;"; ?>
                    </td>
                    <td class="rate <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['rate'])) echo "disabled"; ?>
                    ">
                        <?php echo $this->escape(str_replace('.', $this->kga['conf']['decimalSeparator'], $row['rate'])); ?>
                    </td>
                    <td class="wage <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['wage'])) echo "disabled"; ?>
                    ">
                        <?php if ($row['wage']) echo $this->escape(str_replace('.', $this->kga['conf']['decimalSeparator'], $row['wage']));
                        else echo "&ndash;"; ?>
                    </td>
                    <td class="budget <?php 
                    if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['budget'])) echo "disabled"; ?>
                    ">
                        <?php echo !$isExpense ? $this->escape($row['budget']) : '&ndash;'; ?>
                    </td>
                    <td class="approved <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['approved'])) echo "disabled"; ?>
                    ">
                        <?php echo !$isExpense ? $this->escape($row['approved']) : '&ndash;'; ?>
                    </td>
                    <td class="status <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['status'])) echo "disabled"; ?>
                    ">
                        <?php echo !$isExpense ? $this->escape($row['status']) : '&ndash;'; ?>
                    </td>
                    <td class="billable <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['billable'])) echo "disabled"; ?>
                    ">
                        <?php echo !$isExpense ? $this->escape($row['billable']) . '%' : '&ndash;'; ?>
                    </td>
                    <td class="customer <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['customer'])) echo "disabled"; ?>
                    ">
                        <?php echo $this->escape($row['customerName']) ?>
                    </td>
                    <td class="project <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['project'])) echo "disabled"; ?>
                    ">
                        <a href="#" class="preselect_lnk"
                           onclick="buzzer_preselect_project(<?php echo $row['projectID'] ?>,'<?php echo $this->jsEscape($row['projectName']) ?>',<?php echo $row['customerID'] ?>,'<?php echo $this->jsEscape($row['customerName']) ?>');
                               return false;">
                            <?php echo $this->escape($row['projectName']) ?>
                            <?php if ($this->kga['conf']['project_comment_flag'] == 1): ?>
                                <?php if ($row['projectComment']): ?>
                                    <span class="lighter">(<?php echo $this->jsEscape($row['projectComment']); ?>
                                        )</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </a>
                    </td>
                    <td class="activity <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['activity'])) echo "disabled"; ?>
                    ">
                        <?php if (!$isExpense): ?>
                        <a href="#" class="preselect_lnk"
                           onclick="buzzer_preselect_activity(<?php echo $row['activityID'] ?>,'<?php echo $this->jsEscape($row['activityName']) ?>',0,0);
                               return false;">
                            <?php echo $this->escape($row['activityName']);
                            else: ?>
                                &ndash;
                            <?php endif; ?>
                        </a>
                    </td>
                    <td class="description <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['comment'])) echo "disabled"; ?>
                    ">
                        <?php echo nl2br($this->escape($row['description'])) ?>
                    </td>
                    <td class="comment <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['comment'])) echo "disabled"; ?>
                    ">
                        <?php echo nl2br($this->escape($row['comment'])) ?>
                    </td>
                    <td class="location <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['location'])) echo "disabled"; ?>
                    ">
                        <?php echo $this->escape($row['location']) ?>
                    </td>
                    <td class="trackingNumber <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['trackingNumber'])) echo "disabled"; ?>
                    ">
                        <?php echo $this->escape($row['trackingNumber']) ?>
                    </td>
                    <td class="user <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['user'])) echo "disabled"; ?>
                    ">
                        <?php echo $this->escape($row['username']) ?>
                    </td>
                    <td class="cleared <?php if (strftime("%d", $row['time_in']) != $day_buffer && $this->kga['show_daySeperatorLines']) echo "break_day ";
                    elseif ($row['time_out'] != $time_in_buffer && $this->kga['show_gabBreaks']) echo "break_gap ";
                    if (isset($this->disabled_columns['cleared'])) echo "disabled"; ?>
                    ">
                        <a class="<?php echo ($row['cleared']) ? "is_cleared" : "isnt_cleared" ?>" href="#" onclick="export_toggle_cleared('<?php echo $row['type'], $row['id'] ?>'); return false;"></a>
                    </td>
                </tr>
                <?php
                $day_buffer = strftime("%d", $row['time_in']);
                $time_in_buffer = $row['time_in'];
            endforeach;
            ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div style='padding:5px;color:#f00'>
        <strong><?php echo $this->kga['lang']['noEntries'] ?></strong>
    </div>
<?php endif; ?>
<script type="text/javascript">
    ts_user_annotations = <?php echo json_encode($this->user_annotations); ?>;
    ts_customer_annotations = <?php echo json_encode($this->customer_annotations) ?>;
    ts_project_annotations = <?php echo json_encode($this->project_annotations) ?>;
    ts_activity_annotations = <?php echo json_encode($this->activity_annotations) ?>;
    ts_total = '<?php echo $this->total?>';

    lists_update_annotations(parseInt($('#gui div.ki_export').attr('id').substring(7)), ts_user_annotations, ts_customer_annotations, ts_project_annotations, ts_activity_annotations);
    $('#display_total').html(ts_total);
</script>
