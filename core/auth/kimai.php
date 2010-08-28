<?php

require(WEBROOT.'auth/base.php');


class KimaiAuth extends AuthBase {


  public function authenticate($username,$password,&$userId) {
      global $kga;

      $passCrypt = md5($kga['password_salt'].$password.$kga['password_salt']);

      $result = mysql_query(sprintf("SELECT * FROM %susr WHERE usr_name ='%s';",$kga['server_prefix'],mysql_real_escape_string($username)));
      if (mysql_num_rows($result) != 1) {
        $userId = false;
        return false;
      }

      $row    = mysql_fetch_assoc($result);
      $pass    = $row['pw'];        
      $ban     = $row['ban'];
      $banTime = $row['banTime'];   
      $userId  = $row['usr_ID'];
      
      return ($pass==$password || $pass==$passCrypt) && $username!="";
  }
}

?> 