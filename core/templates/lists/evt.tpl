{cycle values="odd,even" reset=true print=false}
          <table>
    
            <tbody>

{foreach item=evt from=$arr_evt}
{if $evt.evt_visible}
            
                <tr id="row_evt{$evt.evt_ID}" class="{cycle values="odd,even"}" >

                    <td nowrap class="option">
{if $kga.usr && $kga.usr.usr_sts != 2}
                        <a href ="#" onClick="editSubject('evt',{$evt.evt_ID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit} (ID:{$evt.evt_ID})' border='0' /></a>
{/if}
                        <a href ="#" onClick="lists_update_filter('evt',{$evt.evt_ID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/filter.png' width='13' height='13' alt='{$kga.lang.filter}' title='{$kga.lang.filter}' border='0' /></a>

                        <a href ="#" class="preselect" onClick="buzzer_preselect('evt',{$evt.evt_ID},'{$evt.evt_name|replace:"'":"\\'"|escape:'html'}',0,0); return false;" id="ps{$evt.evt_ID}"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/preselect_off.png' width='13' height='13' alt='{$kga.lang.select}' title='{$kga.lang.select} (ID:{$evt.evt_ID})' border='0' /></a>
                    </td>

                    <td width="100%" class="events" onClick="buzzer_preselect('evt',{$evt.evt_ID},'{$evt.evt_name|replace:"'":"\\'"|escape:'html'}',0,0); return false;" onmouseover="lists_change_color(this,true);" onmouseout="lists_change_color(this,false);">
                        {if $evt.evt_visible != 1}<span style="color:#bbb">{/if}
                        {if $kga.conf.showIDs == 1}<span class="ids">{$evt.evt_ID}</span> {/if}{$evt.evt_name|escape:'html'}
                        {if $evt.evt_visible != 1}</span>{/if}
                    </td>

                    <td nowrap class="annotation">
                        {$evt.zeit|escape:'html'}
                    </td>

                </tr>
            
{/if}
{/foreach}




{if $arr_evt == '0'}
                <tr>
                    <td colspan='3'>
                        <strong style="color:red">{$kga.lang.noItems}</strong>
                    </td>
                </tr>
{/if}


            </tbody>  
        </table>  