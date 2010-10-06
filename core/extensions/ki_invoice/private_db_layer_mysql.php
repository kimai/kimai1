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
 * returns the data of a costumer
 *
 * @param integer $name name of costumer in table
 * @global array $kga kimai-global-array
 * @return array
 * @author sl
 */
function get_entry_knd($id) {
    global $kga, $conn;
    $p = $kga['server_prefix'];

    $filter['knd_name'] = MySQL::SQLValue($id);

    $result = $conn->SelectRows($kga['server_prefix'].'knd', $filter,null,null,true,1);

    if (! $result)
      return false;

    return $conn->RowArray(0,MYSQL_ASSOC);
}


?>