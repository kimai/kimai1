<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../skins/flat/css/bootstrap.min.css" rel="stylesheet">
    <script src="../skins/flat/js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
    <?php echo $this->partial('partials/html_head.php', $this); ?>
</head>
<body onload="kimai_onload();">

<!-- Static navbar -->
<div class="navbar navbar-static-top">
    <div class="container-fluid">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">Project name</a>
        <div class="nav-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li class="divider"></li>
                        <li class="nav-header">Nav header</li>
                        <li><a href="#">Separated link</a></li>
                        <li><a href="#">One more separated link</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav pull-right">
                <li><a href="http://examples.getbootstrap.com/examples/navbar/">Default</a></li>
                <li class="active"><a href="http://examples.getbootstrap.com/examples/navbar-static-top/">Static top</a></li>
                <li><a href="http://examples.getbootstrap.com/examples/navbar-fixed-top/">Fixed top</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
    <div id="top">
        <?php echo $this->partial('partials/header.php', $this); ?>
    </div>
    <div id="fliptabs" class="menuBackground">
        <?php echo $this->menu($this->main_navigation); ?>
    </div>
    <div id="gui">
        <?php echo $this->partial('partials/extensions.php', $this); ?>
    </div>
    <div class="lists" style="display:none">
        <?php echo $this->partial('partials/lists.php', array('list_entries' => $this->list_entries)); ?>
        <div id="extensionShrink">&nbsp;</div>
        <div id="usersShrink">&nbsp;</div>
        <div id="customersShrink">&nbsp;</div>
    </div>
    <div id="loader">&nbsp;</div>
	<div id="floater">floater</div>
</body>
</html>
