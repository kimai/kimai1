{*
 *
 * builds dialogue for specify customers list
 *
 *}

 <div id="container">

     <div class="header">
         <a href="kimai.php"><img id="closebutton" src="../grfx/close.png" align="right"></a> {$kga.lang.specify}
     </div>

     <div class="content" style="height:300px; overflow:auto">

                <strong>{$kga.lang.knds}</strong><br /><br />

{section name=row loop=$arr_knd}

{if $arr_knd[row].knd_visible == 1}
                {strip}<a href="#" id="item{$arr_knd[row].knd_ID}" onClick="hide_item('knd',{$arr_knd[row].knd_ID})">
                    <img src="../skins/{$kga.conf.skin}/grfx/auge.png" title="{$kga.lang.showitem}" width="16" height="13" alt="{$kga.lang.showitem}" border="0" />
                </a>{/strip}
{else}
                {strip}<a href="#" id="item{$arr_knd[row].knd_ID}" onClick="show_item('knd',{$arr_knd[row].knd_ID})">
                    <img src="../skins/{$kga.conf.skin}/grfx/auge_zu.png" title="{$kga.lang.hideitem}" width="16" height="13" alt="{$kga.lang.hideitem}" border="0" />
                </a>{/strip}
{/if}
                {$arr_knd[row].knd_name} <br />
{/section}

     </div>
 </div>
