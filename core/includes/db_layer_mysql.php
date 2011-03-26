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


/**
 * Add a new customer to the database.
 *
 * @param array $data  name, address and other data of the new customer
 * @global array $kga  kimai-global-array
 * @return int         the knd_ID of the new customer, false on failure
 * @author th
 */
 
function knd_create($data) {
    global $kga, $conn;
    
    $data = clean_data($data);

    $values     ['knd_name']        =     MySQL::SQLValue($data   ['knd_name']          );
    $values     ['knd_comment']     =     MySQL::SQLValue($data   ['knd_comment']       );
    $values     ['knd_password']    =     MySQL::SQLValue($data   ['knd_password']      );
    $values     ['knd_company']     =     MySQL::SQLValue($data   ['knd_company']       );
    $values     ['knd_vat']         =     MySQL::SQLValue($data   ['knd_vat']           );
    $values     ['knd_contact']     =     MySQL::SQLValue($data   ['knd_contact']       );
    $values     ['knd_street']      =     MySQL::SQLValue($data   ['knd_street']        );
    $values     ['knd_zipcode']     =     MySQL::SQLValue($data   ['knd_zipcode']       );
    $values     ['knd_city']        =     MySQL::SQLValue($data   ['knd_city']          );
    $values     ['knd_tel']         =     MySQL::SQLValue($data   ['knd_tel']           );
    $values     ['knd_fax']         =     MySQL::SQLValue($data   ['knd_fax']           );
    $values     ['knd_mobile']      =     MySQL::SQLValue($data   ['knd_mobile']        );
    $values     ['knd_mail']        =     MySQL::SQLValue($data   ['knd_mail']          );
    $values     ['knd_homepage']    =     MySQL::SQLValue($data   ['knd_homepage']      );
    
    $values['knd_visible'] = MySQL::SQLValue($data['knd_visible'] , MySQL::SQLVALUE_NUMBER  );
    $values['knd_filter']  = MySQL::SQLValue($data['knd_filter']  , MySQL::SQLVALUE_NUMBER  );
 
    $table = $kga['server_prefix']."knd";
    $result = $conn->InsertRow($table, $values);
    
    logfile($result);

    if (! $result) {
    	return false;
    } else {
    	return $conn->GetLastInsertID();
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Returns the data of a certain customer
 *
 * @param array $knd_id  knd_id of the customer
 * @global array $kga    kimai-global-array
 * @return array         the customer's data (name, address etc) as array, false on failure
 * @author th
 */
  
function knd_get_data($knd_id) {
    global $kga, $conn;

    $filter['knd_ID'] = MySQL::SQLValue($knd_id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."knd";
    $result = $conn->SelectRows($table, $filter);
    
    if (! $result) {
    	return false;
    } else {
        // return  $conn->getHTML();
        return $conn->RowArray(0,MYSQL_ASSOC);
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Edits a customer by replacing his data by the new array
 *
 * @param array $knd_id  knd_id of the customer to be edited
 * @param array $data    name, address and other new data of the customer
 * @global array $kga    kimai-global-array
 * @return boolean       true on success, false on failure
 * @author ob/th
 */
 
function knd_edit($knd_id, $data) {
    global $kga, $conn;
    
    $data = clean_data($data);

    $values = array();

    $strings = array(
      'knd_name'    ,'knd_comment','knd_password' ,'knd_company','knd_vat',
      'knd_contact' ,'knd_street' ,'knd_zipcode'  ,'knd_city'   ,'knd_tel',
      'knd_fax'     ,'knd_mobile' ,'knd_mail'     ,'knd_homepage');
    foreach ($strings as $key) {
      if (isset($data[$key]))
        $values[$key] = MySQL::SQLValue($data[$key]);
    }

    $numbers = array('knd_visible','knd_filter');
    foreach ($numbers as $key) {
      if (isset($data[$key]))
        $values[$key] = MySQL::SQLValue($data[$key] , MySQL::SQLVALUE_NUMBER );
    }

    $filter['knd_ID']       = MySQL::SQLValue($knd_id, MySQL::SQLVALUE_NUMBER);
    
    $table = $kga['server_prefix']."knd";
    $query = MySQL::BuildSQLUpdate($table, $values, $filter);
    
    return $conn->Query($query);
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Assigns a customer to 1-n groups by adding entries to the cross table
 *
 * @param int $knd_id         knd_id of the customer to which the groups will be assigned
 * @param array $grp_array    contains one or more grp_IDs
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob/th
 */

// checked  
 
function assign_knd2grps($knd_id, $grp_array) {
    global $kga, $conn;
    
    if (! $conn->TransactionBegin()) $conn->Kill();
    
    $table = $kga['server_prefix']."grp_knd";
    $filter['knd_ID'] = MySQL::SQLValue($knd_id, MySQL::SQLVALUE_NUMBER);
    $d_query = MySQL::BuildSQLDelete($table, $filter);
    $d_result = $conn->Query($d_query);
    
    if ($d_result == false) {
            $conn->TransactionRollback();
            return false;
    }

    foreach ($grp_array as $current_grp) {
        $values['grp_ID'] = MySQL::SQLValue($current_grp , MySQL::SQLVALUE_NUMBER);
        $values['knd_ID'] = MySQL::SQLValue($knd_id      , MySQL::SQLVALUE_NUMBER);
        $query = MySQL::BuildSQLInsert($table, $values);
        $result = $conn->Query($query);            

        if ($result == false) {
                $conn->TransactionRollback();
                return false;
        }
    }

    if ($conn->TransactionEnd() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the groups of the given customer
 *
 * @param array $knd_id  knd_id of the customer
 * @global array $kga    kimai-global-array
 * @return array         contains the grp_IDs of the groups or false on error
 * @author th
 */
 
// checked 
  
function knd_get_grps($knd_id) {
    global $kga, $conn;

    $filter['knd_ID'] = MySQL::SQLValue($knd_id, MySQL::SQLVALUE_NUMBER);
    $columns[]        = "grp_ID";
    $table = $kga['server_prefix']."grp_knd";
    
    $result = $conn->SelectRows($table, $filter, $columns);
    if ($result == false) {
        return false;
    }

    $return_grps = array();
    $counter     = 0;
    
    $rows = $conn->RecordsArray(MYSQL_ASSOC);
    
    if ($conn->RowCount()) {
        foreach ($rows as $current_grp) {
            $return_grps[$counter] = $current_grp['grp_ID'];
            $counter++;   
        }
        return $return_grps;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * deletes a customer
 *
 * @param array $knd_id  knd_id of the customer
 * @global array $kga    kimai-global-array
 * @return boolean       true on success, false on failure
 * @author th
 */

// not implemented yet 

function knd_delete($knd_id) {
    global $kga, $conn;

    $values['knd_trash'] = 1;    
    $filter['knd_ID'] = MySQL::SQLValue($knd_id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."knd";
        
    $query = MySQL::BuildSQLUpdate($table, $values, $filter);
    return $conn->Query($query);
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Adds a new project
 *
 * @param array $data  name, comment and other data of the new project
 * @global array $kga  kimai-global-array
 * @return int         the pct_ID of the new project, false on failure
 * @author th
 */
 
// checked 

function pct_create($data) {
    global $kga, $conn;
    
    $data = clean_data($data);
        
    $values['pct_name']    = MySQL::SQLValue($data['pct_name']    );
    $values['pct_comment'] = MySQL::SQLValue($data['pct_comment'] );
    $values['pct_budget']  = MySQL::SQLValue($data['pct_budget']  , MySQL::SQLVALUE_NUMBER );
    $values['pct_kndID']   = MySQL::SQLValue($data['pct_kndID']   , MySQL::SQLVALUE_NUMBER );
    $values['pct_visible'] = MySQL::SQLValue($data['pct_visible'] , MySQL::SQLVALUE_NUMBER );
    $values['pct_internal']= MySQL::SQLValue($data['pct_internal'], MySQL::SQLVALUE_NUMBER );
    $values['pct_filter']  = MySQL::SQLValue($data['pct_filter']  , MySQL::SQLVALUE_NUMBER );
    
    $table = $kga['server_prefix']."pct";
    $result = $conn->InsertRow($table, $values);
     
    if (! $result)
    	return false;

   	$pct_id = $conn->GetLastInsertID();

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
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Returns the data of a certain project
 *
 * @param array $pct_id  pct_id of the project
 * @global array $kga    kimai-global-array
 * @return array         the project's data (name, comment etc) as array, false on failure
 * @author th
 */
 
// checked 
  
function pct_get_data($pct_id) {
    global $kga, $conn;

    if (!is_numeric($pct_id)) {
        return false;
    }

    $filter['pct_ID'] = MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."pct";
    $result = $conn->SelectRows($table, $filter);

    if (! $result)
    	return false;

    $result_array = $conn->RowArray(0,MYSQL_ASSOC);
    $result_array['pct_default_rate'] = get_rate(NULL,$pct_id,NULL);
    $result_array['pct_my_rate'] = get_rate($kga['usr']['usr_ID'],$pct_id,NULL);
    return $result_array;

}

// -----------------------------------------------------------------------------------------------------------

/**
 * Edits a project by replacing its data by the new array
 *
 * @param array $pct_id   pct_id of the project to be edited
 * @param array $data     name, comment and other new data of the project
 * @global array $kga     kimai-global-array
 * @return boolean        true on success, false on failure
 * @author ob/th
 */

// checked 

function pct_edit($pct_id, $data) {
    global $kga, $conn;
    
    $data = clean_data($data);

    $strings = array('pct_name', 'pct_comment');
    foreach ($strings as $key) {
      if (isset($data[$key]))
        $values[$key] = MySQL::SQLValue($data[$key]);
    }

    $numbers = array(
        'pct_budget', 'pct_kndID', 'pct_visible', 'pct_internal', 'pct_filter');
    foreach ($numbers as $key) {
      if (isset($data[$key]))
        $values[$key] = MySQL::SQLValue($data[$key] , MySQL::SQLVALUE_NUMBER );
    }

    $filter ['pct_ID'] = MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."pct";


    if (! $conn->TransactionBegin()) $conn->Kill();

    $query = MySQL::BuildSQLUpdate($table, $values, $filter);
    
    if ($conn->Query($query)) {
    
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
    
        if (! $conn->TransactionEnd()) $conn->Kill();
        return true;
    } else {
        if (! $conn->TransactionRollback()) $conn->Kill();
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
 * @author ob/th
 */
 
// checked 

function assign_pct2grps($pct_id, $grp_array) {
    global $kga, $conn;
    
    if (! $conn->TransactionBegin()) $conn->Kill();

    $table = $kga['server_prefix']."grp_pct";
    $filter['pct_ID'] = MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER);
    $d_query = MySQL::BuildSQLDelete($table, $filter);
    $d_result = $conn->Query($d_query);    
    
    if ($d_result == false) {
            $conn->TransactionRollback();
            return false;
    }

    foreach ($grp_array as $current_grp) {
        
      $values['grp_ID']   = MySQL::SQLValue($current_grp , MySQL::SQLVALUE_NUMBER);
      $values['pct_ID']   = MySQL::SQLValue($pct_id      , MySQL::SQLVALUE_NUMBER);
      $query = MySQL::BuildSQLInsert($table, $values);
      $result = $conn->Query($query);
      
      if ($result == false) {
              $conn->TransactionRollback();
              return false;
      }
    }

    if ($conn->TransactionEnd() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the groups of the given project
 *
 * @param array $pct_id  pct_id of the project
 * @global array $kga    kimai-global-array
 * @return array         contains the grp_IDs of the groups or false on error
 * @author th
 */
 
// checked 
  
function pct_get_grps($pct_id) {
    global $kga, $conn;

    $filter['pct_ID'] = MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER);
    $columns[]        = "grp_ID";
    $table = $kga['server_prefix']."grp_pct";

    $result = $conn->SelectRows($table, $filter, $columns);
    if ($result == false) {
        return false;
    }

    $return_grps = array();
    $counter     = 0;

    $rows = $conn->RecordsArray(MYSQL_ASSOC);

    if ($conn->RowCount()) {
        foreach ($rows as $current_grp) {
            $return_grps[$counter] = $current_grp['grp_ID'];
            $counter++;   
        }
        return $return_grps;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * deletes a project
 *
 * @param array $pct_id  pct_id of the project
 * @global array $kga    kimai-global-array
 * @return boolean       true on success, false on failure
 * @author th
 */

function pct_delete($pct_id) {
    global $kga, $conn;

    $values['pct_trash'] = 1;    
    $filter['pct_ID'] = MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."pct";
        
    $query = MySQL::BuildSQLUpdate($table, $values, $filter);
    return $conn->Query($query);
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Adds a new event
 *
 * @param array $data   name, comment and other data of the new event
 * @global array $kga   kimai-global-array
 * @return int          the evt_ID of the new project, false on failure
 * @author th
 */

// checked 

function evt_create($data) {
    global $kga, $conn;

    $data = clean_data($data);
    
    $values['evt_name']    = MySQL::SQLValue($data['evt_name']    );
    $values['evt_comment'] = MySQL::SQLValue($data['evt_comment'] );
    $values['evt_visible'] = MySQL::SQLValue($data['evt_visible'] , MySQL::SQLVALUE_NUMBER );
    $values['evt_filter']  = MySQL::SQLValue($data['evt_filter']  , MySQL::SQLVALUE_NUMBER );

    $table = $kga['server_prefix']."evt";
    $result = $conn->InsertRow($table, $values);

    if (! $result)
    	return false;

  	$evt_id = $conn->GetLastInsertID();
    
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
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Returns the data of a certain task
 *
 * @param array $evt_id  evt_id of the project
 * @global array $kga    kimai-global-array
 * @return array         the event's data (name, comment etc) as array, false on failure
 * @author th
 */

// checked 

function evt_get_data($evt_id) {
    global $kga, $conn;

    $filter['evt_ID'] = MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."evt";
    $result = $conn->SelectRows($table, $filter);

    if (! $result)
    	return false;


    $result_array = $conn->RowArray(0,MYSQL_ASSOC);

    $result_array['evt_default_rate'] = get_rate(NULL,NULL,$result_array['evt_ID']);
    $result_array['evt_my_rate'] = get_rate($kga['usr']['usr_ID'],NULL,$result_array['evt_ID']);

    return $result_array;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Edits an event by replacing its data by the new array
 *
 * @param array $evt_id  evt_id of the project to be edited
 * @param array $data    name, comment and other new data of the event
 * @global array $kga    kimai-global-array
 * @return boolean       true on success, false on failure
 * @author th
 */

// checked 

function evt_edit($evt_id, $data) {
    global $kga, $conn;
    
    $data = clean_data($data);
    

    $strings = array('evt_name', 'evt_comment');
    foreach ($strings as $key) {
      if (isset($data[$key]))
        $values[$key] = MySQL::SQLValue($data[$key]);
    }

    $numbers = array('evt_visible', 'evt_filter');
    foreach ($numbers as $key) {
      if (isset($data[$key]))
        $values[$key] = MySQL::SQLValue($data[$key] , MySQL::SQLVALUE_NUMBER );
    }

    $filter  ['evt_ID']          =   MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."evt";

    if (! $conn->TransactionBegin()) $conn->Kill();

    $query = MySQL::BuildSQLUpdate($table, $values, $filter);
    
    if ($conn->Query($query)) {

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

        if (! $conn->TransactionEnd()) $conn->Kill();
        return true;
    } else {
        if (! $conn->TransactionRollback()) $conn->Kill();
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Assigns an event to 1-n groups by adding entries to the cross table
 *
 * @param int $evt_id         evt_id of the project to which the groups will be assigned
 * @param array $grp_array    contains one or more grp_IDs
 * @global array $kga         kimai-global-array
 * @return boolean            true on success, false on failure
 * @author ob/th
 */
 
// checked 

function assign_evt2grps($evt_id, $grp_array) {
    global $kga, $conn;
    
    if (! $conn->TransactionBegin()) $conn->Kill();        

    $table = $kga['server_prefix']."grp_evt";
    $filter['evt_ID'] = MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER);
    $d_query = MySQL::BuildSQLDelete($table, $filter);
    $d_result = $conn->Query($d_query);    

    if ($d_result == false) {
        $conn->TransactionRollback();
        return false;
    }

    foreach ($grp_array as $current_grp) {
      $values['grp_ID'] = MySQL::SQLValue($current_grp , MySQL::SQLVALUE_NUMBER);
      $values['evt_ID'] = MySQL::SQLValue($evt_id      , MySQL::SQLVALUE_NUMBER);
      $query = MySQL::BuildSQLInsert($table, $values);
      $result = $conn->Query($query);            
      
      if ($result == false) {
          $conn->TransactionRollback();
          return false;
      }
    }
    
    if ($conn->TransactionEnd() == true) {
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
    global $kga, $conn;
    
    if (! $conn->TransactionBegin()) $conn->Kill();        

    $table = $kga['server_prefix']."pct_evt";
    $filter['evt_ID'] = MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER);
    $d_query = MySQL::BuildSQLDelete($table, $filter);
    $d_result = $conn->Query($d_query);    

    if ($d_result == false) {
        $conn->TransactionRollback();
        return false;
    }

    foreach ($pct_array as $current_pct) {
      $values['pct_ID'] = MySQL::SQLValue($current_pct , MySQL::SQLVALUE_NUMBER);
      $values['evt_ID'] = MySQL::SQLValue($evt_id      , MySQL::SQLVALUE_NUMBER);
      $query = MySQL::BuildSQLInsert($table, $values);
      $result = $conn->Query($query);            
      
      if ($result == false) {
          $conn->TransactionRollback();
          return false;
      }
    }
    
    if ($conn->TransactionEnd() == true) {
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
    global $kga, $conn;
    
    if (! $conn->TransactionBegin()) $conn->Kill();        

    $table = $kga['server_prefix']."pct_evt";
    $filter['pct_ID'] = MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER);
    $d_query = MySQL::BuildSQLDelete($table, $filter);
    $d_result = $conn->Query($d_query);    

    if ($d_result == false) {
        $conn->TransactionRollback();
        return false;
    }

    foreach ($evt_array as $current_evt) {
      $values['evt_ID'] = MySQL::SQLValue($current_evt , MySQL::SQLVALUE_NUMBER);
      $values['pct_ID'] = MySQL::SQLValue($pct_id      , MySQL::SQLVALUE_NUMBER);
      $query = MySQL::BuildSQLInsert($table, $values);
      $result = $conn->Query($query);            
      
      if ($result == false) {
          $conn->TransactionRollback();
          return false;
      }
    }
    
    if ($conn->TransactionEnd() == true) {
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
    global $kga, $conn;

    $filter ['evt_ID'] = MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER);
    $columns[]         = "pct_ID";
    $table = $kga['server_prefix']."pct_evt";
    
    $result = $conn->SelectRows($table, $filter, $columns);
    if ($result == false) {
        return false;
    }

    $return_grps = array();
    $counter     = 0;
    
    $rows = $conn->RecordsArray(MYSQL_ASSOC);
    
    if ($conn->RowCount()) {
        foreach ($rows as $current_grp) {
            $return_grps[$counter] = $current_grp['pct_ID'];
            $counter++;   
        }
        return $return_grps;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the events which were assigned to a project
 *
 * @param integer $pct_id  pct_id of the project
 * @global array $kga    kimai-global-array
 * @return array         contains the evt_IDs of the events or false on error
 * @author sl
 */
 
function pct_get_evts($pct_id) {
    global $kga, $conn;

    $filter ['pct_ID'] = MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER);
    $columns[]         = "evt_ID";
    $table = $kga['server_prefix']."pct_evt";
    
    $result = $conn->SelectRows($table, $filter, $columns);
    if ($result == false) {
        return false;
    }

    $return_evts = array();
    $counter     = 0;
    
    $rows = $conn->RecordsArray(MYSQL_ASSOC);
    
    if ($conn->RowCount()) {
        foreach ($rows as $current_evt) {
            $return_evts[$counter] = $current_evt['evt_ID'];
            $counter++;   
        }
        return $return_evts;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the groups of the given event
 *
 * @param array $evt_id  evt_id of the project
 * @global array $kga    kimai-global-array
 * @return array         contains the grp_IDs of the groups or false on error
 * @author th
 */
 
// checked 
 
function evt_get_grps($evt_id) {
    global $kga, $conn;

    $filter ['evt_ID'] = MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER);
    $columns[]         = "grp_ID";
    $table = $kga['server_prefix']."grp_evt";
    
    $result = $conn->SelectRows($table, $filter, $columns);
    if ($result == false) {
        return false;
    }

    $return_grps = array();
    $counter     = 0;
    
    $rows = $conn->RecordsArray(MYSQL_ASSOC);
    
    if ($conn->RowCount()) {
        foreach ($rows as $current_grp) {
            $return_grps[$counter] = $current_grp['grp_ID'];
            $counter++;   
        }
        return $return_grps;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * deletes an event
 *
 * @param array $evt_id  evt_id of the event
 * @global array $kga    kimai-global-array
 * @return boolean       true on success, false on failure
 * @author th
 */

// not implemented yet 

function evt_delete($evt_id) {
    global $kga, $conn;

    $values['evt_trash'] = 1;    
    $filter['evt_ID'] = MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."evt";
        
    $query = MySQL::BuildSQLUpdate($table, $values, $filter);
    return $conn->Query($query);
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
 * @author ob/th
 */



function assign_grp2knds($grp_id, $knd_array) {
    global $kga, $conn;
    
    if (! $conn->TransactionBegin()) $conn->Kill();    

    $table = $kga['server_prefix']."grp_knd";
    $filter['grp_ID'] = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
    $d_query = MySQL::BuildSQLDelete($table, $filter);

    $d_result = $conn->Query($d_query);    

    if ($d_result == false) {
            $conn->TransactionRollback();
            return false;
    }
    
    foreach ($knd_array as $current_knd) {
      $values['grp_ID']       = MySQL::SQLValue($grp_id      , MySQL::SQLVALUE_NUMBER);
      $values['knd_ID']       = MySQL::SQLValue($current_knd , MySQL::SQLVALUE_NUMBER);
      $query = MySQL::BuildSQLInsert($table, $values);
      $result = $conn->Query($query);            
      
      if ($result == false) {
              $conn->TransactionRollback();
              return false;
      }
    }
    
    if ($conn->TransactionEnd() == true) {
        return true;
    } else {
        return false;
    }
}

//-----------------------------------------------------------------------------------------------------------

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
    global $kga, $conn;
    
    if (! $conn->TransactionBegin()) $conn->Kill();    

    $table = $kga['server_prefix']."grp_pct";
    $filter['grp_ID'] = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
    $d_query = MySQL::BuildSQLDelete($table, $filter);
    $d_result = $conn->Query($d_query);    

    if ($d_result == false) {
            $conn->TransactionRollback();
            return false;
    }
    
    foreach ($pct_array as $current_pct) {
      $values['grp_ID'] = MySQL::SQLValue($grp_id      , MySQL::SQLVALUE_NUMBER);
      $values['pct_ID'] = MySQL::SQLValue($current_pct , MySQL::SQLVALUE_NUMBER);
      $query = MySQL::BuildSQLInsert($table, $values);
      $result = $conn->Query($query);            

      if ($result == false) {
          $conn->TransactionRollback();
          return false;
      }
    }

    if ($conn->TransactionEnd() == true) {
        return true;
    } else {
        return false;
    }
}

//-----------------------------------------------------------------------------------------------------------

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
    global $kga, $conn;
    
    if (! $conn->TransactionBegin()) $conn->Kill();   

    $table = $kga['server_prefix']."grp_evt";
    $filter['grp_ID'] = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
    $d_query = MySQL::BuildSQLDelete($table, $filter);
    $d_result = $conn->Query($d_query);    

    if ($d_result == false) {
        $conn->TransactionRollback();
        return false;
    }

    foreach ($evt_array as $current_evt) {
      $values['grp_ID'] = MySQL::SQLValue($grp_id      , MySQL::SQLVALUE_NUMBER);
      $values['evt_ID'] = MySQL::SQLValue($current_evt , MySQL::SQLVALUE_NUMBER);
      $query = MySQL::BuildSQLInsert($table, $values);
      $result = $conn->Query($query);            

      if ($result == false) {
          $conn->TransactionRollback();
          return false;
      }
    }

    if ($conn->TransactionEnd() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the customers of the given group
 *
 * @param array $grp_id  grp_id of the group
 * @global array $kga    kimai-global-array
 * @return array         contains the knd_IDs of the groups or false on error
 * @author th
 */
 
// checked 

function grp_get_knds($grp_id) {
    global $kga, $conn;

    $grp_id = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
    $p = $kga['server_prefix'];
    
    $query = "SELECT knd_ID FROM ${p}grp_knd
     JOIN ${p}knd USING (knd_ID)
     WHERE ${p}knd.knd_trash = 0 AND grp_ID = ?;";
    
    $result = $conn->Query($query);
    if ($result == false) {
        return false;
    }
    
    $rows = $conn->RecordsArray(MYSQL_ASSOC);
    
    $return_knds = array();
    $counter     = 0;
    if ($conn->RowCount()) {
        foreach ($rows as $current_knd) {
            $return_knds[$counter] = $current_knd['knd_ID'];
            $counter++;   
        }
        return $return_knds;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the projects of the given group
 *
 * @param array $grp_id  grp_id of the group
 * @global array $kga    kimai-global-array
 * @return array         contains the pct_IDs of the groups or false on error
 * @author th
 */
 
// checked 

function grp_get_pcts($grp_id) {
    global $kga, $conn;

    $grp_id = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
    $p = $kga['server_prefix'];
    
    $query = "SELECT pct_ID FROM ${p}grp_pct
     JOIN ${p}pct USING(pct_ID)
     WHERE ${p}evt.evt_trash=0 AND grp_ID = $grp_id;";
    
    $result = $conn->Query($query);
    if ($result == false) {
        return false;
    }

    $return_pcts = array();
    $counter     = 0;

    $rows = $conn->RecordsArray(MYSQL_ASSOC);

    if ($conn->RowCount()) {
        foreach ($rows as $current_pct) {
            $return_pcts[$counter] = $current_pct['pct_ID'];
            $counter++;
        }
        return $return_pcts;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the events of the given group
 *
 * @param array $grp_id  grp_id of the group
 * @global array $kga    kimai-global-array
 * @return array         contains the evt_IDs of the groups or false on error
 * @author th
 */
 
// checked 
  
function grp_get_evts($grp_id) {
    global $kga, $conn;

    $grp_id = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
    $p = $kga['server_prefix'];
    
    $query = "SELECT evt_ID FROM ${p}grp_evt
     JOIN ${p}evt USING(evt_ID)
     WHERE ${p}evt.evt_trash=0 AND ${p}grp_evt.grp_ID = $grp_id;";

    $result = $conn->Query($query);
    if ($result == false) {
        return false;
    }

    $return_evts = array();
    $counter     = 0;

    $rows = $conn->RecordsArray(MYSQL_ASSOC);

    if ($conn->RowCount()) {
        foreach ($rows as $current_evt) {
            $return_evts[$counter] = $current_evt['evt_ID'];
            $counter++;   
        }
        return $return_evts;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Adds a new user
 *
 * @param array $data  username, email, and other data of the new user
 * @global array $kga  kimai-global-array
 * @return boolean     true on success, false on failure
 * @author th
 */
 
// checked (cleanup!!!)

function usr_create($data) {
    global $kga, $conn;

    // find random but unused user id
    do {
      $data['usr_ID'] = random_number(9);
    } while (usr_get_data($data['usr_ID']));
    
    $data = clean_data($data);

    $values ['usr_name']     =  MySQL::SQLValue($data ['usr_name']  );
    $values ['usr_ID']       =  MySQL::SQLValue($data ['usr_ID']      , MySQL::SQLVALUE_NUMBER  );
    $values ['usr_grp']      =  MySQL::SQLValue($data ['usr_grp']     , MySQL::SQLVALUE_NUMBER  );
    $values ['usr_sts']      =  MySQL::SQLValue($data ['usr_sts']     , MySQL::SQLVALUE_NUMBER  );
    $values ['usr_active']   =  MySQL::SQLValue($data ['usr_active']  , MySQL::SQLVALUE_NUMBER  );
                                                      
    $table  = $kga['server_prefix']."usr";
    $result = $conn->InsertRow($table, $values);


    if ($result===false) {
      return false;
    }
    else {
        if (isset($data['usr_rate'])) {
          if (is_numeric($data['usr_rate']))
            save_rate($usr_id,NULL,NULL,$data['usr_rate']);
          else
            remove_rate($usr_id,NULL,NULL);
        }
        return $data['usr_ID'];
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Returns the data of a certain user
 *
 * @param array $usr_id  knd_id of the user
 * @global array $kga    kimai-global-array
 * @return array         the user's data (username, email-address, status etc) as array, false on failure
 * @author th
 */

// checked 

function usr_get_data($usr_id) {
    global $kga, $conn;

    $filter['usr_ID'] = MySQL::SQLValue($usr_id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."usr";
    $result = $conn->SelectRows($table, $filter);

    if (! $result) {
    	return false;
    } else {
        // return  $conn->getHTML();
        return $conn->RowArray(0,MYSQL_ASSOC);
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Edits a user by replacing his data and preferences by the new array
 *
 * @param array $usr_id  usr_id of the user to be edited
 * @param array $data    username, email, and other new data of the user
 * @global array $kga    kimai-global-array
 * @return boolean       true on success, false on failure
 * @author ob/th
 */
function usr_edit($usr_id, $data) {
    global $kga, $conn;
    
    $data = clean_data($data);

    $strings = array('usr_name', 'usr_mail', 'usr_alias', 'pw');
    foreach ($strings as $key) {
      if (isset($data[$key]))
        $values[$key] = MySQL::SQLValue($data[$key]);
    }

    $numbers = array(
          'usr_grp'     ,'usr_sts'   ,'usr_trash' ,'usr_active',
          'lastProject' ,'lastEvent' ,'lastRecord');
    foreach ($numbers as $key) {
      if (isset($data[$key]))
        $values[$key] = MySQL::SQLValue($data[$key] , MySQL::SQLVALUE_NUMBER );
    }

    $filter ['usr_ID']            = MySQL::SQLValue($usr_id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."usr";
    
    if (! $conn->TransactionBegin()) $conn->Kill();

    $query = MySQL::BuildSQLUpdate($table, $values, $filter);

    if ($conn->Query($query)) {

        if (isset($data['usr_rate'])) {
          if (is_numeric($data['usr_rate']))
            save_rate($usr_id,NULL,NULL,$data['usr_rate']);
          else 
            remove_rate($usr_id,NULL,NULL);
        }

        if (! $conn->TransactionEnd()) $conn->Kill();

        return true;
    } else {
        if (! $conn->TransactionRollback()) $conn->Kill();

        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * deletes a user
 *
 * @param array $usr_id  usr_id of the user
 * @global array $kga    kimai-global-array
 * @return boolean       true on success, false on failure
 * @author th
 */

function usr_delete($usr_id) {
    global $kga, $conn;
    
    $values['usr_trash'] = 1;    
    $filter['usr_ID'] = MySQL::SQLValue($usr_id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."usr";
        
    $query = MySQL::BuildSQLUpdate($table, $values, $filter);
    return $conn->Query($query);
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
    global $kga, $conn;

    if ($userId === null)
      $userId = $kga['usr']['usr_ID'];

    $table  = $kga['server_prefix']."preferences";
    $userId = MySQL::SQLValue($userId,  MySQL::SQLVALUE_NUMBER);
    $key    = MySQL::SQLValue($key);
    
    $query = "SELECT var,value FROM $table WHERE userID = $userId AND var = $key";
    
    $conn->Query($query);

    if ($conn->RowCount() == 0)
      return null;

    if ($conn->RowCount() == 1) {
      $row = $conn->RowArray(0,MYSQL_NUM);
      return $row[1];
    }    
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
    global $kga, $conn;

    if ($userId === null)
      $userId = $kga['usr']['usr_ID'];

    $table  = $kga['server_prefix']."preferences";
    $userId = MySQL::SQLValue($userId,  MySQL::SQLVALUE_NUMBER);
    
    $preparedKeys = array();
    foreach ($keys as $key)
      $preparedKeys[] = MySQL::SQLValue($key);

    $keysString = implode(",",$preparedKeys);
    
    $query = "SELECT var,value FROM $table WHERE userID = $userId AND var IN ($keysString)";
    
    $conn->Query($query);

    $preferences = array();

    while (!$conn->EndOfSeek()) {
      $row = $conn->RowArray();
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
    global $kga, $conn;

    if ($userId === null)
      $userId = $kga['usr']['usr_ID'];

    $prefixLength = strlen($prefix);

    $table  = $kga['server_prefix']."preferences";
    $userId = MySQL::SQLValue($userId,  MySQL::SQLVALUE_NUMBER);
    $prefix = MySQL::SQLValue($prefix.'%');
    
    $query = "SELECT var,value FROM $table WHERE userID = $userId AND var LIKE $prefix";
    $conn->Query($query);

    $preferences = array();

    while (!$conn->EndOfSeek()) {
      $row = $conn->RowArray();
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
    global $kga, $conn;

    if ($userId === null)
      $userId = $kga['usr']['usr_ID'];
    
    if (! $conn->TransactionBegin()) $conn->Kill();  

    $table  = $kga['server_prefix']."preferences";

    $filter['userID']  = MySQL::SQLValue($userId,  MySQL::SQLVALUE_NUMBER);
    $values['userID']  = $filter['userID'];
    foreach ($data as $key=>$value) {
      $values['var']   = MySQL::SQLValue($prefix.$key);
      $values['value'] = MySQL::SQLValue($value);
      $filter['var']   = $values['var'];

      $conn->AutoInsertUpdate($table, $values, $filter);
    }

    return $conn->TransactionEnd();
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
    global $kga, $conn;
    
    if (! $conn->TransactionBegin()) $conn->Kill();    
    
    $table = $kga['server_prefix']."ldr";
    $filter['grp_leader'] = MySQL::SQLValue($ldr_id, MySQL::SQLVALUE_NUMBER);
    $query = MySQL::BuildSQLDelete($table, $filter);
    
    $d_result = $conn->Query($query);    
    
    if ($d_result == false) {
            $conn->TransactionRollback();
            return false;
    }
    
    foreach ($ldr_array as $current_grp) {
      $values['grp_ID']       = MySQL::SQLValue($current_grp , MySQL::SQLVALUE_NUMBER);
      $values['grp_leader']   = MySQL::SQLValue($ldr_id      , MySQL::SQLVALUE_NUMBER);
      $query = MySQL::BuildSQLInsert($table, $values);

      $result = $conn->Query($query);
      
      if ($result == false) {
              $conn->TransactionRollback();
              return false;
      }
    }

    update_leader_status();

    if ($conn->TransactionEnd() == true) {
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
    global $kga, $conn;
    
    if (! $conn->TransactionBegin()) $conn->Kill();

    $table = $kga['server_prefix']."ldr";
    $filter['grp_ID'] = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
    $query = MySQL::BuildSQLDelete($table, $filter);
    
    $d_result = $conn->Query($query);    
    
    if ($d_result == false) {
            $conn->TransactionRollback();
            return false;
    }
    
    foreach ($ldr_array as $current_ldr) {
      $values['grp_ID']       = MySQL::SQLValue($grp_id      , MySQL::SQLVALUE_NUMBER);
      $values['grp_leader']   = MySQL::SQLValue($current_ldr , MySQL::SQLVALUE_NUMBER);
      $query = MySQL::BuildSQLInsert($table, $values);

      $result = $conn->Query($query);
      
      if ($result == false) {
              $conn->TransactionRollback();
              return false;
      }
    }

    update_leader_status();
    
    if ($conn->TransactionEnd() == true) {
        return true;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the groups of the given group leader
 *
 * @param array $ldr_id  usr_id of the group leader
 * @global array $kga    kimai-global-array
 * @return array         contains the grp_IDs of the groups or false on error
 * @author th
 */
function ldr_get_grps($ldr_id) {
    global $kga, $conn;
    
    $filter['grp_leader'] = MySQL::SQLValue($ldr_id, MySQL::SQLVALUE_NUMBER);
    $columns[]            = "grp_ID";
    $table = $kga['server_prefix']."ldr";
    
    $result = $conn->SelectRows($table, $filter, $columns);
    if ($result == false) {
        return false;
    }
 
    $return_grps = array();
    $counter = 0;

    $rows = $conn->RowArray(0,MYSQL_ASSOC);
    
    if ($conn->RowCount()) {
        foreach ($rows as $current_grp) {
            $return_grps[$counter] = $current_grp['grp_ID'];
            $counter++;   
        }
        return $return_grps;
    } else {
        return false;
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns all the group leaders of the given group
 *
 * @param array $grp_id  grp_id of the group
 * @global array $kga    kimai-global-array
 * @return array         contains the usr_IDs of the group's group leaders or false on error
 * @author th
 */
 
// checked 

function grp_get_ldrs($grp_id) {
    global $kga, $conn;

    $grp_id = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
    $p = $kga['server_prefix'];

    $query = "SELECT grp_leader FROM ${p}ldr
    JOIN ${p}usr ON ${p}usr.usr_ID = ${p}ldr.grp_leader WHERE grp_ID = $grp_id AND usr_trash=0;";
        
    $result = $conn->Query($query);
    if ($result == false) {
        return false;
    }
    
    $return_ldrs = array();
    $counter     = 0;
    
    $rows = $conn->RowArray(0,MYSQL_ASSOC);
    
    if ($conn->RowCount()) {
        $conn->MoveFirst();
        while (! $conn->EndOfSeek()) {
            $row = $conn->Row();
            $return_ldrs[$counter] = $row->grp_leader;
            $counter++; 
        }
        return $return_ldrs;
    } else {
        return array();
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Adds a new group
 *
 * @param array $data  name and other data of the new group
 * @global array $kga  kimai-global-array
 * @return int         the grp_id of the new group, false on failure
 * @author th
 */
function grp_create($data) {
    global $kga, $conn;
    
    $data = clean_data($data);
    
    $values ['grp_name']   = MySQL::SQLValue($data ['grp_name'] );
    $table = $kga['server_prefix']."grp";
    $result = $conn->InsertRow($table, $values);

    if (! $result) {
    	return false;
    } else {
    	return $conn->GetLastInsertID();
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Returns the data of a certain group
 *
 * @param array $grp_id  grp_id of the group
 * @global array $kga    kimai-global-array
 * @return array         the group's data (name, leader ID, etc) as array, false on failure
 * @author th
 */
 
// checked  

function grp_get_data($grp_id) {
    global $kga, $conn;
    
    $filter['grp_ID'] = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."grp";
    $result = $conn->SelectRows($table, $filter);    
    
    if (! $result) {
    	return false;
    } else {
        return $conn->RowArray(0,MYSQL_ASSOC);
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Returns the number of users in a certain group
 *
 * @param array $grp_id   grp_id of the group
 * @global array $kga     kimai-global-array
 * @return int            the number of users in the group
 * @author th
 */
 
// checked 

function grp_count_users($grp_id) {
    global $kga, $conn;
    $filter['usr_grp'] = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
    $filter['usr_trash'] = 0;
    $table = $kga['server_prefix']."usr";
    $result = $conn->SelectRows($table, $filter);
    return $conn->RowCount()===false?0:$conn->RowCount();
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Edits a group by replacing its data by the new array
 *
 * @param array $grp_id  grp_id of the group to be edited
 * @param array $data    name and other new data of the group
 * @global array $kga    kimai-global-array
 * @return boolean       true on success, false on failure
 * @author th
 */
function grp_edit($grp_id, $data) {
    global $kga, $conn;
    
    $data = clean_data($data);
   
    $values ['grp_name'] = MySQL::SQLValue($data ['grp_name'] );

    $filter ['grp_ID']   = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."grp";

    $query = MySQL::BuildSQLUpdate($table, $values, $filter);
   
    return $conn->Query($query);
}

// -----------------------------------------------------------------------------------------------------------

/**
 * deletes a group
 *
 * @param array $grp_id  grp_id of the group
 * @global array $kga    kimai-global-array
 * @return boolean       true on success, false on failure
 * @author th
 */
function grp_delete($grp_id) {
    global $kga, $conn;
    $values['grp_trash'] = 1;    
    $filter['grp_ID'] = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."grp";
    $query = MySQL::BuildSQLUpdate($table, $values, $filter);
    return $conn->Query($query);
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Returns all configuration variables
 *
 * @global array $kga  kimai-global-array
 * @return array       array with the vars from the var table
 * @author th
 */
 
// checked 

function var_get_data() {
    global $kga, $conn;

    $table = $kga['server_prefix']."var";
    $result = $conn->SelectRows($table);

    $var_data = array();

    $conn->MoveFirst();
    while (! $conn->EndOfSeek()) {
        $row = $conn->Row();
        $var_data[$row->var] = $row->value; 
    }

    return $var_data;
}

// -----------------------------------------------------------------------------------------------------------
/**
 * Edits a configuration variables by replacing the data by the new array
 *
 * @param array $data    variables array
 * @global array $kga    kimai-global-array
 * @return boolean       true on success, false on failure
 * @author ob
 */
function var_edit($data) {
    global $kga, $conn;
    
	$data = clean_data($data);
	
    $table = $kga['server_prefix']."var";
    
    if (! $conn->TransactionBegin()) $conn->Kill();

    foreach ($data as $key => $value) {
      $filter['var'] = MySQL::SQLValue($key);
      $values ['value'] = MySQL::SQLValue($value);

      $query = MySQL::BuildSQLUpdate($table, $values, $filter);

      $result = $conn->Query($query);
      
      if ($result === false) {
          return false;
      }
    }
    
    if (! $conn->TransactionEnd()) $conn->Kill();
    
    return true;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * checks whether there is a running zef-entry for a given user
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return boolean true=there is an entry, false=there is none (actually 1 or 0 is returnes as number!)
 * @author ob/th
 */

// checked 

function get_rec_state($usr_id) {
    global $kga, $conn;
    $p = $kga['server_prefix'];
    $usr_id = MySQL::SQLValue($usr_id, MySQL::SQLVALUE_NUMBER);
    $conn->Query("SELECT * FROM ${p}zef WHERE zef_usrID = $usr_id AND zef_in > 0 AND zef_out = 0 LIMIT 1;");
    if ($conn->RowCount()) {
        return "1";
    } else {
        return "0";
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
    global $kga, $conn;
    
    $return_state = true;    

    $p = $kga['server_prefix'];
	
    // Lock tables
    $lock  = "LOCK TABLE ${p}usr READ, ${p}zef READ;";
    $conn->Query($lock);

//------
    
    // case 1: scan for multiple running entries of the same user
    
    $query = "SELECT usr_ID FROM ${p}usr";
    $result = $conn->Query($query);

    $rows = $conn->RowArray(0,MYSQL_ASSOC);
    
    foreach ($rows as $row) {
		$usr_id = $row['usr_ID'];
        // echo $row['usr_ID'] . "<br>";
        $query_zef = "SELECT COUNT(*) FROM ${p}zef WHERE zef_usrID = $usr_id AND zef_in > 0 AND zef_out = 0;";

        $result_zef = $conn->Query($query_zef);
        $result_array_zef = $conn->RowArray(0,MYSQL_ASSOC);
        
        if ($result_array_zef[0] > 1) {
        
            $return_state = false;
        
            // echo "User " . $row['usr_ID'] . "has multiple running zef entries:<br>";
            
            $query_zef = "SELECT * FROM ${p}zef WHERE zef_usrID = $usr_id AND zef_in > 0 AND zef_out = 0;";
	        $result_zef = $conn->Query($query_zef);
			$rows_zef = $conn->RowArray(0,MYSQL_ASSOC);
            
            // mark all running-zef-entries with a comment (except the newest one)
            $query_zef_max = "SELECT MAX(zef_in), zef_ID FROM ${p}zef WHERE zef_usrID = $usr_id AND zef_in > 0 AND zef_out = 0 GROUP BY zef_ID;";
            $result_zef_max = $conn->Query($query_zef_max);

            $result_array_zef_max = $conn->RowArray(0,MYSQL_ASSOC);
            // $max_id = $result_array_zef_max['zef_ID'];
            $max_id = $result_array_zef_max->zef_ID;
            
            foreach ($rows_zef as $row_zef) {
            
                if($row_zef['zef_ID'] != $max_id) {
					$zef_id = $row_zef['zef_ID'];
                    $query_zef_edit = "UPDATE ${p}zef SET 
                    zef_comment = 'bad entry: multiple running entries found',
                    zef_comment_type = 2
                    WHERE zef_ID = $zef_id ;";

                    $result_zef_edit = $conn->Query($query_zef_edit); 
                    
                    // $err = $conn->errorInfo();
                    // error_log("ERROR: " . $err[2]);
                }
            
                // var_dump($row_zef);
                // echo "<br>";
            }
        }
    }
    
    // Unlock tables
    $unlock = "UNLOCK TABLES";
    $conn->Query($unlock);
    
    return $return_state;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Returns the data of a certain time record
 *
 * @param array $zef_id  zef_id of the record
 * @global array $kga    kimai-global-array
 * @return array         the record's data (time, event id, project id etc) as array, false on failure
 * @author th
 */
 
// checked 

function zef_get_data($zef_id) {
    global $kga, $conn;
    
    $p = $kga['server_prefix'];
    
    $zef_id = MySQL::SQLValue($zef_id, MySQL::SQLVALUE_NUMBER);

    if ($zef_id) {
        $result = $conn->Query("SELECT * FROM ${p}zef WHERE zef_ID = " . $zef_id);
    } else {
        $result = $conn->Query("SELECT * FROM ${p}zef WHERE zef_usrID = ".$kga['usr']['usr_ID']." ORDER BY zef_ID DESC LIMIT 1");
    }
    
    if (! $result) {
    	return false;
    } else {
        return $conn->RowArray(0,MYSQL_ASSOC);
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * delete zef entry 
 *
 * @param integer $id -> ID of record
 * @global array  $kga kimai-global-array
 * @author th
 */
function zef_delete_record($id) {
    global $kga, $conn;
    $filter["zef_ID"] = MySQL::SQLValue($id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."zef";
    $query = MySQL::BuildSQLDelete($table, $filter);
    return $conn->Query($query);
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
    global $kga, $conn;
 
    $data = clean_data($data);
    
    $values ['zef_location']     =   MySQL::SQLValue( $data ['zlocation'] );
    $values ['zef_comment']      =   MySQL::SQLValue( $data ['comment'] );
    if ($data ['trackingnr'] == '')
      $values ['zef_trackingnr'] = 'NULL';
    else
      $values ['zef_trackingnr'] =   MySQL::SQLValue( $data ['trackingnr'] );
    $values ['zef_usrID']        =   MySQL::SQLValue( $usr_ID                , MySQL::SQLVALUE_NUMBER );
    $values ['zef_pctID']        =   MySQL::SQLValue( $data ['pct_ID']       , MySQL::SQLVALUE_NUMBER );
    $values ['zef_evtID']        =   MySQL::SQLValue( $data ['evt_ID']       , MySQL::SQLVALUE_NUMBER );
    $values ['zef_comment_type'] =   MySQL::SQLValue( $data ['comment_type'] , MySQL::SQLVALUE_NUMBER );
    $values ['zef_in']           =   MySQL::SQLValue( $data ['in']           , MySQL::SQLVALUE_NUMBER );
    $values ['zef_out']          =   MySQL::SQLValue( $data ['out']          , MySQL::SQLVALUE_NUMBER );
    $values ['zef_time']         =   MySQL::SQLValue( $data ['diff']         , MySQL::SQLVALUE_NUMBER );
    $values ['zef_rate']         =   MySQL::SQLValue( $data ['rate']         , MySQL::SQLVALUE_NUMBER );
    $values ['zef_cleared']      =   MySQL::SQLValue( $data ['cleared']?1:0  , MySQL::SQLVALUE_NUMBER );
    
    $table = $kga['server_prefix']."zef";
    $success =  $conn->InsertRow($table, $values);
    if ($success)
      return  $conn->GetLastInsertID();
    else
      return false;
    
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
    global $kga, $conn;
    
    logfile(serialize($data));
    
    $data = clean_data($data);
   
    $original_array = zef_get_data($id);
    $new_array = array();
    
    foreach ($original_array as $key => $value) {
        if (isset($data[$key]) == true) {
            $new_array[$key] = $data[$key];
        } else {
            $new_array[$key] = $original_array[$key];
        }
    }
    logfile(serialize($new_array));

    $values ['zef_comment']      = MySQL::SQLValue($new_array ['zef_comment']                                );
    $values ['zef_location']     = MySQL::SQLValue($new_array ['zef_location']                               );
    if ($new_array ['zef_trackingnr'] == '')
      $values ['zef_trackingnr'] = 'NULL';
    else
      $values ['zef_trackingnr'] = MySQL::SQLValue($new_array ['zef_trackingnr']                             );
    $values ['zef_pctID']        = MySQL::SQLValue($new_array ['zef_pctID']         , MySQL::SQLVALUE_NUMBER );
    $values ['zef_evtID']        = MySQL::SQLValue($new_array ['zef_evtID']         , MySQL::SQLVALUE_NUMBER );
    $values ['zef_comment_type'] = MySQL::SQLValue($new_array ['zef_comment_type']  , MySQL::SQLVALUE_NUMBER );
    $values ['zef_in']           = MySQL::SQLValue($new_array ['zef_in']            , MySQL::SQLVALUE_NUMBER );
    $values ['zef_out']          = MySQL::SQLValue($new_array ['zef_out']           , MySQL::SQLVALUE_NUMBER );
    $values ['zef_time']         = MySQL::SQLValue($new_array ['zef_time']          , MySQL::SQLVALUE_NUMBER );
    $values ['zef_rate']         = MySQL::SQLValue($new_array ['zef_rate']          , MySQL::SQLVALUE_NUMBER );
    $values ['zef_cleared']      = MySQL::SQLValue($new_array ['zef_cleared']?1:0   , MySQL::SQLVALUE_NUMBER );
                                   
    $filter ['zef_ID']           = MySQL::SQLValue($id, MySQL::SQLVALUE_NUMBER);
    $table = $kga['server_prefix']."zef";
    $query = MySQL::BuildSQLUpdate($table, $values, $filter);

    $success = true;
    
    if (! $conn->Query($query)) $success = false;
    
    if ($success) {
        if (! $conn->TransactionEnd()) $conn->Kill();
    } else {
        if (! $conn->TransactionRollback()) $conn->Kill();
    }

    return $success;

    
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
    global $kga, $conn;

    if ($timespace_in == 0 && $timespace_out == 0) {
        $mon = date("n"); $day = date("j"); $Y = date("Y"); 
        $timespace_in  = mktime(0,0,0,$mon,$day,$Y);
        $timespace_out = mktime(23,59,59,$mon,$day,$Y);
    }

    if ($timespace_out == mktime(23,59,59,date('n'),date('j'),date('Y')))
      $timespace_out = 0;

    $values['timespace_in']  = MySQL::SQLValue($timespace_in  , MySQL::SQLVALUE_NUMBER );
    $values['timespace_out'] = MySQL::SQLValue($timespace_out , MySQL::SQLVALUE_NUMBER );

    $table = $kga['server_prefix']."usr";
    $filter  ['usr_ID']          =   MySQL::SQLValue($user, MySQL::SQLVALUE_NUMBER);


    $query = MySQL::BuildSQLUpdate($table, $values, $filter);
    
    if (! $conn->Query($query)) $conn->Kill();

    return true;
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
 
// checked 

function get_arr_pct($group) {
    global $kga, $conn;
    
    $arr = array();
    $p = $kga['server_prefix'];

    if ($group == "all") {
        if ($kga['conf']['flip_pct_display']) {
            $query = "SELECT * FROM ${p}pct JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID AND pct_trash=0 ORDER BY pct_visible DESC,knd_name,pct_name;";
        } else {
            $query = "SELECT * FROM ${p}pct JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID AND pct_trash=0 ORDER BY pct_visible DESC,pct_name,knd_name;";
        }
    } else {
        $group = MySQL::SQLValue($group, MySQL::SQLVALUE_NUMBER);
        if ($kga['conf']['flip_pct_display']) {
            $query = "SELECT * FROM ${p}pct JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID JOIN ${p}grp_pct ON ${p}grp_pct.pct_ID = ${p}pct.pct_ID WHERE ${p}grp_pct.grp_ID = $group AND pct_trash=0 ORDER BY pct_visible DESC,knd_name,pct_name;";
        } else {                                                                                                                                                                                                                                                           
            $query = "SELECT * FROM ${p}pct JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID JOIN ${p}grp_pct ON ${p}grp_pct.pct_ID = ${p}pct.pct_ID WHERE ${p}grp_pct.grp_ID = $group AND pct_trash=0 ORDER BY pct_visible DESC,pct_name,knd_name;";
        }
    }
    
    $result = $conn->Query($query);
    if ($result == false) {
        return false;
    }
    
    $rows = $conn->RecordsArray(MYSQL_ASSOC);

    $arr = array();
    $i = 0;
    if ($rows) {
        foreach ($rows as $row) {
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
    } else {
        return array();
    }
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
 
// checked 

function get_arr_pct_by_knd($group, $knd_id) {
    global $kga, $conn;
    
    $group   = MySQL::SQLValue($group  , MySQL::SQLVALUE_NUMBER);
    $knd_id  = MySQL::SQLValue($knd_id , MySQL::SQLVALUE_NUMBER);
    $p       = $kga['server_prefix'];
    


    if ($kga['conf']['flip_pct_display']) {
        $sort = "knd_name,pct_name";
    } else {
        $sort = "pct_name,knd_name";
    }

    if ($group == "all") {
      $query = "SELECT * FROM ${p}pct JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID AND ${p}pct.pct_kndID = $knd_id AND pct_trash=0 ORDER BY $sort;";
    } else {
      $query = "SELECT * FROM ${p}pct JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID JOIN ${p}grp_pct ON ${p}grp_pct.pct_ID = ${p}pct.pct_ID WHERE ${p}grp_pct.grp_ID = $group AND ${p}pct.pct_kndID = $knd_id AND pct_trash=0 ORDER BY $sort;";
    }     
    
    $conn->Query($query);
    
    $arr = array();    
    $i=0;

    $conn->MoveFirst();
    while (! $conn->EndOfSeek()) {
        $row = $conn->Row();
        $arr[$i]['pct_ID']      = $row->pct_ID;
        $arr[$i]['pct_name']    = $row->pct_name;
        $arr[$i]['knd_name']    = $row->knd_name;
        $arr[$i]['knd_ID']      = $row->knd_ID;
        $arr[$i]['pct_visible'] = $row->pct_visible;
        $arr[$i]['pct_budget']  = $row->pct_budget;
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
 *  This method also makes the values SQL-secure.
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

    for ($i = 0;$i<count($users);$i++)
      $users[$i] = MySQL::SQLValue($users[$i], MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($customers);$i++)
      $customers[$i] = MySQL::SQLValue($customers[$i], MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($projects);$i++)
      $projects[$i] = MySQL::SQLValue($projects[$i], MySQL::SQLVALUE_NUMBER);
    for ($i = 0;$i<count($events);$i++)
      $events[$i] = MySQL::SQLValue($events[$i], MySQL::SQLVALUE_NUMBER);

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

//-----------------------------------------------------------------------------------------------------------

/**
 * returns timesheet for specific user as multidimensional array
 *
 * @param integer $user ID of user in table usr
 * @param integer $in start of timespace in unix seconds
 * @param integer $out end of timespace in unix seconds
 * @param integer $filterCleared where -1 (default) means no filtering, 0 means only not cleared entries, 1 means only cleared entries
 * @global array $kga kimai-global-array
 * @return array
 * @author th 
 */
 
// checked 

function get_arr_zef($in,$out,$users = null, $customers = null, $projects = null, $events = null,$limit = false, $reverse_order = false, $filterCleared = null) {
    global $kga, $conn;

    if (!is_numeric($filterCleared)) {
      $filterCleared = $kga['conf']['hideClearedEntries']-1; // 0 gets -1 for disabled, 1 gets 0 for only not cleared entries
    }
    
    $in    = MySQL::SQLValue($in    , MySQL::SQLVALUE_NUMBER);
    $out   = MySQL::SQLValue($out   , MySQL::SQLVALUE_NUMBER);
    $filterCleared   = MySQL::SQLValue($filterCleared , MySQL::SQLVALUE_NUMBER);
    $limit = MySQL::SQLValue($limit , MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];

    $whereClauses = zef_whereClausesFromFilters($users,$customers,$projects,$events);

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
              ' ORDER BY zef_in '.($reverse_order?'ASC ':'DESC ') . $limit.';';

    $conn->Query($query);
    
    $i=0;
    $arr=array();

    // if ($conn->RowCount()>0) {
        $conn->MoveFirst();
        while (! $conn->EndOfSeek()) {
            $row = $conn->Row();
            $arr[$i]['zef_ID']           = $row->zef_ID;
            if ($row->zef_in <= $in && $row->zef_out < $out)  {
              $arr[$i]['zef_in']         = $in;
              $arr[$i]['zef_out']        = $row->zef_out;
            }
            else if ($row->zef_in <= $in && $row->zef_out >= $out)  {
              $arr[$i]['zef_in']         = $in;
              $arr[$i]['zef_out']        = $out;
            }
            else if ($row->zef_in > $in && $row->zef_out < $out)  {
              $arr[$i]['zef_in']         = $row->zef_in;
              $arr[$i]['zef_out']        = $row->zef_out;
            }
            else if ($row->zef_in > $in && $row->zef_out >= $out)  {
              $arr[$i]['zef_in']         = $row->zef_in;
              $arr[$i]['zef_out']        = $out;
            }

            if ($row->zef_out != 0) {
              // only calculate time after recording is complete
              $arr[$i]['zef_time']         = $arr[$i]['zef_out'] - $arr[$i]['zef_in']; 
              $arr[$i]['zef_duration']     = formatDuration($arr[$i]['zef_time']);
              $arr[$i]['wage_decimal']     = $arr[$i]['zef_time']/3600*$row->zef_rate;
              $arr[$i]['wage']             = sprintf("%01.2f",$arr[$i]['wage_decimal']);
            }
            $arr[$i]['zef_rate']         = $row->zef_rate;
            $arr[$i]['zef_pctID']        = $row->zef_pctID;
            $arr[$i]['zef_evtID']        = $row->zef_evtID;
            $arr[$i]['zef_usrID']        = $row->zef_usrID;
            $arr[$i]['pct_ID']           = $row->pct_ID;
            $arr[$i]['knd_name']         = $row->knd_name;
            $arr[$i]['pct_kndID']        = $row->pct_kndID;
            $arr[$i]['evt_name']         = $row->evt_name;
            $arr[$i]['pct_name']         = $row->pct_name;
            $arr[$i]['pct_comment']      = $row->pct_comment;
            $arr[$i]['zef_location']     = $row->zef_location;
            $arr[$i]['zef_trackingnr']   = $row->zef_trackingnr;
            $arr[$i]['zef_comment']      = $row->zef_comment;
            $arr[$i]['zef_cleared']      = $row->zef_cleared;
            $arr[$i]['zef_comment_type'] = $row->zef_comment_type;
            $arr[$i]['usr_alias']        = $row->usr_alias;
            $arr[$i]['usr_name']         = $row->usr_name;
            $i++;
        }
        return $arr;
    // } else {
        // return false;
    // }
}


//-----------------------------------------------------------------------------------------------------------

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

// seems to be ok  

function checkUser() {
    global $kga, $conn;

	$p = $kga['server_prefix'];
        
  if (isset($_COOKIE['kimai_usr']) && isset($_COOKIE['kimai_key']) && $_COOKIE['kimai_usr'] != "0" && $_COOKIE['kimai_key'] != "0") {
      $kimai_usr = addslashes($_COOKIE['kimai_usr']);
      $kimai_key = addslashes($_COOKIE['kimai_key']);
      
      if (get_seq($kimai_usr) != $kimai_key) {
          kickUser();
      } else {
        if (strncmp($kimai_usr, 'knd_', 4) == 0) {
            $knd_name = MySQL::SQLValue(substr($kimai_usr,4));
            $query = "SELECT knd_ID FROM ${p}knd WHERE knd_name = $knd_name AND NOT knd_trash = '1';";
            $conn->Query($query);
            $row = $conn->RowArray(0,MYSQL_ASSOC);

            $knd_ID   = $row['knd_ID'];
            if ($knd_ID < 1) {
                kickUser();
            }
          }
          else {
            $query = "SELECT usr_ID,usr_sts,usr_grp FROM ${p}usr WHERE usr_name = '$kimai_usr' AND usr_active = '1' AND NOT usr_trash = '1';";
            $conn->Query($query);
            $row = $conn->RowArray(0,MYSQL_ASSOC);
            
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
    
    // load configuration and language
    get_global_config();
    if (strncmp($kimai_usr, 'knd_', 4) == 0)
      get_customer_config($knd_ID);
    else  
      get_user_config($usr_ID);

    // override default language if user has chosen a language in the prefs
    if ($kga['conf']['lang'] != "") {
      $kga['language'] = $kga['conf']['lang'];
      $kga['lang'] = array_replace_recursive($kga['lang'],include(WEBROOT."language/${kga['language']}.php"));
    }

    return (isset($kga['usr'])?$kga['usr']:null);
}

//-----------------------------------------------------------------------------------------------------------

/**
 * write global configuration into $kga including defaults for user settings.
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return array $kga 
 * @author th
 *
 */

// seems to be ok 

function get_global_config() {
  global $kga, $conn;
  // get values from global configuration 
  $table = $kga['server_prefix']."var";
  $conn->SelectRows($table);
  
  $conn->MoveFirst();
  while (! $conn->EndOfSeek()) {
      $row = $conn->Row();
      $kga['conf'][$row->var] = $row->value;
  }
  

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
  global $kga, $conn;
        
  if (!$user) return;
  
  $table = $kga['server_prefix']."usr";
  $filter['usr_ID'] = MySQL::SQLValue($user, MySQL::SQLVALUE_NUMBER);
  
  // get values from user record
  $columns[] = "usr_ID";
  $columns[] = "usr_name";
  $columns[] = "usr_grp";
  $columns[] = "usr_sts";
  $columns[] = "usr_trash";
  $columns[] = "usr_active";
  $columns[] = "usr_mail";
  $columns[] = "pw";
  $columns[] = "ban";
  $columns[] = "banTime";
  $columns[] = "secure";

  $columns[] = "lastProject";
  $columns[] = "lastEvent";
  $columns[] = "lastRecord";
  $columns[] = "timespace_in";
  $columns[] = "timespace_out";

  $conn->SelectRows($table, $filter, $columns);
  $rows = $conn->RowArray(0,MYSQL_ASSOC);
  foreach($rows as $key => $value) {
      $kga['usr'][$key] = $value;
  } 
  
  // get values from user configuration (user-preferences)
  unset($columns);
  unset($filter);

  $kga['conf'] = array_merge($kga['conf'],usr_get_preferences_by_prefix('ui.'));
  $userTimezone = usr_get_preference('timezone');
  if ($userTimezone != '')
    $kga['conf']['timezone'] = $userTimezone;
 
  date_default_timezone_set($kga['conf']['timezone']);
}

//-----------------------------------------------------------------------------------------------------------

/**
 * write details of a specific customer into $kga
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return array $kga 
 * @author sl
 *
 */

function get_customer_config($user) {
  global $kga, $conn;
        
  if (!$user) return;
  
  $table = $kga['server_prefix']."knd";
  $filter['knd_ID'] = MySQL::SQLValue($user, MySQL::SQLVALUE_NUMBER);
  
  // get values from user record
  $columns[] = "knd_ID";
  $columns[] = "knd_name";
  $columns[] = "knd_comment";
  $columns[] = "knd_visible";
  $columns[] = "knd_filter";
  $columns[] = "knd_company";
  $columns[] = "knd_street";
  $columns[] = "knd_zipcode";
  $columns[] = "knd_city";
  $columns[] = "knd_tel";
  $columns[] = "knd_fax";
  $columns[] = "knd_mobile";
  $columns[] = "knd_mail";
  $columns[] = "knd_homepage";
  $columns[] = "knd_trash";
  $columns[] = "knd_password";
  $columns[] = "knd_secure";

  $conn->SelectRows($table, $filter, $columns);
  $rows = $conn->RowArray(0,MYSQL_ASSOC);
  foreach($rows as $key => $value) {
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
    global $kga, $conn;
    
    $name  = MySQL::SQLValue($name);
    $p     = $kga['server_prefix'];

    $query = "SELECT knd_ID FROM ${p}knd WHERE knd_name = $name";

    $conn->Query($query);
    return $conn->RowCount() == 1;
}

//-----------------------------------------------------------------------------------------------------------

/**
 * returns ID of running timesheet event for specific user
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
 
// checked 

function get_event_last() {
    global $kga, $conn;
    
    $p     = $kga['server_prefix'];
    
    $lastRecord = $kga['usr']['lastRecord'];
    
    $query = "SELECT * FROM ${p}zef WHERE zef_ID = $lastRecord ;";

    $conn->Query($query);
    return $conn->RowArray(0,MYSQL_ASSOC);
}

//-----------------------------------------------------------------------------------------------------------

/**
 * returns single timesheet entry as array
 *
 * @param integer $id ID of entry in table zef
 * @global array $kga kimai-global-array
 * @return array
 * @author th
 */

// checked 

function get_entry_zef($id) {
    global $kga, $conn;

    $id    = MySQL::SQLValue($id   , MySQL::SQLVALUE_NUMBER);
	$p     = $kga['server_prefix'];
	
    $query = "SELECT * FROM ${p}zef 
              Left Join ${p}pct ON zef_pctID = pct_ID 
              Left Join ${p}knd ON pct_kndID = knd_ID 
              Left Join ${p}evt ON evt_ID    = zef_evtID
              WHERE zef_ID = $id LIMIT 1;";

    $conn->Query($query);
    return $conn->RowArray(0,MYSQL_ASSOC);
}

//-----------------------------------------------------------------------------------------------------------

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
 
// checked 
 
function get_zef_time($in,$out,$users = null, $customers = null, $projects = null, $events = null,$filterCleared = null) {
    global $kga, $conn;

    if (!is_numeric($filterCleared)) {
      $filterCleared = $kga['conf']['hideClearedEntries']-1; // 0 gets -1 for disabled, 1 gets 0 for only not cleared entries
    }
    
    $in    = MySQL::SQLValue($in    , MySQL::SQLVALUE_NUMBER);
    $out   = MySQL::SQLValue($out   , MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];

    $whereClauses = zef_whereClausesFromFilters($users,$customers,$projects,$events);

    if ($in)
      $whereClauses[]="zef_out > $in";
    if ($out)
      $whereClauses[]="zef_in < $out";
    if ($filterCleared > -1)
      $whereClauses[] = "zef_cleared = $filterCleared";

    $query = "SELECT zef_in,zef_out,zef_time AS zeit FROM ${p}zef 
             Join ${p}pct ON zef_pctID = pct_ID
             Join ${p}knd ON pct_kndID = knd_ID
             Join ${p}usr ON zef_usrID = usr_ID
             Join ${p}evt ON evt_ID    = zef_evtID ".(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses);
    $conn->Query($query);

    $conn->MoveFirst();
    $sum = 0;
    $zef_in = 0;
    $zef_out = 0;
    while (! $conn->EndOfSeek()) {
      $row = $conn->Row();
      if ($row->zef_in <= $in && $row->zef_out < $out)  {
        $zef_in  = $in;
        $zef_out = $row->zef_out;
      }
      else if ($row->zef_in <= $in && $row->zef_out >= $out)  {
        $zef_in  = $in;
        $zef_out = $out;
      }
      else if ($row->zef_in > $in && $row->zef_out < $out)  {
        $zef_in  = $row->zef_in;
        $zef_out = $row->zef_out;
      }
      else if ($row->zef_in > $in && $row->zef_out >= $out)  {
        $zef_in  = $row->zef_in;
        $zef_out = $out;
      }
      $sum+=(int)($zef_out - $zef_in);

    }
    return $sum;
}

//-----------------------------------------------------------------------------------------------------------

/**
 * returns list of customers in a group as array
 *
 * @param integer $group ID of group in table grp or "all" for all groups
 * @global array $kga kimai-global-array
 * @return array
 * @author th
 */

// checked 

function get_arr_knd($group) {
    global $kga, $conn;

	$p = $kga['server_prefix'];
           

    if ($group == "all") {
        $query = "SELECT * FROM ${p}knd WHERE knd_trash=0 ORDER BY knd_visible DESC,knd_name;";
    } else {
        $group = MySQL::SQLValue($group , MySQL::SQLVALUE_NUMBER); 
        $query = "SELECT * FROM ${p}knd JOIN ${p}grp_knd ON `${p}grp_knd`.`knd_ID`=`${p}knd`.`knd_ID` WHERE `${p}grp_knd`.`grp_ID` = $group AND knd_trash=0 ORDER BY knd_visible DESC,knd_name;";
    }
    
    $result = $conn->Query($query);
    if ($result == false) {
        return false;
    }

    $arr = array();
    $i = 0;
    if ($conn->RowCount()) {
        $conn->MoveFirst();
        while (! $conn->EndOfSeek()) {
            $row = $conn->Row();
            $arr[$i]['knd_ID']       = $row->knd_ID;   
            $arr[$i]['knd_name']     = $row->knd_name;
            $arr[$i]['knd_contact']  = $row->knd_contact;
            $arr[$i]['knd_visible']  = $row->knd_visible;
            $i++;
        }
        return $arr;
    } else {
        return array();
    }
}

//-----------------------------------------------------------------------------------------------------------

## Load into Array: Events 

// checked
 
function get_arr_evt($group) {
    global $kga, $conn;
 
 $p = $kga['server_prefix']; 

    if ($group == "all") {
        $query = "SELECT * FROM ${p}evt WHERE evt_trash=0 ORDER BY evt_visible DESC,evt_name;";
    } else {
        $group = MySQL::SQLValue($group , MySQL::SQLVALUE_NUMBER); 
        $query = "SELECT * FROM ${p}evt JOIN ${p}grp_evt ON `${p}grp_evt`.`evt_ID`=`${p}evt`.`evt_ID` WHERE `${p}grp_evt`.`grp_ID` = $group AND evt_trash=0 ORDER BY evt_visible DESC,evt_name;";
    }
    
    $result = $conn->Query($query);
    if ($result == false) {
        return false;
    }

    $arr = array();
    $i = 0;
    if ($conn->RowCount()) {
        $conn->MoveFirst();
        while (! $conn->EndOfSeek()) {
            $row = $conn->Row();
            $arr[$i]['evt_ID']       = $row->evt_ID;   
            $arr[$i]['evt_name']     = $row->evt_name;
            $arr[$i]['evt_visible']  = $row->evt_visible;
            $i++;
        }
        return $arr;
    } else {
        return array();
    }
}

//-----------------------------------------------------------------------------------------------------------

/**
 * Get an array of events, which should be displayed for a specific project.
 * Those are events which were assigned to the project or which are assigned to
 * no project.
 * 
 * Two joins can occur:
 *  The JOIN is for filtering the events by groups.
 *  
 *  The LEFT JOIN gives each event row the project id which it has been assigned
 *  to via the pct_evt table or NULL when there is no assignment. So we only
 *  take rows which have NULL or the project id in that column.
 *  
 *  @author sl
 */
 
function get_arr_evt_by_pct($group,$pct) {
    global $kga, $conn;
    $pct = MySQL::SQLValue($pct , MySQL::SQLVALUE_NUMBER); 
 
    $p = $kga['server_prefix'];

    if ($group == "all") {
        $query = "SELECT ${p}evt.evt_ID,evt_name,evt_visible FROM ${p}evt
 LEFT JOIN ${p}pct_evt ON `${p}pct_evt`.`evt_ID`=`${p}evt`.`evt_ID`
 WHERE evt_trash=0 AND (pct_ID = $pct OR pct_ID IS NULL)
 ORDER BY evt_visible DESC,evt_name;";
    } else {
        $group = MySQL::SQLValue($group , MySQL::SQLVALUE_NUMBER); 
        $query = "SELECT ${p}evt.evt_ID,evt_name,evt_visible FROM ${p}evt
 JOIN ${p}grp_evt ON `${p}grp_evt`.`evt_ID`=`${p}evt`.`evt_ID`
 LEFT JOIN ${p}pct_evt ON `${p}pct_evt`.`evt_ID`=`${p}evt`.`evt_ID`
 WHERE `${p}grp_evt`.`grp_ID` = $group AND evt_trash=0
 AND (pct_ID = $pct OR pct_ID IS NULL)
 ORDER BY evt_visible DESC,evt_name;";
    }
    $result = $conn->Query($query);
    if ($result == false) {
        return false;
    }

    $arr = array();
    $i = 0;
    if ($conn->RowCount()) {
        $conn->MoveFirst();
        while (! $conn->EndOfSeek()) {
            $row = $conn->Row();
            $arr[$i]['evt_ID']       = $row->evt_ID;   
            $arr[$i]['evt_name']     = $row->evt_name;
            $arr[$i]['evt_visible']  = $row->evt_visible;
            $i++;
        }
        return $arr;
    } else {
        return array();
    }
}


// -----------------------------------------------------------------------------------------------------------

/**
 * returns list of events used with specified customer
 *
 * @param integer $customer filter for only this ID of a customer
 * @global array $kga kimai-global-array
 * @global array $conn MySQL connection
 * @return array
 * @author sl
 */
function get_arr_evt_by_knd($customer_ID) {
    global $kga, $conn;
  
    $p = $kga['server_prefix']; 
    
    $customer_ID = MySQL::SQLValue($customer_ID , MySQL::SQLVALUE_NUMBER); 
    
    $query = "SELECT * FROM ${p}evt WHERE evt_ID IN (SELECT zef_evtID FROM ${p}zef WHERE zef_pctID IN (SELECT pct_ID FROM ${p}pct WHERE pct_kndID = $customer_ID)) AND evt_trash=0";
    
    $result = $conn->Query($query);
    if ($result == false) {
        return false;
    }

    $arr = array();
    $i = 0;
    
    if ($conn->RowCount()) {
        $conn->MoveFirst();
        while (! $conn->EndOfSeek()) {
            $row = $conn->Row();
            $arr[$i]['evt_ID']       = $row->evt_ID;   
            $arr[$i]['evt_name']     = $row->evt_name;
            $arr[$i]['evt_visible']  = $row->evt_visible;
            $i++;
        }
        return $arr;
    } else {
        return false;
    }
}

//-----------------------------------------------------------------------------------------------------------

## Load into Array: Events with attached time-sums

// checked

function get_arr_evt_with_time($group,$user,$in,$out) {
    global $kga, $conn;
    
    $arr_evts = get_arr_evt($group);
    $arr_time = get_arr_time_evt($user,$in,$out);
    
    $arr = array(); 
    $i=0;
    foreach ($arr_evts as $evt) {
        $arr[$i]['evt_ID']      = $evt['evt_ID'];
        $arr[$i]['evt_name']    = $evt['evt_name'];
        $arr[$i]['evt_visible'] = $evt['evt_visible'];
        if (isset($arr_time[$evt['evt_ID']])) $arr[$i]['zeit'] = formatDuration($arr_time[$evt['evt_ID']]);
        else $arr[$i]['zeit']   = formatDuration(0);
        $i++;
    }
    return $arr;
}

//-----------------------------------------------------------------------------------------------------------

## Load into Array: Customers with attached time-sums

// checked

function get_arr_knd_with_time($group,$user,$in,$out) {
    global $kga, $conn;
    
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

//-----------------------------------------------------------------------------------------------------------

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

// checked
 
function get_current_timer() {
    global $kga, $conn;
    
    $user  = MySQL::SQLValue($kga['usr']['usr_ID'] , MySQL::SQLVALUE_NUMBER);
	$p     = $kga['server_prefix'];
        
    $conn->Query("SELECT zef_ID,zef_in,zef_time FROM ${p}zef WHERE zef_usrID = $user ORDER BY zef_in DESC LIMIT 1;");
    
    $row = $conn->RowArray(0,MYSQL_ASSOC);

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

//-----------------------------------------------------------------------------------------------------------

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
 
// checked

function get_zef_time_day($inPoint,$user) {
    global $kga, $conn;

    $p = $kga['server_prefix'];
    $inPoint = MySQL::SQLValue($inPoint, MySQL::SQLVALUE_NUMBER);
    $user    = MySQL::SQLValue($user   , MySQL::SQLVALUE_NUMBER);
            
    $outPoint=$inPoint+86399;
    
    $conn->Query("SELECT sum(zef_time) as zeit FROM ${p}zef WHERE zef_in > $inPoint AND zef_out < $outPoint AND zef_usrID = $user ;");
    
    $row = $conn->RowArray(0,MYSQL_ASSOC);
    return $row['zeit'];
}

//-----------------------------------------------------------------------------------------------------------

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
 
// checked

function get_zef_time_mon($inPoint,$user) {
    global $kga, $conn;
    
    $inPoint = MySQL::SQLValue($inPoint, MySQL::SQLVALUE_NUMBER);
    $user    = MySQL::SQLValue($user   , MySQL::SQLVALUE_NUMBER);
    $p       = $kga['server_prefix'];
    
    $inDatum_m = date("m",$inPoint);
    $inDatum_Y = date("Y",$inPoint);
    $inDatum_t = date("t",$inPoint);
    
    $inPoint  = mktime(0,0,0,$inDatum_m,1,$inDatum_Y);
    $outPoint = mktime(23,59,59,$inDatum_m,$inDatum_t,$inDatum_Y);

    $conn->Query("SELECT sum(zef_time) as zeit FROM ${p}zef WHERE zef_in > $inPoint AND zef_out < $outPoint AND zef_usrID = $user ;");

    $row = $conn->RowArray(0,MYSQL_ASSOC);
    return $row['zeit'];
}

//-----------------------------------------------------------------------------------------------------------

/**
 * returns the total worktime in database
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return string
 * @author th 
 */
 
// checked

function get_zef_time_all($user) {
    global $kga, $conn;
    
    $user = MySQL::SQLValue($user, MySQL::SQLVALUE_NUMBER);
    $p    = $kga['server_prefix'];
        
    $conn->Query("SELECT sum(zef_time) as zeit FROM ${p}zef WHERE zef_usrID = $user ;");

    $row = $conn->RowArray(0,MYSQL_ASSOC);
    return $row['zeit'];    
}

//-----------------------------------------------------------------------------------------------------------

/**
 * returns the total worktime of a zef_entry year
 *
 * @param integer $year 4 digit year (not sure yet if 2 digits work...)
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return string
 * @author th 
 */
 
// checked

function get_zef_time_year($year,$user) {
    global $kga, $conn;
    
    $user = MySQL::SQLValue($user, MySQL::SQLVALUE_NUMBER);
    $year = MySQL::SQLValue($year, MySQL::SQLVALUE_NUMBER);
    $p = $kga['server_prefix'];
        
    $in  = (int)mktime(0,0,0,1,1,$year); 
    $out = (int)mktime(23,59,59,12,(int)date("t"),$year);
    
    $conn->Query("SELECT sum(zef_time) as zeit FROM ${p}zef WHERE zef_in > $in AND zef_out < $out AND zef_usrID = $user ;");

    $row = $conn->RowArray(0,MYSQL_ASSOC);
    return $row['zeit'];
}

//-----------------------------------------------------------------------------------------------------------

/**
 * returns the version of the installed Kimai database to compare it with the package version
 *
 * @global array $kga kimai-global-array
 * @return array
 * @author th
 *
 * [0] => version number (x.x.x)
 * [1] => svn revision number
 *
 */

// checked

function get_DBversion() {
    global $kga, $conn;
    
    $filter['var'] = MySQL::SQLValue('version');
    $columns[] = "value";
    $table = $kga['server_prefix']."var";
    $result = $conn->SelectRows($table, $filter, $columns);
    
    $row = $conn->RowArray(0,MYSQL_ASSOC);
    $return[] = $row['value'];  
    
    if ($result == false) $return[0] = "0.5.1";
    
    $filter['var'] = MySQL::SQLValue('revision');
    $result = $conn->SelectRows($table, $filter, $columns);
    
    $row = $conn->RowArray(0,MYSQL_ASSOC);
    $return[] = $row['value'];
    
    return $return;
}

//-----------------------------------------------------------------------------------------------------------

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
 
// checked 

function get_seq($user) {
    global $kga, $conn;
    
    if (strncmp($user, 'knd_', 4) == 0) {
      $filter['knd_name'] = MySQL::SQLValue(substr($user,4));
      $columns[] = "knd_secure";
      $table = $kga['server_prefix']."knd";
    }
    else {
      $filter['usr_name'] = MySQL::SQLValue($user);
      $columns[] = "secure";
      $table = $kga['server_prefix']."usr";
    }
    
    $result = $conn->SelectRows($table, $filter, $columns);
    if ($result == false) {
        return false;
    }
    
    $row = $conn->RowArray(0,MYSQL_ASSOC);
    return strncmp($user, 'knd_', 4)==0?$row['knd_secure']:$row['secure'];
}

//-----------------------------------------------------------------------------------------------------------

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
 
// checked

function get_arr_usr($trash=0) {
    global $kga, $conn;
    
    $p = $kga['server_prefix'];
        
    
    if (!$trash) {
        $trashoption = "WHERE usr_trash !=1";
    } else {
        $trashoption = "";
    }
    
    $query = "SELECT * FROM ${p}usr Left Join ${p}grp ON usr_grp = grp_ID $trashoption ORDER BY usr_name ;";
    $conn->Query($query);

    $rows = $conn->RowArray(0,MYSQL_ASSOC);
    
    $i=0;
    $arr = array();

    $conn->MoveFirst();
    while (! $conn->EndOfSeek()) {
        $row = $conn->Row();
        $arr[$i]['usr_ID']     = $row->usr_ID;
        $arr[$i]['usr_name']   = $row->usr_name;
        $arr[$i]['usr_grp']    = $row->usr_grp;
        $arr[$i]['usr_sts']    = $row->usr_sts;
        $arr[$i]['grp_name']   = $row->grp_name;
        $arr[$i]['usr_mail']   = $row->usr_mail;
        $arr[$i]['usr_active'] = $row->usr_active;
        $arr[$i]['usr_trash']  = $row->usr_trash;
        
        if ($row->pw !='' && $row->pw != '0') {
            $arr[$i]['usr_pw'] = "yes"; 
        } else {                 
            $arr[$i]['usr_pw'] = "no"; 
        }
        $i++;
    }

    return $arr;
}

//-----------------------------------------------------------------------------------------------------------

/**
 * returns array of all groups 
 *
 * [0]=> array(6) {
 *      ["grp_ID"]      =>  string(1) "1" 
 *      ["grp_name"]    =>  string(5) "admin" 
 *      ["grp_leader"]  =>  string(9) "1234" 
 *      ["grp_trash"]   =>  string(1) "0" 
 *      ["count_users"] =>  string(1) "2" 
 *      ["leader_name"] =>  string(5) "user1" 
 * } 
 * 
 * [1]=> array(6) { 
 *      ["grp_ID"]      =>  string(1) "2" 
 *      ["grp_name"]    =>  string(4) "Test" 
 *      ["grp_leader"]  =>  string(9) "12345" 
 *      ["grp_trash"]   =>  string(1) "0" 
 *      ["count_users"] =>  string(1) "1" 
 *      ["leader_name"] =>  string(7) "user2" 
 *  } 
 *
 * @global array $kga kimai-global-array
 * @return array
 * @author th 
 *
 */
 

 
function get_arr_grp($trash=0) {
    global $kga, $conn;
    
    $p = $kga['server_prefix'];

    // Lock tables for alles queries executed until the end of this function
    $lock  = "LOCK TABLE ${p}usr READ, ${p}grp READ, ${p}ldr READ;";
    $conn->Query($lock);
    logfile($conn->Error());

//------

    if (!$trash) {
        $trashoption = "WHERE grp_trash !=1";
    } 

    $query  = "SELECT * FROM ${p}grp $trashoption ORDER BY grp_name;";
    $conn->Query($query);

    // rows into array
    $groups = array();
    $i=0;
    
    $rows = $conn->RecordsArray(MYSQL_ASSOC);

    foreach ($rows as $row){
        $groups[] = $row;

        // append user count
        $groups[$i]['count_users'] = grp_count_users($row['grp_ID']);

        // append leader array
        $ldr_id_array = grp_get_ldrs($row['grp_ID']);
        $ldr_name_array = array();
        $j = 0;
        foreach ($ldr_id_array as $ldr_id) {
            $ldr_name_array[$j] = usr_id2name($ldr_id);
            $j++;
        }
        
        $groups[$i]['leader_name'] = $ldr_name_array;

        $i++;
    }

//------

    // Unlock tables
    $unlock = "UNLOCK TABLES;";
    $conn->Query($unlock);
    logfile($conn->Error());
    
    return $groups;    
}

//-----------------------------------------------------------------------------------------------------------

/**
 * returns array of all groups of a group leader
 *
 * [0]=> array(6) {
 *      ["grp_ID"]      =>  string(1) "1" 
 *      ["grp_name"]    =>  string(5) "admin" 
 *      ["grp_leader"]  =>  string(9) "1234" 
 *      ["grp_trash"]   =>  string(1) "0" 
 *      ["count_users"] =>  string(1) "2" 
 *      ["leader_name"] =>  string(5) "user1" 
 * } 
 * 
 * [1]=> array(6) { 
 *      ["grp_ID"]      =>  string(1) "2" 
 *      ["grp_name"]    =>  string(4) "Test" 
 *      ["grp_leader"]  =>  string(9) "12345" 
 *      ["grp_trash"]   =>  string(1) "0" 
 *      ["count_users"] =>  string(1) "1" 
 *      ["leader_name"] =>  string(7) "user2" 
 *  } 
 *
 * @global array $kga kimai-global-array
 * @return array
 * @author sl
 *
 */
 function get_arr_grp_by_leader($leader_id,$trash=0) {
    global $kga, $conn;

    $leader_id = MySQL::SQLValue($leader_id, MySQL::SQLVALUE_NUMBER  );
    
    $p = $kga['server_prefix'];

    // Lock tables for alles queries executed until the end of this function
    $lock  = "LOCK TABLE ${p}usr READ, ${p}grp READ, ${p}ldr READ;";
    $conn->Query($lock);
    logfile($conn->Error());

//------

    if (!$trash) {
        $trashoption = "AND grp_trash !=1";
    } 
    $query = "SELECT ${p}grp.* 
    FROM ${p}grp JOIN ${p}ldr ON ${p}grp.grp_ID =${p}ldr.grp_ID 
    WHERE grp_leader = $leader_id $trashoption ORDER BY grp_name";
    logfile($query);
    $conn->Query($query);

    // rows into array
    $groups = array();
    $i=0;
    
    $rows = $conn->RecordsArray(MYSQL_ASSOC);

    foreach ($rows as $row){
        $groups[] = $row;

        // append user count
        $groups[$i]['count_users'] = grp_count_users($row['grp_ID']);

        // append leader array
        $ldr_id_array = grp_get_ldrs($row['grp_ID']);
        $ldr_name_array = array();
        $j = 0;
        foreach ($ldr_id_array as $ldr_id) {
            $ldr_name_array[$j] = usr_id2name($ldr_id);
            $j++;
        }
        
        $groups[$i]['leader_name'] = $ldr_name_array;

        $i++;
    }

//------

    // Unlock tables
    $unlock = "UNLOCK TABLES;";
    $conn->Query($unlock);
    logfile($conn->Error());
    
    return $groups;    
}

//-----------------------------------------------------------------------------------------------------------

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
 
// checked
 
function stopRecorder() {
## stop running recording |
    global $kga, $conn;
    
    $table = $kga['server_prefix']."zef";

    $last_task        = get_event_last(); // aktuelle vorgangs-ID auslesen
    
    $filter['zef_ID'] = $last_task['zef_ID'];

    $rounded = roundTimespan($last_task['zef_in'],time(),$kga['conf']['roundPrecision']);

    $values['zef_in'] = $rounded['start'];
    $values['zef_out']  = $rounded['end'];
    $values['zef_time'] = $values['zef_out']-$values['zef_in'];

    
    $query = MySQL::BuildSQLUpdate($table, $values, $filter);
    
    logfile($query);
    
    return $conn->Query($query);    
}

// -----------------------------------------------------------------------------------------------------------

/**
 * starts timesheet record
 *
 * @param integer $pct_ID ID of project to record
 * @global array $kga kimai-global-array
 * @author th
 */
 
// seems to work fine

function startRecorder($pct_ID,$evt_ID,$user) {
    global $kga, $conn;
    
    if (! $conn->TransactionBegin()) $conn->Kill();
    
    $pct_ID = MySQL::SQLValue($pct_ID, MySQL::SQLVALUE_NUMBER  );
    $evt_ID = MySQL::SQLValue($evt_ID, MySQL::SQLVALUE_NUMBER  );
    $user   = MySQL::SQLValue($user  , MySQL::SQLVALUE_NUMBER  );
        
    $values ['zef_pctID'] = $pct_ID;
    $values ['zef_evtID'] = $evt_ID;
    $values ['zef_in']    = time();
    $values ['zef_usrID'] = $user;
    $rate = get_best_fitting_rate($user,$pct_ID,$evt_ID);
    if ($rate)
      $values ['zef_rate'] = $rate;
    
    $table = $kga['server_prefix']."zef";
    $result = $conn->InsertRow($table, $values);

    if (! $result) {
    	return false;
    } 
    
    unset($values);
    $values ['lastRecord'] = $conn->GetLastInsertID();
    $table = $kga['server_prefix']."usr";
    $filter  ['usr_ID'] = $user;
    $query = MySQL::BuildSQLUpdate($table, $values, $filter);
    
    $success = true;
    
    if (! $conn->Query($query)) $success = false;
    
    if ($success) {
        if (! $conn->TransactionEnd()) $conn->Kill();
    } else {
        if (! $conn->TransactionRollback()) $conn->Kill();
    }
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Just edit the project for an entry. This is used for changing the project
 * of a running entry.
 * 
 * @param $zef_id id of the timesheet entry
 * @param $pct_id id of the project to change to
 */
function zef_edit_pct($zef_id,$pct_id) {
    global $kga, $conn;

    $zef_id = MySQL::SQLValue($zef_id, MySQL::SQLVALUE_NUMBER  );
    $pct_id = MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER );
    
    $table = $kga['server_prefix']."zef";
    
    $filter['zef_id'] = $zef_id;

    $values['zef_pctID'] = $pct_id;

    $query = MySQL::BuildSQLUpdate($table, $values, $filter);
    
    logfile($query);
    
    return $conn->Query($query);    

}

// -----------------------------------------------------------------------------------------------------------

/**
 * Just edit the task for an entry. This is used for changing the task
 * of a running entry.
 * 
 * @param $zef_id id of the timesheet entry
 * @param $evt_id id of the task to change to
 */
function zef_edit_evt($zef_id,$evt_id) {
    global $kga, $conn;

    $zef_id = MySQL::SQLValue($zef_id, MySQL::SQLVALUE_NUMBER  );
    $evt_id = MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER );
    
    $table = $kga['server_prefix']."zef";
    
    $filter['zef_id'] = $zef_id;

    $values['zef_evtID'] = $evt_id;

    $query = MySQL::BuildSQLUpdate($table, $values, $filter);
    
    logfile($query);
    
    return $conn->Query($query);    

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
    global $kga, $conn;

    $zef_ID       = MySQL::SQLValue($zef_ID, MySQL::SQLVALUE_NUMBER  );
    $comment_type = MySQL::SQLValue($comment_type );
    $comment      = MySQL::SQLValue($comment );
    
    $table = $kga['server_prefix']."zef";
    
    $filter['zef_ID'] = $zef_ID;

    $values['zef_comment_type'] = $comment_type;
    $values['zef_comment']      = $comment;

    $query = MySQL::BuildSQLUpdate($table, $values, $filter);
    
    logfile($query);
    
    return $conn->Query($query);    

}

// -----------------------------------------------------------------------------------------------------------

/**
 * return details of specific user
 * DEPRICATED!!
 *
 * @param integer $user ID of user in table usr
 * @global array $kga kimai-global-array
 * @return array
 * @author th 
 */

// checked

function get_usr($usr_id) {
    global $kga, $conn;
    
    $p = $kga['server_prefix'];
    
    $usr_id = MySQL::SQLValue($usr_id, MySQL::SQLVALUE_NUMBER);
    $prefix = $kga['server_prefix'];
        
    $query = "SELECT * FROM ${p}usr Left Join ${p}grp ON usr_grp = grp_ID WHERE usr_ID = $usr_id LIMIT 1;";
    $result = $conn->Query($query);
    if ($result == false) {
        return false;
    }

    $row = $conn->RowArray(0,MYSQL_ASSOC);

    $arr['usr_ID']     = $row['usr_ID'];
    $arr['usr_name']   = $row['usr_name'];
    $arr['usr_alias']  = $row['usr_alias'];
    $arr['usr_grp']    = $row['usr_grp'];
    $arr['usr_sts']    = $row['usr_sts'];
    $arr['grp_name']   = $row['grp_name'];
    $arr['usr_mail']   = $row['usr_mail'];
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

// checked

function usr_name2id($name) {
    global $kga, $conn;

    $filter ['usr_name'] = MySQL::SQLValue($name);
    $columns[] = "usr_ID";
    $table = $kga['server_prefix']."usr";
    
    $result = $conn->SelectRows($table, $filter, $columns);
    if ($result == false) {
        return false;
    }
    
    $row = $conn->RowArray(0,MYSQL_ASSOC);
    return $row['usr_ID'];
}

// -----------------------------------------------------------------------------------------------------------

/**
 * return name of a user with specific ID
 *
 * @param string $id the user's usr_ID
 * @global array $kga kimai-global-array
 * @return int
 * @author th
 */
function usr_id2name($id) {
    global $kga, $conn;
    
    $filter ['usr_ID'] = MySQL::SQLValue($id, MySQL::SQLVALUE_NUMBER);
    $columns[] = "usr_name";
    $table = $kga['server_prefix']."usr";
    
    $result = $conn->SelectRows($table, $filter, $columns);
    if ($result == false) {
        return false;
    }
    
    $row = $conn->RowArray(0,MYSQL_ASSOC);
    return $row['usr_name'];
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns data of the group with ID X
 * DEPRECATED!
 */

// checked

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
 
// fail!

function getUsage($id,$subject) {
    global $kga, $conn;
    
    if (($subject!="pct")&&($subject!="evt")&&($subject!="knd")) {
        return false;
    }
    $id = MySQL::SQLValue($id, MySQL::SQLVALUE_NUMBER);
    $p = $kga['server_prefix'];
    
    switch ($subject) {
        case "pct":
        case "evt":
            $query = "SELECT COUNT(*) AS result FROM ${p}zef WHERE zef_${subject}ID = $id;";
            break;
        case "knd":
            $query = "SELECT COUNT(*) AS result FROM ${p}pct Left Join ${prefix}knd ON pct_kndID = knd_ID WHERE pct_kndID = $id;";
            break;
        default:
            return false;
            break;
    }
    
    $result = $conn->Query($query);
    if ($result == false) {
        return false;
    }
    
    $row = $conn->RowArray(0,MYSQL_ASSOC);
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
 
// checked 

function getjointime($usr_id) {
    global $kga, $conn;
    
    $usr_id = MySQL::SQLValue($usr_id, MySQL::SQLVALUE_NUMBER);
    $p = $kga['server_prefix'];

    $query = "SELECT zef_in FROM ${p}zef WHERE zef_usrID = $usr_id ORDER BY zef_in ASC LIMIT 1;";
    
    $result = $conn->Query($query);
    if ($result == false) {
        return false;
    }

    $result_array = $conn->RowArray(0,MYSQL_NUM);
    
    if ($result_array[0] == 0) {
        return mktime(0,0,0,date("n"),date("j"),date("Y"));        
    } else {
        return $result_array[0];
    }
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
    global $kga,$conn;

    $arr = array();
    $user_id = MySQL::SQLValue($user_id, MySQL::SQLVALUE_NUMBER);

    // check if user is admin
    $filter['usr_ID'] = $user_id;
    $table = $kga['server_prefix']."usr";
    $result = $conn->SelectRows($table, $filter);
    if (! $result) return array();
    $row = $conn->RowArray(0,MYSQL_ASSOC);

    if ($row['usr_sts'] == "0") { // if is admin
      $query = "SELECT * FROM " . $kga['server_prefix'] . "usr WHERE usr_trash=0 ORDER BY usr_name";
      $result = $conn->Query($query);
    }
    else {
      $query = "SELECT * FROM " . $kga['server_prefix'] . "usr INNER JOIN " . $kga['server_prefix'] . "ldr ON usr_grp = grp_ID WHERE usr_trash=0 AND grp_leader = $user_id ORDER BY usr_name";
      $result = $conn->Query($query);
    }

    if (! $result) return array();

    return $conn->RecordsArray(MYSQL_ASSOC);
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
function get_arr_time_usr($in,$out,$users = null, $customers = null, $projects = null, $events = null) {
    global $kga;
    global $conn;
    
    $in    = MySQL::SQLValue($in    , MySQL::SQLVALUE_NUMBER);
    $out   = MySQL::SQLValue($out   , MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];
    
    $whereClauses = zef_whereClausesFromFilters($users,$customers,$projects,$events);
    $whereClauses[] = "${p}usr.usr_trash=0";

    if ($in)
      $whereClauses[]="zef_out > $in";
    if ($out)
      $whereClauses[]="zef_in < $out";
    
    $query = "SELECT zef_in,zef_out, usr_ID, (zef_out - zef_in) / 3600 * zef_rate AS costs
             FROM " . $kga['server_prefix'] . "zef 
             Join " . $kga['server_prefix'] . "pct ON zef_pctID = pct_ID
             Join " . $kga['server_prefix'] . "knd ON pct_kndID = knd_ID
             Join " . $kga['server_prefix'] . "usr ON zef_usrID = usr_ID
             Join " . $kga['server_prefix'] . "evt ON evt_ID    = zef_evtID "
             .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses). " ORDER BY zef_in DESC;";
    $result = $conn->Query($query);
    if (! $result) return array();
    $rows = $conn->RecordsArray(MYSQL_ASSOC);
    if (!$rows) return array();

    $arr = array();
    $zef_in = 0;
    $zef_out = 0;   
    foreach($rows as $row) {
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
        $arr[$row['usr_ID']]['costs'] += (double)$row['costs'];
      }
      else  {
        $arr[$row['usr_ID']]['time']  = (int)($zef_out - $zef_in);
        $arr[$row['usr_ID']]['costs'] = (double)$row['costs'];
      }
    }
    
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns list of time summary attached to customer ID's within specific timespace as array
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
    global $kga;
    global $conn;
    
    $in    = MySQL::SQLValue($in    , MySQL::SQLVALUE_NUMBER);
    $out   = MySQL::SQLValue($out   , MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];
    
    $whereClauses = zef_whereClausesFromFilters($users,$customers,$projects,$events);
    $whereClauses[] = "${p}knd.knd_trash=0";

    if ($in)
      $whereClauses[]="zef_out > $in";
    if ($out)
      $whereClauses[]="zef_in < $out";
    
    
    $query = "SELECT zef_in,zef_out, knd_ID, (zef_out - zef_in) / 3600 * zef_rate AS costs
            FROM " . $kga['server_prefix'] . "zef 
            Left Join " . $kga['server_prefix'] . "pct ON zef_pctID = pct_ID
            Left Join " . $kga['server_prefix'] . "knd ON pct_kndID = knd_ID ".
            (count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses);

    $result = $conn->Query($query);
    if (! $result) return array();
    $rows = $conn->RecordsArray(MYSQL_ASSOC);
    if (!$rows) return array();

    $arr = array();   
    $zef_in = 0;
    $zef_out = 0;
    foreach ($rows as $row) {
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
        $arr[$row['knd_ID']]['costs'] += (double)$row['costs'];
      }
      else {
        $arr[$row['knd_ID']]['time']  = (int)($zef_out - $zef_in);
        $arr[$row['knd_ID']]['costs'] = (double)$row['costs'];
      }
    }
    
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * returns list of time summary attached to project ID's within specific timespace as array
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
function get_arr_time_pct($in,$out,$users = null, $customers = null, $projects = null,$events = null) {
    global $kga;
    global $conn;
    
    $in    = MySQL::SQLValue($in    , MySQL::SQLVALUE_NUMBER);
    $out   = MySQL::SQLValue($out   , MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];

    $whereClauses = zef_whereClausesFromFilters($users,$customers,$projects,$events);
    $whereClauses[] = "${p}pct.pct_trash=0";

    if ($in)
      $whereClauses[]="zef_out > $in";
    if ($out)
      $whereClauses[]="zef_in < $out";
    
    $query = "SELECT zef_in, zef_out ,zef_pctID, (zef_out - zef_in) / 3600 * zef_rate AS costs
        FROM " . $kga['server_prefix'] . "zef 
        Left Join " . $kga['server_prefix'] . "pct ON zef_pctID = pct_ID
        Left Join " . $kga['server_prefix'] . "knd ON pct_kndID = knd_ID ".
        (count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses);
    
    $result = $conn->Query($query);
    if (! $result) return array();
    $rows = $conn->RecordsArray(MYSQL_ASSOC);
    if (!$rows) return array();

    $arr = array(); 
    $zef_in = 0;
    $zef_out = 0;
    foreach ($rows as $row) {
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
        $arr[$row['zef_pctID']]['costs'] += (double)$row['costs'];
      }
      else {
        $arr[$row['zef_pctID']]['time']  = (int)($zef_out - $zef_in);
        $arr[$row['zef_pctID']]['costs'] = (double)$row['costs'];
      }
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
function get_arr_time_evt($in,$out,$users = null, $customers = null, $projects = null, $events = null) {
    global $kga;
    global $conn;
    
    $in    = MySQL::SQLValue($in    , MySQL::SQLVALUE_NUMBER);
    $out   = MySQL::SQLValue($out   , MySQL::SQLVALUE_NUMBER);

    $p     = $kga['server_prefix'];

    $whereClauses = zef_whereClausesFromFilters($users,$customers,$projects,$events);
    $whereClauses[] = "${p}evt.evt_trash = 0";

    if ($in)
      $whereClauses[]="zef_out > $in";
    if ($out)
      $whereClauses[]="zef_in < $out";
    
    $query = "SELECT zef_in, zef_out,zef_evtID, (zef_out - zef_in) / 3600 * zef_rate AS costs
        FROM ${p}zef  
        Left Join ${p}evt ON zef_evtID = evt_ID
        Left Join ${p}pct ON zef_pctID = pct_ID
        Left Join ${p}knd ON pct_kndID = knd_ID ".
        (count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses);
    
    $result = $conn->Query($query);
    if (! $result) return array();
    $rows = $conn->RecordsArray(MYSQL_ASSOC);
    if (!$rows) return array();

    $arr = array(); 
    $zef_in = 0;
    $zef_out = 0;
    foreach ($rows as $row) {
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
        $arr[$row['zef_evtID']]['costs'] += (double)$row['costs'];
      }
      else {
        $arr[$row['zef_evtID']]['time'] = (int)($zef_out - $zef_in);
        $arr[$row['zef_evtID']]['costs'] = (double)$row['costs'];
      }
    }
    return $arr;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Set field usr_sts for users to 1 if user is a group leader, otherwise to 2.
 * Admin status will never be changed.
 * Calling function should start and end sql transaction.
 * 
 * @global array $kga          kimai global array
 * @global array $conn         MySQL connection
 * @author sl
 */
function update_leader_status() {
    global $kga,$conn;
    $query = "UPDATE " . $kga['server_prefix'] . "usr," . $kga['server_prefix'] . "ldr SET usr_sts = 2 WHERE usr_sts = 1";
    $result = $conn->Query($query);
    if ($result == false) {
        return false;
    }
    
    $query = "UPDATE " . $kga['server_prefix'] . "usr," . $kga['server_prefix'] . "ldr SET usr_sts = 1 WHERE usr_sts = 2 AND grp_leader = usr_ID";
    $result = $conn->Query($query);
    if ($result == false) {
        return false;
    }

    return true;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Save rate to database.
 * 
 * @global array $kga          kimai global array
 * @global array $conn         MySQL connection
 * @author sl
 */
function save_rate($user_id,$project_id,$event_id,$rate) {
  global $kga,$conn;
  // validate input
  if ($user_id == NULL || !is_numeric($user_id)) $user_id = "NULL";
  if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
  if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";
  if (!is_numeric($rate)) return false;


  // build update or insert statement
  if (get_rate($user_id,$project_id,$event_id) === false)
    $query = "INSERT INTO " . $kga['server_prefix'] . "rates VALUES($user_id,$project_id,$event_id,$rate);";
  else
    $query = "UPDATE " . $kga['server_prefix'] . "rates SET rate = $rate WHERE ".
  (($user_id=="NULL")?"user_id is NULL":"user_id = $user_id"). " AND ".
  (($project_id=="NULL")?"project_id is NULL":"project_id = $project_id"). " AND ".
  (($event_id=="NULL")?"event_id is NULL":"event_id = $event_id");

  $result = $conn->Query($query);

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
 * @global array $conn         MySQL connection
 * @author sl
 */
function get_rate($user_id,$project_id,$event_id) {
  global $kga,$conn;

  // validate input
  if ($user_id == NULL || !is_numeric($user_id)) $user_id = "NULL";
  if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
  if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";


  $query = "SELECT rate FROM " . $kga['server_prefix'] . "rates WHERE ".
  (($user_id=="NULL")?"user_id is NULL":"user_id = $user_id"). " AND ".
  (($project_id=="NULL")?"project_id is NULL":"project_id = $project_id"). " AND ".
  (($event_id=="NULL")?"event_id is NULL":"event_id = $event_id");

  $result = $conn->Query($query);

  if ($conn->RowCount() == 0)
    return false;

  $data = $conn->rowArray(0,MYSQL_ASSOC);
  return $data['rate'];
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Remove rate from database.
 * 
 * @global array $kga          kimai global array
 * @global array $conn         MySQL connection
 * @author sl
 */
function remove_rate($user_id,$project_id,$event_id) {
  global $kga,$conn;

  // validate input
  if ($user_id == NULL || !is_numeric($user_id)) $user_id = "NULL";
  if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
  if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";


  $query = "DELETE FROM " . $kga['server_prefix'] . "rates WHERE ".
  (($user_id=="NULL")?"user_id is NULL":"user_id = $user_id"). " AND ".
  (($project_id=="NULL")?"project_id is NULL":"project_id = $project_id"). " AND ".
  (($event_id=="NULL")?"event_id is NULL":"event_id = $event_id");

  $result = $conn->Query($query);

  if ($result === false)
    return false;
  else
    return true;
}

// -----------------------------------------------------------------------------------------------------------

/**
 * Query the database for the best fitting rate for the given user, project and event.
 * 
 * @global array $kga          kimai global array
 * @global array $conn         MySQL connection
 * @author sl
 */
function get_best_fitting_rate($user_id,$project_id,$event_id) {
  global $kga,$conn;

  // validate input
  if ($user_id == NULL || !is_numeric($user_id)) $user_id = "NULL";
  if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
  if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";



  $query = "SELECT rate FROM " . $kga['server_prefix'] . "rates WHERE
  (user_id = $user_id OR user_id IS NULL)  AND
  (project_id = $project_id OR project_id IS NULL)  AND
  (event_id = $event_id OR event_id IS NULL)
  ORDER BY user_id DESC, event_id DESC , project_id DESC
  LIMIT 1;";

  $result = $conn->Query($query);

  if ($conn->RowCount() == 0)
    return false;

  $data = $conn->rowArray(0,MYSQL_ASSOC);
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
  global $kga,$conn;
  $p = $kga['server_prefix'];

  $query = "UPDATE ${p}usr SET secure='$keymai',ban=0,banTime=0 WHERE usr_ID='".
    mysql_real_escape_string($userId)."';";
  mysql_query($query);

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
  global $kga,$conn;
    $table = $kga['server_prefix']."usr";

    $filter ['usr_ID']  = MySQL::SQLValue($userId);

    $values ['ban']       = "ban+1";
    if ($resetTime)
      $values ['banTime'] = MySQL::SQLValue(time(),MySQL::SQLVALUE_NUMBER);
    
    $table = $kga['server_prefix']."usr";
    $query = MySQL::BuildSQLUpdate($table, $values, $filter);

    $conn->Query($query);
}

?>
