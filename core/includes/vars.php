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
 * along with Kimai; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 */
 
// define kimai-global-array variables which are used in func, processor, etc...
$kga = array();

$kga['now']            = time();

require('version.php');
                    
$kga['virtual_users']  = 0;            // If this is set to 1 there is no login possible - 
                                       // kimai then creates a virtual user that exists for that very session
                                       // and drops the user after the session is closed.
                                       // Use this for demo installments.
                      
$kga['cryptmethod']    = "CRYPT_MD5";  // Possible password encryption methods:
                                       //
                                       // CRYPT_STD_DES    Standard DES-based encryption with a two character salt
                                       // CRYPT_EXT_DES    Extended DES-based encryption with a nine character salt
                                       // CRYPT_MD5        MD5 encryption with a twelve character salt starting with $1$
                                       // CRYPT_BLOWFISH   Blowfish encryption with a sixteen character salt 
                                       //                  starting with $2$ or $2a$ 
                                       //
                                       // in the script the password is encrypted like this: 
                                       // crypt($password,$kga['cryptmethod']);
                                       

// ------------------------------------------------------------------------------------------------------------------------------------------------

$kga['charsets']       = array('utf-8','iso-8859-1');
$kga['charset_descr']  = array('UTF-8','iso-8859-1');

                          
// $kga['customerhack']     = 1; // set to 1 to activate a temporary filter option for PDF printing 
                                 // [actually this is obsolete because PDF printing is not available in 0.8...]

$kga['show_sensible_data'] = 0; // turn this on to display sensible data in the debug/developer extension 
                                // CAUTION - THINK TWICE IF YOU REALLY WANNA DO THIS AND DON'T FORGET TO TURN IT OFF IN A PRODUCTION ENVIRONMENT!!!
                                // DON'T BLAME US - YOU HAVE BEEN WARNED!
                                
$kga['logfile_lines']      = 100; // number of lines shown from the logfile in debug extension. Set to "@" to display the entire file (might freeze your browser...)
$kga['delete_logfile']     = 1;   // can the logfile be cleaned via debug_ext?
                          
$kga['utf8']               = 0;     // set to 1 if utf-8 CONVERSION (!) is needed - this is not always the case, 
                                    // depends on server preferences
                                    
$kga['dbname_public']      = 0;     // allow/disallow to show the name of you database
                                    // as tooltip when hovering over the Kimai logo
                                    
$kga['show_update_warn']   = 1;     // if you find the update warning page annoying - turn it off by setting this to 0
                                    
$kga['check_at_startup']   = 0;     // everytime the login-screen appears the version-number will be checked against our
                                    // server if this is set to 1. We do not transmit any personal data!
                                    // Only revision-number and language-preference (de, en, ...) are transmitted.
                                    // We only *count* the checkups for statistics. If you want to keep your installment
                                    // up-to-date you should activate this.

$kga['show_daySeperatorLines'] = 1; // set to 0 to supress the black lines between days

$kga['show_gabBreaks'] = 1;         // set to 1 to show lines between two records that are not perfectly continuous

$kga['show_RecordAgain'] = 1;       // set to 0 to supress the 'record again' buttons in the timesheet table
                                     
$kga['show_TrackingNr'] = 1;        // set to 1 to make the tracking number of timesheet records editable

$kga['global'] = 0;                 // set to 1 to make tracking records of all users visible for admins
// not fully implemented yet!


                                
// ------------------------------------------------------------------------------------------------------------------------------------------------                                
                               
// german date format                           
$kga['date_format'][0]    = "d.m.y";    // You can only use "d", "m" and either "Y" (2007) or "y" (07) - 
                                        // also use whatever you like as seperators (PHP-format).
                                        
$kga['date_format'][1]    = "%d.%m.";   // Here you can use Smarty date-notation - this appears before every 
                                        // timesheet entry (you should only use day and month here).
                                        
$kga['date_format'][2]    = "%d.%m.%Y"; // Another Smarty notation for the full date.


//$kga['date_format'][3]    = "dmy";    // This sets the order of the date-picker fields in the timespace selector 
                                        // (allowed values: dmy or mdy)  OUTDATED!!!
                                        
$kga['calender_start']      = "";       // here you can set a custom start day for the date-picker.
                                        // if this is not set the day of the users first day in the system will be taken
                                        // Format: ... = "DD/MM/YYYY"; 

                                        
// ------------------------------------------------------------------------------------------------------------------------------------------------

// Read more about PHP date-format used in [0]: http://de2.php.net/manual/en/function.date.php
// Read more about smarty date-format used in [1] and [2]: 
// http://www.smarty.net/manual/en/language.modifier.date.format.php

/*// english date format
$kga['date_format'][0]    = "m-d-Y";
$kga['date_format'][1]    = "%m/%d";
$kga['date_format'][2]    = "%m-%d-%Y";
$kga['date_format'][3]    = "mdy";
*/

if ($kga['virtual_users']) {
    session_start();
    session_register("user");
}

// ------------------------------------------------------------------------------------------------------------------------------------------------
// write vars from conf.php into kga
// to stay compatible to earlier versions with the
// former conf.php format
$kga['server_prefix']   = $server_prefix;   unset($server_prefix);
$kga['server_hostname'] = $server_hostname; unset($server_hostname);
$kga['server_database'] = $server_database; unset($server_database);
$kga['server_username'] = $server_username; unset($server_username);
$kga['server_password'] = $server_password; unset($server_password);
$kga['server_type']     = $server_type;     unset($server_type);
$kga['server_conn']     = $server_conn;     unset($server_conn);
$kga['language']        = $language;        unset($language);

?>
