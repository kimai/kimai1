<?php if (isset($this->kga['user'])): ?>
    <?php echo $this->selectionBox(); ?>

    <div id="stopwatch">
        <span class="watch"><span id="h">00</span>:<span id="m">00</span>:<span id="s">00</span></span>
    </div>

    <div id="stopwatch_ticker">
        <ul id="ticker"><li id="ticker_customer">&nbsp;</li><li id="ticker_project">&nbsp;</li><li id="ticker_activity">&nbsp;</li></ul>
    </div>

    <div id="buzzer" class="disabled">
        <div>&nbsp;</div>
    </div>
<?php endif; ?>