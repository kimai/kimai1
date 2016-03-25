<script type="text/javascript"> 
    $(document).ready(function() {
        deb_ext_onload();
    }); 
</script>

<div id="deb_ext_kga_header">
     <strong>KIMAI GLOBAL ARRAY ($kga)</strong>
    <select id="kga_section">
        <?php
        foreach($this->kga_sections as $key => $name) {
            echo '<option value="' . $key . '">' . $name . '</option>';
        }
        ?>
    </select>
</div>

<div id="deb_ext_kga_wrap">
    <div id ="deb_ext_kga">
        <pre id="deb_ext_kga_cnt"></pre>
    </div>
</div>

<div id="deb_ext_logfile_header">
    <?php if ($this->delete_logfile): ?>
        <div id="deb_ext_buttons">
            <a href="#" title="<?php echo $this->translate('debug:clear'); ?>" onclick="deb_ext_clearLogfile();return false;">
                <img src="<?php echo $this->skin('/grfx/button_trashcan.png'); ?>" width="13" height="13" alt="<?php echo $this->translate('debug:clear'); ?>">
            </a>
        </div>
    <?php endif; ?>

    <strong><?php echo $this->translate('debug:logfile'); ?></strong> <?php echo $this->limitText ?>

    <form id="deb_ext_shoutbox" action="../extensions/ext_debug/processor.php" method="post">
        <input type="text" id="deb_ext_shoutbox_field" name="axValue" value="shoutbox"/>
        <input name="id" type="hidden" value="0" />
        <input name="axAction" type="hidden" value="shoutbox" />
    </form>

</div>

<div id ="deb_ext_logfile_wrap">
    <div id ="deb_ext_logfile"></div>
</div>