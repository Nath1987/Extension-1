<?php

class Zodinet_Momo_Model_Mysql4_Alepay_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('momo/momo');
    }
}