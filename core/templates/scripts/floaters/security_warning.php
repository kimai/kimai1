<div id="floater_innerwrap">

    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->kga['lang']['securityWarning']?></span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();"><?php echo $this->kga['lang']['close']?></a>
        </div>       
    </div>

    <div class="floater_content" style="padding:20px">

        <h2 style="color:red"><?php echo $this->kga['lang']['securityWarning']?></h2> 

        <b><?php echo $this->kga['lang']['installerWarningHeadline']?></b> <br/><br/>
        
        <?php echo $this->kga['lang']['installerWarningText']?>
        
    </div>
</div>
