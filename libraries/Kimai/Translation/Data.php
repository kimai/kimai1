<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // https://www.kimai.org
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
 * Class Kimai_Translation_Data
 */
class Kimai_Translation_Data extends \ArrayObject
{
    /**
     * @var string
     */
    protected $language;

    /**
     * Create a translation object:
     * pre-fill with english and replace by $language specific data.
     *
     * @param array|null|object $language
     */
    public function __construct($language)
    {
        $default = Kimai_Config::getDefault(Kimai_Config::DEFAULT_LANGUAGE);
        $data = include WEBROOT . 'language/'.$default.'.php';
        parent::__construct($data, \ArrayObject::ARRAY_AS_PROPS);
        $this->addTranslations($language);
    }

    /**
     * Adds the translations for the given language.
     *
     * @param $language
     * @throws Exception
     */
    public function addTranslations($language)
    {
        // no need to load the default or already requested language again!
        $default = Kimai_Config::getDefault(Kimai_Config::DEFAULT_LANGUAGE);
        if (empty($language) || $language == $default || $language == $this->language) {
            return;
        }

        $languageFile = WEBROOT . 'language/'.$language.'.php';
        if (!file_exists($languageFile)) {
            Kimai_Logger::logfile('Requested translation is missing: ' . $language);
            return;
        }

        $this->language = $language;
        $data = array_replace_recursive(
            $this->getArrayCopy(),
            include $languageFile
        );

        $this->exchangeArray($data);
    }

    /**
     * Get a translation key, if not available return default.
     *
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return $this->offsetGet($key);
        }
        return $default;
    }

    /**
     * Add a new translation
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->offsetSet($key, $value);
    }
}
