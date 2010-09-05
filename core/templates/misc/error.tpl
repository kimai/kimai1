<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <link rel="SHORTCUT ICON" href="favicon.ico">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta name="robots" value="noindex,nofollow" />
    <title>Kimai Error</title>
    <link rel="stylesheet" type="text/css" media="screen" href="css/error.css" />
    <script src="../../libraries/jQuery/jquery-1.4.2.min.js" type="text/javascript" charset="utf-8"></script>
    
    {literal}    
        <script type="text/javascript"> 
            $(document).ready(function() {
                $('#ok').focus();
            }); 
        </script>
    {/literal}
    
</head>
<body>

<div id="error_wrapper">
    <dix id="error_txt">
        <h3>{$headline}</h3>
        {$message}
    </dix>
    <div id="error_button">
        <form action="index.php" method="post"><input type="submit" value="OK" id="ok"/></form>
    </div>
</div>

</body>
</html>