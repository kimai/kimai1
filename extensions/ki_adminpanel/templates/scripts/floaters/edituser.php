<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->kga['lang']['editUser'] ?></span>
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
            <li class="tab norm"><a href="#groupstab">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->kga['lang']['groups'] ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
        </ul>
    </div>
    <div class="floater_content">
        <form id="adminPanel_extension_form_editUser" action="../extensions/ki_adminpanel/processor.php" method="post">
            <input type="hidden" name="id" value="<?php echo $this->user_details['userID'] ?>"/>
            <input type="hidden" name="axAction" value="sendEditUser"/>
            <fieldset id="general">
                <ul>
                    <li>
                        <label for="name"><?php echo $this->kga['lang']['username'] ?>:</label>
                        <input class="formfield" type="text" id="name" name="name" value="<?php echo $this->escape($this->user_details['name']) ?>" maxlength=20 size=20/>
                    </li>
                    <li>
                        <label for="globalRoleID"><?php echo $this->kga['lang']['globalRole'] ?>:</label>
                        <?php echo $this->formSelect('globalRoleID', $this->user_details['globalRoleID'], array(
                            'class' => 'formfield'), $this->globalRoles); ?>
                    </li>
                    <li>
                        <label for="password"><?php echo $this->kga['lang']['newPassword'] ?>:</label>
                        <input class="formfield" type="password" name="password" size="9" id="password"/> <?php echo $this->kga['lang']['minLength'] ?>
                        <?php if ($this->user_details['password'] == ""): ?>
                            <br/>
                            <img src="<?php echo $this->skin('grfx/caution_mini.png'); ?>" alt="Caution" valign="middle"/>
                            <strong style="color:red"><?php echo $this->kga['lang']['nopasswordset'] ?></strong>
                        <?php endif; ?>
                    </li>
                    <li>
                        <label for="retypePassword"><?php echo $this->kga['lang']['retypePassword'] ?>:</label>
                        <input class="formfield" type="password" name="retypePassword" id="retypePassword" size="9"/>
                    </li>
                    <li>
                        <label for="rate"><?php echo $this->kga['lang']['rate'] ?>:</label>
                        <input class="formfield" type="text" id="rate" name="rate" value="<?php echo $this->escape(str_replace('.', $this->kga['conf']['decimalSeparator'], $this->user_details['rate'])); ?>"/>
                    </li>
                    <li>
                        <label for="mail"><?php echo $this->kga['lang']['mail'] ?>:</label>
                        <input class="formfield" type="text" id="mail" name="mail" value="<?php echo $this->escape($this->user_details['mail']) ?>"/>
                    </li>
                    <li>
                        <label for="alias"><?php echo $this->kga['lang']['alias'] ?>:</label>
                        <input class="formfield" type="text" id="alias" name="alias" value="<?php echo $this->escape($this->user_details['alias']) ?>"/>
                    </li>
                </ul>
            </fieldset>
            <fieldset id="groupstab">
                <table class="groupsTable">
                    <tr>
                        <td><label><?php echo $this->kga['lang']['groups'] ?>:</label></td>
                        <td><label><?php echo $this->kga['lang']['membershipRole'] ?>:</label></td>
                    </tr>
                    <?php

                    $selectArray = array(-1 => '');
                    $assignedGroups = array();
                    foreach ($this->groups as $group) {
                        if (array_key_exists($group['groupID'], $this->memberships)) {
                            $group['membershipRoleID'] = $this->memberships[$group['groupID']];
                            $assignedGroups[] = $group;
                        } else {
                            $selectArray[$group['groupID']] = $group['name'];
                        }
                    }

                    foreach ($assignedGroups as $assignedGroup) {
                        ?>
                        <tr>
                            <td>
                                <?php echo $this->escape($assignedGroup['name']), $this->formHidden('assignedGroups[]', $assignedGroup['groupID']); ?>
                            </td>
                            <td>
                                <?php echo $this->formSelect('membershipRoles[]', $assignedGroup['membershipRoleID'], array('size' => 1, 'multiple' => false), $this->membershipRoles); ?>
                            </td>
                            <td>
                                <a class="deleteButton">
                                    <img src="<?php echo $this->skin('grfx/close.png'); ?>" width="22" height="16"/>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr class="addRow" <?php if (count($selectArray) <= 1): ?> style="display:none" <?php endif; ?> >
                        <td> <?php
                            echo $this->formSelect('newGroup', null, null, $selectArray); ?> </td>
                    </tr>
                </table>
            </fieldset>
            <div id="formbuttons">
                <input class='btn_norm' type='button' value='<?php echo $this->kga['lang']['cancel'] ?>' onclick='floaterClose();return false;'/>
                <input class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['submit'] ?>'/>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#floater_innerwrap').tabs({selected: 0});
        var options = {
            beforeSubmit: function () {

                var oldGlobalRoleID = <?php echo $this->user_details['globalRoleID']; ?>;

                if ($('#globalRoleID').val() != oldGlobalRoleID && $('input[name="id"]').val() == userID) {
                    var message = "<?php echo $this->pureJsEscape($this->kga['lang']['confirmations']['ownGlobalRoleChange']); ?>";
                    message = message.replace(/%OLD%/, $("#globalRoleID>option[value='" + oldGlobalRoleID + "']").text());
                    message = message.replace(/%NEW%/, $("#globalRoleID>option:selected").text());
                    var accepted = confirm(message);

                    if (!accepted) {
                        return false;
                    }
                }

                if ($('#password').val() != '' && !validatePassword($('#password').val(), $('#retypePassword').val())) {
                    return false;
                }

                clearFloaterErrorMessages();

                if ($('#adminPanel_extension_form_editUser').attr('submitting')) {
                    return false;
                }
                else {
                    $('#adminPanel_extension_form_editUser').attr('submitting', true);
                    return true;
                }
            },
            success: function (result) {
                $('#adminPanel_extension_form_editUser').removeAttr('submitting');

                for (var fieldName in result.errors) {
                    setFloaterErrorMessage(fieldName, result.errors[fieldName]);
                }

                if (result.errors.length == 0) {
                    hook_users_changed();
                    adminPanel_extension_refreshSubtab('groups');
                    floaterClose();
                }

                return false;
            },
            'error': function () {
                $('#adminPanel_extension_form_editUser').removeAttr('submitting');
            }
        };

        var memberships = <?php echo json_encode($this->membershipRoles); ?>;

        $('#adminPanel_extension_form_editUser').ajaxForm(options);
        
        function deleteButtonClicked() {
            var row = $(this).parent().parent()[0];
            var id = $('#groupsTable', row).val();
            var text = $('td', row).first().text().trim();
            $('#newGroup').append('<option label = "' + text + '" value = "' + id + '">' + text + '</option>');
            $(row).remove();

            if ($('#newGroup option').length > 1) {
                $('#groupstab .addRow').show();
            }
        }

        $('#groupstab .deleteButton').click(deleteButtonClicked);

        $('#newGroup').change(function () {
            if ($(this).val() == -1) {
                return;
            }

            var membershipRoleSelect = '<select name="membershipRoles[]">';
            $.each(memberships, function (key, value) {
                membershipRoleSelect += '<option value="' + key + '">' + value + '</option>';
            });
            membershipRoleSelect += '</select>';

            var row = $('<tr>' +
                '<td>' + $('option:selected', this).text() + '<input type="hidden" name="assignedGroups[]" value="' + $(this).val() + '"/></td>' +
                '<td>' + membershipRoleSelect + '</td>' +
                '<td> <a class="deleteButton">' +
                '<img src="../skins/' + skin + '/grfx/close.png" width="22" height="16" />' +
                '</a> </td>' +
                '</tr>');
            $('#groupstab .groupsTable tr.addRow').before(row);
            $('.deleteButton', row).click(deleteButtonClicked);

            $('option:selected', this).remove();

            $(this).val(-1);

            if ($('option', this).length <= 1) {
                $('#groupstab .addRow').hide();
            }
        });

        // uniform will mess up cloning select elements, which already are "uniformed"
        // maybe the issue is the same? https://github.com/pixelmatrix/uniform/pull/138
//               $("select, input:checkbox, input:radio, input:file").uniform();
        var optionsToRemove = new Array();
        $('select.groups').each(function (index) {
            if ($(this).val() != '') {
                $(this).children('[value=""]').remove();
                optionsToRemove.push($(this).val());
            }
        });
        var len = 0;
        for (var i = 0, len = optionsToRemove.length; i < len; i++) {
            $('.groups option[value="' + optionsToRemove[i] + '"]').not(':selected').remove();
        }
        var previousValue;
        var previousText;
        $('.groups').on('focus', function () {
            previousValue = this.value;
            previousText = $(this).children('[value="' + previousValue + '"]').text();
        }).on('change', function () {
            if (previousValue != '') {
                // the value we "deselected" has to be added to all other dropdowns to select it again
                $('.groups').each(function (index) {
                    if ($(this).children('[value="' + previousValue + '"]').length == 0) {
                        $(this).append('<option label="' + previousText + '" value="' + previousValue + '">' + previousText + '</option>');
                    }
                });
            }
            // add a new one if the value is in the last field, the value is not empty and there are more options to choose from
            if ($(this).val() != '' && $(this).closest('tr').next().length <= 0 && $(this).children().length > 2) {
                var label = $(this).val();
                $(this).children('[value=""]').remove();
                var tr = $(this).closest('tr');
                var newSelect = tr.clone();
                newSelect.find('select').prepend('<option value=""></option>');
                newSelect.find('select').val('');
                newSelect.find('option[value="' + label + '"]').remove();
                tr.after(newSelect);
            }
            return true;
        });
    });
</script>