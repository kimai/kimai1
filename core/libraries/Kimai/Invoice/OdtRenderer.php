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

        $entries = $this->getModel()->getEntries();
        $doc->mergeXmlBlock('row', $entries);

        $doc->saveXml();
        $doc->close();

        // send and remove the document
        $doc->sendResponse();
        $doc->remove();
    }

}
