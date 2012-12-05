<script type="text/javascript"> 
    $(document).ready(function() {
        deb_ext_onload();
    }); 
</script>

<div id="deb_ext_kga_header">
	<a href="#" title="Clear" onclick="deb_ext_reloadKGA();return false;"><img src="../extensions/ext_debug/grfx/action_refresh.png"  alt="Reload KGA"></a>
     <strong>KIMAI GLOBAL ARRAY ($kga)</strong> 
</div>

<div id="deb_ext_kga_wrap">
    <div id ="deb_ext_kga">
        <pre>
<?php echo $this->kga_display; ?>
        </pre>
    </div>
</div>

<div id="deb_ext_logfile_header">
    <div id="deb_ext_buttons">
<?php if ($this->kga['delete_logfile']): ?>
        <a href="#" title="Clear" onclick="deb_ext_clearLogfile();return false;"><img src="../skins/<?php echo $this->kga['conf']['skin'] ?>/grfx/button_trashcan.png" width="13" height="13" alt="Clear"></a>
<?php endif; ?>
    </div>
    <strong>DEBUG LOGFILE</strong> <?php echo $this->limitText ?>
    
    <form id="deb_ext_shoutbox" action="../extensions/ext_debug/processor.php" method="post" <?php if ($this->kga['delete_logfile']):?> style="margin-right:20px" <?php endif; ?>> 
        <input type="text" id="deb_ext_shoutbox_field" name="axValue" value="shoutbox"/>
        <input name="id" type="hidden" value="0" />
        <input name="axAction" type="hidden" value="shoutbox" />
    </form>

</div>

<div id ="deb_ext_logfile_wrap">
    <div id ="deb_ext_logfile"></div>
</div>