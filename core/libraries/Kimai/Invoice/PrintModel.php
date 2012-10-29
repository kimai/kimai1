<?php
/**
 * A data model holding everything to render an invoice.
 *
 * @author Kevin Papst
 */
class Kimai_Invoice_PrintModel
{
    /**
     * @var array
     */
    private $entries = array();

    /**
     * @param array $entries
     */
    public function setEntries(array $entries)
    {
        $this->entries = $entries;
    }

    /**
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }

}
