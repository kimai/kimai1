<?php
// =====================
// = standard includes =
// =====================
$web = "";
$jqm = "jquery.mobile-1.0/";

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Kimai Mobile: Easy mobile Time-Tracking v0.1</title>
	<link rel="stylesheet"  href="<?php echo $jqm; ?>jquery.mobile-1.0.min.css" />
	<link rel="stylesheet"  href="<?php echo $web; ?>kimai.mobile-1.0.css" />
	<script src="<?php echo $jqm; ?>jquery-1.6.4.min.js"></script>
	<script src="<?php echo $jqm; ?>jquery.mobile-1.0.min.js"></script>
	<script src="<?php echo $web; ?>kimai.mobile-1.0.js"></script>
</head> 
<body> 
<div data-role="page" class="type-home">
	<div data-role="content">
	
		<div class="content-secondary">
	
			<div id="jqm-homeheader">
				<h1 id="jqm-logo">Kimai - your everywhere Time Tracker</h1>
			</div>
			
			<ul id="loginForm" data-role="listview" data-inset="true" data-theme="d" data-dividertheme="c">
				<li data-role="list-divider">Please authenticate first</li>
				<li data-role="fieldcontain">
					<label for="username">Username:</label>
					<input type="text" name="username" id="username" value=""  />
				</li>
				<li data-role="fieldcontain">
					<label for="password">Password:</label>
					<input type="password" name="password" id="password" value=""  />
				</li>
				<li data-role="fieldcontain">
					<div class="ui-grid-c">
						<div class="ui-block-a"><button id="btnLogin" disabled="disabled" type="submit" data-icon="alert" data-theme="c">Login</button></div>
						<div class="ui-block-b"></div>
						<div class="ui-block-c"></div>
						<div class="ui-block-d"></div>
					</div>
				</li>
			</ul>

			<ul data-role="listview" data-inset="true" data-theme="d" data-dividertheme="c" class="kimai-not-login">
				<li data-role="list-divider" id="duration">&nbsp;</li>
				<li data-role="fieldcontain">
					<label for="projects" class="select">Choose project:</label>
					<select name="projects" id="projects">
						
					</select>
				</li>
				<li data-role="fieldcontain">
					<label for="tasks" class="select">Choose task:</label>
					<select name="tasks" id="tasks">
						
					</select>
				</li>
				<li>
					<div class="ui-grid-c">
						<div class="ui-block-a">
							<select id="recorder" name="recorder" data-role="slider" data-theme="c">
								<option value="on">Click to stop</option>
								<option value="off">Click to start</option>
							</select> 
						</div>
						<div class="ui-block-b"></div>
						<div class="ui-block-c"></div>
						<div class="ui-block-d"></div>
					</div>
				</li>
			</ul>
				
			<ul class="kimai-footer" data-role="listview" data-inset="true" data-theme="c">
				<li><a href="http://www.kimai.org/" target="_blank">&copy; 2011 Developed by the Kimai Team</a></li>
			</ul>
			
		</div>
		

	</div>
	
</div>
</body>
</html>
