<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->kga['lang']['about']?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->kga['lang']['close']?></a>
        </div>       
    </div>
    <div class="floater_content">
        <h2>Kimai - Open Source Time Tracking</h2> 
        <?php echo 'v', $this->kga['version'],'.',$this->kga['revision'],' - &copy; ',$this->devtimespan;?> by the Kimai Core-Development-Team<br />
        <?php echo $this->kga['lang']['credits']?>
    </div>
</div>
