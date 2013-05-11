<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Simpy
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: WatchlistSet.php 23775 2011-03-01 17:25:24Z ralph $
 */


/**
 * @see Zend_Service_Simpy_Watchlist
 */
require_once 'Zend/Service/Simpy/Watchlist.php';


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Simpy
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Simpy_WatchlistSet implements IteratorAggregate
{
    /**
     * List of watchlists
     *
     * @var array of Zend_Service_Simpy_Watchlist objects
     */
    protected $_watchlists = array();

    /**
     * Constructor to initialize the object with data
     *
     * @param  DOMDocument $doc Parsed response from a GetWatchlists operation
     * @return void
     */
    public function __construct(DOMDocument $doc)
    {
        $xpath = new DOMXPath($doc);
        $list = $xpath->query('//watchlists/watchlist');

        for ($x = 0; $x < $list->length; $x++) {
            $this->_watchlists[$x] = new Zend_Service_Simpy_Watchlist($list->item($x));
        }
    }

    /**
     * Returns an iterator for the watchlist set
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_watchlists);
    }

    /**
     * Returns the number of watchlists in the set
     *
     * @return int
     */
    public function getLength()
    {
        return count($this->_watchlists);
    }
}
