{literal}    
    <script type="text/javascript">
        function cb(data) {
            if (data=="ok") {
              window.location.reload();
              return;
            }
            $("#ap_ext_form_editadv_submit").blur();
            $("#ap_ext_output").width($(".ap_ext_panel_header").width()-22);
            $("#ap_ext_output").fadeIn(fading_enabled?500:0,function(){
                $("#ap_ext_output").fadeOut(fading_enabled?4000:0);
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
                <input type="text" name="adminmail" size="20" value="{$kga.conf.adminmail|escape:'html'}" class="formfield"> {$kga.lang.adminmail}
            </div>
            <div>
                <input type="text" name="logintries" size="2" value="{$kga.conf.loginTries|escape:'html'}" class="formfield"> {$kga.lang.logintries}
            </div>
            <div>
                <input type="text" name="loginbantime" size="4" value="{$kga.conf.loginBanTime|escape:'html'}" class="formfield"> {$kga.lang.bantime}
            </div>

            <div id="ap_ext_checkupdate">
                <a href="javascript:ap_ext_checkupdate();">{$kga.lang.checkupdate}</a>
            </div>

            <div>
                {$kga.lang.lang}: <select name="language" class="formfield">
                    {html_options values=$languages output=$languages selected=$kga.conf.language}
                </select>
            </div>

            <div>
               <input type="checkbox" name="show_sensible_data" {if $kga.show_sensible_data}checked="checked"{/if} value="1" class="formfield"> {$kga.lang.show_sensible_data}
            </div>

            <div>
               <input type="checkbox" name="show_update_warn" {if $kga.show_update_warn}checked="checked"{/if} value="1" class="formfield"> {$kga.lang.show_update_warn}
            </div>

            <div>
               <input type="checkbox" name="check_at_startup" {if $kga.check_at_startup}checked="checked"{/if} value="1" class="formfield"> {$kga.lang.check_at_startup}
            </div>

            <div>
               <input type="checkbox" name="show_daySeperatorLines" {if $kga.show_daySeperatorLines}checked="checked"{/if} value="1" class="formfield"> {$kga.lang.show_daySeperatorLines}
            </div>

            <div>
               <input type="checkbox" name="show_gabBreaks" {if $kga.show_gabBreaks}checked="checked"{/if} value="1" class="formfield"> {$kga.lang.show_gabBreaks}
            </div>

            <div>
               <input type="checkbox" name="show_RecordAgain" {if $kga.show_RecordAgain}checked="checked"{/if} value="1" class="formfield"> {$kga.lang.show_RecordAgain}
            </div>

            <div>
               <input type="checkbox" name="show_TrackingNr" {if $kga.show_TrackingNr}checked="checked"{/if} value="1" class="formfield"> {$kga.lang.show_TrackingNr}
            </div>

            <div>
               <input type="text" name="currency_name" size="8" value="{$kga.currency_name|escape:'html'}" class="formfield"> {$kga.lang.currency_name}
            </div>

            <div>
               <input type="text" name="currency_sign" size="2" value="{$kga.currency_sign|escape:'html'}" class="formfield"> {$kga.lang.currency_sign}
            </div>

            <div>
               <input type="checkbox" name="currency_first" {if $kga.conf.currency_first}checked="checked"{/if} value="1" class="formfield"> {$kga.lang.currency_first}
            </div>

            <div>
               <input type="text" name="date_format_2" size="8" value="{$kga.date_format.2|escape:'html'}" class="formfield"> {$kga.lang.display_date_format}
            </div>

            <div>
               <input type="text" name="date_format_0" size="8" value="{$kga.date_format.0|escape:'html'}" class="formfield"> {$kga.lang.display_currentDate_format}
            </div>

            <div>
               <input type="text" name="date_format_1" size="8" value="{$kga.date_format.1|escape:'html'}" class="formfield"> {$kga.lang.table_date_format}
            </div>

            <div>
               <input type="checkbox" name="durationWithSeconds" {if $kga.conf.durationWithSeconds}checked="checked"{/if} value="1" class="formfield"> {$kga.lang.durationWithSeconds}
            </div>

            <div>
               {$kga.lang.round_time} <select name="roundPrecision" class="formfield">
                 <option value="0" {if $kga.conf.roundPrecision==0}selected="selected"{/if}>-</option>
                 <option value="1" {if $kga.conf.roundPrecision==1}selected="selected"{/if}>1</option>
                 <option value="5" {if $kga.conf.roundPrecision==5}selected="selected"{/if}>5</option>
                 <option value="10" {if $kga.conf.roundPrecision==10}selected="selected"{/if}>10</option>
                 <option value="15" {if $kga.conf.roundPrecision==15}selected="selected"{/if}>15</option>
                 <option value="15" {if $kga.conf.roundPrecision==20}selected="selected"{/if}>20</option>
                 <option value="30" {if $kga.conf.roundPrecision==30}selected="selected"{/if}>30</option>
               </select> {$kga.lang.round_time_minute}
            </div>

            <div>
               {$kga.lang.decimal_separator}: <input type="text" name="decimalSeparator" size="1" value="{$kga.conf.decimalSeparator|escape:'html'}" class="formfield">
            </div>

            <div>
               <select name="defaultTimezone">
                    {html_options values=$timezones output=$timezones selected=$kga.conf.defaultTimezone}
                </select> {$kga.lang.defaultTimezone}
            </div>

            <div>
               <input type="checkbox" name="exactSums" {if $kga.conf.exactSums}checked="checked"{/if} value="1" class="formfield"> {$kga.lang.exactSums}
            </div>

        
            <input name="axAction" type="hidden" value="sendEditAdvanced" />
        
            <div id="formbuttons">
                <input id="ap_ext_form_editadv_submit" class='btn_ok' type='submit' value='{$kga.lang.save}' />
            </div>
        
        </fieldset>
        
    </form>

</div>