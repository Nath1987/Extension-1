<?php

class Zodinet_Momo_Model_Mysql4_Momo extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the entity_id refers to the key field in your database table.
        $this->_init('momo/momo', 'entity_id');
    }
}