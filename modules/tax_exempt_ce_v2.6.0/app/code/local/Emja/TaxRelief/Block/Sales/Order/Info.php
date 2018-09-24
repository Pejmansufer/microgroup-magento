<?php
class Emja_TaxRelief_Block_Sales_Order_Info extends Mage_Sales_Block_Order_Info
{
    public function getPaymentInfoHtml()
    {
        return parent::getPaymentInfoHtml().$this->getChildHtml('emja_taxrelief_info');
    }
}