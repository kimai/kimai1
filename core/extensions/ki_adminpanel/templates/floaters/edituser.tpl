{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {

	 var options = { 
		beforeSubmit:  function() { 

                	if ($('#password').val() != '' && !validatePassword($('#password').val(),$('#retypePassword').val()))
                	    return false;

                floaterClose();
            	},
    success: function() {
          hook_users_changed();
          adminPanel_extension_refreshSubtab('groups');
          return false;
          }
	    }; 
	 
	    $('#adminPanel_extension_form_editUser').ajaxForm(options); 

        }); 
    </script>
{/literal}

<div id="floater_innerwrap">
    
    <div id="floater_handle">
        <span id="floater_title">{$kga.lang.editUser}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
        </div>       
    </div>

    <div class="floater_content">

        <form id="adminPanel_extension_form_editUser" action="../extensions/ki_adminpanel/processor.php" method="post"> 
            <fieldset>
                
                <ul>
                    
                    <li>
                        <label for="name">{$kga.lang.username}:</label>
                        <input class="formfield" type="text" name="name" value="{$user_details.name|escape:'html'}" maxlength=20 size=20 />
                    </li> 

                    <li>
                        <label for="status">{$kga.lang.status}:</label>

        {if $user_details.status == 1}
                        <select name="status">
                            <option value="0" {if $user_details.status == 0}selected{/if}>{$kga.lang.adminUser} (!)</option>
                            <option value="1" {if $user_details.status == 1}selected{/if}>{$kga.lang.groupleader}</option>
                            <option value="2" {if $user_details.status == 2}selected{/if}>{$kga.lang.user}</option>
                        </select>
        {else}


            {if $curr_user == $user_details.name && $user_details.status == 0}                            
                {$kga.lang.admWarn}
            {else}                
                        <select name="status">
                            <option value="0" {if $user_details.status == 0}selected{/if}>{$kga.lang.adminUser} (!)</option>
                            <option value="2" {if $user_details.status == 2}selected{/if}>{$kga.lang.user}</option>
                        </select>
            {/if}              

        {/if}
                    </li>


                    <li>
                        <label for="password">{$kga.lang.newPassword}:</label>
                        <input class="formfield" type="password" name="password" size="9" id="password" /> {$kga.lang.minLength}
        {if $user_details.password == ""}
        
                        <br/>
                        <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/caution_mini.png" alt="Caution" valign=middle />
                        <strong style="color:red">{$kga.lang.nopasswordset}</strong>
        {/if}
                    </li>


                    <li>
                        <label for="retypePassword">{$kga.lang.retypePassword}:</label>
                        <input class="formfield" type="password" name="retypePassword" id="retypePassword" size="9" />
                    </li>


                    <li>
                        <label for="rate">{$kga.lang.rate}:</label>
                        <input class="formfield" type="text" name="rate" value="{$user_details.rate|number_format:2:$kga.conf.decimalSeparator:""|escape:'html'}" />
                    </li>


                    <li>
                        <label for="mail">{$kga.lang.mail}:</label>
                        <input class="formfield" type="text" name="mail" value="{$user_details.mail|escape:'html'}" />
                    </li>

                    <li>
                        <label for="alias">{$kga.lang.alias}:</label>
                        <input class="formfield" type="text" name="alias" value="{$user_details.alias|escape:'html'}" />
                    </li>

                    <li>
                        <label for="groups">{$kga.lang.group}:</label>
                        <select class="formfield" name="groups[]" size="5" multiple>
                            {html_options values=$groupIDs output=$groupNames selected=$selectedGroups}
                        </select>
                  	</li>

				</ul>

                <input name="id" type="hidden" value="{$user_details.userID}" />
                <input name="axAction" type="hidden" value="sendEditUser" />

                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}' />
                </div>

            </fieldset>
        </form>
    </div>
</div>
