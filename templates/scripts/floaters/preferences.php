<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->kga['lang']['preferences'] ?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->kga['lang']['close'] ?></a>
        </div>
    </div>
    <div class="menuBackground">
        <ul class="menu tabSelection">
            <li class="tab norm"><a href="#prefGeneral">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->kga['lang']['general'] ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
            <li class="tab norm"><a href="#prefSublists">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->kga['lang']['sublists'] ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
            <li class="tab norm"><a href="#prefList">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->kga['lang']['list'] ?></span>
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
                        <label for="skin"><?php echo $this->kga['lang']['skin'] ?>:</label>
                        <?php echo $this->formSelect('skin', $this->kga['conf']['skin'], null, $this->skins); ?>
                    </li>
                    <li>
                        <label for="password"><?php echo $this->kga['lang']['newPassword'] ?>:</label>
                        <input type="password" name="password" size="15" id="password"/> <?php echo $this->kga['lang']['minLength'] ?>
                    </li>
                    <li>
                        <label for="retypePassword"><?php echo $this->kga['lang']['retypePassword'] ?>:</label>
                        <input type="password" name="retypePassword" size="15" id="retypePassword"/>
                    </li>
                    <li>
                        <label for="rate"><?php echo $this->kga['lang']['my_rate'] ?>:</label>
                        <?php echo $this->formText('rate', str_replace('.', $this->kga['conf']['decimalSeparator'], $this->rate), array(
                            'size' => 9)); ?>
                    </li>
                    <li>
                        <label for="lang"><?php echo $this->kga['lang']['lang'] ?>:</label>
                        <?php echo $this->formSelect('lang', $this->kga['conf']['lang'], null, $this->langs); ?>
                    </li>
                    <li>
                        <label for="timezone"><?php echo $this->kga['lang']['timezone'] ?>:</label>
                        <?php echo $this->timeZoneSelect('timezone', $this->kga['timezone']); ?>
                    </li>
                    <li>
                        <label for="autoselection"></label>
                        <?php echo $this->formCheckbox('autoselection', '1', array('checked' => $this->kga['conf']['autoselection']));
                        echo $this->kga['lang']['autoselection'] ?>
                    </li>
                    <li>
                        <label for="openAfterRecorded"></label>
                        <?php echo $this->formCheckbox('openAfterRecorded', '1', array('checked' => isset($this->kga['conf']['openAfterRecorded']) && $this->kga['conf']['openAfterRecorded']));
                        echo $this->kga['lang']['openAfterRecorded'] ?>
                    </li>
                </ul>
            </fieldset>
            <fieldset id="prefSublists">
                <ul>
                    <li>
                        <?php echo $this->kga['lang']['sublistAnnotations'] ?>:
                        <?php echo $this->formSelect('sublistAnnotations', isset($this->kga['conf']['sublistAnnotations']) && $this->kga['conf']['sublistAnnotations'], null, array(
                            $this->kga['lang']['timelabel'], $this->kga['lang']['export_extension']['costs'], $this->kga['lang']['timelabel'] . ' & ' . $this->kga['lang']['export_extension']['costs']
                        )); ?>
                    </li>
                    <li>
                        <label for="flip_project_display"></label>
                        <?php echo $this->formCheckbox('flip_project_display', '1', array('checked' => $this->kga['conf']['flip_project_display'])),
                        $this->kga['lang']['flip_project_display'] ?>
                    </li>
                    <li>
                        <label for="project_comment_flag"></label>
                        <?php echo $this->formCheckbox('project_comment_flag', '1', array('checked' => $this->kga['conf']['project_comment_flag'])),
                        $this->kga['lang']['project_comment_flag'] ?>
                    </li>
                    <li>
                        <label for="showIDs"></label>
                        <?php echo $this->formCheckbox('showIDs', '1', array('checked' => $this->kga['conf']['showIDs'])),
                        $this->kga['lang']['showIDs'] ?>
                    </li>
                    <li>
                        <label for="noFading"></label>
                        <?php echo $this->formCheckbox('noFading', '1', array('checked' => $this->kga['conf']['noFading'])),
                        $this->kga['lang']['noFading'] ?>
                    </li>
                    <li>
                        <label for="user_list_hidden"></label>
                        <?php echo $this->formCheckbox('user_list_hidden', '1', array('checked' => $this->kga['conf']['user_list_hidden'])),
                        $this->kga['lang']['user_list_hidden'] ?>
                    </li>
                </ul>
            </fieldset>
            <fieldset id="prefList">
                <ul>
                    <li>
                        <label for="rowlimit"><?php echo $this->kga['lang']['rowlimit'] ?>:</label>
                        <?php echo $this->formText('rowlimit', $this->kga['conf']['rowlimit'], array('size' => 9)); ?>
                    </li>
                    <li>
                        <label for="hideClearedEntries"></label>
                        <?php echo $this->formCheckbox('hideClearedEntries', '1', array('checked' => $this->kga['conf']['hideClearedEntries'])), $this->kga['lang']['hideClearedEntries'] ?>
                    </li>
                    <li>
                        <?php echo $this->kga['lang']['quickdelete'] ?>:
                        <?php echo $this->formSelect('quickdelete', $this->kga['conf']['quickdelete'], null, array($this->kga['lang']['quickdeleteHide'], $this->kga['lang']['quickdeleteShow'], $this->kga['lang']['quickdeleteShowConfirm'])); ?>
                    </li>
                    <li>
                        <label for="showCommentsByDefault"></label>
                        <?php echo $this->formCheckbox('showCommentsByDefault', '1', array('checked' => isset($this->kga['conf']['showCommentsByDefault']) && $this->kga['conf']['showCommentsByDefault'])), $this->kga['lang']['showCommentsByDefault'] ?>
                    </li>
                    <li>
                        <label for="showTrackingNumber"></label>
                        <?php echo $this->formCheckbox('showTrackingNumber', '1', array('checked' => isset($this->kga['conf']['showTrackingNumber']) && $this->kga['conf']['showTrackingNumber'])), $this->kga['lang']['showTrackingNumber'] ?>
                    </li>
                    <li>
                        <label for="hideOverlapLines"></label>
                        <?php echo $this->formCheckbox('hideOverlapLines', '1', array('checked' => isset($this->kga['conf']['hideOverlapLines']) && $this->kga['conf']['hideOverlapLines'])), $this->kga['lang']['hideOverlapLines'] ?>
                    </li>
                    <li>
                        <label for="defaultLocation"><?php echo $this->kga['lang']['defaultLocation']?>:</label>
                        <?php echo $this->formText('defaultLocation', $this->kga['conf']['defaultLocation'], array('size' => 20)); ?>
                    </li>
                    <li>
                        <label for="showQuickNote"></label>
                        <?php echo $this->formCheckbox('showQuickNote', '1', array('checked' => isset($this->kga['conf']['showQuickNote']) && $this->kga['conf']['showQuickNote'])), $this->kga['lang']['showQuickNote'] ?>
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
        var $core_prefs = $('#core_prefs');
        var options = {
            beforeSubmit: function () {
                if (($('#password').val() != '' || $('#retypePassword').val() != '')
                    && !validatePassword($('#password').val(), $('#retypePassword').val())) {
                    return false;
                }
                if ($core_prefs.attr('submitting')) {
                    return false;
                }
                else {
                    $core_prefs.attr('submitting', true);
                    return true;
                }
            },
            success: function () {
                $core_prefs.removeAttr('submitting');
                window.location.reload();
            },
            'error': function () {
                $core_prefs.removeAttr('submitting');
            }
        };
        $core_prefs.ajaxForm(options);
    });
</script>