<?php
class Zodinet_Momo_Model_Momo extends Mage_Payment_Model_Method_Abstract
{

    protected $_code  = 'momo';
    protected $_formBlockType = 'momo/form_momo';
    protected $_infoBlockType = 'momo/info_momo';

    protected $_canAuthorize                = false;
    protected $_canCapture                  = false;
    protected $_canCapturePartial           = false;
    protected $_canCaptureOnce              = false;
    protected $_canRefund                   = true;

    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('momo/index/redirect', array('_secure' => true));
    }

    public function assignData($data)
    {
        $details = array();

        $info = $this->getInfoInstance();
        
        if (!empty($details)) {
            $this->getInfoInstance()->setAdditionalData(serialize($details));
        }
        return $this;
    }



    public function refund(Varien_Object $payment, $amount) {   
        $additionData = $payment->getData('additional_data');//getAdditionData();
        $additionData = unserialize($additionData);
		Mage::log("aaaaaaaaaaaaaa",null,"momo.log");
        if(!$additionData){
			Mage::log("Could not parse addition data",null,"momo.log");
            throw new Exception("Could not parse addition data", 1);
        }

        $requestId = $additionData['requestId'];
        $transId = $additionData['transId'];
        $orderId = $payment->getOrder()->getIncrementId();

        $data = array(
            "requestId"     => $requestId,
            "amount"        => intval($amount) . "",
            "orderId"       => $orderId,
            "transId"       => $transId,
            "requestType"   => "refundMoMoWallet",
        );

        try{
            $transport = Mage::helper('momo/transport');
            $result = $transport->call($data);
        }
        catch(Exception $e){
			Mage::logException($e);
            throw $e;
        }

        if(!$result){
			Mage::log("Could not parse response refund from MOMO",null,"momo.log");
            throw new Exception("Could not parse response from MOMO", 1);
        }

        if($result->errorCode != 0){
			Mage::log($result->localMessage,null,"momo.log");
            throw new Exception($result->localMessage, 1);
        }

        return parent::refund($payment, $amount);
    }

    // public function capture(\Varien_Object $payment, $amount) {
    //     return parent::capture($payment, $amount);
    // }   

    public function capture(\Varien_Object $payment, $amount) {
        return parent::capture($payment, $amount);
    }   


    public function processInvoice($invoice, $payment) {
        return parent::processInvoice($invoice, $payment);
    }
}
