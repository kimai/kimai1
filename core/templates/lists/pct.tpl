{cycle values="odd,even" reset=true print=false}
          <table>

            <tbody>
    
{section name=row loop=$arr_pct}
{if $arr_pct[row].visible}
                <tr id="row_pct{$arr_pct[row].projectID}" class="pct knd{$arr_pct[row].customerID} {cycle values="odd,even"}" >
                    
                    
                    <td nowrap class="option">

{if $kga.usr && $kga.usr.status != 2}
                        <a href ="#" onClick="editSubject('pct',{$arr_pct[row].projectID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit} (ID:{$arr_pct[row].projectID})' border='0' /></a>
{/if}
                        <a href ="#" onClick="lists_update_filter('pct',{$arr_pct[row].projectID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/filter.png' width='13' height='13' alt='{$kga.lang.filter}' title='{$kga.lang.filter}' border='0' /></a>

                        <a href ="#" class="preselect" onClick="buzzer_preselect('pct',{$arr_pct[row].projectID},'{$arr_pct[row].name|replace:"'":"\\'"|escape:'html'}',{$arr_pct[row].customerID},'{$arr_pct[row].customerName|replace:"'":"\\'"|escape:'html'}'); lists_reload('evt'); return false;" id="ps{$arr_pct[row].projectID}"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/preselect_off.png' width='13' height='13' alt='{$kga.lang.select}' title='{$kga.lang.select} (ID:{$arr_pct[row].projectID})' border='0' /></a>
                    </td>

                    <td width="100%" class="projects" onmouseover="lists_change_color(this,true);" onmouseout="lists_change_color(this,false);" onClick="buzzer_preselect('pct',{$arr_pct[row].projectID},'{$arr_pct[row].name|replace:"'":"\\'"|escape:'html'}',{$arr_pct[row].customerID},'{$arr_pct[row].customerName|replace:"'":"\\'"|escape:'html'}'); lists_reload('evt'); return false;">
                        {if $arr_pct[row].visible != 1}<span style="color:#bbb">{/if}
                        {if $kga.conf.flip_pct_display}    
                            {if $kga.conf.showIDs == 1}<span class="ids">{$arr_pct[row].projectID}</span> {/if}<span class="lighter">{$arr_pct[row].customerName|truncate:30:"..."|escape:'html'}:</span> {$arr_pct[row].name|escape:'html'}
                        {else}
                            {if $kga.conf.comment_flag == 1}
                                {if $kga.conf.showIDs == 1}<span class="ids">{$arr_pct[row].projectID}</span> {/if}{$arr_pct[row].name|escape:'html'} <span class="lighter">{if $arr_pct[row].comment}({$arr_pct[row].comment|truncate:30:"..."|escape:'html'}){else}<span class="lighter">({$arr_pct[row].customerName|truncate:30:"..."|escape:'html'})</span>{/if}</span>
                            {else}
                                {if $kga.conf.showIDs == 1}<span class="ids">{$arr_pct[row].projectID}</span> {/if}{$arr_pct[row].name|escape:'html'} <span class="lighter">({$arr_pct[row].customerName|truncate:30:"..."|escape:'html'})</span>
                            {/if}
                        {/if}
                        {if $arr_pct[row].visible != 1}</span>{/if}
                    </td>


                    <td class="annotation">
                        {$arr_pct[row].zeit|escape:'html'}
                    </td>

                </tr>
{/if}            
{/section}

{if $arr_pct == '0'}
                <tr>
                    <td nowrap colspan='3'>
                        <strong style="color:red">{$kga.lang.noItems}</strong>
                    </td>
                </tr>
{/if}


            </tbody>  
        </table>  