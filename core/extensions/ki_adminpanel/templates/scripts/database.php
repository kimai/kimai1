<?php echo $this->kga['lang']['DBname']?>: <?php echo $this->escape($this->kga['server_database']);?>
<br />
<br />
<a href="../db_restore.php">Database Backup Utility</a>

<?php /*
<br /><br />

<?php echo $this->kga['lang']['lastdbbackup']?>: 

<?php if ($this->kga['conf']['lastdbbackup']): ?>
    <?php echo strftime("%c", $this->kga['conf']['lastdbbackup']); ?>
<?php else: ?>
    none
<?php endif; ?>

<br />

<input class='btn_ok' type='submit' value='<?php echo $this->kga['lang']['runbackup']?>' onClick='backupAll(); return false;' />

*/ ?>