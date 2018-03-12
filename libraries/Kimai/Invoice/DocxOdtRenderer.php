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
 * Class for rendering DOC and DOCX invoices.
 *
 * @author Kevin Papst
 */
 

include_once('../../libraries/tinybutstrong/opentbs/tbs_plugin_opentbs.php');
 
 
class Kimai_Invoice_DocxOdtRenderer extends Kimai_Invoice_AbstractRenderer
{



    /**
     * Render the invoice.
     *
     * @return mixed
     */
    public function render()
    {
        $doc = new clsTinyButStrong;
        $doc->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
       

        $templateForm = $this->getTemplateDir() . $this->getTemplateFile();
        
        $doc->LoadTemplate($templateForm, OPENTBS_ALREADY_UTF8);

        
        $customer = $this->getModel()->getCustomer();
        
        $allCustomer = $this->prepareCustomerArray($customer);
       
        $invoiceData = array_merge($allCustomer, $this->getModel()->toArray());
        
        $doc->MergeField('kimai', $invoiceData);
        
        $doc->MergeBlock('pos', $invoiceData['entries']);

        $invoiceFilename = $invoiceData['invoiceId'] . '-Invoice.' . substr(strrchr($this->getTemplateFile(), "."), 1);
        
        $doc->Show(OPENTBS_DOWNLOAD, $invoiceFilename);
        $doc->LoadTemplate(false);
    }

    /**
     * Returns if the file can be rendered.
     *
     * @return bool
     */
    public function canRender()
    {
        return (
            (stripos($this->getTemplateFile(), '.docx') !== false || stripos($this->getTemplateFile(), '.doc') !== false ||
             stripos($this->getTemplateFile(), '.odt') !== false || stripos($this->getTemplateFile(), '.ods') !== false
            ) &&
            is_file($this->getTemplateDir() . $this->getTemplateFile())
        );
    }
}
