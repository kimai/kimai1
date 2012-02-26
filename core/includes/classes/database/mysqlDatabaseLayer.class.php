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

include(WEBROOT.'libraries/mysql.class.php');

/**
 * Provides the database layer for MySQL.
 *
 * @author th
 * @author sl
 * @author Kevin Papst
 */
class MySQLDatabaseLayer extends DatabaseLayer {

  /**
   * Connect to the database.
   */
  public function connect($host,$database,$username,$password,$utf8,$serverType) {
    if (isset($utf8) && $utf8)
      $this->conn = new MySQL(true, $database, $host, $username, $password,"utf-8");
    else
      $this->conn = new MySQL(true, $database, $host, $username, $password);
  }

  private function logLastError($scope) {
      Logger::logfile($scope.': '.$this->conn->Error());
  }


  /**
  * Add a new customer to the database.
  *
  * @param array $data  name, address and other data of the new customer
  * @return int         the knd_ID of the new customer, false on failure
  * @author th
  */
  public function knd_create($data) {

      $data = $this->clean_data($data);

      $values     ['knd_name']        =     MySQL::SQLValue($data   ['knd_name']          );
      $values     ['knd_comment']     =     MySQL::SQLValue($data   ['knd_comment']       );
      if (isset($data['knd_password']))
        $values   ['knd_password']    =     MySQL::SQLValue($data   ['knd_password']      );
      else
        $values   ['knd_password']    =     "''";
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
      $values     ['knd_timezone']    =     MySQL::SQLValue($data   ['knd_timezone']      );

      $values['knd_visible'] = MySQL::SQLValue($data['knd_visible'] , MySQL::SQLVALUE_NUMBER  );
      $values['knd_filter']  = MySQL::SQLValue($data['knd_filter']  , MySQL::SQLVALUE_NUMBER  );

      $table = $this->kga['server_prefix']."knd";
      $result = $this->conn->InsertRow($table, $values);

      if (! $result) {
        $this->logLastError('knd_create');
        return false;
      } else {
        return $this->conn->GetLastInsertID();
      }
  }

  /**
  * Returns the data of a certain customer
  *
  * @param array $knd_id  knd_id of the customer
  * @return array         the customer's data (name, address etc) as array, false on failure
  * @author th
  */
  public function knd_get_data($knd_id) {
      $filter['knd_ID'] = MySQL::SQLValue($knd_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."knd";
      $result = $this->conn->SelectRows($table, $filter);

      if (! $result) {
        $this->logLastError('knd_get_data');
        return false;
      } else {
          return $this->conn->RowArray(0,MYSQL_ASSOC);
      }
  }

  /**
  * Edits a customer by replacing his data by the new array
  *
  * @param array $knd_id  knd_id of the customer to be edited
  * @param array $data    name, address and other new data of the customer
  * @return boolean       true on success, false on failure
  * @author ob/th
  */
  public function knd_edit($knd_id, $data) {
      $data = $this->clean_data($data);

      $values = array();

      $strings = array(
        'knd_name'    ,'knd_comment','knd_password' ,'knd_company','knd_vat',
        'knd_contact' ,'knd_street' ,'knd_zipcode'  ,'knd_city'   ,'knd_tel',
        'knd_fax'     ,'knd_mobile' ,'knd_mail'     ,'knd_homepage', 'knd_timezone');
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

      $table = $this->kga['server_prefix']."knd";
      $query = MySQL::BuildSQLUpdate($table, $values, $filter);

      return $this->conn->Query($query);
  }

  /**
  * Assigns a customer to 1-n groups by adding entries to the cross table
  *
  * @param int $knd_id         knd_id of the customer to which the groups will be assigned
  * @param array $grp_array    contains one or more grp_IDs
  * @return boolean            true on success, false on failure
  * @author ob/th
  */
  public function assign_knd2grps($knd_id, $grp_array) {
      if (! $this->conn->TransactionBegin()) {
        $this->logLastError('assign_knd2grps');
        return false;
      }

      $table = $this->kga['server_prefix']."grp_knd";
      $filter['knd_ID'] = MySQL::SQLValue($knd_id, MySQL::SQLVALUE_NUMBER);
      $d_query = MySQL::BuildSQLDelete($table, $filter);
      $d_result = $this->conn->Query($d_query);

      if ($d_result == false) {
              $this->logLastError('assign_knd2grps');
              $this->conn->TransactionRollback();
              return false;
      }

      foreach ($grp_array as $current_grp) {
          $values['grp_ID'] = MySQL::SQLValue($current_grp , MySQL::SQLVALUE_NUMBER);
          $values['knd_ID'] = MySQL::SQLValue($knd_id      , MySQL::SQLVALUE_NUMBER);
          $query = MySQL::BuildSQLInsert($table, $values);
          $result = $this->conn->Query($query);

          if ($result == false) {
                  $this->logLastError('assign_knd2grps');
                  $this->conn->TransactionRollback();
                  return false;
          }
      }

      if ($this->conn->TransactionEnd() == true) {
          return true;
      } else {
          $this->logLastError('assign_knd2grps');
          return false;
      }
  }

  /**
  * returns all the groups of the given customer
  *
  * @param array $knd_id  knd_id of the customer
  * @return array         contains the grp_IDs of the groups or false on error
  * @author th
  */
  public function knd_get_grps($knd_id) {
      $filter['knd_ID'] = MySQL::SQLValue($knd_id, MySQL::SQLVALUE_NUMBER);
      $columns[]        = "grp_ID";
      $table = $this->kga['server_prefix']."grp_knd";

      $result = $this->conn->SelectRows($table, $filter, $columns);
      if ($result == false) {
          return false;
      }

      $return_grps = array();
      $counter     = 0;

      $rows = $this->conn->RecordsArray(MYSQL_ASSOC);

      if ($this->conn->RowCount()) {
          foreach ($rows as $current_grp) {
              $return_grps[$counter] = $current_grp['grp_ID'];
              $counter++;
          }
          return $return_grps;
      } else {
          $this->logLastError('knd_get_grps');
          return false;
      }
  }

  /**
  * deletes a customer
  *
  * @param array $knd_id  knd_id of the customer
  * @return boolean       true on success, false on failure
  * @author th
  */
  public function knd_delete($knd_id) {
      $values['knd_trash'] = 1;
      $filter['knd_ID'] = MySQL::SQLValue($knd_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."knd";

      $query = MySQL::BuildSQLUpdate($table, $values, $filter);
      return $this->conn->Query($query);
  }

  /**
  * Adds a new project
  *
  * @param array $data  name, comment and other data of the new project
  * @return int         the pct_ID of the new project, false on failure
  * @author th
  */
  public function pct_create($data) {
      $data = $this->clean_data($data);

      $values['pct_name']    = MySQL::SQLValue($data['pct_name']    );
      $values['pct_comment'] = MySQL::SQLValue($data['pct_comment'] );
      $values['pct_budget']  = MySQL::SQLValue($data['pct_budget']  , MySQL::SQLVALUE_NUMBER );
      $values['pct_effort']  = MySQL::SQLValue($data['pct_effort']  , MySQL::SQLVALUE_NUMBER );
      $values['pct_approved']= MySQL::SQLValue($data['pct_approved'], MySQL::SQLVALUE_NUMBER );
      $values['pct_kndID']   = MySQL::SQLValue($data['pct_kndID']   , MySQL::SQLVALUE_NUMBER );
      $values['pct_visible'] = MySQL::SQLValue($data['pct_visible'] , MySQL::SQLVALUE_NUMBER );
      $values['pct_internal']= MySQL::SQLValue($data['pct_internal'], MySQL::SQLVALUE_NUMBER );
      $values['pct_filter']  = MySQL::SQLValue($data['pct_filter']  , MySQL::SQLVALUE_NUMBER );

      $table = $this->kga['server_prefix']."pct";
      $result = $this->conn->InsertRow($table, $values);

      if (! $result) {
        $this->logLastError('pct_create');
        return false;
      }

      $pct_id = $this->conn->GetLastInsertID();

      if (isset($data['pct_default_rate'])) {
        if (is_numeric($data['pct_default_rate']))
          $this->save_rate(NULL,$pct_id,NULL,$data['pct_default_rate']);
        else
          $this->remove_rate(NULL,$pct_id,NULL);
      }

      if (isset($data['pct_my_rate'])) {
        if (is_numeric($data['pct_my_rate']))
          $this->save_rate($this->kga['usr']['usr_ID'],$pct_id,NULL,$data['pct_my_rate']);
        else
          $this->remove_rate($this->kga['usr']['usr_ID'],$pct_id,NULL);
      }

      if (isset($data['pct_fixed_rate'])) {
        if (is_numeric($data['pct_fixed_rate']))
          $this->save_fixed_rate($pct_id,NULL,$data['pct_fixed_rate']);
        else
          $this->remove_fixed_rate($pct_id,NULL);
      }

      return $pct_id;
  }

  /**
  * Returns the data of a certain project
  *
  * @param array $pct_id  pct_id of the project

  * @return array         the project's data (name, comment etc) as array, false on failure
  * @author th
  */
  public function pct_get_data($pct_id) {
      if (!is_numeric($pct_id)) {
          return false;
      }

      $filter['pct_ID'] = MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."pct";
      $result = $this->conn->SelectRows($table, $filter);

      if (! $result) {
        $this->logLastError('pct_get_data');
        return false;
      }

      $result_array = $this->conn->RowArray(0,MYSQL_ASSOC);
      $result_array['pct_default_rate'] = $this->get_rate(NULL,$pct_id,NULL);
      $result_array['pct_my_rate'] = $this->get_rate($this->kga['usr']['usr_ID'],$pct_id,NULL);
      $result_array['pct_fixed_rate'] = $this->get_fixed_rate($pct_id,NULL);
      return $result_array;
  }

  /**
  * Edits a project by replacing its data by the new array
  *
  * @param array $pct_id   pct_id of the project to be edited
  * @param array $data     name, comment and other new data of the project
  * @return boolean        true on success, false on failure
  * @author ob/th
  */
  public function pct_edit($pct_id, $data) {
      $data = $this->clean_data($data);

      $strings = array('pct_name', 'pct_comment');
      foreach ($strings as $key) {
        if (isset($data[$key]))
          $values[$key] = MySQL::SQLValue($data[$key]);
      }

      $numbers = array(
      'pct_budget', 'pct_kndID', 'pct_visible', 'pct_internal', 'pct_filter', 'pct_effort', 'pct_approved');
      foreach ($numbers as $key) {
        if (isset($data[$key]))
          $values[$key] = MySQL::SQLValue($data[$key] , MySQL::SQLVALUE_NUMBER );
      }

      $filter ['pct_ID'] = MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."pct";


      if (! $this->conn->TransactionBegin()) {
        $this->logLastError('pct_edit');
        return false;
      }

      $query = MySQL::BuildSQLUpdate($table, $values, $filter);

      if ($this->conn->Query($query)) {

          if (isset($data['pct_default_rate'])) {
            if (is_numeric($data['pct_default_rate']))
              $this->save_rate(NULL,$pct_id,NULL,$data['pct_default_rate']);
            else
              $this->remove_rate(NULL,$pct_id,NULL);
          }

          if (isset($data['pct_my_rate'])) {
            if (is_numeric($data['pct_my_rate']))
              $this->save_rate($this->kga['usr']['usr_ID'],$pct_id,NULL,$data['pct_my_rate']);
            else
              $this->remove_rate($this->kga['usr']['usr_ID'],$pct_id,NULL);
          }

          if (isset($data['pct_fixed_rate'])) {
            if (is_numeric($data['pct_fixed_rate']))
              $this->save_fixed_rate($pct_id,NULL,$data['pct_fixed_rate']);
            else
              $this->remove_fixed_rate($pct_id,NULL);
          }

          if (! $this->conn->TransactionEnd()) {
            $this->logLastError('pct_edit');
            return false;
          }
          return true;
      } else {
          $this->logLastError('pct_edit');
          if (! $this->conn->TransactionRollback()) {
            $this->logLastError('pct_edit');
            return false;
          }
          return false;
      }
  }

  /**
  * Assigns a project to 1-n groups by adding entries to the cross table
  *
  * @param int $pct_id        pct_id of the project to which the groups will be assigned
  * @param array $grp_array    contains one or more grp_IDs
  * @return boolean            true on success, false on failure
  * @author ob/th
  */
  public function assign_pct2grps($pct_id, $grp_array) {


      if (! $this->conn->TransactionBegin()) {
        $this->logLastError('assign_pct2grps');
        return false;
      }

      $table = $this->kga['server_prefix']."grp_pct";
      $filter['pct_ID'] = MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER);
      $d_query = MySQL::BuildSQLDelete($table, $filter);
      $d_result = $this->conn->Query($d_query);

      if ($d_result == false) {
              $this->logLastError('assign_pct2grps');
              $this->conn->TransactionRollback();
              return false;
      }

      foreach ($grp_array as $current_grp) {

        $values['grp_ID']   = MySQL::SQLValue($current_grp , MySQL::SQLVALUE_NUMBER);
        $values['pct_ID']   = MySQL::SQLValue($pct_id      , MySQL::SQLVALUE_NUMBER);
        $query = MySQL::BuildSQLInsert($table, $values);
        $result = $this->conn->Query($query);

        if ($result == false) {
                $this->logLastError('assign_pct2grps');
                $this->conn->TransactionRollback();
                return false;
        }
      }

      if ($this->conn->TransactionEnd() == true) {
          return true;
      } else {
          $this->logLastError('assign_pct2grps');
          return false;
      }
  }

  /**
  * returns all the groups of the given project
  *
  * @param array $pct_id  pct_id of the project
  * @return array         contains the grp_IDs of the groups or false on error
  * @author th
  */
  public function pct_get_grps($pct_id) {


      $filter['pct_ID'] = MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER);
      $columns[]        = "grp_ID";
      $table = $this->kga['server_prefix']."grp_pct";

      $result = $this->conn->SelectRows($table, $filter, $columns);
      if ($result == false) {
          $this->logLastError('pct_get_grps');
          return false;
      }

      $return_grps = array();
      $counter     = 0;

      $rows = $this->conn->RecordsArray(MYSQL_ASSOC);

      if ($this->conn->RowCount()) {
          foreach ($rows as $current_grp) {
              $return_grps[$counter] = $current_grp['grp_ID'];
              $counter++;
          }
          return $return_grps;
      } else {
          return false;
      }
  }

  /**
  * deletes a project
  *
  * @param array $pct_id  pct_id of the project
  * @return boolean       true on success, false on failure
  * @author th
  */
  public function pct_delete($pct_id) {


      $values['pct_trash'] = 1;
      $filter['pct_ID'] = MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."pct";

      $query = MySQL::BuildSQLUpdate($table, $values, $filter);
      return $this->conn->Query($query);
  }

  /**
  * Adds a new event
  *
  * @param array $data   name, comment and other data of the new event
  * @return int          the evt_ID of the new project, false on failure
  * @author th
  */
  public function evt_create($data) {
      $data = $this->clean_data($data);

      $values['evt_name']    = MySQL::SQLValue($data['evt_name']    );
      $values['evt_comment'] = MySQL::SQLValue($data['evt_comment'] );
      $values['evt_visible'] = MySQL::SQLValue($data['evt_visible'] , MySQL::SQLVALUE_NUMBER );
      $values['evt_filter']  = MySQL::SQLValue($data['evt_filter']  , MySQL::SQLVALUE_NUMBER );
      $values['evt_assignable'] = MySQL::SQLValue($data['evt_assignable']  , MySQL::SQLVALUE_NUMBER );

      $table = $this->kga['server_prefix']."evt";
      $result = $this->conn->InsertRow($table, $values);

      if (! $result) {
        $this->logLastError('evt_create');
        return false;
      }

      $evt_id = $this->conn->GetLastInsertID();

      if (isset($data['evt_default_rate'])) {
        if (is_numeric($data['evt_default_rate']))
          $this->save_rate(NULL,NULL,$evt_id,$data['evt_default_rate']);
        else
          $this->remove_rate(NULL,NULL,$evt_id);
      }

      if (isset($data['evt_my_rate'])) {
        if (is_numeric($data['evt_my_rate']))
          $this->save_rate($this->kga['usr']['usr_ID'],NULL,$evt_id,$data['evt_my_rate']);
        else
          $this->remove_rate($this->kga['usr']['usr_ID'],NULL,$evt_id);
      }

      if (isset($data['evt_fixed_rate'])) {
        if (is_numeric($data['evt_fixed_rate']))
          $this->save_fixed_rate(NULL,$evt_id,$data['evt_fixed_rate']);
        else
          $this->remove_fixed_rate(NULL,$evt_id);
      }

      return $evt_id;
  }

  /**
  * Returns the data of a certain task
  *
  * @param array $evt_id  evt_id of the project
  * @return array         the event's data (name, comment etc) as array, false on failure
  * @author th
  */
  public function evt_get_data($evt_id) {
      $filter['evt_ID'] = MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."evt";
      $result = $this->conn->SelectRows($table, $filter);

      if (! $result) {
        $this->logLastError('evt_get_data');
        return false;
      }


      $result_array = $this->conn->RowArray(0,MYSQL_ASSOC);

      $result_array['evt_default_rate'] = $this->get_rate(NULL,NULL,$result_array['evt_ID']);
      $result_array['evt_my_rate'] = $this->get_rate($this->kga['usr']['usr_ID'],NULL,$result_array['evt_ID']);
      $result_array['evt_fixed_rate'] = $this->get_fixed_rate(NULL,$result_array['evt_ID']);

      return $result_array;
  }

  /**
  * Edits an event by replacing its data by the new array
  *
  * @param array $evt_id  evt_id of the project to be edited
  * @param array $data    name, comment and other new data of the event
  * @return boolean       true on success, false on failure
  * @author th
  */
  public function evt_edit($evt_id, $data) {


      $data = $this->clean_data($data);


      $strings = array('evt_name', 'evt_comment');
      foreach ($strings as $key) {
        if (isset($data[$key]))
          $values[$key] = MySQL::SQLValue($data[$key]);
      }

      $numbers = array('evt_visible', 'evt_filter', 'evt_assignable');
      foreach ($numbers as $key) {
        if (isset($data[$key]))
          $values[$key] = MySQL::SQLValue($data[$key] , MySQL::SQLVALUE_NUMBER );
      }

      $filter  ['evt_ID']          =   MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."evt";

      if (! $this->conn->TransactionBegin()) {
        $this->logLastError('evt_edit');
        return false;
      }

      $query = MySQL::BuildSQLUpdate($table, $values, $filter);

      if ($this->conn->Query($query)) {

          if (isset($data['evt_default_rate'])) {
            if (is_numeric($data['evt_default_rate']))
              $this->save_rate(NULL,NULL,$evt_id,$data['evt_default_rate']);
            else
              $this->remove_rate(NULL,NULL,$evt_id);
          }

          if (isset($data['evt_my_rate'])) {
            if (is_numeric($data['evt_my_rate']))
              $this->save_rate($this->kga['usr']['usr_ID'],NULL,$evt_id,$data['evt_my_rate']);
            else
              $this->remove_rate($this->kga['usr']['usr_ID'],NULL,$evt_id);
          }

          if (isset($data['evt_fixed_rate'])) {
            if (is_numeric($data['evt_fixed_rate']))
              $this->save_fixed_rate(NULL,$evt_id,$data['evt_fixed_rate']);
            else
              $this->remove_fixed_rate(NULL,$evt_id);
          }

          if (! $this->conn->TransactionEnd()) {
            $this->logLastError('evt_edit');
            return false;
          }
          return true;
      } else {
          $this->logLastError('evt_edit');
          if (! $this->conn->TransactionRollback()) {
            $this->logLastError('evt_edit');
            return false;
          }
          return false;
      }
  }

  /**
  * Assigns an event to 1-n groups by adding entries to the cross table
  *
  * @param int $evt_id         evt_id of the project to which the groups will be assigned
  * @param array $grp_array    contains one or more grp_IDs
  * @return boolean            true on success, false on failure
  * @author ob/th
  */
  public function assign_evt2grps($evt_id, $grp_array) {
      if (! $this->conn->TransactionBegin()) {
        $this->logLastError('assign_evt2grps');
        return false;
      }

      $table = $this->kga['server_prefix']."grp_evt";
      $filter['evt_ID'] = MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER);
      $d_query = MySQL::BuildSQLDelete($table, $filter);
      $d_result = $this->conn->Query($d_query);

      if ($d_result == false) {
          $this->logLastError('assign_evt2grps');
          $this->conn->TransactionRollback();
          return false;
      }

      foreach ($grp_array as $current_grp) {
        $values['grp_ID'] = MySQL::SQLValue($current_grp , MySQL::SQLVALUE_NUMBER);
        $values['evt_ID'] = MySQL::SQLValue($evt_id      , MySQL::SQLVALUE_NUMBER);
        $query = MySQL::BuildSQLInsert($table, $values);
        $result = $this->conn->Query($query);

        if ($result == false) {
            $this->logLastError('assign_evt2grps');
            $this->conn->TransactionRollback();
            return false;
        }
      }

      if ($this->conn->TransactionEnd() == true) {
          return true;
      } else {
          $this->logLastError('assign_evt2grps');
          return false;
      }
  }

  /**
  * Assigns an event to 1-n projects by adding entries to the cross table
  *
  * @param int $evt_id         id of the event to which projects will be assigned
  * @param array $gpct_array    contains one or more pct_IDs
  * @return boolean            true on success, false on failure
  * @author ob/th
  */
  public function assign_evt2pcts($evt_id, $pct_array) {
      if (! $this->conn->TransactionBegin()) {
        $this->logLastError('assign_evt2pcts');
        return false;
      }

      $table = $this->kga['server_prefix']."pct_evt";
      $filter['evt_ID'] = MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER);
      $d_query = MySQL::BuildSQLDelete($table, $filter);
      $d_result = $this->conn->Query($d_query);

      if ($d_result == false) {
          $this->logLastError('assign_evt2pcts');
          $this->conn->TransactionRollback();
          return false;
      }

      foreach ($pct_array as $current_pct) {
        $values['pct_ID'] = MySQL::SQLValue($current_pct , MySQL::SQLVALUE_NUMBER);
        $values['evt_ID'] = MySQL::SQLValue($evt_id      , MySQL::SQLVALUE_NUMBER);
        $query = MySQL::BuildSQLInsert($table, $values);
        $result = $this->conn->Query($query);

        if ($result == false) {
            $this->logLastError('assign_evt2pcts');
            $this->conn->TransactionRollback();
            return false;
        }
      }

      if ($this->conn->TransactionEnd() == true) {
          return true;
      } else {
          $this->logLastError('assign_evt2pcts');
          return false;
      }
  }

  /**
  * Assigns 1-n events to a project by adding entries to the cross table
  *
  * @param int $pct_id         id of the project to which events will be assigned
  * @param array $evt_array    contains one or more evt_IDs
  * @return boolean            true on success, false on failure
  * @author sl
  */
  public function assign_pct2evts($pct_id, $evt_array) {
      if (! $this->conn->TransactionBegin()) {
        $this->logLastError('assign_pct2evts');
        return false;
      }

      $table = $this->kga['server_prefix']."pct_evt";
      $filter['pct_ID'] = MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER);
      $d_query = MySQL::BuildSQLDelete($table, $filter);
      $d_result = $this->conn->Query($d_query);

      if ($d_result == false) {
          $this->logLastError('assign_pct2evts');
          $this->conn->TransactionRollback();
          return false;
      }

      foreach ($evt_array as $current_evt) {
        $values['evt_ID'] = MySQL::SQLValue($current_evt , MySQL::SQLVALUE_NUMBER);
        $values['pct_ID'] = MySQL::SQLValue($pct_id      , MySQL::SQLVALUE_NUMBER);
        $query = MySQL::BuildSQLInsert($table, $values);
        $result = $this->conn->Query($query);

        if ($result == false) {
            $this->logLastError('assign_pct2evts');
            $this->conn->TransactionRollback();
            return false;
        }
      }

      if ($this->conn->TransactionEnd() == true) {
          return true;
      } else {
          $this->logLastError('assign_pct2evts');
          return false;
      }
  }

  /**
  * returns all the projects to which the event was assigned
  *
  * @param array $evt_id  evt_id of the project
  * @return array         contains the pct_IDs of the projects or false on error
  * @author th
  */
  public function evt_get_pcts($evt_id) {
      $filter ['evt_ID'] = MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER);
      $columns[]         = "pct_ID";
      $table = $this->kga['server_prefix']."pct_evt";

      $result = $this->conn->SelectRows($table, $filter, $columns);
      if ($result == false) {
          $this->logLastError('evt_get_pcts');
          return false;
      }

      $return_grps = array();
      $counter     = 0;

      $rows = $this->conn->RecordsArray(MYSQL_ASSOC);

      if ($this->conn->RowCount()) {
          foreach ($rows as $current_grp) {
              $return_grps[$counter] = $current_grp['pct_ID'];
              $counter++;
          }
          return $return_grps;
      } else {
          return false;
      }
  }

  /**
   *
   * update the data for event per project, which is budget, approved and effort
   * @param integer $pct_id
   * @param integer $evt_id
   * @param array $data
   */
  public function pct_evt_edit($pct_id, $evt_id, $data) {

      $data = $this->clean_data($data);

      $filter  ['pct_ID']          =   MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER);
      $filter  ['evt_ID']          =   MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."pct_evt";

      if (! $this->conn->TransactionBegin()) {
        $this->logLastError('pct_evt_edit');
        return false;
      }

      $query = MySQL::BuildSQLUpdate($table, $data, $filter);
      if ($this->conn->Query($query)) {

          if (! $this->conn->TransactionEnd()) {
            $this->logLastError('pct_evt_edit');
            return false;
          }
          return true;
      } else {
          $this->logLastError('pct_evt_edit');
          if (! $this->conn->TransactionRollback()) {
            $this->logLastError('pct_evt_edit');
            return false;
          }
          return false;
      }
  }

  /**
  * returns all the events which were assigned to a project
  *
  * @param integer $pct_id  pct_id of the project
  * @return array         contains the evt_IDs of the events or false on error
  * @author sl
  */
  public function pct_get_evts($pct_id) {
      $projectId = MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER);
      $p = $this->kga['server_prefix'];

      $query = "SELECT ${p}pct_evt.evt_ID, ${p}evt.evt_budget, ${p}evt.evt_effort,
				${p}evt.evt_approved FROM ${p}pct_evt JOIN ${p}evt ON
				${p}evt.evt_ID = ${p}pct_evt.evt_ID WHERE ${p}pct_evt.pct_ID = $projectId AND ${p}evt.evt_trash=0;";

      $result = $this->conn->Query($query);

      if ($result == false) {
          $this->logLastError('pct_get_evts');
          return false;
      }

      $return_evts = array();
      $counter     = 0;

      $rows = $this->conn->RecordsArray(MYSQL_ASSOC);
      return $rows;
  }

  /**
  * returns all the groups of the given event
  *
  * @param array $evt_id  evt_id of the project
  * @return array         contains the grp_IDs of the groups or false on error
  * @author th
  */
  public function evt_get_grps($evt_id) {
      $filter ['evt_ID'] = MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER);
      $columns[]         = "grp_ID";
      $table = $this->kga['server_prefix']."grp_evt";

      $result = $this->conn->SelectRows($table, $filter, $columns);
      if ($result == false) {
          $this->logLastError('evt_get_grps');
          return false;
      }

      $return_grps = array();
      $counter     = 0;

      $rows = $this->conn->RecordsArray(MYSQL_ASSOC);

      if ($this->conn->RowCount()) {
          foreach ($rows as $current_grp) {
              $return_grps[$counter] = $current_grp['grp_ID'];
              $counter++;
          }
          return $return_grps;
      } else {
          return false;
      }
  }

  /**
  * deletes an event
  *
  * @param array $evt_id  evt_id of the event
  * @return boolean       true on success, false on failure
  * @author th
  */
  public function evt_delete($evt_id) {


      $values['evt_trash'] = 1;
      $filter['evt_ID'] = MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."evt";

      $query = MySQL::BuildSQLUpdate($table, $values, $filter);
      return $this->conn->Query($query);
  }

  /**
  * Assigns a group to 1-n customers by adding entries to the cross table
  * (counterpart to assign_knd2grp)
  *
  * @param array $grp_id        grp_id of the group to which the customers will be assigned
  * @param array $knd_array    contains one or more knd_IDs
  * @return boolean            true on success, false on failure
  * @author ob/th
  */
  public function assign_grp2knds($grp_id, $knd_array) {
      if (! $this->conn->TransactionBegin()) {
        $this->logLastError('assign_grp2knds');
        return false;
      }

      $table = $this->kga['server_prefix']."grp_knd";
      $filter['grp_ID'] = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
      $d_query = MySQL::BuildSQLDelete($table, $filter);

      $d_result = $this->conn->Query($d_query);

      if ($d_result == false) {
              $this->logLastError('assign_grp2knds');
              $this->conn->TransactionRollback();
              return false;
      }

      foreach ($knd_array as $current_knd) {
        $values['grp_ID']       = MySQL::SQLValue($grp_id      , MySQL::SQLVALUE_NUMBER);
        $values['knd_ID']       = MySQL::SQLValue($current_knd , MySQL::SQLVALUE_NUMBER);
        $query = MySQL::BuildSQLInsert($table, $values);
        $result = $this->conn->Query($query);

        if ($result == false) {
                $this->logLastError('assign_grp2knds');
                $this->conn->TransactionRollback();
                return false;
        }
      }

      if ($this->conn->TransactionEnd() == true) {
          return true;
      } else {
          $this->logLastError('assign_grp2knds');
          return false;
      }
  }

  /**
  * Assigns a group to 1-n projects by adding entries to the cross table
  * (counterpart to assign_pct2grp)
  *
  * @param array $grp_id        grp_id of the group to which the projects will be assigned
  * @param array $pct_array    contains one or more pct_IDs
  * @return boolean            true on success, false on failure
  * @author ob
  */
  public function assign_grp2pcts($grp_id, $pct_array) {
      if (! $this->conn->TransactionBegin()) {
        $this->logLastError('assign_grp2pcts');
        return false;
      }

      $table = $this->kga['server_prefix']."grp_pct";
      $filter['grp_ID'] = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
      $d_query = MySQL::BuildSQLDelete($table, $filter);
      $d_result = $this->conn->Query($d_query);

      if ($d_result == false) {
              $this->logLastError('assign_grp2pcts');
              $this->conn->TransactionRollback();
              return false;
      }

      foreach ($pct_array as $current_pct) {
        $values['grp_ID'] = MySQL::SQLValue($grp_id      , MySQL::SQLVALUE_NUMBER);
        $values['pct_ID'] = MySQL::SQLValue($current_pct , MySQL::SQLVALUE_NUMBER);
        $query = MySQL::BuildSQLInsert($table, $values);
        $result = $this->conn->Query($query);

        if ($result == false) {
            $this->logLastError('assign_grp2pcts');
            $this->conn->TransactionRollback();
            return false;
        }
      }

      if ($this->conn->TransactionEnd() == true) {
          return true;
      } else {
          $this->logLastError('assign_grp2pcts');
          return false;
      }
  }

  /**
  * Assigns a group to 1-n events by adding entries to the cross table
  * (counterpart to assign_evt2grp)
  *
  * @param array $grp_id        grp_id of the group to which the events will be assigned
  * @param array $evt_array    contains one or more evt_IDs
  * @return boolean            true on success, false on failure
  * @author ob
  */
  public function assign_grp2evts($grp_id, $evt_array) {
      if (! $this->conn->TransactionBegin()) {
        $this->logLastError('assign_grp2evts');
        return false;
      }

      $table = $this->kga['server_prefix']."grp_evt";
      $filter['grp_ID'] = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
      $d_query = MySQL::BuildSQLDelete($table, $filter);
      $d_result = $this->conn->Query($d_query);

      if ($d_result == false) {
          $this->logLastError('assign_grp2evts');
          $this->conn->TransactionRollback();
          return false;
      }

      foreach ($evt_array as $current_evt) {
        $values['grp_ID'] = MySQL::SQLValue($grp_id      , MySQL::SQLVALUE_NUMBER);
        $values['evt_ID'] = MySQL::SQLValue($current_evt , MySQL::SQLVALUE_NUMBER);
        $query = MySQL::BuildSQLInsert($table, $values);
        $result = $this->conn->Query($query);

        if ($result == false) {
            $this->logLastError('assign_grp2evts');
            $this->conn->TransactionRollback();
            return false;
        }
      }

      if ($this->conn->TransactionEnd() == true) {
          return true;
      } else {
          $this->logLastError('assign_grp2evts');
          return false;
      }
  }

  /**
  * Adds a new user
  *
  * @param array $data  username, email, and other data of the new user
  * @return boolean|integer     false on failure, otherwise the new user id
  * @author th
  */
  public function usr_create($data) {
      // find random but unused user id
      do {
        $data['usr_ID'] = random_number(9);
      } while ($this->usr_get_data($data['usr_ID']));

      $data = $this->clean_data($data);

      $values ['usr_name']   = MySQL::SQLValue($data['usr_name']);
      $values ['usr_ID']     = MySQL::SQLValue($data['usr_ID']    , MySQL::SQLVALUE_NUMBER);
      $values ['usr_sts']    = MySQL::SQLValue($data['usr_sts']   , MySQL::SQLVALUE_NUMBER);
      $values ['usr_active'] = MySQL::SQLValue($data['usr_active'], MySQL::SQLVALUE_NUMBER);

      $table  = $this->kga['server_prefix']."usr";
      $result = $this->conn->InsertRow($table, $values);

      if ($result===false) {
        $this->logLastError('usr_create');
        return false;
      }

      if (isset($data['usr_rate'])) {
        if (is_numeric($data['usr_rate'])) {
          $this->save_rate($data['usr_ID'], NULL, NULL, $data['usr_rate']);
        } else {
          $this->remove_rate($data['usr_ID'], NULL, NULL);
        }
      }
    
      return $data['usr_ID'];
  }

  /**
  * Returns the data of a certain user
  *
  * @param array $usr_id  knd_id of the user
  * @return array         the user's data (username, email-address, status etc) as array, false on failure
  * @author th
  */
  public function usr_get_data($usr_id)
  {
      $filter['usr_ID'] = MySQL::SQLValue($usr_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."usr";
      $result = $this->conn->SelectRows($table, $filter);

      if (!$result) {
        $this->logLastError('usr_get_data');
        return false;
      }

      // return  $this->conn->getHTML();
      return $this->conn->RowArray(0,MYSQL_ASSOC);
  }

  /**
  * Edits a user by replacing his data and preferences by the new array
  *
  * @param array $usr_id  usr_id of the user to be edited
  * @param array $data    username, email, and other new data of the user
  * @return boolean       true on success, false on failure
  * @author ob/th
  */
  public function usr_edit($usr_id, $data)
  {
      $data = $this->clean_data($data);

      $strings = array('usr_name', 'usr_mail', 'usr_alias', 'pw', 'apikey');
      foreach ($strings as $key) {
        if (isset($data[$key]))
          $values[$key] = MySQL::SQLValue($data[$key]);
      }

      $numbers = array('usr_sts' ,'usr_trash' ,'usr_active', 'lastProject' ,'lastEvent' ,'lastRecord');
      foreach ($numbers as $key) {
        if (isset($data[$key]))
          $values[$key] = MySQL::SQLValue($data[$key] , MySQL::SQLVALUE_NUMBER );
      }

      $filter['usr_ID'] = MySQL::SQLValue($usr_id, MySQL::SQLVALUE_NUMBER);
      $table            = $this->kga['server_prefix']."usr";

      if (!$this->conn->TransactionBegin()) {
        $this->logLastError('usr_edit');
        return false;
      }

      $query = MySQL::BuildSQLUpdate($table, $values, $filter);

      if ($this->conn->Query($query))
      {
          if (isset($data['usr_rate'])) {
            if (is_numeric($data['usr_rate'])) {
              $this->save_rate($usr_id,NULL,NULL,$data['usr_rate']);
            } else {
              $this->remove_rate($usr_id,NULL,NULL);
            }
          }

          if (! $this->conn->TransactionEnd()) {
            $this->logLastError('usr_edit');
            return false;
          }

          return true;
      }

      if (!$this->conn->TransactionRollback()) {
        $this->logLastError('usr_edit');
        return false;
      }

      $this->logLastError('usr_edit');
      return false;
  }

  /**
  * deletes a user
  *
  * @param array $usr_id  usr_id of the user
  * @return boolean       true on success, false on failure
  * @author th
  */
  public function usr_delete($usr_id) {
      $values['usr_trash'] = 1;
      $filter['usr_ID'] = MySQL::SQLValue($usr_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."usr";

      $query = MySQL::BuildSQLUpdate($table, $values, $filter);
      return $this->conn->Query($query);
  }

  /**
  * Get a preference for a user. If no user ID is given the current user is used.
  *
  * @param string  $key     name of the preference to fetch
  * @param integer $userId  (optional) id of the user to fetch the preference for
  * @return string value of the preference or null if there is no such preference
  * @author sl
  */
  public function usr_get_preference($key,$userId=null) {
      if ($userId === null)
        $userId = $this->kga['usr']['usr_ID'];

      $table  = $this->kga['server_prefix']."preferences";
      $userId = MySQL::SQLValue($userId,  MySQL::SQLVALUE_NUMBER);
      $key    = MySQL::SQLValue($key);

      $query = "SELECT var,value FROM $table WHERE userID = $userId AND var = $key";

      $this->conn->Query($query);

      if ($this->conn->RowCount() == 0)
        return null;

      if ($this->conn->RowCount() == 1) {
        $row = $this->conn->RowArray(0,MYSQL_NUM);
        return $row[1];
      }
  }

  /**
  * Get several preferences for a user. If no user ID is given the current user is used.
  *
  * @param array   $keys    names of the preference to fetch in an array
  * @param integer $userId  (optional) id of the user to fetch the preference for
  * @return array  with keys for every found preference and the found value
  * @author sl
  */
  public function usr_get_preferences(array $keys,$userId=null) {
      if ($userId === null)
        $userId = $this->kga['usr']['usr_ID'];

      $table  = $this->kga['server_prefix']."preferences";
      $userId = MySQL::SQLValue($userId,  MySQL::SQLVALUE_NUMBER);

      $preparedKeys = array();
      foreach ($keys as $key)
        $preparedKeys[] = MySQL::SQLValue($key);

      $keysString = implode(",",$preparedKeys);

      $query = "SELECT var,value FROM $table WHERE userID = $userId AND var IN ($keysString)";

      $this->conn->Query($query);

      $preferences = array();

      while (!$this->conn->EndOfSeek()) {
        $row = $this->conn->RowArray();
        $preferences[$row['var']] = $row['value'];
      }

      return $preferences;
  }

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
  public function usr_get_preferences_by_prefix($prefix,$userId=null) {
      if ($userId === null)
        $userId = $this->kga['usr']['usr_ID'];

      $prefixLength = strlen($prefix);

      $table  = $this->kga['server_prefix']."preferences";
      $userId = MySQL::SQLValue($userId,  MySQL::SQLVALUE_NUMBER);
      $prefix = MySQL::SQLValue($prefix.'%');

      $query = "SELECT var,value FROM $table WHERE userID = $userId AND var LIKE $prefix";
      $this->conn->Query($query);

      $preferences = array();

      while (!$this->conn->EndOfSeek()) {
        $row = $this->conn->RowArray();
        $key = substr($row['var'],$prefixLength);
        $preferences[$key] = $row['value'];
      }

      return $preferences;
  }

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
  * @return boolean        true on success, false on failure
  * @author sl
  */
  public function usr_set_preferences(array $data,$prefix='',$userId=null) {
      if ($userId === null)
        $userId = $this->kga['usr']['usr_ID'];

      if (! $this->conn->TransactionBegin()) {
        $this->logLastError('usr_set_preferences');
        return false;
      }

      $table  = $this->kga['server_prefix']."preferences";

      $filter['userID']  = MySQL::SQLValue($userId,  MySQL::SQLVALUE_NUMBER);
      $values['userID']  = $filter['userID'];
      foreach ($data as $key=>$value) {
        $values['var']   = MySQL::SQLValue($prefix.$key);
        $values['value'] = MySQL::SQLValue($value);
        $filter['var']   = $values['var'];

        $this->conn->AutoInsertUpdate($table, $values, $filter);
      }

      return $this->conn->TransactionEnd();
  }

  /**
  * Assigns a leader to 1-n groups by adding entries to the cross table
  *
  * @param int $ldr_id        usr_id of the group leader to whom the groups will be assigned
  * @param array $grp_array    contains one or more grp_IDs
  * @return boolean            true on success, false on failure
  * @author ob
  */
  public function assign_ldr2grps($ldr_id, $grp_array) {
      if (! $this->conn->TransactionBegin()) {
        $this->logLastError('assign_ldr2grps');
        return false;
      }

      $table = $this->kga['server_prefix']."ldr";
      $filter['grp_leader'] = MySQL::SQLValue($ldr_id, MySQL::SQLVALUE_NUMBER);
      $query = MySQL::BuildSQLDelete($table, $filter);

      $d_result = $this->conn->Query($query);

      if ($d_result == false) {
          $this->logLastError('assign_ldr2grps');
          $this->conn->TransactionRollback();
          return false;
      }

      foreach ($grp_array as $current_grp) {
        $values['grp_ID']       = MySQL::SQLValue($current_grp, MySQL::SQLVALUE_NUMBER);
        $values['grp_leader']   = MySQL::SQLValue($ldr_id     , MySQL::SQLVALUE_NUMBER);
        $query = MySQL::BuildSQLInsert($table, $values);

        $result = $this->conn->Query($query);

        if ($result == false) {
                $this->logLastError('assign_ldr2grps');
                $this->conn->TransactionRollback();
                return false;
        }
      }

      $this->update_leader_status();

      if ($this->conn->TransactionEnd() == true) {
          return true;
      } else {
          $this->logLastError('assign_ldr2grps');
          return false;
      }
  }

  /**
  * Assigns a group to 1-n group leaders by adding entries to the cross table
  * (counterpart to assign_ldr2grp)
  *
  * @param array $grp_id        grp_id of the group to which the group leaders will be assigned
  * @param array $ldr_array    contains one or more usr_ids of the leaders)
  * @return boolean            true on success, false on failure
  * @author ob
  */
  public function assign_grp2ldrs($grp_id, $ldr_array) {
      if (! $this->conn->TransactionBegin()) {
        $this->logLastError('assign_grp2ldrs');
        return false;
      }

      $table = $this->kga['server_prefix']."ldr";
      $filter['grp_ID'] = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
      $query = MySQL::BuildSQLDelete($table, $filter);

      $d_result = $this->conn->Query($query);

      if ($d_result == false) {
              $this->logLastError('assign_grp2ldrs');
              $this->conn->TransactionRollback();
              return false;
      }

      foreach ($ldr_array as $current_ldr) {
        $values['grp_ID']       = MySQL::SQLValue($grp_id      , MySQL::SQLVALUE_NUMBER);
        $values['grp_leader']   = MySQL::SQLValue($current_ldr , MySQL::SQLVALUE_NUMBER);
        $query = MySQL::BuildSQLInsert($table, $values);

        $result = $this->conn->Query($query);

        if ($result == false) {
                $this->logLastError('assign_grp2ldrs');
                $this->conn->TransactionRollback();
                return false;
        }
      }

      $this->update_leader_status();

      if ($this->conn->TransactionEnd() == true) {
          return true;
      } else {
          $this->logLastError('assign_grp2ldrs');
          return false;
      }
  }

  /**
  * returns all the groups of the given group leader
  *
  * @param array $ldr_id  usr_id of the group leader
  * @return array         contains the grp_IDs of the groups or false on error
  * @author th
  */
  public function ldr_get_grps($ldr_id) {
      $filter['grp_leader'] = MySQL::SQLValue($ldr_id, MySQL::SQLVALUE_NUMBER);
      $columns[]            = "grp_ID";
      $table = $this->kga['server_prefix']."ldr";

      $result = $this->conn->SelectRows($table, $filter, $columns);
      if ($result == false) {
          $this->logLastError('ldr_get_grps');
          return false;
      }

      $return_grps = array();
      $counter = 0;

      $rows = $this->conn->RowArray(0,MYSQL_ASSOC);

      if ($this->conn->RowCount()) {
          foreach ($rows as $current_grp) {
              $return_grps[$counter] = $current_grp['grp_ID'];
              $counter++;
          }
          return $return_grps;
      } else {
          $this->logLastError('ldr_get_grps');
          return false;
      }
  }

  /**
  * returns all the group leaders of the given group
  *
  * @param array $grp_id  grp_id of the group
  * @return array         contains the usr_IDs of the group's group leaders or false on error
  * @author th
  */
  public function grp_get_ldrs($grp_id) {
      $grp_id = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
      $p = $this->kga['server_prefix'];

      $query = "SELECT grp_leader FROM ${p}ldr
      JOIN ${p}usr ON ${p}usr.usr_ID = ${p}ldr.grp_leader WHERE grp_ID = $grp_id AND usr_trash=0;";

      $result = $this->conn->Query($query);
      if ($result == false) {
          $this->logLastError('grp_get_ldrs');
          return false;
      }

      $return_ldrs = array();
      $counter     = 0;

      $rows = $this->conn->RowArray(0,MYSQL_ASSOC);

      if ($this->conn->RowCount()) {
          $this->conn->MoveFirst();
          while (! $this->conn->EndOfSeek()) {
              $row = $this->conn->Row();
              $return_ldrs[$counter] = $row->grp_leader;
              $counter++;
          }
          return $return_ldrs;
      } else {
          return array();
      }
  }

  /**
  * Adds a new group
  *
  * @param array $data  name and other data of the new group
  * @return int         the grp_id of the new group, false on failure
  * @author th
  */
  public function grp_create($data) {
      $data = $this->clean_data($data);

      $values ['grp_name']   = MySQL::SQLValue($data ['grp_name'] );
      $table = $this->kga['server_prefix']."grp";
      $result = $this->conn->InsertRow($table, $values);

      if (! $result) {
        $this->logLastError('grp_create');
        return false;
      } else {
        return $this->conn->GetLastInsertID();
      }
  }

  /**
  * Returns the data of a certain group
  *
  * @param array $grp_id  grp_id of the group
  * @return array         the group's data (name, leader ID, etc) as array, false on failure
  * @author th
  */
  public function grp_get_data($grp_id) {


      $filter['grp_ID'] = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."grp";
      $result = $this->conn->SelectRows($table, $filter);

      if (! $result) {
        $this->logLastError('grp_get_data');
        return false;
      } else {
          return $this->conn->RowArray(0,MYSQL_ASSOC);
      }
  }


  /**
  * Returns the data of a certain status
  *
  * @param array $status_id  status_id of the group
  * @return array         	 the group's data (name) as array, false on failure
  * @author mo
  */
  public function status_get_data($status_id) {

      $filter['status_id'] = MySQL::SQLValue($status_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."status";
      $result = $this->conn->SelectRows($table, $filter);

      if (! $result) {
        $this->logLastError('status_get_data');
        return false;
      } else {
          return $this->conn->RowArray(0,MYSQL_ASSOC);
      }
  }

  /**
  * Returns the number of users in a certain group
  *
  * @param array $grp_id   grp_id of the group
  * @return int            the number of users in the group
  * @author th
  */
  public function grp_count_users($grp_id) {
      $filter['grp_ID'] = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."grp_usr";
      $result = $this->conn->SelectRows($table, $filter);

      if (! $result) {
        $this->logLastError('grp_count_data');
        return false;
      }

      return $this->conn->RowCount()===false?0:$this->conn->RowCount();
  }


  /**
  * Returns the number of zef with a certain status
  *
  * @param integer $status_id   status_id of the status
  * @return int            		the number of zef with this status
  * @author mo
  */
  public function status_count_zef($status_id) {
      $filter['zef_status'] = MySQL::SQLValue($status_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."zef";
      $result = $this->conn->SelectRows($table, $filter);

      if (! $result) {
        $this->logLastError('status_count_zef');
        return false;
      }

      return $this->conn->RowCount()===false?0:$this->conn->RowCount();
  }


  /**
  * Edits a group by replacing its data by the new array
  *
  * @param array $grp_id  grp_id of the group to be edited
  * @param array $data    name and other new data of the group
  * @return boolean       true on success, false on failure
  * @author th
  */
  public function grp_edit($grp_id, $data) {
      $data = $this->clean_data($data);

      $values ['grp_name'] = MySQL::SQLValue($data ['grp_name'] );

      $filter ['grp_ID']   = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."grp";

      $query = MySQL::BuildSQLUpdate($table, $values, $filter);

      return $this->conn->Query($query);
  }

 /**
  * Edits a status by replacing its data by the new array
  *
  * @param array $status_id  grp_id of the status to be edited
  * @param array $data    name and other new data of the status
  * @return boolean       true on success, false on failure
  * @author mo
  */
  public function status_edit($status_id, $data) {
      $data = $this->clean_data($data);

      $values ['status'] = MySQL::SQLValue($data ['status'] );

      $filter ['status_id']   = MySQL::SQLValue($status_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."status";

      $query = MySQL::BuildSQLUpdate($table, $values, $filter);

      return $this->conn->Query($query);
  }

  /**
   * Set the groups in which the user is a member in.
   * @param int $userId   id of the user
   * @param array $groups  array of the group ids to be part of
   * @return boolean       true on success, false on failure
   * @author sl
   */
  public function setGroupMemberships($userId,array $groups = null) {
    $table = $this->kga['server_prefix']."grp_usr";

    if (! $this->conn->TransactionBegin()) {
      $this->logLastError('setGroupMemberships');
      return false;
    }

    $data ['usr_ID']   = MySQL::SQLValue($userId, MySQL::SQLVALUE_NUMBER);
    $result = $this->conn->DeleteRows($table,$data);

    if (!$result) {
      $this->logLastError('setGroupMemberships');
      if (! $this->conn->TransactionRollback())
        $this->logLastError('setGroupMemberships');
      return false;
    }

    foreach ($groups as $group) {
      $data['grp_ID'] = MySQL::SQLValue($group, MySQL::SQLVALUE_NUMBER);
      $result = $this->conn->InsertRow($table,$data);
      if ($result === false) {
        $this->logLastError('setGroupMemberships');
        if (! $this->conn->TransactionRollback())
          $this->logLastError('setGroupMemberships');
        return false;
      }
    }

    if (! $this->conn->TransactionEnd()) {
      $this->logLastError('setGroupMemberships');
      return false;
    }
  }

  /**
   * Get the groups in which the user is a member in.
   * @param int $userId   id of the user
   * @return array        list of group ids
   */
  public function getGroupMemberships($userId) {
    $filter['usr_ID'] = MySQL::SQLValue($userId);
    $columns[] = "grp_ID";
    $table = $this->kga['server_prefix']."grp_usr";
    $result = $this->conn->SelectRows($table, $filter, $columns);

    if (!$result) {
        $this->logLastError('getGroupMemberships');
        return null;
    }

    $arr = array();
    if ($this->conn->RowCount()) {
      $this->conn->MoveFirst();
      while (! $this->conn->EndOfSeek()) {
          $row = $this->conn->Row();
          $arr[] = $row->grp_ID;
      }
    }
    return $arr;
  }

  /**
  * deletes a group
  *
  * @param array $grp_id  grp_id of the group
  * @return boolean       true on success, false on failure
  * @author th
  */
  public function grp_delete($grp_id) {
      $values['grp_trash'] = 1;
      $filter['grp_ID'] = MySQL::SQLValue($grp_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."grp";
      $query = MySQL::BuildSQLUpdate($table, $values, $filter);
      return $this->conn->Query($query);
  }

    /**
  * deletes a status
  *
  * @param array $status_id  status_id of the status
  * @return boolean       	 true on success, false on failure
  * @author mo
  */
  public function status_delete($status_id) {
      $filter['status_id'] = MySQL::SQLValue($status_id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."status";
      $query = MySQL::BuildSQLDelete($table, $filter);
      return $this->conn->Query($query);
  }

  /**
  * Returns all configuration variables
  *
  * @return array       array with the vars from the var table
  * @author th
  */
  public function var_get_data() {
      $table = $this->kga['server_prefix']."var";
      $result = $this->conn->SelectRows($table);

      $var_data = array();

      $this->conn->MoveFirst();
      while (! $this->conn->EndOfSeek()) {
          $row = $this->conn->Row();
          $var_data[$row->var] = $row->value;
      }

      return $var_data;
  }

  /**
  * Edits a configuration variables by replacing the data by the new array
  *
  * @param array $data    variables array
  * @return boolean       true on success, false on failure
  * @author ob
  */
  public function var_edit($data) {
    $data = $this->clean_data($data);

      $table = $this->kga['server_prefix']."var";

      if (! $this->conn->TransactionBegin()) {
        $this->logLastError('var_edit');
        return false;
      }

      foreach ($data as $key => $value) {
        $filter['var'] = MySQL::SQLValue($key);
        $values ['value'] = MySQL::SQLValue($value);

        $query = MySQL::BuildSQLUpdate($table, $values, $filter);

        $result = $this->conn->Query($query);

        if ($result === false) {
            $this->logLastError('var_edit');
            return false;
        }
      }

      if (! $this->conn->TransactionEnd()) {
        $this->logLastError('var_edit');
        return false;
      }

      return true;
  }

  /**
  * checks whether there is a running zef-entry for a given user
  *
  * @param integer $user ID of user in table usr
  * @return boolean true=there is an entry, false=there is none (actually 1 or 0 is returnes as number!)
  * @author ob/th
  */
  public function get_rec_state($usr_id) {

      $p = $this->kga['server_prefix'];
      $usr_id = MySQL::SQLValue($usr_id, MySQL::SQLVALUE_NUMBER);
      $this->conn->Query("SELECT * FROM ${p}zef WHERE zef_usrID = $usr_id AND zef_in > 0 AND zef_out = 0 LIMIT 1;");
      if ($this->conn->RowCount()) {
          return "1";
      } else {
          return "0";
      }
  }

  /**
  * Returns the data of a certain time record
  *
  * @param array $zef_id  zef_id of the record
  * @return array         the record's data (time, event id, project id etc) as array, false on failure
  * @author th
  */
  public function zef_get_data($zef_id) {
      $p = $this->kga['server_prefix'];

      $zef_id = MySQL::SQLValue($zef_id, MySQL::SQLVALUE_NUMBER);
	
		$table = $this->getZefTable();
		$projectTable = $this->getProjectTable();
		$eventTable = $this->getEventTable();
		$customerTable = $this->getCustomerTable();
		
      	$select = "SELECT $table.*, $projectTable.pct_name AS pct_name, $customerTable.knd_name AS knd_name, $eventTable.evt_name AS evt_name, $customerTable.knd_ID AS knd_ID
      				FROM $table
                	JOIN $projectTable ON $table.zef_pctID = $projectTable.pct_ID
                	JOIN $customerTable ON $projectTable.pct_kndID = $customerTable.knd_ID
                	JOIN $eventTable ON $eventTable.evt_ID = $table.zef_evtID";
		
		
      if ($zef_id) {
          $result = $this->conn->Query("$select WHERE zef_ID = " . $zef_id);
      } else {
          $result = $this->conn->Query("$select WHERE zef_usrID = ".$this->kga['usr']['usr_ID']." ORDER BY zef_ID DESC LIMIT 1");
      }

      if (! $result) {
        $this->logLastError('zef_get_data');
        return false;
      } else {
          return $this->conn->RowArray(0,MYSQL_ASSOC);
      }
  }

  /**
  * delete zef entry
  *
  * @param integer $id -> ID of record
  * @author th
  */
  public function zef_delete_record($id) {

      $filter["zef_ID"] = MySQL::SQLValue($id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."zef";
      $query = MySQL::BuildSQLDelete($table, $filter);
      return $this->conn->Query($query);
  }

  /**
  * create zef entry
  *
  * @param integer $id    ID of record
  * @param integer $data  array with record data
  * @author th
  */
  public function zef_create_record($usr_ID,$data) {
      $data = $this->clean_data($data);

      $values ['zef_location']     =   MySQL::SQLValue( $data ['zlocation'] );
      $values ['zef_comment']      =   MySQL::SQLValue( $data ['comment'] );
      $values ['zef_description']      =   MySQL::SQLValue( $data ['description'] );
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
      $values ['zef_budget']   	   =   MySQL::SQLValue($data ['budget']   	   , MySQL::SQLVALUE_NUMBER );
      $values ['zef_approved'] 	   =   MySQL::SQLValue($data ['approved']      , MySQL::SQLVALUE_NUMBER );
      $values ['zef_status']   	   =   MySQL::SQLValue($data ['status']   	   , MySQL::SQLVALUE_NUMBER );
      $values ['zef_billable'] 	   =   MySQL::SQLValue($data ['billable'] 	   , MySQL::SQLVALUE_NUMBER );

      $table = $this->kga['server_prefix']."zef";
      $success =  $this->conn->InsertRow($table, $values);
      if ($success)
        return  $this->conn->GetLastInsertID();
      else {
        $this->logLastError('zef_create_record');
        return false;
      }
  }

  /**
  * edit zef entry
  *
  * @param integer $id ID of record
  * @param integer $data  array with new record data
  * @author th
  */
  public function zef_edit_record($id,$data) {
      $data = $this->clean_data($data);

      $original_array = $this->zef_get_data($id);
      $new_array = array();
      $budgetChange = 0;
      $approvedChange = 0;

      foreach ($original_array as $key => $value) {
          if (isset($data[$key]) == true) {
          	// buget is added to total budget for task. So if we change the budget, we need
          	// to first subtract the previous entry before adding the new one
//          	if($key == 'zef_budget') {
//          		$budgetChange = - $value;
//          	} else if($key == 'zef_approved') {
//          		$approvedChange = - $value;
//          	}
              $new_array[$key] = $data[$key];
          } else {
              $new_array[$key] = $original_array[$key];
          }
      }

      $values ['zef_description']  = MySQL::SQLValue($new_array ['zef_description']    						   );
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
      $values ['zef_budget'] 	   = MySQL::SQLValue($new_array ['zef_budget']     	  , MySQL::SQLVALUE_NUMBER );
      $values ['zef_approved'] 	   = MySQL::SQLValue($new_array ['zef_approved']  	  , MySQL::SQLVALUE_NUMBER );
      $values ['zef_status'] 	   = MySQL::SQLValue($new_array ['zef_status']		  , MySQL::SQLVALUE_NUMBER );
      $values ['zef_billable'] 	   = MySQL::SQLValue($new_array ['zef_billable']	  , MySQL::SQLVALUE_NUMBER );
      $values ['zef_description']  = MySQL::SQLValue($new_array ['zef_description']	  , MySQL::SQLVALUE_NUMBER );

      $filter ['zef_ID']           = MySQL::SQLValue($id, MySQL::SQLVALUE_NUMBER);
      $table = $this->kga['server_prefix']."zef";
      $query = MySQL::BuildSQLUpdate($table, $values, $filter);

      $success = true;

      if (! $this->conn->Query($query)) $success = false;

      if ($success) {
          if (! $this->conn->TransactionEnd()) {
            $this->logLastError('zef_edit_record');
            return false;
          }
      } else {
//      	$budgetChange += $values['zef_budget'];
//      	$approvedChange += $values['zef_approved'];
//      	$this->update_evt_budget($values['zef_pctID'], $values['zef_evtID'], $budgetChange);
//      	$this->update_evt_approved($values['zef_pctID'], $values['zef_evtID'], $budgetChange);
          $this->logLastError('zef_edit_record');
          if (! $this->conn->TransactionRollback()) {
            $this->logLastError('zef_edit_record');
            return false;
          }
      }

      return $success;
  }


  /**
  * saves timespace of user in database (table conf)
  *
  * @param string $timespace_in unix seconds
  * @param string $timespace_out unix seconds
  * @param string $user ID of user
  *
  * @author th
  */
  public function save_timespace($timespace_in,$timespace_out,$user) {
      if ($timespace_in == 0 && $timespace_out == 0) {
          $mon = date("n"); $day = date("j"); $Y = date("Y");
          $timespace_in  = mktime(0,0,0,$mon,$day,$Y);
          $timespace_out = mktime(23,59,59,$mon,$day,$Y);
      }

      if ($timespace_out == mktime(23,59,59,date('n'),date('j'),date('Y')))
        $timespace_out = 0;

      $values['timespace_in']  = MySQL::SQLValue($timespace_in  , MySQL::SQLVALUE_NUMBER );
      $values['timespace_out'] = MySQL::SQLValue($timespace_out , MySQL::SQLVALUE_NUMBER );

      $table = $this->kga['server_prefix']."usr";
      $filter  ['usr_ID']          =   MySQL::SQLValue($user, MySQL::SQLVALUE_NUMBER);


      $query = MySQL::BuildSQLUpdate($table, $values, $filter);

      if (! $this->conn->Query($query)) {
        $this->logLastError('save_timespace');
        return false;
      }

      return true;
  }

  /**
  * returns list of projects for specific group as array
  *
  * @param integer $user ID of user in database
  * @return array
  * @author th
  */
  public function get_arr_pct(array $groups = null) {
      $arr = array();
      $p = $this->kga['server_prefix'];

      if ($groups === null)
        $query = "SELECT * FROM ${p}pct JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID AND pct_trash=0";
      else
        $query = "SELECT * FROM ${p}pct
         JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID
         JOIN ${p}grp_pct ON ${p}grp_pct.pct_ID = ${p}pct.pct_ID
         WHERE ${p}grp_pct.grp_ID IN (".implode($groups,',').")
          AND pct_trash=0";

      if ($this->kga['conf']['flip_pct_display'])
        $query .= " ORDER BY pct_visible DESC,knd_name,pct_name;";
      else
        $query .= " ORDER BY pct_visible DESC,pct_name,knd_name;";

      $result = $this->conn->Query($query);
      if ($result == false) {
          $this->logLastError('get_arr_pct');
          return false;
      }

      $rows = $this->conn->RecordsArray(MYSQL_ASSOC);

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
              $arr[$i]['pct_effort'] = $row['pct_effort'];
              $arr[$i]['pct_approved'] = $row['pct_approved'];
              $i++;
          }
          return $arr;
      } else {
          return array();
      }
  }

  /**
  * returns list of projects for specific group and specific customer as array
  *
  * @param integer $knd_id customer id
  * @param array $groups list of group ids
  * @return array
  * @author ob
  */
  public function get_arr_pct_by_knd($knd_id, array $groups = null) {
      $knd_id  = MySQL::SQLValue($knd_id, MySQL::SQLVALUE_NUMBER);
      $p       = $this->kga['server_prefix'];

      if ($this->kga['conf']['flip_pct_display']) {
          $sort = "knd_name,pct_name";
      } else {
          $sort = "pct_name,knd_name";
      }

      if ($groups === null) {
        $query = "SELECT * FROM ${p}pct JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID
                    AND ${p}pct.pct_kndID = $knd_id AND pct_trash=0 ORDER BY $sort;";
      } else {
        $query = "SELECT * FROM ${p}pct
                    JOIN ${p}knd ON ${p}pct.pct_kndID = ${p}knd.knd_ID
                    JOIN ${p}grp_pct ON ${p}grp_pct.pct_ID = ${p}pct.pct_ID
                    WHERE ${p}grp_pct.grp_ID  IN (".implode($groups,',').")
                    AND ${p}pct.pct_kndID = $knd_id
                    AND pct_trash=0
                    ORDER BY $sort;";
      }

      $this->conn->Query($query);

      $arr = array();
      $i=0;

      $this->conn->MoveFirst();
      while (! $this->conn->EndOfSeek()) {
          $row = $this->conn->Row();
          $arr[$i]['pct_ID']      = $row->pct_ID;
          $arr[$i]['pct_name']    = $row->pct_name;
          $arr[$i]['knd_name']    = $row->knd_name;
          $arr[$i]['knd_ID']      = $row->knd_ID;
          $arr[$i]['pct_visible'] = $row->pct_visible;
          $arr[$i]['pct_budget']  = $row->pct_budget;
          $arr[$i]['pct_effort']  = $row->pct_effort;
          $arr[$i]['pct_approved']  = $row->pct_approved;
          $i++;
      }

      return $arr;
  }

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
  public function zef_whereClausesFromFilters($users, $customers , $projects , $events ) {

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

  /**
  * returns timesheet for specific user as multidimensional array
<<<<<<< HEAD
  * @TODO: needs new comments
=======
  *@TODO: needs new comments
>>>>>>> origin/master
  * @param integer $user ID of user in table usr
  * @param integer $in start of timespace in unix seconds
  * @param integer $out end of timespace in unix seconds
  * @param integer $filterCleared where -1 (default) means no filtering, 0 means only not cleared entries, 1 means only cleared entries
  * @param 
  * @return array
  * @author th
  */
  public function get_arr_zef($in, $out, $users = null, $customers = null, $projects = null, $events = null, $limit = false, $reverse_order = false, $filterCleared = null, $startRows = 0, $limitRows = 0, $countOnly = false) {
      if (!is_numeric($filterCleared)) {
        $filterCleared = $this->kga['conf']['hideClearedEntries']-1; // 0 gets -1 for disabled, 1 gets 0 for only not cleared entries
      }
      
      $in    = MySQL::SQLValue($in    , MySQL::SQLVALUE_NUMBER);
      $out   = MySQL::SQLValue($out   , MySQL::SQLVALUE_NUMBER);
      $filterCleared   = MySQL::SQLValue($filterCleared , MySQL::SQLVALUE_NUMBER);
      $limit = MySQL::SQLValue($limit , MySQL::SQLVALUE_BOOLEAN);
      
      $p     = $this->kga['server_prefix'];

      $whereClauses = $this->zef_whereClausesFromFilters($users, $customers, $projects, $events);

      if (isset($this->kga['customer']))
        $whereClauses[] = "${p}pct.pct_internal = 0";

      if ($in)
        $whereClauses[]="(zef_out > $in || zef_out = 0)";
      if ($out)
        $whereClauses[]="zef_in < $out";
      if ($filterCleared > -1)
        $whereClauses[] = "zef_cleared = $filterCleared";
      
      if ($limit) {
		if(!empty($limitRows))
		{
			$startRows = (int)$startRows;
      	  	$limit = "LIMIT $startRows, $limitRows";
		} 
		else 
		{
			if (isset($this->kga['conf']['rowlimit'])) {
				$limit = "LIMIT " .$this->kga['conf']['rowlimit'];
			} else {
				$limit="LIMIT 100";
			}
		}
      } else {
          $limit="";
      }
      
      
      $select = "SELECT zef_ID, zef_in, zef_out, zef_time, zef_rate, zef_budget, zef_approved, status, zef_billable,
                       zef_pctID, zef_evtID, zef_usrID, pct_ID, knd_name, pct_kndID, evt_name, pct_comment, pct_name,
                       zef_location, zef_trackingnr, zef_description, zef_comment, zef_comment_type, usr_name, usr_alias, zef_cleared";
      
      if($countOnly) {
      	$select = "SELECT COUNT(*) AS total";
      	$limit = "";
      }
                       
      $query = "$select
                FROM ${p}zef
                Join ${p}pct ON zef_pctID = pct_ID
                Join ${p}knd ON pct_kndID = knd_ID
                Join ${p}usr ON zef_usrID = usr_ID
                Join ${p}status ON zef_status = status_id
                Join ${p}evt ON evt_ID    = zef_evtID "
                .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
                ' ORDER BY zef_in '.($reverse_order?'ASC ':'DESC ') . $limit.';';
      
      $this->conn->Query($query);
		
      
      if($countOnly)
      {
      	$this->conn->MoveFirst();
      	$row = $this->conn->Row();
      	return $row->total;
      }

      $i=0;
      $arr=array();

          $this->conn->MoveFirst();
          while (! $this->conn->EndOfSeek()) {
              $row = $this->conn->Row();
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
                $arr[$i]['zef_duration']     = Format::formatDuration($arr[$i]['zef_time']);
                $arr[$i]['wage_decimal']     = $arr[$i]['zef_time']/3600*$row->zef_rate;
                $arr[$i]['wage']             = sprintf("%01.2f",$arr[$i]['wage_decimal']);
              }
              $arr[$i]['zef_budget']   	   = $row->zef_budget;
              $arr[$i]['zef_approved']     = $row->zef_approved;
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
              $arr[$i]['zef_budget']  	   = $row->zef_budget;
              $arr[$i]['zef_approved']     = $row->zef_approved;
              $arr[$i]['zef_status']       = $row->status;
              $arr[$i]['zef_billable']     = $row->zef_billable;
              $arr[$i]['zef_description']  = $row->zef_description;
              $arr[$i]['zef_comment']      = $row->zef_comment;
              $arr[$i]['zef_cleared']      = $row->zef_cleared;
              $arr[$i]['zef_comment_type'] = $row->zef_comment_type;
              $arr[$i]['usr_alias']        = $row->usr_alias;
              $arr[$i]['usr_name']         = $row->usr_name;
              $i++;
          }
          return $arr;
  }

  /**
  * checks if user is logged on and returns user information as array
  * kicks client if is not verified
  * TODO: this and get_config should be one public function
  *
  * <pre>
  * returns:
  * [usr_ID] user ID,
  * [usr_sts] user status (rights),
  * [usr_name] username
  * </pre>
  *
  * @param integer $user ID of user in table usr
  * @return array
  * @author th/kp
  */
  public function checkUser()
  {
    if (isset($_COOKIE['kimai_usr']) && isset($_COOKIE['kimai_key']) && $_COOKIE['kimai_usr'] != "0" && $_COOKIE['kimai_key'] != "0") {
        $kimai_usr = addslashes($_COOKIE['kimai_usr']);
        $kimai_key = addslashes($_COOKIE['kimai_key']);

		if ($this->get_seq($kimai_usr) != $kimai_key) {
			kickUser();
		} else {
			return $this->checkUserInternal($kimai_usr);
		}
	}
	kickUser();
  }

  /**
   * A drop-in function to replace checkuser() and be compatible with none-cookie environments.
   *
   * @author th/kp
   */
  public function checkUserInternal($kimai_usr)
  {
    $p = $this->kga['server_prefix'];

	if (strncmp($kimai_usr, 'knd_', 4) == 0) {
		$knd_name = MySQL::SQLValue(substr($kimai_usr,4));
		$query = "SELECT knd_ID FROM ${p}knd WHERE knd_name = $knd_name AND NOT knd_trash = '1';";
		$this->conn->Query($query);
		$row = $this->conn->RowArray(0,MYSQL_ASSOC);

		$knd_ID   = $row['knd_ID'];
		if ($knd_ID < 1) {
			kickUser();
		}
	}
	else
	{
		$query = "SELECT usr_ID,usr_sts FROM ${p}usr WHERE usr_name = '$kimai_usr' AND usr_active = '1' AND NOT usr_trash = '1';";
		$this->conn->Query($query);
		$row = $this->conn->RowArray(0,MYSQL_ASSOC);

		$usr_ID   = $row['usr_ID'];
		$usr_sts  = $row['usr_sts']; // User Status -> 0=Admin | 1=GroupLeader | 2=User
		$usr_name = $kimai_usr;

		if ($usr_ID < 1) {
			kickUser();
		}
	}

	// load configuration and language
	$this->get_global_config();
	if (strncmp($kimai_usr, 'knd_', 4) == 0) {
		$this->get_customer_config($knd_ID);
	} else {
		$this->get_user_config($usr_ID);
	}

	// override default language if user has chosen a language in the prefs
	if ($this->kga['conf']['lang'] != "") {
		$this->kga['language'] = $this->kga['conf']['lang'];
		$this->kga['lang'] = array_replace_recursive($this->kga['lang'],include(WEBROOT.'language/'.$this->kga['language'].'.php'));
	}

	return (isset($this->kga['usr'])?$this->kga['usr']:null);
  }

  /**
  * write global configuration into $this->kga including defaults for user settings.
  *
  * @param integer $user ID of user in table usr
  * @return array $this->kga
  * @author th
  *
  */
  public function get_global_config() {
    // get values from global configuration
    $table = $this->kga['server_prefix']."var";
    $this->conn->SelectRows($table);

    $this->conn->MoveFirst();
    while (! $this->conn->EndOfSeek()) {
        $row = $this->conn->Row();
        $this->kga['conf'][$row->var] = $row->value;
    }


    $this->kga['conf']['timezone'] = $this->kga['conf']['defaultTimezone'];
    $this->kga['conf']['rowlimit'] = 100;
    $this->kga['conf']['skin'] = 'standard';
    $this->kga['conf']['autoselection'] = 1;
    $this->kga['conf']['quickdelete'] = 0;
    $this->kga['conf']['flip_pct_display'] = 0;
    $this->kga['conf']['pct_comment_flag'] = 0;
    $this->kga['conf']['showIDs'] = 0;
    $this->kga['conf']['noFading'] = 0;
    $this->kga['conf']['lang'] = '';
    $this->kga['conf']['user_list_hidden'] = 0;
    $this->kga['conf']['hideClearedEntries'] = 0;


    $table = $this->kga['server_prefix']."status";
    $this->conn->SelectRows($table);

    $this->conn->MoveFirst();
    while (! $this->conn->EndOfSeek()) {
        $row = $this->conn->Row();
        $this->kga['conf']['status'][] = $row->status;
    }
  }

  /**
   * Returns a username for the given $apikey.
   *
   * @param string $apikey
   * @return string|null
   */
  public function getUserByApiKey($apikey)
  {
    if (!$apikey || strlen(trim($apikey)) == 0) {
        return null;
    }

    $table = $this->kga['server_prefix']."usr";
    $filter['apikey'] = MySQL::SQLValue($apikey, MySQL::SQLVALUE_TEXT);
    $filter['usr_trash'] = MySQL::SQLValue(0, MySQL::SQLVALUE_NUMBER);

    // get values from user record
    $columns[] = "usr_ID";
    $columns[] = "usr_name";

    $this->conn->SelectRows($table, $filter, $columns);
    $row = $this->conn->RowArray(0, MYSQL_ASSOC);
    return $row['usr_name'];
  }

  /**
  * write details of a specific user into $this->kga
  *
  * @param integer $user ID of user in table usr
  * @return array $this->kga
  * @author th
  *
  */
  public function get_user_config($user) {
    if (!$user) return;

    $table = $this->kga['server_prefix']."usr";
    $filter['usr_ID'] = MySQL::SQLValue($user, MySQL::SQLVALUE_NUMBER);

    // get values from user record
    $columns[] = "usr_ID";
    $columns[] = "usr_name";
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
    $columns[] = "apikey";

    $this->conn->SelectRows($table, $filter, $columns);
    $rows = $this->conn->RowArray(0,MYSQL_ASSOC);
    foreach($rows as $key => $value) {
        $this->kga['usr'][$key] = $value;
    }

    $this->kga['usr']['groups'] = $this->getGroupMemberships($user);

    // get values from user configuration (user-preferences)
    unset($columns);
    unset($filter);

    $this->kga['conf'] = array_merge($this->kga['conf'],$this->usr_get_preferences_by_prefix('ui.'));
    $userTimezone = $this->usr_get_preference('timezone');
    if ($userTimezone != '')
      $this->kga['conf']['timezone'] = $userTimezone;

    date_default_timezone_set($this->kga['conf']['timezone']);
  }

  /**
  * write details of a specific customer into $this->kga
  *
  * @param integer $user ID of user in table usr
  * @return array $this->kga
  * @author sl
  *
  */
  public function get_customer_config($user) {
    if (!$user) return;

    $table = $this->kga['server_prefix']."knd";
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
    $columns[] = "knd_timezone";

    $this->conn->SelectRows($table, $filter, $columns);
    $rows = $this->conn->RowArray(0,MYSQL_ASSOC);
    foreach($rows as $key => $value) {
        $this->kga['customer'][$key] = $value;
    }

    date_default_timezone_set($this->kga['customer']['knd_timezone']);
  }

  /**
  * checks if a customer with this name exists
  *
  * @param string name
  * @return integer
  * @author sl
  */
  public function is_customer_name($name) {
      $name  = MySQL::SQLValue($name);
      $p     = $this->kga['server_prefix'];

      $query = "SELECT knd_ID FROM ${p}knd WHERE knd_name = $name";

      $this->conn->Query($query);
      return $this->conn->RowCount() == 1;
  }

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
  * @return integer
  * @author th
  */
  public function get_event_last() {
      $p     = $this->kga['server_prefix'];

      $lastRecord = $this->kga['usr']['lastRecord'];

      $query = "SELECT * FROM ${p}zef WHERE zef_ID = $lastRecord ;";

      $this->conn->Query($query);
      return $this->conn->RowArray(0,MYSQL_ASSOC);
  }

  /**
  * returns time summary of current timesheet
  *
  * @param integer $user ID of user in table usr
  * @param integer $in start of timespace in unix seconds
  * @param integer $out end of timespace in unix seconds
  * @return integer
  * @author th
  */
  public function get_zef_time($in,$out,$users = null, $customers = null, $projects = null, $events = null,$filterCleared = null) {
      if (!is_numeric($filterCleared)) {
        $filterCleared = $this->kga['conf']['hideClearedEntries']-1; // 0 gets -1 for disabled, 1 gets 0 for only not cleared entries
      }

      $in    = MySQL::SQLValue($in    , MySQL::SQLVALUE_NUMBER);
      $out   = MySQL::SQLValue($out   , MySQL::SQLVALUE_NUMBER);

      $p     = $this->kga['server_prefix'];

      $whereClauses = $this->zef_whereClausesFromFilters($users,$customers,$projects,$events);

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
      $this->conn->Query($query);

      $this->conn->MoveFirst();
      $sum = 0;
      $zef_in = 0;
      $zef_out = 0;
      while (! $this->conn->EndOfSeek()) {
        $row = $this->conn->Row();
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

  /**
  * returns list of customers in a group as array
  *
  * @param integer $group ID of group in table grp or "all" for all groups
  * @return array
  * @author th
  */
  public function get_arr_knd(array $groups = null) {
    $p = $this->kga['server_prefix'];

      if ($groups === null) {
          $query = "SELECT * FROM ${p}knd WHERE knd_trash=0 ORDER BY knd_visible DESC,knd_name;";
      } else {
          $query = "SELECT * FROM ${p}knd
           JOIN ${p}grp_knd
            ON `${p}grp_knd`.`knd_ID`=`${p}knd`.`knd_ID`
           WHERE `${p}grp_knd`.`grp_ID` IN (".implode($groups,',').")
            AND knd_trash=0
           ORDER BY knd_visible DESC, knd_name;";
      }

      $result = $this->conn->Query($query);
      if ($result == false) {
          $this->logLastError('get_arr_knd');
          return false;
      }

      $arr = array();
      $i = 0;
      if ($this->conn->RowCount()) {
          $this->conn->MoveFirst();
          while (! $this->conn->EndOfSeek()) {
              $row = $this->conn->Row();
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

  ## Load into Array: Events
  public function get_arr_evt(array $groups = null) {
  $p = $this->kga['server_prefix'];

      if ($groups === null) {
          $query = "SELECT * FROM ${p}evt WHERE evt_trash=0 ORDER BY evt_visible DESC,evt_name;";
      } else {
          $query = "SELECT * FROM ${p}evt
           JOIN ${p}grp_evt ON `${p}grp_evt`.`evt_ID`=`${p}evt`.`evt_ID`
          WHERE `${p}grp_evt`.`grp_ID` IN (".implode($groups,',').")
           AND evt_trash=0
          ORDER BY evt_visible DESC, evt_name;";
      }

      $result = $this->conn->Query($query);
      if ($result == false) {
          $this->logLastError('get_arr_evt');
          return false;
      }

      $arr = array();
      $i = 0;
      if ($this->conn->RowCount()) {
          $this->conn->MoveFirst();
          while (! $this->conn->EndOfSeek()) {
              $row = $this->conn->Row();
              $arr[$i]['evt_ID']       = $row->evt_ID;
              $arr[$i]['evt_name']     = $row->evt_name;
              $arr[$i]['evt_visible']  = $row->evt_visible;
              $arr[$i]['evt_assignable']  = $row->evt_assignable;
              $i++;
          }
          return $arr;
      } else {
          return array();
      }
  }

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
  public function get_arr_evt_by_pct($pct, array $groups = null) {
      $pct = MySQL::SQLValue($pct , MySQL::SQLVALUE_NUMBER);

      $p = $this->kga['server_prefix'];

      if ($groups === null) {
          $query = "SELECT ${p}evt.evt_ID,evt_name,evt_visible, ${p}evt.evt_budget, ${p}evt.evt_approved, ${p}evt.evt_effort FROM ${p}evt
  LEFT JOIN ${p}pct_evt ON `${p}pct_evt`.`evt_ID`=`${p}evt`.`evt_ID`
  WHERE evt_trash=0
   AND (pct_ID = $pct OR pct_ID IS NULL)
  ORDER BY evt_visible DESC,evt_name;";
      } else {
          $query = "SELECT ${p}evt.evt_ID,evt_name,evt_visible, ${p}evt.evt_budget, ${p}evt.evt_approved, ${p}evt.evt_effort FROM ${p}evt
  JOIN ${p}grp_evt ON `${p}grp_evt`.`evt_ID`=`${p}evt`.`evt_ID`
  LEFT JOIN ${p}pct_evt ON `${p}pct_evt`.`evt_ID`=`${p}evt`.`evt_ID`
  WHERE `${p}grp_evt`.`grp_ID`  IN (".implode($groups,',').")
   AND evt_trash=0
  AND (pct_ID = $pct OR pct_ID IS NULL)
  ORDER BY evt_visible DESC,evt_name;";
      }

      $result = $this->conn->Query($query);
      if ($result == false) {
          $this->logLastError('get_arr_evt_by_pct');
          return false;
      }

      $arr = array();
      if ($this->conn->RowCount()) {
          $this->conn->MoveFirst();
          while (! $this->conn->EndOfSeek()) {
              $row = $this->conn->Row();
              $arr[$row->evt_ID]['evt_ID']       = $row->evt_ID;
              $arr[$row->evt_ID]['evt_name']     = $row->evt_name;
              $arr[$row->evt_ID]['evt_visible']  = $row->evt_visible;
              $arr[$row->evt_ID]['evt_budget']   = $row->evt_budget;
              $arr[$row->evt_ID]['evt_approved'] = $row->evt_approved;
              $arr[$row->evt_ID]['evt_effort']   = $row->evt_effort;
          }
          return $arr;
      } else {
          return array();
      }
  }

  /**
  * returns list of events used with specified customer
  *
  * @param integer $customer filter for only this ID of a customer
  * @return array
  * @author sl
  */
  public function get_arr_evt_by_knd($customer_ID) {
      $p = $this->kga['server_prefix'];

      $customer_ID = MySQL::SQLValue($customer_ID , MySQL::SQLVALUE_NUMBER);

      $query = "SELECT * FROM ${p}evt WHERE evt_ID IN (SELECT zef_evtID FROM ${p}zef WHERE zef_pctID IN (SELECT pct_ID FROM ${p}pct WHERE pct_kndID = $customer_ID)) AND evt_trash=0";

      $result = $this->conn->Query($query);
      if ($result == false) {
          $this->logLastError('get_arr_evt_by_knd');
          return false;
      }

      $arr = array();
      $i = 0;

      if ($this->conn->RowCount()) {
          $this->conn->MoveFirst();
          while (! $this->conn->EndOfSeek()) {
              $row = $this->conn->Row();
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
  * @return array
  * @author th
  */
  public function get_current_timer() {
      $user  = MySQL::SQLValue($this->kga['usr']['usr_ID'] , MySQL::SQLVALUE_NUMBER);
    $p     = $this->kga['server_prefix'];

      $this->conn->Query("SELECT zef_ID,zef_in FROM ${p}zef WHERE zef_usrID = $user AND zef_out = 0;");

      if ($this->conn->RowCount() == 0) {
          $current_timer['all']  = 0;
          $current_timer['hour'] = 0;
          $current_timer['min']  = 0;
          $current_timer['sec']  = 0;
      }
      else {

        $row = $this->conn->RowArray(0,MYSQL_ASSOC);

        $zef_in    = (int)$row['zef_in'];

        $aktuelleMessung = Format::hourminsec(time()-$zef_in);
        $current_timer['all']  = $zef_in;
        $current_timer['hour'] = $aktuelleMessung['h'];
        $current_timer['min']  = $aktuelleMessung['i'];
        $current_timer['sec']  = $aktuelleMessung['s'];
      }
      return $current_timer;
  }

  /**
  * returns the version of the installed Kimai database to compare it with the package version
  *
  * @return array
  * @author th
  *
  * [0] => version number (x.x.x)
  * [1] => svn revision number
  *
  */
  public function get_DBversion() {
      $filter['var'] = MySQL::SQLValue('version');
      $columns[] = "value";
      $table = $this->kga['server_prefix']."var";
      $result = $this->conn->SelectRows($table, $filter, $columns);

      $row = $this->conn->RowArray(0,MYSQL_ASSOC);
      $return[] = $row['value'];

      if ($result == false) $return[0] = "0.5.1";

      $filter['var'] = MySQL::SQLValue('revision');
      $result = $this->conn->SelectRows($table, $filter, $columns);

      $row = $this->conn->RowArray(0,MYSQL_ASSOC);
      $return[] = $row['value'];

      return $return;
  }

  /**
  * returns the key for the session of a specific user
  *
  * the key is both stored in the database (usr table) and a cookie on the client.
  * when the keys match the user is allowed to access the Kimai GUI.
  * match test is performed via public function userCheck()
  *
  * @param integer $user ID of user in table usr
  * @return string
  * @author th
  */
  public function get_seq($user) {
      if (strncmp($user, 'knd_', 4) == 0) {
        $filter['knd_name'] = MySQL::SQLValue(substr($user,4));
        $columns[] = "knd_secure";
        $table = $this->kga['server_prefix']."knd";
      }
      else {
        $filter['usr_name'] = MySQL::SQLValue($user);
        $columns[] = "secure";
        $table = $this->kga['server_prefix']."usr";
      }

      $result = $this->conn->SelectRows($table, $filter, $columns);
      if ($result == false) {
          $this->logLastError('get_seq');
          return false;
      }

      $row = $this->conn->RowArray(0,MYSQL_ASSOC);
      return strncmp($user, 'knd_', 4)==0?$row['knd_secure']:$row['secure'];
  }

  /**
   * return status names
   * @param integer $statusIds
    * @FIXME kpapst - here we fetch the description of the entries which are already known
    *                 SELECT status from status WHERE status in ('open') - doesn't make
    *                 really sense, only the values will be ordered
   */
    public function get_status($statusIds) {
  	  $p = $this->kga['server_prefix'];
  	  $statusIds = implode(',', $statusIds);
      $query = "SELECT status FROM ${p}status where status_id in ( $statusIds ) order by status_id";
      $result = $this->conn->Query($query);
      if ($result == false) {
          $this->logLastError('get_status');
          return false;
      }

      $rows = $this->conn->RecordsArray(MYSQL_ASSOC);
      foreach($rows as $row) {
      	$res[] = $row['status'];
      }
      return $res;
  }

    /**
  * returns array of all status with the status id as key
  *
  * @return array
  * @author mo
  */
  public function get_arr_status() {
      $p = $this->kga['server_prefix'];

        $query = "SELECT * FROM ${p}status
        ORDER BY status;";
      $this->conn->Query($query);

      $arr = array();
      $i=0;

      $this->conn->MoveFirst();
      $rows = $this->conn->RecordsArray(MYSQL_ASSOC);
      foreach($rows as $row) {
          $arr[] = $row;
          $arr[$i]['count_zef'] = $this->status_count_zef($row['status_id']);
          $i++;
      }

      return $arr;
  }

  /**
   * add a new status
   * @param Array $statusArray
   */
  public function status_create($status) {

      $values['status'] = MySQL::SQLValue(trim($status['status']));

      $table = $this->kga['server_prefix']."status";
      $result = $this->conn->InsertRow($table, $values);
      if (! $result) {
        $this->logLastError('add_status');
        return false;
      }
//  	}
        return true;
  }

  /**
  * returns array of all users
  *
  * [usr_ID] => 23103741
  * [usr_name] => admin
  * [usr_sts] => 0
  * [usr_mail] => 0
  * [usr_active] => 0
  *
  *
  * @param array $groups list of group ids the users must be a member of
  * @return array
  * @author th
  */
  public function get_arr_usr($trash=0,array $groups = null) {
      $p = $this->kga['server_prefix'];


      $trash = MySQL::SQLValue($trash, MySQL::SQLVALUE_NUMBER );

      if ($groups === null)
        $query = "SELECT * FROM ${p}usr
        WHERE usr_trash = $trash
        ORDER BY usr_name ;";
      else
        $query = "SELECT * FROM ${p}usr
         JOIN ${p}grp_usr ON usr_ID = usr_ID
        WHERE ${p}grp_usr.grp_ID IN (".implode($groups,',').") AND
         usr_trash = $trash
        ORDER BY usr_name ;";
      $this->conn->Query($query);

      $rows = $this->conn->RowArray(0,MYSQL_ASSOC);

      $i=0;
      $arr = array();

      $this->conn->MoveFirst();
      while (! $this->conn->EndOfSeek()) {
          $row = $this->conn->Row();
          $arr[$i]['usr_ID']     = $row->usr_ID;
          $arr[$i]['usr_name']   = $row->usr_name;
          $arr[$i]['usr_sts']    = $row->usr_sts;
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
  * @return array
  * @author th
  *
  */
  public function get_arr_grp($trash=0) {
      $p = $this->kga['server_prefix'];

      // Lock tables for alles queries executed until the end of this public function
      $lock  = "LOCK TABLE ${p}usr READ, ${p}grp READ, ${p}ldr READ, ${p}grp_usr READ;";
      $result = $this->conn->Query($lock);
      if (!$result) {
        $this->logLastError('get_arr_grp');
        return false;
      }

  //------

      if (!$trash) {
          $trashoption = "WHERE grp_trash !=1";
      }

      $query  = "SELECT * FROM ${p}grp $trashoption ORDER BY grp_name;";
      $this->conn->Query($query);

      // rows into array
      $groups = array();
      $i=0;

      $rows = $this->conn->RecordsArray(MYSQL_ASSOC);

      foreach ($rows as $row){
          $groups[] = $row;

          // append user count
          $groups[$i]['count_users'] = $this->grp_count_users($row['grp_ID']);

          // append leader array
          $ldr_id_array = $this->grp_get_ldrs($row['grp_ID']);
          $ldr_name_array = array();
          $j = 0;
          foreach ($ldr_id_array as $ldr_id) {
              $ldr_name_array[$j] = $this->usr_id2name($ldr_id);
              $j++;
          }

          $groups[$i]['leader_name'] = $ldr_name_array;

          $i++;
      }

  //------

      // Unlock tables
      $unlock = "UNLOCK TABLES;";
      $result = $this->conn->Query($unlock);
      if (!$result) {
        $this->logLastError('get_arr_grp');
        return false;
      }

      return $groups;
  }

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
  * @return array
  * @author sl
  *
  */
  public function get_arr_grp_by_leader($leader_id,$trash=0) {
      $leader_id = MySQL::SQLValue($leader_id, MySQL::SQLVALUE_NUMBER  );

      $p = $this->kga['server_prefix'];

      // Lock tables for alles queries executed until the end of this public function
      $lock  = "LOCK TABLE ${p}usr READ, ${p}grp READ, ${p}ldr READ;";
      $result = $this->conn->Query($lock);
      if (!$result) {
        $this->logLastError('get_arr_grp_by_leader');
        return false;
      }

  //------

      if (!$trash) {
          $trashoption = "AND grp_trash !=1";
      }
      $query = "SELECT ${p}grp.*
      FROM ${p}grp JOIN ${p}ldr ON ${p}grp.grp_ID =${p}ldr.grp_ID
      WHERE grp_leader = $leader_id $trashoption ORDER BY grp_name";
      $result = $this->conn->Query($query);
      if (!$result) {
        $this->logLastError('get_arr_grp_by_leader');
        return false;
      }

      // rows into array
      $groups = array();
      $i=0;

      $rows = $this->conn->RecordsArray(MYSQL_ASSOC);

      foreach ($rows as $row){
          $groups[] = $row;

          // append user count
          $groups[$i]['count_users'] = $this->grp_count_users($row['grp_ID']);

          // append leader array
          $ldr_id_array = $this->grp_get_ldrs($row['grp_ID']);
          $ldr_name_array = array();
          $j = 0;
          foreach ($ldr_id_array as $ldr_id) {
              $ldr_name_array[$j] = $this->usr_id2name($ldr_id);
              $j++;
          }

          $groups[$i]['leader_name'] = $ldr_name_array;

          $i++;
      }

  //------

      // Unlock tables
      $unlock = "UNLOCK TABLES;";
      $result = $this->conn->Query($unlock);
      if (!$result) {
        $this->logLastError('get_arr_grp_by_leader');
        return false;
      }

      return $groups;
  }

  /**
  * performed when the stop buzzer is hit.
  * Checks which record is currently recording and
  * writes the end time into that entry.
  * if the measured timevalue is longer than one calendar day
  * it is split up and stored in the DB by days
  *
  * @param integer $user ID of user
  * @author th
  * @return boolean
  */
  public function stopRecorder() {
  ## stop running recording |
      $table = $this->kga['server_prefix']."zef";

      $last_task        = $this->get_event_last(); // aktuelle vorgangs-ID auslesen

	  if(!empty($last_task['zef_out']))
	  { // last event was already stopped!
	  	return false;
	  }		
		
      $filter['zef_ID'] = $last_task['zef_ID'];

      $rounded = Rounding::roundTimespan($last_task['zef_in'],time(),$this->kga['conf']['roundPrecision']);

      $values['zef_in'] = $rounded['start'];
      $values['zef_out']  = $rounded['end'];
      $values['zef_time'] = $values['zef_out']-$values['zef_in'];


      $query = MySQL::BuildSQLUpdate($table, $values, $filter);

      return $this->conn->Query($query);
  }

  /**
  * starts timesheet record
  *
  * @param integer $pct_ID ID of project to record
  * @author th
  * @return boolean
  */
  public function startRecorder($pct_ID,$evt_ID,$user) {
      if (! $this->conn->TransactionBegin()) {
        $this->logLastError('startRecorder');
        return false;
      }

      $pct_ID = MySQL::SQLValue($pct_ID, MySQL::SQLVALUE_NUMBER  );
      $evt_ID = MySQL::SQLValue($evt_ID, MySQL::SQLVALUE_NUMBER  );
      $user   = MySQL::SQLValue($user  , MySQL::SQLVALUE_NUMBER  );


      $values ['zef_pctID'] = $pct_ID;
      $values ['zef_evtID'] = $evt_ID;
      $values ['zef_in']    = time();
      $values ['zef_usrID'] = $user;
      $rate = $this->get_best_fitting_rate($user,$pct_ID,$evt_ID);
      if ($rate)
        $values ['zef_rate'] = $rate;

      $table = $this->kga['server_prefix']."zef";
      $result = $this->conn->InsertRow($table, $values);

      if (! $result) {
        $this->logLastError('startRecorder');
        return false;
      }

      unset($values);
      $values ['lastRecord'] = $this->conn->GetLastInsertID();
      $table = $this->kga['server_prefix']."usr";
      $filter  ['usr_ID'] = $user;
      $query = MySQL::BuildSQLUpdate($table, $values, $filter);

      $success = true;

      if (!$this->conn->Query($query)) $success = false;

      if ($success) {
          if (! $this->conn->TransactionEnd()) {
            $this->logLastError('startRecorder');
            return false;
          }
      } else {
          if (! $this->conn->TransactionRollback()) {
            $this->logLastError('startRecorder');
            return false;
          }
      }

      return $success;
  }

  /**
  * Just edit the project for an entry. This is used for changing the project
  * of a running entry.
  *
  * @param $zef_id id of the timesheet entry
  * @param $pct_id id of the project to change to
  */
  public function zef_edit_pct($zef_id,$pct_id) {
      $zef_id = MySQL::SQLValue($zef_id, MySQL::SQLVALUE_NUMBER  );
      $pct_id = MySQL::SQLValue($pct_id, MySQL::SQLVALUE_NUMBER );

      $table = $this->kga['server_prefix']."zef";

      $filter['zef_id'] = $zef_id;

      $values['zef_pctID'] = $pct_id;

      $query = MySQL::BuildSQLUpdate($table, $values, $filter);

      return $this->conn->Query($query);
  }

  /**
  * Just edit the task for an entry. This is used for changing the task
  * of a running entry.
  *
  * @param $zef_id id of the timesheet entry
  * @param $evt_id id of the task to change to
  */
  public function zef_edit_evt($zef_id,$evt_id) {
      $zef_id = MySQL::SQLValue($zef_id, MySQL::SQLVALUE_NUMBER  );
      $evt_id = MySQL::SQLValue($evt_id, MySQL::SQLVALUE_NUMBER );

      $table = $this->kga['server_prefix']."zef";

      $filter['zef_id'] = $zef_id;

      $values['zef_evtID'] = $evt_id;

      $query = MySQL::BuildSQLUpdate($table, $values, $filter);

      return $this->conn->Query($query);
  }

  /**
  * Just edit the comment an entry. This is used for editing the comment
  * of a running entry.
  *
  * @param $zef_ID id of the timesheet entry
  * @param $comment_type new type of the comment
  * @param $comment the comment text
  */
  public function zef_edit_comment($zef_ID,$comment_type,$comment) {
      $zef_ID       = MySQL::SQLValue($zef_ID, MySQL::SQLVALUE_NUMBER  );
      $comment_type = MySQL::SQLValue($comment_type );
      $comment      = MySQL::SQLValue($comment );

      $table = $this->kga['server_prefix']."zef";

      $filter['zef_ID'] = $zef_ID;

      $values['zef_comment_type'] = $comment_type;
      $values['zef_comment']      = $comment;

      $query = MySQL::BuildSQLUpdate($table, $values, $filter);

      return $this->conn->Query($query);
  }

  /**
  * Just edit the starttime of an entry. This is used for editing the starttime
  * of a running entry.
  *
  * @param $zef_ID id of the timesheet entry
  * @param $starttime the new starttime
  */
  function zef_edit_starttime($zef_ID,$starttime) {
      $zef_ID       = MySQL::SQLValue($zef_ID, MySQL::SQLVALUE_NUMBER  );
      $starttime    = MySQL::SQLValue($starttime );

      $table = $this->kga['server_prefix']."zef";

      $filter['zef_ID'] = $zef_ID;

      $values['zef_in'] = $starttime;

      $query = MySQL::BuildSQLUpdate($table, $values, $filter);

      return $this->conn->Query($query);

  }

  /**
  * return ID of specific user named 'XXX'
  *
  * @param integer $name name of user in table usr
  * @return id of the customer
  */
  public function knd_name2id($name) {
      return $this->name2id($this->kga['server_prefix']."knd",'knd_ID','knd_name',$name);
  }

  /**
  * return ID of specific user named 'XXX'
  *
  * @param integer $name name of user in table usr
  * @return string
  * @author th
  */
  public function usr_name2id($name) {
      return $this->name2id($this->kga['server_prefix']."usr",'usr_ID','usr_name',$name);
  }

  /**
   * Query a table for an id by giving the name of an entry.
   * @author sl
   */
  private function name2id($table,$outColumn,$filterColumn,$value) {
      $filter [$filterColumn] = MySQL::SQLValue($value);
      $columns[] = $outColumn;

      $result = $this->conn->SelectRows($table, $filter, $columns);
      if ($result == false) {
          $this->logLastError('name2id');
          return false;
      }

      $row = $this->conn->RowArray(0,MYSQL_ASSOC);

      if ($row === false)
        return false;

      return $row[$outColumn];
  }

  /**
  * return name of a user with specific ID
  *
  * @param string $id the user's usr_ID
  * @return int
  * @author th
  */
  public function usr_id2name($id) {
      $filter ['usr_ID'] = MySQL::SQLValue($id, MySQL::SQLVALUE_NUMBER);
      $columns[] = "usr_name";
      $table = $this->kga['server_prefix']."usr";

      $result = $this->conn->SelectRows($table, $filter, $columns);
      if ($result == false) {
          $this->logLastError('usr_id2name');
          return false;
      }

      $row = $this->conn->RowArray(0,MYSQL_ASSOC);
      return $row['usr_name'];
  }

  /**
  * returns the date of the first timerecord of a user (when did the user join?)
  * this is needed for the datepicker
  * @param integer $id of user
  * @return integer unix seconds of first timesheet record
  * @author th
  */
  public function getjointime($usr_id) {
      $usr_id = MySQL::SQLValue($usr_id, MySQL::SQLVALUE_NUMBER);
      $p = $this->kga['server_prefix'];

      $query = "SELECT zef_in FROM ${p}zef WHERE zef_usrID = $usr_id ORDER BY zef_in ASC LIMIT 1;";

      $result = $this->conn->Query($query);
      if ($result == false) {
          $this->logLastError('getjointime');
          return false;
      }

      $result_array = $this->conn->RowArray(0,MYSQL_NUM);

      if ($result_array[0] == 0) {
          return mktime(0,0,0,date("n"),date("j"),date("Y"));
      } else {
          return $result_array[0];
      }
  }

  /**
  * returns list of users the given user can watch
  *
  * @param integer $user ID of user in table usr
  * @return array
  * @author sl
  */
  public function get_arr_watchable_users($user) {
      $arr = array();
      $user_id = MySQL::SQLValue($user['usr_ID'], MySQL::SQLVALUE_NUMBER);

      if ($user['usr_sts'] == "0") { // if is admin
        $query = "SELECT * FROM " . $this->kga['server_prefix'] . "usr WHERE usr_trash=0 ORDER BY usr_name";
        $result = $this->conn->Query($query);
        return $this->conn->RecordsArray(MYSQL_ASSOC);
      }

      // get groups the user is a leader of

      $query = "SELECT grp_ID FROM " . $this->kga['server_prefix'] . "grp_ldr WHERE grp_leader=$user_id";
      $success = $this->conn->Query($query);

      if (!$success) {
        $this->logLastError('get_arr_watchable_users');
        return array();
      }

      $rows = $this->conn->RecordsArray(MYSQL_ASSOC);
      $leadingGroups = array();
      foreach ($rows as $row) {
        $leadingGroups[] = $row['grp_ID'];
      }

      return $this->get_arr_usr(0,$leadingGroups);

  }

  /**
  * returns assoc. array where the index is the ID of a user and the value the time
  * this user has accumulated in the given time with respect to the filtersettings
  *
  * @param integer $in from this timestamp
  * @param integer $out to this  timestamp
  * @param integer $user ID of user in table usr
  * @param integer $customer ID of customer in table knd
  * @param integer $project ID of project in table pct
  * @return array
  * @author sl
  */
  public function get_arr_time_usr($in,$out,$users = null, $customers = null, $projects = null, $events = null) {
      $in    = MySQL::SQLValue($in    , MySQL::SQLVALUE_NUMBER);
      $out   = MySQL::SQLValue($out   , MySQL::SQLVALUE_NUMBER);

      $p     = $this->kga['server_prefix'];

      $whereClauses = $this->zef_whereClausesFromFilters($users,$customers,$projects,$events);
      $whereClauses[] = "${p}usr.usr_trash=0";

      if ($in)
        $whereClauses[]="zef_out > $in";
      if ($out)
        $whereClauses[]="zef_in < $out";

      $query = "SELECT zef_in,zef_out, usr_ID, (zef_out - zef_in) / 3600 * zef_rate AS costs
              FROM " . $this->kga['server_prefix'] . "zef
              Join " . $this->kga['server_prefix'] . "pct ON zef_pctID = pct_ID
              Join " . $this->kga['server_prefix'] . "knd ON pct_kndID = knd_ID
              Join " . $this->kga['server_prefix'] . "usr ON zef_usrID = usr_ID
              Join " . $this->kga['server_prefix'] . "evt ON evt_ID    = zef_evtID "
              .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses). " ORDER BY zef_in DESC;";
      $result = $this->conn->Query($query);

      if (! $result) {
        $this->logLastError('get_arr_time_usr');
        return array();
      }

      $rows = $this->conn->RecordsArray(MYSQL_ASSOC);
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

  /**
  * returns list of time summary attached to customer ID's within specific timespace as array
  *
  * @param integer $in start of timespace in unix seconds
  * @param integer $out end of timespace in unix seconds
  * @param integer $user filter for only this ID of auser
  * @param integer $customer filter for only this ID of a customer
  * @param integer $project filter for only this ID of a project
  * @return array
  * @author sl
  */
  public function get_arr_time_knd($in,$out,$users = null, $customers = null, $projects = null, $events = null) {
      $in    = MySQL::SQLValue($in    , MySQL::SQLVALUE_NUMBER);
      $out   = MySQL::SQLValue($out   , MySQL::SQLVALUE_NUMBER);

      $p     = $this->kga['server_prefix'];

      $whereClauses = $this->zef_whereClausesFromFilters($users,$customers,$projects,$events);
      $whereClauses[] = "${p}knd.knd_trash=0";

      if ($in)
        $whereClauses[]="zef_out > $in";
      if ($out)
        $whereClauses[]="zef_in < $out";


      $query = "SELECT zef_in,zef_out, knd_ID, (zef_out - zef_in) / 3600 * zef_rate AS costs
              FROM " . $this->kga['server_prefix'] . "zef
              Left Join " . $this->kga['server_prefix'] . "pct ON zef_pctID = pct_ID
              Left Join " . $this->kga['server_prefix'] . "knd ON pct_kndID = knd_ID ".
              (count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses);

      $result = $this->conn->Query($query);
      if (! $result) {
        $this->logLastError('get_arr_time_knd');
        return array();
      }
      $rows = $this->conn->RecordsArray(MYSQL_ASSOC);
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

  /**
  * returns list of time summary attached to project ID's within specific timespace as array
  *
  * @param integer $in start time in unix seconds
  * @param integer $out end time in unix seconds
  * @param integer $user filter for only this ID of auser
  * @param integer $customer filter for only this ID of a customer
  * @param integer $project filter for only this ID of a project
  * @return array
  * @author sl
  */
  public function get_arr_time_pct($in,$out,$users = null, $customers = null, $projects = null,$events = null) {
      $in    = MySQL::SQLValue($in    , MySQL::SQLVALUE_NUMBER);
      $out   = MySQL::SQLValue($out   , MySQL::SQLVALUE_NUMBER);

      $p     = $this->kga['server_prefix'];

      $whereClauses = $this->zef_whereClausesFromFilters($users,$customers,$projects,$events);
      $whereClauses[] = "${p}pct.pct_trash=0";

      if ($in)
        $whereClauses[]="zef_out > $in";
      if ($out)
        $whereClauses[]="zef_in < $out";

      $query = "SELECT zef_in, zef_out ,zef_pctID, (zef_out - zef_in) / 3600 * zef_rate AS costs
          FROM " . $this->kga['server_prefix'] . "zef
          Left Join " . $this->kga['server_prefix'] . "pct ON zef_pctID = pct_ID
          Left Join " . $this->kga['server_prefix'] . "knd ON pct_kndID = knd_ID ".
          (count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses);

      $result = $this->conn->Query($query);
      if (! $result) {
        $this->logLastError('get_arr_time_pct');
        return array();
      }
      $rows = $this->conn->RecordsArray(MYSQL_ASSOC);
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

  /**
  * returns list of time summary attached to event ID's within specific timespace as array
  *
  * @param integer $in start time in unix seconds
  * @param integer $out end time in unix seconds
  * @param integer $user filter for only this ID of auser
  * @param integer $customer filter for only this ID of a customer
  * @param integer $project filter for only this ID of a project
  * @return array
  * @author sl
  */
  public function get_arr_time_evt($in,$out,$users = null, $customers = null, $projects = null, $events = null) {
      $in    = MySQL::SQLValue($in    , MySQL::SQLVALUE_NUMBER);
      $out   = MySQL::SQLValue($out   , MySQL::SQLVALUE_NUMBER);

      $p     = $this->kga['server_prefix'];

      $whereClauses = $this->zef_whereClausesFromFilters($users,$customers,$projects,$events);
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

      $result = $this->conn->Query($query);
      if (! $result) {
        $this->logLastError('get_arr_time_evt');
        return array();
      }
      $rows = $this->conn->RecordsArray(MYSQL_ASSOC);
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

  /**
  * Set field usr_sts for users to 1 if user is a group leader, otherwise to 2.
  * Admin status will never be changed.
  * Calling public function should start and end sql transaction.
  *
  * @author sl
  */
  public function update_leader_status() {
      $query = "UPDATE " . $this->kga['server_prefix'] . "usr," . $this->kga['server_prefix'] . "ldr SET usr_sts = 2 WHERE usr_sts = 1";
      $result = $this->conn->Query($query);
      if ($result == false) {
          $this->logLastError('update_leader_status');
          return false;
      }

      $query = "UPDATE " . $this->kga['server_prefix'] . "usr," . $this->kga['server_prefix'] . "ldr SET usr_sts = 1 WHERE usr_sts = 2 AND grp_leader = usr_ID";
      $result = $this->conn->Query($query);
      if ($result == false) {
          $this->logLastError('update_leader_status');
          return false;
      }

      return true;
  }

  /**
  * Save rate to database.
  *
  * @author sl
  */
  public function save_rate($user_id,$project_id,$event_id,$rate) {
    // validate input
    if ($user_id == NULL || !is_numeric($user_id)) $user_id = "NULL";
    if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
    if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";
    if (!is_numeric($rate)) return false;


    // build update or insert statement
    if ($this->get_rate($user_id,$project_id,$event_id) === false)
      $query = "INSERT INTO " . $this->kga['server_prefix'] . "rates VALUES($user_id,$project_id,$event_id,$rate);";
    else
      $query = "UPDATE " . $this->kga['server_prefix'] . "rates SET rate = $rate WHERE ".
    (($user_id=="NULL")?"user_id is NULL":"user_id = $user_id"). " AND ".
    (($project_id=="NULL")?"project_id is NULL":"project_id = $project_id"). " AND ".
    (($event_id=="NULL")?"event_id is NULL":"event_id = $event_id");

    $result = $this->conn->Query($query);

    if ($result == false) {
      $this->logLastError('save_rate');
      return false;
    }
    else
      return true;
  }

  /**
  * Read rate from database.
  *
  * @author sl
  */
  public function get_rate($user_id,$project_id,$event_id) {
    // validate input
    if ($user_id == NULL || !is_numeric($user_id)) $user_id = "NULL";
    if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
    if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";


    $query = "SELECT rate FROM " . $this->kga['server_prefix'] . "rates WHERE ".
    (($user_id=="NULL")?"user_id is NULL":"user_id = $user_id"). " AND ".
    (($project_id=="NULL")?"project_id is NULL":"project_id = $project_id"). " AND ".
    (($event_id=="NULL")?"event_id is NULL":"event_id = $event_id");

    $result = $this->conn->Query($query);

    if ($this->conn->RowCount() == 0)
      return false;

    $data = $this->conn->rowArray(0,MYSQL_ASSOC);
    return $data['rate'];
  }

  /**
  * Remove rate from database.
  *
  * @author sl
  */
  public function remove_rate($user_id,$project_id,$event_id) {
    // validate input
    if ($user_id == NULL || !is_numeric($user_id)) $user_id = "NULL";
    if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
    if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";


    $query = "DELETE FROM " . $this->kga['server_prefix'] . "rates WHERE ".
    (($user_id=="NULL")?"user_id is NULL":"user_id = $user_id"). " AND ".
    (($project_id=="NULL")?"project_id is NULL":"project_id = $project_id"). " AND ".
    (($event_id=="NULL")?"event_id is NULL":"event_id = $event_id");

    $result = $this->conn->Query($query);

    if ($result === false) {
      $this->logLastError('remove_rate');
      return false;
    }
    else
      return true;
  }

  /**
  * Query the database for the best fitting rate for the given user, project and event.
  *
  * @author sl
  */
  public function get_best_fitting_rate($user_id,$project_id,$event_id) {
    // validate input
    if ($user_id == NULL || !is_numeric($user_id)) $user_id = "NULL";
    if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
    if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";



    $query = "SELECT rate FROM " . $this->kga['server_prefix'] . "rates WHERE
    (user_id = $user_id OR user_id IS NULL)  AND
    (project_id = $project_id OR project_id IS NULL)  AND
    (event_id = $event_id OR event_id IS NULL)
    ORDER BY user_id DESC, event_id DESC , project_id DESC
    LIMIT 1;";

    $result = $this->conn->Query($query);

    if ($result === false) {
      $this->logLastError('get_best_fitting_rate');
      return false;
    }

    if ($this->conn->RowCount() == 0)
      return false;

    $data = $this->conn->rowArray(0,MYSQL_ASSOC);
    return $data['rate'];
  }

  /**
  * Query the database for all fitting rates for the given user, project and event.
  *
  * @author sl
  */
  public function allFittingRates($user_id,$project_id,$event_id) {
    // validate input
    if ($user_id == NULL || !is_numeric($user_id)) $user_id = "NULL";
    if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
    if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";



    $query = "SELECT rate, user_id, project_id, event_id FROM " . $this->kga['server_prefix'] . "rates WHERE
    (user_id = $user_id OR user_id IS NULL)  AND
    (project_id = $project_id OR project_id IS NULL)  AND
    (event_id = $event_id OR event_id IS NULL)
    ORDER BY user_id DESC, event_id DESC , project_id DESC;";

    $result = $this->conn->Query($query);

    if ($result === false) {
      $this->logLastError('allFittingRates');
      return false;
    }

    return $this->conn->RecordsArray(MYSQL_ASSOC);
  }

  /**
  * Save fixed rate to database.
  *
  * @author sl
  */
  public function save_fixed_rate($project_id,$event_id,$rate) {
    // validate input
    if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
    if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";
    if (!is_numeric($rate)) return false;


    // build update or insert statement
    if ($this->get_fixed_rate($project_id,$event_id) === false)
      $query = "INSERT INTO " . $this->kga['server_prefix'] . "fixed_rates VALUES($project_id,$event_id,$rate);";
    else
      $query = "UPDATE " . $this->kga['server_prefix'] . "fixed_rates SET rate = $rate WHERE ".
    (($project_id=="NULL")?"project_id is NULL":"project_id = $project_id"). " AND ".
    (($event_id=="NULL")?"event_id is NULL":"event_id = $event_id");

    $result = $this->conn->Query($query);

    if ($result == false) {
      $this->logLastError('save_fixed_rate');
      return false;
    }
    else
      return true;
  }

  /**
  * Read fixed rate from database.
  *
  * @author sl
  */
  public function get_fixed_rate($project_id,$event_id) {
    // validate input
    if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
    if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";


    $query = "SELECT rate FROM " . $this->kga['server_prefix'] . "fixed_rates WHERE ".
    (($project_id=="NULL")?"project_id is NULL":"project_id = $project_id"). " AND ".
    (($event_id=="NULL")?"event_id is NULL":"event_id = $event_id");

    $result = $this->conn->Query($query);

    if ($result === false) {
      $this->logLastError('get_fixed_rate');
      return false;
    }

    if ($this->conn->RowCount() == 0)
      return false;

    $data = $this->conn->rowArray(0,MYSQL_ASSOC);
    return $data['rate'];
  }

  /**
   *
   * get the whole budget used for the event
   * @param integer $project_id
   * @param integer $event_id
   */
  public function get_budget_used($project_id,$event_id) {
  	$zefs = $this->get_arr_zef(0, time(), null, null, array($project_id), array($event_id));
  	$budgetUsed = 0;
  	if(is_array($zefs)) {
	  	foreach($zefs as $zef) {
	  		$budgetUsed+= $zef['wage_decimal'];
	  	}
  	}
  	return $budgetUsed;
  }

  /**
  * Read event budgets
  *
  * @author mo
  */
  public function get_evt_budget($project_id,$event_id) {
    // validate input
    if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
    if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";


    $query = "SELECT evt_budget, evt_approved, evt_effort FROM " . $this->kga['server_prefix'] . "pct_evt WHERE ".
    (($project_id=="NULL")?"pct_ID is NULL":"pct_ID = $project_id"). " AND ".
    (($event_id=="NULL")?"evt_ID is NULL":"evt_ID = $event_id");

    $result = $this->conn->Query($query);

    if ($result === false) {
      $this->logLastError('get_evt_budget');
      return false;
    }
    $data = $this->conn->rowArray(0,MYSQL_ASSOC);

  	$zefs = $this->get_arr_zef(0, time(), null, null, array($project_id), array($event_id));
  	foreach($zefs as $zef) {
    	$data['evt_budget']+= $zef['zef_budget'];
    	$data['evt_approved']+= $zef['zef_approved'];
  	}
    return $data;
  }

  /**
  * Remove fixed rate from database.
  *
  * @author sl
  */
  public function remove_fixed_rate($project_id,$event_id) {
    // validate input
    if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
    if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";


    $query = "DELETE FROM " . $this->kga['server_prefix'] . "fixed_rates WHERE ".
    (($project_id=="NULL")?"project_id is NULL":"project_id = $project_id"). " AND ".
    (($event_id=="NULL")?"event_id is NULL":"event_id = $event_id");

    $result = $this->conn->Query($query);

    if ($result === false) {
      $this->logLastError('remove_fixed_rate');
      return false;
    }
    else
      return true;
  }

  /**
  * Query the database for the best fitting fixed rate for the given user, project and event.
  *
  * @author sl
  */
  public function get_best_fitting_fixed_rate($project_id,$event_id) {
    // validate input
    if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
    if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";



    $query = "SELECT rate FROM " . $this->kga['server_prefix'] . "fixed_rates WHERE
    (project_id = $project_id OR project_id IS NULL)  AND
    (event_id = $event_id OR event_id IS NULL)
    ORDER BY event_id DESC , project_id DESC
    LIMIT 1;";

    $result = $this->conn->Query($query);

    if ($result === false) {
      $this->logLastError('get_best_fitting_fixed_rate');
      return false;
    }

    if ($this->conn->RowCount() == 0)
      return false;

    $data = $this->conn->rowArray(0,MYSQL_ASSOC);
    return $data['rate'];
  }

  /**
  * Query the database for all fitting fixed rates for the given user, project and event.
  *
  * @author sl
  */
  public function allFittingFixedRates($project_id,$event_id) {
    // validate input
    if ($project_id == NULL || !is_numeric($project_id)) $project_id = "NULL";
    if ($event_id == NULL || !is_numeric($event_id)) $event_id = "NULL";



    $query = "SELECT rate, project_id, event_id FROM " . $this->kga['server_prefix'] . "fixed_rates WHERE
    (project_id = $project_id OR project_id IS NULL)  AND
    (event_id = $event_id OR event_id IS NULL)
    ORDER BY event_id DESC , project_id DESC;";

    $result = $this->conn->Query($query);

    if ($result === false) {
      $this->logLastError('allFittingFixedRates');
      return false;
    }

    return $this->conn->RecordsArray(MYSQL_ASSOC);
  }

  /**
  * Save a new secure key for a user to the database. This key is stored in the users cookie and used
  * to reauthenticate the user.
  *
  * @author sl
  */
  public function usr_loginSetKey($userId,$keymai) {
    $p = $this->kga['server_prefix'];

    $query = "UPDATE ${p}usr SET secure='$keymai',ban=0,banTime=0 WHERE usr_ID='".
      mysql_real_escape_string($userId)."';";
    $this->conn->Query($query);
  }

  /**
  * Save a new secure key for a customer to the database. This key is stored in the clients cookie and used
  * to reauthenticate the customer.
  *
  * @author sl
  */
  public function knd_loginSetKey($customerId,$keymai) {
    $p = $this->kga['server_prefix'];

    $query = "UPDATE ${p}knd SET knd_secure='$keymai' WHERE knd_ID='".
      mysql_real_escape_string($customerId)."';";
    $this->conn->Query($query);
  }

  /**
  * Update the ban status of a user. This increments the ban counter.
  * Optionally it sets the start time of the ban to the current time.
  *
  * @author sl
  */
  public function loginUpdateBan($userId,$resetTime = false) {
      $table = $this->kga['server_prefix']."usr";

      $filter ['usr_ID']  = MySQL::SQLValue($userId);

      $values ['ban']       = "ban+1";
      if ($resetTime)
        $values ['banTime'] = MySQL::SQLValue(time(),MySQL::SQLVALUE_NUMBER);

      $table = $this->kga['server_prefix']."usr";
      $query = MySQL::BuildSQLUpdate($table, $values, $filter);

      $this->conn->Query($query);
  }


  /**
   * Return all rows for the given sql query.
   *
   * @param string $query the sql query to execute
   */
  public function queryAll($query) {
    return $this->conn->QueryArray($query);
  }
  
  /**
   * checks if given $projectId exists in the db
   * 
   * @param int $projectId
   * @return bool
   */
  public function isValidProjectId($projectId)
  {
  	
  	$table = $this->getProjectTable();
	$filter = array('pct_ID' => $projectId, 'pct_trash' => 0);
	return $this->rowExists($table, $filter);
  }
  
  /**
   * checks if given $eventId exists in the db
   * 
   * @param int $eventId
   * @return bool
   */
  public function isValidEventId($eventId)
  {
  	
  	$table = $this->getEventTable();
	$filter = array('evt_ID' => $eventId, 'evt_trash' => 0);
	return $this->rowExists($table, $filter);
  }
  
  
 /**
   * checks if a given db row based on the $idColumn & $id exists
   * @param string $table
   * @param array $filter
   * @return bool
   */
  protected function rowExists($table, Array $filter)
  {
	$select = $this->conn->SelectRows($table, $filter);
	
	if(!$select) {
		$this->logLastError('rowExists');
		return false;
	}
	else 
	{
		$rowExits = (bool)$this->conn->RowArray(0, MYSQL_ASSOC);
		return $rowExits;
	}
  }
  
  /************************************************************************************************
   * EXPENSES
   */
  
  /**
   * returns expenses for specific user as multidimensional array
   * @TODO: needs comments
   * @param integer $user ID of user in table usr
   * @return array
   * @author th
   * @author Alexander Bauer
   */
  public function get_arr_exp($start, $end, $users = null, $customers = null, $projects = null, $limit=false, $reverse_order=false, $filter_refundable = -1, $filterCleared = null, $startRows = 0, $limitRows = 0, $countOnly = false) {
  	$conn = $this->conn;
  	$kga = $this->kga;
  	$p     = $kga['server_prefix'];
  
  	if (!is_numeric($filterCleared)) {
  		$filterCleared = $kga['conf']['hideClearedEntries']-1; // 0 gets -1 for disabled, 1 gets 0 for only not cleared entries
  	}
  
  	$start  = MySQL::SQLValue($start    , MySQL::SQLVALUE_NUMBER);
  	$end = MySQL::SQLValue($end   , MySQL::SQLVALUE_NUMBER);
  	$limit = MySQL::SQLValue($limit , MySQL::SQLVALUE_BOOLEAN);
  
  	$p     = $kga['server_prefix'];
  
  	$whereClauses = $this->exp_whereClausesFromFilters($users, $customers, $projects);
  
  	if (isset($kga['customer']))
  		$whereClauses[] = "${p}pct.pct_internal = 0";
  
  	if ($start)
  		$whereClauses[]="exp_timestamp >= $start";
  	if ($end)
  		$whereClauses[]="exp_timestamp <= $end";
  	if ($filterCleared > -1)
  		$whereClauses[] = "exp_cleared = $filterCleared";
  
  	switch ($filter_refundable) {
  		case 0:
  			$whereClauses[] = "exp_refundable > 0";
  			break;
  		case 1:
  			$whereClauses[] = "exp_refundable <= 0";
  			break;
  		case -1:
  		default:
  			// return all expenses - refundable and non refundable
  	}
  	
  	if ($limit) {
  		if(!empty($limitRows)) {
  			$startRows = (int)$startRows;
  			$limit = "LIMIT $startRows, $limitRows";
  		} else {
  			if (isset($this->kga['conf']['rowlimit'])) {
  				$limit = "LIMIT " .$this->kga['conf']['rowlimit'];
  			} else {
  				$limit="LIMIT 100";
  			}
  		}
  	} else {
  		$limit="";
  	}
  	
  	
  	$select = "SELECT exp_ID, exp_timestamp, exp_multiplier, exp_value, exp_pctID, exp_designation, exp_usrID, pct_ID,
  				knd_name, pct_kndID, pct_name, exp_comment, exp_refundable,
  				exp_comment_type, usr_name, exp_cleared";
				
  	$where = empty($whereClauses) ? '' : "WHERE ".implode(" AND ",$whereClauses);
  	$orderDirection = $reverse_order ? 'ASC' : 'DESC';
  	
  	if($countOnly) {
  		$select = "SELECT COUNT(*) AS total";
  		$limit = "";
  	}
  	 
  	$query = "$select
  		FROM ${p}exp
	  	Join ${p}pct ON exp_pctID = pct_ID
	  	Join ${p}knd ON pct_kndID = knd_ID
	  	Join ${p}usr ON exp_usrID = usr_ID 
	  	$where
	  	ORDER BY exp_timestamp $orderDirection $limit";
  	
  	$conn->Query($query);
  	
  	
  	if($countOnly) {
  		$this->conn->MoveFirst();
  		$row = $this->conn->Row();
  		return $row->total;
  	}
  	
  	
  	$i=0;
  	$arr=array();
  	/* TODO: needs revision as foreach loop */
  	$conn->MoveFirst();
  	while (! $conn->EndOfSeek()) {
  		$row = $conn->Row();
  		$arr[$i]['exp_ID']             = $row->exp_ID;
  		$arr[$i]['exp_timestamp']      = $row->exp_timestamp;
  		$arr[$i]['exp_multiplier']     = $row->exp_multiplier;
  		$arr[$i]['exp_value']          = $row->exp_value;
  		$arr[$i]['exp_pctID']          = $row->exp_pctID;
  		$arr[$i]['exp_designation']    = $row->exp_designation;
  		$arr[$i]['exp_usrID']          = $row->exp_usrID;
  		$arr[$i]['pct_ID']             = $row->pct_ID;
  		$arr[$i]['knd_name']           = $row->knd_name;
  		$arr[$i]['pct_kndID']          = $row->pct_kndID;
  		$arr[$i]['pct_name']           = $row->pct_name;
  		$arr[$i]['exp_comment']        = $row->exp_comment;
  		$arr[$i]['exp_comment_type']   = $row->exp_comment_type;
  		$arr[$i]['exp_refundable']     = $row->exp_refundable;
  		$arr[$i]['usr_name']           = $row->usr_name;
  		$arr[$i]['exp_cleared']        = $row->exp_cleared;
  		$i++;
  	}
  
  	return $arr;
  }
  
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
   */
  public function exp_whereClausesFromFilters($users, $customers, $projects ) {
  
  	if (!is_array($users)) $users = array();
  	if (!is_array($customers)) $customers = array();
  	if (!is_array($projects)) $projects = array();
  
  	for ($i = 0;$i<count($users);$i++)
  		$users[$i] = MySQL::SQLValue($users[$i], MySQL::SQLVALUE_NUMBER);
  		for ($i = 0;$i<count($customers);$i++)
  			$customers[$i] = MySQL::SQLValue($customers[$i], MySQL::SQLVALUE_NUMBER);
  			for ($i = 0;$i<count($projects);$i++)
  			$projects[$i] = MySQL::SQLValue($projects[$i], MySQL::SQLVALUE_NUMBER);
  
  			$whereClauses = array();
  
  			if (count($users) > 0) {
  			$whereClauses[] = "exp_usrID in (".implode(',',$users).")";
  		}
  
  		if (count($customers) > 0) {
  		$whereClauses[] = "knd_ID in (".implode(',',$customers).")";
  		}
  
  				if (count($projects) > 0) {
  		$whereClauses[] = "pct_ID in (".implode(',',$projects).")";
  		}
  
  		return $whereClauses;
  
	}
  
}
