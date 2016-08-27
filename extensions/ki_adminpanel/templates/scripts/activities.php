<a href="#" onclick="floaterShow('floaters.php','add_edit_activity',0,0,500); $(this).blur(); return false;"><img src="<?php echo $this->skin('grfx/add.png'); ?>" width="22" height="16" alt="<?php echo $this->kga['lang']['new_activity']?>"></a> <?php echo $this->kga['lang']['new_activity']?>
&nbsp;&nbsp;&nbsp;<?php echo $this->kga['lang']['view_filter']?>:
<select size="1" id="activity_project_filter" onchange="adminPanel_extension_refreshSubtab('activities');">
	<option value="-2" <?php if ($this->selected_activity_filter == -2): ?> selected="selected"<?php endif; ?>> <?php echo $this->kga['lang']['unassigned']?></option>
	<option value="-1" <?php if ($this->selected_activity_filter == -1): ?> selected="selected"<?php endif; ?>> <?php echo $this->kga['lang']['all_activities']?></option>
	<?php foreach ($this->projects as $row): ?>
	<option value="<?php echo $row['projectID']?>"
		<?php if ($this->selected_activity_filter==$row['projectID']): ?> selected="selected"<?php endif; ?>> <?php echo $this->escape($row['name']),' (', $this->escape($this->truncate($row['customerName'],30,"..."))?>)</option>
	<?php endforeach; ?>
</select>
<br/><br/>
<table>
	<thead>
		<tr class="headerrow">
			<th width="80"><?php echo $this->kga['lang']['options']?></th>
			<th width="25%"><?php echo $this->kga['lang']['activity']?></th>
			<th><?php echo $this->kga['lang']['projects']?></th>
			<th width="25%"><?php echo $this->kga['lang']['groups']?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if (!isset($this->activities) || $this->activities == '0' || count($this->activities) == 0) {
		?>
		<tr>
			<td nowrap colspan="4"><?php echo $this->error(); ?></td>
		</tr>
		<?php
	} else {
		foreach ($this->activities as $activity) {
			$isHidden = $activity['visible'] != 1;
			?>
			<tr class="<?php echo $this->cycle(array("odd", "even"))->next()?>">
				<td class="option">
					<a href="#" onclick="editSubject('activity',<?php echo $activity['activityID']?>); $(this).blur(); return false;">
						<img src="<?php echo $this->skin('grfx/edit2.gif'); ?>" width="13" height="13" alt="<?php echo $this->kga['lang']['edit']?>" title="<?php echo $this->kga['lang']['edit']?>" border="0" /></a>
					&nbsp;
					<a href="#" id="delete_activity<?php echo $activity['activityID']?>" onclick="adminPanel_extension_deleteActivity(<?php echo $activity['activityID']?>)">
						<img src="<?php echo $this->skin('grfx/button_trashcan.png'); ?>" title="<?php echo $this->kga['lang']['delete_activity']?>" width="13" height="13" alt="<?php echo $this->kga['lang']['delete_activity']?>" border="0"></a>
				</td>
				<td class="activities <?php if ($isHidden) { echo 'hidden'; } ?>">
					<?php echo $this->escape($activity['name']); ?>
				</td>
				<td>
					<?php
					$activityProjects = array();
					foreach($activity['projects'] as $project) {
						$name = $this->escape($project['name']) . ' (' . $this->escape($this->ellipsis($project['customer_name'], 30)) . ')';
						if ($project['visible'] != 1 || $project['customerVisible'] != 1) {
							$name = '<span class="hidden">' . $name . '</span>';
						}
						$activityProjects[] = $name;
					}
					echo implode(', ', $activityProjects);
					?>
				</td>
				<td class="<?php if ($isHidden) { echo 'hidden'; } ?>">
					<?php echo $this->escape($activity['groups']); ?>
				</td>
			</tr>
			<?php
		}
	}
	?>
	</tbody>
</table>