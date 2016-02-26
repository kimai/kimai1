<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php if (isset($id)) echo $this->kga['lang']['edit'], ': ', $this->kga['lang']['activity']; else echo $this->kga['lang']['new_activity']; ?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->kga['lang']['close'] ?></a>
        </div>
    </div>
    <div class="menuBackground">
        <ul class="menu tabSelection">
            <li class="tab norm"><a href="#general">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->kga['lang']['general'] ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
            <li class="tab norm"><a href="#projectstab">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->kga['lang']['projects'] ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
            <li class="tab norm"><a href="#groups">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->kga['lang']['groups'] ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
            <li class="tab norm"><a href="#commenttab">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->kga['lang']['comment'] ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
        </ul>
    </div>
    <form id="add_edit_activity" action="processor.php" method="post">
        <input type="hidden" name="activityFilter" value="0"/>
        <input type="hidden" name="axAction" value="add_edit_CustomerProjectActivity"/>
        <input type="hidden" name="axValue" value="activity"/>
        <input type="hidden" name="id" value="<?php echo $this->id; ?>"/>
        <div id="floater_tabs" class="floater_content">
            <fieldset id="general">
                <ul>
                    <li>
                        <label for="name"><?php echo $this->kga['lang']['activity'] ?>:</label>
                        <?php echo $this->formText('name', $this->name); ?>
                    </li>
                    <li>
                        <label for="defaultRate"><?php echo $this->kga['lang']['default_rate'] ?>:</label>
                        <?php echo $this->formText('defaultRate', str_replace('.', $this->kga['conf']['decimalSeparator'], $this->defaultRate)); ?>
                    </li>
                    <li>
                        <label for="myRate"><?php echo $this->kga['lang']['my_rate'] ?>:</label>
                        <?php echo $this->formText('myRate', str_replace('.', $this->kga['conf']['decimalSeparator'], $this->myRate)); ?>
                    </li>
                    <li>
                        <label for="fixedRate"><?php echo $this->kga['lang']['fixedRate'] ?>:</label>
                        <?php echo $this->formText('fixedRate', str_replace('.', $this->kga['conf']['decimalSeparator'], $this->fixedRate)); ?>
                    </li>
                    <li>
                        <label for="visible"><?php echo $this->kga['lang']['visibility'] ?>:</label>
                        <?php echo $this->formCheckbox('visible', '1', array('checked' => $this->visible || !$this->id)); ?>
                    </li>
                </ul>
            </fieldset>
            <fieldset id="commenttab">
                <ul>
                    <li>
                        <label for="comment"><?php echo $this->kga['lang']['comment'] ?>:</label>
                        <?php echo $this->formTextarea('comment', $this->comment, array(
                            'cols' => 30,
                            'rows' => 5,
                            'class' => 'comment'
                        )); ?>
                    </li>
                </ul>
            </fieldset>
            <fieldset id="groups">
                <ul>
                    <li>
                        <label for="activityGroups"><?php echo $this->kga['lang']['groups'] ?>:</label>
                        <?php echo $this->formSelect('activityGroups[]', $this->selectedGroups, array(
                            'class' => 'formfield',
                            'id' => 'activityGroups',
                            'multiple' => 'multiple',
                            'size' => 3,
                            'style' => 'width:255px'), $this->groups); ?>
                    </li>
                </ul>
            </fieldset>
            <fieldset id="projectstab">
                <ul>
                    <li>
                        <label for="activityProjects"><?php echo $this->kga['lang']['projects'] ?>:</label>
                        <?php echo $this->formSelect('projects[]', $this->selectedProjects, array(
                            'class' => 'formfield',
                            'id' => 'activityProjects',
                            'multiple' => 'multiple',
                            'size' => 5,
                            'style' => 'width:255px'), $this->projects); ?>
                    </li>
                </ul>
            </fieldset>
        </div>
        <div id="formbuttons">
            <input class='btn_norm' type='button' value='<?php echo $this->kga['lang']['cancel'] ?>' onclick='floaterClose();return false;'/>
            <input class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['submit'] ?>'/>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#floater_innerwrap').tabs({selected: 0});
        var $add_edit_activity = $('#add_edit_activity');
        $add_edit_activity.ajaxForm({
            'beforeSubmit': function () {
                clearFloaterErrorMessages();

                if ($add_edit_activity.attr('submitting')) {
                    return false;
                }
                else {
                    $add_edit_activity.attr('submitting', true);
                    return true;
                }
            },
            'success': function (result) {
                $add_edit_activity.removeAttr('submitting');
                for (var fieldName in result.errors) {
                    setFloaterErrorMessage(fieldName, result.errors[fieldName]);
                }
                if (result.errors.length == 0) {
                    floaterClose();
                    hook_activities_changed();
                }
            },
            'error': function () {
                $add_edit_activity.removeAttr('submitting');
            }
        });
    });
</script>