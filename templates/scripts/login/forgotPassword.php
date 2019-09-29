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
	<script type="text/javascript">
	var requestData = <?php echo json_encode($this->requestData); ?>;

	$(function(){
		Cookies.set('KimaiCookieTest', 'yes');
		if (Cookies.get('KimaiCookieTest') == 'yes') {
			$("#cookiewarning").remove();
			Cookies.remove('KimaiCookieTest');
		}

		if ($('#warning').find("p").size() < 2) {
			$('#warning').remove();
		}

		$('#forgotPasswordLink').click(function(e) {
			e.preventDefault();
			$('#login').fadeOut();
			$('#forgotPassword').fadeIn();
			return false;
		});

		$('#loginButton').click(function(e) {
			requestData['password'] = $('#password').val();
			e.preventDefault();
			$.ajax({
				type: 'POST',
				url: 'processor.php?a=resetPassword',
				data: requestData,
				dataType: 'json',
				success: function(data) {
					$('#login').fadeOut();
					$('#message').find('p').html(data.message);
					if (data.showLoginLink) {
						$('#message').find('a').show();
					}
					$('#message').fadeIn();
				}
			});
			return false;
		});

		$('#kimaiusername').focus();
	});
	</script>
</head>
<body>
<div id="content">
	<div id="box">
		<div id="login" <?php if ($this->keyCorrect): ?>style="display:block" <?php endif; ?>>
			<form action="index.php?a=checklogin" name="form1" method="post">
				<fieldset>
					<label for="password"><?php echo $this->translate('newPassword') ?>:</label>
					<input type="password" name="password" id="password" />
					<label for="password2"><?php echo $this->translate('retypePassword') ?>:</label>
					<input type="password" name="password2" id="password2" />
					<button id="loginButton" type="submit"></button>
				</fieldset>
			</form>
		</div>
		<div id="message" <?php if (!$this->keyCorrect): ?>style="display:block" <?php endif; ?>>
			<p><?php echo $this->translate('passwordReset:invalidKey'); ?></p>
			<a style="display:none" href="index.php"><?php echo $this->translate('passwordReset:returnToLogin') ?></a>
		</div>
		<div id="warning">
			<p id="JSwarning"><strong style="color:red"><?php echo $this->translate('JSwarning') ?></strong></p>
			<p id="cookiewarning"><strong style="color:red"><?php echo $this->translate('cookiewarning') ?></strong></p>
		</div>
	</div>
	<?php echo $this->partial('misc/copyrightnotes.php', ['kga' => $this->kga, 'devtimespan' => $this->devtimespan]); ?>
</div>
</body>
</html>