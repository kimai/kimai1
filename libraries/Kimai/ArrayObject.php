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
 * Base class for all ArrayObjects in Kimai.
 */
class Kimai_ArrayObject extends \ArrayObject
{

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * @param $key
     * @param mixed $default
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
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Adds a bunch of new settings through an associative array.
     *
     * @param array $values
     */
    public function add(array $values)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }
}
