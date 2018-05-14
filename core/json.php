<?php
/**
 * This file is part of
 * Kimai - Open Source Time Tracking // https://www.kimai.org
 * (c) 2006-2009 Kimai-Development-Team
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
 * This file is the JSON interface to Kimai to be used by
 * external APPs to allow remote access.
 *
 * Please read the following page to know how this server works:
 * https://framework.zend.com/manual/1.12/en/zend.json.server.html
 *
 * @author Kevin Papst <kpapst@gmx.net>
 */

// Bootstrap Kimai
require(dirname(__FILE__) . '/../includes/basics.php');

header('Access-Control-Allow-Origin: *');

$server = new Zend_Json_Server();
$server->setClass('Kimai_Remote_Api');

if ('GET' == $_SERVER['REQUEST_METHOD']) {
    // Indicate the URL endpoint and the JSON-RPC version used

    /* @var Zend_Json_Server_Smd $server */
    $server->setTarget('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])
           ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);

    // Grab the SMD
    /* @var Zend_Json_Server $server */
    $smd = $server->getServiceMap();

    // Return the SMD to the client
    header('Content-Type: application/json');
    echo $smd;
    return;
}

/**
 * http request will
 *  - parse php://input
 *  - json_decode it
 *  - auto setOptions
 * therefore request should be a string e.g. {jsonrpc : '2.0', method: '<actionString>', params : [param1, param2], id : '<anyId>' }
 */
$request = new Zend_Json_Server_Request_Http();
$server->setRequest($request);
$server->handle();
