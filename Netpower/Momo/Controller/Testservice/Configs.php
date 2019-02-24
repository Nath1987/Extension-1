<?php

namespace Netpower\Momo\Controller\Testservice;

use Netpower\Momo\Services\Config;
use Netpower\Momo\Services\Transport;

use Netpower\Momo\Helper\TransferAllDataConfig;

use \Magento\Framework\App\Action\Context; 

class Configs extends \Magento\Framework\App\Action\Action
{

    protected $_helper;
    protected $_transport;
    protected $_config;

    public function __construct(
        Config $config,
        TransferAllDataConfig $helper,
        Transport $transport,
        Context $context
        )
    {
        $this->_config = $config;
        $this->_helper = $helper;
        $this->_transport = $transport;
        return parent::__construct($context);
    }

    public function execute()
    {
        $dataArray = [
            'amount' => "50000",
            'orderInfo' => "pay with MoMo",
            'extraData' => "merchantName=Grab taxi;merchantId=3948"
        ];
        $configValues = $this->_helper->allDataApiRequire($dataArray);

        $dataSend = json_encode($configValues);

       $result =  $this->_transport->post('https://test-payment.momo.vn/gw_payment/transactionProcessor',$dataSend);

       var_dump($result);
    }
}