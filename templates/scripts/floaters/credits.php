<div id="floater_innerwrap">
    <div id="floater_handle">
        <span id="floater_title"><?php echo $this->kga['lang']['about']?></span>
        <div class="right">
            <a href="#" class="close" onclick="floaterClose();return false;"><?php echo $this->kga['lang']['close']?></a>
        </div>       
    </div>
    <div class="floater_content" style="margin:10px">
        <h2>Kimai - Open Source Time Tracking</h2> 
        <p>
            <?php echo 'v' . $this->kga['version'] . '.' . $this->kga['revision'] . ' - &copy; ' . $this->devtimespan;?> by the Kimai-Team:
            <br/>
            Torsten Höltge, Severin Leonhardt, Kevin Papst, Simon Schaufelberger, Oleg Britvin, Martin Klemkow ...
        </p>
        <p>
            <a href="http://www.kimai.org" target="_blank">Kimai Homepage</a> |
            <a href="http://forum.kimai.org/" target="_blank">Forum</a> |
            <a href="https://github.com/kimai/kimai" target="_blank">GitHub</a> |
            <a href="http://www.kimai.org/download/" target="_blank">Download</a> |
            <a href="http://forum.kimai.org/index.php?board=10.0" target="_blank">Friendly Hacks</a>
        </p>
        <p>
            <strong><?php
                echo sprintf(
                    $this->kga['lang']['credits_license'],
                    '<a href="../COPYING" target="_blank">GPL 3</a>'
                );
            ?></strong>
        </p>
        <p>
            <?php
            echo sprintf(
                $this->kga['lang']['credits'],
                'http://forum.kimai.org',
                'https://github.com/kimai/kimai/archive/master.zip',
                'http://www.kimai.org/donate/',
                'https://github.com/kimai/kimai/issues',
                'https://github.com/kimai/kimai/tree/master/language'
            );
            ?>
        </p>
        <p>
            <strong><?php echo $this->kga['lang']['credits_thanks']?></strong>
            Vasilis van Gemert, Maximilian Kern, Enrico Ties, Thomas Wensing, John Resig, Kelvin Luck, Urs Gerig, Willem van Gemert,
            Torben Boe and HamBug Studios, Klaus Franken, Chris (Urban Willi), Andreas Berndt, Niels Hoffmann, Günter Hengsbach,
            Paul Brand, Joaqu&iacute;n G. de la Zerda, Allesandro Bertoldo, Jos&eacute; Ricardo Cardoso,
            RRZE (Regionales Rechenzentrum Erlangen) ...
        </p>
        <p>
            <strong><?php echo $this->kga['lang']['credits_libs']?></strong>
                <a href="http://framework.zend.com/" target="_blank">Zend Framework</a>,
                <a href="http://jquery.com/" target="_blank">jQuery</a>,
                <a href="http://phpjs.org/" target="_blank">phpjs</a>,
                <a href="https://github.com/js-cookie/js-cookie" target="_blank">js-cookie</a>,
                <a href="https://github.com/malsup/form" target="_blank">jQuery Form Plugin</a>,
                <a href="https://github.com/SamWM/jQuery-Plugins/tree/master/newsticker" target="_blank">jQuery newsticker</a>,
                <a href="https://github.com/SamWM/jQuery-Plugins/tree/master/selectboxes" target="_blank">jQuery selectboxes</a>,
                <a href="http://jqplot.com" target="_blank">jqPlot</a>,
                <a href="http://www.tinybutstrong.com/" target="_blank">TinyButStrong</a>,
                <a href="http://tinydoc.unesolution.fr/" target="_blank">tinyDoc</a>,
                <a href="http://www.tcpdf.org/" target="_blank">TCPDF</a>,
                <a href="http://www.phpclasses.org/ultimatemysql" target="_blank">Ultimate MySQL Class</a>,
                <a href="http://www.phpconcept.net/" target="_blank">PclZip</a>,
                <a href="https://getcomposer.org/" target="_blank">Composer</a>,
                <a href="http://mysql.com/" target="_blank">MySQL</a>,
                <a href="http://php.net/" target="_blank">PHP</a>
        </p>
    </div>
</div>
