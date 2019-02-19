<?php
class Zodinet_Momo_Model_Queue extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('momo/queue');
    }

    public function checkOrderStatus(){
		$collection = Mage::getModel('momo/queue')
			->getCollection()
			->addFieldToFilter('status',array('like','new'));

		$transport = Mage::helper('momo/transport');

		foreach ($collection as $key => $momo) {
			$orderId = $momo->getOrderId();
			$requestId = $momo->getRequestId();

			$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
			$data = array(
				"requestId" => $requestId,
				"orderId" => $orderId,
				"requestType" => "transactionStatus",
			);

			try{
				$result = $transport->call($data);
				if($result && $result->errorCode == 0){
					$canDelete = true;
					$this->handleSuccessOrder($order,$result);
				}
				else if($result && $result->errorCode){
					$canDelete = true;
					$this->handleCancelOrder($order,$result);
				}
			}
			catch(Exception $e){
				$canDelete = false;
				$this->getHelper()->log($e);
			}

		    if($canDelete){
				$momo->delete();
			}
		}
	}

	protected function getHelper(){
		return Mage::helper('momo');
	}

	protected function handleCancelOrder($order,$result){
		if($order && $order->getStatus() == 'pending'){

			$order->getPayment()->cancel();
			$order->addStatusHistoryComment("MOMO Queue changes from pending to canceled");
	                $order->registerCancellation("Cancel from MOMO with Error Code {$result->localMessage}");

		    $order->save();	
		}
	}

	protected function handleSuccessOrder($order, $result){
		if($order && in_array($order->getStatus(),array('pending','canceled'))){
		
                    if($order->getStatus() == 'pending'){
			$order->addStatusHistoryComment("MOMO Queue changes from pending to processing");			
		    }
		    else if($order->getStatus() == Mage_Sales_Model_Order::STATE_CANCELED){
			$order->addStatusHistoryComment("MOMO Queue changes from cancel to processing");
		    }

                    $order->setData('state', "processing");
		    $order->setStatus("processing");       

		    $payment = $order->getPayment();
		    $payment->setTransactionId($result->requestId)
			            ->setCurrencyCode($order->getBaseCurrencyCode())
			            ->registerCaptureNotification($result->amount);

       	            $details = (array)$result;

	            if (!empty($details)) {
	                $payment->setAdditionalData(serialize($details));
	            }

		    $order->save();	

		    if ($order->getId()) {
                        $order->queueNewOrderEmail();
                    }

		}
	}
}
