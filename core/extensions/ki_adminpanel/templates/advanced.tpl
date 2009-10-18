{literal}    
    <script type="text/javascript">
        function cb() {
            $("#ap_ext_form_editadv_submit").blur();
            $("#ap_ext_output").width($(".ap_ext_panel_header").width()-22);
            $("#ap_ext_output").fadeIn(500,function(){
                $("#ap_ext_output").fadeOut(4000);
            });
        }
        $(document).ready(function() {
            $('#ap_ext_form_editadv').ajaxForm({target:'#ap_ext_output',success:cb}); 
        }); 
    </script>
{/literal}

<div class="content">
    
    <div id="ap_ext_output"></div>
        
    <form id="ap_ext_form_editadv" action="../extensions/ki_adminpanel/processor.php" method="post">
        
        <fieldset class="ap_ext_advanced">
            <div>
                <input type="text" name="adminmail" size="20" value="{$kga.conf.adminmail}" class="formfield"> {$kga.lang.adminmail}
            </div>
            <div>
                <input type="text" name="logintries" size="2" value="{$kga.conf.loginTries}" class="formfield"> {$kga.lang.logintries}
            </div>
            <div>
                <input type="text" name="loginbantime" size="4" value="{$kga.conf.loginBanTime}" class="formfield"> {$kga.lang.bantime}
            </div>
            <div>
                <select name="charset" class="formfield">
                    {html_options values=$kga.charsets output=$kga.charset_descr selected=$kga.conf.charset}
                </select> {$kga.lang.charset} {$kga.lang.charset_msg}
            </div>

            <div id="ap_ext_checkupdate">
                <a href="javascript:ap_ext_checkupdate({$kga.revision});">{$kga.lang.checkupdate}</a>
            </div>
        
            <input name="axAction" type="hidden" value="sendEditAdvanced" />
        
            <div id="formbuttons">
                <input id="ap_ext_form_editadv_submit" class='btn_ok' type='submit' value='{$kga.lang.save}' />
            </div>
        
        </fieldset>
        
    </form>

</div>