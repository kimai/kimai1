<a href="#" onClick="floaterShow('floaters.php','add_edit_project',0,0,650,200); $(this).blur(); return false;"><img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/add.png" width="22" height="16" alt="{$kga.lang.new_project}"></a> {$kga.lang.new_project}
<br/><br/>

{cycle values="odd,even" reset=true print=false}
          <table>
              
              <thead>
                  <tr class='headerrow'>
                      <th>{$kga.lang.options}</th>
                      <th>{$kga.lang.projects}</th>
                      <th>{$kga.lang.groups}</th>
                  </tr>
              </thead>
              

            <tbody>
    
{section name=row loop=$projects}
{if $projects[row].visible || $projects[row].zeit != "0:00"}            
                <tr class="{cycle values="odd,even"}">

                    <td class="option">
                        <a href ="#" onClick="editSubject('project',{$projects[row].projectID}); $(this).blur(); return false;">
                            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit}' border='0' />
                        </a>
                        
                        &nbsp;
                        
                        <a href="#" id="delete_project{$projects[row].projectID}" onClick="adminPanel_extension_deleteProject({$projects[row].projectID})">
                          <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan.png" title="{$kga.lang.delete_project}" width="13" height="13" alt="{$kga.lang.delete_project}" border="0">
                        </a>
                    </td>

                    <td class="projects">
                        {if $projects[row].visible != 1}<span style="color:#bbb">{/if}
                        {if $kga.conf.flip_project_display}    
                        <span class="lighter">{$projects[row].customerName|truncate:30:"..."|escape:'html'}:</span> {$projects[row].name|escape:'html'}
                        {else}
                        {$projects[row].name|escape:'html'} <span class="lighter">({$projects[row].customerName|truncate:30:"..."|escape:'html'})</span>
                        {/if}
                        {if $projects[row].visible != 1}</span>{/if}
                    </td>
                    
                    <td>
                        {$projects[row].groups|escape:'html'}
                    </td>

                </tr>
{/if}            
{/section}

{if $projects == '0'}
                <tr>
                    <td colspan='3'>
                        <strong style="color:red">{$kga.lang.noItems}</strong>
                    </td>
                </tr>
{/if}


            </tbody>  
        </table>  