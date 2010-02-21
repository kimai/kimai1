<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <link rel="SHORTCUT ICON" href="favicon.ico">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta name="robots" value="noindex,nofollow" />
    <title>Kimai Installer</title>
    <link rel="stylesheet" type="text/css" media="screen" href="css/login.css" />
</head>
<body>
    <div id='install'>
        
            <form action="installer/install.php" method="post">
                <fieldset>
                    
                    <div class="installtext">
                        <div class="welcome">Welcome!</div>
                        
                        <div class="txt">We need to set up the database first.</div>
                        
                        <div class="txt">
                            Due to security reasons you should erase the "install.php" file after installation!
                        </div>   
                        
                        <div class="txt">
                            <input type="checkbox" name="accept" value=1 style="width:15px;height:15px;display:inline;">
                  
                            {if $disagreedGPL}<strong style="color:red">{/if} I accept the terms of the GNU GPL Version 3{if $disagreedGPL}</strong>{/if}
                    
                            {if $disagreedGPL}<br /><img src="grfx/caution_small.png" alt="Caution" /> <span style="color:red">You have to accept the the terms!</span>{/if}
                        </div>
                        
                    </div>
        
                        
                    <button type="submit">Install</button>
                </fieldset>
            </form>
        
            {include file="misc/copyrightnotes.tpl"} 
    </div>
</body>
</html>