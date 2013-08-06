<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <?php echo $this->partial('partials/html_head.php', $this); ?>
</head>
<body onload="kimai_onload();">
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
        <?php echo $this->partial('partials/lists.php', $this); ?>
        <div id="extensionShrink">&nbsp;</div>
        <div id="usersShrink">&nbsp;</div>
        <div id="customersShrink">&nbsp;</div>
    </div>
    <div id="loader">&nbsp;</div>
	<div id="floater">floater</div>
</body>
</html>
