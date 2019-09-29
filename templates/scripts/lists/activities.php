<?php
// remove hidden entries from list
$activities = $this->filterListEntries($this->activities);
?>
<table>
    <tbody>
    <?php
    if (count($activities)) {
        foreach ($activities as $activity) {
            ?>
            <tr id="row_activity" data-id="<?php echo $activity['activityID'] ?>"
                class="<?php echo $this->cycle(['odd', 'even'])->next() ?>">
                <td nowrap class="option">
                    <?php if ($this->show_activity_edit_button): ?>
                        <a href="#"
                           onclick="editSubject('activity',<?php echo $activity['activityID'] ?>); $(this).blur(); return false;">
                            <img src="<?php echo $this->skin('grfx/edit2.gif'); ?>" width='13' height='13'
                                 alt='<?php echo $this->translate('edit') ?>'
                                 title='<?php echo $this->translate('edit') ?> (ID:<?php echo $activity['activityID'] ?>)'
                                 border='0'/>
                        </a>
                    <?php endif; ?>
                    <a href="#"
                       onclick="lists_update_filter('activity',<?php echo $activity['activityID'] ?>); $(this).blur(); return false;">
                        <img src="<?php echo $this->skin('grfx/filter.png'); ?>" width='13' height='13'
                             alt='<?php echo $this->translate('filter') ?>'
                             title='<?php echo $this->translate('filter') ?>' border='0'/>
                    </a>
                    <a href="#" class="preselect"
                       onclick="buzzer_preselect_activity(<?php echo $activity['activityID'] ?>,'<?php echo $this->jsEscape($activity['name']) ?>'); return false;"
                       id="ps<?php echo $activity['activityID'] ?>">
                        <img src="<?php echo $this->skin('grfx/preselect_off.png'); ?>" width='13' height='13'
                             alt='<?php echo $this->translate('select') ?>'
                             title='<?php echo $this->translate('select') ?> (ID:<?php echo $activity['activityID'] ?>)'
                             border='0'/>
                    </a>
                </td>
                <td width="100%" class="activities"
                    onclick="buzzer_preselect_activity(<?php echo $activity['activityID'] ?>,'<?php echo $this->jsEscape($activity['name']) ?>'); return false;"
                    onmouseover="lists_change_color(this,true);" onmouseout="lists_change_color(this,false);">
                    <?php if ($this->kga->getSettings()->isShowIds()): ?><span
                            class="ids"><?php echo $activity['activityID'] ?></span> <?php endif;
                    echo $this->escape($activity['name']) ?>
                </td>
                <td nowrap class="annotation"></td>
            </tr>
            <?php
        }
    } else {
        ?>
        <tr>
            <td nowrap colspan="3"><?php echo $this->error(); ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>