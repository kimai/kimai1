<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<link rel="SHORTCUT ICON" href="favicon.ico">
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex,nofollow" />
<title>Kimai <?php echo $this->kga['lang']['login']?></title>
<script type="text/javascript" src="libraries/jQuery/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="libraries/jQuery/jquery.cookie.js"></script>
<?php if ($this->kga['check_at_startup']): ?>
<script type="text/javascript" src="js/main.js"></script>
<?php endif; ?>
<script type='text/javascript'>
var requestData = <?php echo json_encode($this->requestData); ?>;
    $(function(){
        //$("#JSwarning").remove();
        $.cookie('KimaiCookietest', 'jes');
        KimaiCookietest = $.cookie('KimaiCookietest');
        if (KimaiCookietest == 'jes') {
            $("#cookiewarning").remove();
            $.cookie('KimaiCookietest', '', {expires: -1});
        }
        if (!$("#warning").find("p").size()) $("#warning").remove();

        $("#forgotPasswordLink").click(function(event) {
          event.preventDefault();
          $("#login").fadeOut();
          $("#forgotPassword").fadeIn();
          return false;
        });

        $("#loginButton").click(function() {
          requestData['password'] = $("#password").val();
          event.preventDefault();
          $.ajax({
            type: "POST",
            url: "processor.php?a=resetPassword",
            data: requestData,
            dataType: "json",
            success: function(data) {
              $("#login").fadeOut();
              $("#message").find("p").html(data.message);
              if (data.showLoginLink)
                $("#message").find("a").show();
              $("#message").fadeIn();
            }
          });
          return false;
        });

        $("#kimaiusername").focus();
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
<body>

  <div id="content">

    <div id="box">

    <div id="login" <?php if ($this->keyCorrect): ?>style="display:block" <?php endif; ?>>
        <form action='index.php?a=checklogin' name='form1' method='post'>
            <fieldset>
                <label for="password">
                    <?php echo $this->kga['lang']['newPassword']?>:
                </label>
                <input type='password' name="password" id="password" />
                <label for="password2">
                    <?php echo $this->kga['lang']['retypePassword']?>:
                </label>
                <input type='password' name="password2" id="password2" />
                <?php echo $this->selectbox ?>
                <button id="loginButton" type='submit'></button>
            </fieldset>
        </form>
      </div>
      <div id="message" <?php if (!$this->keyCorrect): ?>style="display:block" <?php endif; ?>>
        <p>
          <?php echo $this->kga['lang']['passwordReset']['invalidKey']; ?>
        </p>
        <a style="display:none" href="index.php"><?php echo $this->kga['lang']['passwordReset']['returnToLogin'] ?></a>
      </div>
            <div id="warning">
                <p id="JSwarning"><strong style="color:red"><?php $this->kga['lang']['JSwarning']?></strong></p>
                <p id="cookiewarning"><strong style="color:red"><?php $this->kga['lang']['cookiewarning']?></strong></p>
            </div>
        </div>
            <?php echo $this->partial('misc/copyrightnotes.php', array('kga' => $this->kga, 'devtimespan' => $this->devtimespan)); ?>
</div>
</body>
</html>