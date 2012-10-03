<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<link rel="SHORTCUT ICON" href="favicon.ico">
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="robots" value="noindex,nofollow" />
<title>Kimai <?php echo $this->kga['lang']['login']?></title>
<script type="text/javascript" src="libraries/jQuery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="libraries/jQuery/jquery.cookie.js"></script>
<?php if ($this->kga['check_at_startup']): ?>
<script type="text/javascript" src="js/main.js"></script>
<?php endif; ?>
<script type='text/javascript'>function setfocus() { document.form1.name.focus(); }</script>
<script type='text/javascript'>
    $(function(){
        $("#JSwarning").remove();
        $.cookie('KimaiCookietest', 'jes');
        KimaiCookietest = $.cookie('KimaiCookietest');
        if (KimaiCookietest == 'jes') {
            $("#cookiewarning").remove();
            $.cookie('KimaiCookietest', '', {expires: -1});
        }
        if (!$("#warning p").size()) $("#warning").remove();
    });
</script>


<?php if ($this->kga['check_at_startup']): ?>
<script type='text/javascript'>
    $(function(){
        checkupdate("core/");
    });
</script>
<?php endif; ?>

<link rel="stylesheet" type="text/css" media="screen" href="css/login.css" />
</head>
<body onLoad="setfocus();">
