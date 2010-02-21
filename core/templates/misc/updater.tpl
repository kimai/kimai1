<!-- FILE IS TEMPORARILY NOT IN USE! -->


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<link rel="SHORTCUT ICON" href="favicon.ico">
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="robots" value="noindex,nofollow" />
<title>Kimai {$kga.lang.login}</title>

<link rel="stylesheet" type="text/css" media="screen" href="css/login.css" />

</head>
<body>
<body>

        <div id='login'>
            <div align='center'>
                              
                <img src='grfx/caution.png' />
                <h1 style="color:red">UPDATE WARNING!</h1>
        
                <div id="message" style="width:500px">
                    <p><strong style='color:#C81C22'>{$kga.lang.update}</strong></p>
                </div>
        
                <p>
                <a href='updater.php?ok=1' title="{$kga.lang.updateNow}"><img src='grfx/but_update.gif' border = "0"/></a></p>
                <p style="color:#C81C22;">CAUTION: If you use more than one database (server_ext_database),<br />each of them has to be updated seperately!
                </p> 
        
            </div>
        </div>
        {$refresh}
    </body>
</html>