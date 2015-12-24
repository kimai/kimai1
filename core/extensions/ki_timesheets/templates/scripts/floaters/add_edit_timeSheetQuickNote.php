<div id="floater_innerwrap">
    <div id="floater_handle">
		<span id="floater_title"><?php
            if (isset($this->id)) {
                echo $this->kga['lang']['editNote'];
            } else {
                echo $this->kga['lang']['addNote'];
            } ?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->kga['lang']['close'] ?></a>
            <a href="#" class="help" onclick="$(this).blur(); $('#help').slideToggle();"><?php echo $this->kga['lang']['help'] ?></a>
        </div>
    </div>
    <div id="help">
        <div class="content"><?php echo $this->kga['lang']['editNoteHelp'] ?></div>
    </div>
    <div class="menuBackground">
        <ul class="menu tabSelection">
            <li class="tab norm"><a href="#extended">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->kga['lang']['note'] ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
        </ul>
    </div>
    <form id="ts_ext_form_add_edit_timeSheetQuickNote" action="../extensions/ki_timesheets/processor.php" method="post">
        <input type="hidden" name="id" value="<?php echo $this->id; ?>"/>
        <input type="hidden" name="axAction" value="add_edit_timeSheetQuickNote"/>
        <input type="hidden" name="userID" value="<?php echo $this->kga['user']['userID']; ?>"/>
        <input type="hidden" name="projectID" value="<?php $this->escape($this->projectID); ?>"/>
        <input type="hidden" name="activityID" value="<?php $this->escape($this->activityID); ?>"/>
        <div id="floater_tabs" class="floater_content">
            <fieldset id="extended">
                <ul>
                    <li>
                        <label for="location"><?php echo $this->kga['lang']['location'] ?>:</label>
                        <input id='location' type='text' name='location'
                               value='<?php echo $this->escape($this->location) ?>' maxlength='50' size='20'
                               tabindex='11' <?php if ($this->kga['conf']['autoselection']): ?> onclick="this.select();"<?php endif; ?> />
                    </li>
                    <?php if ($this->kga['show_TrackingNr']): ?>
                        <li>
                            <label for="trackingNumber"><?php echo $this->kga['lang']['trackingNumber'] ?>:</label>
                            <input id='trackingNumber' type='text' name='trackingNumber'
                                   value='<?php echo $this->escape($this->trackingNumber) ?>' maxlength='20' size='20'
                                   tabindex='12' <?php if ($this->kga['conf']['autoselection']): ?> onclick="this.select();"<?php endif; ?> />
                        </li>
                    <?php endif; ?>
                    <li>
                        <label for="comment"><?php echo $this->kga['lang']['comment'] ?>:</label>
                        <textarea id='comment' style="width:395px" class='comment' name='comment' cols='40' rows='5'
                                  tabindex='13'><?php echo $this->escape($this->comment) ?></textarea>
                    </li>
                    <li>
                        <label for="commentType"><?php echo $this->kga['lang']['commentType'] ?>:</label>
                        <?php echo $this->formSelect('commentType', $this->commentType, array(
                            'id' => 'commentType',
                            'class' => 'formfield',
                            'tabindex' => '14'
                        ), $this->commentTypes); ?>
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
        $('#help').hide();
        $('#floater_innerwrap').tabs({ selected: 0 });
        var $ts_ext_form_add_edit_timeSheetQuickNote = $('#ts_ext_form_add_edit_timeSheetQuickNote');
        $ts_ext_form_add_edit_timeSheetQuickNote.ajaxForm({
            'beforeSubmit': function () {
                clearFloaterErrorMessages();
                if ($ts_ext_form_add_edit_timeSheetQuickNote.attr('submitting')) {
                    return false;
                } else {
                    $ts_ext_form_add_edit_timeSheetQuickNote.attr('submitting', true);
                    return true;
                }
            }, 'success': function (result) {
                $ts_ext_form_add_edit_timeSheetQuickNote.removeAttr('submitting');
                for (var fieldName in result.errors) {
                    setFloaterErrorMessage(fieldName, result.errors[fieldName]);
                }
                if (result.errors.length == 0) {
                    floaterClose();
                    ts_ext_reload();
                }
            },
            'error': function () {
                $ts_ext_form_add_edit_timeSheetQuickNote.removeAttr('submitting');
            }
        });
    });
</script>