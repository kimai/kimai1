<span style="color:red">This option is still under construction. Sorry... We may deliver this finally with v0.8.1 ...</span>

<br /><br />

{$kga.lang.DBname}: {$kga.server_database}

{*
<br /><br />

{$kga.lang.lastdbbackup}: 

{if $kga.conf.lastdbbackup}  
    {$kga.conf.lastdbbackup|date_format}
{else}
    none
{/if}

<br />

<input class='btn_ok' type='submit' value='{$kga.lang.runbackup}' onClick='backupAll(); return false;' />

*}