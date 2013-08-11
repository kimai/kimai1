<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $this->partial('partials/html_head.php', $this); ?>
    <script type="text/javascript">
        // defined in kimai_onload();
        function skin_onload() { }
        // defined in init.js and main.js
        function lists_resize() { }

    </script>
    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="../skins/flat/js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
</head>
<body onload="kimai_onload();">
    <div class="navbar navbar-static-top">
        <div class="container-fluid">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">KIMAI</a>
            <div class="nav-collapse collapse">
                <ul class="nav navbar-nav" id="fliptabs">
                    <?php echo $this->menu($this->main_navigation); ?>
                </ul>
                <ul class="nav navbar-nav pull-right">
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
            <strong><?php echo $this->kga['lang']['securityWarning']?>:</strong><br/>
            <b><?php echo $this->kga['lang']['installerWarningHeadline']?></b> <br/>
            <?php echo $this->kga['lang']['installerWarningText']?>

        </div>
    </div>
    <?php endif; ?>

    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default" id="headings">
                    <div class="panel-heading">Date Picker</div>
                    <?php echo $this->partial('partials/datepicker.php', $this); ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel panel-default" id="content-formatting">
                    <div class="panel-heading">Buzzer</div>
                    <?php echo $this->partial('partials/buzzer.php', $this); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <?php
            echo $this->partial('partials/extensions.php', $this);
            ?>
        </div>

        <div class="row lists">
            <?php $this->renderSelectLists($this->list_entries); ?>
        </div>
    </div>

    <?php echo $this->loader(); ?>
    <?php echo $this->floater(); ?>

</body>
</html>