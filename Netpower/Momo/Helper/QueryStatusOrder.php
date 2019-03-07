<?php

namespace Netpower\Momo\Helper;

use Netpower\Momo\Services\Cons;
use Netpower\Momo\Services\Config;

use Netpower\Momo\Services\Transport;

class QueryStatusOrder 
{
    /**
     * @var Netpower\Momo\Services\Config
     */
    protected $_config;

    /**
     * @var Netpower\Momo\Helper\TransferAllDataConfig 
     */
    protected $_transfer;

    /**
     * @var Netpower\Momo\Servies\Transport
     */
    protected $_transport;

    /**
     * @param  Netpower\Momo\Services\Config $config;
     * @param Netpower\Momo\Helper\TransferAllDataConfig $transfer;
     * @param Netpower\Momo\Servies\Transport $transport;
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

    public function queryStatus($momoId)
    {
       $partnerCode =  $this->_config->getMerchantnentId();
       $version = Cons::VERSION;

        $dataArray = [
            'partnerCode' => $partnerCode,
            'partnerRefId' => $momoId,
            'requestId' => $momoId
        ];

        $hashValue = $this->_transfer->calHashValue($dataArray);

          $dataRequest = [
            "partnerCode" => $partnerCode,
            "partnerRefId" => $momoId,
            "hash" => $hashValue,
            "version" => $version
          ];

          $dataJSON = json_encode($dataRequest);

          $urlEndpoint = $this->_config->getUrlEndPoint() . Cons::INFOR_ORDER_PATH;
          try {
            $result = $this->_transport->post($urlEndpoint, $dataJSON);
          } catch(Exception $e) {
            // REQUEST API FAIL
          }
          return $result;
    }
}