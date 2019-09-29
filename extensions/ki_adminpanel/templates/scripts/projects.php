<a href="#" onclick="floaterShow('floaters.php', 'add_edit_project', 0, 0, 650); $(this).blur(); return false;"><img src="<?php echo $this->skin('grfx/add.png'); ?>" width="22" height="16" alt="<?php echo $this->translate('new_project')?>"></a>
<?php echo $this->translate('new_project')?><br/><br/>
<table>
<thead>
	<tr class="headerrow">
		<th width="80px"><?php echo $this->translate('options')?></th>
		<?php if ($this->kga->getSettings()->isFlipProjectDisplay()): ?>
			<th width="25%"><?php echo $this->translate('customer')?></th>
			<th><?php echo $this->translate('project')?></th>
		<?php else: ?>
			<th width="25%"><?php echo $this->translate('project')?></th>
			<th><?php echo $this->translate('customer')?></th>
		<?php endif; ?>
		<th width="25%"><?php echo $this->translate('groups')?></th>
	</tr>
</thead>
<tbody>
<?php
	if (!isset($this->projects) || $this->projects == '0' || count($this->projects) == 0) {
		?>
		<tr>
			<td nowrap colspan="3"><?php echo $this->error(); ?></td>
		</tr>
		<?php
	} else {
		foreach ($this->projects as $row) {
			$isHidden = $row['visible'] != 1 || $row['customerVisible'] != 1;
			?>
			<tr class="<?php echo $this->cycle(["odd", "even"])->next()?>">
				<td class="option">
					<a href="#" onclick="editSubject('project',<?php echo $row['projectID']?>); $(this).blur(); return false;"><img
						src="<?php echo $this->skin('grfx/edit2.gif'); ?>" width="13" height="13"
						alt="<?php echo $this->translate('edit')?>" title="<?php echo $this->translate('edit')?>" border="0" /></a>
					&nbsp;
					<a href="#" id="delete_project<?php echo $row['projectID']?>" onclick="adminPanel_extension_deleteProject(<?php echo $row['projectID']?>)"><img
						src="<?php echo $this->skin('grfx/button_trashcan.png'); ?>" title="<?php echo $this->translate('delete_project')?>"
						width="13" height="13" alt="<?php echo $this->translate('delete_project')?>" border="0"></a>
				</td>
				<?php if ($this->kga->getSettings()->isFlipProjectDisplay()): ?>
					<td class="customer <?php if ($isHidden) { echo 'hidden'; } ?>">
						<?php echo $this->escape($this->ellipsis($row['customerName'], 30))?>
					</td>
					<td class="projects <?php if ($isHidden) { echo 'hidden'; } ?>">
						<?php echo $this->escape($row['name']) ?>
					</td>
				<?php else: ?>
					<td class="projects <?php if ($isHidden) { echo 'hidden'; } ?>">
						<?php echo $this->escape($row['name']) ?>
					</td>
					<td class="customer <?php if ($isHidden) { echo 'hidden'; } ?>">
						<?php echo $this->escape($this->ellipsis($row['customerName'], 30))?>
					</td>
				<?php endif; ?>
				<td class="<?php if ($isHidden) { echo 'hidden'; } ?>">
					<?php echo $this->escape($row['groups'])?>
				</td>
			</tr>
			<?php
		}
	}
	?>
	</tbody>
</table>
