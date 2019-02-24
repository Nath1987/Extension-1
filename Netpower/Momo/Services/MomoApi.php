<?php

namespace Netpower\Momo\Services;

class MomoApi 
{
    /**
	 *  @var \Netpower\Momo\Services\Transport;
	 */
    protected $_transport;

    /**
     * CONSTRUCTOR
	 *  @param \Netpower\Momo\Services\Transport;
	 */
    public function __construct(Transport $transport)
    {   
        $this->_transport = $transport;
    }

    /**
	 *@param $dataJson  : Data use to send to Momo API
	 *@return $pay information include payURL
	 */
    public function getInforAllInOnePayment($dataJson)
    {
        return $this->_transport->post($dataJson);
    }
}