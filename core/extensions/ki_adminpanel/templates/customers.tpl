<a href="#" onClick="floaterShow('floaters.php','add_edit_customer',0,0,450,200); $(this).blur(); return false;"><img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/add.png" width="22" height="16" alt="{$kga.lang.new_customer}"></a> {$kga.lang.new_customer}
<br/><br/>

{cycle values="odd,even" reset=true print=false}
          <table>
              
              <thead>
                  <tr class='headerrow'>
                      <th>{$kga.lang.options}</th>
                      <th>{$kga.lang.customers}</th>
                      <th>{$kga.lang.contactPerson}</th>
                      <th>{$kga.lang.groups}</th>
                  </tr>
              </thead>
              
                  
            <tbody>
    
{section name=row loop=$customers}
{if $customers[row].visible || $customers[row].zeit != "0:00"}
            
                <tr class="{cycle values="odd,even"}">

                    <td class="option">
                        <a href ="#" onClick="editSubject('customer',{$customers[row].customerID}); $(this).blur(); return false;">
                            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit}' border='0' />
                        </a>
                        
                        &nbsp;
                        
                        <a href="#" id="delete_customer{$customers[row].customerID}" onClick="adminPanel_extension_deleteCustomer({$customers[row].customerID})">
                          <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan.png" title="{$kga.lang.delete_customer}" width="13" height="13" alt="{$kga.lang.delete_customer}" border="0">
                        </a>
                    </td>

                    <td class="clients">
                            {if $customers[row].visible != 1}<span style="color:#bbb">{/if}
                            {$customers[row].name|escape:'html'}
                            {if $customers[row].visible != 1}</span>{/if}
                    </td>
                    
                    <td>
                      {$customers[row].contact|escape:'html'}
                    </td>
                    
                    <td>
                        {$customers[row].groups|escape:'html'}
                    </td>

                </tr>
{/if}            
{/section}

{if $customers == '0'}
                <tr>
                    <td colspan='3'>
                        <strong style="color:red">{$kga.lang.noItems}</strong>
                    </td>
                </tr>
{/if}


            </tbody>  
        </table>  