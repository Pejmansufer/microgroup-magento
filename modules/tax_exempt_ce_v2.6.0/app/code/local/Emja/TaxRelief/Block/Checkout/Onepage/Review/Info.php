<?php
class Emja_TaxRelief_Block_Checkout_Onepage_Review_Info extends Mage_Checkout_Block_Onepage_Review_Info
{
    public function getPayment()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getPayment();
    }
}