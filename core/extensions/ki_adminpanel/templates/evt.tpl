
<a href="#" onClick="floaterShow('floaters.php','add_edit_evt',0,0,500,200); $(this).blur(); return false;"><img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/add.png" width="22" height="16" alt="{$kga.lang.new_evt}"></a> {$kga.lang.new_evt}

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

{foreach item=evt from=$arr_evt}
{if $evt.evt_visible || $evt.zeit != "0:00"}
            
                <tr class="{cycle values="odd,even"}">

                    <td class="option">
                        <a href ="#" onClick="editSubject('evt',{$evt.evt_ID}); $(this).blur(); return false;">
                            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit}' border='0' />
                        </a>
                        
                        &nbsp;
                        
                        <a href="#" id="delete_evt{$evt.evt_ID}" onClick="ap_ext_deleteEvent({$evt.evt_ID})">
                          <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan.png" title="{$kga.lang.delevt}" width="13" height="13" alt="{$kga.lang.delevt}" border="0">
                        </a>
                    </td>

                    <td class="events">
                        {if $evt.evt_visible != 1}<span style="color:#bbb">{/if}
                        {$evt.evt_name|escape:'html'}
                        {if $evt.evt_visible != 1}</span>{/if}
                    </td>
                    
                    <td>
                        {$evt.groups|escape:'html'}
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