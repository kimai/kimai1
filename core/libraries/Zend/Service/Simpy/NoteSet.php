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
 * @version    $Id: NoteSet.php 23775 2011-03-01 17:25:24Z ralph $
 */


/**
 * @see Zend_Service_Simpy_Note
 */
require_once 'Zend/Service/Simpy/Note.php';


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Simpy
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Simpy_NoteSet implements IteratorAggregate
{
    /**
     * List of notes
     *
     * @var array of Zend_Service_Simpy_Note objects
     */
    protected $_notes;

    /**
     * Constructor to initialize the object with data
     *
     * @param  DOMDocument $doc Parsed response from a GetNotes operation
     * @return void
     */
    public function __construct(DOMDocument $doc)
    {
        $xpath = new DOMXPath($doc);
        $list = $xpath->query('//notes/note');
        $this->_notes = array();

        for ($x = 0; $x < $list->length; $x++) {
            $this->_notes[$x] = new Zend_Service_Simpy_Note($list->item($x));
        }
    }

    /**
     * Returns an iterator for the note set
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_notes);
    }

    /**
     * Returns the number of notes in the set
     *
     * @return int
     */
    public function getLength()
    {
        return count($this->_notes);
    }
}
