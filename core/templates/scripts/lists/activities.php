<?php
// remove hidden entries from list
$activities = $this->filterListEntries($this->activities);
?>
<table>
  <tbody>
    <?php
    if (count($activities) == 0)
    {
        ?>
        <tr>
            <td nowrap colspan='3'>
                <?php echo $this->error(); ?>
            </td>
        </tr>
        <?php
    }
    else
    {
        foreach ($activities as $activity)
        {
            ?>
            <tr id="row_activity" data-id="<?php echo $activity['activityID']?>" class="<?php echo $this->cycle(array('odd','even'))->next()?>" >

                <td nowrap class="option">
                    <?php if ($this->show_activity_edit_button): ?>
                    <a href ="#" onClick="editSubject('activity',<?php echo $activity['activityID']?>); $(this).blur(); return false;">
                        <?php echo $this->icons('edit', array('title' => $this->kga['lang']['edit'] . ' (ID:'.$activity['activityID'].')')); ?>
                    </a>
                    <?php endif; ?>

                    <a href ="#" onClick="lists_update_filter('activity',<?php echo $activity['activityID']?>); $(this).blur(); return false;">
                        <?php echo $this->icons('filter'); ?>
                    </a>

                    <a href ="#" class="preselect" onClick="buzzer_preselect_activity(<?php echo $activity['activityID']?>,'<?php echo $this->jsEscape($activity['name'])?>'); return false;" id="ps<?php echo $activity['activityID']?>">
                      <img src='../skins/<?php echo $this->escape($this->kga['conf']['skin']) ?>/grfx/preselect_off.png' width='13' height='13' alt='<?php echo $this->kga['lang']['select']?>' title='<?php echo $this->kga['lang']['select']?> (ID:<?php echo $activity['activityID']?>)' border='0' />
                    </a>
                </td>

                <td width="100%" class="activities" onClick="buzzer_preselect_activity(<?php echo $activity['activityID']?>,'<?php echo $this->jsEscape($activity['name'])?>'); return false;" onmouseover="lists_change_color(this,true);" onmouseout="lists_change_color(this,false);">
                    <?php if ($activity['visible'] != 1): ?><span style="color:#bbb"><?php endif; ?>
                    <?php if ($this->kga['conf']['showIDs'] == 1): ?><span class="ids"><?php echo $activity['activityID']?></span> <?php endif; echo $this->escape($activity['name']) ?>
                    <?php if ($activity['visible'] != 1): ?></span><?php endif; ?>
                </td>

                <td nowrap class="annotation"></td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>  