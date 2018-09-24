<?php
class Emja_TaxRelief_Block_Sales_Order_Payment_Info extends Mage_Core_Block_Template
{
    protected function _getOrder()
    {
        return Mage::registry('current_order');
    }
    protected function _beforeToHtml()
    {
        $this->setPayment($this->_getOrder()->getPayment());
        return parent::_beforeToHtml();
    }
}