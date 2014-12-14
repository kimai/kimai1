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
          $("#forgotPasswordUsername").val("");
          $("#forgotPassword").fadeIn();
          return false;
        });

        $("#resetPassword").click(function() {
          event.preventDefault();
          $("#forgotPasswordUsername").blur();
          $.ajax({
            type: "POST",
            url: "processor.php?a=forgotPassword",
            data: {
              name:  $("#forgotPasswordUsername").val()
            },
            dataType: "json",
            success: function(data) {
              $("#forgotPassword").fadeOut();
              $("#forgotPasswordConfirmation").find("p").html(data.message);
              $("#forgotPasswordConfirmation").fadeIn();
            }
          });
          return false;
        });

        $(".returnToLogin").click(function(event) {
          event.preventDefault();
          $("#login").fadeIn();
          $("#forgotPassword").fadeOut();
          $("#forgotPasswordConfirmation").fadeOut();
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

      <div id="login" style="display:block">
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
                <button id="loginButton" type='submit'></button>
                <a id="forgotPasswordLink" href=""><?php echo $this->kga['lang']['forgotPassword'] ?></a>
            </fieldset>
        </form>
      </div>

      <div id="forgotPassword">
<?php echo $this->kga['lang']['passwordReset']['instructions']; ?>
        <form action="">
            <fieldset>
                <label for="forgotPasswordUsername">
                    <?php echo $this->kga['lang']['username']?>:
                </label>
                <input type='text' name="name" id="forgotPasswordUsername" />
                <button id="resetPassword" type='submit'>reset password</button>
            </fieldset>
        </form>
        <a class="returnToLogin" href=""><?php echo $this->kga['lang']['passwordReset']['returnToLogin'] ?></a>
      </div>

      <div id="forgotPasswordConfirmation">
        <p></p>
        <a class="returnToLogin" href=""><?php echo $this->kga['lang']['passwordReset']['returnToLogin'] ?></a>
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
