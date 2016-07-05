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

namespace KimaiTest\View\Helper;

use KimaiTest\TestCase;
use Kimai_View_Helper_AdminScreen;

/**
 * @coversDefaultClass Kimai_View_Helper_AdminScreen
 */
class AdminScreenTest extends TestCase
{

    /**
     * @covers ::adminScreen
     */
    public function testAdminScreenReturnsSelf()
    {
        $helper = new Kimai_View_Helper_AdminScreen();
        $this->assertInstanceOf('Kimai_View_Helper_AdminScreen', $helper->adminScreen());
    }

    /**
     * @covers ::accordion
     * @covers ::accordionHeader
     * @covers ::accordionContent
     * @covers ::accordionFooter
     * @covers ::accordionTitle
     */
    public function testAccordionHtmlContainsElements()
    {
        $id = 4711;
        $title = 'FooBar';
        $content = '<h1>Some HTML content</h1>';

        $helper = new Kimai_View_Helper_AdminScreen();
        $html = $helper->accordion($id, $title, $content);

        $this->assertGreaterThan(0, stripos($html, $id));
        $this->assertGreaterThan(0, stripos($html, $title));
        $this->assertGreaterThan(0, stripos($html, $content));
    }
}
