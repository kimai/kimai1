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
 * Provide the database layer for MySQL.
 *
 * @author th
 * @author sl
 * @author Kevin Papst
 */
class Kimai_Database_Pdo extends Kimai_Database_Abstract
{

    /**
    * Connect to the database.
    */
    public function connect($host,$database,$username,$password,$utf8,$serverType)
    {
        $pdo_dsn = $serverType.':dbname='.$database.';host='.$host;

        try {
          $this->conn = new PDO($pdo_dsn, $username, $password);
        } catch (PDOException $pdo_ex) {
          Logger::logfile('PDO CONNECTION FAILED: ' . $pdo_ex->getMessage());
        }
    }

    private function logLastError($scope)
    {
      $err = $this->conn->errorInfo();
      Logger::logfile($scope.': ('.$err[0].') '.$err[2]);
    }

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
    private function buildSQLUpdateSet(&$keys,&$data)
    {
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

    /**
    * Bind all values from the data array to the sql query.
    * If the data array contains keys which are not present in the query you will get
    * an error when executing the statement.
    *
    * @param PDOStatement PDO statement object
    * @param array &$data array containing all data to set
    * @return true on success, false otherwise
    */
    private function bindValues(&$statement,$keys,&$data)
    {
      foreach ($keys as $key) {
        if (!isset($data[$key]))
          continue;

        $value = $data[$key];

        if (!$statement->bindValue(":$key",$value)) {
          Logger::logfile("failed binding ".$key." to ".$value);
          return false;
        }
      }
      return true;
    }

    /**
    * Adds a new customer
    *
    * @param array $data        name, address and other data of the new customer
    * @global array $this->kga         kimai-global-array
    * @return int                the customerID of the new customer, false on failure
    * @author ob
    */
    public function customer_create($data)
    {
      $data = $this->clean_data($data);

      $pdo_query = $this->conn->prepare("
      INSERT INTO " . $this->getCustomerTable() . "(
      name,
      comment,
      password,
      company,
      street,
      zipcode,
      city,
      phone,
      fax,
      mobile,
      mail,
      homepage,
      visible,
      filter,
      vat,
      contact,
      timezone
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");

      $result = $pdo_query->execute(array(
          $data['name'],
          $data['comment'],
          $data['password'],
          $data['company'],
          $data['street'],
          $data['zipcode'],
          $data['city'],
          $data['phone'],
          $data['fax'],
          $data['mobile'],
          $data['mail'],
          $data['homepage'],
          $data['visible'],
          $data['filter'],
          $data['vat'],
          $data['contact'],
          $data['timezone']
      ));

      if ($result == true) {
          return $this->conn->lastInsertId();
      } else {
          $this->logLastError('customer_create');
          return false;
      }
    }

    /**
    * Returns the data of a certain customer
    *
    * @param array $customerID        id of the customer
    * @global array $this->kga         kimai-global-array
    * @return array            the customer's data (name, address etc) as array, false on failure
    * @author ob
    */
    public function customer_get_data($customerID)
    {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT * FROM ${p}customers WHERE customerID = ?");
      $result = $pdo_query->execute(array($customerID));

      if ($result == false) {
          $this->logLastError('customer_get_data');
          return false;
      } else {
          $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);
          return $result_array;
      }
    }

    /**
    * Edits a customer by replacing his data by the new array
    *
    * @param int $customerID    id of the customer to be edited
    * @param array $data        name, address and other new data of the customer
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function customer_edit($customerID, $data)
    {
      $data = $this->clean_data($data);

      $keys = array(
        'name'    ,'comment','password' ,'company','vat',
        'contact' ,'street' ,'zipcode'  ,'city'   ,'phone',
        'fax'     ,'mobile' ,'mail'     ,'homepage',
        'visible' ,'filter', 'timezone');

      $query = 'UPDATE ' . $this->getCustomerTable() . ' SET ';
      $query .= $this->buildSQLUpdateSet($keys,$data);
      $query .= ' WHERE customerID = :customerId;';

      $statement = $this->conn->prepare($query);

      $this->bindValues($statement,$keys,$data);

      $statement->bindValue(":customerId", $customerID);

      if (!$statement->execute()) {
          $this->logLastError('customer_edit');
          return false;
      }

      return true;
    }

    /**
    * Assigns a customer to 1-n groups by adding entries to the cross table
    *
    * @param int $customerID     id of the customer to which the groups will be assigned
    * @param array $groupIDs    contains one or more groupIDs
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function assign_customerToGroups($customerID, $groupIDs)
    {
      $p = $this->kga['server_prefix'];

      $this->conn->beginTransaction();

      $pdo_query = $this->conn->prepare("DELETE FROM ${p}groups_customers WHERE customerID=?;");
      $d_result = $pdo_query->execute(array($customerID));
      if ($d_result == false) {
          $this->logLastError('assign_customerToGroups');
          $this->conn->rollBack();
          return false;
      }

      foreach ($groupIDs as $groupID) {
        $pdo_query = $this->conn->prepare("INSERT INTO ${p}groups_customers (groupID, customerID) VALUES (?,?);");
        $result = $pdo_query->execute(array($groupID,$customerID));
        if ($result == false) {
            $this->logLastError('assign_customerToGroups');
            $this->conn->rollBack();
            return false;
        }
      }

      if ($this->conn->commit() == true) {
          return true;
      } else {
          $this->logLastError('assign_customerToGroups');
          return false;
      }
    }

    /**
    * returns all IDs of the groups of the given customer
    *
    * @param int $customerID        id of the customer
    * @global array $this->kga          kimai-global-array
    * @return array               contains the groupIDs of the groups or false on error
    * @author ob
    */
    public function customer_get_groupIDs($customerID)
    {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT groupID FROM ${p}groups_customers WHERE customerID = ?;");

      $result = $pdo_query->execute(array($customerID));
      if ($result == false) {
          $this->logLastError('customer_get_groupIDs');
          return false;
      }

      $groupIDs = array();
      $counter = 0;

      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
          $groupIDs[$counter] = $row['groupID'];
          $counter++;
      }

      return $groupIDs;
    }

    /**
    * deletes a customer
    *
    * @param int $customerID        id of the customer
    * @global array $this->kga          kimai-global-array
    * @return boolean             true on success, false on failure
    * @author ob
    */
    public function customer_delete($customerID)
    {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("UPDATE ${p}customers SET trash=1 WHERE customerID = ?;");
      $result = $pdo_query->execute(array($customerID));

      if ($result == false) {
          $this->logLastError('customer_delete');
          return false;
      }

      return $result;
    }

    /**
    * Adds a new project
    *
    * @param array $data         name, comment and other data of the new project
    * @global array $this->kga         kimai-global-array
    * @return int                the ID of the new project, false on failure
    * @author ob
    */
    public function project_create($data)
    {
      $data = $this->clean_data($data);

      $pdo_query = $this->conn->prepare("INSERT INTO " . $this->getProjectTable() . " (
      customerID,
      name,
      comment,
      visible,
      internal,
      filter,
      budget,
      effort,
      approved
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);");

      $result = $pdo_query->execute(array(
      (int)$data['customerID'],
      $data['name'],
      $data['comment'],
      (int)$data['visible'],
      (int)$data['internal'],
      (int)$data['filter'],
      doubleval($data['budget']),
      doubleval($data['effort']),
      doubleval($data['approved'])));

      if ($result == true) {

        $projectID = $this->conn->lastInsertId();

        if (isset($data['defaultRate'])) {
          if (is_numeric($data['defaultRate']))
            $this->save_rate(NULL,$projectID,NULL,$data['defaultRate']);
          else
            $this->remove_rate(NULL,$projectID,NULL);
        }

        if (isset($data['myRate'])) {
          if (is_numeric($data['myRate']))
            $this->save_rate($this->kga['user']['userID'],$projectID,NULL,$data['myRate']);
          else
            $this->remove_rate($this->kga['user']['userID'],$projectID,NULL);
        }

        if (isset($data['fixedRate'])) {
          if (is_numeric($data['fixedRate']))
            $this->save_fixed_rate($projectID,NULL,$data['fixedRate']);
          else
            $this->remove_fixed_rate($projectID,NULL);
        }

          return $projectID;
      } else {
          $this->logLastError('project_create');
          return false;
      }
    }

    /**
    * Returns the data of a certain project
    *
    * @param int $projectID        ID of the project
    * @global array $this->kga         kimai-global-array
    * @return array            the project's data (name, comment etc) as array, false on failure
    * @author ob
    */
    public function project_get_data($projectID)
    {
      $p = $this->kga['server_prefix'];

      if (!is_numeric($projectID)) {
          return false;
      }

      $pdo_query = $this->conn->prepare("SELECT * FROM ${p}projects WHERE projectID = ?");
      $result = $pdo_query->execute(array($projectID));

      if ($result == false) {
          $this->logLastError('project_get_data');
          return false;
      }

      $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);

      $result_array['defaultRate'] = $this->get_rate(NULL,$projectID,NULL);
      $result_array['myRate'] = $this->get_rate($this->kga['user']['userID'],$projectID,NULL);
      $result_array['fixedRate'] = $this->get_fixed_rate($projectID,NULL);
      return $result_array;
    }

    /**
    * Edits a project by replacing its data by the new array
    *
    * @param int $projectID     id of the project to be edited
    * @param array $data        name, comment and other new data of the project
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function project_edit($projectID, $data)
    {
      $data = $this->clean_data($data);

      $this->conn->beginTransaction();

      if (isset($data['defaultRate'])) {
        if (is_numeric($data['defaultRate']))
          $this->save_rate(NULL,$projectID,NULL,$data['defaultRate']);
        else
          $this->remove_rate(NULL,$projectID,NULL);
        unset($data['defaultRate']);
      }

      if (isset($data['myRate'])) {
        if (is_numeric($data['myRate']))
          $this->save_rate($this->kga['user']['userID'],$projectID,NULL,$data['myRate']);
        else
          $this->remove_rate($this->kga['user']['userID'],$projectID,NULL);
        unset($data['myRate']);
      }

        if (isset($data['fixedRate'])) {
          if (is_numeric($data['fixedRate']))
            $this->save_fixed_rate($projectID,NULL,$data['fixedRate']);
          else
            $this->remove_fixed_rate($projectID,NULL);
        }

      $keys = array(
        'customerID', 'name', 'comment', 'visible', 'internal',
        'filter', 'budget', 'effort', 'approved');

      $query = 'UPDATE ' . $this->kga['server_prefix'] . 'projects SET ';
      $query .= $this->buildSQLUpdateSet($keys,$data);
      $query .= ' WHERE projectID = :projectID;';

      $statement = $this->conn->prepare($query);

      $this->bindValues($statement,$keys,$data);

      $statement->bindValue(":projectID", $projectID);

      if ($statement->execute() === false) {
        $this->logLastError('project_edit');
        $this->conn->rollBack();
        return false;
      }

      if ($this->conn->commit() == true) {
          return true;
      } else {
          $this->logLastError('project_edit');
          return false;
      }
    }

    /**
    * Assigns a project to 1-n groups by adding entries to the cross table
    *
    * @param int $projectID        projectID of the project to which the groups will be assigned
    * @param array $groupIDs    contains one or more groupIDs
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function assign_projectToGroups($projectID, $groupIDs)
    {
      $p = $this->kga['server_prefix'];

      $this->conn->beginTransaction();

      $pdo_query = $this->conn->prepare("DELETE FROM ${p}groups_projects WHERE projectID=?;");
      $d_result = $pdo_query->execute(array($projectID));
      if ($d_result == false) {
          $this->logLastError('assign_projectToGroups');
          $this->conn->rollBack();
          return false;
      }

      foreach ($groupIDs as $groupID) {
        $pdo_query = $this->conn->prepare("INSERT INTO ${p}groups_projects (groupID,projectID) VALUES (?,?);");
        $result = $pdo_query->execute(array($groupID,$projectID));
        if ($result == false) {
            $this->logLastError('assign_projectToGroups');
            $this->conn->rollBack();
            return false;
        }
      }

      if ($this->conn->commit() == true) {
          return true;
      } else {
          $this->logLastError('assign_projectToGroups');
          return false;
      }
    }

      /**
    * deletes a status
    *
    * @param array $statusID  statusID of the status
    * @return boolean       	 true on success, false on failure
    * @author mo
    */
    public function status_delete($statusID)
    {
      $p = $this->kga['server_prefix'];
      $pdo_query = $this->conn->prepare("DELETE FROM ${p}statuses WHERE statusID=?;");
      $d_result = $pdo_query->execute(array($statusID));
      if ($d_result == false) {
          $this->logLastError('status_delete');
          $this->conn->rollBack();
          return false;
      }
      return true;
    }

    /**
    * returns all the groups of the given project
    *
    * @param array $projectID        projectID of the project
    * @global array $this->kga         kimai-global-array
    * @return array            contains the groupIDs of the groups or false on error
    * @author ob
    */
    public function project_get_groupIDs($projectID)
    {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT groupID FROM ${p}groups_projects WHERE projectID = ?;");
      $result = $pdo_query->execute(array($projectID));
      if ($result == false) {
          $this->logLastError('project_get_groupIDs');
          return false;
      }

      $groupIDs = array();
      $counter = 0;

      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
          $groupIDs[$counter] = $row['groupID'];
          $counter++;
      }

      return $groupIDs;
    }

    /**
    * deletes a project
    *
    * @param array $projectID        projectID of the project
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function project_delete($projectID)
    {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("UPDATE ${p}projects SET trash=1 WHERE projectID = ?;");
      $result = $pdo_query->execute(array($projectID));
      if ($result == false) {
          $this->logLastError('project_delete');
          return false;
      }
      return $result;
    }

    /**
    * Adds a new activity
    *
    * @param array $data        name, comment and other data of the new activity
    * @global array $this->kga         kimai-global-array
    * @return int                the activityID of the new project, false on failure
    * @author ob
    */
    public function activity_create($data)
    {
      $data = $this->clean_data($data);

      $pdo_query = $this->conn->prepare("
      INSERT INTO " . $this->getActivityTable() . " (
      name,
      comment,
      visible,
      filter
      ) VALUES (?, ?, ?, ?, ?);");

      $result = $pdo_query->execute(array(
      $data['name'],
      $data['comment'],
      $data['visible'],
      $data['filter']
      ));

      if ($result == true) {

        $activityID = $this->conn->lastInsertId();


        if (isset($data['defaultRate'])) {
          if (is_numeric($data['defaultRate']))
            $this->save_rate(NULL,NULL,$activityID,$data['defaultRate']);
          else
            $this->remove_rate(NULL,NULL,$activityID);
        }

        if (isset($data['myRate'])) {
          if (is_numeric($data['myRate']))
            $this->save_rate($this->kga['user']['userID'],NULL,$activityID,$data['myRate']);
          else
            $this->remove_rate($this->kga['user']['userID'],NULL,$activityID);
        }

        if (isset($data['fixedRate'])) {
          if (is_numeric($data['fixedRate']))
            $this->save_fixed_rate(NULL,$activityID,$data['fixedRate']);
          else
            $this->remove_fixed_rate(NULL,$activityID);
        }

        return $activityID;
      } else {
        $this->logLastError('activity_create');
        return false;
      }
    }

    /**
    *
    * update the data for activity per project, which is budget, approved and effort
    * @param integer $projectID
    * @param integer $activityID
    * @param array $data
    */
    public function project_activity_edit($projectID, $activityID, $data)
    {
      $data = $this->clean_data($data);
      $keys = array('budget', 'effort', 'approved');

      $query = 'UPDATE ' . $this->kga['server_prefix'] . 'projects_activities SET ';
      $query .= $this->buildSQLUpdateSet($keys, $data);
      $query .= ' WHERE activityID = :activityId and projectID = :projectId;';
       $statement = $this->conn->prepare($query);

      $this->bindValues($statement,$keys,$data);
      $statement->bindValue(":activityId", $activityID);
      $statement->bindValue(":projectId", $projectID);

      if (!$statement->execute()) {
          $this->logLastError('project_activity_edit');
        return false;
      }

      if ($this->conn->commit() == true) {
          return true;
      } else {
          $this->logLastError('project_activity_edit');
          return false;
      }
    }

    /**
    * Returns the data of a certain project
    *
    * @param array $activityID        activityID of the project
    * @global array $this->kga         kimai-global-array
    * @return array            the activity's data (name, comment etc) as array, false on failure
    * @author ob
    */
    public function activity_get_data($activityID)
    {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT * FROM ${p}activities WHERE activityID = ?");
      $result = $pdo_query->execute(array($activityID));

      if ($result == false) {
          $this->logLastError('activity_get_data');
          return false;
      }

      $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);

      $result_array['defaultRate'] = $this->get_rate(NULL,NULL,$result_array['activityID']);
      $result_array['myRate'] = $this->get_rate($this->kga['user']['userID'],NULL,$result_array['activityID']);
      $result_array['fixedRate'] = $this->get_fixed_rate(NULL,$result_array['activityID']);

      return $result_array;
    }

    /**
    * Edits an activity by replacing its data by the new array
    *
    * @param array $activityID        activityID of the project to be edited
    * @param array $data        name, comment and other new data of the activity
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function activity_edit($activityID, $data)
    {
      $data = $this->clean_data($data);

      $this->conn->beginTransaction();

      if (isset($data['defaultRate'])) {
        if (is_numeric($data['defaultRate']))
          $this->save_rate(NULL,NULL,$activityID,$data['defaultRate']);
        else
          $this->remove_rate(NULL,NULL,$activityID);
        unset($data['defaultRate']);
      }

      if (isset($data['myRate'])) {
        if (is_numeric($data['myRate']))
          $this->save_rate($this->kga['user']['userID'],NULL,$activityID,$data['myRate']);
        else
          $this->remove_rate($this->kga['user']['userID'],NULL,$activityID);
        unset($data['myRate']);
      }

      if (isset($data['fixedRate'])) {
        if (is_numeric($data['fixedRate']))
          $this->save_fixed_rate(NULL,$activityID,$data['fixedRate']);
        else
          $this->remove_fixed_rate(NULL,$activityID);
        unset($data['fixedRate']);
      }

      $keys = array('name', 'comment', 'visible', 'filter', 'budget', 'effort', 'approved');

      $query = 'UPDATE ' . $this->kga['server_prefix'] . 'activities SET ';
      $query .= $this->buildSQLUpdateSet($keys,$data);
      $query .= ' WHERE activityID = :activityID;';

      $statement = $this->conn->prepare($query);

      $this->bindValues($statement,$keys,$data);

      $statement->bindValue(":activityID", $activityID);

      if (!$statement->execute()) {
          $this->logLastError('activity_edit');
        return false;
      }

      if ($this->conn->commit() == true) {
          return true;
      } else {
          $this->logLastError('activity_edit');
          return false;
      }
    }

    /**
    * Assigns an activity to 1-n groups by adding entries to the cross table
    *
    * @param int $activityID        activityID of the project to which the groups will be assigned
    * @param array $groupIDs    contains one or more groupIDs
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function assign_activityToGroups($activityID, $groupIDs)
    {
      $p = $this->kga['server_prefix'];

      $this->conn->beginTransaction();

      $pdo_query = $this->conn->prepare("DELETE FROM ${p}groups_activities WHERE activityID=?;");
      $d_result = $pdo_query->execute(array($activityID));
      if ($d_result == false) {
          $this->logLastError('assign_activityToGroups');
          $this->conn->rollBack();
          return false;
      }

      foreach ($groupIDs as $groupID) {
        $pdo_query = $this->conn->prepare("INSERT INTO ${p}groups_activities (groupID,activityID) VALUES (?,?);");
        $result = $pdo_query->execute(array($groupID,$activityID));
        if ($result == false) {
          $this->logLastError('assign_activityToGroups');
            $this->conn->rollBack();
            return false;
        }
      }

      if ($this->conn->commit() == true) {
          return true;
      } else {
          $this->logLastError('assign_activityToGroups');
          return false;
      }
    }

    /**
    * Assigns an activity to 1-n projects by adding entries to the cross table
    *
    * @param int $activityID         id of the activity to which projects will be assigned
    * @param array $gprojectIDs    contains one or more projectIDs
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob/th
    */
    public function assign_activityToProjects($activityID, $projectIDs)
    {
      $p = $this->kga['server_prefix'];

      $this->conn->beginTransaction();

      $pdo_query = $this->conn->prepare("DELETE FROM ${p}projects_activities WHERE activityID=?;");
      $d_result = $pdo_query->execute(array($activityID));
      if ($d_result == false) {
          $this->logLastError('assign_activityToProjects');
          $this->conn->rollBack();
          return false;
      }

      foreach ($projectIDs as $projectID) {
        $pdo_query = $this->conn->prepare("INSERT INTO ${p}projects_activities (projectID,activityID) VALUES (?,?);");
        $result = $pdo_query->execute(array($projectID,$activityID));
        if ($result == false) {
            $this->logLastError('assign_activityToProjects');
            $this->conn->rollBack();
            return false;
        }
      }

      if ($this->conn->commit() == true) {
          return true;
      } else {
          $this->logLastError('assign_activityToProjects');
          return false;
      }
    }

    /**
    * Assigns 1-n activities to a project by adding entries to the cross table
    *
    * @param int $projectID         id of the project to which activities will be assigned
    * @param array $activityIDs    contains one or more activityIDs
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author sl
    */
    public function assign_projectToActivities($projectID, $activityIDs) {
      $p = $this->kga['server_prefix'];

      $this->conn->beginTransaction();

      $pdo_query = $this->conn->prepare("DELETE FROM ${p}projects_activities WHERE projectID=?;");
      $d_result = $pdo_query->execute(array($projectID));
      if ($d_result == false) {
          $this->logLastError('assign_projectToActivities');
          $this->conn->rollBack();
          return false;
      }

      foreach ($activityIDs as $activityID) {
        $pdo_query = $this->conn->prepare("INSERT INTO ${p}projects_activities (activityID,projectID) VALUES (?,?);");
        $result = $pdo_query->execute(array($activityID,$projectID));
        if ($result == false) {
          $this->logLastError('assign_projectToActivities');
            $this->conn->rollBack();
            return false;
        }
      }

      if ($this->conn->commit() == true) {
          return true;
      } else {
          $this->logLastError('assign_projectToActivities');
          return false;
      }
    }

    /**
    * returns all the projects to which the activity was assigned
    *
    * @param array $activityID  activityID of the project
    * @global array $this->kga    kimai-global-array
    * @return array         contains the projectIDs of the projects or false on error
    * @author th
    */
    public function activity_get_projects($activityID) {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT projectID FROM ${p}projects_activities WHERE activityID = ?;");

      $result = $pdo_query->execute(array($activityID));
      if ($result == false) {
          $this->logLastError('activity_get_projects');
          return false;
      }

      $projectIDs = array();
      $counter = 0;

      while ($projectID = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
          $projectIDs[$counter] = $projectID['projectID'];
          $counter++;
      }

      return $projectIDs;
    }

    /**
    * returns all the activities which are assigned to a project
    *
    * @param integer $projectID  projectID of the project
    * @global array $this->kga    kimai-global-array
    * @return array         contains the activityIDs of the activities or false on error
    * @author sl
    */
    public function project_get_activities($projectID) {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare(
        "SELECT activity.*, activityID, budget, effort, approved
        FROM ${p}projects_activities AS p_a
        JOIN ${p}activities AS activity USING (activityID)
        WHERE projectID = ? AND activity.trash = 0;"
      );

      $result = $pdo_query->execute(array($projectID));
      if ($result == false) {
          $this->logLastError('project_get_activities');
          return false;
      }

      $activityIDs = array();

      while ($activityID = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
          $activityIDs[] = $activityID;
      }

      return $activityIDs;
    }

    /**
    * returns all the groups of the given activity
    *
    * @param array $activityID        activityID of the project
    * @global array $this->kga         kimai-global-array
    * @return array            contains the groupIDs of the groups or false on error
    * @author ob
    */
    public function activity_get_groups($activityID) {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT groupID FROM ${p}groups_activities WHERE activityID = ?;");

      $result = $pdo_query->execute(array($activityID));
      if ($result == false) {
          $this->logLastError('activity_get_groups');
          return false;
      }

      $groupIDs = array();
      $counter = 0;

      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
          $groupIDs[$counter] = $row['groupID'];
          $counter++;
      }

      return $groupIDs;
    }

    /**
    * deletes an activity
    *
    * @param array $activityID        activityID of the activity
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function activity_delete($activityID) {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("UPDATE ${p}activities SET trash=1 WHERE activityID = ?;");
      $result = $pdo_query->execute(array($activityID));
      if ($result == false) {
          $this->logLastError('activity_delete');
          return false;
      }

      return $result;
    }

    /**
    * Assigns a group to 1-n customers by adding entries to the cross table
    * (counterpart to assign_customerToGroups)
    *
    * @param array $groupID      ID of the group to which the customers will be assigned
    * @param array $customerIDs  contains one or more IDs of customers
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function assign_groupToCustomers($groupID, $customerIDs) {
      $p = $this->kga['server_prefix'];

      $this->conn->beginTransaction();

      $d_query = $this->conn->prepare("DELETE FROM ${p}groups_customers WHERE groupID=?;");
      $d_result = $d_query->execute(array($groupID));
      if ($d_result == false) {
          $this->logLastError('assign_groupToCustomers');
          return false;
      }

      foreach ($customerIDs as $customerID) {
        $pdo_query = $this->conn->prepare("INSERT INTO ${p}groups_customers (groupID,customerID) VALUES (?,?);");
        $result = $pdo_query->execute(array($groupID,$customerID));
        if ($result == false) {
            $this->logLastError('assign_groupToCustomers');
            return false;
        }
      }

      if ($this->conn->commit() == true) {
          return true;
      } else {
          $this->logLastError('assign_groupToCustomers');
          return false;
      }
    }

    /**
    * Assigns a group to 1-n projects by adding entries to the cross table
    * (counterpart to assign_projectToGroups)
    *
    * @param array $groupID        groupID of the group to which the projects will be assigned
    * @param array $projectIDs    contains one or more projectIDs
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function assign_groupToProjects($groupID, $projectIDs) {
      $p = $this->kga['server_prefix'];

      $this->conn->beginTransaction();

      $d_query = $this->conn->prepare("DELETE FROM ${p}groups_projects WHERE groupID=?;");
      $d_result = $d_query->execute(array($groupID));
      if ($d_result == false) {
          $this->logLastError('assign_groupToProjects');
          return false;
      }

      foreach ($projectIDs as $projectID) {
        $pdo_query = $this->conn->prepare("INSERT INTO ${p}groups_projects (groupID,projectID) VALUES (?,?);");
        $result = $pdo_query->execute(array($groupID,$projectID));
        if ($result == false) {
            $this->logLastError('assign_groupToProjects');
            return false;
        }
      }

      if ($this->conn->commit() == true) {
          return true;
      } else {
          $this->logLastError('assign_groupToProjects');
          return false;
      }
    }

    /**
    * Assigns a group to 1-n activities by adding entries to the cross table
    * (counterpart to assign_activityToGroups)
    *
    * @param array $groupID        groupID of the group to which the activities will be assigned
    * @param array $activityIDs    contains one or more activityIDs
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function assign_groupToActivities($groupID, $activityIDs) {
      $p = $this->kga['server_prefix'];

      $this->conn->beginTransaction();

      $d_query = $this->conn->prepare("DELETE FROM ${p}groups_activities WHERE groupID=?;");
      $d_result = $d_query->execute(array($groupID));
      if ($d_result == false) {
          $this->logLastError('assign_groupToActivities');
          return false;
      }

      foreach ($activityIDs as $activityID) {
        $pdo_query = $this->conn->prepare("INSERT INTO ${p}groups_activities (groupID,activityID) VALUES (?,?);");
        $result = $pdo_query->execute(array($groupID,$activityID));
        if ($result == false) {
          $this->logLastError('assign_groupToActivities');
            return false;
        }
      }

      if ($this->conn->commit() == true) {
          return true;
      } else {
          $this->logLastError('assign_groupToActivities');
          return false;
      }
    }

    /**
    * Adds a new user
    *
    * @param array $data         username, email, and other data of the new user
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function user_create($data)
    {
      $p = $this->kga['server_prefix'];

      // find random but unused user id
      do {
        $data['userID'] = random_number(9);
      } while ($this->user_get_data($data['userID']));

      $data = $this->clean_data($data);

      $pdo_query = $this->conn->prepare("INSERT INTO ${p}users (`userID`, `name`, `status`, `active` ) VALUES (?, ?, ?, ?)");

      $result = $pdo_query->execute(array(
          $data['userID'],
          $data['name'],
          $data['status'],
          $data['active']
      ));

      if ($result == true) {
          if (isset($data['rate'])) {
            if (is_numeric($data['rate']))
              $this->save_rate($data['userID'], NULL, NULL, $data['rate']);
            else
              $this->remove_rate($data['userID'], NULL, NULL);
          }
          return $data['userID'];
      } else {
          $this->logLastError('user_create');
          return false;
      }
    }

    /**
    * Returns the data of a certain user
    *
    * @param array $userID        ID of the user
    * @global array $this->kga         kimai-global-array
    * @return array            the user's data (username, email-address, status etc) as array, false on failure
    * @author ob
    */
    public function user_get_data($userID)
    {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT * FROM ${p}users WHERE userID = ?");
      $result = $pdo_query->execute(array($userID));

      if ($result == false) {
          $this->logLastError('user_get_data');
          return false;
      }
        
      $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);
      return $result_array;
    }

    /**
    * Edits a user by replacing his data and preferences by the new array
    *
    * @param array $userID       userID of the user to be edited
    * @param array $data         username, email, and other new data of the user
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function user_edit($userID, $data)
    {
      $p = $this->kga['server_prefix'];

      $data = $this->clean_data($data);

      $this->conn->beginTransaction();

      $keys = array(
        'name', 'status', 'trash', 'active', 'mail',
        'alias', 'password', 'lastRecord', 'lastProject', 'lastActivity', 'apikey'
      );

      $query = 'UPDATE ' . $this->kga['server_prefix'] . 'users SET ';
      $query .= $this->buildSQLUpdateSet($keys,$data);
      $query .= ' WHERE userID = :userID;';

      $statement = $this->conn->prepare($query);

      $this->bindValues($statement,$keys,$data);

      $statement->bindValue(":userID", $userID);

      if (!$statement->execute()) {
        $this->logLastError('user_edit');
        return false;
      }

      if (isset($data['rate'])) {
        if (is_numeric($data['rate'])) {
          $this->save_rate($userID,NULL,NULL,$data['rate']);
        } else {
          $this->remove_rate($userID,NULL,NULL);
        }
      }

      if ($this->conn->commit() == true) {
          return true;
      }

      $this->logLastError('user_edit');
      return false;
    }

    /**
     * deletes a user
     *
     * @param array $userID        userID of the user
     * @param boolean $moveToTrash whether to delete user or move to trash
     * @global array $this->kga         kimai-global-array
     * @return boolean            true on success, false on failure
     * @author ob
     */
    public function user_delete($userID, $moveToTrash = false)
    {
        $p = $this->kga['server_prefix'];

        if ($moveToTrash)
            $pdo_query = $this->conn->prepare("UPDATE ${p}user SET trash=1 WHERE userID = ?;");
        else
          $pdo_query = $this->conn->prepare("DELETE FROM ${p}user WHERE userID = ?;");

        $result = $pdo_query->execute(array($userID));
        if ($result == false) {
          $this->logLastError('user_delete');
          $this->conn->rollBack();
          return false;
        }

        return $result;
    }

    /**
    * Get a preference for a user. If no user ID is given the current user is used.
    *
    * @param string  $key     name of the preference to fetch
    * @param integer $userId  (optional) id of the user to fetch the preference for
    * @return string value of the preference or null if there is no such preference
    * @author sl
    */
    public function user_get_preference($key,$userId=null) {
      $p = $this->kga['server_prefix'];

      if ($userId === null)
        $userId = $this->kga['user']['userID'];

      $pdo_query = $this->conn->prepare("SELECT value FROM ${p}preferences WHERE userID = ? AND `option` = ?");

      $result = $pdo_query->execute(array($userId,$key));

      if ($result == false) {
          $this->logLastError('user_get_preference');
          return null;
      }

      $data = $pdo_query->fetch();

      if (!$data)
        return null;

      return $data['value'];
    }

    /**
    * Get several preferences for a user. If no user ID is given the current user is used.
    *
    * @param array   $keys    names of the preference to fetch in an array
    * @param integer $userId  (optional) id of the user to fetch the preference for
    * @return array  with keys for every found preference and the found value
    * @author sl
    */
    public function user_get_preferences(array $keys,$userId=null) {
      $p = $this->kga['server_prefix'];

      if ($userId === null) {
        $userId = $this->kga['user']['userID'];
      }

      $placeholders = implode(",",array_fill(0,count($keys),'?'));

      $pdo_query = $this->conn->prepare("SELECT `option`,value FROM ${p}preferences WHERE userID = ? AND `option` IN ($placeholders)");
      $result = $pdo_query->execute(array_merge(array($userId, $p), $keys));

      if ($result == false) {
          $this->logLastError('user_get_preferences');
          return null;
      }

      $preferences = array();

      while ($row = $pdo_query->fetch()) {
        $preferences[$row['option']] = $row['value'];
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
    public function user_get_preferences_by_prefix($prefix,$userId=null) {
      $p = $this->kga['server_prefix'];

      if ($userId === null)
        $userId = $this->kga['user']['userID'];

      $prefixLength = strlen($prefix);
      //$prefix .= '%';

      $pdo_query = $this->conn->prepare("SELECT `option`, value FROM ${p}preferences WHERE userID = ? AND `option` LIKE ?");

      $result = $pdo_query->execute(array($userId,"$prefix%"));

      if ($result == false) {
          $this->logLastError('user_get_preferences_by_prefix');
          return null;
      }

      $preferences = array();

      while ($row = $pdo_query->fetch()) {
        $key = substr($row['option'],$prefixLength);
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
    * @global array $this->kga     kimai-global-array
    * @return boolean        true on success, false on failure
    * @author sl
    */
    public function user_set_preferences(array $data,$prefix='',$userId=null) {
      $p = $this->kga['server_prefix'];

      if ($userId === null)
        $userId = $this->kga['user']['userID'];

      $this->conn->beginTransaction();

      $pdo_query = $this->conn->prepare("INSERT INTO ${p}preferences (`userID`,`option`,`value`)
      VALUES(?,?,?) ON DUPLICATE KEY UPDATE value = ?;");

      foreach ($data as $key=>$value) {
        $key = $prefix.$key;
        $result = $pdo_query->execute(array(
          $userId,$key,$value,$value));
        if (! $result) {
          $this->logLastError('user_set_preferences');
          $this->conn->rollBack();
          return false;
        }
      }

      return $this->conn->commit();
    }

    /**
    * Assigns a leader to 1-n groups by adding entries to the cross table
    *
    * @param int $userID        userID of the group leader to whom the groups will be assigned
    * @param array $groupIDs    contains one or more groupIDs
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function assign_groupleaderToGroups($userID, $groupIDs) {
      $p = $this->kga['server_prefix'];

      $this->conn->beginTransaction();

      $pdo_query = $this->conn->prepare("DELETE FROM ${p}groupleaders WHERE userID=?;");
      $d_result = $pdo_query->execute(array($userID));
      if ($d_result == false) {
              $this->logLastError('assign_groupleaderToGroups');
              $this->conn->rollBack();
              return false;
      }

      foreach ($groupIDs as $groupID) {
        $pdo_query = $this->conn->prepare("INSERT INTO ${p}groupleaders(groupID,userID) VALUES (?,?);");
        $result = $pdo_query->execute(array($groupID,$userID));
        if ($result == false) {
            $this->logLastError('assign_groupleaderToGroups');
            $this->conn->rollBack();
            return false;
        }
      }

      $this->update_leader_status();

      if ($this->conn->commit() == true) {
          return true;
      } else {
              $this->logLastError('assign_groupleaderToGroups');
          return false;
      }
    }

    /**
    * Assigns a group to 1-n group leaders by adding entries to the cross table
    * (counterpart to assign_groupleaderToGroups)
    *
    * @param array $groupID        groupID of the group to which the group leaders will be assigned
    * @param array $leaderIDs    contains one or more userIDs of the leaders)
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function assign_groupToGroupleaders($groupID, $leaderIDs) {
      $p = $this->kga['server_prefix'];

      $this->conn->beginTransaction();

      $d_query = $this->conn->prepare("DELETE FROM ${p}groupleaders WHERE groupID=?;");
      $d_result = $d_query->execute(array($groupID));
      if ($d_result == false) {
          $this->logLastError('assign_groupToGroupleaders');
          return false;
      }

      foreach ($leaderIDs as $leaderID) {
        $pdo_query = $this->conn->prepare("INSERT INTO ${p}groupleaders (groupID,userID) VALUES (?,?);");
        $result = $pdo_query->execute(array($groupID,$leaderID));
        if ($result == false) {
            $this->logLastError('assign_groupToGroupleaders');
            return false;
        }
      }

      $this->update_leader_status();

      if ($this->conn->commit() == true) {
          return true;
      } else {
          $this->logLastError('assign_groupToGroupleaders');
          return false;
      }
    }

    /**
    * returns all the groups of the given group leader
    *
    * @param array $userID        userID of the group leader
    * @global array $this->kga         kimai-global-array
    * @return array            contains the groupIDs of the groups or false on error
    * @author ob
    */
    public function groupleader_get_groups($userID) {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT groupID FROM ${p}groupleaders WHERE userID = ?;");
      $result = $pdo_query->execute(array($userID));
      if ($result == false) {
          $this->logLastError('groupleader_get_groups');
          return false;
      }

      $groupIDs = array();
      $counter = 0;

      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
          $groupIDs[$counter] = $row['groupID'];
          $counter++;
      }

      return $groupIDs;
    }

    /**
    * returns all the group leaders of the given group
    *
    * @param array $groupID        groupID of the group
    * @global array $this->kga         kimai-global-array
    * @return array            contains the userIDs of the group's group leaders or false on error
    * @author ob
    */
    public function group_get_groupleaders($groupID) {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT userID FROM ${p}groupleaders
      JOIN ${p}users USING(userID)
      WHERE groupID = ? AND trash=0;");
      $result = $pdo_query->execute(array($groupID));
      if ($result == false) {
          $this->logLastError('group_get_groupleaders');
          return false;
      }

      $leaderIDs = array();
      $counter = 0;

      while ($leader = $pdo_query->fetch()) {
          $leaderIDs[$counter] = $leader['userID'];
          $counter++;
      }

      return $leaderIDs;
    }

    /**
    * Adds a new group
    *
    * @param array $data         name and other data of the new group
    * @global array $this->kga         kimai-global-array
    * @return int                the groupID of the new group, false on failure
    * @author ob
    */
    public function group_create($data) {
      $p = $this->kga['server_prefix'];

      $data = $this->clean_data($data);

      $pdo_query = $this->conn->prepare("INSERT INTO ${p}groups (name, trash) VALUES (?, ?);");
      $result = $pdo_query->execute(array($data['name'], 0));

      if ($result == true) {
          return $this->conn->lastInsertId();
      } else {
          $this->logLastError('group_create');
          return false;
      }
    }

    /**
    * Returns the data of a certain group
    *
    * @param array $groupID        groupID of the group
    * @global array $this->kga         kimai-global-array
    * @return array            the group's data (name, leader ID, etc) as array, false on failure
    * @author ob
    */
    public function group_get_data($groupID) {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT * FROM ${p}groups WHERE groupID = ?");
      $result = $pdo_query->execute(array($groupID));

      if ($result == false) {
          $this->logLastError('group_get_data');
          return false;
      } else {
          $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);
          return $result_array;
      }
    }


    /**
    * Returns the data of a certain status
    *
    * @param array $statusID  ID of the group
    * @return array         	 the group's data (name) as array, false on failure
    * @author mo
    */
    public function status_get_data($statusID) {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT * FROM ${p}statuses WHERE statusID = ?");
      $result = $pdo_query->execute(array($statusID));

      if ($result == false) {
        $this->logLastError('status_get_data');
        return false;
      } else {
          $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);
          return $result_array;
      }
    }

    /**
    * Returns the number of users in a certain group
    *
    * @param array $groupID        groupID of the group
    * @global array $this->kga         kimai-global-array
    * @return int            the number of users in the group
    * @author ob
    */
    public function group_count_users($groupID) {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT COUNT(*) FROM ${p}groups_users WHERE groupID = ?");
      $result = $pdo_query->execute(array($groupID));

      if ($result == false) {
          $this->logLastError('group_count_users');
          return false;
      } else {
          $result_array = $pdo_query->fetch();
          return $result_array[0];
      }
    }

    /**
    * Edits a group by replacing its data by the new array
    *
    * @param array $groupID        groupID of the group to be edited
    * @param array $data    name and other new data of the group
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function group_edit($groupID, $data) {
      $p = $this->kga['server_prefix'];

      $data = $this->clean_data($data);

      $pdo_query = $this->conn->prepare("UPDATE ${p}groups SET name = ? WHERE groupID = ?;");
      $result = $pdo_query->execute(array($data['name'],$groupID));

      if ($result == false) {
          $this->logLastError('group_edit');
          return false;
      }

      return true;
    }

    /**
    * Edits a status by replacing its data by the new array
    *
    * @param array $statusID  ID of the status to be edited
    * @param array $data    name and other new data of the status
    * @return boolean       true on success, false on failure
    * @author mo
    */
    public function status_edit($statusID, $data) {
      $p = $this->kga['server_prefix'];

      $data = $this->clean_data($data);

      $pdo_query = $this->conn->prepare("UPDATE ${p}statuses SET status = ? WHERE statusID = ?;");
      $result = $pdo_query->execute(array($data['status'],$statusID));

      if ($result == false) {
          $this->logLastError('status_edit');
          return false;
      }

      return true;
    }

    /**
    * Set the groups in which the user is a member in.
    * @param int $userId   id of the user
    * @param array $groups  array of the group ids to be part of
    * @return boolean       true on success, false on failure
    * @author sl
    */
    public function setGroupMemberships($userId,array $groups = null) {
      $p = $this->kga['server_prefix'];

      $this->conn->beginTransaction();

      $pdo_query = $this->conn->prepare("DELETE FROM ${p}groups_users WHERE userID = ?");
      $result = $pdo_query->execute(array($userId));

      if (!$result) {
        $this->logLastError('setGroupMemberships');
        $this->conn->rollBack();
        return false;
      }

      foreach ($groups as $group) {
        $pdo_query = $this->conn->prepare("INSERT INTO ${p}groups_users (userID,groupID) VALUES (?,?)");
        $result = $pdo_query->execute(array($userId,$group));

        if (!$result) {
          $this->logLastError('setGroupMemberships');
          $this->conn->rollBack();
          return false;
        }
      }

      if ($this->conn->commit() == false) {
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
    $p = $this->kga['server_prefix'];

    $pdo_query = $this->conn->prepare("SELECT groupID FROM ${p}groups_users WHERE userID = ?");
    $result = $pdo_query->execute(array($userId));

    if ($result == false) {
        $this->logLastError('getGroupMemberships');
        return null;
    }

    $arr = array();
    while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
      $arr[] = $row['groupID'];
    }

    return $arr;
    }

    /**
    * deletes a group
    *
    * @param array $groupID        groupID of the group
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function group_delete($groupID) {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("UPDATE ${p}groups SET trash=1 WHERE groupID = ?;");
      $result = $pdo_query->execute(array($groupID));

      if ($result == false) {
          $this->logLastError('group_delete');
          return false;
      }

      return true;
    }

    /**
    * Returns all configuration variables
    *
    * @global array $this->kga         kimai-global-array
    * @return array            array with the options from the var table
    * @author ob
    */
    public function configuration_get_data() {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT * FROM ${p}configuration;");
      $result = $pdo_query->execute();

      if ($result == false) {
          $this->logLastError('configuration_get_data');
          return null;
      }

      $config_data = array();

      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
          $config_data[$row['option']] = $row['value'];
      }

      return $config_data;
    }

    /**
    * Edits a configuration variables by replacing the data by the new array
    *
    * @param array $data    variables array
    * @global array $this->kga         kimai-global-array
    * @return boolean            true on success, false on failure
    * @author ob
    */
    public function configuration_edit($data) {
      $p = $this->kga['server_prefix'];

      $data = $this->clean_data($data);

      $this->conn->beginTransaction();

      $statement = $this->conn->prepare("UPDATE ${p}configuration SET value = ? WHERE `option` = ?");

      foreach ($data as $key => $value) {
        $statement->bindValue(1,$value);
        $statement->bindValue(2,$key);

        if (!$statement->execute()) {
          $this->logLastError('configuration_edit');
          return false;
        }
      }

      if ($this->conn->commit() == false) {
          $this->logLastError('configuration_edit');
          return false;
      }

      return true;
    }

    /**
     * Returns a list of IDs of all current recordings.
     *
     * @param integer $user ID of user in table users
     * @return array with all IDs of current recordings. This array will be empty if there are none.
     */
    public function get_current_recordings($userID) {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT timeEntryID FROM ${p}timeSheet WHERE userID = ? AND start > 0 AND end = 0;");
      $result = $pdo_query->execute(array($userID));

      if ($result == false) {
          $this->logLastError('get_current_recordings');
          return array();
      }

      $IDs = array();

      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $IDs[] = $row['timeEntryID'];
      }

      return $IDs;
    }

    /**
    * Returns the data of a certain time record
    *
    * @param array $timeEntryID       timeEntryID of the record
    * @global array $this->kga          kimai-global-array
    * @return array               the record's data (time, activity id, project id etc) as array, false on failure
    * @author ob
    */
    public function timeSheet_get_data($timeEntryID) {
    	 
	  $table = $this->getTimeSheetTable();
	  $projectTable = $this->getProjectTable();
	  $activityTable = $this->getActivityTable();
	  $customerTable = $this->getCustomerTable();
	  $select = "SELECT $table.*, $projectTable.name AS projectName, $customerTable.name AS customerName, $activityTable.name AS activityName, $customerTable.customerID AS customerID
      				FROM $table
                	JOIN $projectTable USING(projectID)
                	JOIN $customerTable USING(customerID)
                	JOIN $activityTable USING(activityID)";
		
		

      if ($timeEntryID) {
          $pdo_query = $this->conn->prepare("$select WHERE timeEntryID = ?");
      } else {
          $pdo_query = $this->conn->prepare("$select WHERE userID = ".$this->kga['user']['userID']." ORDER BY timeEntryID DESC LIMIT 1");
      }

      $result = $pdo_query->execute(array($timeEntryID));

      if ($result == false) {
          $this->logLastError('timeSheet_get_data');
          return false;
      } else {
          $result_array = $pdo_query->fetch(PDO::FETCH_ASSOC);
          return $result_array;
      }
    }

    /**
    * delete time sheet entry
    *
    * @param integer $userID
    * @param integer $id -> ID of record
    * @global array  $this->kga kimai-global-array
    * @author th
    */
    public function timeEntry_delete($id) {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("DELETE FROM ${p}timeSheet WHERE `timeEntryID` = ? LIMIT 1;");
      $result = $pdo_query->execute(array($id));
      if ($result == false) {
          $this->logLastError('timeEntry_delete');
          return $result;
      }
    }

    /**
    * create time sheet entry
    *
    * @param integer $id    ID of record
    * @param integer $data  array with record data
    * @global array  $this->kga    kimai-global-array
    * @author th
    */
    public function timeEntry_create($data) {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("INSERT INTO ${p}timeSheet (
      `projectID`,
      `activityID`,
      `location`,
      `trackingNumber`,
      `description`,
      `comment`,
      `commentType`,
      `start`,
      `end`,
      `duration`,
      `userID`,
      `rate`,
      `cleared`,
      `budget`,
      `approved`,
      `statusID`,
      `billable`
      ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
      ;");

      $result = $pdo_query->execute(array(
      (int)$data['projectID'],
      (int)$data['activityID'] ,
      $data['location'],
      $data['trackingNumber']==''?null:$data['trackingNumber'],
      $data['description'],
      $data['comment'],
      (int)$data['commentType'] ,
      (int)$data['start'],
      (int)$data['end'],
      (int)$data['duration'],
      (int)$data['userID'],
      doubleval($data['rate']),
      $data['cleared']?1:0,
      doubleval($data['budget']),
      doubleval($data['approved']),
      (int)$data['statusID'],
      (int)$data['billable']
      ));

      if ($result === false) {
          $this->logLastError('timeEntry_create');
          return false;
      }
      else
        return $this->conn->lastInsertId();
    }

    /**
    * edit time sheet entry
    *
    * @param integer $id ID of record
    * @global array $this->kga kimai-global-array
    * @param array $data  array with new record data
    * @author th
    */
    public function timeEntry_edit($id, Array $data) {
      $p = $this->kga['server_prefix'];

      $original_array = $this->timeSheet_get_data($id);
      $new_array = array();

      foreach ($original_array as $key => $value) {
          if (isset($data[$key]) == true) {
              $new_array[$key] = $data[$key];
          } else {
              $new_array[$key] = $original_array[$key];
          }
      }

      $pdo_query = $this->conn->prepare("UPDATE ${p}timeSheet SET
      userID = ?,
      projectID = ?,
      activityID = ?,
      location = ?,
      trackingNumber = ?,
      description = ?,
      comment = ?,
      commentType = ?,
      start = ?,
      end = ?,
      duration = ?,
      rate = ?,
      cleared= ?,
      budget= ?,
      approved= ?,
      statusID= ?,
      billable= ?
      WHERE timeEntryID = ?;");

      $result = $pdo_query->execute(array(
      (int)$new_array['userID'],
      (int)$new_array['projectID'],
      (int)$new_array['activityID'] ,
      $new_array['location'],
      $new_array['trackingNumber']==''?null:$new_array['trackingNumber'],
      $new_array['description'],
      $new_array['comment'],
      (int)$new_array['commentType'] ,
      (int)$new_array['start'],
      (int)$new_array['end'],
      (int)$new_array['duration'],
      doubleval($new_array['rate']),
      (int)$new_array['cleared'],
      doubleval($new_array['budget']),
      doubleval($new_array['approved']),
      (int)$new_array['statusID'],
      (int)$new_array['billable'],
      $id
      ));

      if ($result == false) {
          $this->logLastError('timeEntry_edit');
          return $result;
      }
    }

    /**
    * saves timeframe of user in database (table conf)
    *
    * @param string $timeframeBegin unix seconds
    * @param string $timeframeEnd unix seconds
    * @param string $user ID of user
    *
    * @author th
    */
    public function save_timeframe($timeframeBegin,$timeframeEnd,$user) {
      $p = $this->kga['server_prefix'];

      if ($timeframeBegin == 0 && $timeframeEnd == 0) {
          $mon = date("n"); $day = date("j"); $Y = date("Y");
          $timeframeBegin  = mktime(0,0,0,$mon,$day,$Y);
          $timeframeEnd = mktime(23,59,59,$mon,$day,$Y);
      }

      if ($timeframeEnd == mktime(23,59,59,date('n'),date('j'),date('Y')))
        $timeframeEnd = 0;

      $pdo_query = $this->conn->prepare("UPDATE ${p}users SET timeframeBegin  = ? WHERE userID = ?;");
      $result = $pdo_query->execute(array($timeframeBegin ,$user));

      if ($result == false) {
          $this->logLastError('save_timeframe');
          return false;
      }

      $pdo_query = $this->conn->prepare("UPDATE ${p}users SET timeframeEnd = ? WHERE userID = ?;");
      $result = $pdo_query->execute(array($timeframeEnd ,$user));

      if ($result == false) {
          $this->logLastError('save_timeframe');
          return false;
      }
    }

    /**
    * returns list of projects for specific group as array
    *
    * @param integer $user ID of user in database
    * @global array $this->kga kimai-global-array
    * @return array
    * @author th
    */
    public function get_projects(array $groups = null) {
      $p = $this->kga['server_prefix'];

      $arr = array();

      if ($groups === null)
        $query = "SELECT project.*, customer.name AS customerName
                  FROM ${p}projects AS project
                  JOIN ${p}customers AS customer ON USING(customerID)
                  WHERE project.trash=0";
      else
        $query = "SELECT DISTINCT project.*, customer.name AS customerName
                  FROM ${p}projects AS project
                  JOIN ${p}customers AS customer USING(customerID)
                  JOIN ${p}groups_projects USING(projectID)
                  WHERE ${p}groups_projects.groupID IN (".implode($groups,',').")
                    AND project.trash=0";

      if ($this->kga['conf']['flip_project_display'])
        $query .= " ORDER BY project.visible DESC, customerName, name;";
      else
        $query .= " ORDER BY project.visible DESC, name, customerName;";

      $pdo_query = $this->conn->prepare($query);
      $result = $pdo_query->execute();

      if ($result == false) {
          $this->logLastError('get_projects');
          return false;
      }

      $i=0;
      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
          $arr[$i]['projectID']    = $row['projectID'];
          $arr[$i]['customerID']   = $row['customerID'];
          $arr[$i]['name']         = $row['name'];
          $arr[$i]['comment']      = $row['comment'];
          $arr[$i]['visible']      = $row['visible'];
          $arr[$i]['filter']       = $row['filter'];
          $arr[$i]['trash']        = $row['trash'];
          $arr[$i]['budget']       = $row['budget'];
          $arr[$i]['effort']       = $row['effort'];
          $arr[$i]['approved']     = $row['approved'];
          $arr[$i]['internal']     = $row['internal'];
          $arr[$i]['customerName'] = $row['customerName'];
          $i++;
      }
      return $arr;
    }

    /**
    * returns list of projects for specific group and specific customer as array
    *
    * @param integer $customerID   ID of the customer
    * @param array $groups group IDs
    * @global array $this->kga kimai-global-array
    * @return array
    * @author ob
    */
    public function get_projects_by_customer($customerID, array $groups = null) {
      $p = $this->kga['server_prefix'];

      $arr = array();

      if ($groups == "all" || $groups == null)
        $query = "SELECT project.*, customer.name AS customerName
                  FROM ${p}projects AS project
                  JOIN ${p}customers USING(customerID)
                  WHERE customerID = ? 
                    AND project.internal=0
                    AND project.trash=0";
      else
        $query = "SELECT DISTINCT project.*, customer.name AS customerName
                  FROM ${p}projects AS project
                  JOIN ${p}customers USING(customerID)
                  JOIN ${p}groups_projects USING(projectID)
                  WHERE groupID  IN (".implode($groups,',').")
                    AND ${p}projects.customerID = ?
                    AND project.internal=0
                    AND project.trash=0";

      if ($this->kga['conf']['flip_project_display'])
        $query .= " ORDER BY customerName, name;";
      else
        $query .= " ORDER BY name, customerName;";

      $pdo_query = $this->conn->prepare($query);
      $result = $pdo_query->execute(array($customerID));

      if ($result == false) {
          $this->logLastError('get_projects_by_customer');
          return false;
      }

      $i=0;
      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
          $arr[$i]['projectID']    = $row['projectID'];
          $arr[$i]['name']         = $row['name'];
          $arr[$i]['customerName'] = $row['customerName'];
          $arr[$i]['customerID']   = $row['customerID'];
          $arr[$i]['visible']      = $row['visible'];
          $arr[$i]['budget']       = $row['budget'];
          $i++;
      }
      return $arr;
    }

    /**
    *  Creates an array of clauses which can be joined together in the WHERE part
    *  of a sql query. The clauses describe whether a line should be included
    *  depending on the filters set.
    *
    *
    * @param Array list of IDs of users to include
    * @param Array list of IDs of customers to include
    * @param Array list of IDs of projects to include
    * @param Array list of IDs of activities to include
    * @return Array list of where clauses to include in the query
    *
    */
    public function timeSheet_whereClausesFromFilters($users, $customers , $projects , $activities ) {
      if (!is_array($users)) $users = array();
      if (!is_array($customers)) $customers = array();
      if (!is_array($projects)) $projects = array();
      if (!is_array($activities)) $activities = array();

      $whereClauses = array();

      if (count($users) > 0) {
        $whereClauses[] = "userID in (".implode(',',$users).")";
      }

      if (count($customers) > 0) {
        $whereClauses[] = "customerID in (".implode(',',$customers).")";
      }

      if (count($projects) > 0) {
        $whereClauses[] = "projectID in (".implode(',',$projects).")";
      }

      if (count($activities) > 0) {
        $whereClauses[] = "activityID in (".implode(',',$activities).")";
      }

      return $whereClauses;
    }

    /**
    * returns timesheet for specific user as multidimensional array
    *
    * TODO: Test it!
    *
    * @param integer $user ID of user in table users
    * @param integer $start start of timeframe in unix seconds
    * @param integer $end end of timeframe in unix seconds
    * @global array $this->kga kimai-global-array
    * @return array
    * @author th
    */
    public function get_timeSheet($start,$end,$users = null, $customers = null, $projects = null, $activities = null, $limit = false, $reverse_order = false, $filterCleared = null, $startRows = 0, $limitRows = 0, $countOnly = false) {
      $p = $this->kga['server_prefix'];

      if (!is_numeric($filterCleared)) {
        $filterCleared = $this->kga['conf']['hideClearedEntries']-1; // 0 gets -1 for disabled, 1 gets 0 for only not cleared entries
      }

      $whereClauses = $this->timeSheet_whereClausesFromFilters($users, $customers , $projects , $activities );

      if (isset($this->kga['customer']))
        $whereClauses[] = "project.internal = 0";

      if ($start)
        $whereClauses[]="(end > $start || end = 0)";
      if ($end)
        $whereClauses[]="start < $end";
      if ($filterCleared > -1)
        $whereClauses[] = "cleared = $filterCleared";

      if ($limit) {
      	  if(!empty($limitRows))
      	  {
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
		
      $select = "SELECT timeSheet.*, status.status, customer.name AS customerName, customer.customerID AS customerID, activity.name AS activityName,
                        project.name AS projectName, project.comment AS projectComment, user.name AS userName, user.alias AS userAlias ";
      if($countOnly)
      {
      	$select = "SELECT COUNT(*) AS total";
      	$limit = "";
      }
      
      $query = "$select
                FROM ${p}timeSheet AS timeSheet
                Join ${p}projects AS project USING (projectID)
                Join ${p}customers AS customer USING (customerID)
                Join ${p}users AS user USING(userID)
                Join ${p}statuses AS status USING(statusID)
                Join ${p}activities AS activity USING(activityID) "
                .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses).
              ' ORDER BY start '.($reverse_order?'ASC ':'DESC ') . $limit . ';';

      $pdo_query = $this->conn->prepare($query);

      $result = $pdo_query->execute();

      if ($result == false) {
          $this->logLastError('get_timeSheet');
          return false;
      }
		
      // return only number of rows
      if($countOnly)
      {
      	$row = $pdo_query->fetch(PDO::FETCH_ASSOC);
      	return $row->total;
      }
      
      
      $i=0;
      $arr=array();
      /* TODO: needs revision as foreach loop */
      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
          $arr[$i]['timeEntryID']           = $row['timeEntryID'];

          // Start time should not be less than the selected start time. This would confuse the user.
          if ($start && $row['start'] <= $start)  {
            $arr[$i]['start'] = $start;
          } else {
            $arr[$i]['start'] = $row['start'];
          }

          // End time should not be less than the selected start time. This would confuse the user.
          if ($end && $row['end'] >= $end)  {
            $arr[$i]['end'] = $end;
          } else {
            $arr[$i]['end'] = $row['end'];
          }

          if ($row['end'] != 0) {
            // only calculate time after recording is complete
            $arr[$i]['duration']         = $arr[$i]['end'] - $arr[$i]['start'];
            $arr[$i]['formattedDuration']     = Format::formatDuration($arr[$i]['duration']);
            $arr[$i]['wage_decimal']     = $arr[$i]['duration']/3600*$row['rate'];
            $arr[$i]['wage']             = sprintf("%01.2f",$arr[$i]['wage_decimal']);
          }


          $arr[$i]['rate']         = $row['rate'];
          $arr[$i]['projectID']        = $row['projectID'];
          $arr[$i]['activityID']        = $row['activityID'];
          $arr[$i]['userID']        = $row['userID'];
          $arr[$i]['projectID']           = $row['projectID'];
          $arr[$i]['customerName']         = $row['customerName'];
          // $arr[$i]['groupName']      = $row['groupName'];
          // $arr[$i]['pct_grpID']     = $row['pct_grpID'];
          $arr[$i]['customerID']        = $row['customerID'];
          $arr[$i]['activityName']         = $row['activityName'];
          $arr[$i]['projectName']         = $row['projectName'];
          $arr[$i]['projectComment']      = $row['projectComment'];
          $arr[$i]['location']     = $row['location'];
          $arr[$i]['trackingNumber']   = $row['trackingNumber'];
          $arr[$i]['budget']  	   = $row['budget'];
          $arr[$i]['approved']     = $row['approved'];
          $arr[$i]['statusID']     = $row['statusID'];
          $arr[$i]['status']       = $row['status'];
          $arr[$i]['billable']     = $row['billable'];
          $arr[$i]['description']  = $row['description'];
          $arr[$i]['comment']      = $row['comment'];
          $arr[$i]['cleared']      = $row['cleared'];
          $arr[$i]['commentType'] = $row['commentType'];
          $arr[$i]['userName']         = $row['userName'];
          $arr[$i]['userAlias']        = $row['userAlias'];
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
    * [userID] user ID,
    * [status] user status (rights),
    * [name] username
    * </pre>
    *
    * @param integer $user ID of user in table users
    * @global array $this->kga kimai-global-array
    * @return array
    * @author th
    */
    public function checkUserInternal($kimai_user)
    {
    $p = $this->kga['server_prefix'];
    if (strncmp($kimai_user, 'customer_', 4) == 0) {
        $data     = $pdo_query = $this->conn->prepare("SELECT customerID FROM ${p}customers WHERE name = ? AND NOT trash = '1';");
        $result   = $pdo_query->execute(array(substr($kimai_user,4)));

        if ($result == false) {
            $this->logLastError('checkUser');
            kickUser();
            return null;
        }

        $row      = $pdo_query->fetch(PDO::FETCH_ASSOC);
        $customerID   = $row['customerID'];
        if ($customerID < 1) {
            kickUser();
        }
    }
    else
    {
        $data     = $pdo_query = $this->conn->prepare("SELECT userID, status FROM ${p}users WHERE name = ? AND active = '1' AND NOT trash = '1';");
        $result   = $pdo_query->execute(array($kimai_user));

        if ($result == false) {
            $this->logLastError('checkUser');
            kickUser();
            return null;
        }

        $row      = $pdo_query->fetch(PDO::FETCH_ASSOC);
        $userID   = $row['userID'];
        $status  = $row['status']; // User Status -> 0=Admin | 1=GroupLeader | 2=User
        $name = $kimai_user;
        if ($userID < 1) {
            kickUser();
        }
    }

    // load configuration
    $this->get_global_config();
    if (strncmp($kimai_user, 'customer_', 4) == 0) {
        $this->get_customer_config($customerID);
    } else {
        // get_customer_config
        $this->get_user_config($userID);
    }

    // override default language if user has chosen a language in the prefs
    if ($this->kga['conf']['lang'] != "") {
    $this->kga['language'] = $this->kga['conf']['lang'];
    $this->kga['lang'] = array_replace_recursive($this->kga['lang'],include(WEBROOT.'language/'.$this->kga['language'].'.php'));
    }

    return (isset($this->kga['user'])?$this->kga['user']:null);
    }

    /**
    * Write global configuration into $this->kga including defaults for user settings.
    *
    * @param integer $user ID of user in table users
    * @global array $this->kga kimai-global-array
    * @return array $this->kga
    * @author th
    */
    public function get_global_config() {
      $p = $this->kga['server_prefix'];

    // get values from global configuration
    $pdo_query = $this->conn->prepare("SELECT * FROM ${p}configuration;");
    $result = $pdo_query->execute();

    if ($result == false) {
        $this->logLastError('get_global_config');
        return;
    }

    $row  = $pdo_query->fetch(PDO::FETCH_ASSOC);

    do {
        $this->kga['conf'][$row['option']] = $row['value'];
    } while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC));

    $this->kga['conf']['rowlimit'] = 100;
    $this->kga['conf']['skin'] = 'standard';
    $this->kga['conf']['autoselection'] = 1;
    $this->kga['conf']['quickdelete'] = 0;
    $this->kga['conf']['flip_project_display'] = 0;
    $this->kga['conf']['project_comment_flag'] = 0;
    $this->kga['conf']['showIDs'] = 0;
    $this->kga['conf']['noFading'] = 0;
    $this->kga['conf']['lang'] = '';
    $this->kga['conf']['user_list_hidden'] = 0;
    $this->kga['conf']['hideClearedEntries'] = 0;


    // get status values
    $pdo_query = $this->conn->prepare("SELECT * FROM ${p}statuses;");
    $result = $pdo_query->execute();

    if ($result == false) {
        $this->logLastError('get_global_config');
        return;
    }

    $row  = $pdo_query->fetch(PDO::FETCH_ASSOC);

    do {
        $this->kga['conf']['status'][$row['statusID']] = $row['status'];
    } while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC));
    }

    /**
    * Returns a username for the given $apikey.
    *
    * @param string $apikey
    * @return string|null
    */
    public function getUserByApiKey($apikey)
    {
        $p = $this->kga['server_prefix'];

        if (!$apikey || strlen(trim($apikey)) == 0) {
          return null;
        }

        // get values from user record
        $pdo_query = $this->conn->prepare("SELECT `userID`, `name` FROM ${p}users WHERE `apikey` = ? AND NOT trash = '1';");

        $result = $pdo_query->execute(array($apikey));

        if ($result == false) {
            $this->logLastError('getUserByApiKey');
            return null;
        }

        $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
        return $row['name'];
    }

    /**
    * write details of a specific user into $this->kga
    *
    * @param integer $user ID of user in table users
    * @global array $this->kga kimai-global-array
    * @return array $this->kga
    * @author th
    */
    public function get_user_config($user) {
    $p = $this->kga['server_prefix'];

    if (!$user)
      return;

    // get values from user record
    $pdo_query = $this->conn->prepare("SELECT
    `userID`,
    `name`,
    `status`,
    `trash`,
    `active`,
    `mail`,
    `password`,
    `ban`,
    `banTime`,
    `secure`,

    `lastProject`,
    `lastActivity`,
    `lastRecord`,
    `timeframeBegin`,
    `timeframeEnd`,
    `apikey`

    FROM ${p}users WHERE userID = ?;");

    $result = $pdo_query->execute(array($user));

    if ($result == false) {
        $this->logLastError('get_user_config');
        return;
    }

    $row  = $pdo_query->fetch(PDO::FETCH_ASSOC);
    foreach( $row as $key => $value) {
        $this->kga['user'][$key] = $value;
    }

    $this->kga['user']['groups'] = $this->getGroupMemberships($user);

    $this->kga['conf'] = array_merge($this->kga['conf'],$this->user_get_preferences_by_prefix('ui.',$this->kga['user']['userID']));
    $userTimezone = $this->user_get_preference('timezone');
    if ($userTimezone != '')
      $this->kga['timezone'] = $userTimezone;
    else
      $this->kga['timezone'] = $this->kga['defaultTimezone'];

    date_default_timezone_set($this->kga['timezone']);
    }

    /**
    * write details of a specific customer into $this->kga
    *
    * @param integer $user ID of user in table users
    * @global array $this->kga kimai-global-array
    * @return array $this->kga
    * @author sl
    */
    public function get_customer_config($customer_ID) {
    $p = $this->kga['server_prefix'];


    // get values from customer record
    $pdo_query = $this->conn->prepare("SELECT * FROM ${p}customers WHERE customerID = ?;");
    $result = $pdo_query->execute(array($customer_ID));

    if ($result == false) {
        $this->logLastError('get_customer_config');
        return;
    }

    $row  = $pdo_query->fetch(PDO::FETCH_ASSOC);
    foreach( $row as $key => $value) {
        $this->kga['customer'][$key] = $value;
    }

    date_default_timezone_set($this->kga['customer']['timezone']);
    }

    /**
    * checks if a customer with this name exists
    *
    * @param string name
    * @global array $this->kga kimai-global-array
    * @return integer
    * @author sl
    */
    public function is_customer_name($name) {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT customerID FROM ${p}customers WHERE name = ?");
      $result = $pdo_query->execute(array($name));

      if ($result == false) {
          $this->logLastError('is_customer_name');
          return false;
      }

      return $pdo_query->rowCount() == 1;
    }

    /**
    * returns time summary of current timesheet
    *
    * @param integer $user ID of user in table users
    * @param integer $start start of timeframe in unix seconds
    * @param integer $end end of timeframe in unix seconds
    * @global array $this->kga kimai-global-array
    * @return integer
    * @author th
    */
    // correct syntax - but doesn't work with all PDO versions because of a bug
    // reported here: http://pecl.php.net/bugs/bug.php?id=8045
    // public function get_duration($user,$start,$end) {
    //     global $this->kga;
    //     global $this->conn;
    //     $pdo_query = $this->conn->prepare("SELECT SUM(`duration`) AS zeit FROM " . $this->kga['server_prefix'] . "timeSheet WHERE userID = ? AND start > ? AND end < ? LIMIT ?;");
    //     $pdo_query->execute(array($user,$start,$end,$this->kga['conf']['rowlimit']));
    //     $data = $pdo_query->fetch(PDO::FETCH_ASSOC);
    //     $zeit = $data['zeit'];
    //     return $zeit;
    // }
    // th: solving this by doing a loop and add the seconds manually...
    //     btw - using the rowlimit is not correct here because we want the time for the timeframe, not for the rows in the timesheet ... my fault
    public function get_duration($start,$end,$users = null, $customers = null, $projects = null, $activities = null, $filterCleared = null) {
      $p = $this->kga['server_prefix'];

      if (!is_numeric($filterCleared)) {
        $filterCleared = $this->kga['conf']['hideClearedEntries']-1; // 0 gets -1 for disabled, 1 gets 0 for only not cleared entries
      }

      $whereClauses = $this->timeSheet_whereClausesFromFilters($users,$customers,$projects,$activities);

      if ($start)
        $whereClauses[]="end > $start";
      if ($end)
        $whereClauses[]="start < $end";
      if ($filterCleared > -1)
        $whereClauses[] = "cleared = $filterCleared";

      $pdo_query = $this->conn->prepare("SELECT start, end, duration FROM ${p}timeSheet
              Join ${p}projects USING(projectID)
              Join ${p}customers USING(customerID)
              Join ${p}users USING(userID)
              Join ${p}activities USING(activityID) "
              .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses));
      $result = $pdo_query->execute();

      if ($result == false) {
          $this->logLastError('get_duration');
          return null;
      }

      $sum = 0;
      $consideredStart = 0;
      $consideredEnd = 0;
      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if ($row['start'] <= $start && $row['end'] < $end)  {
          $consideredStart  = $start;
          $consideredEnd = $row['end'];
        }
        else if ($row['start'] <= $start && $row['end'] >= $end)  {
          $consideredStart  = $start;
          $consideredEnd = $end;
        }
        else if ($row['start'] > $start && $row['end'] < $end)  {
          $consideredStart  = $row['start'];
          $consideredEnd = $row['end'];
        }
        else if ($row['start'] > $start && $row['end'] >= $end)  {
          $consideredStart  = $row['start'];
          $consideredEnd = $end;
        }
        $sum+=(int)($consideredEnd - $consideredStart);
      }
      return $sum;
    }

    // TODO: check if this public function is redundant!!!
    // ob: no it isn't :-)
    // th: sorry for the 3 '!' ... this was an order to myself, i'm sometimes a little rude to myself :D
    /**
    * returns list of customers in a group as array
    *
    * @param integer $group ID of group in table groups or "all" for all groups
    * @global array $this->kga kimai-global-array
    * @return array
    * @author th
    */
    public function get_customers(array $groups = null) {
      $p = $this->kga['server_prefix'];

      $arr = array();
      if ($groups === null) {
          $pdo_query = $this->conn->prepare("SELECT customerID, name, contact, visible
              FROM ${p}customers
              WHERE trash=0
              ORDER BY visible DESC, name;");
          $result = $pdo_query->execute();
      } else {
          $pdo_query = $this->conn->prepare("SELECT DISTINCT customerID, name, contact, visible
              FROM ${p}customers AS customer
              JOIN ${p}groups_customers AS g_c USING(customerID)
              WHERE g_c.groupID IN (".implode($groups,',').")
                AND trash=0
              ORDER BY visible DESC, name;");
          $result = $pdo_query->execute();
      }

      if ($result == false) {
          $this->logLastError('get_customers');
          return null;
      }

      $i=0;
      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
          $arr[$i]['customerID'] = $row['customerID'];
          $arr[$i]['name']       = $row['name'];
          $arr[$i]['contact']    = $row['contact'];
          $arr[$i]['visible']    = $row['visible'];
          $i++;
      }

      return $arr;
    }

    /**
    * returns list of users the given user can watch
    *
    * @param integer $user ID of user in table users
    * @global array $this->kga kimai-global-array
    * @return array
    * @author sl
    */
    public function get_watchable_users($user) {
      $p = $this->kga['server_prefix'];

      $arr = array();

      if ($user['status'] == "0") { // if is admin
        $pdo_query = $this->conn->prepare("SELECT * FROM ${p}users WHERE trash=0 ORDER BY name");
        $result = $pdo_query->execute();

        $arr = array();
        $i=0;
        while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
            $arr[$i]['userID']   = $row['userID'];
            $arr[$i]['name'] = $row['name'];
            $i++;
        }
        return $arr;
      }

      $pdo_query = $this->conn->prepare("SELECT groupID FROM " . $this->kga['server_prefix'] . "groupleaders WHERE userID=?");
      $success = $pdo_query->execute(array($user['userID']));

      if (!$success) {
        $this->logLastError('get_watchable_users');
        return array();
      }

      $leadingGroups = array();
      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC))
        $leadingGroups[] = $row['groupID'];

      return $this->get_users(0,$leadingGroups);
    }

    /**
    * returns assoc. array where the index is the ID of a user and the value the time
    * this user has accumulated in the given time with respect to the filtersettings
    *
    * @param integer $start from this timestamp
    * @param integer $end to this  timestamp
    * @param integer $user ID of user in table users
    * @param integer $customer ID of customer in table customers
    * @param integer $project ID of project in table projects
    * @global array $this->kga kimai-global-array
    * @return array
    * @author sl
    */
    public function get_time_users($start,$end,$users = null, $customers = null, $projects = null,$activities = null) {
      $p = $this->kga['server_prefix'];

      $whereClauses = $this->timeSheet_whereClausesFromFilters($users,$customers,$projects,$activities);
      $whereClauses[] = "${p}users.trash=0";

      if ($start)
        $whereClauses[]="end > $start";
      if ($end)
        $whereClauses[]="start < $end";


      $pdo_query = $this->conn->prepare("SELECT start,end, userID, (end - start) / 3600 * rate AS costs
              FROM ${p}timeSheet
              Join ${p}projects USING(projectID)
              Join ${p}customers USING(customerID)
              Join ${p}users USING(userID)
              Join ${p}activities USING(activityID) "
              .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses). " ORDER BY start DESC;");

      $result = $pdo_query->execute();

      if ($result == false) {
          $this->logLastError('get_time_users');
          return array();
      }

      $arr = array();
      $consideredStart = 0;
      $consideredEnd = 0;
      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if ($row['start'] <= $start && $row['end'] < $end)  {
          $consideredStart  = $start;
          $consideredEnd = $row['end'];
        }
        else if ($row['start'] <= $start && $row['end'] >= $end)  {
          $consideredStart  = $start;
          $consideredEnd = $end;
        }
        else if ($row['start'] > $start && $row['end'] < $end)  {
          $consideredStart  = $row['start'];
          $consideredEnd = $row['end'];
        }
        else if ($row['start'] > $start && $row['end'] >= $end)  {
          $consideredStart  = $row['start'];
          $consideredEnd = $end;
        }

        if (isset($arr[$row['userID']])) {
          $arr[$row['userID']]['time']  += (int)($consideredEnd - $consideredStart);
          $arr[$row['userID']]['costs'] += doubleval($row['costs']);
        }
        else  {
          $arr[$row['userID']]['time']  = (int)($consideredEnd - $consideredStart);
          $arr[$row['userID']]['costs'] = doubleval($row['costs']);
        }
      }

      return $arr;
    }

    /**
    * returns list of time summary attached to customer ID's within specific timeframe as array
    * !! becomes obsolete with new querys !!
    *
    * @param integer $start start of timeframe in unix seconds
    * @param integer $end end of timeframe in unix seconds
    * @param integer $user filter for only this ID of auser
    * @param integer $customer filter for only this ID of a customer
    * @param integer $project filter for only this ID of a project
    * @global array $this->kga kimai-global-array
    * @return array
    * @author sl
    */
    public function get_time_customers($start,$end,$users = null, $customers = null, $projects = null, $activities = null) {
      $p = $this->kga['server_prefix'];

      $whereClauses = $this->timeSheet_whereClausesFromFilters($users,$customers,$projects,$activities);
      $whereClauses[] = "${p}customers.trash=0";

      if ($start)
        $whereClauses[]="end > $start";
      if ($end)
        $whereClauses[]="start < $end";

      $pdo_query = $this->conn->prepare("SELECT start,end, customerID, (end - start) / 3600 * rate AS costs
              FROM ${p}timeSheet
              Left Join ${p}projects USING(projectID)
              Left Join ${p}customers USING(customerID) "
              .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses));
      $result = $pdo_query->execute();

      if ($result == false) {
          $this->logLastError('get_time_customers');
          return array();
      }

      $arr = array();
      $consideredStart = 0;
      $consideredEnd = 0;
      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if ($row['start'] <= $start && $row['end'] < $end)  {
          $consideredStart  = $start;
          $consideredEnd = $row['end'];
        }
        else if ($row['start'] <= $start && $row['end'] >= $end)  {
          $consideredStart  = $start;
          $consideredEnd = $end;
        }
        else if ($row['start'] > $start && $row['end'] < $end)  {
          $consideredStart  = $row['start'];
          $consideredEnd = $row['end'];
        }
        else if ($row['start'] > $start && $row['end'] >= $end)  {
          $consideredStart  = $row['start'];
          $consideredEnd = $end;
        }

        if (isset($arr[$row['customerID']])) {
          $arr[$row['customerID']]['time']  += (int)($consideredEnd - $consideredStart);
          $arr[$row['customerID']]['costs'] += doubleval($row['costs']);
        }
        else {
          $arr[$row['customerID']]['time']  = (int)($consideredEnd - $consideredStart);
          $arr[$row['customerID']]['costs'] = doubleval($row['costs']);
        }
      }

      return $arr;
    }


    /**
    * Read activity budgets
    *
    * @author mo
    */
    public function get_activity_budget($projectID,$activityID) {
    // validate input
    if ($projectID == NULL || !is_numeric($projectID)) $projectID = "NULL";
    if ($activityID == NULL || !is_numeric($activityID)) $activityID = "NULL";


    $pdo_query = $this->conn->prepare("SELECT budget, approved, effort FROM " . $this->kga['server_prefix'] . "projects_activities WHERE ".
    (($projectID=="NULL")?"projectID is NULL":"projectID = $projectID"). " AND ".
    (($activityID=="NULL")?"activityID is NULL":"activityID = $activityID"));

    $result = $pdo_query->execute();

    if ($result == false) {
        $this->logLastError('get_activity_budget');
        return array();
    }
    $data = $pdo_query->fetch(PDO::FETCH_ASSOC);
    $timeSheet = $this->get_timeSheet(0, time(), null, null, array($projectID), array($activityID));
    foreach($timeSheet as $timeSheetEntry) {
        $data['budget']+= $timeSheetEntry['budget'];
        $data['approved']+= $timeSheetEntry['approved'];
    }
    return $data;
    }

    /**
    *
    * get the whole budget used for the activity
    * @param integer $projectID
    * @param integer $activityID
    */
    public function get_budget_used($projectID,$activityID) {
    $timeSheet = $this->get_timeSheet(0, time(), null, null, array($projectID), array($activityID));
    $budgetUsed = 0;
    if(is_array($timeSheet) && count($timeSheet) > 0) {
        foreach($timeSheet as $timeSheetEntry) {
            $budgetUsed+= $timeSheetEntry['wage_decimal'];
        }
    }
    return $budgetUsed;
    }


    /**
    * returns list of time summary attached to project ID's within specific timeframe as array
    * !! becomes obsolete with new querys !!
    *
    * @param integer $start start time in unix seconds
    * @param integer $end end time in unix seconds
    * @param integer $user filter for only this ID of auser
    * @param integer $customer filter for only this ID of a customer
    * @param integer $project filter for only this ID of a project
    * @global array $this->kga kimai-global-array
    * @return array
    * @author sl
    */
    public function get_time_projects($start,$end,$users = null,$customers = null, $projects = null, $activities = null) {
      $p = $this->kga['server_prefix'];

      $whereClauses = $this->timeSheet_whereClausesFromFilters($users,$customers,$projects,$activities);
      $whereClauses[] = "${p}projects.trash=0";

      if ($start)
        $whereClauses[]="end > $start";
      if ($end)
        $whereClauses[]="start < $end";
      $arr = array();
      $pdo_query = $this->conn->prepare("SELECT start,end,projectID, (end - start) / 3600 * rate AS costs
          FROM ${p}timeSheet
          Left Join ${p}projects USING (projectID)
          Left Join ${p}customers USING(customerID) "
          .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses));
      $result = $pdo_query->execute();

      if ($result == false) {
          $this->logLastError('get_time_projects');
          return array();
      }

      $arr = array();
      $consideredStart = 0;
      $consideredEnd = 0;
      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if ($row['start'] <= $start && $row['end'] < $end)  {
          $consideredStart  = $start;
          $consideredEnd = $row['end'];
        }
        else if ($row['start'] <= $start && $row['end'] >= $end)  {
          $consideredStart  = $start;
          $consideredEnd = $end;
        }
        else if ($row['start'] > $start && $row['end'] < $end)  {
          $consideredStart  = $row['start'];
          $consideredEnd = $row['end'];
        }
        else if ($row['start'] > $start && $row['end'] >= $end)  {
          $consideredStart  = $row['start'];
          $consideredEnd = $end;
        }

        if (isset($arr[$row['projectID']])) {
          $arr[$row['projectID']]['time']  += (int)($consideredEnd - $consideredStart);
          $arr[$row['projectID']]['costs'] += doubleval($row['costs']);
        }
        else {
          $arr[$row['projectID']]['time']  = (int)($consideredEnd - $consideredStart);
          $arr[$row['projectID']]['costs'] = doubleval($row['costs']);
        }
      }
      return $arr;
    }

    ## Load into Array: Activities
    public function get_activities(array $groups = null) {
      $p = $this->kga['server_prefix'];

      $arr = array();
      if ($groups === null) {
          $pdo_query = $this->conn->prepare("SELECT activityID, name, visible
              FROM ${p}activities
              WHERE trash=0
              ORDER BY visible DESC, name;");
      } else {
          $pdo_query = $this->conn->prepare("SELECT DISTINCT activityID, name, visible
            FROM ${p}activities
            JOIN ${p}groups_activities AS g_a USING(activityID)
            WHERE g_a.groupID IN (".implode($groups,',').")
              AND trash=0
            ORDER BY visible DESC, name;");
      }

      $result = $pdo_query->execute();

      if ($result == false) {
          $this->logLastError('get_activities');
          return array();
      }

      $i=0;
      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
          $arr[$i]['activityID'] = $row['activityID'];
          $arr[$i]['name'] = $row['name'];
          $arr[$i]['visible'] = $row['visible'];
          $i++;
      }

      return $arr;
    }

    ## Load into Array: Activities
    public function get_activities_by_project($projectID, array $groups = null) {
      $p = $this->kga['server_prefix'];

      $arr = array();
      if ($groups === null) {
          $pdo_query = $this->conn->prepare("SELECT activity.*, p_a.budget, p_a.approved, p_a.effort
            FROM ${p}activities AS activity
            LEFT JOIN ${p}projects_activities p_a USING(activityID)
            WHERE trash=0
              AND (projectID = ? OR projectID IS NULL)
            ORDER BY visible DESC, name;");
      } else {
          $pdo_query = $this->conn->prepare("SELECT DISTINCT activity.*, p_a.budget, p_a.approved, p_a.effort
            FROM ${p}activities AS activity
            JOIN ${p}groups_activities USING(activityID)
            LEFT JOIN ${p}projects_activities p_a USING(activityID)
            WHERE `${p}groups_activities`.`groupID` IN (".implode($groups,',').")
              AND trash=0
              AND (projectID = ? OR projectID IS NULL)
            ORDER BY visible DESC, name;");
      }

      $result = $pdo_query->execute(array($projectID));

      if ($result == false) {
          $this->logLastError('get_activities_by_project');
          return array();
      }

      $i=0;
      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
          $arr[$i]['activityID'] = $row['activityID'];
          $arr[$i]['name'] = $row['name'];
          $arr[$i]['visible'] = $row['visible'];
          $i++;
      }

      return $arr;
    }

    /**
    * returns list of activities used with specified customer
    *
    * @param integer $customer filter for only this ID of a customer
    * @global array $this->kga kimai-global-array
    * @global array $this->conn PDO connection
    * @return array
    * @author sl
    */
    public function get_activities_by_customer($customer_ID) {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT DISTINCT activityID, name, visible
          FROM ${p}activities
          WHERE activityID IN
              (SELECT activityID FROM ${p}timeSheet
                WHERE projectID IN (SELECT projectID FROM ${p}projects WHERE customerID = ?))
            AND trash=0");
      $result = $pdo_query->execute(array($customer_ID));

      if ($result == false) {
          $this->logLastError('get_activities_by_customer');
          return array();
      }

      $arr=array();
      $i=0;
      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
          $arr[$i]['activityID'] = $row['activityID'];
          $arr[$i]['name'] = $row['name'];
          $arr[$i]['visible'] = $row['visible'];
          $i++;
      }

      return $arr;
    }

    /**
    * returns list of time summary attached to activity ID's within specific timeframe as array
    *
    * @param integer $start start time in unix seconds
    * @param integer $end end time in unix seconds
    * @param integer $user filter for only this ID of auser
    * @param integer $customer filter for only this ID of a customer
    * @param integer $project filter for only this ID of a project
    * @global array $this->kga kimai-global-array
    * @return array
    * @author sl
    */
    public function get_time_activities($start,$end,$users = null,$customers = null,$projects = null, $activities = null) {
      $p = $this->kga['server_prefix'];

      $whereClauses = $this->timeSheet_whereClausesFromFilters($users,$customers,$projects,$activities);
      $whereClauses[] = "${p}activities.trash = 0";

      if ($start)
        $whereClauses[]="end > $start";
      if ($end)
        $whereClauses[]="start < $end";
      $pdo_query = $this->conn->prepare("SELECT start, end, activityID, (end - start) / 3600 * rate AS costs
          FROM ${p}timeSheet
          Left Join ${p}activities USING(activityID)
          Left Join ${p}projects USING(projectID)
          Left Join ${p}customers USING(customerID) "
          .(count($whereClauses)>0?" WHERE ":" ").implode(" AND ",$whereClauses));
      $result = $pdo_query->execute();

      if ($result == false) {
          $this->logLastError('get_time_activities');
          return array();
      }

      $arr = array();
      $consideredStart = 0;
      $consideredEnd = 0;
      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        if ($row['start'] <= $start && $row['end'] < $end)  {
          $consideredStart = $start;
          $consideredEnd = $row['end'];
        }
        else if ($row['start'] <= $start && $row['end'] >= $end)  {
          $consideredStart  = $start;
          $consideredEnd = $end;
        }
        else if ($row['start'] > $start && $row['end'] < $end)  {
          $consideredStart  = $row['start'];
          $consideredEnd = $row['end'];
        }
        else if ($row['start'] > $start && $row['end'] >= $end)  {
          $consideredStart  = $row['start'];
          $consideredEnd = $end;
        }

        if (isset($arr[$row['activityID']])) {
          $arr[$row['activityID']]['time']  += (int)($consideredEnd - $consideredStart);
          $arr[$row['activityID']]['costs'] += doubleval($row['costs']);
        }
        else {
          $arr[$row['activityID']]['time'] = (int)($consideredEnd - $consideredStart);
          $arr[$row['activityID']]['costs'] = doubleval($row['costs']);
        }
      }
      return $arr;
    }

    /**
    * returns time of currently running activity recording as array
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
    * @param integer $user ID of user in table users
    * @global array $this->kga kimai-global-array
    * @return array
    * @author th
    */
    public function get_current_timer() {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT timeEntryID, start FROM ${p}timeSheet WHERE userID = ? AND end = 0;");
      $result = $pdo_query->execute(array($this->kga['user']['userID']));

      if ($result == false) {
          $this->logLastError('get_current_timer');
          return false;
      }

      if ($pdo_query->rowCount()) {
          $current_timer['all']  = 0;
          $current_timer['hour'] = 0;
          $current_timer['min']  = 0;
          $current_timer['sec']  = 0;
      }
      else {
        $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
        $start    = (int)$row['start'];

        $aktuelleMessung = Format::hourminsec(time()-$start);
        $current_timer['all']  = $start;
        $current_timer['hour'] = $aktuelleMessung['h'];
        $current_timer['min']  = $aktuelleMessung['i'];
        $current_timer['sec']  = $aktuelleMessung['s'];
      }

      return $current_timer;
    }

    /**
    * returns the version of the installed Kimai database (not of the software)
    *
    * @param string $path path to admin dir relative to the document that calls this public function (usually "." or "..")
    * @global array $this->kga kimai-global-array
    * @return array
    * @author th
    *
    * [0] => version number (x.x.x)
    * [1] => svn revision number
    *
    */
    public function get_DBversion() {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT value FROM ${p}configuration WHERE `option` = 'version';");
      $result = $pdo_query->execute(array());

      if ($result == false) {
        // before database revision 1369 (503 + 866)
        $pdo_query = $this->conn->prepare("SELECT value FROM ${p}var WHERE var = 'version';");
        $result = $pdo_query->execute(array());
      }

      $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
      $return[0]   = $row['value'];

      if (!is_array($row)) $return[0] = "0.5.1";

      $pdo_query = $this->conn->prepare("SELECT value FROM ${p}configuration WHERE `option` = 'revision';");
      $result = $pdo_query->execute(array());

      if ($result == false) {
        // before database revision 1369 (503 + 866)
        $pdo_query = $this->conn->prepare("SELECT value FROM ${p}var WHERE var = 'revision';");
        $result = $pdo_query->execute(array());
      }

      $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
      $return[1]   = $row['value'];

      return $return;
    }

    /**
    * returns the key for the session of a specific user
    *
    * the key is both stored in the database (users table) and a cookie on the client.
    * when the keys match the user is allowed to access the Kimai GUI.
    * match test is performed via public function userCheck()
    *
    * @param integer $user ID of user in table users
    * @global array $this->kga kimai-global-array
    * @return string
    * @author th
    */
    public function get_seq($user) {
      $p = $this->kga['server_prefix'];

      if (strncmp($user, 'customer_', 4) == 0) {
        $pdo_query = $this->conn->prepare("SELECT secure FROM ${p}customers WHERE name = ?;");
        $result = $pdo_query->execute(array(substr($user,4)));

        if ($result == false) {
            $this->logLastError('get_seq');
            return false;
        }

        $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
        $seq         = $row['secure'];
      }
      else {
        $pdo_query = $this->conn->prepare("SELECT secure FROM ${p}users WHERE name = ?;");
        $result = $pdo_query->execute(array($user));

        if ($result == false) {
            $this->logLastError('get_seq');
            return false;
        }

        $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
        $seq         = $row['secure'];
      }

      return $seq;
    }

    /**
    * return status names
    * @param integer $statusIds
    */
    public function get_status($statusIds) {
      $p = $this->kga['server_prefix'];
      $statusIds = implode(',', $statusIds);
      $pdo_query = $this->conn->prepare("SELECT status FROM ${p}statuses where statusID in ( $statusIds ) order by statusID");

      $result = $pdo_query->execute();
      if ($result == false) {
          $this->logLastError('get_status');
          return false;
      }

      $res = array();
      while($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
        $res[] = $row['status'];
      }
      return $res;
    }


    /**
    * Returns the number of time sheet entries with a certain status
    *
    * @param integer $statusID   statusID of the status
    * @return int            		the number of time sheet entries with this status
    * @author mo
    */
    public function status_timeSheetEntryCount($statusID) {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT COUNT(*) FROM ${p}timeSheet WHERE statusID = ?");
      $result = $pdo_query->execute(array($statusID));

      if ($result == false) {
          $this->logLastError('status_timeSheetEntryCount');
          return false;
      } else {
          $result_array = $pdo_query->fetch();
          return $result_array[0];
      }
    }


    /**
    * returns array of all status with the status id as key
    *
    * @return array
    * @author mo
    */
    public function get_statuses() {
      $p = $this->kga['server_prefix'];

      $query = "SELECT * FROM ${p}status ORDER BY status;";
      $pdo_query = $this->conn->prepare($query);
      $result = $pdo_query->execute();

      if ($result == false) {
          $this->logLastError('getStatus');
          return array();
      }
      $i = 0;
      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
          $arr[] = $row;
          $arr[$i]['timeSheetEntryCount'] = $this->status_timeSheetEntryCount($row['statusID']);
          $i++;
      }
      return $arr;
    }

    /**
    * add a new status
    * @param array $statusArray
    */
    public function status_create($status) {
      $p = $this->kga['server_prefix'];
          $pdo_query = $this->conn->prepare("INSERT INTO ${p}statuses (status) VALUES (?);");
          $result = $pdo_query->execute(array(trim($status)));

          if (! $result) {
            $this->logLastError('add_status');
            return false;
          }
      return $this->conn->lastInsertId();
    }

    /**
    * returns array of all users
    *
    * [userID] => 23103741
    * [name] => admin
    * [status] => 0
    * [mail] => 0
    * [active] => 0
    *
    *
    * @global array $this->kga kimai-global-array
    * @param array $groups list of group ids the users must be a member of
    * @return array
    * @author th
    */
    public function get_users($trash=0,array $groups = null) {
      $p = $this->kga['server_prefix'];

      $arr = array();

      if ($groups === null)
        $query = "SELECT * FROM ${p}users
        WHERE trash = ?
        ORDER BY name ;";
      else
        $query = "SELECT * FROM ${p}users
         JOIN ${p}groups_users AS g_u USING(userID)
        WHERE g_u.groupID IN (".implode($groups,',').") AND
         trash = ?
        ORDER BY name ;";

      $pdo_query = $this->conn->prepare($query);
      $result = $pdo_query->execute(array($trash));

      if ($result == false) {
          $this->logLastError('get_users');
          return array();
      }

      $i=0;
      while ($row = $pdo_query->fetch()) {
          $arr[$i]['userID']   = $row['userID'];
          $arr[$i]['name'] = $row['name'];
          $arr[$i]['status']  = $row['status'];
          $arr[$i]['mail'] = $row['mail'];
          $arr[$i]['active'] = $row['active'];
          $arr[$i]['trash'] = $row['trash'];
          if ($row['password']!=''&&$row['password']!='0') {
              $arr[$i]['passwordSet'] = "yes";
          } else {
              $arr[$i]['passwordSet'] = "no";
          }
          $i++;
      }
      return $arr;
    }

    /**
    * returns array of all groups
    *
    * [0]=>  array(6) {
    *     ["groupID"]=>  string(1) "1"
    *      ["groupName"]=>  string(5) "admin"
    *      ["userID"]=>  string(9) "1234"
    *      ["trash"]=>  string(1) "0"
    *      ["count_users"]=>  string(1) "2"
    *      ["leader_name"]=>  string(5) "user1"
    * }
    *
    * [1]=>  array(6) {
    *      ["groupID"]=>  string(1) "2"
    *      ["groupName"]=>  string(4) "Test"
    *      ["userID"]=>  string(9) "12345"
    *      ["trash"]=>  string(1) "0"
    *      ["count_users"]=>  string(1) "1"
    *      ["leader_name"]=>  string(7) "user2"
    *  }
    *
    * @global array $this->kga kimai-global-array
    * @return array
    * @author th
    */
    public function get_groups($trash=0) {
      $p = $this->kga['server_prefix'];

      // Lock tables
      $pdo_query_l = $this->conn->prepare("LOCK TABLE
      ${p}users READ,
      ${p}groups READ,
      ${p}groupleaders READ
      ");
      $result_l = $pdo_query_l->execute();

      if ($result_l == false) {
          $this->logLastError('get_groups');
          return array();
      }

      if (!$trash) {
          $trashoption = "WHERE ${p}groups.trash !=1";
      }
      $pdo_query = $this->conn->prepare(sprintf("SELECT * FROM ${p}groups %s ORDER BY name;",$trashoption));
      $result = $pdo_query->execute();

      if ($result == false) {
          $this->logLastError('get_groups');
          return array();
      }

      // rows into array
      $groups = array();
      $i=0;
      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)){
          $groups[] = $row;

          // append user count
        $groups[$i]['count_users'] = $this->group_count_users($row['groupID']);

          // append leader array
          $userID_array = $this->group_get_groupleaders($row['groupID']);
          $j = 0;
          $leaderNames = array();
          foreach ($userID_array as $userID) {
            $leaderNames[$j] = $this->userIDToName($userID);
            $j++;
          }

          $groups[$i]['leader_name'] = $leaderNames;

          $i++;
      }

      // Unlock tables
      $pdo_query_ul = $this->conn->prepare("UNLOCK TABLES");
      $result_ul = $pdo_query_ul->execute();

      if ($result_ul == false) {
          $this->logLastError('get_groups');
          return array();
      }

      // error_log("get_groups: " . serialize($groups));

      return $groups;
    }

    /**
    * returns array of all groups
    *
    * [0]=>  array(6) {
    *     ["groupID"]=>  string(1) "1"
    *      ["groupName"]=>  string(5) "admin"
    *      ["userID"]=>  string(9) "1234"
    *      ["trash"]=>  string(1) "0"
    *      ["count_users"]=>  string(1) "2"
    *      ["leader_name"]=>  string(5) "user1"
    * }
    *
    * [1]=>  array(6) {
    *      ["groupID"]=>  string(1) "2"
    *      ["groupName"]=>  string(4) "Test"
    *      ["userID"]=>  string(9) "12345"
    *      ["trash"]=>  string(1) "0"
    *      ["count_users"]=>  string(1) "1"
    *      ["leader_name"]=>  string(7) "user2"
    *  }
    *
    * @global array $this->kga kimai-global-array
    * @return array
    * @author th
    *
    */
    public function get_groups_by_leader($leader_id,$trash=0)
    {
      // Lock tables
      $pdo_query_l = $this->conn->prepare("LOCK TABLE
      " . $this->kga['server_prefix'] . "users READ,
      " . $this->kga['server_prefix'] . "groups READ,
      " . $this->kga['server_prefix'] . "groupleaders READ
      ");
      $result_l = $pdo_query_l->execute();

      if ($result_l == false) {
          $this->logLastError('get_groups_by_leader');
          return array();
      }

      if (!$trash) {
          $trashoption = "AND group.trash !=1";
      }
      $pdo_query = $this->conn->prepare(
    "SELECT group.*
      FROM " . $this->kga['server_prefix'] . "groups AS group 
      JOIN " . $this->kga['server_prefix'] . "groupleaders USING(groupID)
      WHERE userID = ? $trashoption ORDER BY group.name");

      $result = $pdo_query->execute($leader_id);

      if ($result == false) {
          $this->logLastError('get_groups_by_leader');
          return array();
      }

      // rows into array
      $groups = array();
      $i=0;
      while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)){
          $groups[] = $row;

          // append user count
        $groups[$i]['count_users'] = $this->group_count_users($row['groupID']);

          // append leader array
          $userID_array = $this->group_get_groupleaders($row['groupID']);
          $j = 0;
          $leaderNames = array();
          foreach ($userID_array as $userID) {
            $leaderNames[$j] = $this->userIDToName($userID);
            $j++;
          }

          $groups[$i]['leader_name'] = $leaderNames;

          $i++;
      }

      // Unlock tables
      $pdo_query_ul = $this->conn->prepare("UNLOCK TABLES");
      $result_ul = $pdo_query_ul->execute();

      if ($result_ul == false) {
          $this->logLastError('get_groups_by_leader');
          return array();
      }

      // error_log("get_groups: " . serialize($groups));

      return $groups;
    }

    /**
    * Performed when the stop buzzer is hit.
    *
    * @global array $this->kga kimai-global-array
    * @param integer $id id of the entry to stop
    * @author th, sl
    * @return boolean
    */
    public function stopRecorder($id)
    {
      ## stop running recording
      $p = $this->kga['server_prefix'];

      $task = $this->timeSheet_get_data($id);

      $rounded = Rounding::roundTimespan($task['start'],time(),$this->kga['conf']['roundPrecision']);
      $difference = $rounded['end']-$rounded['start'];

      $pdo_query = $this->conn->prepare("UPDATE ${p}timeSheet SET start = ?, end = ?, duration = ? WHERE timeEntryID = ?;");
      $result = $pdo_query->execute(array($rounded['start'],$rounded['end'],$difference,$task['timeEntryID']));

      if ($result == false) {
          $this->logLastError('stopRecorder');
      }
      return $result;
    }

    /**
    * starts timesheet record
    *
    * @param integer $projectID ID of project to record
    * @global array $this->kga kimai-global-array
    * @author th, sl
    * @return id of the new entry or false on failure
    */
    public function startRecorder($projectID,$activityID,$user,$startTime)
    {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("INSERT INTO ${p}timeSheet
      (projectID,activityID,start,userID,rate) VALUES
      (?, ?, ?, ?, ?);");
      $result = $pdo_query->execute(array($projectID,$activityID,$startTime,$user,$this->get_best_fitting_rate($user,$projectID,$activityID)));
      if ($result === false) {
          $this->logLastError('startRecorder');
          return false;
      }

      return $this->conn->lastInsertId();
    }

    /**
    * Just edit the project for an entry. This is used for changing the project
    * of a running entry.
    *
    * @param $timeEntryID of the timesheet entry
    * @param $projectID id of the project to change to
    */
    public function timeEntry_edit_project($timeEntryID,$projectID)
    {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("UPDATE ${p}timeSheet
      SET projectID = ? WHERE timeEntryID = ?");

      $result = $pdo_query->execute(array($projectID,$timeEntryID));

      if ($result == false)
          $this->logLastError('timeEntry_edit_project');
    }

    /**
    * Just edit the task for an entry. This is used for changing the task
    * of a running entry.
    *
    * @param $timeEntryID of the timesheet entry
    * @param $activityID id of the task to change to
    */
    public function timeEntry_edit_activity($timeEntryID,$activityID)
    {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("UPDATE ${p}timeSheet
      SET activityID = ? WHERE timeEntryID = ?");

      $result = $pdo_query->execute(array($activityID,$timeEntryID));

      if ($result == false)
          $this->logLastError('timeEntry_edit_activity');
    }

    /**
    * return ID of specific customer named 'XXX'
    *
    * @param string $name name of the customer in table customers
    * @return integer
    */
    public function customer_nameToID($name)
    {
      return $this->name2id($this->kga['server_prefix']."customers",'customerID',$name);
    }

    /**
    * return ID of specific user named 'XXX'
    *
    * @param integer $name name of user in table users
    * @return string
    * @author th
    */
    public function user_name2id($name)
    {
      return $this->name2id($this->kga['server_prefix']."users",'userID',$name);
    }

    /**
    * Query a table for an id by giving the name of an entry.
    *
    * @author sl
    */
    private function name2id($table,$endColumn,$value)
    {
      $pdo_query = $this->conn->prepare("SELECT $endColumn FROM $table WHERE name = ? LIMIT 1;");
      $result = $pdo_query->execute(array($value));

      if ($result == false)
          $this->logLastError('name2id');

      if ($pdo_query->rowCount() == 0)
        return false;

      $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
      return $row[$endColumn];
    }

    /**
    * return name of a user with specific ID
    *
    * @param string $id the user's userID
    * @global array $this->kga kimai-global-array
    * @return int
    * @author ob
    */
    public function userIDToName($id)
    {
      $p = $this->kga['server_prefix'];

      $pdo_query = $this->conn->prepare("SELECT name FROM ${p}users WHERE userID = ? LIMIT 1;");
      $result = $pdo_query->execute(array($id));

      if ($result == false)
          $this->logLastError('userIDToName');

      $row = $pdo_query->fetch(PDO::FETCH_ASSOC);
      return $row['name'];
    }

    /**
    * returns the date of the first timerecord of a user (when did the user join?)
    * this is needed for the datepicker
    * @param integer $id of user
    * @return integer unix seconds of first timesheet record
    * @author th
    */
    public function getjointime($userID)
    {
      $p = $this->kga['server_prefix'];

      $query = "SELECT start FROM ${p}timeSheet WHERE userID = ? ORDER BY start ASC LIMIT 1;";
      $pdo_query = $this->conn->prepare($query);
      $result = $pdo_query->execute(array($userID));

      if ($result == false) {
          $this->logLastError('getjointime');
          return false;
      }

      $result_array = $pdo_query->fetch();

      if ($result_array[0] == 0) {
          return mktime(0,0,0,date("n"),date("j"),date("Y"));
      } else {
          return $result_array[0];
      }
    }

    /**
    * Set field status for users to 1 if user is a group leader, otherwise to 2.
    * Admin status will never be changed.
    * Calling public function should start and end sql transaction.
    *
    * @global array $this->kga              kimai global array
    * @global array $this->conn         PDO connection
    * @author sl
    */
    public function update_leader_status()
    {
      $p = $this->kga['server_prefix'];

      $query = $this->conn->prepare("UPDATE ${p}users SET status = 2 WHERE status = 1");
      $result = $query->execute();
      if ($result == false) {
          $this->logLastError('update_leader_status');
          return false;
      }

      $query = $this->conn->prepare("UPDATE ${p}users AS user, ${p}groupleaders AS leader SET status = 1 WHERE status = 2 AND user.userID = leader.userID");
      $result = $query->execute();
      if ($result == false) {
          $this->logLastError('update_leader_status');
          return false;
      }

      return true;
    }

    /**
    * Save rate to database.
    *
    * @global array $this->kga              kimai global array
    * @global array $this->conn         PDO connection
    * @author sl
    */
    public function save_rate($userID,$projectID,$activityID,$rate)
    {
        $p = $this->kga['server_prefix'];

        // validate input
        if ($userID == NULL || !is_numeric($userID)) $userID = "NULL";
        if ($projectID == NULL || !is_numeric($projectID)) $projectID = "NULL";
        if ($activityID == NULL || !is_numeric($activityID)) $activityID = "NULL";
        if (!is_numeric($rate)) return false;


        // build update or insert statement
        $query_string = "";
        if ($this->get_rate($userID,$projectID,$activityID) === false)
          $query_string = "INSERT INTO ${p}rates VALUES($userID,$projectID,$activityID,$rate);";
        else
          $query_string = "UPDATE ${p}rates SET rate = $rate WHERE ".
        (($userID=="NULL")?"userID is NULL":"userID = $userID"). " AND ".
        (($projectID=="NULL")?"projectID is NULL":"projectID = $projectID"). " AND ".
        (($activityID=="NULL")?"activityID is NULL":"activityID = $activityID");

        $query = $this->conn->prepare($query_string);
        $result = $query->execute();

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
    * @global array $this->kga              kimai global array
    * @global array $this->conn         PDO connection
    * @author sl
    */
    public function get_rate($userID,$projectID,$activityID)
    {
        $p = $this->kga['server_prefix'];

        // validate input
        if ($userID == NULL || !is_numeric($userID)) $userID = "NULL";
        if ($projectID == NULL || !is_numeric($projectID)) $projectID = "NULL";
        if ($activityID == NULL || !is_numeric($activityID)) $activityID = "NULL";


        $query_string = "SELECT rate FROM ${p}rates WHERE ".
        (($userID=="NULL")?"userID is NULL":"userID = $userID"). " AND ".
        (($projectID=="NULL")?"projectID is NULL":"projectID = $projectID"). " AND ".
        (($activityID=="NULL")?"activityID is NULL":"activityID = $activityID");

        $query = $this->conn->prepare($query_string);
        $result = $query->execute();

        if ($result == false) {
          $this->logLastError('get_rate');
          return false;
        }

        if ($query->rowCount() == 0)
          return false;

        $data = $query->fetch(PDO::FETCH_ASSOC);
        return $data['rate'];
    }

    /**
    * Remove rate from database.
    *
    * @global array $this->kga              kimai global array
    * @global array $this->conn         PDO connection
    * @author sl
    */
    public function remove_rate($userID,$projectID,$activityID)
    {
        $p = $this->kga['server_prefix'];

        // validate input
        if ($userID == NULL || !is_numeric($userID)) $userID = "NULL";
        if ($projectID == NULL || !is_numeric($projectID)) $projectID = "NULL";
        if ($activityID == NULL || !is_numeric($activityID)) $activityID = "NULL";

        $query_string = "DELETE FROM ${p}rates WHERE ".
        (($userID=="NULL")?"userID is NULL":"userID = $userID"). " AND ".
        (($projectID=="NULL")?"projectID is NULL":"projectID = $projectID"). " AND ".
        (($activityID=="NULL")?"activityID is NULL":"activityID = $activityID");

        $query = $this->conn->prepare($query_string);
        $result = $query->execute();

        if ($result === false) {
          $this->logLastError('remove_rate');
          return false;
        }

        return true;
    }

    /**
    * Query the database for the best fitting rate for the given user, project and activity.
    *
    * @global array $this->kga              kimai global array
    * @global array $this->conn         PDO connection
    * @author sl
    */
    public function get_best_fitting_rate($userID,$projectID,$activityID)
    {
        $p = $this->kga['server_prefix'];

        // validate input
        if ($userID == NULL || !is_numeric($userID)) $userID = "NULL";
        if ($projectID == NULL || !is_numeric($projectID)) $projectID = "NULL";
        if ($activityID == NULL || !is_numeric($activityID)) $activityID = "NULL";

        $query_string = "SELECT rate FROM ${p}rates WHERE
        (userID = $userID OR userID IS NULL)  AND
        (projectID = $projectID OR projectID IS NULL)  AND
        (activityID = $activityID OR activityID IS NULL)
        ORDER BY userID DESC, activityID DESC, projectID DESC
        LIMIT 1;";

        $query = $this->conn->prepare($query_string);
        $result = $query->execute();

        if ($result == false) {
          $this->logLastError('get_best_fitting_rate');
          return false;
        }

        if ($query->rowCount() == 0)
          return false;

        $data = $query->fetch(PDO::FETCH_ASSOC);
        return $data['rate'];
    }

    /**
    * Query the database for all fitting rates for the given user, project and activity.
    *
    * @global array $this->kga              kimai global array
    * @global array $this->conn         PDO connection
    * @author sl
    */
    public function allFittingRates($userID,$projectID,$activityID)
    {
        $p = $this->kga['server_prefix'];

        // validate input
        if ($userID == NULL || !is_numeric($userID)) $userID = "NULL";
        if ($projectID == NULL || !is_numeric($projectID)) $projectID = "NULL";
        if ($activityID == NULL || !is_numeric($activityID)) $activityID = "NULL";

        $query_string = "SELECT rate, userID, projectID, activityID FROM ${p}rates WHERE
        (userID = $userID OR userID IS NULL)  AND
        (projectID = $projectID OR projectID IS NULL)  AND
        (activityID = $activityID OR activityID IS NULL)
        ORDER BY userID DESC, activityID DESC, projectID DESC;";

        $query = $this->conn->prepare($query_string);
        $result = $query->execute();

        if ($result == false) {
          $this->logLastError('allFittingRates');
          return false;
        }

        $allRates = array();

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $allRates[] = $row;
        }

        return $allRates;
    }

    /**
    * Save fixed rate to database.
    *
    * @global array $this->kga              kimai global array
    * @global array $this->conn         PDO connection
    * @author sl
    */
    public function save_fixed_rate($projectID,$activityID,$rate)
    {
        $p = $this->kga['server_prefix'];

        // validate input
        if ($projectID == NULL || !is_numeric($projectID)) $projectID = "NULL";
        if ($activityID == NULL || !is_numeric($activityID)) $activityID = "NULL";
        if (!is_numeric($rate)) return false;


        // build update or insert statement
        $query_string = "";
        if ($this->get_fixed_rate($projectID,$activityID) === false)
          $query_string = "INSERT INTO ${p}fixedRates VALUES($projectID,$activityID,$rate);";
        else
          $query_string = "UPDATE ${p}fixedRates SET rate = $rate WHERE ".
        (($projectID=="NULL")?"projectID is NULL":"projectID = $projectID"). " AND ".
        (($activityID=="NULL")?"activityID is NULL":"activityID = $activityID");

        $query = $this->conn->prepare($query_string);
        $result = $query->execute();

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
    * @global array $this->kga              kimai global array
    * @global array $this->conn         PDO connection
    * @author sl
    */
    public function get_fixed_rate($projectID,$activityID)
    {
        $p = $this->kga['server_prefix'];

        // validate input
        if ($projectID == NULL || !is_numeric($projectID)) $projectID = "NULL";
        if ($activityID == NULL || !is_numeric($activityID)) $activityID = "NULL";


        $query_string = "SELECT rate FROM ${p}fixedRates WHERE ".
        (($projectID=="NULL")?"projectID is NULL":"projectID = $projectID"). " AND ".
        (($activityID=="NULL")?"activityID is NULL":"activityID = $activityID");

        $query = $this->conn->prepare($query_string);
        $result = $query->execute();

        if ($result == false) {
          $this->logLastError('get_fixed_rate');
          return false;
        }

        if ($query->rowCount() == 0)
          return false;

        $data = $query->fetch(PDO::FETCH_ASSOC);
        return $data['rate'];
    }

    /**
    * Remove fixed rate from database.
    *
    * @global array $this->kga              kimai global array
    * @global array $this->conn         PDO connection
    * @author sl
    */
    public function remove_fixed_rate($projectID,$activityID)
    {
        $p = $this->kga['server_prefix'];

        // validate input
        if ($projectID == NULL || !is_numeric($projectID)) $projectID = "NULL";
        if ($activityID == NULL || !is_numeric($activityID)) $activityID = "NULL";


        $query_string = "DELETE FROM ${p}fixedRates WHERE ".
        (($projectID=="NULL")?"projectID is NULL":"projectID = $projectID"). " AND ".
        (($activityID=="NULL")?"activityID is NULL":"activityID = $activityID");

        $query = $this->conn->prepare($query_string);
        $result = $query->execute();

        if ($result === false) {
          $this->logLastError('remove_fixed_rate');
          return false;
        }
        else
          return true;
    }

    /**
    * Query the database for the best fitting rate for the given user, project and activity.
    *
    * @global array $this->kga              kimai global array
    * @global array $this->conn         PDO connection
    * @author sl
    */
    public function get_best_fitting_fixed_rate($projectID, $activityID)
    {
        $p = $this->kga['server_prefix'];

        // validate input
        if ($projectID == NULL || !is_numeric($projectID)) $projectID = "NULL";
        if ($activityID == NULL || !is_numeric($activityID)) $activityID = "NULL";

        $query_string = "SELECT rate FROM ${p}fixedRates WHERE
        (projectID = $projectID OR projectID IS NULL)  AND
        (activityID = $activityID OR activityID IS NULL)
        ORDER BY activityID DESC, projectID DESC
        LIMIT 1;";

        $query = $this->conn->prepare($query_string);
        $result = $query->execute();

        if ($result == false) {
          $this->logLastError('get_best_fitting_fixed_rate');
          return false;
        }

        if ($query->rowCount() == 0)
          return false;

        $data = $query->fetch(PDO::FETCH_ASSOC);
        return $data['rate'];
    }

    /**
    * Query the database for all fitting rates for the given user, project and activity.
    *
    * @global array $this->kga              kimai global array
    * @global array $this->conn         PDO connection
    * @author sl
    */
    public function allFittingFixedRates($projectID, $activityID)
    {
        $p = $this->kga['server_prefix'];

        // validate input
        if ($projectID == NULL || !is_numeric($projectID)) $projectID = "NULL";
        if ($activityID == NULL || !is_numeric($activityID)) $activityID = "NULL";

        $query_string = "SELECT rate, projectID, activityID FROM ${p}fixedRates WHERE
        (projectID = $projectID OR projectID IS NULL)  AND
        (activityID = $activityID OR activityID IS NULL)
        ORDER BY activityID DESC, projectID DESC;";

        $query = $this->conn->prepare($query_string);
        $result = $query->execute();

        if ($result == false) {
          $this->logLastError('allFittingFixedRates');
          return false;
        }

        $allRates = array();

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $allRates[] = $row;
        }

        return $allRates;
    }

    /**
    * Save a new secure key for a user to the database. This key is stored in the users cookie and used
    * to reauthenticate the user.
    *
    * @global array $this->kga          kimai global array
    * @global array $conn         MySQL connection
    * @author sl
    */
    public function user_loginSetKey($userId, $keymai)
    {
        $p = $this->kga['server_prefix'];

        $query = "UPDATE ${p}users SET secure=?, ban=0, banTime=0 WHERE userID=?;";
        $query = $this->conn->prepare($query);
        $result = $query->execute(array($keymai,$userId));

        if ($result == false)
          $this->logLastError('user_loginSetKey');
    }

    /**
    * Save a new secure key for a customer to the database. This key is stored in the clients cookie and used
    * to reauthenticate the customer.
    *
    * @author sl
    */
    public function customer_loginSetKey($customerId, $keymai)
    {
        $p = $this->kga['server_prefix'];

        $query = "UPDATE ${p}customers SET secure=? WHERE customerID=?;";
        $query = $this->conn->prepare($query);
        $result = $query->execute(array($keymai,$customerId));

        if ($result == false)
          $this->logLastError('customer_loginSetKey');
    }

    /**
    * Update the ban status of a user. This increments the ban counter.
    * Optionally it sets the start time of the ban to the current time.
    *
    * @global array $this->kga          kimai global array
    * @global array $conn         MySQL connection
    * @author sl
    */
    public function loginUpdateBan($userId, $resetTime = false)
    {
        $p = $this->kga['server_prefix'];

        $query = "UPDATE ${p}users SET ban=ban+1 ";
        if ($resetTime)
          $query .= ",banTime = ".time()." ";
        $query .= "WHERE userID = ?";

        $query = $this->conn->prepare($query);
        $result = $query->execute(array($userId));

        if ($result == false)
          $this->logLastError('loginUpdateBan');
    }


    /**
    * Return all rows for the given sql query.
    *
    * @param string $query the sql query to execute
    */
    public function queryAll($statement)
    {
        $pdo_query = $this->conn->query($statement);

        if ($pdo_query == false)
          $this->logLastError("queryAll for $statement");

        $result = array();
        while ($row = $pdo_query->fetch()) {
              $result[] = $row;
          }
        return $result;
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
	$filter = array('projectID' => $projectId, 'trash' => 0);
	
	return $this->rowExists($table, $filter);
  }
  
  /**
   * checks if given $activityId exists in the db
   * 
   * @param int $activityId
   * @return bool
   */
  public function isValidActivityId($activityId)
  {
  	
  	$table = $this->getActivityTable();
	$filter = array('activityID' => $activityId, 'trash' => 0);
	
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
  	/**
	 * TODO: source out to a function! (MySQL::BuildSQLWhereClause())
	 */
	$where = '';
	foreach($filter as $key => $value)
	{
		if(empty($where))
		{
			$where .= "$key = $value";
		} else {
			$where .= " AND $key = $value";
		}
	}
	$pdo_query = $this->conn->prepare("SELECT * FROM $table WHERE $where");
    $select = $pdo_query->execute(array($id));

      if (!$select) {
          $this->logLastError('rowExists');
          return false;
      } else {
         $rowExits = (bool)$pdo_query->fetch(PDO::FETCH_ASSOC);
          return $rowExits;
      }
  }
   /************************************************************************************************
   * EXPENSES
   */
  
  /**
   * returns expenses for specific user as multidimensional array
   * @TODO: needs comments
   * @param integer $user ID of user in table users
   * @return array
   * @author th
   * @author Alexander Bauer
   */
  public function get_expenses($start, $end, $users = null, $customers = null, $projects = null, $limit=false, $reverse_order=false, $filter_refundable = -1, $filterCleared = null, $startRows = 0, $limitRows = 0, $countOnly = false) {
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
  
  	$whereClauses = $this->expenses_widthhereClausesFromFilters($users, $customers, $projects);
  
  	if (isset($kga['customer']))
  		$whereClauses[] = "${p}projects.internal = 0";
  
  	if ($start)
  		$whereClauses[]="timestamp >= $start";
  	if ($end)
  		$whereClauses[]="timestamp <= $end";
  	if ($filterCleared > -1)
  		$whereClauses[] = "cleared = $filterCleared";
  
  	switch ($filter_refundable) {
  		case 0:
  			$whereClauses[] = "refundable > 0";
  			break;
  		case 1:
  			$whereClauses[] = "refundable <= 0";
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
  	
  	
  	$select = "SELECT expenseID, timestamp, multiplier, value, project.projectID, designation, user.userID,
  				customer.name AS customerName, customer.customerID AS customerID, project.name AS projectName, comment, refundable,
  				commentType, name, cleared";
  				
  	$where = empty($whereClauses) ? '' : "WHERE ".implode(" AND ",$whereClauses);
  	$orderDirection = $reverse_order ? 'ASC' : 'DESC';
  	
  	if($countOnly) {
  		$select = "SELECT COUNT(*) AS total";
  		$limit = "";
  	}
  	 
  	$query = "$select
  		FROM ${p}expenses
	  	Join ${p}projects AS project USING(projectID)
	  	Join ${p}customers AS customer USING(customerID)
	  	Join ${p}users AS user USING(userID)
	  	$where
	  	ORDER BY timestamp $orderDirection $limit";
  	
	 $pdo_query = $this->conn->prepare($query);

      $result = $pdo_query->execute();

      if ($result == false) {
          $this->logLastError('get_expenses');
          return false;
      }
		
      // return only number of rows
      if($countOnly) {
      	$row = $pdo_query->fetch(PDO::FETCH_ASSOC);
      	return $row->total;
      }	
  	
  	$i=0;
  	$arr=array();
  	/* TODO: needs revision as foreach loop */
  	while ($row = $pdo_query->fetch(PDO::FETCH_ASSOC)) {
  		$row = $conn->Row();
  		$arr[$i] = $row;
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
   * @param Array list of IDs of activities to include
   * @return Array list of where clauses to include in the query
   */
  public function expenses_widthhereClausesFromFilters($users, $customers, $projects ) {
  
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
  			$whereClauses[] = "userID in (".implode(',',$users).")";
  		}
  
  		if (count($customers) > 0) {
  		$whereClauses[] = "customerID in (".implode(',',$customers).")";
  		}
  
  				if (count($projects) > 0) {
  		$whereClauses[] = "projectID in (".implode(',',$projects).")";
  		}
  
  		return $whereClauses;
  
	}

}
