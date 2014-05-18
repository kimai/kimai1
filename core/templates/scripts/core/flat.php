<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $this->partial('partials/html_head.php', $this); ?>
    <script src="../skins/flat/js/skin.js" type="text/javascript" charset="utf-8"></script>
    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="../skins/flat/js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
</head>
<body onload="kimai_onload();">

<div id="wrap">

    <div class="navbar navbar-static-top">
        <div class="container-fluid">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="http://www.kimai.org/" title="Kimai - Open Source time-tracking" target="_blank">KIMAI</a>
            <div class="nav-collapse collapse">
                <?php echo $this->menu($this->main_navigation); ?>
                <ul class="nav navbar-nav pull-right">
                    <li>
                        <?php echo $this->partial('partials/datepicker.php', $this); ?>
                    </li>
                    <li>
                        <?php echo $this->partial('partials/buzzer.php', $this); ?>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user"></i> <span><?php echo $this->username(); ?></span><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><?php echo $this->links('preferences'); ?></li>
                            <li><?php echo $this->links('credits'); ?></li>
                            <li class="divider"></li>
                            <li><?php echo $this->links('logout', array('content' => $this->kga['lang']['logout'])); ?></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <?php if ($this->showInstallWarning): ?>
    <div class="container">
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <i class="icon-warning-sign icon-large"></i>
            <strong><?php echo $this->kga['lang']['securityWarning']?>:</strong><br/>
            <b><?php echo $this->kga['lang']['installerWarningHeadline']?></b> <br/>
            <?php echo $this->kga['lang']['installerWarningText']?>

        </div>
    </div>
    <?php endif; ?>

    <div class="container">

        <div class="row">
            <div class="col-lg-12">
            <?php echo $this->partial('partials/extensions.php', $this); ?>
            </div>
        </div>

        <div class="row lists">
            <?php $this->renderSelectLists($this->list_entries); ?>
        </div>
    </div>

    <?php echo $this->loader(); ?>
    <?php echo $this->floater()->floaterBody(); ?>

</body>
</html>
