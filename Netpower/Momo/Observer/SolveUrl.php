<?php

namespace Netpower\Momo\Observer;

use \Netpower\Momo\Services\Config;
use \Netpower\Momo\Services\Transport;

use \Netpower\Momo\Helper\TransferAllDataConfig;


class SolveUrl implements \Magento\Framework\Event\ObserverInterface
{      

    /**
     * @var \Netpower\Momo\Services\Config
     */
    protected $_config;

    /**
     * @var \Netpower\Momo\Helper\TransferAllDataConfig
     */
    protected $_transfer;

    /**
     * @var \Netpower\Momo\Services\Transport
     */
    protected $_transport;
    /**
     * @param \Netpower\Momo\Services\Config $config
     * @param \Netpower\Momo\Helper\TransferAllDataConfig $transfer
     * @param \Netpower\Momo\Services\Transport $transport
     */
    public function __construct(
        Config $config,
        TransferAllDataConfig $transfer,
        Transport $transport
    )
    {
        $this->_config = $config;
        $this->_transfer = $transfer;
        $this->_transport = $transport;
    }

     /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {      
        $order = $observer->getEvent()->getOrder();

        $requestId = $orderId = time()."";

        $amount = $this->_transfer->getOrderTotal();


        $shippingAddress = $order->getShippingAddress();
        $firstname = $shippingAddress->getData('firstname');
        $lastname = $shippingAddress->getData('lastname');

        $amount = $amount*10000;
        $dataArray = [
            'amount' => (string)$amount,
            'requestId' => $requestId,
            'orderId' => $orderId,
            'extraData' => "firstname=$firstname;lastname=$lastname"
        ];

        $configValues = $this->_transfer->allDataApiRequire($dataArray);

        $dataSend = json_encode($configValues);


       $result =  $this->_transport->post('https://test-payment.momo.vn/gw_payment/transactionProcessor',$dataSend);

       $result = json_decode($result, true);

       $url = $result['payUrl'];

       //return $this->resultRedirectFactory->create()->setUrl($url);
    }
}

