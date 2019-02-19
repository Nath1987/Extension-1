<?php

class Zodinet_Momo_Model_Observer{
	public function checkOrderStatus(){
		Mage::getModel('momo/queue')->checkOrderStatus();
	}
}