{*
 *
 * builds dialogue for specify events list
 *
 *}

 <div id="container">

     <div class="header">
         <a href="kimai.php"><img id="closebutton" src="../grfx/close.png" align="right"></a> {$kga.lang.specify}
     </div>

     <div class="content" style="height:300px; overflow:auto">

                <strong>{$kga.lang.evts}</strong><br /><br />

{section name=row loop=$arr_evt}

{if $arr_evt[row].evt_visible == 1}
                {strip}<a href="#" id="item{$arr_evt[row].evt_ID}" onClick="hide_item('evt',{$arr_evt[row].evt_ID})">
                    <img src="../skins/{$kga.conf.skin}/grfx/auge.png" title="{$kga.lang.showitem}" width="16" height="13" alt="{$kga.lang.showitem}" border="0" />
                </a>{/strip}
{else}
                {strip}<a href="#" id="item{$arr_evt[row].evt_ID}" onClick="show_item('evt',{$arr_evt[row].evt_ID})">
                    <img src="../skins/{$kga.conf.skin}/grfx/auge_zu.png" title="{$kga.lang.hideitem}" width="16" height="13" alt="{$kga.lang.hideitem}" border="0" />
                </a>{/strip}
{/if}
                {$arr_evt[row].evt_name} <br />
{/section}

     </div>
 </div>
