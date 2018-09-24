<?php
class Emja_TaxRelief_Block_Adminhtml_Sales_Order_Create_Billing_Method_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Billing_Method_Form
{
    public function getPayment()
    {
        $payment = $address = $this->getQuote()->getPayment();
        return $payment instanceof Mage_Sales_Model_Quote_Payment ? $payment : Mage::getModel('sales/quote_payment'); 
    }
    public function getAddress()
    {
        $address = $this->getQuote()->getShippingAddress();
        return $address instanceof Mage_Sales_Model_Quote_Address ? $address : Mage::getModel('sales/quote_address'); 
    }
    
    public function getTaxReliefCode()
    {
        if (!$this->getQuote()->getPayment()->getTaxReliefCode()) {
            return $this->getQuote()->getCustomer()->getResource()->getAttribute('tax_exempt_number')
                ->getFrontend()->getValue($this->getQuote()->getCustomer());
        }
        return $this->getQuote()->getPayment()->getTaxReliefCode();
    }
    public function getTaxReliefState()
    {
        return $this->getQuote()->getPayment()->getTaxReliefState();
    }
    public function customerHasTaxRelief()
    {
//        $payment = Mage::app()->getRequest()->getPost('payment', array());
//        if (array_key_exists('customer_has_tax_relief', $payment) && $payment['customer_has_tax_relief'] == 0) {
//            return false;
//        }
        return $this->getPayment()->getCustomerHasTaxRelief();
    }
}