<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2013 Kimai-Development-Team
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

namespace KimaiTest\Language;

use PHPUnit_Framework_TestCase;

/**
 * Class MissingKeysTest.
 *
 * @package KimaiTest
 * @author Kevin Papst <kpapst@gmx.net>
 */
class MissingKeysTest extends PHPUnit_Framework_TestCase
{
    /**
     * The language array to check.
     *
     * @var array
     */
    private $SUT = null;
    /**
     * The directory where language files can be found.
     *
     * @var string
     */
    private $directory = null;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->directory = realpath(__DIR__ . '/../../../core/language/') . '/';
        $this->SUT = $this->loadLanguageFile('en');
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        $this->SUT = null;
    }

    /**
     * Returns the contents of the given language file.
     *
     * @param $locale
     * @return array
     */
    protected function loadLanguageFile($locale)
    {
        $filename = $this->directory . $locale . '.php';
        $translation = @include($filename);
        if (!($translation)) {
            $this->fail('Missing language: ' . $filename);
        }
        return $translation;
    }

    /**
     * Searches for keys which are no longer required.
     *
     * @dataProvider languageProvider
     */
    public function testDeprecatedKeys($locale)
    {
        $cmpLanguage = $this->loadLanguageFile($locale);
        foreach($cmpLanguage as $key => $value)
        {
            if (!is_array($value)) {
                $this->assertTrue(array_key_exists($key, $this->SUT), 'Key "'.$key.'" is not needed any longer in "'.$locale.'"');
            } else {
                foreach($value as $key2 => $value2)
                {
                    $this->assertTrue(
                        array_key_exists($key2, $this->SUT[$key]),
                        'Key "'.$key.'['.$key2.']" is not needed any longer in "'.$locale.'"'
                    );
                }
            }
        }
    }

    /**
     * Provider for all existing language files.
     *
     * @return array
     */
    public static function languageProvider()
    {
        return \KimaiTest\Language\Provider::getAll();
    }
}