<?php 

namespace Netpower\Momo\Helper;

use Netpower\Momo\Services\Config;
use Netpower\Momo\Services\Cons;
use Netpower\Momo\Services\Transport;

class ConfirmOrder
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
     * @var Netpower\Momo\Services\Transport
     */
    protected $_transport;

    /**
     * @param Netpower\Momo\Services\Config $config;
     * @param Netpower\Momo\Helper\TransferAllDataConfig $transfer;
     * @param Netpower\Momo\Services\Transport $transport;
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
     * @param Array []
     * 
     * @return Array []
     */
    public function confirm($dataArray)
    {
        $secrectKey = $this->_config->getSecretKey();
        $signature = $this->_transfer->calculateSignature($dataArray, $secrectKey);
        $dataArray['signature'] = $signature;

        $dataJSON = json_encode($dataArray);

        $urlEndpoint  = $this->_config->getUrlEndPoint() . Cons::CONFIRM_ORDER_PATH;

        try {
            $result = $this->_transport->post($urlEndpoint, $dataJSON);
        } catch(Exception $e) {
            // FAIL API REQUEST
        }
        return $result;
    }
}