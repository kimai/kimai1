<?php
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
        $project    = $this->getModel()->getProject();
        $entries    = $this->getModel()->getEntries();

        // ugly but neccessary for tinyButString
        // set globals variables, so they can be used in invoice templates
        $GLOBALS['customerContact'] = $customer['contact'];
        $GLOBALS['companyName']     = $customer['company'];
        $GLOBALS['customerStreet']  = $customer['street'];
        $GLOBALS['customerCity']    = $customer['city'];
        $GLOBALS['customerZip']     = $customer['zipcode'];
        $GLOBALS['customerPhone']   = $customer['phone'];
        $GLOBALS['customerEmail']   = $customer['mail'];
        $GLOBALS['customerComment'] = $customer['comment'];
        $GLOBALS['customerFax']     = $customer['fax'];
        $GLOBALS['customerMobile']  = $customer['mobile'];
        $GLOBALS['customerURL']     = $customer['homepage'];
        $GLOBALS['customerVat']     = $customer['vat'];
        $GLOBALS['projectComment']  = $project['comment'];

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
