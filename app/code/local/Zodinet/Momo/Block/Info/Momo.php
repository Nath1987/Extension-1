<?php
class Zodinet_Momo_Block_Info_Momo extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/info/momo.phtml');
    }

    public function toPdf()
    {
        $this->setTemplate('payment/info/pdf/momo.phtml');
        return $this->toHtml();
    }

}
