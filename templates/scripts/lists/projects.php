<?php
// remove hidden entries from list
$projects = $this->filterListEntries($this->projects);
?>
<table>
    <tbody>
    <?php
    if (count($projects)) {
        foreach ($projects as $project) {
            ?>
            <tr id="row_project" data-id="<?php echo $project['projectID'] ?>"
                class="project customer<?php echo $project['customerID'] ?> <?php echo $this->cycle([
                    'odd',
                    'even'
                ])->next() ?>">
                <td nowrap class="option">
                    <?php if ($this->show_project_edit_button): ?>
                        <a href="#"
                           onclick="editSubject('project',<?php echo $project['projectID'] ?>); $(this).blur(); return false;">
                            <img src="<?php echo $this->skin('grfx/edit2.gif'); ?>" width='13' height='13'
                                 alt='<?php echo $this->translate('edit') ?>'
                                 title='<?php echo $this->translate('edit') ?> (ID:<?php echo $project['projectID'] ?>)'
                                 border='0'/>
                        </a>
                    <?php endif; ?>
                    <a href="#"
                       onclick="lists_update_filter('project',<?php echo $project['projectID'] ?>); $(this).blur(); return false;">
                        <img src="<?php echo $this->skin('grfx/filter.png'); ?>" width='13' height='13'
                             alt='<?php echo $this->translate('filter') ?>'
                             title='<?php echo $this->translate('filter') ?>' border='0'/>
                    </a>
                    <a href="#" class="preselect"
                       onclick="buzzer_preselect_project(<?php echo $project['projectID'] ?>,'<?php echo $this->jsEscape($project['name']) ?>',<?php echo $project['customerID'] ?>,'<?php echo $this->jsEscape($project['customerName']) ?>'); return false;"
                       id="ps<?php echo $project['projectID'] ?>">
                        <img src="<?php echo $this->skin('grfx/preselect_off.png'); ?>" width='13' height='13'
                             alt='<?php echo $this->translate('select') ?>'
                             title='<?php echo $this->translate('select') ?> (ID:<?php echo $project['projectID'] ?>)'
                             border='0'/>
                    </a>
                </td>

                <td width="100%" class="projects" onmouseover="lists_change_color(this,true);"
                    onmouseout="lists_change_color(this,false);"
                    onclick="buzzer_preselect_project(<?php echo $project['projectID'] ?>,'<?php echo $this->jsEscape($project['name']) ?>',<?php echo $project['customerID'] ?>,'<?php echo $this->jsEscape($project['customerName']) ?>'); lists_reload('activity'); return false;">
                    <?php if ($this->kga->getSettings()->isFlipProjectDisplay()): ?>
                        <?php if ($this->kga->getSettings()->isShowIds()): ?>
                            <span class="ids"><?php echo $project['projectID'] ?></span>
                        <?php endif; ?>
                        <span class="lighter"><?php echo $this->escape($this->truncate($project['customerName'], 30,
                                '...')) ?>:</span> <?php echo $this->escape($project['name']) ?>
                    <?php else: ?>
                        <?php if ($this->kga->getSettings()->isShowProjectComment()): ?>
                            <?php if ($this->kga->getSettings()->isShowIds()): ?>
                                <span class="ids"><?php echo $project['projectID'] ?></span>
                            <?php endif; ?>
                            <?php echo $this->escape($project['name']) ?>
                            <span class="lighter">
                        <?php if ($project['comment']): ?>
                            (<?php echo $this->escape($this->truncate($project['comment'], 30, '...')) ?>)
                        <?php else: ?>
                            <span class="lighter">(<?php echo $this->escape($project['customerName']) ?>)</span>
                        <?php endif; ?>
                        </span>
                        <?php else: ?>
                            <?php if ($this->kga->getSettings()->isShowIds()): ?>
                                <span class="ids"><?php echo $project['projectID'] ?></span>
                            <?php endif; ?>
                            <?php echo $this->escape($project['name']) ?>
                            <span class="lighter">(<?php echo $this->escape($this->truncate($project['customerName'],
                                    30, '...')) ?>)</span>
                        <?php endif; ?>
                    <?php endif; ?>
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