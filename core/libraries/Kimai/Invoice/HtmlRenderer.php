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
 * Class for rendering PHTML invoices templates as PDF.
 *
 * @author Kevin Papst
 */
class Kimai_Invoice_HtmlRenderer extends Kimai_Invoice_AbstractRenderer
{

    const FILE_PHTML = 'index.phtml';
    const FILE_HTML = 'index.html';

    /**
     * Render the invoice.
     *
     * @return mixed
     */
    public function render()
    {
        echo $this->getHtml();
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        $view = new Kimai_View();
        $view->setScriptPath($this->getTemplateDir().$this->getTemplateFile());

        // DO NOT RELY ON THESE VARIABLES - they are only here for compatibility with the ODT layer
        $view->assign('CustomerODT', $this->prepareCustomerArray($this->getModel()->getCustomer()));
        $view->assign('project', implode(', ', array_map(function($project) { return $project['name']; }, $this->getModel()->getProjects())));

        $data = $this->getModel()->toArray();
        foreach($data as $key => $value) {
            $view->assign($key, $value);
        }

        // can be used to reference styles and images
        $view->assign('invoiceUrl', 'invoices/'.$this->getTemplateFile().'/');

        return $view->render($this->getTemplateFilename());
    }

    /**
     * @return string
     */
    protected function getTemplateFilename()
    {
        if (is_file($this->getTemplateDir() . $this->getTemplateFile() . DIRECTORY_SEPARATOR . self::FILE_PHTML)) {
            return self::FILE_PHTML;
        }
        return self::FILE_HTML;
    }

    /**
     * Returns if the file can be rendered.
     *
     * @return bool
     */
    public function canRender()
    {
        if (!is_dir($this->getTemplateDir() . $this->getTemplateFile())) {
            return false;
        }

        return (
            is_file($this->getTemplateDir() . $this->getTemplateFile() . DIRECTORY_SEPARATOR . self::FILE_HTML) ||
            is_file($this->getTemplateDir() . $this->getTemplateFile() . DIRECTORY_SEPARATOR . self::FILE_PHTML)
        );
    }

}
