<?php
class Emja_TaxRelief_Model_Tax_Observer
{
    public function applyTaxRelief(Varien_Event_Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $paymentData = Mage::app()->getRequest()->getParam('payment',array());
        if(array_key_exists('customer_has_tax_relief',$paymentData) && $paymentData['customer_has_tax_relief']){
            $customer = Mage::getSingleton('customer/session');
            if ($customer && Mage::helper('emja_taxrelief')->allowedForCustomer($customer)) {
                $request->setCountryId(0)
                    ->setCustomerClassId(0)
                    ->setProductClassId(0);
            }

            if (!$customer) {
                $request->setCountryId(0)
                    ->setCustomerClassId(0)
                    ->setProductClassId(0);
            }
        }

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote && $quote->getPayment() && $quote->getPayment()->getTaxReliefCode()) {
            if (Mage::helper('emja_taxrelief')->allowedForCustomer($quote->getCustomer())) {
                $request->setCountryId(0)
                    ->setCustomerClassId(0)
                    ->setProductClassId(0);
            }
        }

        return $this;
    }
	
	public function applyTaxReliefQuote(Varien_Event_Observer $observer)
    {
		$quote = $observer->getEvent()->getQuote();
		if(!Mage::getStoreConfig('emja_taxrelief/settings/allowspecific')) {
			if($quote->getPayment()->getTaxReliefCode() != '') {
				$subTotal = $quote->getSubtotal();
				$taxRate = Mage::getStoreConfig('emja_taxrelief/settings/tax_rate');
				$taxAmount = $subTotal * $taxRate / 100;
				
				foreach ($quote->getAllAddresses() as $address) {
					$address->setExtraTaxAmount($taxAmount);
					$address->setBaseExtraTaxAmount($taxAmount);
				}
			}
		} else {
			$allowedCountries = Mage::getStoreConfig('emja_taxrelief/settings/specificcountry');
			$allowedCountries = explode(',', $allowedCountries);
			
			if($quote->getPayment()->getTaxReliefCode() != '') {
				$subTotal = $quote->getSubtotal();
				$taxRate = Mage::getStoreConfig('emja_taxrelief/settings/tax_rate');
				$taxAmount = $subTotal * $taxRate / 100;
				
				foreach ($quote->getAllAddresses() as $address) {
					if(in_array($address->getCountryId(), $allowedCountries)) {
                        $address->setAppliedTaxesReset(true);
						$address->setExtraTaxAmount($taxAmount);
						$address->setBaseExtraTaxAmount($taxAmount);
					}
				}
			}
		}
		return $this;
    }
}