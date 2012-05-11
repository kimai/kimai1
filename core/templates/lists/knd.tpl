{cycle values="odd,even" reset=true print=false}
          <table>
    
            <tbody>
    
{section name=row loop=$arr_knd}
{if $arr_knd[row].visible}
            
                    <tr id="row_knd{$arr_knd[row].customerID}" class="knd knd{$arr_knd[row].customerID} {cycle values="odd,even"}">


{* --- option cell ---*}

                    <td nowrap class="option">
                    

{if $kga.usr && $kga.usr.status != 2}
                        <a href ="#" onClick="editSubject('knd',{$arr_knd[row].customerID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit}' border='0' /></a>
{/if}  

                        <a href ="#" onClick="lists_update_filter('knd',{$arr_knd[row].customerID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/filter.png' width='13' height='13' alt='{$kga.lang.filter}' title='{$kga.lang.filter}' border='0' /></a>

                    </td>

{* --- name cell ---*}
                    <td width="100%" class="clients" onmouseover="lists_change_color(this,true);" onmouseout="lists_change_color(this,false);" onClick="lists_knd_prefilter({$arr_knd[row].customerID},'highlight'); $(this).blur(); return false;">
                        {if $arr_knd[row].visible != 1}<span style="color:#bbb">{/if}
                        {if $kga.conf.showIDs == 1}<span class="ids">{$arr_knd[row].customerID}</span> {/if}{$arr_knd[row].name|escape:'html'}
                        {if $arr_knd[row].visible != 1}</span>{/if}
                    </td>


{* --- annotation cell ---*}
                    <td nowrap class="annotation">
                        {$arr_knd[row].zeit|escape:'html'}
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