<script type="text/javascript"> 
    $(document).ready(function() {
        deb_ext_onload();
    }); 
</script>

<?php

$postTitle = '';
$preTitle = '';
if ($this->kga['delete_logfile']) {
    $preTitle = '<a href="#" title="Clear" onclick="deb_ext_clearLogfile();return false;">'.$this->icons('delete').'</a>';
}
$postTitle .= '
    <form id="deb_ext_shoutbox" action="../extensions/ext_debug/processor.php" method="post">
        <input type="text" id="deb_ext_shoutbox_field" name="axValue" value="shoutbox"/>
        <input name="id" type="hidden" value="0" />
        <input name="axAction" type="hidden" value="shoutbox" />
    </form>';

echo $this->extensionScreen(
    array(
        'pre_title' => $preTitle,
        'post_title' => $postTitle,
        'title' => 'DEBUG LOGFILE ' . $this->limitText,
        'id'    => 'deb_ext_logfile_header',
        'level' => array('deb_ext_logfile_wrap')
    )
)->getHeader();
?>
<div id="deb_ext_logfile"></div>

<?php echo $this->extensionScreen()->getFooter(); ?>

<?php
echo $this->extensionScreen(
    array(
        'pre_title' => '<a href="#" title="Clear" onclick="deb_ext_reloadKGA();return false;">'. $this->icons('reload') . '</a>',
        'title' => 'KIMAI GLOBAL ARRAY ($kga)',
        'id'    => 'deb_ext_kga_header',
        'level' => array('deb_ext_kga_wrap')
    )
)->getHeader();
?>
<div id="deb_ext_kga"><pre><?php echo $this->kga_display; ?></pre></div>
<?php echo $this->extensionScreen()->getFooter(); ?>
