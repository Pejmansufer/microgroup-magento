<?php

class Emja_TaxRelief_Block_Customer_Account extends Mage_Core_Block_Template
{
    /**
     * Get customer
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    /**
     *
     */
    protected function _toHtml()
    {
        if (Mage::helper('emja_taxrelief')->allowedForCustomer($this->getCustomer())) {
            return parent::_toHtml();
        }
        return '';
    }

    public function getTaxNumberLabel()
    {
        return $this->helper('emja_taxrelief')->__(Mage::getStoreConfig('emja_taxrelief/labels/tax_number_label'));
    }

    public function getAboveTaxNumber()
    {
        return Mage::getStoreConfig('emja_taxrelief/labels/above_tax_number');
    }

    public function getAboveTaxState()
    {
        return Mage::getStoreConfig('emja_taxrelief/labels/above_tax_state');
    }

    public function getTaxStateLabel()
    {
        return $this->helper('emja_taxrelief')->__(Mage::getStoreConfig('emja_taxrelief/labels/tax_state_label'));
    }

    public function getTaxExemptNumberValue()
    {
        return $this->getCustomer()->getResource()->getAttribute('tax_exempt_number')
            ->getFrontend()->getValue($this->getCustomer());
    }

    public function getTaxExemptStateValue()
    {
        return $this->getCustomer()->getResource()->getAttribute('tax_exempt_state')
            ->getFrontend()->getValue($this->getCustomer());
    }
}