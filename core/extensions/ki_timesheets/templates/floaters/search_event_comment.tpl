<div id="floater_innerwrap">

    <div id="floater_handle">
        <span id="floater_title">{$kga.lang.search}</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
        </div>       
    </div>

    <div class="floater_content">

        <form id="search_event_comment" action="../extensions/ki_timesheets/processor.php" method="post"> 
            <fieldset>

                <ul>
                
                    <li>
                        <label for="search">{$kga.lang.search}:</label>
                        <input id="search" type="text" name="search" class="focussed" maxlength="100"  size="8" tabindex="12" />
                        
                   </li>

                </ul>
                 <div id="search_result">...</div>
 
                <input name="axAction"     type="hidden" value="search_event_comment" />   
                <input name="axValue"      type="hidden" value="{$id}" />     
                                             
                <div id="formbuttons">
                    <input class="btn_norm" type="button" value="{$kga.lang.cancel}" onClick="floaterClose(); return false;" />
                    <input class="btn_ok" type="submit" value="{$kga.lang.submit}" />
                </div>
                
            </fieldset>
        </form>
       
    </div>
</div>
{literal}
<script language=javascript>
<!--
    $("#search").keyup(function()
    {
        // erst bei drei eingegebenen Zeichen Suche beginnen
        // ToDo: fcw: hier die Anzahl der Mindestzeichen in einen eigenen Tab in Einstellungen 
        if($(this).val().length >= {/literal}{$kga.conf.searchMin}{literal})
        {
            // empty results first
            $("#search_result").html("");
            // call the processor with each char.input
            $.get("../extensions/ki_timesheets/processor.php", {search: $(this).val(), axAction: "search_event_comment"}, function(data)
            {
                // fill div-searc_results with the results
                $("#search_result").html(data);
            });
        }
    });
// -->
</script>
{/literal}  