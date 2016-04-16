<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team since 2006
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
 * Class Kimai_Config_Settings
 *
 * Holds user and system specific configurations,
 * where user settings can override system settings.
 */
class Kimai_Config_Settings extends Kimai_ArrayObject
{

    /**
     * Setup the configuration object with the given array.
     *
     * @param array $settings
     */
    public function __construct(array $settings = array())
    {
        $data = array_merge(
            $this->getDefaults(),
            $settings
        );
        parent::__construct($data, \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * Return default settings for the application.
     *
     * @return array
     */
    protected function getDefaults()
    {
        return array(
            'rowlimit' => 100,
            'skin' => Kimai_Config::getDefault(Kimai_Config::DEFAULT_SKIN),
            'autoselection' => 1,
            'quickdelete' => 0,
            'flip_project_display' => 0,
            'project_comment_flag' => 0,
            'showIDs' => 0,
            'noFading' => 0,
            'lang' => '',
            'user_list_hidden' => 0,
            'hideClearedEntries' => 0,
            'durationWithSeconds' => 0,
            'showQuickNote' => 0,
            'defaultLocation' => '',
            'showCommentsByDefault' => 0,
            'hideOverlapLines' => 0,
            'showTrackingNumber' => 0,
            'sublistAnnotations' => 0,
        );
    }

    /**
     * Returns the skin to be used
     *
     * @return string|null
     */
    public function getSkin()
    {
        return $this->get('skin');
    }

    /**
     * Sets the skin to be used
     *
     * @param $skin
     */
    public function setSkin($skin)
    {
        $this->set('skin', $skin);
    }

    /**
     * Returns the user specific language or an empty string if not configured.
     *
     * @return string
     */
    public function getUserLanguage()
    {
        return $this->get('lang');
    }

    /**
     * system language is set in admin extended panel
     *
     * @return string|null
     */
    public function getSystemLanguage()
    {
        return $this->get('language');
    }

    /**
     * Returns the language to be displayed
     *
     * @return string|null
     */
    public function getLanguage()
    {
        $lang = $this->getUserLanguage();
        if (!empty($lang)) {
            return $lang;
        }

        return $this->getSystemLanguage();
    }

    /**
     * @return bool
     */
    public function isShowComments()
    {
        return $this->get('showCommentsByDefault', 0) == 1;
    }

    /**
     * @return bool
     */
    public function isShowOverlapLines()
    {
        return $this->get('hideOverlapLines', 0) == 0;
    }

    /**
     * Whether the user sees the tracking number in its timesheet.
     * Can be configured by the user himself.
     *
     * @return bool
     */
    public function isShowTrackingNumber()
    {
        return $this->get('showTrackingNumber', 0) == 1;
    }

    /**
     * @return int
     */
    public function getSublistAnnotationType()
    {
        return $this->get('sublistAnnotations', 0);
    }

    /**
     * @return int
     */
    public function getRowLimit()
    {
        return (int)$this->get('rowlimit', 100);
    }

    /**
     * @return bool
     */
    public function isUseSmoothFading()
    {
        return !(bool)$this->get('noFading', false);
    }

    /**
     * @return int
     */
    public function getQuickDeleteType()
    {
        return $this->get('quickdelete', 0);
    }

    /**
     * @return string
     */
    public function getDefaultLocation()
    {
        return $this->get('defaultLocation', '');
    }

    /**
     * @return bool
     */
    public function isShowQuickDelete()
    {
        return $this->getQuickDeleteType() > 0;
    }

    /**
     * @return bool
     */
    public function isShowQuickNote()
    {
        return (bool)$this->get('showQuickNote', false);
    }

    /**
     * @return bool
     */
    public function isUseAutoSelection()
    {
        return (bool)$this->get('autoselection', true);
    }

    /**
     * @return bool
     */
    public function isShowIds()
    {
        return (bool)$this->get('showIDs', false);
    }

    /**
     * @return bool
     */
    public function isUserListHidden()
    {
        return (bool)$this->get('user_list_hidden', false);
    }

    /**
     * @return bool
     */
    public function isShowAfterRecorded()
    {
        return (bool)$this->get('openAfterRecorded', false);
    }

    /**
     * @return bool
     */
    public function isFlipProjectDisplay()
    {
        return (bool)$this->get('flip_project_display', false);
    }

    /**
     * @return bool
     */
    public function isHideClearedEntries()
    {
        return (bool)$this->get('hideClearedEntries', false);
    }

    /**
     * @return bool
     */
    public function isShowProjectComment()
    {
        return (bool)$this->get('project_comment_flag', false);
    }
}
