{literal}    
<script type="text/javascript">
    //logfile("{/literal}{$jsArrKndPct}{literal}");
    //var ts_ext_ArrKndPct = new Array({/literal}{$jsArrKndPct}{literal});
</script>
{/literal}

{cycle values="odd,even" reset=true print=false}
          <table>
    
            <tbody>
    
{section name=row loop=$arr_usr}
            
                <tr id="row_usr{$arr_usr[row].usr_ID}" class="{cycle values="odd,even"}">
                    


{* --- option cell ---*}

                    <td nowrap class="option">

                    </td>

{* --- name cell ---*}
                    <td width="100%" class="clients">
                            {$arr_usr[row].usr_name}
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