<a onClick="floaterShow('floaters.php','add_edit_activity',0,0,500); $(this).blur(); return false;"
   href="#" ><?php echo $this->icons('add', array('title' => $this->kga['lang']['new_activity'])); ?></a>
<?php echo $this->kga['lang']['new_activity']?>

&nbsp;&nbsp;&nbsp;<?php echo $this->kga['lang']['view_filter']?>:
<select size="1" id="activity_project_filter" onchange="adminPanel_extension_refreshSubtab('activities');">
  <option value="-2" <?php if ($this->selected_activity_filter==-2): ?> selected="selected"<?php endif; ?>> <?php echo $this->kga['lang']['unassigned']?></option>
  <option value="-1" <?php if ($this->selected_activity_filter==-1): ?> selected="selected"<?php endif; ?>> <?php echo $this->kga['lang']['all_activities']?></option>
  <?php foreach ($this->projects as $row): ?>
  <option value="<?php echo $row['projectID']?>"
     <?php if ($this->selected_activity_filter==$row['projectID']): ?> selected="selected"<?php endif; ?>> <?php echo $this->escape($row['name']),' (', $this->escape($this->truncate($row['customerName'],30,"..."))?>)</option>
  <?php endforeach; ?>
</select>

<br/><br/>

<table>

    <thead>
        <tr class='headerrow'>
            <th class="admin_options"><?php echo $this->kga['lang']['options']?></th>
            <th><?php echo $this->kga['lang']['activities']?></th>
            <th><?php echo $this->kga['lang']['groups']?></th>
        </tr>
    </thead>

<tbody>
<?php
    if (!isset($this->activities) || $this->activities == '0' || count($this->activities) == 0)
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
        foreach ($this->activities as $activity)
        {
            ?>

            <tr class="<?php echo $this->cycle(array("odd","even"))->next()?>">
                <td class="option">
                    <a href ="#" onClick="editSubject('activity',<?php echo $activity['activityID']?>); $(this).blur(); return false;">
                        <?php echo $this->icons('edit'); ?></a>
                    &nbsp;
                    <a href="#" id="delete_activity<?php echo $activity['activityID']?>" onClick="adminPanel_extension_deleteActivity(<?php echo $activity['activityID']?>)">
                        <?php echo $this->icons('delete', array('title' => $this->kga['lang']['delete_activity'])); ?>
                    </a>
                </td>
                <td class="activities">
                    <?php if ($activity['visible'] != 1): ?><span style="color:#bbb"> <?php endif; ?>
                    <?php echo $this->escape($activity['name']); ?>
                    <?php if ($activity['visible'] != 1): ?></span><?php endif; ?>
                </td>
                <td>
                    <?php echo $this->escape($activity['groups']); ?>
                </td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>