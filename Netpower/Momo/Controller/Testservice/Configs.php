<?php

namespace Netpower\Momo\Controller\Testservice;

use Netpower\Momo\Services\Config;
use Netpower\Momo\Services\Transport;

use Netpower\Momo\Helper\TransferAllDataConfig;

use \Magento\Framework\App\Action\Context; 

use \Magento\Checkout\Model\Session;

class Configs extends \Magento\Framework\App\Action\Action
{

    protected $_helper;
    protected $_transport;
    protected $_config;
    protected $_session;

    public function __construct(
        Config $config,
        TransferAllDataConfig $helper,
        Transport $transport,
        Context $context,
        Session $session
        )
    {
        $this->_config = $config;
        $this->_helper = $helper;
        $this->_transport = $transport;
        $this->_session = $session;
        return parent::__construct($context);
    }

    public function execute()
    {
       $url = $this->_session->getPayUrl();
       return $this->resultRedirectFactory->create()->setUrl($url);
    }
}