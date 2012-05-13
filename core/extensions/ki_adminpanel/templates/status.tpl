<form>
    <input type=text id="newstatus" class="formfield"></input>
    <input class='btn_ok' type=submit value="{$kga.lang.new_status}" onclick="adminPanel_extension_newStatus(); return false;">
</form>
<br />
<table>
    <thead>
        <tr class='headerrow'>
            <th>{$kga.lang.status}</th>
            <th>{$kga.lang.options}</th>
        </tr>
    </thead>
    <tbody>


{section name=statusarray loop=$arr_status}
    <tr class='{cycle values="even,odd"}'>

        <td>
            {$arr_status[statusarray].status|escape:'html'}
        </td>

        <td>{strip}
            <a href="#" onClick="adminPanel_extension_editStatus('{$arr_status[statusarray].status_id}'); $(this).blur(); return false;">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif" title="{$kga.lang.editstatus}" width="13" height="13" alt="{$kga.lang.editstatus}" border="0">
            </a>
            
            &nbsp;
            
{if $arr_status[statusarray].timeSheetEntryCount == 0}            
            <a href="#" onClick="adminPanel_extension_deleteStatus({$arr_status[statusarray].status_id})">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan.png" title="{$kga.lang.delstatus}" width="13" height="13" alt="{$kga.lang.delstatus}" border="0">
            </a>
{else}
             <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan_.png" title="{$kga.lang.delstatus}" width="13" height="13" alt="{$kga.lang.delstatus}" border="0">
{/if}            
            
        {/strip}</td>
    </tr>
{/section}
</tbody>
</table>

