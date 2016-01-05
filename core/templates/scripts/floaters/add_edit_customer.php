<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php 
            if (isset($id)) {
                echo $this->kga['lang']['edit'] . ': ' . $this->kga['lang']['customer'];
            } else {
                echo $this->kga['lang']['new_customer'];
            }
            ?></span>
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
            <li class="tab norm"><a href="#address">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->kga['lang']['address'] ?></span>
                    <span class="cc">&nbsp;</span>
                </a></li>
            <li class="tab norm"><a href="#contact">
                    <span class="aa">&nbsp;</span>
                    <span class="bb"><?php echo $this->kga['lang']['contact'] ?></span>
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
    <form id="add_edit_customer" action="processor.php" method="post">
        <input type="hidden" name="customerFilter" value="0"/>
        <input type="hidden" name="axAction" value="add_edit_CustomerProjectActivity"/>
        <input type="hidden" name="axValue" value="customer"/>
        <input type="hidden" name="id" value="<?php echo $this->id ?>"/>
        <div id="floater_tabs" class="floater_content">
            <fieldset id="general">
                <ul>
                    <li>
                        <label for="name"><?php echo $this->kga['lang']['customer'] ?>*:</label>
                        <?php echo $this->formText('name', $this->name, array('required' => 'required')); ?>
                    </li>
                    <li>
                        <label for="vat"><?php echo $this->kga['lang']['vat'] ?>:</label>
                        <?php echo $this->formText('vat', $this->vat); ?>
                    </li>
                    <li>
                        <label for="visible"><?php echo $this->kga['lang']['visibility'] ?>:</label>
                        <?php echo $this->formCheckbox('visible', '1', array('checked' => $this->visible || !$this->id)); ?>
                    </li>
                    <li>
                        <label for="password"><?php echo $this->kga['lang']['password'] ?>:</label>
                        <div class="multiFields">
                            <?php echo $this->formPassword('password', '', array(
                                'cols' => 30,
                                'rows' => 3,
                                'disabled' => (!$this->password) ? 'disabled' : ''
                            )); ?><br/>
                            <?php echo $this->formCheckbox('no_password', '1', array('class' => 'disableInput', 'checked' => !$this->password));
                            echo $this->kga['lang']['nopassword'] ?>
                        </div>
                    </li>
                    <li>
                        <label for="timezone"><?php echo $this->kga['lang']['timezone'] ?>:</label>
                        <?php echo $this->timeZoneSelect('timezone', $this->timezone); ?>
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
                        <label for="customerGroups"><?php echo $this->kga['lang']['groups'] ?>:</label>
                        <?php echo $this->formSelect('customerGroups[]', $this->selectedGroups, array(
                            'class' => 'formfield',
                            'id' => 'customerGroups',
                            'multiple' => 'multiple',
                            'size' => 3,
                            'style' => 'width:255px'), $this->groups); ?>
                    </li>
                </ul>
            </fieldset>
            <fieldset id="address">
                <ul>
                    <li>
                        <label for="company"><?php echo $this->kga['lang']['company'] ?>:</label>
                        <?php echo $this->formText('company', $this->company); ?>
                    </li>
                    <li>
                        <label for="contactPerson"><?php echo $this->kga['lang']['contactPerson'] ?>:</label>
                        <?php echo $this->formText('contactPerson', $this->contact); ?>
                    </li>
                    <li>
                        <label for="street"><?php echo $this->kga['lang']['street'] ?>:</label>
                        <?php echo $this->formText('street', $this->street); ?>
                    </li>
                    <li>
                        <label for="zipcode"><?php echo $this->kga['lang']['zipcode'] ?>:</label>
                        <?php echo $this->formText('zipcode', $this->zipcode); ?>
                    </li>
                    <li>
                        <label for="city"><?php echo $this->kga['lang']['city'] ?>:</label>
                        <?php echo $this->formText('city', $this->city); ?>
                    </li>
                </ul>
            </fieldset>
            <fieldset id="contact">
                <ul>
                    <li>
                        <label for="phone"><?php echo $this->kga['lang']['telephon'] ?>:</label>
                        <?php echo $this->formText('phone', $this->phone); ?>
                    </li>
                    <li>
                        <label for="fax"><?php echo $this->kga['lang']['fax'] ?>:</label>
                        <?php echo $this->formText('fax', $this->fax); ?>
                    </li>
                    <li>
                        <label for="mobile"><?php echo $this->kga['lang']['mobilephone'] ?>:</label>
                        <?php echo $this->formText('mobile', $this->mobile); ?>
                    </li>
                    <li>
                        <label for="mail"><?php echo $this->kga['lang']['mail'] ?>:</label>
                        <?php echo $this->formText('mail', $this->mail); ?>
                    </li>
                    <li>
                        <label for="homepage"><?php echo $this->kga['lang']['homepage'] ?>:</label>
                        <?php echo $this->formText('homepage', $this->homepage); ?>
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
        $('.disableInput').click(function () {
            var input = $(this);
            if (input.is(':checked')) {
                input.siblings().prop("disabled", "disabled");
            } else {
                input.siblings().prop("disabled", "");
            }
        });
        var $add_edit_customer = $('#add_edit_customer');
        $add_edit_customer.ajaxForm({
            'beforeSubmit': function () {
                clearFloaterErrorMessages();
                if ($add_edit_customer.attr('submitting')) {
                    return false;
                }
                else {
                    $add_edit_customer.attr('submitting', true);
                    return true;
                }
            },
            'success': function (result) {
                $add_edit_customer.removeAttr('submitting');
                for (var fieldName in result.errors) {
                    setFloaterErrorMessage(fieldName, result.errors[fieldName]);
                }
                if (result.errors.length == 0) {
                    floaterClose();
                    hook_customers_changed();
                }
            },
            'error': function () {
                $add_edit_customer.removeAttr('submitting');
            }
        });
    });
</script>