<?php

namespace Netpower\Momo\Observer;

use \Netpower\Momo\Services\Config;

class SolveUrl implements \Magento\Framework\Event\ObserverInterface
{      

    protected $_config;

    public function __construct(Config $config)
    {
        $this->_config = $config;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
        var_dump('Observer');
        die;
        var_dump($this->config->getModel());
        $order = $observer->getEvent()->getOrder();
        echo $orderId = $order->getId();
        $comment = $this->getRequest()->getParam('comment');
        print_r("Catched event succssfully !"); exit;
    }
}

