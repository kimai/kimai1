<a href="#" onClick="floaterShow('floaters.php','add_edit_pct',0,0,450,200); $(this).blur(); return false;"><img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/add.png" width="22" height="16" alt="{$kga.lang.new_pct}"></a> {$kga.lang.new_pct}
<br/><br/>

{cycle values="odd,even" reset=true print=false}
          <table>
              
              <thead>
                  <tr class='headerrow'>
                      <th>{$kga.lang.options}</th>
                      <th>{$kga.lang.pcts}</th>
                      <th>{$kga.lang.groups}</th>
                  </tr>
              </thead>
              

            <tbody>
    
{section name=row loop=$arr_pct}
{if $arr_pct[row].pct_visible || $arr_pct[row].zeit != "0:00"}            
                <tr class="{cycle values="odd,even"}">

                    <td class="option">
                        <a href ="#" onClick="editSubject('pct',{$arr_pct[row].pct_ID}); $(this).blur(); return false;">
                            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif' width='13' height='13' alt='{$kga.lang.edit}' title='{$kga.lang.edit}' border='0' />
                        </a>
                        
                        &nbsp;
                        
                        <a href="#" id="delete_pct{$arr_pct[row].pct_ID}" onClick="ap_ext_deleteProject({$arr_pct[row].pct_ID})">
                          <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan.png" title="{$kga.lang.delpct}" width="13" height="13" alt="{$kga.lang.delpct}" border="0">
                        </a>
                    </td>

                    <td class="projects">
                        {if $arr_pct[row].pct_visible != 1}<span style="color:#bbb">{/if}
                        {if $kga.conf.flip_pct_display}    
                        <span class="lighter">{$arr_pct[row].knd_name|truncate:30:"..."|escape:'html'}:</span> {$arr_pct[row].pct_name|escape:'html'}
                        {else}
                        {$arr_pct[row].pct_name|escape:'html'} <span class="lighter">({$arr_pct[row].knd_name|truncate:30:"..."|escape:'html'})</span>
                        {/if}
                        {if $arr_pct[row].pct_visible != 1}</span>{/if}
                    </td>
                    
                    <td>
                        {$arr_pct[row].groups|escape:'html'}
                    </td>

                </tr>
{/if}            
{/section}

{if $arr_pct == '0'}
                <tr>
                    <td colspan='3'>
                        <strong style="color:red">{$kga.lang.noItems}</strong>
                    </td>
                </tr>
{/if}


            </tbody>  
        </table>  