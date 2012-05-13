{cycle values="odd,even" reset=true print=false}
          <table>
    
            <tbody>

{foreach item=activity from=$activities}
{if $activity.visible}
            
                <tr id="row_activity{$activity.activityID}" class="{cycle values="odd,even"}" >

                    <td nowrap class="option">
{if $kga.user && $kga.user.status != 2}
                        <a href ="#" onClick="editSubject('activity',{$activity.activityID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit} (ID:{$activity.activityID})' border='0' /></a>
{/if}
                        <a href ="#" onClick="lists_update_filter('activity',{$activity.activityID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/filter.png' width='13' height='13' alt='{$kga.lang.filter}' title='{$kga.lang.filter}' border='0' /></a>

                        <a href ="#" class="preselect" onClick="buzzer_preselect('activity',{$activity.activityID},'{$activity.name|replace:"'":"\\'"|escape:'html'}',0,0); return false;" id="ps{$activity.activityID}"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/preselect_off.png' width='13' height='13' alt='{$kga.lang.select}' title='{$kga.lang.select} (ID:{$activity.activityID})' border='0' /></a>
                    </td>

                    <td width="100%" class="activities" onClick="buzzer_preselect('activity',{$activity.activityID},'{$activity.name|replace:"'":"\\'"|escape:'html'}',0,0); return false;" onmouseover="lists_change_color(this,true);" onmouseout="lists_change_color(this,false);">
                        {if $activity.visible != 1}<span style="color:#bbb">{/if}
                        {if $kga.conf.showIDs == 1}<span class="ids">{$activity.activityID}</span> {/if}{$activity.name|escape:'html'}
                        {if $activity.visible != 1}</span>{/if}
                    </td>

                    <td nowrap class="annotation">
                        {$activity.zeit|escape:'html'}
                    </td>

                </tr>
            
{/if}
{/foreach}




{if $activities == '0'}
                <tr>
                    <td colspan='3'>
                        <strong style="color:red">{$kga.lang.noItems}</strong>
                    </td>
                </tr>
{/if}


            </tbody>  
        </table>  