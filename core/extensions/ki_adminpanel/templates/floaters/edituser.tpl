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
          hook_chgUsr();
          ap_ext_refreshSubtab('grp');
          return false;
          }
	    }; 
	 
	    $('#ap_ext_form_editusr').ajaxForm(options); 

        }); 
    </script>
{/literal}

<div id="floater_innerwrap">
    
    <div id="floater_handle">
        <span id="floater_title">{$kga.lang.editusr}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
        </div>       
    </div>

    <div class="floater_content">

        <form id="ap_ext_form_editusr" action="../extensions/ki_adminpanel/processor.php" method="post"> 
            <fieldset>
                
                <ul>
                    
                    <li>
                        <label for="usr_name">{$kga.lang.username}:</label>
                        <input class="formfield" type="text" name="usr_name" value="{$usr_details.usr_name|escape:'html'}" maxlength=20 size=20 />
                    </li> 

                    <li>
                        <label for="usr_sts">{$kga.lang.status}:</label>

        {if $usr_details.usr_sts == 1}
                        <select name="usr_sts">
                            <option value="0" {if $usr_details.usr_sts == 0}selected{/if}>{$kga.lang.adminusr} (!)</option>
                            <option value="1" {if $usr_details.usr_sts == 1}selected{/if}>{$kga.lang.groupleader}</option>
                            <option value="2" {if $usr_details.usr_sts == 2}selected{/if}>{$kga.lang.regusr}</option>
                        </select>
        {else}


            {if $curr_user == $usr_details.usr_name && $usr_details.usr_sts == 0}                            
                {$kga.lang.admWarn}
            {else}                
                        <select name="usr_sts">
                            <option value="0" {if $usr_details.usr_sts == 0}selected{/if}>{$kga.lang.adminusr} (!)</option>
                            <option value="2" {if $usr_details.usr_sts == 2}selected{/if}>{$kga.lang.regusr}</option>
                        </select>
            {/if}              

        {/if}
                    </li>


                    <li>
                        <label for="usr_pw">{$kga.lang.newPassword}:</label>
                        <input class="formfield" type="password" name="usr_pw" size="9" id="password" /> {$kga.lang.minLength}
        {if $usr_details.usr_pw == "no"}
        
                        <br/>
                        <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/caution_mini.png" alt="Caution" valign=middle />
                        <strong style="color:red">{$kga.lang.nopassword}</strong>
        {/if}
                    </li>


                    <li>
                        <label for="usr_pw">{$kga.lang.retypePassword}:</label>
                        <input class="formfield" type="password" name="retypePassword" id="retypePassword" size="9" />
                    </li>


                    <li>
                        <label for="usr_rate">{$kga.lang.rate}:</label>
                        <input class="formfield" type="text" name="usr_rate" value="{$usr_details.usr_rate|number_format:2:$kga.conf.decimalSeparator:""|escape:'html'}" />
                    </li>


                    <li>
                        <label for="usr_mail">{$kga.lang.mail}:</label>
                        <input class="formfield" type="text" name="usr_mail" value="{$usr_details.usr_mail|escape:'html'}" />
                    </li>

                    <li>
                        <label for="usr_alias">{$kga.lang.alias}:</label>
                        <input class="formfield" type="text" name="usr_alias" value="{$usr_details.usr_alias|escape:'html'}" />
                    </li>

                    <li>
                        <label for="usr_grp">{$kga.lang.group}:</label>
                        <select class="formfield" name="usr_grp">
                            {html_options values=$arr_grp_ID output=$arr_grp_name selected=$usr_details.usr_grp}
                        </select>
                  	</li>

				</ul>

                <input name="id" type="hidden" value="{$usr_details.usr_ID}" />
                <input name="axAction" type="hidden" value="sendEditUsr" />

                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}' />
                </div>

            </fieldset>
        </form>
    </div>
</div>
