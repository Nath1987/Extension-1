<?php
namespace Netpower\Momo\Cron;

use Netpower\Momo\Helper\OrderProblemQueue;

class Momo 
{
    protected $_orderProblemQueue;

    public function __construct(
        OrderProblemQueue $orderProblemQueue
    ) 
    {
        $this->_orderProblemQueue = $orderProblemQueue;
    }
 
   public function execute()
   {
      $this->_orderProblemQueue->checkOrderStatus();
   }
}