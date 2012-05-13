<form>
    <input type=text id="newgroup" class="formfield"></input>
    <input class='btn_ok' type=submit value="{$kga.lang.addgroup}" onclick="adminPanel_extension_newGroup(); return false;">
</form>
<br />
<table>
    <thead>
        <tr class='headerrow'>
            <th>{$kga.lang.group}</th>
            <th>{$kga.lang.options}</th>
            <th>{$kga.lang.members}</th>
            <th>{$kga.lang.groupleader}</th>
        </tr>
    </thead>
    <tbody>


{section name=grouparray loop=$groups}
    <tr class='{cycle values="even,odd"}'>

        <td>
{if $groups[grouparray].groupID == 1}            
            <span style="color:red">{$groups[grouparray].name|escape:'html'}</span>
{else}
            {$groups[grouparray].name|escape:'html'}
{/if}            
        </td>


        
        <td>{strip}
            <a href="#" onClick="adminPanel_extension_editGroup('{$groups[grouparray].groupID}'); $(this).blur(); return false;">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif" title="{$kga.lang.editGroup}" width="13" height="13" alt="{$kga.lang.editGroup}" border="0">
            </a>
            
            &nbsp;
{*
            <a href="#" onClick="switchGroup('{$groups[grouparray].groupID}'); return false;">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/auge.png" title="{$kga.lang.switchGroup}" width="16" height="13" alt="{$kga.lang.switchGroup}" border="0">
            </a>
            
            &nbsp;
            
            <a href="#" onClick="backupGrp({$groups[grouparray].groupID}); return false;">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_backup.gif" title="{$kga.lang.backupgrp}" width="12" height="13" border="0" alt="{$kga.lang.backupgrp}">
            </a>

            &nbsp;
            
            <a href="mailto:{$groups[grouparray].mail|escape:'html'}">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_mail_.gif" title="{$kga.lang.mailgrp}" width="12" height="13" alt="{$kga.lang.mailgrp}" border="0">
            </a>
            
            &nbsp;
*}            
            
{if $groups[grouparray].count_users == 0}            
            <a href="#" onClick="adminPanel_extension_deleteGroup({$groups[grouparray].groupID})">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan.png" title="{$kga.lang.delete_group}" width="13" height="13" alt="{$kga.lang.delete_group}" border="0">
            </a>
{else}
             <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan_.png" title="{$kga.lang.delete_group}" width="13" height="13" alt="{$kga.lang.delete_group}" border="0">
{/if}            
            
        {/strip}</td>
        
        <td>{$groups[grouparray].count_users}</td>
        
        
{*Display name(s) of the group-leader(s)*}
    

        <td>
            {foreach item=leader from=$groups[grouparray].leader_name}
            {$leader|escape:'html'}
            {/foreach}
        </td>




    </tr>
{/section}
</tbody>
</table>

