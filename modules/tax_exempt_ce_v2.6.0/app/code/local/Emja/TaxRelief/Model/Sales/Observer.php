<?php
class Emja_TaxRelief_Model_Sales_Observer
{
    public function importTaxReliefData(Varien_Event_Observer $observer)
    {
        $payment = $observer->getEvent()->getPayment();
        $data = $observer->getEvent()->getInput();
        if($data instanceof Varien_Object && $data->getCustomerHasTaxRelief() == 1){
            $data->getTaxReliefState() ? $payment->setTaxReliefState($data->getTaxReliefState()) : $payment->setTaxReliefState('N/A');
            $data->getTaxReliefCode() ? $payment->setTaxReliefCode($data->getTaxReliefCode()) : $payment->setTaxReliefCode('N/A');
        } else {
            $payment->setTaxReliefState('');
            $payment->setTaxReliefCode('');
        }
        return $this;
    }

    public function resetTaxReliefData()
    {
        $cart = Mage::getSingleton('checkout/cart');
        if ($cart && $cart->getQuote() && $cart->getQuote()->getPayment()) {
            if ($cart->getQuote()->getPayment()->getTaxReliefState()) {
                $cart->getQuote()->getPayment()->setTaxReliefState('');
            }
            if ($cart->getQuote()->getPayment()->getTaxReliefCode()) {
                $cart->getQuote()->getPayment()->setTaxReliefCode('');
            }
            $cart->getQuote()->getPayment()->save();
        }
    }
} 

