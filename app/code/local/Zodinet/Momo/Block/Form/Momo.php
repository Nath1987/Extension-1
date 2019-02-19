<?php

class Zodinet_Momo_Block_Form_Momo extends Mage_Payment_Block_Form
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/form/momo.phtml');
    }

}
