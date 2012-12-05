    <script type="text/javascript"> 
        $(document).ready(function() {

	 var options = { 
		beforeSubmit:  function() { 

                	if ($('#password').val() != '' && !validatePassword($('#password').val(),$('#retypePassword').val()))
                	    return false;

            	},
    success: function(result) {
        for (var fieldName in result.errors)
          setFloaterErrorMessage(fieldName,result.errors[fieldName]);
        
        if (result.success) {
          hook_users_changed();
          adminPanel_extension_refreshSubtab('groups');
          floaterClose();
        }

        return false;
    }
	    }; 
	 
	    $('#adminPanel_extension_form_editUser').ajaxForm(options); 

        }); 
    </script>

<div id="floater_innerwrap">
    
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->kga['lang']['editUser']?></span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();"><?php echo $this->kga['lang']['close']?></a>
        </div>       
    </div>

    <div class="floater_content">

        <form id="adminPanel_extension_form_editUser" action="../extensions/ki_adminpanel/processor.php" method="post"> 
            <fieldset>
                
                <ul>
                    
                    <li>
                        <label for="name"><?php echo $this->kga['lang']['username']?>:</label>
                        <input class="formfield" type="text" id="name" name="name" value="<?php echo $this->escape($this->user_details['name'])?>" maxlength=20 size=20 />
                    </li> 

                    <li>
                        <label for="status"><?php echo $this->kga['lang']['status']?>:</label>

        <?php if ($this->user_details['status'] == 1): ?>
                        <select id="status" name="status">
                            <option value="0" <?php if ($this->user_details['status'] == 0): ?> selected<?php endif; ?>> <?php echo $this->kga['lang']['adminUser']?> (!)</option>
                            <option value="1" <?php if ($this->user_details['status'] == 1): ?> selected<?php endif; ?>> <?php echo $this->kga['lang']['groupleader']?></option>
                            <option value="2" <?php if ($this->user_details['status'] == 2): ?> selected<?php endif; ?>> <?php echo $this->kga['lang']['user']?></option>
                        </select>
        <?php else: ?>


            <?php if ($this->curr_user == $this->user_details['name'] && $this->user_details['status'] == 0): ?>
                <?php echo $this->kga['lang']['admWarn']?>
            <?php else: ?>
                        <select id="status" name="status">
                            <option value="0" <?php if ($this->user_details['status'] == 0): ?> selected<?php endif; ?>> <?php echo $this->kga['lang']['adminUser']?> (!)</option>
                            <option value="2" <?php if ($this->user_details['status'] == 2): ?> selected<?php endif; ?>> <?php echo $this->kga['lang']['user']?></option>
                        </select>
            <?php endif; ?>

        <?php endif; ?>
                    </li>


                    <li>
                        <label for="password"><?php echo $this->kga['lang']['newPassword']?>:</label>
                        <input class="formfield" type="password" id="password" name="password" size="9" id="password" /> <?php echo $this->kga['lang']['minLength']?>
        <?php if ($this->user_details['password'] == ""): ?>
        
                        <br/>
                        <img src="../skins/<?php echo $this->escape($this->kga['conf']['skin'])?>/grfx/caution_mini.png" alt="Caution" valign="middle" />
                        <strong style="color:red"><?php echo $this->kga['lang']['nopasswordset']?></strong>
        <?php endif; ?>
                    </li>


                    <li>
                        <label for="retypePassword"><?php echo $this->kga['lang']['retypePassword']?>:</label>
                        <input class="formfield" type="password" id="retypePassword" name="retypePassword" id="retypePassword" size="9" />
                    </li>


                    <li>
                        <label for="rate"><?php echo $this->kga['lang']['rate']?>:</label>
                        <input class="formfield" type="text" id="rate" name="rate" value="<?php echo $this->escape(number_format($this->user_details['rate'], 2, $this->kga['conf']['decimalSeparator'],""))?>" />
                    </li>


                    <li>
                        <label for="mail"><?php echo $this->kga['lang']['mail']?>:</label>
                        <input class="formfield" type="text" id="mail" name="mail" value="<?php echo $this->escape($this->user_details['mail'])?>" />
                    </li>

                    <li>
                        <label for="alias"><?php echo $this->kga['lang']['alias']?>:</label>
                        <input class="formfield" type="text" id="alias" name="alias" value="<?php echo $this->escape($this->user_details['alias'])?>" />
                    </li>

                    <li>
                        <label for="groups"><?php echo $this->kga['lang']['group']?>:</label>
                        <?php echo $this->formSelect('groups[]', $this->selectedGroups, array(
                          'class' => "formfield",
                          'size' => "5",
                          'multiple' => 'multiple'), $this->groups); ?>
                  	</li>

				</ul>

                <input name="id" type="hidden" value="<?php echo $this->user_details['userID']?>" />
                <input name="axAction" type="hidden" value="sendEditUser" />

                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='<?php echo $this->kga['lang']['cancel']?>' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['submit']?>' />
                </div>

            </fieldset>
        </form>
    </div>
</div>
