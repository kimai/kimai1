<?php
$autoSelection = $this->kga->getSettings()->isUseAutoSelection();
?>
<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php
            if (isset($this->expense['expenseID'])) {
                echo $this->translate('edit');
            } else {
                echo $this->translate('add');
            }
        ?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->translate('close') ?></a>
            <a href="#" class="help" onclick="$(this).blur(); $('#help').slideToggle();"><?php echo $this->translate('help') ?></a>
        </div>
    </div>
    <div id="help">
        <div class="content">
            <?php echo $this->translate('dateAndTimeHelp') ?>
        </div>
    </div>
    <div class="menuBackground">
        <ul class="menu tabSelection">
            <li class="tab norm"><a href="#general">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->translate('general') ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
            <li class="tab norm"><a href="#extended">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->translate('advanced') ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
        </ul>
    </div>
    <form id="expense_extension_form_add_edit_record" action="../extensions/ki_expenses/processor.php" method="post">
        <?php if (isset($this->expense['expenseID'])) : ?>
	        <input type="hidden" name="id" value="<?php echo $this->expense['expenseID'] ?>"/>
        <?php endif; ?>
        <input type="hidden" name="axAction" value="add_edit_record"/>
        <div id="floater_tabs" class="floater_content">
            <fieldset id="general">
                <ul>
                    <li>
                        <label for="projectID"><?php echo $this->translate('project') ?>:</label>
                        <div class="multiFields"><?php
                            echo $this->formSelect('projectID', $this->expense['projectID'], [
                                'id' => 'add_edit_expense_project_ID',
                                'class' => 'formfield',
                                'size' => '5',
                                'style' => 'width:400px',
                                'tabindex' => '1',
                            ], $this->projects);
                            ?><br/>
                            <input type="text" style="width:395px;margin-top:3px" tabindex="2" size="10" name="filter" id="filter" onkeyup="filter_selects('add_edit_expense_project_ID', this.value);"/>
                        </div>
                    </li>
                    <li>
                        <label for="edit_day"><?php echo $this->translate('day') ?>:</label>
                        <input type="text" name="edit_day" id="edit_day" value="<?php echo $this->escape(date($this->kga->getDateFormat(3), $this->expense['timestamp'])) ?>" maxlength="10" size="10" tabindex="5" <?php if ($autoSelection): ?> onclick="this.select();" <?php endif; ?> />
                    </li>
                    <li>
                        <label for="edit_time"><?php echo $this->translate('timelabel') ?>:</label>
                        <input type="text" name="edit_time" id="edit_time" value="<?php echo $this->escape(date('H:i:s', $this->expense['timestamp'])) ?>" maxlength="8" size="8" tabindex="7" <?php if ($autoSelection): ?> onclick="this.select();" <?php endif; ?> />
                        <a href="#" onclick="expense_pasteNow(); $(this).blur(); return false;"><?php echo $this->translate('now') ?></a>
                    </li>
                    <li>
                        <label for="multiplier"><?php echo $this->translate('multiplier') ?>:</label>
                        <input type="text" name="multiplier" id="multiplier" value="<?php echo $this->escape($this->expense['multiplier']) ?>" maxlength="8" size="8" tabindex="9" <?php if ($autoSelection): ?> onclick="this.select();" <?php endif; ?> />
                    </li>
                    <li>
                        <label for="edit_value"><?php echo $this->translate('expense') ?>:</label>
                        <input type="text" name="edit_value" id="edit_value" value="<?php echo $this->escape($this->expense['value']) ?>" maxlength="8" size="8" tabindex="10" <?php if ($autoSelection): ?> onclick="this.select();" <?php endif; ?> />
                    </li>
                    <li>
                        <label for="designation"><?php echo $this->translate('designation') ?>:</label>
                        <input type="text" name="designation" id="designation" value="<?php echo $this->escape($this->expense['designation']) ?>" maxlength="50" size="50" tabindex="11" <?php if ($autoSelection): ?> onclick="this.select();" <?php endif; ?> />
                    </li>
                </ul>
            </fieldset>
            <fieldset id="extended">
                <ul>
                    <li>
                        <label for="refundable"><?php echo $this->translate('refundable_long') ?>:</label>
                        <input type="checkbox" name="refundable" id="refundable" <?php if ($this->expense['refundable']): ?> checked="checked" <?php endif; ?> tabindex="12"/>
                    </li>
                    <li>
                        <label for="comment"><?php echo $this->translate('comment') ?>:</label>
                        <textarea name="comment" id="comment" style="width:395px" class="comment" cols="40" rows="5" tabindex="13"><?php echo $this->escape($this->expense['comment']) ?></textarea>
                    </li>
                    <li>
                        <label for="commentType"><?php echo $this->translate('commentType') ?>:</label>
                        <?php echo $this->commentTypeSelect($this->expense['commentType']); ?>
                    </li>
                    <li>
                        <label for="erase"><?php echo $this->translate('erase') ?>:</label>
                        <input type="checkbox" name="erase" id="erase" tabindex="15"/>
                    </li>
	                <li>
		                <label for="cleared"><?php echo $this->translate('cleared') ?>:</label>
		                <input type="checkbox" name="cleared" id="cleared" <?php if ($this->expense['cleared']): ?> checked="checked" <?php endif; ?> tabindex="16"/>
	                </li>
                </ul>
            </fieldset>
        </div>
        <div id="formbuttons">
	        <button type="button" class="btn_norm" onclick="floaterClose();"><?php echo $this->translate('cancel') ?></button>
	        <input type="submit" class="btn_ok" value="<?php echo $this->translate('submit') ?>"/>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#help').hide();

        $('#edit_day').datepicker();

        $('#edit_value').keyup(function() {
            var $self = $(this);
            $self.val($self.val().replace(/,/g, '.'));
        });

        var $expense_extension_form_add_edit_record = $('#expense_extension_form_add_edit_record');
        $expense_extension_form_add_edit_record.ajaxForm({
            'beforeSubmit': function () {
                clearFloaterErrorMessages();

                if ($expense_extension_form_add_edit_record.attr('submitting')) {
                    return false;
                } else {
                    $expense_extension_form_add_edit_record.attr('submitting', true);
                    return true;
                }
            },
            'success': function (result) {
                $expense_extension_form_add_edit_record.removeAttr('submitting');

                for (var fieldName in result.errors) {
                    setFloaterErrorMessage(fieldName, result.errors[fieldName]);
                }
                if (result.errors.length === 0) {
                    floaterClose();
                    expense_extension_reload();
                }
            },
            'error': function () {
                $expense_extension_form_add_edit_record.removeAttr('submitting');
            }
        });

        <?php if (!isset($this->id)): ?>
        $("#add_edit_expense_project_ID").val(selected_project);
        <?php endif; ?>
        $('#floater_innerwrap').tabs({selected: 0});
    });
</script>