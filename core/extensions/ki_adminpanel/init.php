<?php
    // Include Basics
    include('../../includes/basics.php');

    $usr = checkUser();
    // ============================================
    // = initialize currently displayed timespace =
    // ============================================
    $timespace = get_timespace();
    $in = $timespace[0];
    $out = $timespace[1];

    // append (!) config to $kga
    get_config($usr['usr_ID']);

    if ($kga['conf']['lang'] == "") {
    $language = $kga['language'];
    } else {
    $language = $kga['conf']['lang'];
    }

    require_once(WEBROOT."language/${language}.php");

    // set smarty config
    require_once('../../libraries/smarty/Smarty.class.php');
    $tpl = new Smarty();
    $tpl->template_dir = 'templates/';
    $tpl->compile_dir  = 'compile/';
    // $tpl->cache_dir    = 'smarty/cache';
    // $tpl->config_dir   = 'smarty/configs';

    $tpl->assign('kga', $kga);

    // ==========================
    // = display customer table =
    // ==========================
    $arr_knd = get_arr_knd("all");
    if (count($arr_knd)>0) {
    $tpl->assign('arr_knd', $arr_knd);
    } else {
    $tpl->assign('arr_knd', '0');
    }
    $tpl->assign('knd_display', $tpl->fetch("knd.tpl"));

    // =========================
    // = display project table =
    // =========================
    $arr_pct = get_arr_pct("all");
    if (count($arr_pct)>0) {
    $tpl->assign('arr_pct', $arr_pct);
    } else {
    $tpl->assign('arr_pct', '0');
    }
    $tpl->assign('pct_display', $tpl->fetch("pct.tpl"));

    // ========================
    // = display events table =
    // ========================
    $arr_evt = get_arr_evt("all");
    if (count($arr_evt)>0) {
    $tpl->assign('arr_evt', $arr_evt);
    } else {
    $tpl->assign('arr_evt', '0');
    }
    $tpl->assign('evt_display', $tpl->fetch("evt.tpl"));

    $tpl->assign('curr_user', $kga['usr']['usr_name']);
    $tpl->assign('arr_grp', get_arr_grp(get_cookie('ap_ext_show_deleted_groups',0)));
    $tpl->assign('arr_usr', get_arr_usr(get_cookie('ap_ext_show_deleted_users',0)));
    $tpl->assign('showDeletedGroups', get_cookie('ap_ext_show_deleted_groups',0));
    $tpl->assign('showDeletedUsers', get_cookie('ap_ext_show_deleted_users',0));
    $admin['users'] = $tpl->fetch("users.tpl");
    $admin['groups'] = $tpl->fetch("groups.tpl");
    $admin['advanced'] = $tpl->fetch("advanced.tpl");
    
    if ($kga['show_sensible_data']) {
        $admin['database'] = $tpl->fetch("database.tpl");
    } else {
        $admin['database'] = "You don't have permission to see this information ...";
    }

    $tpl->assign('admin',  $admin);

    $tpl->display('main.tpl');
?>