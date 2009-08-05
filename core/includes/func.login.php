<?php
/**
 * This file is part of 
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2009 Kimai-Development-Team
 * 
 * Kimai is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; Version 3, 29 June 2007
 * 
 * Kimai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Kimai; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 */

function denyAccess() {
 /* TODO: test if bantime is set correctly when login fails */
    global $kga; 
    
    setcookie ("kimai_key","0"); setcookie ("kimai_usr","0");
    @mysql_query(sprintf("UPDATE %susr SET ban=ban+1 WHERE usr_name = '%s';"),$kga['server_prefix'],$user);
    echo "<span style='color:red'>".$kga['lang']['accessDenied']."</span><br><a href='index.php'>".$kga['lang']['back']."</a><br><br>";
    if ($ban > ($kga['conf']['loginTries']-2)) {
        $query = 
        @mysql_query(sprintf("UPDATE %susr SET `banTime` = '%d' WHERE usr_name = '$user';",$kga['server_prefix'],time()));
        echo $kga['lang']['tooManyLogins']."<br>";
    } else {
        echo $kga['lang']['tryAgain']."<br>";
    }
    // $subject = "Kimai: A L A R M !";
    // $xtra    = "From: $kimail\r\nContent-Type: text/html\r\nContent-Transfer-Encoding: 8bit\r\nX-Mailer: Kimai";
    // $message = "### kimai attack message ###";
    // mail($adminmail, $subject, $message, $xtra);
    exit;
}

function showLoginPanel() {
    global $kga;
    $tpl = new Smarty();
    $tpl->template_dir = 'templates/';
    $tpl->compile_dir  = 'compile/';
    $tpl->assign('browser', get_agent());
    $tpl->assign('kga', $kga);
    $tpl->assign('devtimespan', '2006-'.date('y'));
    $tpl->display('login/panel.tpl');
}

function showDemoPanel() {
    global $kga;
    $tpl = new Smarty();
    $tpl->template_dir = 'templates/';
    $tpl->compile_dir  = 'compile/';
    $tpl->assign('browser', get_agent());
    $tpl->assign('kga', $kga);
    $tpl->assign('devtimespan', '2006-'.date('y'));
    $tpl->display('demopanel.tpl');
}
?>