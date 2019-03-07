<?php

namespace Netpower\Momo\Model\ResourceModel;


use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class MomoOrder extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('sales_order_momo', 'entity_id'); //id is a primary key 
    }
}