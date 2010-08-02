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
    if ($kga['usr']['usr_sts']==0)
      $arr_knd = get_arr_knd("all");
    else
      $arr_knd = get_arr_knd($kga['usr']['usr_grp']);
    if (count($arr_knd)>0) {
    $tpl->assign('arr_knd', $arr_knd);
    } else {
    $tpl->assign('arr_knd', '0');
    }
    $tpl->assign('knd_display', $tpl->fetch("knd.tpl"));

    // =========================
    // = display project table =
    // =========================
    if ($kga['usr']['usr_sts']==0)
      $arr_pct = get_arr_pct("all");
    else
      $arr_pct = get_arr_pct($kga['usr']['usr_grp']);
    if (count($arr_pct)>0) {
    $tpl->assign('arr_pct', $arr_pct);
    } else {
    $tpl->assign('arr_pct', '0');
    }
    $tpl->assign('pct_display', $tpl->fetch("pct.tpl"));

    // ========================
    // = display events table =
    // ========================
    if ($kga['usr']['usr_sts']==0)
      $arr_evt = get_arr_evt_by_pct("all",-2);
    else
      $arr_evt = get_arr_evt_by_pct($kga['usr']['usr_grp'],-2);
    if (count($arr_evt)>0) {
    $tpl->assign('arr_evt', $arr_evt);
    } else {
    $tpl->assign('arr_evt', '0');
    }
    $tpl->assign('evt_display', $tpl->fetch("evt.tpl"));
    $tpl->assign('selected_evt_filter',-2);

    $tpl->assign('curr_user', $kga['usr']['usr_name']);

    if ($kga['usr']['usr_sts']==0)
      $tpl->assign('arr_grp', get_arr_grp(get_cookie('ap_ext_show_deleted_groups',0)));
    else
      $tpl->assign('arr_grp', get_arr_grp_by_leader($kga['usr']['usr_ID'],
        get_cookie('ap_ext_show_deleted_groups',0)));

    if ($kga['usr']['usr_sts']==0)
      $tpl->assign('arr_usr',  get_arr_usr(get_cookie('ap_ext_show_deleted_users',0)));
    else
      $tpl->assign('arr_usr',get_arr_watchable_users($kga['usr']['usr_ID']));
    $tpl->assign('showDeletedGroups', get_cookie('ap_ext_show_deleted_groups',0));
    $tpl->assign('showDeletedUsers', get_cookie('ap_ext_show_deleted_users',0));
    $tpl->assign('languages', langs());
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