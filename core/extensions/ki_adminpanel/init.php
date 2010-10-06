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
 * along with Kimai; If not, see <http://www.gnu.org/licenses/>.
 */

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

    $tpl->assign('kga', $kga);

    // ==========================
    // = display customer table =
    // ==========================
    if ($kga['usr']['usr_sts']==0)
      $arr_knd = get_arr_knd("all");
    else
      $arr_knd = get_arr_knd($kga['usr']['usr_grp']);

    foreach ($arr_knd as $row=>$knd_data) {
      $grp_names = array();
      foreach (knd_get_grps($knd_data['knd_ID']) as $grp_id) {
        $data = grp_get_data($grp_id);
         $grp_names[] = $data['grp_name'];
      }
      $arr_knd[$row]['groups'] = implode(", ",$grp_names);
    }

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

    foreach ($arr_pct as $row=>$pct_data) {
      $grp_names = array();
      foreach (pct_get_grps($pct_data['pct_ID']) as $grp_id) {
        $data = grp_get_data($grp_id);
         $grp_names[] = $data['grp_name'];
      }
      $arr_pct[$row]['groups'] = implode(", ",$grp_names);
    }

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

    foreach ($arr_evt as $row=>$evt_data) {
      $grp_names = array();
      foreach (evt_get_grps($evt_data['evt_ID']) as $grp_id) {
        $data = grp_get_data($grp_id);
         $grp_names[] = $data['grp_name'];
      }
      $arr_evt[$row]['groups'] = implode(", ",$grp_names);
    }

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

    $tpl->assign('timezones', timezoneList());

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