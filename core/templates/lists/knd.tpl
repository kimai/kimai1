{literal}    
<script type="text/javascript">
    //logfile("{/literal}{$jsArrKndPct}{literal}");
    //var ts_ext_ArrKndPct = new Array({/literal}{$jsArrKndPct}{literal});
</script>
{/literal}

{cycle values="odd,even" reset=true print=false}
          <table>
    
            <tbody>
    
{section name=row loop=$arr_knd}
{if $arr_knd[row].knd_visible || $arr_knd[row].zeit != "0:00"}
            
                    <tr id="row_knd{$arr_knd[row].knd_ID}" class="knd knd{$arr_knd[row].knd_ID} {cycle values="odd,even"}">


{* --- option cell ---*}

                    <td nowrap class="option">
                        
                        

{*
                        <a href ="#" onClick="filterSubject('knd',{$arr_knd[row].knd_ID});"><img src='../skins/{$kga.conf.skin}/grfx/filter.png' width='13' height='13' alt='{$kga.lang.filter}' title='{$kga.lang.filter}' border='0' /></a>
*}



{if $kga.usr.usr_sts != 2}
                        <a href ="#" onClick="editSubject('knd',{$arr_knd[row].knd_ID});"><img src='../skins/{$kga.conf.skin}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit}' border='0' /></a>
{/if}  

{*
                        <a href ="#" onClick="lists_preselect('knd',{$arr_knd[row].knd_ID},'{$arr_knd[row].knd_name}',0,0); return false;" id="ps{$arr_knd[row].knd_ID}"><img src='../skins/{$kga.conf.skin}/grfx/preselect_off.png' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit} (ID:{$arr_knd[row].knd_ID})' border='0' /></a>
*}
                        <a href ="#" onClick="lists_update_filter('knd',{$arr_knd[row].knd_ID}); return false;"><img src='../skins/{$kga.conf.skin}/grfx/filter.png' width='13' height='13' alt='{$kga.lang.filter}' title='{$kga.lang.filter}' border='0' /></a>

                    </td>

{* --- name cell ---*}
                    <td width="100%" class="clients" onmouseover="lists_change_color(this,true);" onmouseout="lists_change_color(this,false);" onClick="lists_knd_prefilter({$arr_knd[row].knd_ID},'highlight'); return false;">
{if $kga.customerhack}
{if $filter == $arr_knd[row].knd_ID}
                        <a href ="#" onClick="filter(0); return false;">
                            <img src='../skins/{$kga.conf.skin}/grfx/printer_indicator.png' width='13' height='11' alt='{$kga.lang.filter}' title='{$kga.lang.filter}' border='0' />
                        </a>
{/if}
{/if}
                        {*if $kga.customerhack}<a href ="#" onClick="filter({$arr_knd[row].knd_ID}); return false;">{/if*}
                            {if $arr_knd[row].knd_visible != 1}<span style="color:#bbb">{/if}
                            {if $kga.conf.showIDs == 1}<span class="ids">{$arr_knd[row].knd_ID}</span> {/if}{$arr_knd[row].knd_name}
                            {if $arr_knd[row].knd_visible != 1}</span>{/if}
                        {*if $kga.customerhack}</a>{/if*}
                    </td>


{* --- annotation cell ---*}
                    <td nowrap class="annotation">
                        {if $arr_knd[row].knd_visible != 1}<span style="color:#bbb">{/if}
                        {$arr_knd[row].zeit}
                        {if $arr_knd[row].knd_visible != 1}</span>{/if}
                    </td>

                </tr>
{/if}            
{/section}

{if $arr_knd == '0'}
                <tr>
                    <td colspan='3'>
                        <strong style="color:red">{$kga.lang.noItems}</strong>
                    </td>
                </tr>
{/if}


            </tbody>  
        </table>  