<?php
/**
 * The Kimai Global Array ($kga) is initialized here. It is used throught
 * all functions, processors, etc.
 */
 
$kga = array();

require('version.php');

// ------------------------------------------------------------------------------------------------------------------------------------------------



//$kga['show_sensible_data'] = 1; // turn this on to display sensible data in the debug/developer extension 
                                // CAUTION - THINK TWICE IF YOU REALLY WANNA DO THIS AND DON'T FORGET TO TURN IT OFF IN A PRODUCTION ENVIRONMENT!!!
                                // DON'T BLAME US - YOU HAVE BEEN WARNED!
                                
$kga['logfile_lines']      = 100; // number of lines shown from the logfile in debug extension. Set to "@" to display the entire file (might freeze your browser...)
$kga['delete_logfile']     = 1;   // can the logfile be cleaned via debug_ext?
                          
$kga['utf8']               = 0;     // set to 1 if utf-8 CONVERSION (!) is needed - this is not always the case, 
                                    // depends on server preferences
                                 
$kga['calender_start']      = "0";      // here you can set a custom start day for the date-picker.
                                        // if this is not set the day of the users first day in the system will be taken
                                        // Format: ... = "DD/MM/YYYY"; 


// ------------------------------------------------------------------------------------------------------------------------------------------------
// write vars from autoconf.php into kga
$kga['server_prefix']   = $server_prefix;   unset($server_prefix);
$kga['server_hostname'] = $server_hostname; unset($server_hostname);
$kga['server_database'] = $server_database; unset($server_database);
$kga['server_username'] = $server_username; unset($server_username);
$kga['server_password'] = $server_password; unset($server_password);
$kga['server_type']     = $server_type;     unset($server_type);
$kga['server_conn']     = $server_conn;     unset($server_conn);
$kga['language']        = $language;        unset($language);
$kga['password_salt']   = $password_salt;   unset($password_salt);

?>
