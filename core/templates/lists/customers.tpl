{cycle values="odd,even" reset=true print=false}
          <table>
    
            <tbody>
    
{section name=row loop=$customers}
{if $customers[row].visible}
            
                    <tr id="row_customer{$customers[row].customerID}" class="customer customer{$customers[row].customerID} {cycle values="odd,even"}">


{* --- option cell ---*}

                    <td nowrap class="option">
                    

{if $kga.user && $kga.user.status != 2}
                        <a href ="#" onClick="editSubject('customer',{$customers[row].customerID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit}' border='0' /></a>
{/if}  

                        <a href ="#" onClick="lists_update_filter('customer',{$customers[row].customerID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/filter.png' width='13' height='13' alt='{$kga.lang.filter}' title='{$kga.lang.filter}' border='0' /></a>

                    </td>

{* --- name cell ---*}
                    <td width="100%" class="clients" onmouseover="lists_change_color(this,true);" onmouseout="lists_change_color(this,false);" onClick="lists_customer_prefilter({$customers[row].customerID},'highlight'); $(this).blur(); return false;">
                        {if $customers[row].visible != 1}<span style="color:#bbb">{/if}
                        {if $kga.conf.showIDs == 1}<span class="ids">{$customers[row].customerID}</span> {/if}{$customers[row].name|escape:'html'}
                        {if $customers[row].visible != 1}</span>{/if}
                    </td>


{* --- annotation cell ---*}
                    <td nowrap class="annotation">
                        {$customers[row].zeit|escape:'html'}
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