
<a href="#" onClick="floaterShow('floaters.php','add_edit_evt',0,0,450,200); $(this).blur(); return false;"><img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/add.png" width="22" height="16" alt="{$kga.lang.new_evt}"></a> {$kga.lang.new_evt}

&nbsp;&nbsp;&nbsp;{$kga.lang.view_filter}:
        <select size="1" id="evt_pct_filter" onchange="ap_ext_refreshSubtab('evt');">
          <option value="-2" {if $selected_evt_filter==-2}selected="selected"{/if}>{$kga.lang.unassigned}</option>
          <option value="-1" {if $selected_evt_filter==-1}selected="selected"{/if}>{$kga.lang.all_events}</option>
          {section name=row loop=$arr_pct}
          <option value="{$arr_pct[row].pct_ID}"
             {if $selected_evt_filter==$arr_pct[row].pct_ID}selected="selected"{/if}>{$arr_pct[row].pct_name|escape:'html'} ({$arr_pct[row].knd_name|truncate:30:"..."|escape:'html'})</option>
          {/section}
        </select>
<br/><br/>

{cycle values="odd,even" reset=true print=false}
          <table>
              
              <thead>
                  <tr class='headerrow'>
                      <th>{$kga.lang.options}</th>
                      <th>{$kga.lang.evts}</th>
                      <th>{$kga.lang.groups}</th>
                  </tr>
              </thead>
              
    
            <tbody>

{section name=row loop=$arr_evt}
{if $arr_evt[row].evt_visible || $arr_evt[row].zeit != "0:00"}
            
                <tr class="{cycle values="odd,even"}">

                    <td class="option">
                        <a href ="#" onClick="editSubject('evt',{$arr_evt[row].evt_ID}); $(this).blur(); return false;">
                            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit}' border='0' />
                        </a>
                        
                        &nbsp;
                        
                        <a href="#" id="delete_evt{$arr_evt[row].evt_ID}" onClick="ap_ext_deleteEvent({$arr_evt[row].evt_ID})">
                          <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan.png" title="{$kga.lang.delevt}" width="13" height="13" alt="{$kga.lang.delevt}" border="0">
                        </a>
                    </td>

                    <td class="events">
                        {if $arr_evt[row].evt_visible != 1}<span style="color:#bbb">{/if}
                        {$arr_evt[row].evt_name|escape:'html'}
                        {if $arr_evt[row].evt_visible != 1}</span>{/if}
                    </td>
                    
                    <td>
                        {$arr_evt[row].groups|escape:'html'}
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