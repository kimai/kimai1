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
 * Class for rendering LaTeX invoices templates as PDF.
 *
 * @author Gustav Johansson
 */
class Kimai_Invoice_LaTeXRenderer extends Kimai_Invoice_AbstractRenderer
{
    const FILE_TEX = 'invoice.tex';

    /**
     * Render the invoice.
     *
     * @return mixed
     */
    public function render()
    {
        global $kga;

        Kimai_Logger::logfile('Rendering LaTeX invoice!');
        $tempDir = $this->getTemporaryDirectory();
        $dateFormat = '%B %e, %Y';
        $templateDir = $this->getTemplateDir() . $this->getTemplateFile();

        //Load the ini file
        $iniFile = $templateDir . '/invoice.ini';
        $iniArray = parse_ini_file($iniFile, true);

        // fetch variables from model to get values
        $customer = $this->getModel()->getCustomer();
        $projects = $this->getModel()->getProjects();
        $entries = $this->getModel()->getEntries();
        $data = $this->getModel()->toArray();

        //Create invoiceId
        $in = time();
        $invoiceID = date('y', $in) . $customer['customerID'] . date('m', $in) . date('d', $in);
        $checksum = new Kimai_Invoice_Checksum();
        $invoiceID = $checksum->generateChecksum('OCR', $invoiceID, true);
        Kimai_Logger::logfile('invoiceID: ' . $invoiceID);
        $this->getModel()->setInvoiceId($invoiceID);

        $data = $this->getModel()->toArray();

        //Write the header/footer data
        $file = $tempDir . '/info.tex';
        $handle = fopen($file, 'w');
        $content = '';
        $content .= "\\def\\duedate{" . strftime($dateFormat, $data['dueDate']) . "}%\n";
        $content .= "\\def\\total{" . $data['amount'] . "}%\n";
        $content .= "\\def\\invoiceID{" . $data['invoiceId'] . "}%\n";
        $content .= "\\def\\currency{" . $data['currencySign'] . "}%\n";
        $content .= "\\def\\companyName{" . $customer['company'] . "}%\n";
        $content .= "\\def\\companyAddress{" . $customer['street'] . "\\\\" . $customer['zipcode'] . " " . $customer['city'] . "}%\n";
        $content .= "\\def\\companyPhone{" . $customer['phone'] . "}%\n";
        $content .= "\\def\\companyEmail{" . $customer['mail'] . "}%\n";
        $content .= "\\def\\comment{" . $customer['comment'] . "}%\n";
        $content .= "\\def\\startDate{" . strftime($dateFormat, $data['beginDate']) . "}%\n";
        $content .= "\\def\\endDate{" . strftime($dateFormat, $data['endDate']) . "}%\n";
        $content .= "\\def\\vatRate{" . $data['vatRate'] . "}%\n";
        $content .= "\\def\\vat{" . $data['vat'] . "}%\n";
        $content .= "\\def\\gtotal{" . $data['total'] . "}%\n";
        $content .= "\\endinput";
        fwrite($handle, $content);
        fclose($handle);

        //Write the table
        $file = $tempDir . '/data.tex';
        $handle = fopen($file, 'w');
        $content = '';
        foreach ($entries as $row) {
            $table_row = "\\product";
            foreach ($iniArray['table'] as $index) {
                $table_row = $table_row . '{' . $row[$index] . '}';
            }
            $table_row = $table_row . "%\n";
            $content .= $table_row;
        }
        $content .= "\\endinput";
        fwrite($handle, $content);
        fclose($handle);

        //Copy all the neccessary files to the rendering directory
        copy($templateDir . '/invoice.tex', $tempDir . '/' . $data['invoiceId'] . '.tex');
        foreach ($iniArray['files'] as $file) {
            copy($templateDir . '/' . $file, $tempDir . '/' . $file);
        }

        //Run pdflatex, throw error if not!
        $output = exec('cd ' . $tempDir . ' && ' . $kga['LaTeXExec'] . ' ' . $data['invoiceId'] . '.tex');
        if (strlen($output) == 0) {
            Kimai_Logger::logfile('Could not execute pdflatex. Check your installation!');
            return;
        }
        //Run pdflatex again, throw error if not!
        $output = exec('cd ' . $tempDir . ' && ' . $kga['LaTeXExec'] . ' ' . $data['invoiceId'] . '.tex');

        //Return the rendered file
        $this->sendResponse($tempDir . '/' . $data['invoiceId'] . '.pdf');
    }

    /**
     * @return null
     */
    public function sendResponse($data)
    {
        Kimai_Logger::logfile('File to send: ' . $data);
        header('Content-Type: pdf');
        header('Content-Disposition: attachment; filename="' . basename($data) . '"');
        header('Content-Length: ' . filesize($data));
        ob_clean();
        flush();
        readfile($data);
        exit;
    }

    /**
     * @return string
     */
    protected function getTemplateFilename()
    {
        return self::FILE_TEX;
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

        return (is_file($this->getTemplateDir() . $this->getTemplateFile() . DIRECTORY_SEPARATOR . self::FILE_TEX));
    }
}
