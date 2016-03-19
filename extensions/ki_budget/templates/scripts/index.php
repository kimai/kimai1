
<div id="budgetArea"></div>

<script type="text/javascript">
    $(document).ready(function () {
        try {
            budget_extension_onload();
        } catch (e) {
            alert(e);
        }

        $(window).resize(function () {
            recalculate_budget_window();
        });
        // we don't want this to show on the page...why is it even there in the first place?
        $('#ui-datepicker-div').hide();
    });

</script>