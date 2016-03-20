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

        // we don't want to show the date picker
        $('#ui-datepicker-div').hide();
    });
</script>