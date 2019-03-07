<?php
namespace Netpower\Momo\Model\ResourceModel\MomoOrderQueue;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
        'Netpower\Momo\Model\MomoOrderQueue',
        'Netpower\Momo\Model\ResourceModel\MomoOrderQueue'
    	);
    }
}