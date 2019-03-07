<?php

namespace Netpower\Momo\Model\ResourceModel;


use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class MomoOrderQueue extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('sales_order_momo_queue', 'id'); //id is a primary key 
    }
}