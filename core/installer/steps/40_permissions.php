<?php 
echo '<script type="text/javascript" charset="utf-8">current=40;</script>';

if ($_REQUEST['lang']=="en") {

echo<<<EOD
    <h2>Write-permissions</h2>
    PHP must have permissions to write to the following files/folders:<br/><br/>
    
    <span class="ch_compile"><strong>/compile</strong> <span class="gray">(Directory)</span></span><br/>
    <span class="ch_compile_tsext"><strong>/extensions/ki_timesheets/compile</strong> <span class="gray">(Directory)</span></span><br/>
    <span class="ch_compile_apext"><strong>/extensions/ki_adminpanel/compile</strong> <span class="gray">(Directory)</span></span><br/>

    <span class="ch_compile_epext"><strong>/extensions/ki_expenses/compile</strong> <span class="gray">(Directory)</span></span><br/>
    <span class="ch_compile_xpext"><strong>/extensions/ki_export/compile</strong> <span class="gray">(Directory)</span></span><br/>
    <span class="ch_compile_xpext"><strong>/extensions/ki_budget/compile</strong> <span class="gray">(Directory)</span></span><br/>
    <span class="ch_compile_ivext"><strong>/extensions/ki_invoice/compile</strong> <span class="gray">(Directory)</span></span><br/>

    <span class="ch_temporary"><strong>/temporary</strong> <span class="gray">(Directory)</span></span><br/>
    <span class="ch_logfile"><strong>/temporary/logfile.txt</strong> <span class="gray">(File)</span></span><br/>
    <span class="ch_autoconf"><strong>/includes/autoconf.php</strong> <span class="gray">(File)</span></span><br/><br/>
    <button onClick="step_back(); return false;" class="">Back</button>
    <button class="cp-button" onClick='check_permissions();'>Check permissions now</button>
    <button onClick="cp_proceed(); return false;" class="invisible proceed">Proceed</button>
    <span class="ch_correctit invisible arrow">Please correct the write-permissions and press the button again!</span><br/>
EOD;

} else {

echo<<<EOD
    <h2>Schreibrechte</h2>
    PHP muss Schreibrechte auf folgende Dateien und Ordner haben:<br/><br/>
    
    <span class="ch_compile"><strong>/compile</strong> <span class="gray">(Verzeichnis)</span></span><br/>
    <span class="ch_compile_tsext"><strong>/extensions/ki_timesheets/compile</strong> <span class="gray">(Verzeichnis)</span></span><br/>
    <span class="ch_compile_apext"><strong>/extensions/ki_adminpanel/compile</strong> <span class="gray">(Verzeichnis)</span></span><br/>

    <span class="ch_compile_epext"><strong>/extensions/ki_expenses/compile</strong> <span class="gray">(Verzeichnis)</span></span><br/>
    <span class="ch_compile_xpext"><strong>/extensions/ki_export/compile</strong>   <span class="gray">(Verzeichnis)</span></span><br/>
    <span class="ch_compile_bgtext"><strong>/extensions/ki_budget/compile</strong> <span class="gray">(Verzeichnis)</span></span><br/>
    <span class="ch_compile_ivext"><strong>/extensions/ki_invoice/compile</strong>  <span class="gray">(Verzeichnis)</span></span><br/>

    <span class="ch_temporary"><strong>/temporary</strong> <span class="gray">(Verzeichnis)</span></span><br/>
    <span class="ch_logfile"><strong>/temporary/logfile.txt</strong> <span class="gray">(Datei)</span></span><br/>
    <span class="ch_autoconf"><strong>/includes/autoconf.php</strong> <span class="gray">(Datei)</span></span><br/><br/>
    <button onClick="step_back(); return false;" class="">Zurück</button>
    <button class="cp-button" onClick='check_permissions();'>Schreibrechte prüfen</button>
    <button onClick="cp_proceed(); return false;" class="invisible proceed">Fortfahren</button>
    <span class="ch_correctit invisible arrow">Korrigieren Sie die Schreibrechte und klicken Sie den Button erneut!</span><br/>
EOD;

}

echo '<script type="text/javascript" charset="utf-8">check_permissions();</script>';

?>