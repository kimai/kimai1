<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->translate('preferences') ?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->translate('close') ?></a>
        </div>
    </div>
    <div class="menuBackground">
        <ul class="menu tabSelection">
            <li class="tab norm"><a href="#prefGeneral">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->translate('general') ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
            <li class="tab norm"><a href="#prefSublists">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->translate('sublists') ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
            <li class="tab norm"><a href="#prefList">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->translate('list') ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
        </ul>
    </div>
    <form id="core_prefs" action="processor.php" method="post">
        <input type="hidden" name="axAction" value="editPrefs"/>
        <input type="hidden" name="id" value="0"/>
        <div id="floater_tabs" class="floater_content">
            <fieldset id="prefGeneral">
                <ul>
                    <li>
                        <label for="skin"><?php echo $this->translate('skin') ?>:</label>
                        <?php echo $this->formSelect('skin', $this->skin()->getName(), null, $this->skins); ?>
                    </li>
                    <li>
                        <label for="password"><?php echo $this->translate('newPassword') ?>:</label>
                        <input type="password" name="password" size="15" id="password"/> <?php echo $this->translate('minLength') ?>
                    </li>
                    <li>
                        <label for="retypePassword"><?php echo $this->translate('retypePassword') ?>:</label>
                        <input type="password" name="retypePassword" size="15" id="retypePassword"/>
                    </li>
                    <li>
                        <label for="rate"><?php echo $this->translate('my_rate') ?>:</label>
                        <?php echo $this->formText('rate', str_replace('.', $this->kga['conf']['decimalSeparator'], $this->rate), [
                            'size' => 9
                        ]); ?>
                    </li>
                    <li>
                        <label for="lang"><?php echo $this->translate('lang') ?>:</label>
                        <?php echo $this->formSelect('lang', $this->kga->getLanguage(), null, $this->langs); ?>
                    </li>
                    <li>
                        <label for="timezone"><?php echo $this->translate('timezone') ?>:</label>
                        <?php echo $this->timeZoneSelect('timezone', $this->kga['timezone']); ?>
                    </li>
                    <li>
                        <label for="autoselection"></label>
                        <?php echo $this->formCheckbox('autoselection', '1', ['checked' => $this->kga->getSettings()->isUseAutoSelection()]);
                        echo $this->translate('autoselection') ?>
                    </li>
                    <li>
                        <label for="openAfterRecorded"></label>
                        <?php echo $this->formCheckbox('openAfterRecorded', '1', ['checked' => $this->kga->getSettings()->isShowAfterRecorded()]);
                        echo $this->translate('openAfterRecorded') ?>
                    </li>
                </ul>
            </fieldset>
            <fieldset id="prefSublists">
                <ul>
                    <li>
                        <?php echo $this->translate('sublistAnnotations') ?>:
                        <?php echo $this->formSelect('sublistAnnotations', $this->kga->getSettings()->getSublistAnnotationType(), null, [
                            $this->translate('timelabel'), $this->translate('export_extension:costs'), $this->translate('timelabel') . ' & ' . $this->translate('export_extension:costs')
                        ]); ?>
                    </li>
                    <li>
                        <label for="flip_project_display"></label>
                        <?php echo $this->formCheckbox('flip_project_display', '1', ['checked' => $this->kga->getSettings()->isFlipProjectDisplay()]),
                        $this->translate('flip_project_display') ?>
                    </li>
                    <li>
                        <label for="project_comment_flag"></label>
                        <?php echo $this->formCheckbox('project_comment_flag', '1', ['checked' => $this->kga->getSettings()->isShowProjectComment()]),
                        $this->translate('project_comment_flag') ?>
                    </li>
                    <li>
                        <label for="showIDs"></label>
                        <?php echo $this->formCheckbox('showIDs', '1', ['checked' => $this->kga->getSettings()->isShowIds()]),
                        $this->translate('showIDs') ?>
                    </li>
                    <li>
                        <label for="noFading"></label>
                        <?php echo $this->formCheckbox('noFading', '1', ['checked' => !$this->kga->getSettings()->isUseSmoothFading()]),
                        $this->translate('noFading') ?>
                    </li>
                    <li>
                        <label for="user_list_hidden"></label>
                        <?php echo $this->formCheckbox('user_list_hidden', '1', ['checked' => $this->kga->getSettings()->isUserListHidden()]),
                        $this->translate('user_list_hidden') ?>
                    </li>
                </ul>
            </fieldset>
            <fieldset id="prefList">
                <ul>
                    <li>
                        <label for="rowlimit"><?php echo $this->translate('rowlimit') ?>:</label>
                        <?php echo $this->formText('rowlimit', $this->kga->getSettings()->getRowLimit(), ['size' => 9]); ?>
                    </li>
                    <li>
                        <label for="hideClearedEntries"></label>
                        <?php echo $this->formCheckbox('hideClearedEntries', '1', ['checked' => $this->kga->getSettings()->isHideClearedEntries()]), $this->translate('hideClearedEntries') ?>
                    </li>
                    <li>
                        <?php echo $this->translate('quickdelete') ?>:
                        <?php echo $this->formSelect('quickdelete', $this->kga->getSettings()->getQuickDeleteType(), null, [$this->translate('quickdeleteHide'), $this->translate('quickdeleteShow'), $this->translate('quickdeleteShowConfirm')]); ?>
                    </li>
                    <li>
                        <label for="showCommentsByDefault"></label>
                        <?php echo $this->formCheckbox('showCommentsByDefault', '1', ['checked' => $this->kga->getSettings()->isShowComments()]), $this->translate('showCommentsByDefault') ?>
                    </li>
                    <?php if ($this->kga->isTrackingNumberEnabled()) { ?>
                    <li>
                        <label for="showTrackingNumber"></label>
                        <?php echo $this->formCheckbox('showTrackingNumber', '1', ['checked' => $this->kga->getSettings()->isShowTrackingNumber()]), $this->translate('showTrackingNumber') ?>
                    </li>
                    <?php } ?>
                    <li>
                        <label for="showBillability"></label>
                        <?php echo $this->formCheckbox('showBillability', '1', ['checked' => $this->kga->getSettings()->isShowBillability()]), $this->translate('showBillability') ?>
                    </li>
                    <li>
                        <label for="hideOverlapLines"></label>
                        <?php echo $this->formCheckbox('hideOverlapLines', '1', ['checked' => !$this->kga->getSettings()->isShowOverlapLines()]), $this->translate('hideOverlapLines') ?>
                    </li>
                    <li>
                        <label for="defaultLocation"><?php echo $this->translate('defaultLocation')?>:</label>
                        <?php echo $this->formText('defaultLocation', $this->kga->getSettings()->getDefaultLocation(), ['size' => 20]); ?>
                    </li>
                    <li>
                        <label for="showQuickNote"></label>
                        <?php echo $this->formCheckbox('showQuickNote', '1', ['checked' => $this->kga->getSettings()->isShowQuickNote()]), $this->translate('showQuickNote') ?>
                    </li>
                    <li>
                        <label for="inlineEditingOfDescriptions"></label>
                        <?php echo $this->formCheckbox('inlineEditingOfDescriptions', '1', ['checked' => $this->kga->getSettings()->isInlineEditingOfDescriptionsSet()]), $this->translate('inlineEditingOfDescriptions') ?>
                    </li>
                    <li>
                        <label for="table_time_format"></label>
                        <?php echo $this->translate('table_time_format')?>:
                        <?php echo $this->formText('table_time_format', $this->prefs['table_time_format'], ['size' => 20]); ?>
                    </li>
                </ul>
            </fieldset>
        </div>
        <div id="formbuttons">
	        <button class="btn_norm" type="button" onclick="floaterClose();"><?php echo $this->translate('cancel') ?></button>
            <input class="btn_ok" type="submit" value="<?php echo $this->translate('submit') ?>"/>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#floater_innerwrap').tabs({selected: 0});

        var $core_prefs = $('#core_prefs');
        $core_prefs.ajaxForm({
            beforeSubmit: function () {
                var $password = $('#password');
                var $retypePassword = $('#retypePassword');
                if (($password.val() != '' || $retypePassword.val() != '')
                    && !validatePassword($password.val(), $retypePassword.val())) {
                    return false;
                }
                if ($core_prefs.attr('submitting')) {
                    return false;
                } else {
                    $core_prefs.attr('submitting', true);
                    return true;
                }
            },
            success: function () {
                $core_prefs.removeAttr('submitting');
                window.location.reload();
            },
            error: function () {
                $core_prefs.removeAttr('submitting');
            }
        });
    });
</script>