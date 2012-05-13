{cycle values="odd,even" reset=true print=false}
          <table>

            <tbody>
    
{section name=row loop=$projects}
{if $projects[row].visible}
                <tr id="row_project{$projects[row].projectID}" class="project customer{$projects[row].customerID} {cycle values="odd,even"}" >
                    
                    
                    <td nowrap class="option">

{if $kga.user && $kga.user.status != 2}
                        <a href ="#" onClick="editSubject('project',{$projects[row].projectID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit} (ID:{$projects[row].projectID})' border='0' /></a>
{/if}
                        <a href ="#" onClick="lists_update_filter('project',{$projects[row].projectID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/filter.png' width='13' height='13' alt='{$kga.lang.filter}' title='{$kga.lang.filter}' border='0' /></a>

                        <a href ="#" class="preselect" onClick="buzzer_preselect('project',{$projects[row].projectID},'{$projects[row].name|replace:"'":"\\'"|escape:'html'}',{$projects[row].customerID},'{$projects[row].customerName|replace:"'":"\\'"|escape:'html'}'); lists_reload('activity'); return false;" id="ps{$projects[row].projectID}"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/preselect_off.png' width='13' height='13' alt='{$kga.lang.select}' title='{$kga.lang.select} (ID:{$projects[row].projectID})' border='0' /></a>
                    </td>

                    <td width="100%" class="projects" onmouseover="lists_change_color(this,true);" onmouseout="lists_change_color(this,false);" onClick="buzzer_preselect('project',{$projects[row].projectID},'{$projects[row].name|replace:"'":"\\'"|escape:'html'}',{$projects[row].customerID},'{$projects[row].customerName|replace:"'":"\\'"|escape:'html'}'); lists_reload('activity'); return false;">
                        {if $projects[row].visible != 1}<span style="color:#bbb">{/if}
                        {if $kga.conf.flip_project_display}    
                            {if $kga.conf.showIDs == 1}<span class="ids">{$projects[row].projectID}</span> {/if}<span class="lighter">{$projects[row].customerName|truncate:30:"..."|escape:'html'}:</span> {$projects[row].name|escape:'html'}
                        {else}
                            {if $kga.conf.comment_flag == 1}
                                {if $kga.conf.showIDs == 1}<span class="ids">{$projects[row].projectID}</span> {/if}{$projects[row].name|escape:'html'} <span class="lighter">{if $projects[row].comment}({$projects[row].comment|truncate:30:"..."|escape:'html'}){else}<span class="lighter">({$projects[row].customerName|truncate:30:"..."|escape:'html'})</span>{/if}</span>
                            {else}
                                {if $kga.conf.showIDs == 1}<span class="ids">{$projects[row].projectID}</span> {/if}{$projects[row].name|escape:'html'} <span class="lighter">({$projects[row].customerName|truncate:30:"..."|escape:'html'})</span>
                            {/if}
                        {/if}
                        {if $projects[row].visible != 1}</span>{/if}
                    </td>


                    <td class="annotation">
                        {$projects[row].zeit|escape:'html'}
                    </td>

                </tr>
{/if}            
{/section}

{if $projects == '0'}
                <tr>
                    <td nowrap colspan='3'>
                        <strong style="color:red">{$kga.lang.noItems}</strong>
                    </td>
                </tr>
{/if}


            </tbody>  
        </table>  