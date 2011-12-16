<a href="#" onClick="floaterShow('floaters.php','add_edit_knd',0,0,450,200); $(this).blur(); return false;"><img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/add.png" width="22" height="16" alt="{$kga.lang.new_knd}"></a> {$kga.lang.new_knd}
<br/><br/>

{cycle values="odd,even" reset=true print=false}
          <table>
              
              <thead>
                  <tr class='headerrow'>
                      <th>{$kga.lang.options}</th>
                      <th>{$kga.lang.knds}</th>
                      <th>{$kga.lang.contactPerson}</th>
                      <th>{$kga.lang.groups}</th>
                  </tr>
              </thead>
              
                  
            <tbody>
    
{section name=row loop=$arr_knd}
{if $arr_knd[row].knd_visible || $arr_knd[row].zeit != "0:00"}
            
                <tr class="{cycle values="odd,even"}">

                    <td class="option">
                        <a href ="#" onClick="editSubject('knd',{$arr_knd[row].knd_ID}); $(this).blur(); return false;">
                            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit}' border='0' />
                        </a>
                        
                        &nbsp;
                        
                        <a href="#" id="delete_knd{$arr_knd[row].knd_ID}" onClick="ap_ext_deleteCustomer({$arr_knd[row].knd_ID})">
                          <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan.png" title="{$kga.lang.delknd}" width="13" height="13" alt="{$kga.lang.delknd}" border="0">
                        </a>
                    </td>

                    <td class="clients">
                            {if $arr_knd[row].knd_visible != 1}<span style="color:#bbb">{/if}
                            {$arr_knd[row].knd_name|escape:'html'}
                            {if $arr_knd[row].knd_visible != 1}</span>{/if}
                    </td>
                    
                    <td>
                      {$arr_knd[row].knd_contact|escape:'html'}
                    </td>
                    
                    <td>
                        {$arr_knd[row].groups|escape:'html'}
                    </td>

                </tr>
{/if}            
{/section}

{if $arr_knd == '0'}
                <tr>
                    <td colspan='3'>
                        <strong style="color:red">{$kga.lang.noItems}</strong>
                    </td>
                </tr>
{/if}


            </tbody>  
        </table>  