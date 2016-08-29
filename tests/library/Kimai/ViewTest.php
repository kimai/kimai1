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

namespace KimaiTest;

use Kimai_View;

/**
 * @coversDefaultClass Kimai_View
 */
class ViewTest extends TestCase
{

    /**
     * @covers ::init
     */
    public function testInit()
    {
        $myKga = new \Kimai_Config(array('foo' => 'bar'));
        $this->setKga($myKga);

        $view = new Kimai_View();

        $helperPaths = $view->getHelperPaths();
        $this->assertArrayHasKey('Zend_View_Helper_', $helperPaths);
        $this->assertContains(APPLICATION_PATH . '/templates/helpers/', $helperPaths['Zend_View_Helper_']);

        $scriptsPaths = $view->getScriptPaths();
        $this->assertContains(APPLICATION_PATH . '/templates/scripts/', $scriptsPaths);

        $vars = $view->getVars();
        $this->assertArrayHasKey('kga', $vars);

        $this->assertEquals($myKga, $vars['kga']);
    }
}
