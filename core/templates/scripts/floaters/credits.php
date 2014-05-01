<?php
echo $this->floater()
    ->setTitle($this->translate('about') . ' Kimai')
    ->setShowCancelButton(false)
    ->setShowSaveButton(false)
    ->floaterBegin();
?>

    <h3>Kimai - Open Source Time Tracking</h3>
    <?php echo 'v', $this->kga['version'],'.',$this->kga['revision'],' - &copy; ',$this->devtimespan;?> by the Kimai Core-Development-Team<br />
    <?php echo $this->kga['lang']['credits']?>

<?php echo $this->floater()->floaterEnd(); ?>