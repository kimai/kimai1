{cycle values="odd,even" reset=true print=false}
          <table>

            <tbody>
    
{section name=row loop=$arr_pct}
{if $arr_pct[row].pct_visible}
                <tr id="row_pct{$arr_pct[row].pct_ID}" class="pct knd{$arr_pct[row].knd_ID} {cycle values="odd,even"}" >
                    
                    
                    <td nowrap class="option">

{if $kga.usr && $kga.usr.usr_sts != 2}
                        <a href ="#" onClick="editSubject('pct',{$arr_pct[row].pct_ID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit} (ID:{$arr_pct[row].pct_ID})' border='0' /></a>
{/if}
                        <a href ="#" onClick="lists_update_filter('pct',{$arr_pct[row].pct_ID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/filter.png' width='13' height='13' alt='{$kga.lang.filter}' title='{$kga.lang.filter}' border='0' /></a>

                        <a href ="#" class="preselect" onClick="buzzer_preselect('pct',{$arr_pct[row].pct_ID},'{$arr_pct[row].pct_name|replace:"'":"\\'"|escape:'html'}',{$arr_pct[row].knd_ID},'{$arr_pct[row].knd_name|replace:"'":"\\'"|escape:'html'}'); lists_reload('evt'); return false;" id="ps{$arr_pct[row].pct_ID}"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/preselect_off.png' width='13' height='13' alt='{$kga.lang.select}' title='{$kga.lang.select} (ID:{$arr_pct[row].pct_ID})' border='0' /></a>
                    </td>

                    <td width="100%" class="projects" onmouseover="lists_change_color(this,true);" onmouseout="lists_change_color(this,false);" onClick="buzzer_preselect('pct',{$arr_pct[row].pct_ID},'{$arr_pct[row].pct_name|replace:"'":"\\'"|escape:'html'}',{$arr_pct[row].knd_ID},'{$arr_pct[row].knd_name|replace:"'":"\\'"|escape:'html'}'); lists_reload('evt'); return false;">
                        {if $arr_pct[row].pct_visible != 1}<span style="color:#bbb">{/if}
                        {if $kga.conf.flip_pct_display}    
                            {if $kga.conf.showIDs == 1}<span class="ids">{$arr_pct[row].pct_ID}</span> {/if}<span class="lighter">{$arr_pct[row].knd_name|truncate:30:"..."|escape:'html'}:</span> {$arr_pct[row].pct_name|escape:'html'}
                        {else}
                            {if $kga.conf.pct_comment_flag == 1}
                                {if $kga.conf.showIDs == 1}<span class="ids">{$arr_pct[row].pct_ID}</span> {/if}{$arr_pct[row].pct_name|escape:'html'} <span class="lighter">{if $arr_pct[row].pct_comment}({$arr_pct[row].pct_comment|truncate:30:"..."|escape:'html'}){else}<span class="lighter">({$arr_pct[row].knd_name|truncate:30:"..."|escape:'html'})</span>{/if}</span>
                            {else}
                                {if $kga.conf.showIDs == 1}<span class="ids">{$arr_pct[row].pct_ID}</span> {/if}{$arr_pct[row].pct_name|escape:'html'} <span class="lighter">({$arr_pct[row].knd_name|truncate:30:"..."|escape:'html'})</span>
                            {/if}
                        {/if}
                        {if $arr_pct[row].pct_visible != 1}</span>{/if}
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