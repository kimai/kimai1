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
 * @author Severin/Kevin
 */
class Zend_View_Helper_Hierarchy extends Zend_View_Helper_Abstract
{

    public function hierarchy()
    {
        return $this;
    }

    /**
     * @brief Parse the hierarchy of permissions.
     *
     *  All permissions are split at dashes. From those parts two hierearchies are built.
     *
     * @param array $permissions list of permission names
     * @param array $extensions will contain all extensions for which an access permission key exists
     * @param array $keyHierarchy will contain all other permissions in a hierarchy split by the dash (-)
     */
    public function parse($permissions, &$extensions, &$keyHierarchy)
    {
        foreach ($permissions as $key => $value) {

            $keyParts = explode("-", $key);

            if (count($keyParts) == 2 && $keyParts[1] == 'access') {
                $extensions [$keyParts[0]] = $value;
                continue;
            }
            $currentHierarchyLevel = & $keyHierarchy;

            foreach ($keyParts as $keyPart) {
                if (!array_key_exists($keyPart, $currentHierarchyLevel))
                    $currentHierarchyLevel[$keyPart] = array();
                $currentHierarchyLevel = & $currentHierarchyLevel[$keyPart];
            }

            $currentHierarchyLevel = $value;

        }
    }

    /**
     * @brief Print nested fieldsets for the permissions hierarchy.
     *
     * @param array $kga the Kimai global Array, necessary for translations
     * @param array $keyHierarchy the key hierarchy, see parseHierarchy
     * @param array $parentKeys all keys of the parents, the closest one at the end
     * @param integer $level the level in the hierarchy
     */
    public function render($kga, $keyHierarchy, $parentKeys = array(), $level = 0)
    {
        $originalLevel = $level;
        $noLegendOnLevel[] = 0;

        // If the hierarchy only contains one key that key is "jumped" to simplify the displayed hierarchy.
        $jumpedKeys = array();
        while ($this->isJumpable($keyHierarchy)) {
            $keys = array_keys($keyHierarchy);
            $jumpedKeys[] = $keys[0];
            $parentKeys[] = $keys[0];
            $level++;
            $keyHierarchy = $keyHierarchy[$keys[0]];
        }

        if ($level > 0) {
            if ($originalLevel == 1) {
                $id = $parentKeys[$originalLevel - 1];
                echo $this->renderLevelBegin($level, $id);
            } else {
                echo $this->renderLevelBegin($level, null);
            }

            $names = array();
            for ($i = max(0, $originalLevel - 1); $i < count($parentKeys); $i++) {
                if (array_search($i, $noLegendOnLevel) !== false) continue;

                $name = $parentKeys[$i];
                if (isset($kga['lang']['permissions'][$name]))
                    $name = $kga['lang']['permissions'][$name];
                if (isset($kga['lang'][$name]))
                    $name = $kga['lang'][$name];
                $names[] = $name;
            }

            $titles = implode(', ', $names);
            if (trim($titles) !== '') {
                echo $this->renderLevelTitle($titles);
            }
        }

        foreach ($keyHierarchy as $key => $subKeys) {
            if (is_array($subKeys)) continue;

            if (empty($parentKeys)) {
                $permissionKey = $key;
            } else {
                $permissionKey = implode('-', $parentKeys) . '-' . $key;
            }
            $name = $key;

            if (isset($kga['lang']['permissions'][$name])) {
                $name = $kga['lang']['permissions'][$name];
            }

            if (isset($kga['lang'][$name])) {
                $name = $kga['lang'][$name];
            }

            echo $this->renderPermissionSelect($name, $permissionKey, ($subKeys == 1));
        }

        foreach ($keyHierarchy as $key => $subKeys) {
            if (!is_array($subKeys)) continue;

            $newParentKeys = $parentKeys;
            $newParentKeys[] = $key;

            $this->render($kga, $subKeys, $newParentKeys, $level + 1);
        }

        if ($level > 0) {
            echo $this->renderLevelEnd();
        }
    }

    /**
     * @brief Decide if a hierarchy step can be jumped.
     *
     * A hierarchy step can be jumped if there is only one item on the current level and
     * at least one item on the next level in the hierarchy. This effectivly combines several
     * levels of hierarchy if they are only used for structure and not to provide several permissions
     * on the same level.
     *
     * @param array $keyHierarchy the hierarchy of keys, see parseHierarchy
     * @return true if this level can be jumped, false otherwise
     */
    private function isJumpable($keyHierarchy)
    {
        if (count($keyHierarchy) != 1) {
            return false;
        }

        $keys = array_keys($keyHierarchy);
        $values = $keyHierarchy[$keys[0]];

        if (!is_array($values)) {
            return false;
        }

        return true;
    }

    // ==================== COULD BE OVERWRITTEN BY SKINS ====================

    protected function renderPermissionSelect($title, $key, $activated)
    {
        $checked = ($activated) ? ' checked="checked"' : '';
        return '<span class="permission"><input type="checkbox" value="1" name="'.$key.'"'.$checked.' />'.$title.'</span>';
    }

    protected function renderLevelTitle($title)
    {
        return "<legend> " . $title . " </legend>";
    }

    protected function renderLevelEnd()
    {
        return "</fieldset>";
    }

    protected function renderLevelBegin($level, $id = null)
    {
        if ($id === null) {
            return '<fieldset class="hierarchyLevel'.$level.'">';
        }

        return '<fieldset id="'.$id.'" class="hierarchyLevel'.$level.'">';
    }
} 
