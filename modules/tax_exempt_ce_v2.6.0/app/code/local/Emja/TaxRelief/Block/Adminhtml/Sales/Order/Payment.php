<?php
class Emja_TaxRelief_Block_Adminhtml_Sales_Order_Payment extends Mage_Adminhtml_Block_Sales_Order_Payment
{
    protected function _toHtml()
    {
        return parent::_toHtml().$this->getChildHtml('emja_taxrelief_info');
    }
    
}