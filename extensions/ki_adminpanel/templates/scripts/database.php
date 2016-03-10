<?php echo $this->kga['lang']['DBname']?>: <?php echo $this->escape($this->kga['server_database']);?>
<?php if (file_exists(WEBROOT . '/updater/db_restore.php')) { ?>
    <br /><br />
    <a href="../updater/db_restore.php">Database Backup Utility</a>
<?php } ?>
