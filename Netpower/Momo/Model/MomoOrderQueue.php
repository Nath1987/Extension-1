<?php
namespace Netpower\Momo\Model;

use Magento\Framework\Model\AbstractModel;
	
/**
 * Collection to get data from sales_order_momo
 */
class MomoOrderQueue extends AbstractModel
{   
    protected function _construct()
    {
        $this->_init('Netpower\Momo\Model\ResourceModel\MomoOrderQueue');
    }
}