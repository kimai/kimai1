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

/**
 * Class Provider for all language specific stuff.
 *
 * @package KimaiTest\Language
 */
class Provider
{

    /**
     * Provider for all existing language files.
     *
     * @return array
     */
    public static function getAll()
    {
        return array(
            array('cz'),
            array('da'),
            array('de'),
            array('es'),
            array('fr'),
            array('hu'),
            array('is'),
            array('it'),
            array('nl'),
            array('nl-BE'),
            array('no-bo'),
            array('pl'),
            array('pt'),
            array('pt_BR'),
            array('si'),
            array('sv'),
            array('zh'),
        );
    }

}