<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->translate('securityWarning')?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->translate('close') ?></a>
        </div>
    </div>
    <div class="floater_content" style="padding:20px">
        <h2 style="color:red"><?php echo $this->translate('securityWarning')?></h2>
        <b><?php echo $this->translate('installerWarningHeadline')?></b> <br/><br/>
        <?php echo $this->translate('installerWarningText')?>
    </div>
</div>