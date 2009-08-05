<a href="#" onClick="floaterShow('floaters.php','add_edit_evt',0,0,450,200); return false;"><img src="../skins/standard/grfx/add.png" width="22" height="16" alt="{$kga.lang.new_evt}"></a> {$kga.lang.new_evt}
<br/><br/>

{cycle values="odd,even" reset=true print=false}
          <table>
              
              <thead>
                  <tr class='headerrow'>
                      <th>{$kga.lang.options}</th>
                      <th>{$kga.lang.evts}</th>
                  </tr>
              </thead>
              
    
            <tbody>

{section name=row loop=$arr_evt}
{if $arr_evt[row].evt_visible || $arr_evt[row].zeit != "0:00"}
            
                <tr class="{cycle values="odd,even"}">

                    <td class="option">
                        <a href ="#" onClick="editSubject('evt',{$arr_evt[row].evt_ID});">
                            <img src='../skins/{$kga.conf.skin}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit}' border='0' />
                        </a>
                    </td>

                    <td class="events">
                        {if $arr_evt[row].evt_visible != 1}<span style="color:#bbb">{/if}
                        {$arr_evt[row].evt_name}
                        {if $arr_evt[row].evt_visible != 1}</span>{/if}
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