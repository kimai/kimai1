{*
 *
 * builds dialogue for specify projects list
 *
 *}

 <div id="container">

     <div class="header">
         <a href="kimai.php"><img id="closebutton" src="../grfx/close.png" align="right"></a> {$kga.lang.specify}
     </div>

     <div class="content" style="height:300px; overflow:auto">

                <strong>{$kga.lang.pcts}</strong><br /><br />

{section name=row loop=$arr_pct}



    {if $arr_pct[row].pct_visible == 1}
                    {strip}<a href="#" id="item{$arr_pct[row].pct_ID}" onClick="hide_item('pct',{$arr_pct[row].pct_ID})">
                        <img src="../skins/{$kga.conf.skin}/grfx/auge.png" title="{$kga.lang.showitem}" width="16" height="13" alt="{$kga.lang.showitem}" border="0" />
                    </a>{/strip}
    {else}
                    {strip}<a href="#" id="item{$arr_pct[row].pct_ID}" onClick="show_item('pct',{$arr_pct[row].pct_ID})">
                        <img src="../skins/{$kga.conf.skin}/grfx/auge_zu.png" title="{$kga.lang.hideitem}" width="16" height="13" alt="{$kga.lang.hideitem}" border="0" />
                    </a>{/strip}
    {/if}
    
    
    {if $kga.conf.flip_pct_display}
                    <span style="color:#bbb">{$arr_pct[row].knd_name}:</span> {$arr_pct[row].pct_name} <br />
    {else}
                    {$arr_pct[row].pct_name} <span style="color:#bbb">({$arr_pct[row].knd_name})</span><br />
    {/if} {*flip pct*}
                
                
                
{/section}

     </div>
 </div>
