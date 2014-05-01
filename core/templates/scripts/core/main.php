<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <?php echo $this->partial('partials/html_head.php', $this); ?>
    <script src="../skins/standard/js/skin.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript">
        function skin_onload() {
            $('#extensionShrink').hover(lists_extensionShrinkShow,lists_extensionShrinkHide);
            $('#extensionShrink').click(lists_shrinkExtToggle);
            $('#customersShrink').hover(lists_customerShrinkShow,lists_customerShrinkHide);
            $('#customersShrink').click(lists_shrinkCustomerToggle);
            <?php if (count($this->users) > 0): ?>
            $('#usersShrink').hover(lists_userShrinkShow,lists_userShrinkHide);
            $('#usersShrink').click(lists_shrinkUserToggle);
            <?php else: ?>
            $('#usersShrink').hide();
            <?php endif; ?>

            <?php if ($this->kga['conf']['user_list_hidden'] || count($this->users) <= 1): ?>
            lists_shrinkUserToggle();
            <?php endif; ?>

            var lists_resizeTimer = null;
            $(window).bind('resize', function() {
                resize_floater();
                if (lists_resizeTimer) {
                    clearTimeout(lists_resizeTimer);
                }
                lists_resizeTimer = setTimeout(lists_resize, 500);
            });

            // give browser time to render page. afterwards make sure lists are resized correctly
            setTimeout(lists_resize,500);
            clearTimeout(lists_resize);

            resize_menu();

            <?php if ($this->showInstallWarning): ?>
            floaterShow("floaters.php","securityWarning","installer",0,450);
            <?php endif; ?>
        }
    </script>
</head>
<body onload="kimai_onload();">
    <div id="top">
        <?php echo $this->partial('partials/logo.php', $this); ?>
        <?php echo $this->partial('partials/menu_tools.php', $this); ?>
        <?php echo $this->partial('partials/datepicker.php', $this); ?>
        <?php echo $this->partial('partials/buzzer.php', $this); ?>
    </div>
    <div id="fliptabs" class="menuBackground">
        <?php echo $this->menu($this->main_navigation); ?>
    </div>
    <div id="gui">
        <?php echo $this->partial('partials/extensions.php', $this); ?>
    </div>
    <div class="lists" style="display:none">
        <?php $this->renderSelectLists($this->list_entries); ?>
        <div id="extensionShrink">&nbsp;</div>
        <div id="usersShrink">&nbsp;</div>
        <div id="customersShrink">&nbsp;</div>
    </div>
    <?php echo $this->loader(); ?>
    <?php echo $this->floater()->floaterBody(); ?>
</body>
</html>
