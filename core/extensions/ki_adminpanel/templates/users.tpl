{*########## field for add new user ##########*}
<form>
    <input type=text id="newuser" class="formfield"></input>
    <input class='btn_ok' type="submit" value="{$kga.lang.adduser}" onclick="adminPanel_extension_newUser(); return false;">
{if $showDeletedUsers}    
    <input class='btn_ok' type="button" value="{$kga.lang.hidedeletedusers}" onclick="adminPanel_extension_hideDeletedUsers(); return false;">
{else}
    <input class='btn_ok' type="button" value="{$kga.lang.showdeletedusers}" onclick="adminPanel_extension_showDeletedUsers(); return false;">
{/if}
</form>
{*########## field for add new user ##########*}



<br />



<table>

    <thead>
      <tr>
          <th>{$kga.lang.username}</th>
          <th>{$kga.lang.options}</th>
          <th>{$kga.lang.status}</th>
          <th>{$kga.lang.group}</th>
      </tr>
    </thead>


    <tbody>
{section name=userarray loop=$users}{strip}
    <tr class='{cycle values="even,odd"}'>
    
    
        
{*########## USER NAME ##########*}
        <td>
{if $curr_user == $users[userarray].name}
            <strong style="color:#00E600">{$users[userarray].name|escape:'html'}</strong>
{else}
    {if $users[userarray].trash}<span style="color:#999">{/if}
            {$users[userarray].name|escape:'html'}
    {if $users[userarray].trash}</span>{/if}
{/if}
        </td>
{*########## /USER NAME ##########*}







{*########## Option cells ##########*}
        <td>
            
            <a href="#" onClick="adminPanel_extension_editUser('{$users[userarray].userID}'); $(this).blur(); return false;">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif" title="{$kga.lang.editUser}" width="13" height="13" alt="{$kga.lang.editUser}" border="0">
            </a>
            
            &nbsp;

{*
{if $curr_user != $users[userarray].name}            
            <a href="#" onClick="switchUser('{$users[userarray].userID}'); return false;">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/auge.png" title="{$kga.lang.switchUser}" width="16" height="13" alt="{$kga.lang.switchUser}" border="0">
            </a>
{else} 
            <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/auge_.gif" title="{$kga.lang.switchUser}" width="16" height="13" alt="{$kga.lang.switchUser}" border="0">
{/if}

            &nbsp;
     
            <a href="#" onClick="backupUser({$users[userarray].userID}); return false;">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_backup.gif" title="{$kga.lang.backupUser}" width="12" height="13" border=0 alt="{$kga.lang.backupUser}">
            </a>
            
            &nbsp;
            
*}
            
{if $users[userarray].mail}            
            <a href="mailto:{$users[userarray].mail|escape:'html'}">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_mail.gif" title="{$kga.lang.mailUser}" width="12" height="13" alt="{$kga.lang.mailUser}" border="0">
            </a>           
{else} 
            <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_mail_.gif" title="{$kga.lang.mailUser}" width="12" height="13" alt="{$kga.lang.mailUser}" border="0">
{/if}

            &nbsp;

{if $curr_user != $users[userarray].name}
            <a href="#" id="deleteUser{$users[userarray].userID}" onClick="adminPanel_extension_deleteUser({$users[userarray].userID})">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan.png" title="{$kga.lang.deleteUser}" width="13" height="13" alt="{$kga.lang.deleteUser}" border="0">
            </a>
{else} 
            <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan_.png" title="{$kga.lang.deleteUser}" width="13" height="13" alt="{$kga.lang.deleteUser}" border="0">
{/if}
            
        </td>      
{*########## /Option cells ##########*}








{*########## Status cells ##########*}
        <td>
{if $users[userarray].status == 0}
            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/crown.png' alt='{$kga.lang.adminUser}' title='{$kga.lang.adminUser}' border="0">
{/if}

{if $users[userarray].status == 1}
            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/leader.gif' alt='{$kga.lang.groupleader}' title='{$kga.lang.groupleader}' border="0">
{/if}

{if $users[userarray].status == 2}
            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/user.gif' alt='{$kga.lang.user}' title='{$kga.lang.user}' border="0">
{/if}

            &nbsp;
            
{if $users[userarray].active == 1}
    {if $curr_user != $users[userarray].name}
            <a href="#" id="ban{$users[userarray].userID}" onClick="adminPanel_extension_banUser('{$users[userarray].userID}'); return false;">
                <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/jipp.gif' alt='{$kga.lang.activeAccount}' title='{$kga.lang.activeAccount}' border="0" width="16" height="16" />
            </a>
    {else}
            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/jipp_.gif' alt='{$kga.lang.activeAccount}' title='{$kga.lang.activeAccount}' border="0" width="16" height="16" />
    {/if}
{/if}
            
{if $users[userarray].active == 0}
            <a href="#" id="ban{$users[userarray].userID}" onClick="adminPanel_extension_unbanUser('{$users[userarray].userID}'); return false;">
                <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/lock.png' alt='{$kga.lang.bannedUser}' title='{$kga.lang.bannedUser}' border="0" width="16" height="16" />
            </a>
{/if}

            &nbsp;
            
{if $users[userarray].passwordSet == "no"}
            <a href="#" onClick="adminPanel_extension_editUser('{$users[userarray].userID}'); $(this).blur(); return false;">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/caution_mini.png" width="16" height="16" title='{$kga.lang.nopasswordset}' border="0">
            </a>
{/if}

            &nbsp;

{if $users[userarray].trash} 
            <strong style="color:red">X</strong>
{/if}


        </td>
{*########## /Status cells ##########*}




{*########## Group cells ##########*}
        <td>
            {section name=group loop=$users[userarray].groups} 
              {$users[userarray].groups[group]|escape:'html'}{if $smarty.section.group.last eq false}, {/if}
            {/section}
        </td>      
{*########## Group cells ##########*}

    </tr>
    </tbody>
{/strip}{/section}

</table>

<p><strong>{$kga.lang.hint}</strong> {$kga.lang.rename_caution_before_username} '{$curr_user|escape:'html'}' {$kga.lang.rename_caution_after_username}</p>