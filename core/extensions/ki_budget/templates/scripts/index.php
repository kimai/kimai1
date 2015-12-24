<script type="text/javascript">
    $(document).ready(function () {
        try {
            budget_extension_onload();
        } catch (e) {
            alert(e);
        }

        $(window).resize(function () {
            recalculateWindow();
        });
        // we don't want this to show on the page...why is it even there in the first place?
        $('#ui-datepicker-div').hide();
    });

    function recalculateWindow() {
        // adjust length of the elements if the legend is longer than the space allows
        $('.project_overview').each(function () {
            try {
                var element = $(this).parent();
                var numberOfElements = element.nextUntil('br').andSelf().length;
                var numberOnLine = Math.floor(($(document).width() - 45) / 225);
                var height = Math.ceil(numberOfElements / numberOnLine) * 270;
                var br = element.nextAll('br').eq(0);
                br.css('line-height', height + 'px');
                var addHeight = $(this).next().find('.jqplot-table-legend').height() - 150;

                if ($(this).next().find('.jqplot-table-legend').height() > 150) {
                    var br = element.nextAll('br').eq(0);
                    br.css('line-height', parseInt(br.css('line-height')) + addHeight + 'px');
                    element.nextUntil('br').andSelf().slice(0, numberOnLine).each(function () {
                        // only add the height if it's not added already before (like if
                        // we make the windows smaller and then bigger again, we need to
                        // add the height so some of the charts)
                        if ($(this).height() < 250 + addHeight) {
                            $(this).height(($(this).height() + addHeight));
                        }
                    });
                    // in case we make the window bigger and some "long" elements are on a new page
                    // and need the "normal" length
                    element.nextUntil('br').andSelf().slice(numberOnLine).each(function () {
                        $(this).height(250);
                    });
                }
            } catch (err) {
                alert(err);
            }
        });
    }
</script>
<div id="budgetArea">
    <div class="budget_project">
        <div class="project_head project_overview"></div>
    </div>
    <div class="budget_project_end"></div>
</div>