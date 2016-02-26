<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2012 Kimai-Development-Team
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
 * Provides functions for parsing the hierarchy of permissions and printing them in HTML.
 *
 * @author Severin
 */
class Zend_View_Helper_ParseHierarchy extends Zend_View_Helper_Abstract
{

  /**
   *  @brief Parse the hierarchy of permissions.
   *  
   *  All permissions are split at dashes. From those parts two hierearchies are built.
   *
   * @param array $permissions list of permission names
   * @param array $extensions will contain all extensions for which an access permission key exists
   * @param array $keyHierarchy will contain all other permissions in a hierarchy split by the dash (-)
   */
  public function parseHierarchy($permissions, &$extensions, &$keyHierarchy) {

    foreach ($permissions as $key => $value) {

      $keyParts = explode("-",$key);

      if (count($keyParts) == 2 && $keyParts[1] == 'access') {
        $extensions [$keyParts[0]] = $value;
        continue;
      }
      $currentHierarchyLevel = &$keyHierarchy;

      foreach ($keyParts as $keyPart) {
        if (!array_key_exists($keyPart,$currentHierarchyLevel))
          $currentHierarchyLevel[$keyPart] = array();
        $currentHierarchyLevel = &$currentHierarchyLevel[$keyPart];
      }

      $currentHierarchyLevel = $value;

    }
  }
} 
