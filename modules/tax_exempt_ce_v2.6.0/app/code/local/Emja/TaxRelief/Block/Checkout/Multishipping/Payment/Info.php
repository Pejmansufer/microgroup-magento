<?php
class Emja_TaxRelief_Block_Checkout_Multishipping_Payment_Info extends Mage_Checkout_Block_Multishipping_Payment_Info
{
    protected function _toHtml()
    {
        return parent::_toHtml().$this->getChildHtml('emja_taxrelief_info');
    }
}