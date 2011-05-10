{*########## field for add new user ##########*}
<form>
    <input type=text id="newuser" class="formfield"></input>
    <input class='btn_ok' type="submit" value="{$kga.lang.adduser}" onclick="ap_ext_newUser(); return false;">
{if $showDeletedUsers}    
    <input class='btn_ok' type="button" value="{$kga.lang.hidedeletedusers}" onclick="ap_ext_hideDeletedUsers(); return false;">
{else}
    <input class='btn_ok' type="button" value="{$kga.lang.showdeletedusers}" onclick="ap_ext_showDeletedUsers(); return false;">
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
{section name=userarray loop=$arr_usr}{strip}
    <tr class='{cycle values="even,odd"}'>
    
    
        
{*########## USER NAME ##########*}
        <td>
{if $curr_user == $arr_usr[userarray].usr_name}
            <strong style="color:#00E600">{$arr_usr[userarray].usr_name|escape:'html'}</strong>
{else}
    {if $arr_usr[userarray].usr_trash}<span style="color:#999">{/if}
            {$arr_usr[userarray].usr_name|escape:'html'}
    {if $arr_usr[userarray].usr_trash}</span>{/if}
{/if}
        </td>
{*########## /USER NAME ##########*}







{*########## Option cells ##########*}
        <td>
            
            <a href="#" onClick="ap_ext_editUser('{$arr_usr[userarray].usr_ID}'); $(this).blur(); return false;">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/edit2.gif" title="{$kga.lang.editusr}" width="13" height="13" alt="{$kga.lang.editusr}" border="0">
            </a>
            
            &nbsp;

{*
{if $curr_user != $arr_usr[userarray].usr_name}            
            <a href="#" onClick="switchUsr('{$arr_usr[userarray].usr_ID}'); return false;">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/auge.png" title="{$kga.lang.switchusr}" width="16" height="13" alt="{$kga.lang.switchusr}" border="0">
            </a>
{else} 
            <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/auge_.gif" title="{$kga.lang.switchusr}" width="16" height="13" alt="{$kga.lang.switchusr}" border="0">
{/if}

            &nbsp;
     
            <a href="#" onClick="backupUsr({$arr_usr[userarray].usr_ID}); return false;">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_backup.gif" title="{$kga.lang.backupusr}" width="12" height="13" border=0 alt="{$kga.lang.backupusr}">
            </a>
            
            &nbsp;
            
*}
            
{if $arr_usr[userarray].usr_mail}            
            <a href="mailto:{$arr_usr[userarray].usr_mail|escape:'html'}">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_mail.gif" title="{$kga.lang.mailusr}" width="12" height="13" alt="{$kga.lang.mailusr}" border="0">
            </a>           
{else} 
            <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_mail_.gif" title="{$kga.lang.mailusr}" width="12" height="13" alt="{$kga.lang.mailusr}" border="0">
{/if}

            &nbsp;

{if $curr_user != $arr_usr[userarray].usr_name}
            <a href="#" id="delete_usr{$arr_usr[userarray].usr_ID}" onClick="ap_ext_deleteUser({$arr_usr[userarray].usr_ID})">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan.png" title="{$kga.lang.delusr}" width="13" height="13" alt="{$kga.lang.delusr}" border="0">
            </a>
{else} 
            <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/button_trashcan_.png" title="{$kga.lang.delusr}" width="13" height="13" alt="{$kga.lang.delusr}" border="0">
{/if}
            
        </td>      
{*########## /Option cells ##########*}








{*########## Status cells ##########*}
        <td>
{if $arr_usr[userarray].usr_sts == 0}
            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/crown.png' alt='{$kga.lang.adminusr}' title='{$kga.lang.adminusr}' border="0">
{/if}

{if $arr_usr[userarray].usr_sts == 1}
            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/leader.gif' alt='{$kga.lang.groupleader}' title='{$kga.lang.groupleader}' border="0">
{/if}

{if $arr_usr[userarray].usr_sts == 2}
            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/user.gif' alt='{$kga.lang.regusr}' title='{$kga.lang.regusr}' border="0">
{/if}

            &nbsp;
            
{if $arr_usr[userarray].usr_active == 1}
    {if $curr_user != $arr_usr[userarray].usr_name}
            <a href="#" id="ban{$arr_usr[userarray].usr_ID}" onClick="ap_ext_banUser('{$arr_usr[userarray].usr_ID}'); return false;">
                <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/jipp.gif' alt='{$kga.lang.activeusr}' title='{$kga.lang.activeusr}' border="0" width="16" height="16" />
            </a>
    {else}
            <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/jipp_.gif' alt='{$kga.lang.activeusr}' title='{$kga.lang.activeusr}' border="0" width="16" height="16" />
    {/if}
{/if}
            
{if $arr_usr[userarray].usr_active == 0}
            <a href="#" id="ban{$arr_usr[userarray].usr_ID}" onClick="ap_ext_unbanUser('{$arr_usr[userarray].usr_ID}'); return false;">
                <img src='../skins/{$kga.conf.skin|escape:'html'}/grfx/lock.png' alt='{$kga.lang.bannedusr}' title='{$kga.lang.bannedusr}' border="0" width="16" height="16" />
            </a>
{/if}

            &nbsp;
            
{if $arr_usr[userarray].usr_pw == "no"}
            <a href="#" onClick="ap_ext_editUser('{$arr_usr[userarray].usr_ID}'); $(this).blur(); return false;">
                <img src="../skins/{$kga.conf.skin|escape:'html'}/grfx/caution_mini.png" width="16" height="16" title='{$kga.lang.nopassword}' border="0">
            </a>
{/if}

            &nbsp;

{if $arr_usr[userarray].usr_trash} 
            <strong style="color:red">X</strong>
{/if}


        </td>
{*########## /Status cells ##########*}




{*########## Group cells ##########*}
        <td>
{if $arr_usr[userarray].usr_grp == 1}
            <span style="color:red">{$arr_usr[userarray].grp_name|escape:'html'}</span>
{else}
            {$arr_usr[userarray].grp_name|escape:'html'}
{/if}
        </td>      
{*########## Group cells ##########*}

    </tr>
    </tbody>
{/strip}{/section}

</table>

<p><strong>{$kga.lang.hint}</strong> {$kga.lang.usr_caution1} '{$curr_user|escape:'html'}' {$kga.lang.usr_caution2}</p>