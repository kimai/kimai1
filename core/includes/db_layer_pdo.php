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

/**
 * This file contains all functions which access the database directly.
 */



// -----------------------------------------------------------------------------------------------------------

/**
 * Create the set part of an SQL update query depending on which keys are possible
 * and which are available from the data. Only if a key is possible and data is
 * available for that key (i.e. a value is set for that key in the data array)
 * it will be included.
 * 
 * @param array $keys list of keys which are possible
 * @param array $data array containing data, keys are looked at.
 * @return string the set part of the sql query
 * @author sl
 */
function buildSQLUpdateSet(&$keys,&$data) {
    $firstRun = true;
    $query = '';

    foreach ($keys as $key) {
      if (!isset($data[$key]))
        continue;

      if ($firstRun)
        $firstRun = false;
      else
        $query .= ', ';

      $query .= "$key = :$key";
      
    }
    return $query;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Bind all values from the data array to the sql query.
 * If the data array contains keys which are not present in the query you will get
 * an error when executing the statement.
 * 
 * @param PDOStatement PDO statement object
 * @param array &$data array containing all data to set
 * @return true on success, false otherwise
 */
function bindValues(&$statement,$keys,&$data) {
    foreach ($keys as $key) {
      if (!isset($data[$key]))
        continue;

      $value = $data[$key];

      if (!$statement->bindValue(":$key",$value)) {
        logfile("failed binding ".$key." to ".$value);
        return false;
      }
    }
    return true;
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
    knd_password, 
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
    knd_vat,
    knd_contact
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
    
    $result = $pdo_query->execute(
    array(
    $data['knd_name'], 
    $data['knd_comment'], 
    $data['knd_password'], 
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
    $data['knd_vat'],
    $data['knd_contact']
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}knd WHERE knd_ID = ?");
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

    $keys = array(
      'knd_name'    ,'knd_comment','knd_password' ,'knd_company','knd_vat',
      'knd_contact' ,'knd_street' ,'knd_zipcode'  ,'knd_city'   ,'knd_tel',
      'knd_fax'     ,'knd_mobile' ,'knd_mail'     ,'knd_homepage',
      'knd_visible','knd_filter');

    $query = 'UPDATE ' . $kga['server_prefix'] . 'knd SET ';
    $query .= buildSQLUpdateSet($keys,$data);
    $query .= ' WHERE knd_id = :customerId;';

    $statement = $pdo_conn->prepare($query);

    bindValues($statement,$keys,$data);

    $statement->bindValue(":customerId", $knd_id);

    return $statement->execute();
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
    $p = $kga['server_prefix'];

    
    $pdo_conn->beginTransaction();
    
    $pdo_query = $pdo_conn->prepare("DELETE FROM ${p}grp_knd WHERE knd_ID=?;");    
    $d_result = $pdo_query->execute(array($knd_id));
    if ($d_result == false) {
        $pdo_conn->rollBack();
        return false;
    }
    
    foreach ($grp_array as $current_grp) {
      $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}grp_knd (grp_ID,knd_ID) VALUES (?,?);");
      $result = $pdo_query->execute(array($current_grp,$knd_id));
      if ($result == false) {
          $pdo_conn->rollBack();
          return false;
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT grp_ID FROM ${p}grp_knd WHERE knd_ID = ?;");
    
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("UPDATE ${p}knd SET knd_trash=1 WHERE knd_ID = ?;");
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
    pct_internal,
    pct_filter,
    pct_budget
    ) VALUES (?, ?, ?, ?, ?, ?, ?);");

    $result = $pdo_query->execute(array(
    (int)$data['pct_kndID'], 
    $data['pct_name'],
    $data['pct_comment'],
    (int)$data['pct_visible'],
    (int)$data['pct_internal'],
    (int)$data['pct_filter'],
    doubleval($data['pct_budget'])
    ));
    
    if ($result == true) {
    
      $pct_id = $pdo_conn->lastInsertId();

      if (isset($data['pct_default_rate'])) {
        if (is_numeric($data['pct_default_rate']))
          save_rate(NULL,$pct_id,NULL,$data['pct_default_rate']);
        else
          remove_rate(NULL,$pct_id,NULL);
      }

      if (isset($data['pct_my_rate'])) {
        if (is_numeric($data['pct_my_rate']))
          save_rate($kga['usr']['usr_ID'],$pct_id,NULL,$data['pct_my_rate']);
        else
          remove_rate($kga['usr']['usr_ID'],$pct_id,NULL);
      }
        
        return $pct_id;
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
    $p = $kga['server_prefix'];

    if (!is_numeric($pct_id)) {
        return false;
    }

    $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}pct WHERE pct_ID = ?");
    $result = $pdo_query->execute(array($pct_id));
    
    if ($result == false) {
        return false;
    }

    $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);
 
    $result_array['pct_default_rate'] = get_rate(NULL,$pct_id,NULL);
    $result_array['pct_my_rate'] = get_rate($kga['usr']['usr_ID'],$pct_id,NULL);
    return $result_array;
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
    
    if (isset($data['pct_default_rate'])) {
      if (is_numeric($data['pct_default_rate']))
        save_rate(NULL,$pct_id,NULL,$data['pct_default_rate']);
      else
        remove_rate(NULL,$pct_id,NULL);
      unset($data['pct_default_rate']);
    }

    if (isset($data['pct_my_rate'])) {
      if (is_numeric($data['pct_my_rate']))
        save_rate($kga['usr']['usr_ID'],$pct_id,NULL,$data['pct_my_rate']);
      else
        remove_rate($kga['usr']['usr_ID'],$pct_id,NULL);
      unset($data['pct_my_rate']);
    }

    $keys = array(
      'pct_kndID', 'pct_name', 'pct_comment', 'pct_visible', 'pct_internal',
      'pct_filter', 'pct_budget');

    $query = 'UPDATE ' . $kga['server_prefix'] . 'pct SET ';
    $query .= buildSQLUpdateSet($keys,$data);
    $query .= ' WHERE pct_id = :projectId;';

    $statement = $pdo_conn->prepare($query);

    bindValues($statement,$keys,$data);

    $statement->bindValue(":projectId", $pct_id);

    if ($statement->execute() === false) {
      $pdo_conn->rollBack();
      return false;
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
    $p = $kga['server_prefix'];
    
    $pdo_conn->beginTransaction();
    
    $pdo_query = $pdo_conn->prepare("DELETE FROM ${p}grp_pct WHERE pct_ID=?;");    
    $d_result = $pdo_query->execute(array($pct_id));
    if ($d_result == false) {
        $pdo_conn->rollBack();
        return false;
    }
    
    foreach ($grp_array as $current_grp) {
      $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}grp_pct (grp_ID,pct_ID) VALUES (?,?);");
      $result = $pdo_query->execute(array($current_grp,$pct_id));
      if ($result == false) {
          $pdo_conn->rollBack();
          return false;
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT grp_ID FROM ${p}grp_pct WHERE pct_ID = ?;");
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("UPDATE ${p}pct SET pct_trash=1 WHERE pct_ID = ?;");
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
    evt_filter
    ) VALUES (?, ?, ?, ?);");
    
    $result = $pdo_query->execute(array(
    $data['evt_name'],
    $data['evt_comment'],
    $data['evt_visible'],
    $data['evt_filter']
    ));
    
    if ($result == true) {
    
      $evt_id = $pdo_conn->lastInsertId();
    
      
      if (isset($data['evt_default_rate'])) {
        if (is_numeric($data['evt_default_rate']))
          save_rate(NULL,NULL,$evt_id,$data['evt_default_rate']);
        else
          remove_rate(NULL,NULL,$evt_id);
      }

      if (isset($data['evt_my_rate'])) {
        if (is_numeric($data['evt_my_rate']))
          save_rate($kga['usr']['usr_ID'],NULL,$evt_id,$data['evt_my_rate']);
        else
          remove_rate($kga['usr']['usr_ID'],NULL,$evt_id);
      }

      return $evt_id;
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
    $p = $kga['server_prefix'];

    $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}evt WHERE evt_ID = ?");
    $result = $pdo_query->execute(array($evt_id));
    
    if ($result == false) {
        return false;
    }

    $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);

    $result_array['evt_default_rate'] = get_rate(NULL,NULL,$result_array['evt_ID']);
    $result_array['evt_my_rate'] = get_rate($kga['usr']['usr_ID'],NULL,$result_array['evt_ID']);

    return $result_array;
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

    if (isset($data['evt_default_rate'])) {
      if (is_numeric($data['evt_default_rate']))
        save_rate(NULL,NULL,$evt_id,$data['evt_default_rate']);
      else
        remove_rate(NULL,NULL,$evt_id);
      unset($data['evt_default_rate']);
    }

    if (isset($data['evt_my_rate'])) {
      if (is_numeric($data['evt_my_rate']))
        save_rate($kga['usr']['usr_ID'],NULL,$evt_id,$data['evt_my_rate']);
      else
        remove_rate($kga['usr']['usr_ID'],NULL,$evt_id);
      unset($data['evt_my_rate']);
    }

    $keys = array('evt_name', 'evt_comment', 'evt_visible', 'evt_filter');

    $query = 'UPDATE ' . $kga['server_prefix'] . 'evt SET ';
    $query .= buildSQLUpdateSet($keys,$data);
    $query .= ' WHERE evt_id = :eventId;';

    $statement = $pdo_conn->prepare($query);

    bindValues($statement,$keys,$data);

    $statement->bindValue(":eventId", $evt_id);

    if (!$statement->execute())
      return false;
    
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
    $p = $kga['server_prefix'];
    
    $pdo_conn->beginTransaction();
    
    $pdo_query = $pdo_conn->prepare("DELETE FROM ${p}grp_evt WHERE evt_ID=?;");    
    $d_result = $pdo_query->execute(array($evt_id));
    if ($d_result == false) {
        $pdo_conn->rollBack();
        return false;
    }
    
    foreach ($grp_array as $current_grp) {
      $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}grp_evt (grp_ID,evt_ID) VALUES (?,?);");
      $result = $pdo_query->execute(array($current_grp,$evt_id));
      if ($result == false) {
          $pdo_conn->rollBack();
          return false;
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
 * Assigns an event to 1-n projects by adding entries to the cross table
 *
 * @param int $evt_id         id of the event to which projects will be assigned
 * @param array $gpct_array    contains one or more pct_IDs
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob/th
 */

function assign_evt2pcts($evt_id, $pct_array) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];
    
    $pdo_conn->beginTransaction();
    
    $pdo_query = $pdo_conn->prepare("DELETE FROM ${p}pct_evt WHERE evt_ID=?;");    
    $d_result = $pdo_query->execute(array($evt_id));
    if ($d_result == false) {
        $pdo_conn->rollBack();
        return false;
    }
    
    foreach ($pct_array as $current_pct) {
      $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}pct_evt (pct_ID,evt_ID) VALUES (?,?);");
      $result = $pdo_query->execute(array($current_pct,$evt_id));
      if ($result == false) {
          $pdo_conn->rollBack();
          return false;
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
 * Assigns 1-n events to a project by adding entries to the cross table
 *
 * @param int $pct_id         id of the project to which events will be assigned
 * @param array $evt_array    contains one or more evt_IDs
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author sl
 */

function assign_pct2evts($pct_id, $evt_array) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];
    
    $pdo_conn->beginTransaction();
    
    $pdo_query = $pdo_conn->prepare("DELETE FROM ${p}pct_evt WHERE pct_ID=?;");    
    $d_result = $pdo_query->execute(array($pct_id));
    if ($d_result == false) {
        $pdo_conn->rollBack();
        return false;
    }
    
    foreach ($evt_array as $current_evt) {
      $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}pct_evt (evt_ID,pct_ID) VALUES (?,?);");
      $result = $pdo_query->execute(array($current_evt,$pct_id));
      if ($result == false) {
          $pdo_conn->rollBack();
          return false;
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
 * returns all the projects to which the event was assigned
 *
 * @param array $evt_id  evt_id of the project
 * @global array $kga    kimai-global-array
 * @return array         contains the pct_IDs of the projects or false on error
 * @author th
 */
 
// checked 
 
function evt_get_pcts($evt_id) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT pct_ID FROM ${p}pct_evt WHERE evt_ID = ?;");
    
    $result = $pdo_query->execute(array($evt_id));
    if ($result == false) {
        return false;
    }
    
    $return_pcts = array();
    $counter = 0;
    
    while ($current_pct = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $return_pcts[$counter] = $current_pct['pct_ID'];
        $counter++;
    }
    
    return $return_pcts;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the events which are assigned to a project
 *
 * @param integer $pct_id  pct_id of the project
 * @global array $kga    kimai-global-array
 * @return array         contains the evt_IDs of the events or false on error
 * @author sl
 */ 
 
function pct_get_evts($pct_id) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT evt_ID FROM ${p}pct_evt WHERE pct_ID = ?;");
    
    $result = $pdo_query->execute(array($pct_id));
    if ($result == false) {
        return false;
    }
    
    $return_evts = array();
    $counter = 0;
    
    while ($current_evt = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $return_evts[$counter] = $current_evt['evt_ID'];
        $counter++;
    }
    
    return $return_evts;
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT grp_ID FROM ${p}grp_evt WHERE evt_ID = ?;");
    
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("UPDATE ${p}evt SET evt_trash=1 WHERE evt_ID = ?;");
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
    $p = $kga['server_prefix'];
    
    $pdo_conn->beginTransaction();
    
    $d_query = $pdo_conn->prepare("DELETE FROM ${p}grp_knd WHERE grp_ID=?;");
    $d_result = $d_query->execute(array($grp_id));
    if ($d_result == false) {
        return false;
    }
    
    foreach ($knd_array as $current_knd) {
      $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}grp_knd (grp_ID,knd_ID) VALUES (?,?);");
      $result = $pdo_query->execute(array($grp_id,$current_knd));
      if ($result == false) {
          return false;
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
    $p = $kga['server_prefix'];
    
    $pdo_conn->beginTransaction();
    
    $d_query = $pdo_conn->prepare("DELETE FROM ${p}grp_pct WHERE grp_ID=?;");
    $d_result = $d_query->execute(array($grp_id));
    if ($d_result == false) {
        return false;
    }
    
    foreach ($pct_array as $current_pct) {
      $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}grp_pct (grp_ID,pct_ID) VALUES (?,?);");
      $result = $pdo_query->execute(array($grp_id,$current_pct));
      if ($result == false) {
          return false;
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
    $p = $kga['server_prefix'];
    
    $pdo_conn->beginTransaction();
    
    $d_query = $pdo_conn->prepare("DELETE FROM ${p}grp_evt WHERE grp_ID=?;");
    $d_result = $d_query->execute(array($grp_id));
    if ($d_result == false) {
        return false;
    }
    
    foreach ($evt_array as $current_evt) {
      $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}grp_evt (grp_ID,evt_ID) VALUES (?,?);");
      $result = $pdo_query->execute(array($grp_id,$current_evt));
      if ($result == false) {
          return false;
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT knd_ID FROM ${p}grp_knd
     JOIN ${p}knd USING (knd_ID)
     WHERE ${p}knd.knd_trash = 0 AND grp_ID = ?;");
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT pct_ID FROM ${p}grp_pct
     JOIN ${p}pct USING(pct_ID)
     WHERE ${p}evt.evt_trash=0 AND grp_ID = ?;");
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT evt_ID FROM ${p}grp_evt
     JOIN ${p}evt USING(evt_ID)
     WHERE ${p}evt.evt_trash=0 AND ${p}grp_evt.grp_ID = ?;");
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
    $p = $kga['server_prefix'];

    // find random but unused user id
    do {
      $data['usr_ID'] = random_number(9);
    } while (usr_get_data($data['usr_ID']));
    
    $data = clean_data($data);

    $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}usr (
    `usr_ID`,
    `usr_name`,
    `usr_grp`,
    `usr_sts`,
    `usr_active`
    ) VALUES (?, ?, ?, ?, ?)");
    
    $result = $pdo_query->execute(array(
    $data['usr_ID'],
    $data['usr_name'],
    $data['usr_grp'],
    $data['usr_sts'],
    $data['usr_active']
    ));
            
    if ($result == true) {
        if (isset($data['usr_rate'])) {
          if (is_numeric($data['usr_rate']))
            save_rate($usr_id,NULL,NULL,$data['usr_rate']);
          else
            remove_rate($usr_id,NULL,NULL);
        }
        return $data['usr_ID'];
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
    $p = $kga['server_prefix'];

    $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}usr WHERE usr_ID = ?");
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
 * Edits a user by replacing his data and preferences by the new array
 *
 * @param array $usr_id       usr_id of the user to be edited
 * @param array $data         username, email, and other new data of the user
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob
 */
function usr_edit($usr_id, $data) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];
    
    $data = clean_data($data);
            
    $pdo_conn->beginTransaction();

    $keys = array(
      'usr_name', 'usr_grp', 'usr_sts', 'usr_trash', 'usr_active', 'usr_mail',
      'usr_alias', 'pw', 'lastRecord', 'lastProject', 'lastEvent');

    $query = 'UPDATE ' . $kga['server_prefix'] . 'usr SET ';
    $query .= buildSQLUpdateSet($keys,$data);
    $query .= ' WHERE usr_id = :userId;';

    $statement = $pdo_conn->prepare($query);

    bindValues($statement,$keys,$data);

    $statement->bindValue(":userId", $usr_id);

    if (!$statement->execute())
      return false;

    if (isset($data['usr_rate'])) {
      if (is_numeric($data['usr_rate']))
        save_rate($usr_id,NULL,NULL,$data['usr_rate']);
      else
        remove_rate($usr_id,NULL,NULL);
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("UPDATE ${p}usr SET usr_trash=1 WHERE usr_ID = ?;");
    $result = $pdo_query->execute(array($usr_id));
    if ($result == false) {
        return false;
    }
    return $result;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Get a preference for a user. If no user ID is given the current user is used.
 * 
 * @param string  $key     name of the preference to fetch
 * @param integer $userId  (optional) id of the user to fetch the preference for
 * @return string value of the preference or null if there is no such preference
 * @author sl
 */

function usr_get_preference($key,$userId=null) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];

    if ($userId === null)
      $userId = $kga['usr']['usr_ID'];

    $pdo_query = $pdo_conn->prepare("SELECT var,value FROM ${p}preferences WHERE userID = ? AND var = ?");

    $result = $pdo_query->execute(array($userId,$key));

    $data = $pdo_query->fetch();

    if (!$data)
      return null;

    return $data['value'];
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Get several preferences for a user. If no user ID is given the current user is used.
 * 
 * @param array   $keys    names of the preference to fetch in an array
 * @param integer $userId  (optional) id of the user to fetch the preference for
 * @return array  with keys for every found preference and the found value
 * @author sl
 */

function usr_get_preferences(array $keys,$userId=null) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];

    if ($userId === null)
      $userId = $kga['usr']['usr_ID'];
    
    $placeholders = implode(",",array_fill(0,count($keys),'?'));

    $pdo_query = $pdo_conn->prepare("SELECT var,value FROM ${p}preferences WHERE userID = ? AND var IN ($placeholders)");
    $pdo_query->execute(array_merge(array($userId,$prefix),$keys));

    $preferences = array();

    while ($row = $pdo_query->fetch()) {
      $preferences[$row['var']] = $row['value'];
    }

    return $preferences;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Get several preferences for a user which have a common prefix. The returned preferences are striped off
 * the prefix.
 * If no user ID is given the current user is used.
 * 
 * @param string  $prefix   prefix all preferenc keys to fetch have in common
 * @param integer $userId  (optional) id of the user to fetch the preference for
 * @return array  with keys for every found preference and the found value
 * @author sl
 */

function usr_get_preferences_by_prefix($prefix,$userId=null) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];

    if ($userId === null)
      $userId = $kga['usr']['usr_ID'];

    $prefixLength = strlen($prefix);
    //$prefix .= '%';

    $pdo_query = $pdo_conn->prepare("SELECT var,value FROM ${p}preferences WHERE userID = ? AND var LIKE ?");
       
    $pdo_query->execute(array($userId,"$prefix%"));

    $preferences = array();

    while ($row = $pdo_query->fetch()) {
      $key = substr($row['var'],$prefixLength);
      $preferences[$key] = $row['value'];
    }

    return $preferences;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Save one or more preferences for a user. If no user ID is given the current user is used.
 * The array has to assign every preference key a value to store.
 * Example: array ( 'setting1' => 'value1', 'setting2' => 'value2');
 * 
 * A prefix can be specified, which will be prepended to every preference key.
 *
 * @param array   $data   key/value pairs to store
 * @param string  $prefix prefix for all preferences
 * @param integer $userId (optional) id of another user than the current 
 * @global array $kga     kimai-global-array
 * @return boolean        true on success, false on failure
 * @author sl
 */

function usr_set_preferences(array $data,$prefix='',$userId=null) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];

    if ($userId === null)
      $userId = $kga['usr']['usr_ID'];
    
    $pdo_conn->beginTransaction();

    $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}preferences (`userID`,`var`,`value`)
    VALUES(?,?,?) ON DUPLICATE KEY UPDATE value = ?;");

    foreach ($data as $key=>$value) {
      $key = $prefix.$key;
      $result = $pdo_query->execute(array(
        $userId,$key,$value,$value));
      if (! $result) {
        $pdo_conn->rollBack();
        return false;
      }      
    }
    
    return $pdo_conn->commit();
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
    $p = $kga['server_prefix'];
    
    $pdo_conn->beginTransaction();
    
    $pdo_query = $pdo_conn->prepare("DELETE FROM ${p}ldr WHERE grp_leader=?;");    
    $d_result = $pdo_query->execute(array($ldr_id));
    if ($d_result == false) {
            $pdo_conn->rollBack();
            return false;
    }
    
    foreach ($grp_array as $current_grp) {
      $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}ldr(grp_ID,grp_leader) VALUES (?,?);");
      $result = $pdo_query->execute(array($current_grp,$ldr_id));
      if ($result == false) {
          $pdo_conn->rollBack();
          return false;
      }
    }
    
    update_leader_status();
    
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
    $p = $kga['server_prefix'];
    
    $pdo_conn->beginTransaction();
    
    $d_query = $pdo_conn->prepare("DELETE FROM ${p}ldr WHERE grp_ID=?;");
    $d_result = $d_query->execute(array($grp_id));
    if ($d_result == false) {
        return false;
    }
    
    foreach ($ldr_array as $current_ldr) {
      $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}ldr (grp_ID,grp_leader) VALUES (?,?);");
      $result = $pdo_query->execute(array($grp_id,$current_ldr));
      if ($result == false) {
          return false;
      }
    }
    
    update_leader_status();
    
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT grp_ID FROM ${p}ldr WHERE grp_leader = ?;");
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT grp_leader FROM ${p}ldr
    JOIN ${p}usr ON ${p}usr.usr_ID = ${p}ldr.grp_leader WHERE grp_ID = ? AND usr_trash=0;");
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
    $p = $kga['server_prefix'];
    
    $data = clean_data($data);
        
    $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}grp (grp_name, grp_trash) VALUES (?, ?);");
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
    $p = $kga['server_prefix'];

    $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}grp WHERE grp_ID = ?");
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT COUNT(*) FROM ${p}usr WHERE usr_trash=0 AND usr_grp = ?");
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
    $p = $kga['server_prefix'];
    
    $data = clean_data($data); 
    
    $pdo_query = $pdo_conn->prepare("UPDATE ${p}grp SET grp_name = ? WHERE grp_ID = ?;");
    return $pdo_query->execute(array($data['grp_name'],$grp_id));
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("UPDATE ${p}grp SET grp_trash=1 WHERE grp_ID = ?;");
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}var;");
    $result = $pdo_query->execute();
    
    $var_data = array();
        
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $var_data[$row['var']] = $row['value']; 
    } 
    
    return $var_data;
}

// -----------------------------------------------------------------------------------------------------------
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
    $p = $kga['server_prefix'];
    
    $data = clean_data($data);
        
    $pdo_conn->beginTransaction();

    $statement = $pdo_conn->prepare("UPDATE ${p}var SET value = ? WHERE var = ?");
    
    foreach ($data as $key => $value) {
      $statement->bindValue(1,$value);
      $statement->bindValue(2,$key);

      if (!$statement->execute())
        return false;
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT COUNT( * ) FROM ${p}zef WHERE zef_usrID = ? AND zef_in > 0 AND zef_out = 0;");
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
    $p = $kga['server_prefix'];
    
    $return_state = true;    
    
    // Lock tables
    $pdo_query_l = $pdo_conn->prepare("LOCK TABLE ${p}usr READ, ${p}zef READ");
    $result_l = $pdo_query_l->execute();
    
    // case 1: scan for multiple running entries of the same user
    
    $pdo_query = $pdo_conn->prepare("SELECT usr_ID FROM ${p}usr");
    $result = $pdo_query->execute();
    
    while ($current_row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {    
        // echo $current_row['usr_ID'] . "<br>";
        $pdo_query_zef = $pdo_conn->prepare("SELECT COUNT(*) FROM ${p}zef WHERE zef_usrID = ? AND zef_in > 0 AND zef_out = 0;");
        $result_zef = $pdo_query_zef->execute(array($current_row['usr_ID']));
        $result_array_zef = $pdo_query_zef->fetch();
        
        if ($result_array_zef[0] > 1) {
        
            $return_state = false;
        
            // echo "User " . $current_row['usr_ID'] . "has multiple running zef entries:<br>";
            
            $pdo_query_zef = $pdo_conn->prepare("SELECT * FROM ${p}zef WHERE zef_usrID = ? AND zef_in > 0 AND zef_out = 0;");
            $result_zef = $pdo_query_zef->execute(array($current_row['usr_ID']));
            
            // mark all running-zef-entries with a comment (except the newest one)
            $pdo_query_zef_max = $pdo_conn->prepare("SELECT MAX(zef_in), zef_ID FROM ${p}zef WHERE zef_usrID = ? AND zef_in > 0 AND zef_out = 0 GROUP BY zef_ID;");
            $result_zef_max = $pdo_query_zef_max->execute(array($current_row['usr_ID']));
            $result_array_zef_max = $pdo_query_zef_max->fetch(PDO::FETCH_ASSOC);
            $max_id = $result_array_zef_max['zef_ID'];
            
            while ($current_row_zef = $pdo_query_zef->fetch(PDO::FETCH_ASSOC)) {
            
                if($current_row_zef['zef_ID'] != $max_id) {
                    $pdo_query_zef_edit = $pdo_conn->prepare("UPDATE ${p}zef SET 
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
    $pdo_query_ul = $pdo_conn->prepare("UNLOCK TABLES");
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
    $p = $kga['server_prefix'];

    if ($zef_id) {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}zef WHERE zef_ID = ?");
    } else {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}zef WHERE zef_usrID = ".$kga['usr']['usr_ID']." ORDER BY zef_ID DESC LIMIT 1");
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
    $p = $kga['server_prefix'];

    $pdo_query = $pdo_conn->prepare("DELETE FROM ${p}zef WHERE `zef_ID` = ? LIMIT 1;");
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}zef (  
    `zef_pctID`, 
    `zef_evtID`,
    `zef_location`,
    `zef_trackingnr`,
    `zef_comment`,
    `zef_comment_type`,
    `zef_in`,
    `zef_out`,
    `zef_time`,
    `zef_usrID`,
    `zef_rate`,
    `zef_cleared`
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
    ;");
    
    $result = $pdo_query->execute(array(
    (int)$data['pct_ID'],
    (int)$data['evt_ID'] ,
    $data['zlocation'],
    $data['trackingnr']==''?null:$data['trackingnr'],
    $data['comment'],
    (int)$data['comment_type'] ,
    (int)$data['in'],
    (int)$data['out'],
    (int)$data['diff'],
    (int)$usr_ID,
    (int)$data['rate'],
    $data['cleared']?1:0
    ));
   logfile(serialize($pdo_query->errorInfo()));
    if ($result === false)
        return false;
    else
      return $pdo_conn->lastInsertId();
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
    $p = $kga['server_prefix'];
    
    $original_array = zef_get_data($id);
    $new_array = array();
    
    foreach ($original_array as $key => $value) {
        if (isset($data[$key]) == true) {
            $new_array[$key] = $data[$key];
        } else {
            $new_array[$key] = $original_array[$key];
        }
    }

    $pdo_query = $pdo_conn->prepare("UPDATE ${p}zef SET
    zef_pctID = ?,
    zef_evtID = ?,
    zef_location = ?,
    zef_trackingnr = ?,
    zef_comment = ?,
    zef_comment_type = ?,
    zef_in = ?,
    zef_out = ?,
    zef_time = ?,
    zef_rate = ?,
    zef_cleared= ?
    WHERE zef_ID = ?;");    
    
    $result = $pdo_query->execute(array(
    (int)$new_array['zef_pctID'],
    (int)$new_array['zef_evtID'] ,
    $new_array['zef_location'],
    $new_array['zef_trackingnr']==''?null:$new_array['zef_trackingnr'],
    $new_array['zef_comment'],
    (int)$new_array['zef_comment_type'] ,
    (int)$new_array['zef_in'],
    (int)$new_array['zef_out'],
    (int)$new_array['zef_time'],
    (int)$new_array['zef_rate'],
    (int)$new_array['zef_cleared'],
    $id
    ));
   
    if ($result == false) {
        $err = $pdo_query->errorInfo();
        error_log("ERROR: " . $err[2]);
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
    $p = $kga['server_prefix'];

    if ($timespace_in == 0 && $timespace_out == 0) {
        $mon = date("n"); $day = date("j"); $Y = date("Y"); 
        $timespace_in  = mktime(0,0,0,$mon,$day,$Y);
        $timespace_out = mktime(23,59,59,$mon,$day,$Y);
    }

    if ($timespace_out == mktime(23,59,59,date('n'),date('j'),date('Y')))
      $timespace_out = 0;
       
    $pdo_query = $pdo_conn->prepare("UPDATE ${p}usr SET timespace_in  = ? WHERE usr_ID = ?;");
    $pdo_query->execute(array($timespace_in ,$user));
     
    $pdo_query = $pdo_conn->prepare("UPDATE ${p}usr SET timespace_out = ? WHERE usr_ID = ?;");
    $pdo_query->execute(array($timespace_out ,$user));
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
    $p = $kga['server_prefix'];
    
    $arr = array();

    if ($group == "all") {
        if ($kga['conf']['flip_pct_display']) {
            $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}pct JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID AND pct_trash=0 ORDER BY pct_visible DESC,knd_name,pct_name;");
        } else {
            $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}pct JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID AND pct_trash=0 ORDER BY pct_visible DESC,pct_name,knd_name;");
        }
        $result = $pdo_query->execute();    
    } else {
        if ($kga['conf']['flip_pct_display']) {
            $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}pct JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID JOIN ${p}grp_pct ON ${p}grp_pct.pct_ID = ${p}pct.pct_ID WHERE ${p}grp_pct.grp_ID = ? AND pct_trash=0 ORDER BY pct_visible DESC,knd_name,pct_name;");
        } else {
            $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}pct JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID JOIN ${p}grp_pct ON ${p}grp_pct.pct_ID = ${p}pct.pct_ID WHERE ${p}grp_pct.grp_ID = ? AND pct_trash=0 ORDER BY pct_visible DESC,pct_name,knd_name;");
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
        $arr[$i]['pct_budget'] = $row['pct_budget'];
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
    $p = $kga['server_prefix'];
    
    $arr = array();

    if ($group == "all") {
      if ($kga['conf']['flip_pct_display']) {
          $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}pct JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID WHERE ${p}pct.pct_kndID = ? AND pct_internal=0 AND pct_trash=0 ORDER BY knd_name,pct_name;");
      } else {
          $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}pct JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID WHERE ${p}pct.pct_kndID = ? AND pct_internal=0 AND pct_trash=0 ORDER BY pct_name,knd_name;");        
      }
      $result = $pdo_query->execute(array($knd_id));  
    } else {
      if ($kga['conf']['flip_pct_display']) {
          $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}pct JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID JOIN ${p}grp_pct ON ${p}grp_pct.pct_ID = ${p}pct.pct_ID WHERE ${p}grp_pct.grp_ID = ? AND ${p}pct.pct_kndID = ? AND pct_internal=0 AND pct_trash=0 ORDER BY knd_name,pct_name;");
      } else {
          $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}pct JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID JOIN ${p}grp_pct ON ${p}grp_pct.pct_ID = ${p}pct.pct_ID WHERE ${p}grp_pct.grp_ID = ? AND ${p}pct.pct_kndID = ? AND pct_internal=0 AND pct_trash=0 ORDER BY pct_name,knd_name;");        
      }  
      $result = $pdo_query->execute(array($group, $knd_id));
    }
    
    $i=0;
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $arr[$i]['pct_ID']      = $row['pct_ID'];
        $arr[$i]['pct_name']    = $row['pct_name'];
        $arr[$i]['knd_name']    = $row['knd_name'];
        $arr[$i]['knd_ID']      = $row['knd_ID'];
        $arr[$i]['pct_visible'] = $row['pct_visible'];
        $arr[$i]['pct_budget']  = $row['pct_budget'];
        $i++;
    }
    return $arr;
}

//-----------------------------------------------------------------------------------------------------------



/**
 *  Creates an array of clauses which can be joined together in the WHERE part
 *  of a sql query. The clauses describe whether a line should be included
 *  depending on the filters set.
 *  
 *
 * @param Array list of IDs of users to include
 * @param Array list of IDs of customers to include
 * @param Array list of IDs of projects to include
 * @param Array list of IDs of events to include
 * @return Array list of where clauses to include in the query
 *
 */

function zef_whereClausesFromFilters($users, $customers , $projects , $events ) {
    
    if (!is_array($users)) $users = array();
    if (!is_array($customers)) $customers = array();
    if (!is_array($projects)) $projects = array();
    if (!is_array($events)) $events = array();


    $whereClauses = array();
    
    if (count($users) > 0) {
      $whereClauses[] = "zef_usrID in (".implode(',',$users).")";
    }
    
    if (count($customers) > 0) {
      $whereClauses[] = "knd_ID in (".implode(',',$customers).")";
    }
    
    if (count($projects) > 0) {
      $whereClauses[] = "pct_ID in (".implode(',',$projects).")";
    }  
    
    if (count($events) > 0) {
      $whereClauses[] = "evt_ID in (".implode(',',$events).")";
    }  

    return $whereClauses;


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
function get_arr_zef($in,$out,$users = null, $customers = null, $projects = null, $events = null, $limit = false, $reverse_order = false, $filterCleared = null) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];

    if (!is_numeric($filterCleared)) {
      $filterCleared = $kga['conf']['hideClearedEntries']-1; // 0 gets -1 for disabled, 1 gets 0 for only not cleared entries
    }

    $whereClauses = zef_whereClausesFromFilters($users, $customers , $projects , $events );

    if (isset($kga['customer']))
      $whereClauses[] = "${p}pct.pct_internal = 0";

    if ($in)
      $whereClauses[]="(zef_out > $in || zef_out = 0)";
    if ($out)
      $whereClauses[]="zef_in < $out";
    if ($filterCleared > -1)
      $whereClauses[] = "zef_cleared = $filterCleared";

    if ($limit) {
        if (isset($kga['conf']['rowlimit'])) {
            $limit = "LIMIT " .$kga['conf']['rowlimit'];
        } else {
            $limit="LIMIT 100";
        }
    } else {
        $limit="";
    }

    $query = "SELECT zef_ID, zef_in, zef_out, zef_time, zef_rate, zef_pctID, zef_evtID, zef_usrID, pct_ID, knd_name, pct_kndID, evt_name, pct_comment, pct_name, zef_location, zef_trackingnr, zef_comment, zef_comment_type, usr_name, usr_alias, zef_cleared
             FROM ${p}zef
             Join ${p}pct ON zef_pctID = pct_ID
             Join ${p}knd ON pct_kndID = knd_ID
             Join ${p}usr ON zef_usrID = usr_ID 
             Join ${p}evt ON evt_ID    = zef_evtID "
              .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
             ' ORDER BY zef_in '.($reverse_order?'ASC ':'DESC ') . $limit . ';';
             
    $pdo_query = $pdo_conn->prepare($query);
    
             $pdo_query->execute();
    $i=0;
    $arr=array();
    /* TODO: needs revision as foreach loop */
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $arr[$i]['zef_ID']           = $row['zef_ID'];
        if ($row['zef_in'] <= $in && $row['zef_out'] < $out)  {
          $arr[$i]['zef_in']            = $in;
          $arr[$i]['zef_out']          = $row['zef_out'];
        }
        else if ($row['zef_in'] <= $in && $row['zef_out'] >= $out)  {
          $arr[$i]['zef_in']            = $in;
          $arr[$i]['zef_out']          = $out;
        }
        else if ($row['zef_in'] > $in && $row['zef_out'] < $out)  {
          $arr[$i]['zef_in']            = $row['zef_in'];
          $arr[$i]['zef_out']          = $row['zef_out'];
        }
        else if ($row['zef_in'] > $in && $row['zef_out'] >= $out)  {
          $arr[$i]['zef_in']            = $row['zef_in'];
          $arr[$i]['zef_out']          = $out;
        }

        if ($row['zef_out'] != 0) {
          // only calculate time after recording is complete
          $arr[$i]['zef_time']         = $arr[$i]['zef_out'] - $arr[$i]['zef_in']; 
          $arr[$i]['zef_duration']     = formatDuration($arr[$i]['zef_time']);
          $arr[$i]['wage_decimal']     = $arr[$i]['zef_time']/3600*$row['zef_rate'];
          $arr[$i]['wage']             = sprintf("%01.2f",$arr[$i]['wage_decimal']);
        }
        

        $arr[$i]['zef_rate']         = $row['zef_rate'];
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
        $arr[$i]['zef_cleared']      = $row['zef_cleared'];
        $arr[$i]['zef_comment_type'] = $row['zef_comment_type'];
        $arr[$i]['usr_name']        = $row['usr_name'];
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
    $p = $kga['server_prefix'];
        
    if (isset($_COOKIE['kimai_usr']) && isset($_COOKIE['kimai_key']) && $_COOKIE['kimai_usr'] != "0" && $_COOKIE['kimai_key'] != "0") {
        $kimai_usr = addslashes($_COOKIE['kimai_usr']);
        $kimai_key = addslashes($_COOKIE['kimai_key']);
        if (get_seq($kimai_usr) != $kimai_key) {
            kickUser();
        } else {
            if (strncmp($kimai_usr, 'knd_', 4) == 0) {
              
              $data     = $pdo_query = $pdo_conn->prepare("SELECT knd_ID FROM ${p}knd WHERE knd_name = ? AND NOT knd_trash = '1';");
              $result   = $pdo_query->execute(array(substr($kimai_usr,4)));
              $row      = $pdo_query->fetch(PDO::FETCH_ASSOC);
              $knd_ID   = $row['knd_ID'];
              if ($knd_ID < 1) {
                  kickUser();
              }
            }
            else {
              $data     = $pdo_query = $pdo_conn->prepare("SELECT usr_ID,usr_sts,usr_grp FROM ${p}usr WHERE usr_name = ? AND usr_active = '1' AND NOT usr_trash = '1';");
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
        }
        
    } else {
        kickUser();
    }

    
    if ((isset($knd_ID) && $knd_ID<1) ||  (isset($usr_ID) && $usr_ID<1)) {
        kickUser();
    }

    // load configuration
    get_global_config();
    if (strncmp($kimai_usr, 'knd_', 4) == 0)
      get_customer_config($knd_ID);
    else  
      get_user_config($usr_ID);
    // get_customer_config

    // override default language if user has chosen a language in the prefs
    if ($kga['conf']['lang'] != "") {
      $kga['language'] = $kga['conf']['lang'];
      $kga['lang'] = array_replace_recursive($kga['lang'],include(WEBROOT."language/${kga['language']}.php"));
    }
    
    return (isset($kga['usr'])?$kga['usr']:null);
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Write global configuration into $kga including defaults for user settings.
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return array $kga 
 * @author th
 *
 */
function get_global_config() {
  global $kga, $pdo_conn;    
    $p = $kga['server_prefix'];

  // get values from global configuration 
  $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}var;");
  $result = $pdo_query->execute();
  $row  = $pdo_query->fetch(PDO::FETCH_ASSOC);

  do { 
      $kga['conf'][$row['var']] = $row['value']; 
  } while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC));

  $kga['conf']['timezone'] = $kga['conf']['defaultTimezone'];
  $kga['conf']['rowlimit'] = 100;
  $kga['conf']['skin'] = 'standard';
  $kga['conf']['autoselection'] = 1;
  $kga['conf']['quickdelete'] = 0;
  $kga['conf']['flip_pct_display'] = 0;
  $kga['conf']['pct_comment_flag'] = 0;
  $kga['conf']['showIDs'] = 0;
  $kga['conf']['noFading'] = 0;
  $kga['conf']['lang'] = '';
  $kga['conf']['user_list_hidden'] = 0;
  $kga['conf']['hideClearedEntries'] = 0;
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
  $p = $kga['server_prefix'];

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
  `secure`,

  `lastProject`,
  `lastEvent`,
  `lastRecord`,
  `timespace_in`,
  `timespace_out`

  FROM ${p}usr WHERE usr_ID = ?;");

  $result = $pdo_query->execute(array($user));
  $row  = $pdo_query->fetch(PDO::FETCH_ASSOC);
  foreach( $row as $key => $value) {
      $kga['usr'][$key] = $value;
  }

  $kga['conf'] = array_merge($kga['conf'],usr_get_preferences_by_prefix('ui.',$kga['usr']['usr_ID']));
  $userTimezone = usr_get_preference('timezone');
  if ($userTimezone != '')
    $kga['conf']['timezone'] = $userTimezone;
  
  date_default_timezone_set($kga['conf']['timezone']);
}

// -----------------------------------------------------------------------------------------------------------

/**
 * write details of a specific customer into $kga
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return array $kga 
 * @author sl
 *
 */

function get_customer_config($customer_ID) {
  global $kga, $pdo_conn;
  $p = $kga['server_prefix'];


  // get values from customer record
  $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}knd WHERE knd_ID = ?;");
  $result = $pdo_query->execute(array($customer_ID));
  $row  = $pdo_query->fetch(PDO::FETCH_ASSOC);
  foreach( $row as $key => $value) {
      $kga['customer'][$key] = $value;
  }

}

// -----------------------------------------------------------------------------------------------------------

/**
 * checks if a customer with this name exists
 *
 * @param string name
 * @global array $kga kimai-global-array
 * @return integer
 * @author sl
 */
function is_customer_name($name) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];

    $pdo_query = $pdo_conn->prepare("SELECT knd_ID FROM ${p}knd WHERE knd_name = ?");
    $pdo_query->execute(array($name));
    return $pdo_query->rowCount() == 1;
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
 * @global array $kga kimai-global-array
 * @return integer
 * @author th
 */
function get_event_last() {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];

    $lastRecord = $kga['usr']['lastRecord'];
    $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}zef WHERE zef_ID = ?");
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
    $p = $kga['server_prefix'];

    $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}zef 
    Left Join ${p}pct ON zef_pctID = pct_ID 
    Left Join ${p}knd ON pct_kndID = knd_ID 
    Left Join ${p}evt ON evt_ID    = zef_evtID
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
function get_zef_time($in,$out,$users = null, $customers = null, $projects = null, $events = null, $filterCleared = null) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];

    if (!is_numeric($filterCleared)) {
      $filterCleared = $kga['conf']['hideClearedEntries']-1; // 0 gets -1 for disabled, 1 gets 0 for only not cleared entries
    }

    $whereClauses = zef_whereClausesFromFilters($users,$customers,$projects,$events);

    if ($in)
      $whereClauses[]="zef_out > $in";
    if ($out)
      $whereClauses[]="zef_in < $out";
    if ($filterCleared > -1)
      $whereClauses[] = "zef_cleared = $filterCleared";

    $pdo_query = $pdo_conn->prepare("SELECT zef_in,zef_out,zef_time FROM ${p}zef 
             Join ${p}pct ON zef_pctID = pct_ID
             Join ${p}knd ON pct_kndID = knd_ID
             Join ${p}usr ON zef_usrID = usr_ID
             Join ${p}evt ON evt_ID    = zef_evtID ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses));
    $pdo_query->execute();
    $sum = 0;
    $zef_in = 0;
    $zef_out = 0;
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
      if ($row['zef_in'] <= $in && $row['zef_out'] < $out)  {
        $zef_in  = $in;
        $zef_out = $row['zef_out'];
      }
      else if ($row['zef_in'] <= $in && $row['zef_out'] >= $out)  {
        $zef_in  = $in;
        $zef_out = $out;
      }
      else if ($row['zef_in'] > $in && $row['zef_out'] < $out)  {
        $zef_in  = $row['zef_in'];
        $zef_out = $row['zef_out'];
      }
      else if ($row['zef_in'] > $in && $row['zef_out'] >= $out)  {
        $zef_in  = $row['zef_in'];
        $zef_out = $out;
      }
      $sum+=(int)($zef_out - $zef_in);
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
    $p = $kga['server_prefix'];
        
    $arr = array();
    if ($group == "all") {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}knd WHERE knd_trash=0 ORDER BY knd_visible DESC,knd_name;");
        $pdo_query->execute();
    } else {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}knd JOIN ${p}grp_knd ON `${p}grp_knd`.`knd_ID`=`${p}knd`.`knd_ID` WHERE `${p}grp_knd`.`grp_ID` = ? AND knd_trash=0 ORDER BY knd_visible DESC,knd_name;");
        $pdo_query->execute(array($group));
    }
  
    $i=0;
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $arr[$i]['knd_ID']      = $row['knd_ID'];
        $arr[$i]['knd_name']    = $row['knd_name'];
        $arr[$i]['knd_contact'] = $row['knd_contact'];
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
    $p = $kga['server_prefix'];

    $arr = array();

    // check if user is admin
    $pdo_query = $pdo_conn->prepare("SELECT usr_sts FROM ${p}usr WHERE usr_ID = ?");
    $result   = $pdo_query->execute(array($user_id));
    $row      = $pdo_query->fetch(PDO::FETCH_ASSOC);

    // SELECT usr_ID,usr_name FROM kimai_usr u INNER JOIN kimai_ldr l ON usr_grp = grp_ID WHERE grp_leader = 990287573
    if ($row['usr_sts'] == "0") { // if is admin
      $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}usr WHERE usr_trash=0 ORDER BY usr_name");
      $pdo_query->execute();
    }
    else {
      $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}usr INNER JOIN ${p}ldr ON usr_grp = grp_ID WHERE usr_trash=0 AND grp_leader = ? ORDER BY usr_name");
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
function get_arr_time_usr($in,$out,$users = null, $customers = null, $projects = null,$events = null) {
    global $kga;
    global $pdo_conn;
    $p = $kga['server_prefix'];

    $whereClauses = zef_whereClausesFromFilters($users,$customers,$projects,$events);
    $whereClauses[] = "${p}usr.usr_trash=0";

    if ($in)
      $whereClauses[]="zef_out > $in";
    if ($out)
      $whereClauses[]="zef_in < $out";
    
    
 $pdo_query = $pdo_conn->prepare("SELECT zef_in,zef_out, usr_ID, (zef_out - zef_in) / 3600 * zef_rate AS costs
             FROM ${p}zef 
             Join ${p}pct ON zef_pctID = pct_ID
             Join ${p}knd ON pct_kndID = knd_ID
             Join ${p}usr ON zef_usrID = usr_ID
             Join ${p}evt ON evt_ID    = zef_evtID "
             .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses). " ORDER BY zef_in DESC;");
    
             $pdo_query->execute();

    $arr = array();
    $zef_in = 0;
    $zef_out = 0;  
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
      if ($row['zef_in'] <= $in && $row['zef_out'] < $out)  {
        $zef_in  = $in;
        $zef_out = $row['zef_out'];
      }
      else if ($row['zef_in'] <= $in && $row['zef_out'] >= $out)  {
        $zef_in  = $in;
        $zef_out = $out;
      }
      else if ($row['zef_in'] > $in && $row['zef_out'] < $out)  {
        $zef_in  = $row['zef_in'];
        $zef_out = $row['zef_out'];
      }
      else if ($row['zef_in'] > $in && $row['zef_out'] >= $out)  {
        $zef_in  = $row['zef_in'];
        $zef_out = $out;
      }

      if (isset($arr[$row['usr_ID']])) {
        $arr[$row['usr_ID']]['time']  += (int)($zef_out - $zef_in);
        $arr[$row['usr_ID']]['costs'] += (int)$row['costs'];
      }
      else  {
        $arr[$row['usr_ID']]['time']  = (int)($zef_out - $zef_in);
        $arr[$row['usr_ID']]['costs'] = (int)$row['costs'];
      }
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
function get_arr_time_knd($in,$out,$users = null, $customers = null, $projects = null, $events = null) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];

    $whereClauses = zef_whereClausesFromFilters($users,$customers,$projects,$events);
    $whereClauses[] = "${p}knd.knd_trash=0";
    
    if ($in) 
      $whereClauses[]="zef_out > $in";
    if ($out) 
      $whereClauses[]="zef_in < $out";
    
    $pdo_query = $pdo_conn->prepare("SELECT zef_in,zef_out, knd_ID, (zef_out - zef_in) / 3600 * zef_rate AS costs
            FROM ${p}zef 
            Left Join ${p}pct ON zef_pctID = pct_ID
            Left Join ${p}knd ON pct_kndID = knd_ID ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses));
    $pdo_query->execute();

    $arr = array();  
    $zef_in = 0;
    $zef_out = 0;
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
      if ($row['zef_in'] <= $in && $row['zef_out'] < $out)  {
        $zef_in  = $in;
        $zef_out = $row['zef_out'];
      }
      else if ($row['zef_in'] <= $in && $row['zef_out'] >= $out)  {
        $zef_in  = $in;
        $zef_out = $out;
      }
      else if ($row['zef_in'] > $in && $row['zef_out'] < $out)  {
        $zef_in  = $row['zef_in'];
        $zef_out = $row['zef_out'];
      }
      else if ($row['zef_in'] > $in && $row['zef_out'] >= $out)  {
        $zef_in  = $row['zef_in'];
        $zef_out = $out;
      }

      if (isset($arr[$row['knd_ID']])) {
        $arr[$row['knd_ID']]['time']  += (int)($zef_out - $zef_in);
        $arr[$row['knd_ID']]['costs'] += (int)$row['costs'];
      }
      else {
        $arr[$row['knd_ID']]['time']  = (int)($zef_out - $zef_in);
        $arr[$row['knd_ID']]['costs'] = (int)$row['costs'];
      }
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
function get_arr_time_pct($in,$out,$users = null,$customers = null, $projects = null, $events = null) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];

    $whereClauses = zef_whereClausesFromFilters($users,$customers,$projects,$events);
    $whereClauses[] = "${p}pct.pct_trash=0";

    if ($in)
      $whereClauses[]="zef_out > $in";
    if ($out)
      $whereClauses[]="zef_in < $out";
    $arr = array();
    $pdo_query = $pdo_conn->prepare("SELECT zef_in,zef_out,zef_pctID, (zef_out - zef_in) / 3600 * zef_rate AS costs
        FROM ${p}zef 
        Left Join ${p}pct ON zef_pctID = pct_ID
        Left Join ${p}knd ON pct_kndID = knd_ID ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses));
    $pdo_query->execute();

    $arr = array();  
    $zef_in = 0;
    $zef_out = 0;
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
      if ($row['zef_in'] <= $in && $row['zef_out'] < $out)  {
        $zef_in  = $in;
        $zef_out = $row['zef_out'];
      }
      else if ($row['zef_in'] <= $in && $row['zef_out'] >= $out)  {
        $zef_in  = $in;
        $zef_out = $out;
      }
      else if ($row['zef_in'] > $in && $row['zef_out'] < $out)  {
        $zef_in  = $row['zef_in'];
        $zef_out = $row['zef_out'];
      }
      else if ($row['zef_in'] > $in && $row['zef_out'] >= $out)  {
        $zef_in  = $row['zef_in'];
        $zef_out = $out;
      }

      if (isset($arr[$row['zef_pctID']])) {
        $arr[$row['zef_pctID']]['time']  += (int)($zef_out - $zef_in);
        $arr[$row['zef_pctID']]['costs'] += (int)$row['costs'];
      }
      else {
        $arr[$row['zef_pctID']]['time']  = (int)($zef_out - $zef_in);
        $arr[$row['zef_pctID']]['costs'] = (int)$row['costs'];
      }
    }
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

## Load into Array: Events 
function get_arr_evt($group) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];
    
    $arr = array();
    if ($group == "all") {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}evt WHERE evt_trash=0 ORDER BY evt_visible DESC,evt_name;");
        $pdo_query->execute();
    } else {
        $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}evt JOIN ${p}grp_evt ON `${p}grp_evt`.`evt_ID`=`${p}evt`.`evt_ID` WHERE `${p}grp_evt`.`grp_ID` = ? AND evt_trash=0 ORDER BY evt_visible DESC,evt_name;");
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

## Load into Array: Events 
function get_arr_evt_by_pct($group,$pct) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];
    
    $arr = array();
    if ($group == "all") {
        $pdo_query = $pdo_conn->prepare("SELECT ${p}evt.evt_ID,evt_name,evt_visible FROM ${p}evt
 LEFT JOIN ${p}pct_evt ON `${p}pct_evt`.`evt_ID`=`${p}evt`.`evt_ID`
 WHERE evt_trash=0 AND (pct_ID = ? OR pct_ID IS NULL)
 ORDER BY evt_visible DESC,evt_name;");
        $pdo_query->execute(array($pct));
    } else {
        $pdo_query = $pdo_conn->prepare("SELECT ${p}evt.evt_ID,evt_name,evt_visible FROM ${p}evt
 JOIN ${p}grp_evt ON `${p}grp_evt`.`evt_ID`=`${p}evt`.`evt_ID`
 LEFT JOIN ${p}pct_evt ON `${p}pct_evt`.`evt_ID`=`${p}evt`.`evt_ID`
 WHERE `${p}grp_evt`.`grp_ID` = ? AND evt_trash=0
 AND (pct_ID = ? OR pct_ID IS NULL)
 ORDER BY evt_visible DESC,evt_name;");
        $pdo_query->execute(array($group,$pct));
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
 * returns list of events used with specified customer
 *
 * @param integer $customer filter for only this ID of a customer
 * @global array $kga kimai-global-array
 * @global array $pdo_conn PDO connection
 * @return array
 * @author sl
 */
function get_arr_evt_by_knd($customer_ID) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}evt WHERE evt_ID IN (SELECT zef_evtID FROM ${p}zef WHERE zef_pctID IN (SELECT pct_ID FROM ${p}pct WHERE pct_kndID = ?)) AND evt_trash=0");
    $pdo_query->execute(array($customer_ID));
    
    $arr=array();
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
function get_arr_time_evt($in,$out,$users = null,$customers = null,$projects = null, $events = null) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];

    $whereClauses = zef_whereClausesFromFilters($users,$customers,$projects,$events);
    $whereClauses[] = "${p}evt.evt_trash = 0";

    if ($in)
      $whereClauses[]="zef_out > $in";
    if ($out)
      $whereClauses[]="zef_in < $out";
    $pdo_query = $pdo_conn->prepare("SELECT zef_in,zef_out,zef_evtID, (zef_out - zef_in) / 3600 * zef_rate AS costs
        FROM ${p}zef 
        Left Join ${p}evt ON zef_evtID = evt_ID
        Left Join ${p}pct ON zef_pctID = pct_ID
        Left Join ${p}knd ON pct_kndID = knd_ID ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses));
    $pdo_query->execute();

    $arr = array();  
    $zef_in = 0;
    $zef_out = 0;
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
      if ($row['zef_in'] <= $in && $row['zef_out'] < $out)  {
        $zef_in  = $in;
        $zef_out = $row['zef_out'];
      }
      else if ($row['zef_in'] <= $in && $row['zef_out'] >= $out)  {
        $zef_in  = $in;
        $zef_out = $out;
      }
      else if ($row['zef_in'] > $in && $row['zef_out'] < $out)  {
        $zef_in  = $row['zef_in'];
        $zef_out = $row['zef_out'];
      }
      else if ($row['zef_in'] > $in && $row['zef_out'] >= $out)  {
        $zef_in  = $row['zef_in'];
        $zef_out = $out;
      }

      if (isset($arr[$row['zef_evtID']])) {
        $arr[$row['zef_evtID']]['time']  += (int)($zef_out - $zef_in);
        $arr[$row['zef_evtID']]['costs'] += (int)$row['costs'];
      }
      else {
        $arr[$row['zef_evtID']]['time'] = (int)($zef_out - $zef_in);
        $arr[$row['zef_evtID']]['costs'] = (int)$row['costs'];
      }
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
        if (isset($arr_time[$knd['knd_ID']])) $arr[$i]['zeit'] = formatDuration($arr_time[$knd['knd_ID']]);
        else $arr[$i]['zeit']   = formatDuration(0);
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
    $p = $kga['server_prefix'];
        
    $pdo_query = $pdo_conn->prepare("SELECT zef_ID,zef_in,zef_time FROM ${p}zef WHERE zef_usrID = ? ORDER BY zef_in DESC LIMIT 1;");
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
    $p = $kga['server_prefix'];
       
    $outPoint=$inPoint+86399;
    $pdo_query = $pdo_conn->prepare("SELECT sum(zef_time) as zeit FROM ${p}zef WHERE zef_in > ? AND zef_out < ? AND zef_usrID = ?;");
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
    $p = $kga['server_prefix'];
    
    $inDatum_m = date("m",$inPoint);
    $inDatum_Y = date("Y",$inPoint);
    $inDatum_t = date("t",$inPoint);
    
    $inPoint  = mktime(0,0,0,$inDatum_m,1,$inDatum_Y);
    $outPoint = mktime(23,59,59,$inDatum_m,$inDatum_t,$inDatum_Y);

    $pdo_query = $pdo_conn->prepare("SELECT sum(zef_time) as zeit FROM ${p}zef WHERE zef_in > ? AND zef_out < ? AND zef_usrID = ?;");
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT sum(zef_time) as zeit FROM ${p}zef WHERE zef_usrID = ?");
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
    $p = $kga['server_prefix'];

    $in  = (int)mktime(0,0,0,1,1,$year); 
    $out = (int)mktime(23,59,59,12,(int)date("t"),$year);
    $pdo_query = $pdo_conn->prepare("SELECT sum(zef_time) as zeit FROM ${p}zef WHERE zef_in > ? AND zef_out < ? AND zef_usrID = ?");
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT value FROM ${p}var WHERE var = 'version';");
    $pdo_query->execute(array());
    $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
    $return[0]   = $row['value'];
    
    if (!is_array($row)) $return[0] = "0.5.1";
    
    $pdo_query = $pdo_conn->prepare("SELECT value FROM ${p}var WHERE var = 'revision';");
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
    $p = $kga['server_prefix'];
    
    if (strncmp($user, 'knd_', 4) == 0) {
      $pdo_query = $pdo_conn->prepare("SELECT knd_secure FROM ${p}knd WHERE knd_name = ?;");
      $pdo_query->execute(array(substr($user,4)));
      $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
      $seq         = $row['knd_secure'];
    }
    else {
      $pdo_query = $pdo_conn->prepare("SELECT secure FROM ${p}usr WHERE usr_name = ?;");
      $pdo_query->execute(array($user));
      $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
      $seq         = $row['secure'];
    }
    
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
    $p = $kga['server_prefix'];
      
    $arr = array();
    
    if (!$trash) {
        $trashoption = "WHERE usr_trash !=1";
    }
    $pdo_query = $pdo_conn->prepare(sprintf("SELECT * FROM ${p}usr Left Join ${p}grp ON usr_grp = grp_ID %s ORDER BY usr_name;",$trashoption));
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
    $p = $kga['server_prefix'];
    
    // Lock tables
    $pdo_query_l = $pdo_conn->prepare("LOCK TABLE 
    ${p}usr READ, 
    ${p}grp READ,      
    ${p}ldr READ
    ");
    $result_l = $pdo_query_l->execute();
    
    if (!$trash) {
        $trashoption = "WHERE grp_trash !=1";
    }
    $pdo_query = $pdo_conn->prepare(sprintf("SELECT * FROM ${p}grp %s ORDER BY grp_name;",$trashoption));
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
    $pdo_query_ul = $pdo_conn->prepare("UNLOCK TABLES");
    $result_ul = $pdo_query_ul->execute();
    
    // error_log("get_arr_grp: " . serialize($groups));
    
    return $groups;
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
function get_arr_grp_by_leader($leader_id,$trash=0) {
    global $kga, $pdo_conn;
    
    // Lock tables
    $pdo_query_l = $pdo_conn->prepare("LOCK TABLE 
    " . $kga['server_prefix'] . "usr READ, 
    " . $kga['server_prefix'] . "grp READ,      
    " . $kga['server_prefix'] . "ldr READ
    ");
    $result_l = $pdo_query_l->execute();
    
    if (!$trash) {
        $trashoption = "AND grp_trash !=1";
    }
    $pdo_query = $pdo_conn->prepare(
"SELECT " . $kga['server_prefix'] . "grp.* 
    FROM " . $kga['server_prefix'] . "grp JOIN " . $kga['server_prefix'] . "ldr ON " . $kga['server_prefix'] . "grp.grp_ID =" . $kga['server_prefix'] . "ldr.grp_ID 
    WHERE grp_leader = ? $trashoption ORDER BY grp_name");

    $result = $pdo_query->execute($leader_id);
    
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
    $pdo_query_ul = $pdo_conn->prepare("UNLOCK TABLES");
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
    $p = $kga['server_prefix'];
    
    $last_task = get_event_last();      // aktuelle vorgangs-ID auslesen
    
    $zef_ID = $last_task['zef_ID'];


    $rounded = roundTimespan($last_task['zef_in'],time(),$kga['conf']['roundPrecision']);
    $difference = $rounded['end']-$rounded['start'];

    $pdo_query = $pdo_conn->prepare("UPDATE ${p}zef SET zef_in = ?, zef_out = ?, zef_time = ? WHERE zef_ID = ?;");
    $pdo_query->execute(array($rounded['start'],$rounded['end'],$difference,$zef_ID));
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("INSERT INTO ${p}zef 
    (zef_pctID,zef_evtID,zef_in,zef_usrID,zef_rate) VALUES 
    (?, ?, ?, ?, ?);");
    $pdo_query->execute(array($pct_ID,$evt_ID,time(),$user,get_best_fitting_rate($user,$pct_ID,$evt_ID)));
    
    $pdo_query = $pdo_conn->prepare("UPDATE ${p}usr SET lastRecord = LAST_INSERT_ID() WHERE usr_ID = ?;");
    $pdo_query->execute(array($user));
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Just edit the project for an entry. This is used for changing the project
 * of a running entry.
 * 
 * @param $zef_id of the timesheet entry
 * @param $pct_id id of the project to change to
 */
function zef_edit_pct($zef_id,$pct_id) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("UPDATE ${p}zef 
    SET zef_pctID = ? WHERE zef_ID = ?");

    $pdo_query->execute(array($pct_id,$zef_id));

}

// -----------------------------------------------------------------------------------------------------------

/**
 * Just edit the task for an entry. This is used for changing the task
 * of a running entry.
 * 
 * @param $zef_id of the timesheet entry
 * @param $evt_id id of the task to change to
 */
function zef_edit_evt($zef_id,$evt_id) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("UPDATE ${p}zef 
    SET zef_evtID = ? WHERE zef_ID = ?");

    $pdo_query->execute(array($evt_id,$zef_id));

}

// -----------------------------------------------------------------------------------------------------------

/**
 * Just edit the comment an entry. This is used for editing the comment
 * of a running entry.
 * 
 * @param $zef_ID id of the timesheet entry
 * @param $comment_type new type of the comment
 * @param $comment the comment text
 */
function zef_edit_comment($zef_ID,$comment_type,$comment) {
    global $kga, $pdo_conn;
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("UPDATE ${p}zef 
    SET zef_comment_type = ?, zef_comment = ? WHERE zef_ID = ?");

    $pdo_query->execute(array($comment_type,$comment,$zef_ID));

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
    $p = $kga['server_prefix'];
        
    $pdo_query = $pdo_conn->prepare("SELECT * FROM ${p}usr Left Join ${p}grp ON usr_grp = grp_ID WHERE usr_ID = ? LIMIT 1;");
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

    $arr['usr_rate'] = get_rate($arr['usr_ID'],NULL,NULL);
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT usr_ID FROM ${p}usr WHERE usr_name = ? LIMIT 1;");
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
    $p = $kga['server_prefix'];
    
    $pdo_query = $pdo_conn->prepare("SELECT usr_name FROM ${p}usr WHERE usr_ID = ? LIMIT 1;");
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
    $p = $kga['server_prefix'];
    
    switch ($subject) {
        case "pct":
        case "evt":
            $pdo_query = $pdo_conn->prepare("SELECT COUNT(*) AS result FROM ${p}zef WHERE zef_" . $subject . "ID = ?;");
            $pdo_query->execute(array($id));
        break;

        case "knd":
            $pdo_query = $pdo_conn->prepare("SELECT COUNT(*) AS result FROM ${p}pct Left Join ${p}knd ON pct_kndID = knd_ID WHERE pct_kndID = ?;");
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
    $p = $kga['server_prefix'];

    $query = "SELECT zef_in FROM ${p}zef WHERE zef_usrID = ? ORDER BY zef_in ASC LIMIT 1;";
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

/**
 * Set field usr_sts for users to 1 if user is a group leader, otherwise to 2.
 * Admin status will never be changed.
 * Calling function should start and end sql transaction.
 * 
 * @global array $kga              kimai global array
 * @global array $pdo_conn         PDO connection
 * @author sl
 */
function update_leader_status() {
    global $kga,$pdo_conn;
    $p = $kga['server_prefix'];

    $query = $pdo_conn->prepare("UPDATE ${p}usr,${p}ldr SET usr_sts = 2 WHERE usr_sts = 1");
    $result = $query->execute();
    if ($result == false) {
        return false;
    }
    
    $query = $pdo_conn->prepare("UPDATE ${p}usr,${p}ldr SET usr_sts = 1 WHERE usr_sts = 2 AND grp_leader = usr_ID");
    $result = $query->execute();
    if ($result == false) {
        return false;
    }

    return true;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Save rate to database.
 * 
 * @global array $kga              kimai global array
 * @global array $pdo_conn         PDO connection
 * @author sl
 */
function save_rate($user_id,$project_id,$event_id,$rate) {
  global $kga,$pdo_conn;
  $p = $kga['server_prefix'];

  // validate input
  if ($user_id == NULL || !is_numeric($user_id)) $user_id = "NULL";
  if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
  if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";
  if (!is_numeric($rate)) return false;


  // build update or insert statement
  $query_string = "";
  if (get_rate($user_id,$project_id,$event_id) === false)
    $query_string = "INSERT INTO ${p}rates VALUES($user_id,$project_id,$event_id,$rate);";
  else
    $query_string = "UPDATE ${p}rates SET rate = $rate WHERE ".
  (($user_id=="NULL")?"user_id is NULL":"user_id = $user_id"). " AND ".
  (($project_id=="NULL")?"project_id is NULL":"project_id = $project_id"). " AND ".
  (($event_id=="NULL")?"event_id is NULL":"event_id = $event_id");

  $query = $pdo_conn->prepare($query_string);
  $result = $query->execute();

  if ($result == false)
    return false;
  else
    return true;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Read rate from database.
 * 
 * @global array $kga              kimai global array
 * @global array $pdo_conn         PDO connection
 * @author sl
 */
function get_rate($user_id,$project_id,$event_id) {
  global $kga,$pdo_conn;
  $p = $kga['server_prefix'];

  // validate input
  if ($user_id == NULL || !is_numeric($user_id)) $user_id = "NULL";
  if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
  if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";


  $query_string = "SELECT rate FROM ${p}rates WHERE ".
  (($user_id=="NULL")?"user_id is NULL":"user_id = $user_id"). " AND ".
  (($project_id=="NULL")?"project_id is NULL":"project_id = $project_id"). " AND ".
  (($event_id=="NULL")?"event_id is NULL":"event_id = $event_id");

  $query = $pdo_conn->prepare($query_string);
  $result = $query->execute();

  if ($query->rowCount() == 0)
    return false;

  $data = $query->fetch(PDO::FETCH_ASSOC);
  return $data['rate'];
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Remove rate from database.
 * 
 * @global array $kga              kimai global array
 * @global array $pdo_conn         PDO connection
 * @author sl
 */
function remove_rate($user_id,$project_id,$event_id) {
  global $kga,$pdo_conn;
  $p = $kga['server_prefix'];


  // validate input
  if ($user_id == NULL || !is_numeric($user_id)) $user_id = "NULL";
  if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
  if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";


  $query_string = "DELETE FROM ${p}rates WHERE ".
  (($user_id=="NULL")?"user_id is NULL":"user_id = $user_id"). " AND ".
  (($project_id=="NULL")?"project_id is NULL":"project_id = $project_id"). " AND ".
  (($event_id=="NULL")?"event_id is NULL":"event_id = $event_id");

  $query = $pdo_conn->prepare($query_string);
  $result = $query->execute();

  if ($result === false)
    return false;
  else
    return true;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Query the database for the best fitting rate for the given user, project and event.
 * 
 * @global array $kga              kimai global array
 * @global array $pdo_conn         PDO connection
 * @author sl
 */
function get_best_fitting_rate($user_id,$project_id,$event_id) {
  global $kga,$pdo_conn;
  $p = $kga['server_prefix'];


  // validate input
  if ($user_id == NULL || !is_numeric($user_id)) $user_id = "NULL";
  if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
  if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";



  $query_string = "SELECT rate FROM ${p}rates WHERE
  (user_id = $user_id OR user_id IS NULL)  AND
  (project_id = $project_id OR project_id IS NULL)  AND
  (event_id = $event_id OR event_id IS NULL)
  ORDER BY user_id DESC, event_id DESC, project_id DESC
  LIMIT 1;";

  $query = $pdo_conn->prepare($query_string);
  $result = $query->execute();

  if ($query->rowCount() == 0)
    return false;

  $data = $query->fetch(PDO::FETCH_ASSOC);
  return $data['rate'];
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Save a new secure key for a user to the database. This key is stored in the users cookie and used
 * to reauthenticate the user.
 * 
 * @global array $kga          kimai global array
 * @global array $conn         MySQL connection
 * @author sl
 */
function loginSetKey($userId,$keymai) {
  global $kga,$pdo_conn;
  $p = $kga['server_prefix'];

  $query = "UPDATE ${p}usr SET secure=?, ban=0, banTime=0 WHERE usr_ID=?;";
  $query = $pdo_conn->prepare($query);
  $query->execute(array($keymai,$userId));
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Update the ban status of a user. This increments the ban counter.
 * Optionally it sets the start time of the ban to the current time.
 * 
 * @global array $kga          kimai global array
 * @global array $conn         MySQL connection
 * @author sl
 */
function loginUpdateBan($userId,$resetTime = false) {
  global $kga,$pdo_conn;
  $p = $kga['server_prefix'];


  $query = "UPDATE ${p}usr SET ban=ban+1 ";
  if ($resetTime)
    $query .= ",banTime = ".time()." ";
  $query .= "WHERE usr_ID = ?";

  $query = $pdo_conn->prepare($query);
  $query->execute(array($userId));
}


?>