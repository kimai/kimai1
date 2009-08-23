{*     IMPORTANT NOTE:
       Javascript or jQuery stuff that should run when your extension *has finished loading*  
       should sit in an special onload function like this:
*}
{literal}    
    <script type="text/javascript"> 
        $(document).ready(function() {
            demo_ext_onload();
        }); 
    </script>
{/literal}

<div id="demo_ext_header">
     <strong>Demo Extension</strong>
</div>


<h1>My Extension</h1>

<div id="testdiv">
    This DIV is going to be changed via jQuery when the tab has been changed to another extension and back.
</div>

<div id="demo_timespace">
    When you change the timespace it will be entered here ====> <span class="timespace_target">__________</span> via jQuery. (only timespace_in for the sake of this demonstration...) 
</div>


