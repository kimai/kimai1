<script type="text/javascript">
    $(document).ready(function() {
        demo_ext_onload();
    });
</script>

<?php
echo $this->extensionScreen(
    array(
        'title'     => 'Demo',
        'id'        => 'demo_extension_header',
        'level'     => array('demo_extension_wrap', 'demo_extension'),
        'styles'    => true
    )
)->getHeader();
?>

<h1>My Extension</h1>

<div id="testdiv">
    This DIV is going to be changed via jQuery when the tab has been changed to another extension and back.
</div>

<div id="demo_timeframe">
    When you change the timeframe it will be entered here ====> <span class="timeframe_target">__________</span> via jQuery.
    (just the value that was changed for the sake of this demonstration...)
</div>

<?php echo $this->extensionScreen()->getFooter(); ?>
