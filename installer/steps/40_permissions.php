<?php
echo '<script type="text/javascript">current=40;</script>';

if ($_REQUEST['lang'] == "de") {
    echo<<<EOD
    <h2>Schreibrechte</h2>
    PHP muss Schreibrechte auf folgende Dateien und Ordner haben:<br/><br/>

    <span class="ch_temporary"><strong>/temporary</strong> <span class="gray">(Verzeichnis)</span></span><br/>
    <span class="ch_logfile"><strong>/temporary/logfile.txt</strong> <span class="gray">(Datei)</span></span><br/>
    <span class="ch_autoconf"><strong>/includes/autoconf.php</strong> <span class="gray">(Datei)</span> und <strong>/includes</strong> <span class="gray">(Verzeichnis)</span></span><br/><br/>
    <button onclick="step_back(); return false;" class="">Zurück</button>
    <button class="cp-button" onclick='check_permissions();'>Schreibrechte prüfen</button>
    <button onclick="cp_proceed(); return false;" class="invisible proceed">Fortfahren</button>
    <span class="ch_correctit invisible arrow">Korrigieren Sie die Schreibrechte und klicken Sie den Button erneut!</span><br/>
EOD;
} elseif ($_REQUEST['lang'] == "bg") {
    echo<<<EOD
    <h2>Достъп до файловата система</h2>
    PHP трябва да има достъп до следните файлове и директории:<br/><br/>

    <span class="ch_temporary"><strong>/temporary</strong> <span class="gray">(Директория)</span></span><br/>
    <span class="ch_logfile"><strong>/temporary/logfile.txt</strong> <span class="gray">(Файл)</span></span><br/>
    <span class="ch_autoconf"><strong>/includes/autoconf.php</strong> <span class="gray">(Файл)</span> и <strong>/includes</strong> <span class="gray">(Директория)</span></span><br/><br/>
    <button onclick="step_back(); return false;" class="">Zurück</button>
    <button class="cp-button" onclick='check_permissions();'>Провери достъпа</button>
    <button onclick="cp_proceed(); return false;" class="invisible proceed">Напред</button>
    <span class="ch_correctit invisible arrow">Поправете достъпа и натиснете бутона отново!</span><br/>
EOD;
} else {
    echo<<<EOD
    <h2>Write-permissions</h2>
    PHP must have permissions to write to the following files/folders:<br/><br/>

    <span class="ch_temporary"><strong>/temporary</strong> <span class="gray">(Directory)</span></span><br/>
    <span class="ch_logfile"><strong>/temporary/logfile.txt</strong> <span class="gray">(File)</span></span><br/>
    <span class="ch_autoconf"><strong>/includes/autoconf.php</strong> <span class="gray">(File)</span> and <strong>/includes</strong> <span class="gray">(Directory)</span></span><br/><br/>
    <button onclick="step_back(); return false;" class="">Back</button>
    <button class="cp-button" onclick='check_permissions();'>Check permissions now</button>
    <button onclick="cp_proceed(); return false;" class="invisible proceed">Proceed</button>
    <span class="ch_correctit invisible arrow">Please correct the write-permissions and press the button again!</span><br/>
EOD;
}
echo '<script type="text/javascript">check_permissions();</script>';
