        <div id='login'>
            <form action='index.php?a=checklogin' name='form1' method='post'>
                <fieldset>
                    <label for='kimaiusername'>
                        {$kga.lang.username}:
                    </label>
                    <input type='text' name='name' />
                    <label for='kimaipassword'>
                        {$kga.lang.password}:
                    </label>
                    <input type='password' name='password' />
                    {$selectbox}
                    <button type='submit'>Submit</button>
                </fieldset>
            </form>

            
            <div id="warning">
                <p id="JSwarning"><strong style='color:red'>{$kga.lang.JSwarning}</strong></p>
                <p id="cookiewarning"><strong style='color:red'>{$kga.lang.cookiewarning}</strong></p>
            </div>
            {include file="misc/copyrightnotes.tpl"}
            {* 
                YOU ARE NOT ALLOWED TO REMOVE THE COPYRIGHT NOTES! YOU MAY CHANGE THE APPEARANCE OF THE LOGIN WINDOW
                BUT REMOVING THE CREDITS IS STRICTLY PROHIBITED. If you feel uncomfortable with this rule - use
                other time tracking software, please.
                
            *}
        </div>
</body>
</html>