<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<meta name="robots" content="noindex,nofollow" />
	<title>Kimai <?php echo $this->translate('login') ?></title>
	<link rel="SHORTCUT ICON" href="favicon.ico">
	<link rel="stylesheet" type="text/css" media="screen" href="skins/<?php echo $this->skin()->getName(); ?>/login.css" />
	<script type="text/javascript" src="libraries/jQuery/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" src="libraries/jQuery/js.cookie-2.1.0.min.js"></script>
	<script type='text/javascript'>
	$(function(){
		Cookies.set('KimaiCookieTest', 'yes');

		if (Cookies.get('KimaiCookieTest') == 'yes') {
			$("#cookiewarning").remove();
			Cookies.remove('KimaiCookieTest');
		}

		if ($("#warning").find("p").size() < 2) {
			$("#warning").remove();
		}

		$("#forgotPasswordLink").click(function(event) {
			event.preventDefault();
			$("#login").fadeOut();
			$("#forgotPasswordUsername").val("");
			$("#forgotPassword").fadeIn();
			return false;
		});

		$("#resetPassword").click(function(event) {
			event.preventDefault();
			$("#forgotPasswordUsername").blur();
			$.ajax({
				type: "POST",
				url: "processor.php?a=forgotPassword",
				data: {
					name: $("#forgotPasswordUsername").val()
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
	<script type="text/javascript" src="js/main.js"></script>
	<script type="text/javascript">
	$(function(){
		checkupdate("core/");
	});
	</script>
<?php endif; ?>
</head>
<body>
<div id="content">
	<div id="box">
		<div id="login" style="display:block">
			<form action="index.php?a=checklogin" name="form1" method="post">
				<fieldset>
					<label for="kimaiusername">
						<?php echo $this->translate('username')?>:
					</label>
					<input type="text" name="name" id="kimaiusername" />
					<label for="kimaipassword">
						<?php echo $this->translate('password')?>:
					</label>
					<input type="password" name="password" id="kimaipassword" />
					<button id="loginButton" type='submit'></button>
					<a id="forgotPasswordLink" href=""><?php echo $this->translate('forgotPassword') ?></a>
				</fieldset>
			</form>
		</div>
		<div id="forgotPassword">
			<?php echo $this->translate('passwordReset:instructions'); ?>
			<form action="">
				<fieldset>
					<label for="forgotPasswordUsername">
						<?php echo $this->translate('username') ?>:
					</label>
					<input type="text" name="name" id="forgotPasswordUsername" />
					<button id="resetPassword" type="submit"><?php echo $this->translate('passwordReset:button'); ?></button>
				</fieldset>
			</form>
			<a class="returnToLogin" href=""><?php echo $this->translate('passwordReset:returnToLogin') ?></a>
		</div>
		<div id="forgotPasswordConfirmation">
			<p></p>
			<a class="returnToLogin" href=""><?php echo $this->translate('passwordReset:returnToLogin') ?></a>
		</div>
		<div id="warning" style="display:block">
			<p id="JSwarning"><strong style="color:red"><?php echo $this->translate('JSwarning') ?></strong></p>
			<p id="cookiewarning"><strong style="color:red"><?php echo $this->translate('cookiewarning') ?></strong></p>
		</div>
	</div>
	<?php echo $this->partial('misc/copyrightnotes.php', ['kga' => $this->kga, 'devtimespan' => $this->devtimespan]); ?>
</div>
</body>
</html>