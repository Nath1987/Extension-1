<?php

namespace Netpower\Momo\Helper;

use Netpower\Momo\Services\Config;
use Netpower\Momo\Services\Transport;

class TransferAllDataConfig 
{

    /** 
     * @var Netpower\Momo\Services\Config;
     */
    protected $_config;

    /** 
     * @param Netpower\Momo\Services\Config;
     */
    public function __construct(
        Config $config
    ) {
        $this->_config = $config;
    }

     /** 
     * @param array     : include [amount, orderInfo, extraData]
     * @return array    : all add that needed to send to API require [partnerCode, accessKey, requestId, amount, orderId, orderInfo, returnUrl, notifyUrl, requestType, extraData, signature]
     */
    public function allDataApiRequire($dataArray)
    {   
        $dataConfigs = $this->_config->getConfigValues();
        
        $dataConfigs['amount'] = $dataArray['amount'];
        $dataConfigs['orderInfo'] = $dataArray['orderInfo'];
        $dataConfigs['extraData'] = $dataArray['extraData'];
        $dataConfigs['orderId'] = time()."";
        $dataConfigs['requestId'] = time()."";
        $dataConfigs['returnUrl'] = "https://momo.vn/return"; 
        $dataConfigs['notifyUrl'] = "https://dummy-url.vn/notify";
        $dataConfigs['requestType'] = "captureMoMoWallet"; 

        return $dataConfigs;
        // $signature = $this->calculateSignature($dataConfigs);

        // $dataConfigs['signature'] = $signature;

        // return $dataConfigs;

    }

    /** 
     * @param array     : all data is required by API.
     * @return string   : signature
     */
    public function calculateSignature($dataArray)
    {

    }   
}