<?php

if ($_REQUEST['lang'] == "de") {
    echo <<<EOD
        <h2>Lizenz</h2>
        <div class="gpl">
EOD;
    require("20_gpl_de.php");
    echo <<<EOD
        </div>
        <input type="checkbox" name="accept" value='1' onclick="gpl_agreed(this);" style="width:15px;height:15px;display:inline;">
        <span style="margin-top:5px">Ich stimme den Bedingungen der General Public License zu.</span><br/>
        <!--<button onclick="step_back(); return false;" class="">Zurück</button>-->
        <button onclick="gpl_proceed(); return false;" class="invisible proceed">Fortfahren</button>
EOD;

} elseif ($_REQUEST['lang'] == "bg") {
    echo <<<EOD
        <h2>Лиценз</h2>
        <div class="gpl">
EOD;
    require("20_gpl_bg.php");
    echo <<<EOD
        </div>
        <input type="checkbox" name="accept" value='1' onclick="gpl_agreed(this);" style="width:15px;height:15px;display:inline;">
        <span style="margin-top:5px">Съгласен съм с условията на General Public License.</span><br/>
        <!--<button onclick="step_back(); return false;" class="">Назад</button>-->
        <button onclick="gpl_proceed(); return false;" class="invisible proceed">Напред</button>
EOD;

} else {
    echo <<<EOD
        <h2>License</h2>
        <div class="gpl">
EOD;
    require("20_gpl_en.php");
    echo <<<EOD
        </div>
        <input type="checkbox" name="accept" value='1' onclick="gpl_agreed(this);" style="width:15px;height:15px;display:inline;">
        <span style="margin-top:5px">I agree to the terms of the General Public License.</span><br/>
        <!--<button onclick="step_back(); return false;" class="">Back</button>-->
        <button onclick="gpl_proceed(); return false;" class="invisible proceed">Proceed</button>
EOD;

}
