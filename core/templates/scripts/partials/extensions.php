<?php
$i = 0;
foreach($this->extensions as $extension)
{
    $display = ($i==0) ? '' : 'display:none;';
    ?>
    <div id="extdiv_<?php echo $i++; ?>" class="ext <?php echo $extension->getId()?>" style="<?php echo $display; ?>"></div>
    <?php
}
?>