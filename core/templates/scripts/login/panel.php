        <div id='login'>
            <form action='index.php?a=checklogin' name='form1' method='post'>
                <fieldset>
                    <label for="kimaiusername">
                        <?php echo $this->kga['lang']['username']?>:
                    </label>
                    <input type='text' name="name" id="kimaiusername" />
                    <label for="kimaipassword">
                        <?php echo $this->kga['lang']['password']?>:
                    </label>
                    <input type='password' name="password" id="kimaipassword" />
                    <?php echo $this->selectbox ?>
                    <button type='submit'>Submit</button>
                </fieldset>
            </form>

            
            <div id="warning">
                <p id="JSwarning"><strong style="color:red"><?php $this->kga['lang']['JSwarning']?></strong></p>
                <p id="cookiewarning"><strong style="color:red"><?php $this->kga['lang']['cookiewarning']?></strong></p>
            </div>
            <?php echo $this->partial('misc/copyrightnotes.php', array('kga' => $this->kga, 'devtimespan' => $this->devtimespan)); ?>
            <!-- 
                YOU ARE NOT ALLOWED TO REMOVE THE COPYRIGHT NOTES! YOU MAY CHANGE THE APPEARANCE OF THE LOGIN WINDOW
                BUT REMOVING THE CREDITS IS STRICTLY PROHIBITED. If you feel uncomfortable with this rule - use
                other time tracking software, please.
                
            -->
        </div>
</body>
</html>
