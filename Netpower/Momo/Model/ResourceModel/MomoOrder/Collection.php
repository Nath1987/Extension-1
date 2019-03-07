<?php
namespace Netpower\Momo\Model\ResourceModel\MomoOrder;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
        'Netpower\Momo\Model\MomoOrder',
        'Netpower\Momo\Model\ResourceModel\MomoOrder'
    	);
    }
}