<?php
if (count($this->exportData) > 0)
{
    $showGabBreaks = $this->kga->isShowGabBreaks();
    $showDaySeperatorLines = $this->kga->isShowDaySeperatorLines();

    ?>
    <div id="xptable">
        <table>
            <tbody>
            <?php
            $day_buffer = 0;
            $time_in_buffer = 0;
            ?>

            <?php
            foreach ($this->exportData as $row)
            {
                $isExpense = $row['type'] == "expense";
                $tdClass = '';
                if (strftime("%d", $row['time_in']) != $day_buffer && $showDaySeperatorLines) {
                    $tdClass = "break_day ";
                } elseif ($row['time_out'] != $time_in_buffer && $showGabBreaks) {
                    $tdClass = "break_gap ";
                }

                ?>
                <tr id="xp<?php echo $row['type'], $row['id'] ?>"
                    class="<?php echo $this->cycle(array("odd", "even"))->next() ?> <?php if (!$row['time_out']): ?>active<?php endif; ?> <?php if ($isExpense): ?> expense<?php endif; ?>">
                    <td class="date <?php echo $tdClass;
                    if (isset($this->disabled_columns['date'])) echo "disabled"; ?>
                    ">
                        <?php echo $this->escape(strftime($this->dateformat, $row['time_in'])) ?>
                    </td>

                    <td class="from <?php echo $tdClass;
                    if (isset($this->disabled_columns['from'])) echo "disabled"; ?>
                    ">
                        <?php echo $this->escape(strftime($this->timeformat, $row['time_in'])) ?>
                    </td>

                    <td class="to <?php echo $tdClass;
                    if (isset($this->disabled_columns['to'])) echo "disabled"; ?>
                    ">
                        <?php if ($row['time_out']) echo $this->escape(strftime($this->timeformat, $row['time_out']));
                        else echo "&ndash;&ndash;:&ndash;&ndash;" ?>
                    </td>

                    <td class="time <?php echo $tdClass;
                    if (isset($this->disabled_columns['time'])) echo "disabled"; ?>
                    ">
                        <?php if ($row['duration']) echo $row['formattedDuration'];
                        else echo "&ndash;:&ndash;&ndash;"; ?>
                    </td>

                    <td class="dec_time <?php echo $tdClass;
                    if (isset($this->disabled_columns['dec_time'])) echo "disabled"; ?>
                    ">
                        <?php if ($row['decimalDuration']) echo $this->escape(str_replace('.', $this->kga['conf']['decimalSeparator'], $row['decimalDuration']));
                        else echo "&ndash;:&ndash;&ndash;"; ?>
                    </td>

                    <td class="rate <?php echo $tdClass;
                    if (isset($this->disabled_columns['rate'])) echo "disabled"; ?>
                    ">
                        <?php echo $this->escape(str_replace('.', $this->kga['conf']['decimalSeparator'], $row['rate'])); ?>
                    </td>

                    <td class="wage <?php echo $tdClass;
                    if (isset($this->disabled_columns['wage'])) echo "disabled"; ?>
                    ">
                        <?php if ($row['wage']) echo $this->escape(str_replace('.', $this->kga['conf']['decimalSeparator'], $row['wage']));
                        else echo "&ndash;"; ?>
                    </td>

                    <td class="budget <?php echo $tdClass;
                    if (isset($this->disabled_columns['budget'])) echo "disabled"; ?>
                    ">
                        <?php echo !$isExpense ? $this->escape($row['budget']) : '&ndash;'; ?>
                    </td>

                    <td class="approved <?php echo $tdClass;
                    if (isset($this->disabled_columns['approved'])) echo "disabled"; ?>
                    ">
                        <?php echo !$isExpense ? $this->escape($row['approved']) : '&ndash;'; ?>
                    </td>

                    <td class="status <?php echo $tdClass;
                    if (isset($this->disabled_columns['status'])) echo "disabled"; ?>
                    ">
                        <?php echo !$isExpense ? $this->escape($row['status']) : '&ndash;'; ?>
                    </td>

                    <td class="billable <?php echo $tdClass;
                    if (isset($this->disabled_columns['billable'])) echo "disabled"; ?>
                    ">
                        <?php echo !$isExpense ? $this->escape($row['billable']) . '%' : '&ndash;'; ?>
                    </td>

                    <td class="customer <?php echo $tdClass;
                    if (isset($this->disabled_columns['customer'])) echo "disabled"; ?>
                    ">
                        <?php echo $this->escape($row['customerName']) ?>
                    </td>

                    <td class="project <?php echo $tdClass;
                    if (isset($this->disabled_columns['project'])) echo "disabled"; ?>
                    ">
                        <a href="#" class="preselect_lnk"
                           onclick="buzzer_preselect_project(<?php echo $row['projectID'] ?>,'<?php echo $this->jsEscape($row['projectName']) ?>',<?php echo $row['customerID'] ?>,'<?php echo $this->jsEscape($row['customerName']) ?>');
                               return false;">
                            <?php echo $this->escape($row['projectName']) ?>
                            <?php if ($this->kga->getSettings()->isShowProjectComment()): ?>
                                <?php if ($row['projectComment']): ?>
                                    <span class="lighter">(<?php echo $this->jsEscape($row['projectComment']); ?>
                                        )</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </a>
                    </td>

                    <td class="activity <?php echo $tdClass;
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

                    <td class="description <?php echo $tdClass;
                    if (isset($this->disabled_columns['comment'])) echo "disabled"; ?>
                    ">
                        <?php echo nl2br($this->escape($row['description'])) ?>
                    </td>

                    <td class="comment <?php echo $tdClass;
                    if (isset($this->disabled_columns['comment'])) echo "disabled"; ?>
                    ">
                        <?php echo nl2br($this->escape($row['comment'])) ?>
                    </td>

                    <td class="location <?php echo $tdClass;
                    if (isset($this->disabled_columns['location'])) echo "disabled"; ?>
                    ">
                        <?php echo $this->escape($row['location']) ?>
                    </td>

                    <td class="trackingNumber <?php echo $tdClass;
                    if (isset($this->disabled_columns['trackingNumber'])) echo "disabled"; ?>
                    ">
                        <?php echo $this->escape($row['trackingNumber']) ?>
                    </td>

                    <td class="user <?php echo $tdClass;
                    if (isset($this->disabled_columns['user'])) echo "disabled"; ?>
                    ">
                        <?php echo $this->escape($row['username']) ?>
                    </td>

                    <td class="cleared <?php echo $tdClass;
                    if (isset($this->disabled_columns['cleared'])) echo "disabled"; ?>
                    ">
                        <a class="<?php echo ($row['cleared']) ? "is_cleared" : "isnt_cleared" ?>" href="#"
                           onclick="export_toggle_cleared('<?php echo $row['type'], $row['id'] ?>'); return false;"></a>
                    </td>
                </tr>
                <?php
                $day_buffer = strftime("%d", $row['time_in']);
                $time_in_buffer = $row['time_in'];
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

    lists_update_annotations(parseInt($('#gui div.ki_export').attr('id').substring(7)), ts_user_annotations, ts_customer_annotations, ts_project_annotations, ts_activity_annotations);
    $('#display_total').html(ts_total);
</script>
