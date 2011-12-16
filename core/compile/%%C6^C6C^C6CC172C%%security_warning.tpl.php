<?php /* Smarty version 2.6.20, created on 2011-12-07 17:39:08
         compiled from security_warning.tpl */ ?>
<div id="floater_innerwrap">

    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->_tpl_vars['kga']['lang']['securityWarning']; ?>
</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();"><?php echo $this->_tpl_vars['kga']['lang']['close']; ?>
</a>
        </div>       
    </div>

    <div class="floater_content" style="padding:20px">

        <h2 style="color:red"><?php echo $this->_tpl_vars['kga']['lang']['securityWarning']; ?>
</h2> 

        <b><?php echo $this->_tpl_vars['kga']['lang']['installerWarningHeadline']; ?>
</b> <br/><br/>
        
        <?php echo $this->_tpl_vars['kga']['lang']['installerWarningText']; ?>

        
    </div>
</div>