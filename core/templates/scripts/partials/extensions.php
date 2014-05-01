<div id="extdiv_0" class="ext ki_timesheet"></div>
<?php
for ($i = 0; $i < count($this->extensions); $i++)
{
    if ($this->extensions[$i] != "ki_timesheet")
    {
        ?>
        <div id="extdiv_<?php echo $i+1; ?>" class="ext <?php echo $this->extensions[$i]['key']?>" style="display:none;"></div>
    <?php
    }
}
?>
