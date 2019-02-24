<?php

namespace Netpower\Momo\Controller\Testservice;

use Netpower\Momo\Services\Config;

use Netpower\Momo\Helper\TransferAllDataConfig;

use \Magento\Framework\App\Action\Context; 

class Configs extends \Magento\Framework\App\Action\Action
{

    protected $_helper;

    protected $_config;

    public function __construct(
        Config $config,
        TransferAllDataConfig $helper,
        Context $context
        )
    {
        $this->_config = $config;
        $this->_helper = $helper;
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

        var_dump($configValues);
    }

}