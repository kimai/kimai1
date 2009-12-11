{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            $('#core_prefs').ajaxForm(function() { 
                //floaterClose();
                window.location.reload();
            }); 
        }); 
    </script>
{/literal}

<div id="floater_innerwrap">
    
    <div id="floater_handle">
        <span id="floater_title">{$kga.lang.preferences}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
        </div>       
    </div>
    
    <div id="floater_content"><div id="floater_dimensions">

        <form id="core_prefs" action="processor.php" method="post"> 
            <fieldset>

                <ul>
                
                    <li>
                        <label for="skin">{$kga.lang.skin}:</label>
                        <select name="skin">
                            {html_options values=$skins output=$skins selected=$kga.conf.skin}
                        </select>
                    </li>

                    <li>
                        <label for="pw">{$kga.lang.newPassword}:</label>
                        <input type="password" name="pw" size="9" id="focus" /> {$kga.lang.minLength}
                    </li>

                    <li>
                        <label for="rate">{$kga.lang.my_rate}:</label>
                        <input type="text" name="rate" size="9" value="{$rate}"/>
                    </li>

                    <li>
                        <label for="rowlimit">{$kga.lang.rowlimit}:</label>
                        <input type="text" name="rowlimit" value="{$kga.conf.rowlimit}" size="9" />
                    </li>

                    <li>
                        <label for="lang">{$kga.lang.lang}:</label>
                        <select name="lang">
                            {html_options values=$langs output=$langs selected=$kga.conf.lang}
                        </select>
                    </li>

                    <li>
                        <label for="autoselection"></label>
                        <input type="checkbox" name="autoselection" value="1" {if $kga.conf.autoselection}checked{/if} /> {$kga.lang.autoselection}
                    </li>

                    <li>
                        <label for="quickdelete"></label>
                        <input type="checkbox" name="quickdelete" value="1" {if $kga.conf.quickdelete}checked{/if} /> {$kga.lang.quickdelete}
                    </li>

                    <li>
                        <label for="flip_pct_display"></label>
                        <input type="checkbox" name="flip_pct_display" value="1" {if $kga.conf.flip_pct_display}checked{/if} /> {$kga.lang.flip_pct_display}
                    </li>
                    <li>
                        <label for="pct_comment_flag"></label>
                        <input type="checkbox" name="pct_comment_flag" value="1" {if $kga.conf.pct_comment_flag}checked{/if} /> {$kga.lang.pct_comment_flag}
                    </li>
                    <li>
                        <label for="showIDs"></label>
                        <input type="checkbox" name="showIDs" value="1" {if $kga.conf.showIDs}checked{/if} /> {$kga.lang.showIDs}
                    </li>
                    <li>
                        <label for="noFading"></label>
                        <input type="checkbox" name="noFading" value="1" {if $kga.conf.noFading}checked{/if} /> {$kga.lang.noFading}
                    </li>
                    
                </ul>
                
                <input name="axAction" type="hidden" value="editPrefs" />   
                <input name="id" type="hidden" value="0" />   
                             
                <div id="formbuttons">
                    <input class='btn_norm' type='button' value='{$kga.lang.cancel}' onClick='floaterClose(); return false;' />
                    <input class='btn_ok' type='submit' value='{$kga.lang.submit}' />
                </div>
                
            </fieldset>
        </form>
        
    </div></div>
</div>
