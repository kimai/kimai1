{cycle values="odd,even" reset=true print=false}
          <table>
    
            <tbody>
    
{section name=row loop=$arr_usr}
            
                <tr id="row_usr{$arr_usr[row].usr_ID}" class="{cycle values="odd,even"}">
                    


{* --- option cell ---*}

                    <td nowrap class="option">
                      <a href ="#" onClick="lists_update_filter('usr',{$arr_usr[row].usr_ID}); $(this).blur(); return false;"><img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/filter.png' width='13' height='13' alt='{$kga.lang.filter}' title='{$kga.lang.filter}' border='0' /></a>

                    </td>

{* --- name cell ---*}
                    <td width="100%" class="clients">
                            {$arr_usr[row].usr_name|escape:'html'}
                    </td>


{* --- annotation cell ---*}
                    <td nowrap class="annotation">

                    </td>

                </tr>
         
{/section}

{if $arr_usr == '0'}
                <tr>
                    <td colspan='3'>
                        <strong style="color:red">{$kga.lang.noItems}</strong>
                    </td>
                </tr>
{/if}


            </tbody>  
        </table>  