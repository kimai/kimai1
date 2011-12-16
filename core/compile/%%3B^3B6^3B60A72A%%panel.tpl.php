<?php /* Smarty version 2.6.20, created on 2011-12-07 17:26:44
         compiled from login/panel.tpl */ ?>
        <div id='login'>
            <form action='index.php?a=checklogin' name='form1' method='post'>
                <fieldset>
                    <label for='kimaiusername'>
                        <?php echo $this->_tpl_vars['kga']['lang']['username']; ?>
:
                    </label>
                    <input type='text' name='name' />
                    <label for='kimaipassword'>
                        <?php echo $this->_tpl_vars['kga']['lang']['password']; ?>
:
                    </label>
                    <input type='password' name='password' />
                    <?php echo $this->_tpl_vars['selectbox']; ?>

                    <button type='submit'>Submit</button>
                </fieldset>
            </form>

            
            <div id="warning">
                <p id="JSwarning"><strong style='color:red'><?php echo $this->_tpl_vars['kga']['lang']['JSwarning']; ?>
</strong></p>
                <p id="cookiewarning"><strong style='color:red'><?php echo $this->_tpl_vars['kga']['lang']['cookiewarning']; ?>
</strong></p>
            </div>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "misc/copyrightnotes.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                    </div>
</body>
</html>