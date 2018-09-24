<?php
class Emja_TaxRelief_Block_Checkout_Onepage_Payment_Additional extends Mage_Checkout_Block_Onepage_Billing
{
    const STATE_NOT_REQUIRED = 0;
    const STATE_REQUIRED = 1;
    const STATE_PER_DESIGN = 2;

    public function getAddress() {
        return $this->getQuote()->getShippingAddress() instanceof Mage_Sales_Model_Quote_Address ? $this->getQuote()->getShippingAddress():Mage::getModel('sales/quote_address');
    }

    public function isTaxReliefCodeRequired()
    {
        return Mage::getStoreConfigFlag('emja_taxrelief/options/is_require_tax_number');
    }

    public function isTaxReliefStateRequired()
    {
        $isRequire = Mage::getStoreConfig('emja_taxrelief/options/is_require_tax_state');

        switch($isRequire) {
            case 0:
                return self::STATE_NOT_REQUIRED;
            case 1:
                return self::STATE_REQUIRED;
            case 2:
                return self::STATE_PER_DESIGN;
        }
        return false;
    }

    protected function _toHtml()
    {
        if (Mage::helper('emja_taxrelief')->allowedForCustomer($this->getQuote()->getCustomer())) {
            return parent::_toHtml();
        }
        return '';
    }

    public function getTaxReliefCode()
    {
        $customer = $this->getQuote()->getCustomer();
        if ($customer) {
            return $customer->getResource()->getAttribute('tax_exempt_number')
                ->getFrontend()->getValue($customer);
        }
        return '';
    }
}