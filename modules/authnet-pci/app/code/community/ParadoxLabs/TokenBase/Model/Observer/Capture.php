<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 * 
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 * 
 * Want to customize or need help with your store?
 *  Phone: 717-431-3330
 *  Email: sales@paradoxlabs.com
 *
 * @category	ParadoxLabs
 * @package		TokenBase
 * @author		Ryan Hoerr <magento@paradoxlabs.com>
 * @license		http://store.paradoxlabs.com/license.html
 */

class ParadoxLabs_TokenBase_Model_Observer_Capture extends Mage_Catalog_Model_Observer
{
	/**
	 * In core, invoice is not directly accessible from the payment. What's with that?.
	 */
	public function processCapture( $observer )
	{
		$payment	= $observer->getEvent()->getPayment();
		$invoice	= $observer->getEvent()->getInvoice();
		
		if( !$payment->hasInvoice() ) {
			$payment->setInvoice( $invoice );
		}
		
		return $this;
	}
}