<?php
/* -*- Mode: PHP; tab-width: 4; indent-tabs-mode: nil -*- */
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

// =============================================================
// = various functions for working with the kimai database     =
// =============================================================

function clean_data($data) {
    global $kga;   
    foreach ($data as $key => $value) {
        if ($key != "pw") { 
            $return[$key] = urldecode(strip_tags($data[$key]));
    		$return[$key] = str_replace('"','_',$data[$key]);
    		$return[$key] = str_replace("'",'_',$data[$key]);
    		$return[$key] = str_replace('\\','',$data[$key]);
        } else {
            $return[$key] = $data[$key];
        }
		if ($kga['utf8']) $return[$key] = utf8_decode($return[$key]);
    }
    
    return $return;
}


// -----------------------------------------------------------------------------------------------------------

/**
 * Adds a new customer
 *
 * @param array $data        name, address and other data of the new customer
 * @global array $kga         kimai-global-array
 * @return int                the knd_ID of the new customer, false on failure
 * @author ob
 */
function knd_create($data) {
    global $kga, $pdo_conn;
    
    $data = clean_data($data);
    
    $pdo_query = $pdo_conn->prepare("
    INSERT INTO " . $kga['server_prefix'] . "knd (
    knd_name, 
    knd_comment, 
    knd_company, 
    knd_street, 
    knd_zipcode, 
    knd_city, 
    knd_tel, 
    knd_fax, 
    knd_mobile, 
    knd_mail, 
    knd_homepage,
    knd_visible,
    knd_filter,  
    knd_logo 
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
    
    $result = $pdo_query->execute(
    array(
    $data['knd_name'], 
    $data['knd_comment'], 
    $data['knd_company'],
    $data['knd_street'],
    $data['knd_zipcode'],
    $data['knd_city'],
    $data['knd_tel'],
    $data['knd_fax'],
    $data['knd_mobile'],
    $data['knd_mail'],
    $data['knd_homepage'],
    $data['knd_visible'], 
    $data['knd_filter'], 
    $data['knd_logo']  
    ));
      
    $err = $pdo_query->errorInfo();
    
    if ($result == true) {
        return $pdo_conn->lastInsertId();
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Returns the data of a certain customer
 *
 * @param array $knd_id        knd_id of the customer
 * @global array $kga         kimai-global-array
 * @return array            the customer's data (name, address etc) as array, false on failure
 * @author ob
 */
function knd_get_data($knd_id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "knd WHERE knd_ID = ?");
    $result = $pdo_query->execute(array($knd_id));
    
    if ($result == false) {
        return false;
    } else {
        $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);
        return $result_array;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Edits a customer by replacing his data by the new array
 *
 * @param array $knd_id        knd_id of the customer to be edited
 * @param array $data        name, address and other new data of the customer
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function knd_edit($knd_id, $data) {
    global $kga, $pdo_conn;
    
    $data = clean_data($data);
        
    $pdo_conn->beginTransaction();
    
    $original_array = knd_get_data($knd_id);
    $new_array = array();
    
    foreach ($original_array as $key => $value) {
        if (isset($data[$key]) == true) {
            $new_array[$key] = $data[$key];
        } else {
            $new_array[$key] = $original_array[$key];
        }
    }

    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "knd SET 
    knd_name = ?, 
    knd_comment = ?,
    knd_company = ?,
    knd_street = ?,
    knd_zipcode = ?,
    knd_city = ?,
    knd_tel = ?,
    knd_fax = ?,
    knd_mobile = ?,
    knd_mail = ?,
    knd_homepage = ?,
    knd_visible = ?,
    knd_filter = ?,
    knd_logo = ?
    WHERE knd_id = ?;");
    
    $result = $pdo_query->execute(array(
    $new_array['knd_name'], 
    $new_array['knd_comment'], 
    $new_array['knd_company'],
    $new_array['knd_street'],
    $new_array['knd_zipcode'],
    $new_array['knd_city'],
    $new_array['knd_tel'],
    $new_array['knd_fax'],
    $new_array['knd_mobile'],
    $new_array['knd_mail'],
    $new_array['knd_homepage'],
    $new_array['knd_visible'], 
    $new_array['knd_filter'], 
    $new_array['knd_logo'],  
    $knd_id
    ));
    
    if ($result == false) {
        return $result;
    }
    
    if ($pdo_conn->commit() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Assigns a customer to 1-n groups by adding entries to the cross table
 *
 * @param int $knd_id         knd_id of the customer to which the groups will be assigned
 * @param array $grp_array    contains one or more grp_IDs
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function assign_knd2grps($knd_id, $grp_array) {
    global $kga, $pdo_conn;
    
    $pdo_conn->beginTransaction();
    
    $pdo_query = $pdo_conn->prepare("DELETE FROM " . $kga['server_prefix'] . "grp_knd WHERE knd_ID=?;");    
    $d_result = $pdo_query->execute(array($knd_id));
    if ($d_result == false) {
        $pdo_conn->rollBack();
        return false;
    }
    
    foreach ($grp_array as $current_grp) {
        
        $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "grp_knd WHERE grp_ID=? AND knd_ID=?;");
        $c_result = $pdo_query->execute(array($current_grp,$knd_id));
        if (count($pdo_query->fetchAll()) == 0) {
            $pdo_query = $pdo_conn->prepare("INSERT INTO " . $kga['server_prefix'] . "grp_knd (grp_ID,knd_ID) VALUES (?,?);");
            $result = $pdo_query->execute(array($current_grp,$knd_id));
            if ($result == false) {
                $pdo_conn->rollBack();
                return false;
            }
        }
    }
    
    if ($pdo_conn->commit() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the groups of the given customer
 *
 * @param array $knd_id        knd_id of the customer
 * @global array $kga          kimai-global-array
 * @return array               contains the grp_IDs of the groups or false on error
 * @author ob
 */
function knd_get_grps($knd_id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT grp_ID FROM " . $kga['server_prefix'] . "grp_knd WHERE knd_ID = ?;");
    
    $result = $pdo_query->execute(array($knd_id));
    if ($result == false) {
        return false;
    }
    
    $return_grps = array();
    $counter = 0;
    
    while ($current_grp = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $return_grps[$counter] = $current_grp['grp_ID'];
        $counter++;
    }
    
    return $return_grps;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * deletes a customer
 *
 * @param array $knd_id        knd_id of the customer
 * @global array $kga          kimai-global-array
 * @return boolean             true on success, false on failure
 * @author ob
 */
function knd_delete($knd_id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "knd SET knd_trash=1 WHERE knd_ID = ?;");
    $result = $pdo_query->execute(array($knd_id));
    
    if ($result == false) {
        return false;
    }
    
    return $result;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Adds a new project
 *
 * @param array $data         name, comment and other data of the new project
 * @global array $kga         kimai-global-array
 * @return int                the pct_ID of the new project, false on failure
 * @author ob
 */
function pct_create($data) {
    global $kga, $pdo_conn;
    
    $data = clean_data($data);
        
    $pdo_query = $pdo_conn->prepare("INSERT INTO " . $kga['server_prefix'] . "pct (
    pct_kndID, 
    pct_name, 
    pct_comment, 
    pct_visible, 
    pct_filter, 
    pct_logo 
    ) VALUES (?, ?, ?, ?, ?, ?);");

    $result = $pdo_query->execute(array(
    $data['pct_kndID'], 
    $data['pct_name'],
    $data['pct_comment'],
    $data['pct_visible'],
    $data['pct_filter'],
    $data['pct_logo']
    ));
    
    if ($result == true) {
        return $pdo_conn->lastInsertId();
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Returns the data of a certain project
 *
 * @param array $pct_id        pct_id of the project
 * @global array $kga         kimai-global-array
 * @return array            the project's data (name, comment etc) as array, false on failure
 * @author ob
 */
function pct_get_data($pct_id) {
    global $kga, $pdo_conn;

    $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "pct WHERE pct_ID = ?");
    $result = $pdo_query->execute(array($pct_id));
    
    if ($result == false) {
        return false;
    } else {
        $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);
        return $result_array;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Edits a project by replacing its data by the new array
 *
 * @param array $pct_id        pct_id of the project to be edited
 * @param array $data        name, comment and other new data of the project
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function pct_edit($pct_id, $data) {
    global $kga, $pdo_conn;
    
    $data = clean_data($data);
        
    $pdo_conn->beginTransaction();
    
    $original_array = pct_get_data($pct_id);
    $new_array = array();
    
    foreach ($original_array as $key => $value) {
        if (isset($data[$key]) == true) {
            $new_array[$key] = $data[$key];
        } else {
            $new_array[$key] = $original_array[$key];
        }
    }
    
    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "pct SET 
    pct_kndID = ?, 
    pct_name = ?,
    pct_comment = ?,
    pct_visible = ?,
    pct_filter = ?,
    pct_logo = ?
    WHERE pct_id = ?;");
    
    $result = $pdo_query->execute(array(
    $new_array['pct_kndID'], 
    $new_array['pct_name'], 
    $new_array['pct_comment'],
    $new_array['pct_visible'],
    $new_array['pct_filter'],
    $new_array['pct_logo'],
    $pct_id  
    ));
    
    if ($result == false) {
        return $result;
    }
    
    if ($pdo_conn->commit() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Assigns a project to 1-n groups by adding entries to the cross table
 *
 * @param int $pct_id        pct_id of the project to which the groups will be assigned
 * @param array $grp_array    contains one or more grp_IDs
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function assign_pct2grps($pct_id, $grp_array) {
    global $kga, $pdo_conn;
    
    $pdo_conn->beginTransaction();
    
    $pdo_query = $pdo_conn->prepare("DELETE FROM " . $kga['server_prefix'] . "grp_pct WHERE pct_ID=?;");    
    $d_result = $pdo_query->execute(array($pct_id));
    if ($d_result == false) {
        $pdo_conn->rollBack();
        return false;
    }
    
    foreach ($grp_array as $current_grp) {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "grp_pct WHERE grp_ID=? AND pct_ID=?;");
        $c_result = $pdo_query->execute(array($current_grp,$pct_id));
        if (count($pdo_query->fetchAll()) == 0) {
            $pdo_query = $pdo_conn->prepare("INSERT INTO " . $kga['server_prefix'] . "grp_pct (grp_ID,pct_ID) VALUES (?,?);");
            $result = $pdo_query->execute(array($current_grp,$pct_id));
            if ($result == false) {
                $pdo_conn->rollBack();
                return false;
            }
        }
    }
    
    if ($pdo_conn->commit() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the groups of the given project
 *
 * @param array $pct_id        pct_id of the project
 * @global array $kga         kimai-global-array
 * @return array            contains the grp_IDs of the groups or false on error
 * @author ob
 */
function pct_get_grps($pct_id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT grp_ID FROM " . $kga['server_prefix'] . "grp_pct WHERE pct_ID = ?;");
    $result = $pdo_query->execute(array($pct_id));
    if ($result == false) {
        return false;
    }
    
    $return_grps = array();
    $counter = 0;
    
    while ($current_grp = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $return_grps[$counter] = $current_grp['grp_ID'];
        $counter++;
    }
    
    return $return_grps;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * deletes a project
 *
 * @param array $pct_id        pct_id of the project
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function pct_delete($pct_id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "pct SET pct_trash=1 WHERE pct_ID = ?;");
    $result = $pdo_query->execute(array($pct_id));
    if ($result == false) {
        return false;
    }
    return $result;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Adds a new event
 *
 * @param array $data        name, comment and other data of the new event
 * @global array $kga         kimai-global-array
 * @return int                the evt_ID of the new project, false on failure
 * @author ob
 */
function evt_create($data) {
    global $kga, $pdo_conn;
    
    $data = clean_data($data);
           
    $pdo_query = $pdo_conn->prepare("
    INSERT INTO " . $kga['server_prefix'] . "evt ( 
    evt_name, 
    evt_comment, 
    evt_visible, 
    evt_filter, 
    evt_logo 
    ) VALUES (?, ?, ?, ?, ?);");
    
    $result = $pdo_query->execute(array(
    $data['evt_name'],
    $data['evt_comment'],
    $data['evt_visible'],
    $data['evt_filter'],
    $data['evt_logo']
    ));
    
    if ($result == true) {
        return $pdo_conn->lastInsertId();
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Returns the data of a certain project
 *
 * @param array $evt_id        evt_id of the project
 * @global array $kga         kimai-global-array
 * @return array            the event's data (name, comment etc) as array, false on failure
 * @author ob
 */
function evt_get_data($evt_id) {
    global $kga, $pdo_conn;

    $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "evt WHERE evt_ID = ?");
    $result = $pdo_query->execute(array($evt_id));
    
    if ($result == false) {
        return false;
    } else {
        $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);
        return $result_array;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Edits an event by replacing its data by the new array
 *
 * @param array $evt_id        evt_id of the project to be edited
 * @param array $data        name, comment and other new data of the event
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function evt_edit($evt_id, $data) {
    global $kga, $pdo_conn;
    
    $data = clean_data($data);
        
    $pdo_conn->beginTransaction();
    
    $original_array = evt_get_data($evt_id);
    $new_array = array();
    
    foreach ($original_array as $key => $value) {
        if (isset($data[$key]) == true) {
            $new_array[$key] = $data[$key];
        } else {
            $new_array[$key] = $original_array[$key];
        }
    }
    
    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "evt SET  
    evt_name = ?,
    evt_comment = ?,
    evt_visible = ?,
    evt_filter = ?,
    evt_logo = ?
    WHERE evt_id = ?;");
    
    $result = $pdo_query->execute(array(
    $new_array['evt_name'], 
    $new_array['evt_comment'],
    $new_array['evt_visible'],
    $new_array['evt_filter'],
    $new_array['evt_logo'],
    $evt_id
    ));
    
    if ($result == false) {
        return $result;
    }
    
    if ($pdo_conn->commit() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Assigns an event to 1-n groups by adding entries to the cross table
 *
 * @param int $evt_id        evt_id of the project to which the groups will be assigned
 * @param array $grp_array    contains one or more grp_IDs
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function assign_evt2grps($evt_id, $grp_array) {
    global $kga, $pdo_conn;
    
    $pdo_conn->beginTransaction();
    
    $pdo_query = $pdo_conn->prepare("DELETE FROM " . $kga['server_prefix'] . "grp_evt WHERE evt_ID=?;");    
    $d_result = $pdo_query->execute(array($evt_id));
    if ($d_result == false) {
        $pdo_conn->rollBack();
        return false;
    }
    
    foreach ($grp_array as $current_grp) {
        
        $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "grp_evt WHERE grp_ID=? AND evt_ID=?;");
        $c_result = $pdo_query->execute(array($current_grp,$evt_id));
        if (count($pdo_query->fetchAll()) == 0) {
            $pdo_query = $pdo_conn->prepare("INSERT INTO " . $kga['server_prefix'] . "grp_evt (grp_ID,evt_ID) VALUES (?,?);");
            $result = $pdo_query->execute(array($current_grp,$evt_id));
            if ($result == false) {
                $pdo_conn->rollBack();
                return false;
            }
        }
    }
    
    if ($pdo_conn->commit() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the groups of the given event
 *
 * @param array $evt_id        evt_id of the project
 * @global array $kga         kimai-global-array
 * @return array            contains the grp_IDs of the groups or false on error
 * @author ob
 */
function evt_get_grps($evt_id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT grp_ID FROM " . $kga['server_prefix'] . "grp_evt WHERE evt_ID = ?;");
    
    $result = $pdo_query->execute(array($evt_id));
    if ($result == false) {
        return false;
    }
    
    $return_grps = array();
    $counter = 0;
    
    while ($current_grp = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $return_grps[$counter] = $current_grp['grp_ID'];
        $counter++;
    }
    
    return $return_grps;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * deletes an event
 *
 * @param array $evt_id        evt_id of the event
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function evt_delete($evt_id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "evt SET evt_trash=1 WHERE evt_ID = ?;");
    $result = $pdo_query->execute(array($evt_id));
    if ($result == false) {
        return false;
    }
    
    return $result;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Assigns a group to 1-n customers by adding entries to the cross table
 * (counterpart to assign_knd2grp)
 * 
 * @param array $grp_id        grp_id of the group to which the customers will be assigned
 * @param array $knd_array    contains one or more knd_IDs
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function assign_grp2knds($grp_id, $knd_array) {
    global $kga, $pdo_conn;
    
    $pdo_conn->beginTransaction();
    
    $d_query = $pdo_conn->prepare("DELETE FROM " . $kga['server_prefix'] . "grp_knd WHERE grp_ID=?;");
    $d_result = $d_query->execute(array($grp_id));
    if ($d_result == false) {
        return false;
    }
    
    foreach ($knd_array as $current_knd) {
        $c_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "grp_knd WHERE grp_ID=? AND knd_ID=?;");
        $c_result = $c_query->execute(array($grp_id,$current_knd));
        if (count($c_query->fetchAll()) == 0) {
            $pdo_query = $pdo_conn->prepare("INSERT INTO " . $kga['server_prefix'] . "grp_knd (grp_ID,knd_ID) VALUES (?,?);");
            $result = $pdo_query->execute(array($grp_id,$current_knd));
            if ($result == false) {
                return false;
            }
        }
    }
    
    if ($pdo_conn->commit() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Assigns a group to 1-n projects by adding entries to the cross table
 * (counterpart to assign_pct2grp)
 * 
 * @param array $grp_id        grp_id of the group to which the projects will be assigned
 * @param array $pct_array    contains one or more pct_IDs
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function assign_grp2pcts($grp_id, $pct_array) {
    global $kga, $pdo_conn;
    
    $pdo_conn->beginTransaction();
    
    $d_query = $pdo_conn->prepare("DELETE FROM " . $kga['server_prefix'] . "grp_pct WHERE grp_ID=?;");
    $d_result = $d_query->execute(array($grp_id));
    if ($d_result == false) {
        return false;
    }
    
    foreach ($pct_array as $current_pct) {
        $c_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "grp_pct WHERE grp_ID=? AND pct_ID=?;");
        $c_result = $c_query->execute(array($grp_id,$current_pct));
        if (count($c_query->fetchAll()) == 0) {
            $pdo_query = $pdo_conn->prepare("INSERT INTO " . $kga['server_prefix'] . "grp_pct (grp_ID,pct_ID) VALUES (?,?);");
            $result = $pdo_query->execute(array($grp_id,$current_pct));
            if ($result == false) {
                return false;
            }
        }
    }
    
    if ($pdo_conn->commit() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Assigns a group to 1-n events by adding entries to the cross table
 * (counterpart to assign_evt2grp)
 * 
 * @param array $grp_id        grp_id of the group to which the events will be assigned
 * @param array $evt_array    contains one or more evt_IDs
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function assign_grp2evts($grp_id, $evt_array) {
    global $kga, $pdo_conn;
    
    $pdo_conn->beginTransaction();
    
    $d_query = $pdo_conn->prepare("DELETE FROM " . $kga['server_prefix'] . "grp_evt WHERE grp_ID=?;");
    $d_result = $d_query->execute(array($grp_id));
    if ($d_result == false) {
        return false;
    }
    
    foreach ($evt_array as $current_evt) {
        $c_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "grp_evt WHERE grp_ID=? AND evt_ID=?;");
        $c_result = $c_query->execute(array($grp_id,$current_evt));
        if (count($c_query->fetchAll()) == 0) {
            $pdo_query = $pdo_conn->prepare("INSERT INTO " . $kga['server_prefix'] . "grp_evt (grp_ID,evt_ID) VALUES (?,?);");
            $result = $pdo_query->execute(array($grp_id,$current_evt));
            if ($result == false) {
                return false;
            }
        }
    }
    
    if ($pdo_conn->commit() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the customers of the given group
 *
 * @param array $grp_id        grp_id of the group
 * @global array $kga         kimai-global-array
 * @return array            contains the knd_IDs of the groups or false on error
 * @author ob
 */
function grp_get_knds($grp_id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT knd_ID FROM " . $kga['server_prefix'] . "grp_knd WHERE grp_ID = ?;");
    $result = $pdo_query->execute(array($grp_id));
    if ($result == false) {
        return false;
    }
    
    $return_knds = array();
    $counter = 0;
    
    while ($current_knd = $pdo_query->fetch()) {
        $return_knds[$counter] = $current_knd['knd_ID'];
        $counter++;
    }
    
    return $return_knds;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the projects of the given group
 *
 * @param array $grp_id        grp_id of the group
 * @global array $kga         kimai-global-array
 * @return array            contains the pct_IDs of the groups or false on error
 * @author ob
 */
function grp_get_pcts($grp_id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT pct_ID FROM " . $kga['server_prefix'] . "grp_pct WHERE grp_ID = ?;");
    $result = $pdo_query->execute(array($grp_id));
    if ($result == false) {
        return false;
    }
    
    $return_pcts = array();
    $counter = 0;
    
    while ($current_pct = $pdo_query->fetch()) {
        $return_pcts[$counter] = $current_pct['pct_ID'];
        $counter++;
    }
    
    return $return_pcts;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the events of the given group
 *
 * @param array $grp_id        grp_id of the group
 * @global array $kga         kimai-global-array
 * @return array            contains the evt_IDs of the groups or false on error
 * @author ob
 */
function grp_get_evts($grp_id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT evt_ID FROM " . $kga['server_prefix'] . "grp_evt WHERE grp_ID = ?;");
    $result = $pdo_query->execute(array($grp_id));
    if ($result == false) {
        return false;
    }
    
    $return_evts = array();
    $counter = 0;
    
    while ($current_evt = $pdo_query->fetch()) {
        $return_evts[$counter] = $current_evt['evt_ID'];
        $counter++;
    }
    
    return $return_evts;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Adds a new user
 *
 * @param array $data         username, email, and other data of the new user
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function usr_create($data) {
    global $kga, $pdo_conn;
    
    $data = clean_data($data);

    $pdo_query = $pdo_conn->prepare("INSERT INTO " . $kga['server_prefix'] . "usr (
    `usr_ID`,
    `usr_name`,
    `usr_grp`,
    `usr_sts`,
    `usr_active`,
    `rowlimit`,
    `skin`
    ) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $result = $pdo_query->execute(array(
    $data['usr_ID'],
    $data['usr_name'],
    $data['usr_grp'],
    $data['usr_sts'],
    $data['usr_active'],
    $data['rowlimit'],
    $data['skin']   
    ));
            
    if ($result == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Returns the data of a certain user
 *
 * @param array $usr_id        knd_id of the user
 * @global array $kga         kimai-global-array
 * @return array            the user's data (username, email-address, status etc) as array, false on failure
 * @author ob
 */
function usr_get_data($usr_id) {
    global $kga, $pdo_conn;

    $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "usr WHERE usr_ID = ?");
    $result =  $pdo_query->execute(array($usr_id));
    
    if ($result == false) {
        return false;
    } else {
        $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);
        return $result_array;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Edits a user by replacing his data by the new array
 *
 * @param array $usr_id       usr_id of the user to be edited
 * @param array $data         username, email, and other new data of the user
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function usr_edit($usr_id, $data) {
    global $kga, $pdo_conn;
    
    $data = clean_data($data);
            
    $pdo_conn->beginTransaction();
    
    $original_array = usr_get_data($usr_id);
    $new_array = array();
    
    foreach ($original_array as $key => $value) {
        if (isset($data[$key]) == true) {
            $new_array[$key] = $data[$key];
        } else {
            $new_array[$key] = $original_array[$key];
        }
    }
    
    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "usr SET  
    usr_name = ?,
    usr_grp = ?,
    usr_sts = ?,
    usr_trash = ?,
    usr_active = ?,
    usr_mail = ?,
    usr_alias = ?,
    pw = ?,
    rowlimit = ?,
    skin = ?,
    filter = ?,
    autoselection = ?,
    quickdelete = ?,
    allvisible = ?,
    flip_pct_display = ?,
    pct_comment_flag = ?,
    showIDs = ?,
    lang = ? 
    WHERE usr_id = ?;");
    
    $result = $pdo_query->execute(array(
    $new_array['usr_name'],
    $new_array['usr_grp'],
    $new_array['usr_sts'],
    $new_array['usr_trash'],
    $new_array['usr_active'],
    $new_array['usr_mail'],
    $new_array['usr_alias'],
    $new_array['pw'],
    $new_array['rowlimit'],
    $new_array['skin'],
    $new_array['filter'],
    $new_array['autoselection'],
    $new_array['quickdelete'],
    $new_array['allvisible'],
    $new_array['flip_pct_display'],
    $new_array['pct_comment_flag'],
    $new_array['showIDs'],
    $new_array['lang'],
    $usr_id
    ));
    
    if ($result == false) {
        return $result;
    }
    
    if ($pdo_conn->commit() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * deletes a user
 *
 * @param array $usr_id        usr_id of the user
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */

function usr_delete($usr_id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "usr SET usr_trash=1 WHERE usr_ID = ?;");
    $result = $pdo_query->execute(array($usr_id));
    if ($result == false) {
        return false;
    }
    return $result;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Assigns a leader to 1-n groups by adding entries to the cross table
 *
 * @param int $ldr_id        usr_id of the group leader to whom the groups will be assigned
 * @param array $grp_array    contains one or more grp_IDs
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function assign_ldr2grps($ldr_id, $grp_array) {
    global $kga, $pdo_conn;
    
    $pdo_conn->beginTransaction();
    
    $pdo_query = $pdo_conn->prepare("DELETE FROM " . $kga['server_prefix'] . "ldr WHERE grp_leader=?;");    
    $d_result = $pdo_query->execute(array($ldr_id));
    if ($d_result == false) {
            $pdo_conn->rollBack();
            return false;
    }
    
    foreach ($grp_array as $current_grp) {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "ldr WHERE grp_ID=? AND grp_leader=?;");
        $c_result = $pdo_query->execute(array($current_grp,$ldr_id));
        if (count($pdo_query->fetchAll()) == 0) {
            $pdo_query = $pdo_conn->prepare("INSERT INTO " . $kga['server_prefix'] . "ldr(grp_ID,grp_leader) VALUES (?,?);");
            $result = $pdo_query->execute(array($current_grp,$ldr_id));
            if ($result == false) {
                $pdo_conn->rollBack();
                return false;
            }
        }
    }
    
    if ($pdo_conn->commit() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Assigns a group to 1-n group leaders by adding entries to the cross table
 * (counterpart to assign_ldr2grp)
 * 
 * @param array $grp_id        grp_id of the group to which the group leaders will be assigned
 * @param array $ldr_array    contains one or more usr_ids of the leaders)
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function assign_grp2ldrs($grp_id, $ldr_array) {
    global $kga, $pdo_conn;
    
    $pdo_conn->beginTransaction();
    
    $d_query = $pdo_conn->prepare("DELETE FROM " . $kga['server_prefix'] . "ldr WHERE grp_ID=?;");
    $d_result = $d_query->execute(array($grp_id));
    if ($d_result == false) {
        return false;
    }
    
    foreach ($ldr_array as $current_ldr) {
        $c_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "ldr WHERE grp_ID=? AND grp_leader=?;");
        $c_result = $c_query->execute(array($grp_id,$current_ldr));
        if (count($c_query->fetchAll()) == 0) {
            $pdo_query = $pdo_conn->prepare("INSERT INTO " . $kga['server_prefix'] . "ldr (grp_ID,grp_leader) VALUES (?,?);");
            $result = $pdo_query->execute(array($grp_id,$current_ldr));
            if ($result == false) {
                return false;
            }
        }
    }
    
    if ($pdo_conn->commit() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the groups of the given group leader
 *
 * @param array $ldr_id        usr_id of the group leader
 * @global array $kga         kimai-global-array
 * @return array            contains the grp_IDs of the groups or false on error
 * @author ob
 */
function ldr_get_grps($ldr_id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT grp_ID FROM " . $kga['server_prefix'] . "ldr WHERE grp_leader = ?;");
    $result = $pdo_query->execute(array($ldr_id));
    if ($result == false) {
        return false;
    }
    
    $return_grps = array();
    $counter = 0;
    
    while ($current_grp = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $return_grps[$counter] = $current_grp['grp_ID'];
        $counter++;
    }
    
    return $return_grps;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the group leaders of the given group
 *
 * @param array $grp_id        grp_id of the group
 * @global array $kga         kimai-global-array
 * @return array            contains the usr_IDs of the group's group leaders or false on error
 * @author ob
 */
function grp_get_ldrs($grp_id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT grp_leader FROM " . $kga['server_prefix'] . "ldr WHERE grp_ID = ?;");
    $result = $pdo_query->execute(array($grp_id));
    if ($result == false) {
        return false;
    }
    
    $return_ldrs = array();
    $counter = 0;
    
    while ($current_ldr = $pdo_query->fetch()) {
        $return_ldrs[$counter] = $current_ldr['grp_leader'];
        $counter++;
    }
    
    return $return_ldrs;
}
    
// -----------------------------------------------------------------------------------------------------------

/**
 * Adds a new group
 *
 * @param array $data         name and other data of the new group
 * @global array $kga         kimai-global-array
 * @return int                the grp_id of the new group, false on failure
 * @author ob
 */
function grp_create($data) {
    global $kga, $pdo_conn;
    
    $data = clean_data($data);
        
    $pdo_query = $pdo_conn->prepare("INSERT INTO " . $kga['server_prefix'] . "grp (grp_name, grp_trash) VALUES (?, ?);");
    $result = $pdo_query->execute(array($data['grp_name'], 0));
    
    if ($result == true) {
        return $pdo_conn->lastInsertId();
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Returns the data of a certain group
 *
 * @param array $grp_id        grp_id of the group
 * @global array $kga         kimai-global-array
 * @return array            the group's data (name, leader ID, etc) as array, false on failure
 * @author ob
 */
function grp_get_data($grp_id) {
    global $kga, $pdo_conn;

    $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "grp WHERE grp_ID = ?");
    $result =  $pdo_query->execute(array($grp_id));
    
    if ($result == false) {
        return false;
    } else {
        $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);
        return $result_array;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Returns the number of users in a certain group
 *
 * @param array $grp_id        grp_id of the group
 * @global array $kga         kimai-global-array
 * @return int            the number of users in the group
 * @author ob
 */
function grp_count_users($grp_id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT COUNT(*) FROM " . $kga['server_prefix'] . "usr WHERE usr_grp = ?");
    $result =  $pdo_query->execute(array($grp_id));
    
    if ($result == false) {
        return false;
    } else {
        $result_array = $pdo_query->fetch();
        return $result_array[0];
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Edits a group by replacing its data by the new array
 *
 * @param array $grp_id        grp_id of the group to be edited
 * @param array $data    name and other new data of the group
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function grp_edit($grp_id, $data) {
    global $kga, $pdo_conn;
    
    $data = clean_data($data); 
       
    $pdo_conn->beginTransaction();
    
    $original_array = grp_get_data($grp_id);
    $new_array = array();
    
    foreach ($original_array as $key => $value) {
        if (isset($data[$key]) == true) {
            $new_array[$key] = $data[$key];
        } else {
            $new_array[$key] = $original_array[$key];
        }
    }
    
    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "grp SET grp_name = ? WHERE grp_ID = ?;");
    $result = $pdo_query->execute(array($new_array['grp_name'],$grp_id));
    
    if ($result == false) {
        return $result;
    }
    
    if ($pdo_conn->commit() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * deletes a group
 *
 * @param array $grp_id        grp_id of the group
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function grp_delete($grp_id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "grp SET grp_trash=1 WHERE grp_ID = ?;");
    $result = $pdo_query->execute(array($grp_id));
    if ($result == false) {
        return false;
    }
    
    return $result;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Returns all configuration variables
 *
 * @global array $kga         kimai-global-array
 * @return array            array with the vars from the var table
 * @author ob
 */

function var_get_data() {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "var;");
    $result = $pdo_query->execute();
    $row  = $pdo_query->fetch(PDO::FETCH_ASSOC);
    
    $var_data = array();
        
    do { 
        $var_data[$row['var']] = $row['value']; 
    } while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC));
    
    return $var_data;
}

// -----------------------------------------------------------------------------------------------------------

// Still under development!!! DO NOT USE YET!

/**
 * Edits a configuration variables by replacing the data by the new array
 *
 * @param array $data    variables array
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function var_edit($data) {
    global $kga, $pdo_conn;
    
    $data = clean_data($data);
        
    $pdo_conn->beginTransaction();
    
    $original_array = var_get_data();
    $new_array = array();
    
    foreach ($original_array as $key => $value) {
        if (isset($data[$key]) == true) {
            $new_array[$key] = $data[$key];
        } else {
            $new_array[$key] = $original_array[$key];
        }
    }
    
    foreach ($new_array as $current_var_key => $current_var_value) {
    
        $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "var SET value = ? WHERE var = ?");
        $result = $pdo_query->execute(array($current_var_value, $current_var_key));
        
        $err = $pdo_query->errorInfo();
    
        if ($result == false) {
            return $result;
        }        
    }
    
    if ($pdo_conn->commit() == false) {
        return false;
    }
    
    return true;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * checks whether there is a running zef-entry for a given user
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return boolean true=there is an entry, false=there is none
 * @author ob 
 */

function get_rec_state($usr_id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT COUNT( * ) FROM " . $kga['server_prefix'] . "zef WHERE zef_usrID = ? AND zef_in > 0 AND zef_out = 0;");
    $result = $pdo_query->execute(array($usr_id));
    $result_array = $pdo_query->fetch();
    
    if ($result_array[0] == 0) {
        return 0;    
    } else {
        return 1;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * validates the contents of the zef-table and marks them if there is a problem
 *
 * @global array $kga kimai-global-array
 * @return boolean true=everything okay, false=there was at least one issue
 * @author ob 
 */

function validate_zef() {
    global $kga, $pdo_conn;
    
    $return_state = true;    
    
    // Lock tables
    $pdo_query_l = $pdo_conn->prepare("LOCK TABLE " . $kga['server_prefix'] . "usr, " . $kga['server_prefix'] . "zef");
    $result_l = $pdo_query_l->execute();
    
    // case 1: scan for multiple running entries of the same user
    
    $pdo_query = $pdo_conn->prepare("SELECT usr_ID FROM " . $kga['server_prefix'] . "usr");
    $result = $pdo_query->execute();
    
    while ($current_row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {    
        // echo $current_row['usr_ID'] . "<br>";
        $pdo_query_zef = $pdo_conn->prepare("SELECT COUNT(*) FROM " . $kga['server_prefix'] . "zef WHERE zef_usrID = ? AND zef_in > 0 AND zef_out = 0;");
        $result_zef = $pdo_query_zef->execute(array($current_row['usr_ID']));
        $result_array_zef = $pdo_query_zef->fetch();
        
        if ($result_array_zef[0] > 1) {
        
            $return_state = false;
        
            // echo "User " . $current_row['usr_ID'] . "has multiple running zef entries:<br>";
            
            $pdo_query_zef = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "zef WHERE zef_usrID = ? AND zef_in > 0 AND zef_out = 0;");
            $result_zef = $pdo_query_zef->execute(array($current_row['usr_ID']));
            
            // mark all running-zef-entries with a comment (except the newest one)
            $pdo_query_zef_max = $pdo_conn->prepare("SELECT MAX(zef_in), zef_ID FROM " . $kga['server_prefix'] . "zef WHERE zef_usrID = ? AND zef_in > 0 AND zef_out = 0 GROUP BY zef_ID;");
            $result_zef_max = $pdo_query_zef_max->execute(array($current_row['usr_ID']));
            $result_array_zef_max = $pdo_query_zef_max->fetch(PDO::FETCH_ASSOC);
            $max_id = $result_array_zef_max['zef_ID'];
            
            while ($current_row_zef = $pdo_query_zef->fetch(PDO::FETCH_ASSOC)) {
            
                if($current_row_zef['zef_ID'] != $max_id) {
                    $pdo_query_zef_edit = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "zef SET 
                    zef_comment = 'bad entry: multiple running entries found',
                    zef_comment_type = 2
                    WHERE zef_ID = ?");
                    $result_zef_edit = $pdo_query_zef_edit->execute(array($current_row_zef['zef_ID']));
                    $err = $pdo_query_zef_edit->errorInfo();
                    error_log("ERROR: " . $err[2]);
                }
            
                // var_dump($current_row_zef);
                // echo "<br>";
            }
        }
    }
    
    // Unlock tables
    $pdo_query_ul = $pdo_conn->prepare("UNLOCK TABLE " . $kga['server_prefix'] . "usr, " . $kga['server_prefix'] . "zef");
    $result_ul = $pdo_query_ul->execute();
    
    return $return_state;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Returns the data of a certain time record
 *
 * @param array $zef_id        zef_id of the record
 * @global array $kga          kimai-global-array
 * @return array               the record's data (time, event id, project id etc) as array, false on failure
 * @author ob
 */
function zef_get_data($zef_id) {
    global $kga, $pdo_conn;

    if ($zef_id) {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "zef WHERE zef_ID = ?");
    } else {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "zef WHERE zef_usrID = ".$kga['usr']['usr_ID']." ORDER BY zef_ID DESC LIMIT 1");
        // logfile("SELECT * FROM " . $kga['server_prefix'] . "zef ORDER BY zef_ID DESC LIMIT 1");
    }
    
    $result = $pdo_query->execute(array($zef_id));
    
    if ($result == false) {
        return false;
    } else {
        $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);
        return $result_array;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * delete zef entry 
 *
 * @param integer $usr_ID 
 * @param integer $id -> ID of record
 * @global array  $kga kimai-global-array
 * @author th
 */
function zef_delete_record($id) {
    global $kga, $pdo_conn;
    $pdo_query = $pdo_conn->prepare("DELETE FROM " . $kga['server_prefix'] . "zef WHERE `zef_ID` = ? LIMIT 1;");
    $result = $pdo_query->execute(array($id));
    if ($result == false) {
        return $result;
    }
} 

// -----------------------------------------------------------------------------------------------------------

/**
 * create zef entry 
 *
 * @param integer $id    ID of record
 * @param integer $data  array with record data
 * @global array  $kga    kimai-global-array
 * @author th
 */
function zef_create_record($usr_ID,$data) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("INSERT INTO " . $kga['server_prefix'] . "zef (  
    `zef_pctID`, 
    `zef_evtID`,
    `zef_location`,
    `zef_trackingnr`,
    `zef_comment`,
    `zef_comment_type`,
    `zef_in`,
    `zef_out`,
    `zef_time`,
    `zef_usrID`
    ) VALUES (?,?,?,?,?,?,?,?,?,?)
    ;");
    
    $result = $pdo_query->execute(array(
    $data['pct_ID'],
    $data['evt_ID'] ,
    $data['zlocation'],
    $data['trackingnr'],
    $data['comment'],
    $data['comment_type'] ,
    $data['in'],
    $data['out'],
    $data['diff'],
    $usr_ID
    ));
   logfile($pdo_query->errorInfo());
    if ($result == false) {
        return $result;
    }
} 

// -----------------------------------------------------------------------------------------------------------

/**
 * edit zef entry 
 *
 * @param integer $id ID of record
 * @global array $kga kimai-global-array
 * @param integer $data  array with new record data
 * @author th
 */
 
function zef_edit_record($id,$data) {
    global $kga, $pdo_conn;
    
    $original_array = zef_get_data($id);
    $new_array = array();
    
    foreach ($original_array as $key => $value) {
        if (isset($data[$key]) == true) {
            $new_array[$key] = $data[$key];
        } else {
            $new_array[$key] = $original_array[$key];
        }
    }

    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "zef SET
    zef_pctID = ?,
    zef_evtID = ?,
    zef_location = ?,
    zef_trackingnr = ?,
    zef_comment = ?,
    zef_comment_type = ?,
    zef_in = ?,
    zef_out = ?,
    zef_time = ?
    WHERE zef_ID = ?;");    
    
    $result = $pdo_query->execute(array(
    $new_array['zef_pctID'],
    $new_array['zef_evtID'] ,
    $new_array['zef_location'],
    $new_array['zef_trackingnr'],
    $new_array['zef_comment'],
    $new_array['zef_comment_type'] ,
    $new_array['zef_in'],
    $new_array['zef_out'],
    $new_array['zef_time'],
    $id
    ));
   
    if ($result == false) {
        return $result;
    }
    
    logfile("editrecord:result:".$result);

    
    // $data['pct_ID']       
    // $data['evt_ID']       
    // $data['comment']      
    // $data['comment_type'] 
    // $data['erase']        
    // $data['in']           
    // $data['out']          
    // $data['diff']    
    
    // if wrong time values have been entered in the edit window
    // the following 3 variables arrive as zeros - like so:

    // $data['in']   = 0;
    // $data['out']  = 0;
    // $data['diff'] = 0;   
    
    // in this case the record has to be edited WITHOUT setting new time values
    

     // @oleg: ein zef-eintrag muss auch ohne die zeiten aktualisierbar sein weil die ggf. bei der prüfung durchfallen.
} 

// -----------------------------------------------------------------------------------------------------------

/* ############################### Old (database-related) functions from func.php ######################### */

/**
 * saves timespace of user in database (table conf)
 *
 * @param string $timespace_in unix seconds
 * @param string $timespace_out unix seconds
 * @param string $user ID of user
 *
 * @author th
 */
function save_timespace($timespace_in,$timespace_out,$user) {
    global $kga, $pdo_conn;

    if ($timespace_in == 0 && $timespace_out == 0) {
        $mon = date("n"); $day = date("j"); $Y = date("Y"); 
        $timespace_in  = mktime(0,0,0,$mon,$day,$Y);
        $timespace_out = mktime(23,59,59,$mon,$day,$Y);
    }
       
    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "usr SET timespace_in  = ? WHERE usr_ID = ?;");
    $pdo_query->execute(array($timespace_in ,$user));
    
    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "usr SET timespace_out = ? WHERE usr_ID = ?;");
    $pdo_query->execute(array($timespace_out ,$user));
    
    return timespace_warning($timespace_in,$timespace_out);
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns list of projects for specific group as array
 *
 * @param integer $user ID of user in database
 * @global array $kga kimai-global-array
 * @return array
 * @author th
 */
function get_arr_pct($group) {
    global $kga, $pdo_conn;
    
    $arr = array();

    if ($group == "all") {
        if ($kga['conf']['flip_pct_display']) {
            $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "pct JOIN " . $kga['server_prefix'] . "knd ON " . $kga['server_prefix'] . "pct.pct_kndID = " . $kga['server_prefix'] . "knd.knd_ID JOIN " . $kga['server_prefix'] . "grp_pct ON " . $kga['server_prefix'] . "grp_pct.pct_ID = " . $kga['server_prefix'] . "pct.pct_ID ORDER BY knd_name,pct_name;");
        } else {
            $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "pct JOIN " . $kga['server_prefix'] . "knd ON " . $kga['server_prefix'] . "pct.pct_kndID = " . $kga['server_prefix'] . "knd.knd_ID JOIN " . $kga['server_prefix'] . "grp_pct ON " . $kga['server_prefix'] . "grp_pct.pct_ID = " . $kga['server_prefix'] . "pct.pct_ID ORDER BY pct_name,knd_name;");
        }
        $result = $pdo_query->execute();    
    } else {
        if ($kga['conf']['flip_pct_display']) {
            $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "pct JOIN " . $kga['server_prefix'] . "knd ON " . $kga['server_prefix'] . "pct.pct_kndID = " . $kga['server_prefix'] . "knd.knd_ID JOIN " . $kga['server_prefix'] . "grp_pct ON " . $kga['server_prefix'] . "grp_pct.pct_ID = " . $kga['server_prefix'] . "pct.pct_ID WHERE " . $kga['server_prefix'] . "grp_pct.grp_ID = ? ORDER BY knd_name,pct_name;");
        } else {
            $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "pct JOIN " . $kga['server_prefix'] . "knd ON " . $kga['server_prefix'] . "pct.pct_kndID = " . $kga['server_prefix'] . "knd.knd_ID JOIN " . $kga['server_prefix'] . "grp_pct ON " . $kga['server_prefix'] . "grp_pct.pct_ID = " . $kga['server_prefix'] . "pct.pct_ID WHERE " . $kga['server_prefix'] . "grp_pct.grp_ID = ? ORDER BY pct_name,knd_name;");
        }
        $result = $pdo_query->execute(array($group));
    }
    
    $i=0;
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $arr[$i]['pct_ID']      = $row['pct_ID'];
        $arr[$i]['pct_name']    = $row['pct_name'];
		$arr[$i]['pct_comment'] = $row['pct_comment'];
        $arr[$i]['knd_name']    = $row['knd_name'];
        $arr[$i]['knd_ID']      = $row['knd_ID'];
        $arr[$i]['pct_visible'] = $row['pct_visible'];
        $i++;
    }
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns list of projects for specific group and specific customer as array
 *
 * @param integer $user ID of user in database
 * @param integer $knd_id customer id
 * @global array $kga kimai-global-array
 * @return array
 * @author ob
 */
function get_arr_pct_by_knd($group, $knd_id) {
    global $kga, $pdo_conn;
    
    $arr = array();

    if ($kga['conf']['flip_pct_display']) {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "pct JOIN " . $kga['server_prefix'] . "knd ON " . $kga['server_prefix'] . "pct.pct_kndID = " . $kga['server_prefix'] . "knd.knd_ID JOIN " . $kga['server_prefix'] . "grp_pct ON " . $kga['server_prefix'] . "grp_pct.pct_ID = " . $kga['server_prefix'] . "pct.pct_ID WHERE " . $kga['server_prefix'] . "grp_pct.grp_ID = ? AND " . $kga['server_prefix'] . "pct.pct_kndID = ? ORDER BY knd_name,pct_name;");
    } else {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "pct JOIN " . $kga['server_prefix'] . "knd ON " . $kga['server_prefix'] . "pct.pct_kndID = " . $kga['server_prefix'] . "knd.knd_ID JOIN " . $kga['server_prefix'] . "grp_pct ON " . $kga['server_prefix'] . "grp_pct.pct_ID = " . $kga['server_prefix'] . "pct.pct_ID WHERE " . $kga['server_prefix'] . "grp_pct.grp_ID = ? AND " . $kga['server_prefix'] . "pct.pct_kndID = ? ORDER BY pct_name,knd_name;");        
    }  
    
    $result = $pdo_query->execute(array($group, $knd_id));
    
    $i=0;
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $arr[$i]['pct_ID']      = $row['pct_ID'];
        $arr[$i]['pct_name']    = $row['pct_name'];
        $arr[$i]['knd_name']    = $row['knd_name'];
        $arr[$i]['knd_ID']      = $row['knd_ID'];
        $arr[$i]['pct_visible'] = $row['pct_visible'];
        $i++;
    }
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns timesheet for specific user as multidimensional array
 *
 * @param integer $user ID of user in table usr
 * @param integer $in start of timespace in unix seconds
 * @param integer $out end of timespace in unix seconds
 * @global array $kga kimai-global-array
 * @return array
 * @author th 
 */

// TODO: Test it!
function get_arr_zef($user,$in,$out,$limit) {
    global $kga, $pdo_conn;
    
    $currTimespace = "AND zef_in > $in AND zef_out < $out";
    if ($limit) {
        if (isset($kga['conf']['rowlimit'])) {
            $limit = "LIMIT " .$kga['conf']['rowlimit'];
        } else {
            $limit="LIMIT 100";
        }
    } else {
        $limit="";
    }

    if (!$kga['global']) {
        $not_global_query_extension = " Join " . $kga['server_prefix'] . "usr ON zef_usrID = usr_ID ";
    } else {
        $not_global_query_extension = " Join " . $kga['server_prefix'] . "usr ";
    }
    
    $query = "SELECT zef_ID, zef_in, zef_out, zef_time, zef_pctID, zef_evtID, zef_usrID, pct_ID, knd_name, pct_kndID, evt_name, pct_comment, pct_name, zef_location, zef_trackingnr, zef_comment, zef_comment_type, usr_alias
			
             FROM " . $kga['server_prefix'] . "zef
             Join " . $kga['server_prefix'] . "pct ON zef_pctID = pct_ID
             Join " . $kga['server_prefix'] . "knd ON pct_kndID = knd_ID
             " . $not_global_query_extension . "
             Join " . $kga['server_prefix'] . "evt ON evt_ID    = zef_evtID
             WHERE zef_pctID > 0 AND zef_evtID > 0 " . $currTimespace . " ORDER BY zef_in DESC " . $limit . ";";
             
    $pdo_query = $pdo_conn->prepare($query);
    
             $pdo_query->execute(array($user));
                
                logfile($query);
    $i=0;
    $arr=array();
    /* TODO: needs revision as foreach loop */
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $arr[$i]['zef_ID']           = $row['zef_ID'];
        $arr[$i]['zef_in']           = $row['zef_in'];
        $arr[$i]['zef_out']          = $row['zef_out'];
        $arr[$i]['zef_time']         = $row['zef_time'];
        $arr[$i]['zef_apos']         = intervallApos($row['zef_time']);
        $arr[$i]['zef_coln']         = intervallColon($row['zef_time']);
        $arr[$i]['zef_pctID']        = $row['zef_pctID'];
        $arr[$i]['zef_evtID']        = $row['zef_evtID'];
        $arr[$i]['zef_usrID']        = $row['zef_usrID'];
        $arr[$i]['pct_ID']           = $row['pct_ID'];
        $arr[$i]['knd_name']         = $row['knd_name'];
        // $arr[$i]['grp_name']      = $row['grp_name'];
        // $arr[$i]['pct_grpID']     = $row['pct_grpID'];
        $arr[$i]['pct_kndID']        = $row['pct_kndID'];
        $arr[$i]['evt_name']         = $row['evt_name'];
        $arr[$i]['pct_name']         = $row['pct_name'];
        $arr[$i]['pct_comment']      = $row['pct_comment'];
        $arr[$i]['zef_location']     = $row['zef_location'];
        $arr[$i]['zef_trackingnr']   = $row['zef_trackingnr'];
        $arr[$i]['zef_comment']      = $row['zef_comment'];
        $arr[$i]['zef_comment_type'] = $row['zef_comment_type'];
        $arr[$i]['usr_alias']        = $row['usr_alias'];
        $i++;
    }
    
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * checks if user is logged on and returns user information as array
 * kicks client if is not verified
 * TODO: this and get_config should be one function
 *
 * <pre>
 * returns: 
 * [usr_ID] user ID, 
 * [usr_sts] user status (rights), 
 * [usr_grp] group of user, 
 * [usr_name] username 
 * </pre>
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return array
 * @author th 
 */
function checkUser() {
    global $kga, $pdo_conn;
    if (!$kga['virtual_users']) {
        
        if (isset($_COOKIE['kimai_usr']) && isset($_COOKIE['kimai_key']) && $_COOKIE['kimai_usr'] != "0" && $_COOKIE['kimai_key'] != "0") {
            $kimai_usr = addslashes($_COOKIE['kimai_usr']);
            $kimai_key = addslashes($_COOKIE['kimai_key']);
            if (get_seq($kimai_usr) != $kimai_key) {
                kickUser();
            } else {
                $data     = $pdo_query = $pdo_conn->prepare("SELECT usr_ID,usr_sts,usr_grp FROM " . $kga['server_prefix'] . "usr WHERE usr_name = ? AND usr_active = '1' AND NOT usr_trash = '1';");
                $result   = $pdo_query->execute(array($kimai_usr));
                $row      = $pdo_query->fetch(PDO::FETCH_ASSOC);
                $usr_ID   = $row['usr_ID'];
                $usr_sts  = $row['usr_sts']; // User Status -> 0=Admin | 1=GroupLeader | 2=User
                $usr_grp  = $row['usr_grp'];
                $usr_name = $kimai_usr;
                if ($usr_ID < 1) {
                    kickUser();
                }
            }
            
        } else {
            kickUser();
        }

    } else {
        $usr_ID   = $_SESSION['user']; 
        $usr_grp  = $_SESSION['user'];  
        $usr_name = $_SESSION['user'];  
        $usr_sts  = 2; 
    }
    
    if ($usr_ID<1) {
        kickUser();
    }
    
    // load configuration
    get_global_config();
    get_user_config($usr_ID);

    // override conf.php language if user has chosen a language in the prefs
    if ($kga['conf']['lang'] != "") {
      $kga['language'] = $kga['conf']['lang'];
    }
    require(sprintf(WEBROOT."language/%s.php",$kga['language']));
    
    return $kga['usr'];
}

// -----------------------------------------------------------------------------------------------------------

/**
 * write global configuration into $kga
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return array $kga 
 * @author th
 *
 */
function get_global_config() {
  global $kga, $pdo_conn;    
  // get values from global configuration 
  $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "var;");
  $result = $pdo_query->execute();
  $row  = $pdo_query->fetch(PDO::FETCH_ASSOC);

  do { 
      $kga['conf'][$row['var']] = $row['value']; 
  } while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC));
}


/**
 * write details of a specific user into $kga
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return array $kga 
 * @author th
 *
 */
function get_user_config($user) {
  global $kga, $pdo_conn;    
  if (!$user) 
    return;

  // get values from user record
  $pdo_query = $pdo_conn->prepare("SELECT
  `usr_ID`,
  `usr_name`,
  `usr_grp`,
  `usr_sts`,
  `usr_trash`,
  `usr_active`,
  `usr_mail`,
  `pw`,
  `ban`,
  `banTime`,
  `secure`
  FROM " . $kga['server_prefix'] . "usr WHERE usr_ID = ?;");

  $result = $pdo_query->execute(array($user));
  $row  = $pdo_query->fetch(PDO::FETCH_ASSOC);
  foreach( $row as $key => $value) {
      $kga['usr'][$key] = $value;
  }

  $pdo_query->fetchAll();

  // get values from user configuration (user-preferences)
  $pdo_query = $pdo_conn->prepare("SELECT 
  `rowlimit`,
  `skin`,
  `lastProject`,
  `lastEvent`,
  `lastRecord`,
  `filter`,
  `filter_knd`,
  `filter_pct`,
  `filter_evt`,
  `view_knd`,
  `view_pct`,
  `view_evt`,
  `zef_anzahl`,
  `timespace_in`,
  `timespace_out`,
  `autoselection`,
  `quickdelete`,
  `allvisible`,
  `flip_pct_display`,
  `pct_comment_flag`,
  `showIDs`,
  `lang`
  FROM " . $kga['server_prefix'] . "usr WHERE usr_ID = ?;");

  $result = $pdo_query->execute(array($user));
  $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
  foreach( $row as $key => $value) {
      $kga['conf'][$key] = $value;
  }

            $pdo_query->fetchAll();
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns ID of running timesheet event for specific user
 *
 *
 * TODO: this function is not really returning USERdata - it simply returns the last record of ALL records ...
 *
 * <pre>
 * ['zef_ID'] ID of last recorded task
 * ['zef_in'] in point of timesheet record in unix seconds
 * ['zef_pctID']
 * ['zef_evtID']
 * </pre>
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return integer
 * @author th
 */
function get_event_last($user) {
    global $kga, $pdo_conn;
    $lastRecord = $kga['conf']['lastRecord'];
    $pdo_query = $pdo_conn->prepare("SELECT zef_ID,zef_in,zef_pctID,zef_evtID FROM " . $kga['server_prefix'] . "zef WHERE zef_ID = ?");
    $pdo_query->execute(array($lastRecord));
    $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
    return $row;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns single timesheet entry as array
 *
 * @param integer $id ID of entry in table zef
 * @global array $kga kimai-global-array
 * @return array
 * @author th
 */
function get_entry_zef($id) {
    global $kga, $pdo_conn;     
    $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "zef 
    Left Join " . $kga['server_prefix'] . "pct ON zef_pctID = pct_ID 
    Left Join " . $kga['server_prefix'] . "knd ON pct_kndID = knd_ID 
    Left Join " . $kga['server_prefix'] . "evt ON evt_ID    = zef_evtID
    WHERE zef_ID = ? LIMIT 1;");
  
    $pdo_query->execute(array($id));
    $row    = $pdo_query->fetch(PDO::FETCH_ASSOC);
    return $row;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns time summary of current timesheet
 *
 * @param integer $user ID of user in table usr
 * @param integer $in start of timespace in unix seconds
 * @param integer $out end of timespace in unix seconds
 * @global array $kga kimai-global-array
 * @return integer
 * @author th 
 */
 
// correct syntax - but doesn't work with all PDO versions because of a bug
// reported here: http://pecl.php.net/bugs/bug.php?id=8045 
// function get_zef_time($user,$in,$out) {
//     global $kga;
//     global $pdo_conn;
//     $pdo_query = $pdo_conn->prepare("SELECT SUM(`zef_time`) AS zeit FROM " . $kga['server_prefix'] . "zef WHERE zef_usrID = ? AND zef_in > ? AND zef_out < ? LIMIT ?;");
//     $pdo_query->execute(array($user,$in,$out,$kga['conf']['rowlimit']));
//     $data = $pdo_query->fetch(PDO::FETCH_ASSOC);
//     $zeit = $data['zeit'];
//     return $zeit;
// }
// th: solving this by doing a loop and add the seconds manually...
//     btw - using the rowlimit is not correct here because we want the time for the timespace, not for the rows in the timesheet ... my fault
function get_zef_time($user,$in,$out) {
    global $kga, $pdo_conn;
    $pdo_query = $pdo_conn->prepare("SELECT zef_time FROM " . $kga['server_prefix'] . "zef WHERE zef_usrID = ? AND zef_in > ? AND zef_out < ? ;");
    $pdo_query->execute(array($user,$in,$out));
    $sum = 0;
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $sum+=(int)$row['zef_time'];
    }
    return $sum;
}

// -----------------------------------------------------------------------------------------------------------

// TODO: check if this function is redundant!!!
// ob: no it isn't :-)
// th: sorry for the 3 '!' ... this was an order to myself, i'm sometimes a little rude to myself :D
/**
 * returns list of customers in a group as array
 *
 * @param integer $group ID of group in table grp or "all" for all groups
 * @global array $kga kimai-global-array
 * @return array
 * @author th
 */
function get_arr_knd($group) {
    global $kga, $pdo_conn;
        
    $arr = array();
    if ($group == "all") {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "knd ORDER BY knd_name;");
        $pdo_query->execute();
    } else {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "knd JOIN " . $kga['server_prefix'] . "grp_knd ON `" . $kga['server_prefix'] . "grp_knd`.`knd_ID`=`" . $kga['server_prefix'] . "knd`.`knd_ID` WHERE `" . $kga['server_prefix'] . "grp_knd`.`grp_ID` = ? ORDER BY knd_name;");
        $pdo_query->execute(array($group));
    }
  
    $i=0;
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $arr[$i]['knd_ID']      = $row['knd_ID'];
        $arr[$i]['knd_name']    = $row['knd_name'];
        $arr[$i]['knd_visible'] = $row['knd_visible'];
        $i++;
    }
    
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns list of users the given user can watch
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return array
 * @author sl
 */
function get_arr_watchable_users($user_id) {
    global $kga,$pdo_conn;

    $arr = array();

    // check if user is admin
    $pdo_query = $pdo_conn->prepare("SELECT usr_sts FROM " . $kga['server_prefix'] . "usr WHERE usr_ID = ?");
    $result   = $pdo_query->execute(array($user_id));
    $row      = $pdo_query->fetch(PDO::FETCH_ASSOC);

    // SELECT usr_ID,usr_name FROM kimai_usr u INNER JOIN kimai_ldr l ON usr_grp = grp_ID WHERE grp_leader = 990287573
    if ($row['usr_sts'] == "0") { // if is admin
      $pdo_query = $pdo_conn->prepare("SELECT usr_ID,usr_name FROM " . $kga['server_prefix'] . "usr");
      $pdo_query->execute();
    }
    else {
      $pdo_query = $pdo_conn->prepare("SELECT usr_ID,usr_name FROM " . $kga['server_prefix'] . "usr INNER JOIN " . $kga['server_prefix'] . "ldr ON usr_grp = grp_ID WHERE grp_leader = ?");
      $pdo_query->execute(array($user_id));
    }
    
    $i=0;
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $arr[$i]['usr_ID']   = $row['usr_ID'];
        $arr[$i]['usr_name'] = $row['usr_name'];
        $i++;
    }
    
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns assoc. array where the index is the ID of a user and the value the time
 * this user has accumulated in the given time with respect to the filtersettings
 *
 * @param integer $in from this timestamp
* @param integer $out to this  timestamp
* @param integer $user ID of user in table usr
* @param integer $customer ID of customer in table knd
* @param integer $project ID of project in table pct
 * @global array $kga kimai-global-array
 * @return array
 * @author sl
 */
function get_arr_time_usr($in,$out,$user = -1, $customer = -1, $project = -1) {
    global $kga;
    global $pdo_conn;
    
    $whereClauses = array();
    
    if ($user > -1) {
      $whereClauses[] = "zef_usrID = $user";
    }
    
    if ($customer > -1) {
      $whereClauses[] = "knd_ID = $customer";
    }
    
    if ($project > -1) {
      $whereClauses[] = "pct_ID = $project";
    }  

    if ($in)
      $whereClauses[]="zef_in > $in";
    if ($out)
      $whereClauses[]="zef_out < $out";
    
    
 $pdo_query = $pdo_conn->prepare("SELECT SUM(zef_time) as zeit, usr_ID
             FROM " . $kga['server_prefix'] . "zef 
             Join " . $kga['server_prefix'] . "pct ON zef_pctID = pct_ID
             Join " . $kga['server_prefix'] . "knd ON pct_kndID = knd_ID
             Join " . $kga['server_prefix'] . "usr ON zef_usrID = usr_ID
             Join " . $kga['server_prefix'] . "evt ON evt_ID    = zef_evtID "
             .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses). " ORDER BY zef_in DESC;");
    
             $pdo_query->execute();

    $arr = array();  
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if (empty($row['usr_ID'])) break;
        $arr[$row['usr_ID']] = $row['zeit'];
    }
    
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns list of time summary attached to customer ID's within specific timespace as array
 * !! becomes obsolete with new querys !!
 *
 * @param integer $in start of timespace in unix seconds
 * @param integer $out end of timespace in unix seconds
 * @param integer $user filter for only this ID of auser
 * @param integer $customer filter for only this ID of a customer
 * @param integer $project filter for only this ID of a project
 * @global array $kga kimai-global-array
 * @return array
 * @author sl
 */
function get_arr_time_knd($in,$out,$user = -1, $customer = -1, $project = -1) {
    global $kga;
    global $pdo_conn;
    
    $whereClauses = array();
    
    if ($user > -1) {
      $whereClauses[] = "zef_usrID = $user";
    }
    
    if ($customer > -1) {
      $whereClauses[] = "knd_ID = $customer";
    }
    
    if ($project > -1) {
      $whereClauses[] = "pct_ID = $project";
    }  
    
    if ($in) 
      $whereClauses[]="zef_in > $in";
    if ($out) 
      $whereClauses[]="zef_out < $out";
    $arr = array();  
    
    $pdo_query = $pdo_conn->prepare("SELECT SUM(zef_time) as zeit, knd_ID FROM " . $kga['server_prefix'] . "zef 
            Left Join " . $kga['server_prefix'] . "pct ON zef_pctID = pct_ID
            Left Join " . $kga['server_prefix'] . "knd ON pct_kndID = knd_ID ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
            " GROUP BY knd_ID;");
    $pdo_query->execute();
    
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if (empty($row['knd_ID'])) break;
        $arr[$row['knd_ID']] = $row['zeit'];
    }
    
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns list of time summary attached to project ID's within specific timespace as array
 * !! becomes obsolete with new querys !!
 *
 * @param integer $in start time in unix seconds
 * @param integer $out end time in unix seconds
 * @param integer $user filter for only this ID of auser
 * @param integer $customer filter for only this ID of a customer
 * @param integer $project filter for only this ID of a project
 * @global array $kga kimai-global-array
 * @return array
 * @author sl
 */
function get_arr_time_pct($in,$out,$user = -1,$customer = -1, $project = -1) {
    global $kga;
    global $pdo_conn;
    
    $whereClauses = array();
    
    if ($user > -1) {
      $whereClauses[] = "zef_usrID = $user";
    }
    
    if ($customer > -1) {
      $whereClauses[] = "knd_ID = $customer";
    }
    
    if ($project > -1) {
      $whereClauses[] = "pct_ID = $project";
    }  

    if ($in)
      $whereClauses[]="zef_in > $in";
    if ($out)
      $whereClauses[]="zef_out < $out";
    $arr = array();
    $pdo_query = $pdo_conn->prepare("SELECT sum(zef_time) as zeit,zef_pctID FROM " . $kga['server_prefix'] . "zef 
        Left Join " . $kga['server_prefix'] . "pct ON zef_pctID = pct_ID
        Left Join " . $kga['server_prefix'] . "knd ON pct_kndID = knd_ID ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
       " GROUP BY zef_pctID;");
    $pdo_query->execute();

    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if (empty($row['zef_pctID'])) break;
        $arr[$row['zef_pctID']] = $row['zeit'];
    }
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

## Load into Array: Events 
function get_arr_evt($group) {
    global $kga, $pdo_conn;
    
    $arr = array();
    if ($group == "all") {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "evt ORDER BY evt_name;");
        $pdo_query->execute();
    } else {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "evt JOIN " . $kga['server_prefix'] . "grp_evt ON `" . $kga['server_prefix'] . "grp_evt`.`evt_ID`=`" . $kga['server_prefix'] . "evt`.`evt_ID` WHERE `" . $kga['server_prefix'] . "grp_evt`.`grp_ID` = ? ORDER BY evt_name;");
        $pdo_query->execute(array($group));
    }
    
    $i=0;
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $arr[$i]['evt_ID'] = $row['evt_ID'];
        $arr[$i]['evt_name'] = $row['evt_name'];
        $arr[$i]['evt_visible'] = $row['evt_visible'];
        $i++;
    }
 
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns list of time summary attached to event ID's within specific timespace as array
 *
 * @param integer $in start time in unix seconds
 * @param integer $out end time in unix seconds
 * @param integer $user filter for only this ID of auser
 * @param integer $customer filter for only this ID of a customer
 * @param integer $project filter for only this ID of a project
 * @global array $kga kimai-global-array
 * @return array
 * @author sl
 */
function get_arr_time_evt($in,$out,$user = -1,$customer = -1,$project = -1) {
    global $kga;
    global $pdo_conn;
    
    $whereClauses = array();
    
    if ($user > -1) {
      $whereClauses[] = "zef_usrID = $user";
    }
    
    if ($customer > -1) {
      $whereClauses[] = "knd_ID = $customer";
    }
    
    if ($project > -1) {
      $whereClauses[] = "pct_ID = $project";
    }  

    if ($in)
      $whereClauses[]="zef_in > $in";
    if ($out)
      $whereClauses[]="zef_out < $out";
    $arr = array();    
    $pdo_query = $pdo_conn->prepare("SELECT sum(zef_time) as zeit,zef_evtID FROM " . $kga['server_prefix'] . "zef 
        Left Join " . $kga['server_prefix'] . "pct ON zef_pctID = pct_ID
        Left Join " . $kga['server_prefix'] . "knd ON pct_kndID = knd_ID ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
        " GROUP BY zef_evtID;");
    $pdo_query->execute();
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if (empty($row['zef_evtID'])) break;
        $arr[$row['zef_evtID']] = $row['zeit'];
    }
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

## Load into Array: Events with attached time-sums
function get_arr_evt_with_time($group,$user,$in,$out) {
    global $kga, $pdo_conn; 
    
    $arr_evts = get_arr_evt($group);
    $arr_time = get_arr_time_evt($user,$in,$out);
    
    $arr = array(); 
    
    $i=0;
    foreach ($arr_evts as $evt) {
        $arr[$i]['evt_ID']      = $evt['evt_ID'];
        $arr[$i]['evt_name']    = $evt['evt_name'];
        $arr[$i]['evt_visible'] = $evt['evt_visible'];
        if (isset($arr_time[$evt['evt_ID']])) $arr[$i]['zeit'] = intervallApos($arr_time[$evt['evt_ID']]);
        else $arr[$i]['zeit']   = intervallApos(0);
        $i++;
    }
    
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

## Load into Array: Customers with attached time-sums
function get_arr_knd_with_time($group,$user,$in,$out) {
    global $kga, $pdo_conn; 
    
    $arr_knds = get_arr_knd($group);
    $arr_time = get_arr_time_knd($user,$in,$out);
    
    $arr = array(); 
    
    $i=0;
    foreach ($arr_knds as $knd) {
        $arr[$i]['knd_ID']      = $knd['knd_ID'];
        $arr[$i]['knd_name']    = $knd['knd_name'];
        $arr[$i]['knd_visible'] = $knd['knd_visible'];
        if (isset($arr_time[$knd['knd_ID']])) $arr[$i]['zeit'] = intervallApos($arr_time[$knd['knd_ID']]);
        else $arr[$i]['zeit']   = intervallApos(0);
        $i++;
    }
    
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns time of currently running event recording as array
 *
 * result is meant as params for the stopwatch if the window is reloaded
 *
 * <pre>
 * returns:
 * [all] start time of entry in unix seconds (forgot why I named it this way, sorry ...)
 * [hour]
 * [min]
 * [sec]
 * </pre>
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return array
 * @author th
 */
function get_current_timer() {
    global $kga, $pdo_conn;
        
    $pdo_query = $pdo_conn->prepare("SELECT zef_ID,zef_in,zef_time FROM " . $kga['server_prefix'] . "zef WHERE zef_usrID = ? ORDER BY zef_in DESC LIMIT 1;");
    $pdo_query->execute(array($kga['usr']['usr_ID']));
    $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
    $zef_time  = (int)$row['zef_time'];
    $zef_in    = (int)$row['zef_in'];

    if (!$zef_time && $zef_in) {
        $aktuelleMessung = hourminsec(time()-$zef_in);
        $current_timer['all']  = $zef_in;
        $current_timer['hour'] = $aktuelleMessung['h'];
        $current_timer['min']  = $aktuelleMessung['i'];
        $current_timer['sec']  = $aktuelleMessung['s'];
    } else {
        $current_timer['all']  = 0;
        $current_timer['hour'] = 0;
        $current_timer['min']  = 0;
        $current_timer['sec']  = 0;
    }
    return $current_timer;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns the total worktime of a zef_entry day
 *
 * WARNING: $inPoint has to be *exactly* the first second of the day 
 *
 * @param integer $inPoint begin of the day in unix seconds
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return string
 * @author th 
 */
function get_zef_time_day($inPoint,$user) {
    global $kga, $pdo_conn; 
       
    $outPoint=$inPoint+86399;
    $pdo_query = $pdo_conn->prepare("SELECT sum(zef_time) as zeit FROM " . $kga['server_prefix'] . "zef WHERE zef_in > ? AND zef_out < ? AND zef_usrID = ?;");
    $pdo_query->execute(array($inPoint,$outPoint,$user));
    $row   = $pdo_query->fetch(PDO::FETCH_ASSOC);
    return $row['zeit'];
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns the total worktime of a zef_entry month
 *
 * WARNING: $inPoint has to be *exactly* the first second of any day in the wanted month 
 *
 * @param integer $inPoint begin of one day of desired month in unix seconds
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return string
 * @author th 
 */
function get_zef_time_mon($inPoint,$user) {
    global $kga, $pdo_conn; 
    
    $inDatum_m = date("m",$inPoint);
    $inDatum_Y = date("Y",$inPoint);
    $inDatum_t = date("t",$inPoint);
    
    $inPoint  = mktime(0,0,0,$inDatum_m,1,$inDatum_Y);
    $outPoint = mktime(23,59,59,$inDatum_m,$inDatum_t,$inDatum_Y);

    $pdo_query = $pdo_conn->prepare("SELECT sum(zef_time) as zeit FROM " . $kga['server_prefix'] . "zef WHERE zef_in > ? AND zef_out < ? AND zef_usrID = ?;");
    $pdo_query->execute(array($inPoint,$outPoint,$user));
    $row   = $pdo_query->fetch(PDO::FETCH_ASSOC);
    return $row['zeit'];
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns the total worktime in database
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return string
 * @author th 
 */
function get_zef_time_all($user) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT sum(zef_time) as zeit FROM " . $kga['server_prefix'] . "zef WHERE zef_usrID = ?");
    $pdo_query->execute(array($user));
    $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
    return $row['zeit'];
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns the total worktime of a zef_entry year
 *
 * @param integer $year 4 digit year (not sure yet if 2 digits work...)
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return string
 * @author th 
 */
function get_zef_time_year($year,$user) {
    global $kga, $pdo_conn; 
    $in  = (int)mktime(0,0,0,1,1,$year); 
    $out = (int)mktime(23,59,59,12,(int)date("t"),$year);
    $pdo_query = $pdo_conn->prepare("SELECT sum(zef_time) as zeit FROM " . $kga['server_prefix'] . "zef WHERE zef_in > ? AND zef_out < ? AND zef_usrID = ?");
    $pdo_query->execute(array($in,$out,$user));
    $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
    return $row['zeit'];
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns the version of the installed Kimai database (not of the software)
 *
 * @param string $path path to admin dir relative to the document that calls this function (usually "." or "..")
 * @global array $kga kimai-global-array
 * @return array
 * @author th
 *
 * [0] => version number (x.x.x)
 * [1] => svn revision number
 *
 */
function get_DBversion() {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT value FROM " . $kga['server_prefix'] . "var WHERE var = 'version';");
    $pdo_query->execute(array());
    $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
    $return[0]   = $row['value'];
    
    if (!is_array($row)) $return[0] = "0.5.1";
    
    $pdo_query = $pdo_conn->prepare("SELECT value FROM " . $kga['server_prefix'] . "var WHERE var = 'revision';");
    $pdo_query->execute(array());
    $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
    $return[1]   = $row['value'];
    
    return $return;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns the key for the session of a specific user 
 *
 * the key is both stored in the database (usr table) and a cookie on the client. 
 * when the keys match the user is allowed to access the Kimai GUI. 
 * match test is performed via function userCheck()
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return string
 * @author th 
 */
function get_seq($user) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT secure FROM " . $kga['server_prefix'] . "usr WHERE usr_name = ?;");
    $pdo_query->execute(array($user));
    $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
    $seq         = $row['secure'];
    
    return $seq;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns array of all users 
 *
 * [usr_ID] => 23103741
 * [usr_name] => admin
 * [usr_grp] => 1
 * [usr_sts] => 0
 * [grp_name] => miesepriem
 * [usr_mail] => 0
 * [usr_active] => 0
 *
 *
 * @global array $kga kimai-global-array
 * @return array
 * @author th 
 */
function get_arr_usr($trash=0) {
    global $kga, $pdo_conn;  
      
    $arr = array();
    
    if (!$trash) {
        $trashoption = "WHERE usr_trash !=1";
    }
    $pdo_query = $pdo_conn->prepare(sprintf("SELECT * FROM " . $kga['server_prefix'] . "usr Left Join " . $kga['server_prefix'] . "grp ON usr_grp = grp_ID %s ORDER BY usr_name;",$trashoption));
    $result = $pdo_query->execute();
    
    $i=0;
    while ($row = $pdo_query->fetch()) {
        $arr[$i]['usr_ID']   = $row['usr_ID'];
        $arr[$i]['usr_name'] = $row['usr_name'];
        $arr[$i]['usr_grp']  = $row['usr_grp'];
        $arr[$i]['usr_sts']  = $row['usr_sts'];
        $arr[$i]['grp_name'] = $row['grp_name'];
        $arr[$i]['usr_mail'] = $row['usr_mail'];
        $arr[$i]['usr_active'] = $row['usr_active'];
        $arr[$i]['usr_trash'] = $row['usr_trash'];
        if ($row['pw']!=''&&$row['pw']!='0') {
            $arr[$i]['usr_pw'] = "yes"; 
        } else {                 
            $arr[$i]['usr_pw'] = "no"; 
        }
        $i++;
    }
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns array of all groups 
 *
 * [0]=>  array(6) {
 *     ["grp_ID"]=>  string(1) "1" 
 *      ["grp_name"]=>  string(5) "admin" 
 *      ["grp_leader"]=>  string(9) "1234" 
 *      ["grp_trash"]=>  string(1) "0" 
 *      ["count_users"]=>  string(1) "2" 
 *      ["leader_name"]=>  string(5) "user1" 
 * } 
 * 
 * [1]=>  array(6) { 
 *      ["grp_ID"]=>  string(1) "2" 
 *      ["grp_name"]=>  string(4) "Test" 
 *      ["grp_leader"]=>  string(9) "12345" 
 *      ["grp_trash"]=>  string(1) "0" 
 *      ["count_users"]=>  string(1) "1" 
 *      ["leader_name"]=>  string(7) "user2" 
 *  } 
 *
 * @global array $kga kimai-global-array
 * @return array
 * @author th 
 *
 */
function get_arr_grp($trash=0) {
    global $kga, $pdo_conn;
    
    // Lock tables
    $pdo_query_l = $pdo_conn->prepare("LOCK TABLE 
    " . $kga['server_prefix'] . "usr, 
    " . $kga['server_prefix'] . "grp     
    ");
    $result_l = $pdo_query_l->execute();
    
    if (!$trash) {
        $trashoption = "WHERE grp_trash !=1";
    }
    $pdo_query = $pdo_conn->prepare(sprintf("SELECT * FROM " . $kga['server_prefix'] . "grp %s ORDER BY grp_name;",$trashoption));
    $result = $pdo_query->execute();
    
    // rows into array
    $groups = array();
    $i=0;
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)){
        $groups[] = $row;
        
        // append user count
    	$groups[$i]['count_users'] = grp_count_users($row['grp_ID']); 
        
        // append leader array
        $ldr_id_array = grp_get_ldrs($row['grp_ID']);
        $j = 0;
        $ldr_name_array = array();
        foreach ($ldr_id_array as $ldr_id) {
        	$ldr_name_array[$j] = usr_id2name($ldr_id);
        	$j++;
        }
        
        $groups[$i]['leader_name'] = $ldr_name_array;
        
        $i++;
    }
    
    // Unlock tables
    $pdo_query_ul = $pdo_conn->prepare("UNLOCK TABLE 
    " . $kga['server_prefix'] . "usr, 
    " . $kga['server_prefix'] . "grp     
    ");
    $result_ul = $pdo_query_ul->execute();
    
    // error_log("get_arr_grp: " . serialize($groups));
    
    return $groups;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * performed when the stop buzzer is hit.
 * Checks which record is currently recording and
 * writes the end time into that entry.
 * if the measured timevalue is longer than one calendar day
 * it is split up and stored in the DB by days
 *
 * @global array $kga kimai-global-array
 * @param integer $user ID of user
 * @author th 
 *
 */
function stopRecorder() {
## stop running recording
    global $kga, $pdo_conn;
    
    $last_task = get_event_last($kga['usr']['usr_ID']);      // aktuelle vorgangs-ID auslesen
    
    $zef_ID = $last_task['zef_ID'];
    $zef_in = $last_task['zef_in'];

    // ...in-zeitpunkt und jetzt-zeitpunkt werden an den EXPLODER gesendet
    // der daraus ein mehrdimensionales array macht. die tage dieses arrays
    // werden anschließend in die DB zurückgeschreiben
    $records = explode_record($zef_in,$kga['now']);

    $difference = $records[0]['diff'];

    // hier wird sofort mal der erste ausgeworfene tag verarbeitet.
    // wenn nur einer zurückgekommen ist, ist die verarbeitung danach direkt
    // beendet.
    // update zeitdifferenz in laufendem vorgang speichern
    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "zef SET zef_time = ? WHERE zef_ID = ?;");
    $pdo_query->execute(array($difference,$zef_ID));

    // update outPoint in laufendem vorgang speichern
    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "zef SET zef_out = ? WHERE zef_ID = ?;");
    $pdo_query->execute(array($records[0]['out'],$zef_ID));

    // noch mehr tage?
    if (count($records)>1) {
        save_further_records($user,$last_task,$records);
    }
}

// -----------------------------------------------------------------------------------------------------------

function save_further_records($user,$last_task,$records) {
    global $kga, $pdo_conn;

    // nur der zweite eintrag wird zusätzlich gespeichert
    // TODO: schleife für alle einträge

    $pctID = $last_task['zef_pctID'];
    $evtID = $last_task['zef_evtID'];

    if (count($records)>2) {
        $type = 2;
        $comment=$kga['lang']['ac_error']; // auto continued with error (entry too long).";
    } else {
        $type = 1;
        $comment=$kga['lang']['ac']; // "auto continued.";
    }

	$pdo_query = $pdo_conn->prepare("INSERT INTO " . $kga['server_prefix'] . "zef 
	(`zef_in`, `zef_out`, `zef_time`,`zef_usrID`,`zef_pctID`,`zef_evtID`,`zef_comment`,`zef_comment_type`)
    VALUES (?,?,?,?,?,?,?,?);");

    $pdo_query->execute(array($records[1]['in'],$records[1]['out'],$records[1]['diff'],$user,$pctID,$evtID,$comment,$type));
}

// -----------------------------------------------------------------------------------------------------------

/**
 * starts timesheet record
 *
 * @param integer $pct_ID ID of project to record
 * @global array $kga kimai-global-array
 * @author th
 */
function startRecorder($pct_ID,$evt_ID,$user) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("INSERT INTO " . $kga['server_prefix'] . "zef 
    (zef_pctID,zef_evtID,zef_in,zef_usrID) VALUES 
    (?, ?, ?, ?);");
    $pdo_query->execute(array($pct_ID,$evt_ID,$kga['now'],$user));
    
    $pdo_query = $pdo_conn->prepare("UPDATE " . $kga['server_prefix'] . "usr SET lastRecord = LAST_INSERT_ID() WHERE usr_ID = ?;");
    $pdo_query->execute(array($user));
}

// -----------------------------------------------------------------------------------------------------------

/**
 * return details of specific user
 *
 * <pre>
 * returns: 
 * ...
 * </pre>
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return array
 * @author th 
 */
function get_usr($id) {
    global $kga, $pdo_conn;
        
    $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "usr Left Join " . $kga['server_prefix'] . "grp ON usr_grp = grp_ID WHERE usr_ID = ? LIMIT 1;");
    $pdo_query->execute(array($id));
    
    $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
        $arr['usr_ID']    = $row['usr_ID'];
        $arr['usr_name']  = $row['usr_name'];
        $arr['usr_alias'] = $row['usr_alias'];
        $arr['usr_grp']   = $row['usr_grp'];
        $arr['usr_sts']   = $row['usr_sts'];
        $arr['grp_name']  = $row['grp_name'];
        $arr['usr_mail']  = $row['usr_mail'];
        $arr['usr_active'] = $row['usr_active'];
        
        if ($row['pw']!=''&&$row['pw']!='0') {
            $arr['usr_pw'] = "yes"; 
        } else {                 
            $arr['usr_pw'] = "no"; 
        }
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * return ID of specific user named 'XXX'
 *
 * @param integer $name name of user in table usr
 * @global array $kga kimai-global-array
 * @return string
 * @author th
 */
function usr_name2id($name) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT usr_ID FROM " . $kga['server_prefix'] . "usr WHERE usr_name = ? LIMIT 1;");
    $pdo_query->execute(array($name));
    
    $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
    return $row['usr_ID'];
}

// -----------------------------------------------------------------------------------------------------------

/**
 * return name of a user with specific ID
 *
 * @param string $id the user's usr_ID
 * @global array $kga kimai-global-array
 * @return int
 * @author ob
 */
function usr_id2name($id) {
    global $kga, $pdo_conn;
    
    $pdo_query = $pdo_conn->prepare("SELECT usr_name FROM " . $kga['server_prefix'] . "usr WHERE usr_ID = ? LIMIT 1;");
    $pdo_query->execute(array($id));
    
    $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
    return $row['usr_name'];
}


/**
 * returns data of the group with ID X
 * DEPRECATED!
 */
function get_grp($id) {
    return grp_get_data($id);
}

// -----------------------------------------------------------------------------------------------------------

/**
 * get in and out unix seconds of specific user
 *
 * <pre>
 * returns:
 * [0] -> in
 * [1] -> out
 * </pre>
 *
 * @param string $user ID of user
 * @global array $kga kimai-global-array
 * @return array
 * @author th
 */
function get_timespace() {
    global $kga,$pdo_conn;
    
    if (isset($kga['usr']['usr_ID'])) {
        $pdo_query = $pdo_conn->prepare("SELECT timespace_in, timespace_out FROM " . $kga['server_prefix'] . "usr WHERE usr_ID = ?;");
        $pdo_query->execute(array($kga['usr']['usr_ID']));
    
        $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
        //die ($query);
        $timespace[0] = $row['timespace_in'];
        $timespace[1] = $row['timespace_out'];

        /* database has no entries? */
        $mon = date("n"); $day = date("j"); $Y = date("Y");
        if (!$timespace[0]) {
            $timespace[0] = mktime(0,0,0,$mon,1,$Y);
        }
        if (!$timespace[1]) {
            $timespace[1] = mktime(23,59,59,$mon,lastday($month=$mon,$year=$Y),$Y);
        }
    
        return $timespace;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * lookup if an item (knd pct evt) is referenced in timesheet table
 * returns number of entities
 *
 * @param integer $id of item
 * @param string $subject of item
 * @return integer
 *
 * @author th
 */
function getUsage($id,$subject) {
    global $kga, $pdo_conn;
    
    switch ($subject) {
        case "pct":
        case "evt":
            $pdo_query = $pdo_conn->prepare("SELECT COUNT(*) AS result FROM " . $kga['server_prefix'] . "zef WHERE zef_" . $subject . "ID = ?;");
            $pdo_query->execute(array($id));
        break;

        case "knd":
            $pdo_query = $pdo_conn->prepare("SELECT COUNT(*) AS result FROM " . $kga['server_prefix'] . "pct Left Join " . $kga['server_prefix'] . "knd ON pct_kndID = knd_ID WHERE pct_kndID = ?;");
            $pdo_query->execute(array($id));
        break;
            
        default:
        break;
    }
    $row   = $pdo_query->fetch(PDO::FETCH_ASSOC);
    return $row['result'];
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns the date of the first timerecord of a user (when did the user join?)
 * this is needed for the datepicker
 * @param integer $id of user
 * @return integer unix seconds of first timesheet record
 * @author th
 */
function getjointime($usr_id) {
    global $kga, $pdo_conn;

    $query = "SELECT zef_in FROM " . $kga['server_prefix'] . "zef" . " WHERE zef_usrID = ? ORDER BY zef_in ASC LIMIT 1;";
    $pdo_query = $pdo_conn->prepare($query);
    $pdo_query->execute(array($usr_id));
    $result_array = $pdo_query->fetch();
        
    if ($result_array[0] == 0) {
        return mktime(0,0,0,date("n"),date("j"),date("Y"));        
    } else {
        return $result_array[0];
    }
}

// -----------------------------------------------------------------------------------------------------------
// TODO


// FOR TS FILTER 
// WORKS AND READY TO USE...
/**
 * returns a multidimensional array in string format for customer-project-relationships
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return String
 * @author ob 
 */

/*
function knd_pct_arr() {

    global $kga, $pdo_conn;
    
    $usr = checkUser();
    get_config($usr['usr_ID']);
    
    // Lock tables
    $pdo_query_l = $pdo_conn->prepare("LOCK TABLE 
    " . $kga['server_prefix'] . "knd, 
    " . $kga['server_prefix'] . "pct, 
    " . $kga['server_prefix'] . "zef    
    ");
    $result_l = $pdo_query_l->execute();
    

    $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "knd");
    $result = $pdo_query->execute();
    
    $knds = array();
    
    // build initial knd array
    while ($row  = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        array_push($knds, $row['knd_ID']);
    }
    
    $list = array();
    
    // fill the array with pcts
    foreach ($knds as $current_knd) {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "pct WHERE pct_kndID = ?");
        $result = $pdo_query->execute(array($current_knd));
        
        $pdo_query_count = $pdo_conn->prepare("SELECT COUNT(*) FROM " . $kga['server_prefix'] . "pct WHERE pct_kndID = ?");
        $result_count = $pdo_query_count->execute(array($current_knd));
        $result_array_count = $pdo_query_count->fetch();
        
        $list[$current_knd] = array();
                
        // insert last project
        $pdo_query_pre = $pdo_conn->prepare("SELECT MAX(`zef_ID`) FROM " . $kga['server_prefix'] . "zef JOIN " . $kga['server_prefix'] . "pct
         ON " . $kga['server_prefix'] . "zef.zef_pctID = " . $kga['server_prefix'] . "pct.pct_ID 
         WHERE zef_usrID = ?
         AND pct_kndID = ?");
        $result_pre = $pdo_query_pre->execute(array($kga['usr']['usr_ID'], $current_knd));
        $result_pre_array = $pdo_query_pre->fetch();
        
        $pdo_query2 = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "zef WHERE zef_ID = ?");
        $result2 = $pdo_query2->execute(array($result_pre_array[0]));
        
        $pdo_query_count2 = $pdo_conn->prepare("SELECT COUNT(*) FROM " . $kga['server_prefix'] . "zef WHERE zef_ID = ?");
        $result_count2 = $pdo_query_count2->execute(array($result_pre_array[0]));
        $result_array_count2 = $pdo_query_count2->fetch();
        
        $result_array2 = $pdo_query2->fetch(PDO::FETCH_ASSOC);
        
        // error_log("COUNT: " . $result_array_count[0]);
        
        if ($result_array_count[0] != 0) {
        
            if ($result_array_count2[0] != 0) {
                // if there is a last accessed project by the user:
                $list[$current_knd][0] = $result_array2['zef_pctID'];
            } else {
                // if there are projects associated with this customer, but none accessed by current user:
                
                $pdo_query_default = $pdo_conn->prepare("SELECT * FROM " . $kga['server_prefix'] . "pct WHERE pct_kndID = ? ORDER BY pct_name");
                $result_default = $pdo_query_default->execute(array($current_knd));
                $result_array_default = $pdo_query_default->fetch();
        
                $list[$current_knd][0] = 0;
            }
        
        } else {
            // if the customer has no projects at all:
            $list[$current_knd][0] = 0;
        }
        
        // $list[$current_knd][0] = "foo";
        
        while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
            array_push($list[$current_knd], $row['pct_ID']);
        }
    }
    
    // string format for array
    $s_list = '[],';
    foreach ($knds as $current_knd) {
        $s_list .= '[';
        
        $i = 0;        
        foreach ($list[$current_knd] as $current_pct) {
            $s_list .= $current_pct;
            
            if ($i < count($list[$current_knd]) - 1) { 
                $s_list .= ',';
            }
            
            $i++;
        }
        
        $s_list .= '],';
    }
    
    $s_list = substr($s_list, 0, -1);
    
    
    // Unlock tables
    $pdo_query_ul = $pdo_conn->prepare("UNLOCK TABLE 
    " . $kga['server_prefix'] . "knd, 
    " . $kga['server_prefix'] . "pct, 
    " . $kga['server_prefix'] . "zef    
    ");
    $result_ul = $pdo_query_ul->execute();
    
    return $s_list;

}
*/


// OBSOLETE .....
/** ----------------- */
/** ---- ALPHA ---- */
/** ------------- */

// function get_arr_first_pct_of_knd() {
//     global $kga;
//     global $pdo_conn;
//     $arr = array();
//     
//     $pdo_query = $pdo_conn->prepare("SELECT knd_ID, pct_ID, pct_name FROM " . $kga['server_prefix'] . "knd JOIN " . $kga['server_prefix'] . "pct ON knd_ID = pct_kndID ORDER BY knd_ID, pct_name;");
//    	$pdo_query->execute(array());
// 
//     $ruwen = 1;
//     $flag  = 0;
//     $i=0;
//     while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
//         if ($ruwen == $row['pct_ID'] && $flag != $row['pct_ID']) {
//             $arr[$i]['knd_ID']      = $row['knd_ID'];
//             $arr[$i]['pct_ID']      = $row['pct_ID'];
//             $arr[$i]['pct_name']    = $row['pct_name'];
//             $i++;
//             $flag=$row['pct_ID'];
//         } else {
//             $ruwen=$row['pct_ID'];
//         }
//     }
//     return $arr;
// }
/** -------- */
/** -------- */
/** -------- */


?>