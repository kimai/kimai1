{cycle values="odd,even" reset=true print=false}
          <table>
    
            <tbody>

{section name=row loop=$arr_evt}
{if $arr_evt[row].evt_visible}
            
                <tr id="row_evt{$arr_evt[row].evt_ID}" class="{cycle values="odd,even"}" >
                    
                    
                    
                    
                    

                    <td nowrap class="option">
{if $kga.usr && $kga.usr.usr_sts != 2}
                        <a href ="#" onClick="editSubject('evt',{$arr_evt[row].evt_ID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit} (ID:{$arr_evt[row].evt_ID})' border='0' /></a>
{/if}
                        <a href ="#" onClick="lists_update_filter('evt',{$arr_evt[row].evt_ID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/filter.png' width='13' height='13' alt='{$kga.lang.filter}' title='{$kga.lang.filter}' border='0' /></a>

                        <a href ="#" class="preselect" onClick="buzzer_preselect('evt',{$arr_evt[row].evt_ID},'{$arr_evt[row].evt_name|replace:"'":"\\'"|escape:'html'}',0,0); return false;" id="ps{$arr_evt[row].evt_ID}"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/preselect_off.png' width='13' height='13' alt='{$kga.lang.select}' title='{$kga.lang.select} (ID:{$arr_evt[row].evt_ID})' border='0' /></a>
                    </td>

                    <td width="100%" class="events" onClick="buzzer_preselect('evt',{$arr_evt[row].evt_ID},'{$arr_evt[row].evt_name|replace:"'":"\\'"|escape:'html'}',0,0); return false;" onmouseover="lists_change_color(this,true);" onmouseout="lists_change_color(this,false);">
                        {if $arr_evt[row].evt_visible != 1}<span style="color:#bbb">{/if}
                        {if $kga.conf.showIDs == 1}<span class="ids">{$arr_evt[row].evt_ID}</span> {/if}{$arr_evt[row].evt_name|escape:'html'}
                        {if $arr_evt[row].evt_visible != 1}</span>{/if}
                    </td>

                    <td nowrap class="annotation">
                        {$arr_evt[row].zeit|escape:'html'}
                    </td>

                </tr>
            
{/if}
{/section}




{if $arr_evt == '0'}
                <tr>
                    <td colspan='3'>
                        <strong style="color:red">{$kga.lang.noItems}</strong>
                    </td>
                </tr>
{/if}


            </tbody>  
        </table>  