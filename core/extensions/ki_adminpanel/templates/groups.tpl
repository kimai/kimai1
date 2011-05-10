<form>
    <input type=text id="newgroup" class="formfield"></input>
    <input class='btn_ok' type=submit value="{$kga.lang.addgroup}" onclick="ap_ext_newGroup(); return false;">
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


{section name=grouparray loop=$arr_grp}
    <tr class='{cycle values="even,odd"}'>

        <td>
{if $arr_grp[grouparray].grp_ID == 1}            
            <span style="color:red">{$arr_grp[grouparray].grp_name|escape:'html'}</span>
{else}
            {$arr_grp[grouparray].grp_name|escape:'html'}
{/if}            
        </td>


        
        <td>{strip}
            <a href="#" onClick="ap_ext_editGroup('{$arr_grp[grouparray].grp_ID}'); $(this).blur(); return false;">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif" title="{$kga.lang.editgrp}" width="13" height="13" alt="{$kga.lang.editgrp}" border="0">
            </a>
            
            &nbsp;
{*
            <a href="#" onClick="switchGrp('{$arr_grp[grouparray].grp_ID}'); return false;">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/auge.png" title="{$kga.lang.switchgrp}" width="16" height="13" alt="{$kga.lang.switchgrp}" border="0">
            </a>
            
            &nbsp;
            
            <a href="#" onClick="backupGrp({$arr_grp[grouparray].grp_ID}); return false;">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_backup.gif" title="{$kga.lang.backupgrp}" width="12" height="13" border="0" alt="{$kga.lang.backupgrp}">
            </a>

            &nbsp;
            
            <a href="mailto:{$arr_grp[grouparray].grp_mail|escape:'html'}">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_mail_.gif" title="{$kga.lang.mailgrp}" width="12" height="13" alt="{$kga.lang.mailgrp}" border="0">
            </a>
            
            &nbsp;
*}            
            
{if $arr_grp[grouparray].count_users == 0}            
            <a href="#" onClick="ap_ext_deleteGroup({$arr_grp[grouparray].grp_ID})">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan.png" title="{$kga.lang.delgrp}" width="13" height="13" alt="{$kga.lang.delgrp}" border="0">
            </a>
{else}
             <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan_.png" title="{$kga.lang.delgrp}" width="13" height="13" alt="{$kga.lang.delgrp}" border="0">
{/if}            
            
        {/strip}</td>
        
        <td>{$arr_grp[grouparray].count_users}</td>
        
        
{*Display name(s) of the group-leader(s)*}
    

        <td>
            {foreach item=leader from=$arr_grp[grouparray].leader_name}
            {$leader|escape:'html'}
            {/foreach}
        </td>




    </tr>
{/section}
</tbody>
</table>

