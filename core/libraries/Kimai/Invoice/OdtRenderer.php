<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) Kimai-Development-Team
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
 * Class for rendering ODT and ODS invoices.
 *
 * @author Kevin Papst
 */
class Kimai_Invoice_OdtRenderer extends Kimai_Invoice_AbstractRenderer
{

    /**
     * Render the invoice.
     *
     * @return mixed
     */
    public function render()
    {
        // libs TinyButStrong
        include_once('TinyButStrong/tinyButStrong.class.php');
        include_once('TinyButStrong/tinyDoc.class.php');

        $doc   = new tinyDoc();

        // use zip extension if available
        if (class_exists('ZipArchive')) {
            $doc->setZipMethod('ziparchive');
        }
        else {
            $doc->setZipMethod('shell');
            try {
                $doc->setZipBinary('zip');
                $doc->setUnzipBinary('unzip');
            }
            catch (tinyDocException $e) {
                $doc->setZipMethod('pclzip');
            }
        }

        $doc->setProcessDir($this->getTemporaryDirectory());

        //This is where the template is selected

        $templateform = $this->getTemplateDir() . $this->getTemplateFile();
        $doc->createFrom($templateform);

        $doc->loadXml('content.xml');

        // fetch variables from model to get values
        $customer   = $this->getModel()->getCustomer();
        $projects   = $this->getModel()->getProjects();
        $entries    = $this->getModel()->getEntries();

        // assign all available variables (which are not arrays as they do not work in tinyButStrong)
        foreach($this->getModel()->toArray() as $k => $v) {
            if (is_array($v)) continue;
            $GLOBALS[$k] = $v;
        }

        // ugly but neccessary for tinyButStrong
        // set globals variables, so they can be used in invoice templates
        $allCustomer = $this->prepareCustomerArray($customer);
        foreach($allCustomer as $k => $v) {
            $GLOBALS[$k] = $v;
        }

        $GLOBALS['projects'] = $projects;
        $GLOBALS['project'] = implode(', ', array_map(function($project) { return $project['name']; }, $projects));

        $doc->mergeXmlBlock('row', $entries);

        $doc->saveXml();
        $doc->close();

        // send and remove the document
        $doc->sendResponse();
        $doc->remove();
    }

    /**
     * Returns if the file can be rendered.
     *
     * @return bool
     */
    public function canRender()
    {
        return (
            (stripos($this->getTemplateFile(), '.odt') !== false || stripos($this->getTemplateFile(), '.ods') !== false) &&
            is_file($this->getTemplateDir() . $this->getTemplateFile())
        );
    }

}
