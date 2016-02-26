<!--
IMPORTANT NOTE:
Javascript or jQuery stuff that should run when your extension *has finished loading*  
should sit in an special onload function like this:
-->
<script type="text/javascript">
    $(document).ready(function ()
    {
        demo_ext_onload();
    });
</script>

<h1>My Extension</h1>

<div id="testdiv">
    This DIV is going to be changed via jQuery when the tab has been changed to another extension and back.
</div>

<div id="demo_timeframe">
    When you change the timeframe it will be entered here ====> <span class="timeframe_target">__________</span> via
    jQuery. (only timeframe_in for the sake of this demonstration...)
</div>
