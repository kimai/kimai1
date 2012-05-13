
<a href="#" onClick="floaterShow('floaters.php','add_edit_activity',0,0,500,200); $(this).blur(); return false;"><img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/add.png" width="22" height="16" alt="{$kga.lang.new_activity}"></a> {$kga.lang.new_activity}

&nbsp;&nbsp;&nbsp;{$kga.lang.view_filter}:
        <select size="1" id="activity_project_filter" onchange="adminPanel_extension_refreshSubtab('activities');">
          <option value="-2" {if $selected_activity_filter==-2}selected="selected"{/if}>{$kga.lang.unassigned}</option>
          <option value="-1" {if $selected_activity_filter==-1}selected="selected"{/if}>{$kga.lang.all_activities}</option>
          {section name=row loop=$projects}
          <option value="{$projects[row].projectID}"
             {if $selected_activity_filter==$projects[row].projectID}selected="selected"{/if}>{$projects[row].name|escape:'html'} ({$projects[row].customerName|truncate:30:"..."|escape:'html'})</option>
          {/section}
        </select>
<br/><br/>

{cycle values="odd,even" reset=true print=false}
          <table>
              
              <thead>
                  <tr class='headerrow'>
                      <th>{$kga.lang.options}</th>
                      <th>{$kga.lang.activities}</th>
                      <th>{$kga.lang.groups}</th>
                  </tr>
              </thead>
              
    
            <tbody>

{foreach item=activity from=$activities}
{if $activity.visible || $activity.zeit != "0:00"}
            
                <tr class="{cycle values="odd,even"}">

                    <td class="option">
                        <a href ="#" onClick="editSubject('activity',{$activity.activityID}); $(this).blur(); return false;">
                            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit}' border='0' />
                        </a>
                        
                        &nbsp;
                        
                        <a href="#" id="delete_activity{$activity.activityID}" onClick="adminPanel_extension_deleteActivity({$activity.activityID})">
                          <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan.png" title="{$kga.lang.delete_activity}" width="13" height="13" alt="{$kga.lang.delete_activity}" border="0">
                        </a>
                    </td>

                    <td class="activities">
                        {if $activity.visible != 1}<span style="color:#bbb">{/if}
                        {$activity.name|escape:'html'}
                        {if $activity.visible != 1}</span>{/if}
                    </td>
                    
                    <td>
                        {$activity.groups|escape:'html'}
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