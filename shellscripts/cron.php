<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2009 Kimai-Development-Team
 * Kimai is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; Version 3, 29 June 2007
 * Kimai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with Kimai; If not, see <http://www.gnu.org/licenses/>.
 */

// Check that we are executed from command line
if (PHP_SAPI !== 'cli') {
    echo 'This file are intended to be executed from terminal as a PHP CLI script.' . "\n";
    // Exit with an error code
    exit(1);
}

defined('WEBROOT') || define('WEBROOT', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);

require WEBROOT . 'includes/basics.php';

// Manage PHP error reporting
// Give all php notice and error messages
error_reporting(E_ALL);
// Will overwrite the ini setting again which was set in basics.php
ini_set('display_errors', 1);

try {
    $opts = new Zend_Console_Getopt([
        'help|h' => 'Displays usage information.',
        'verbose|v' => 'Verbose messages will be dumped to the default output.',
    ]);
    $opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    exit($e->getMessage() . "\n\n" . $e->getUsageMessage());
}

if (isset($opts->h)) {
    echo $opts->getUsageMessage();
    exit;
}

$database = Kimai_Registry::getDatabase();
$config = Kimai_Registry::getConfig();

$view = new Zend_View();
$view->setBasePath(WEBROOT . 'templates');
$view->assign('kga', $config);

$timeFrame = get_timeFrameOfYesterday();
[$in, $out] = $timeFrame;

$filePath = WEBROOT . 'skins/' . $config->getSkin() . '/grfx/g3_logo.png';
$fileName = basename($filePath);
$fileType = getimagesize($filePath);

$customers = $database->get_customers();
if ($customers === false) {
    echo 'Error while fetching customers.';
    exit(1);
}
foreach ($customers as $customer) {
    $customerData = $database->customer_get_data($customer['customerID']);
    $customerMail = $customerData['mail'];

    if ($customerData['cron_job_active'] == 1 && $customerMail != '') {
        // Get the array of timesheet entries.
        $timeSheetEntries = $database->get_timeSheet($in, $out, null, [$customer['customerID']]);
        if (count($timeSheetEntries) > 0) {
            $view->assign('timeSheetEntries', $timeSheetEntries);

            $mail = new Zend_Mail('utf-8');
            $mail->setType(Zend_Mime::MULTIPART_RELATED);

            $transport = new Zend_Mail_Transport_Smtp($config['smtp_host'], [
                'ssl' => 'tls',
                'port' => $config['smtp_port'],
                'auth' => 'login',
                'username' => $config['smtp_user'],
                'password' => $config['smtp_pass']
            ]);

            $att = $mail->createAttachment(file_get_contents($filePath));
            $att->type = $fileType['mime'];
            $att->disposition = Zend_Mime::DISPOSITION_INLINE;
            $att->encoding = Zend_Mime::ENCODING_BASE64;
            $att->filename = $fileName;
            $att->id = md5($att->filename);

            // Embed Images
            $view->assign('embed', ['logo' => 'cid:' . $att->id]);

            $output = $view->render('shellscripts/timeSheet.php');

            #$mail->setBodyText(''); //TODO: no plain Text available yet
            $mail->setBodyHtml($output)
                 ->setFrom($config->getAdminEmail(), 'Kimai - Open Source Time Tracking')
                 ->addTo($customerMail)
                 ->addBcc($config->getAdminEmail())
                 ->setSubject($config['lang']['extensions']['ki_timesheet'] . ' ' . strftime($config->getDateFormat(2), $in))
                 ->send($transport);

            if (isset($opts->v)) {
                Kimai_Logger::logfile('Mail sent to: ' . $customerMail);
            }
        }
    }
}

/**
 * @return array
 */
function get_timeFrameOfYesterday()
{
    $config = Kimai_Registry::getConfig();

    $d = new DateTime('-1 day', new DateTimeZone($config['defaultTimezone']));
    $d->setTime(0, 0);

    $timeFrame = [];
    $timeFrame[0] = $d->format('U');

    $d->setTime(23, 59, 59);
    $timeFrame[1] = $d->format('U');

    return $timeFrame;
}
