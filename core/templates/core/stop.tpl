{*
 *
 * builds buzzer as stop trigger and stopwatch
 *
 *}
 
<form action='kimai.php' method='post'>
    <fieldset>
        <button id='buzzer' type='submit' title='{$kga.lang.stop}' onClick="ts_ext_stopRecord(); return(false);">{$kga.lang.stop}</button>
        <div id='runningtime'>
            {if $watchtext}
                <p>{$kga.lang.running}</p>
            {else}
                <p><span id='h'>00</span>:<span id='m'>00</span>:<span id='s'>00</span></p>
            {/if}
        </div>
    </fieldset>
</form>