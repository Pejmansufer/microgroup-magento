<?php 
/** 
* Moogento
* 
* SOFTWARE LICENSE
* 
* This source file is covered by the Moogento End User License Agreement
* that is bundled with this extension in the file License.html
* It is also available online here:
* https://www.moogento.com/License.html
* 
* NOTICE
* 
* If you customize this file please remember that it will be overwrtitten
* with any future upgrade installs. 
* If you'd like to add a feature which is not in this software, get in touch
* at www.moogento.com for a quote.
* 
* ID          pe+sMEDTrtCzNq3pehW9DJ0lnYtgqva4i4Z=
* File        Functions.php
* @category   Moogento
* @package    pickPack
* @copyright  Copyright (c) 2014 Moogento <info@moogento.com> / All rights reserved.
* @license    https://www.moogento.com/License.html
*/ 

class Moogento_Pickpack_Helper_Functions extends Mage_Core_Helper_Abstract
{
	private $_clean_shipping_dirty_array = array('Select Delivery Method','Method','Select postage','Select Shipping','Delivery','- ', "\n","\r","\t",'  ','((','))',
				'Free Shipping Free','Free shipping? Free','Free Economy Free Economy','Free Economy? Free Economy',
				'Paypal Express Checkout','Pick up Pick up','Pick up at our ','Normal ','Registered',' Postage','Postage','Royal Mail','Mail','RM ',
            	'Store Pickup Store Pickup','Cash on Delivery','Courier Service','Delivery Charge','International','First','Second','2nd Class 2nd Class',
            	'1st Class 1st Class','View a UPS Ground Shipping Map Here',' rate:','Australia Post','Standard','working days with tracking','You qualify for',
            	'free ground shipping Free','United States Postal Service','United Parcel Service','UPS UPS','UPS (UPS)','Fed Ex','Federal Express',
				'Available Shipping Options','shipping','choose a day','Choose a Delivery Day','Choose a Shipping Day','Please choose a shipping',
				'Please choose',' a ','Select Your','  ','( does not exist for',' )','Options');
			
	private $_clean_shipping_replace_array = array('','','','','','',' ',' ',' ',' ','(',')','Free Shipping','Free Shipping','Free Economy','Free Economy',
            	'Paypal Express','Pick up','Pickup@','',"Reg'd",',','','RM','','Royal Mail ',
            	'Store Pickup','COD','Courier','Charge','Int\'l','1st','2nd','2nd Class',
            	'1st Class','','','AusPost','Std','working days','',
            	'free ground shipping','USPS','UPS','UPS','UPS','FedEx','FedEx','','','','','','','','','',' ','','');
			
	private $_clean_payment_dirty_array = array("\n","\r","\t",'  ',', credit card type:','credit card type:','- Account info','PayPal Secured Payment',
				'credit card number','#: ','Credit Card','American Express','Master Card',',','(Authorize.net)','Cash on Delivery','Number','Credit / Debit CardCC',
				'Credit / Debit Card','CardCC','payment gateway by','n/a','cccc','cc cc','****','****','***','***','Card: Visa','&rArr;','&amp;','sup3;','rArr','  ','#: ',
				'Purchase Order Purchase Order','Payment:','Payment Visa','Visa#','Payment Mastercard','Mastercard#','MasterCard','COD COD','Reference','Name:',
				'(saved):','Name on the card:','Pay with Paypal','#Wiped','Expiration','Date','CC, Type:','CC, Type','Pay by','Exp date','Exp. date','DebitCC',
				'CC, Type MCCC','Type MCCC','MCCC','Type','Credit or Debit Card','  ');

	private $_clean_payment_replace_array = array(' ',' ',' ',' ',':',':','','Paypal','#','#','CC','Amex','MC','','','COD','#','CC','Card','CC','','CC','CC','CC',
				'**','**','**','**','Visa','','','','','','#','Purchase Order','','Visa','Visa#','MC','MC#','MC','COD','Ref.','','','','Paypal','','Exp','','','','',
				'Exp','Exp','Debit','MC','MC','MC','','Card','');				

	private $_clean_shipping_address_dirty_array = array('Amazon does not supply the complete billing buyer information','Amazon does not supply the complete buyer information','Amazon does not supply the complete');
	
	private $_clean_shipping_address_replace_array = array('','','');

	private function clean_object($dirty) {
			if(is_object($dirty)) $dirty = strval($dirty);
			return $dirty; }

	private function pre_clean($dirty) {
			return strip_tags($this->clean_object($dirty)); }
		
	private function to_utf8($in) 
	{ 
	        if (is_array($in)) { 
	            foreach ($in as $key => $value) { 
	                $out[to_utf8($key)] = to_utf8($value); 
	            } 
	        } elseif(is_string($in)) { 
	            if(mb_detect_encoding($in) != "UTF-8")
	                return utf8_encode($in);
	            else 
	                return $in; 
	        } else { 
	            return $in; 
	        } 
	        return $out; 
	} 			

	private function clean_shipping($dirty) {
			$dirty = $this->pre_clean($dirty);
			$clean = trim(str_ireplace('Local Delivery','~localdel~',$dirty));
			$clean = trim(str_ireplace($this->_clean_shipping_dirty_array, $this->_clean_shipping_replace_array, $clean));
			$clean = preg_replace('~Charge:$~i','',$clean);
			$clean = preg_replace('~^\s*~','',$clean);
			$clean = trim(preg_replace('~^shipping~i','',$clean));
			$clean = preg_replace('~^\-~','',$clean);
			$clean = preg_replace('~^\:~','',$clean);
			$clean = trim(preg_replace('~^via\s~i','',$clean));
			$clean = preg_replace('~,$~i','',$clean);
			$clean = trim(str_ireplace('~localdel~', 'Local Delivery',$clean));
			// if same name has been configured as title and method, this will trim out the repetition
			$clean_length = strlen($clean);
			$s_a = trim(substr($clean,0,(round($clean_length/2) - 1)));
			$s_b = trim(substr($clean,(round($clean_length/2)),$clean_length));
			if($s_a == $s_b) $clean = $s_a;
			return $clean;
	}

	private function clean_payment($dirty, $trim_card = false) {
			$dirty = $this->pre_clean($dirty);
		
			$dirty_array = explode('{{pdf_row_separator}}', $dirty);
			foreach ($dirty_array as $key=>$value)
			{
				if (strip_tags(trim($value))=='')
				{
					unset($dirty_array[$key]);
				}
			}
			$clean = implode(',',$dirty_array);
			unset($dirty);
			unset($dirty_array);

			$clean = trim(str_ireplace($this->_clean_payment_dirty_array, $this->_clean_payment_replace_array, $clean));

			$clean = preg_replace('~^\s*~','',$clean);
			$clean = trim(preg_replace('~^:~','',$clean));
			$clean = preg_replace('~Paypal(.*)$~i','Paypal',$clean);
			$clean = preg_replace('~Account(.*)$~i','Account',$clean);
			$clean = preg_replace('~Processed Amount(.*)$~i','',$clean);
			$clean = preg_replace('~Payer Email(.*)$~i','',$clean);
			$clean = preg_replace('~Charge:$~i','',$clean);
			$clean = preg_replace('~Order was placed(.*)$~i','',$clean);
			
			
			if($trim_card === true)
			{
				$clean = preg_replace('~Expiration(.*)$~i','',$clean);
				$clean = str_ireplace('Name on the Card','Name',$clean);
			}
			else
			{
				$clean = str_ireplace('Expiration','|Expiration',$clean);
				$clean = str_ireplace('Name on the Card','|Name on the Card',$clean);
			}
			$clean = preg_replace('~^\-~','',$clean);
			$clean = preg_replace('~Check / Money order(.*)$~i','Check / Money order',$clean);
			$clean = preg_replace('~Cheque / Money order(.*)$~i','Cheque / Money order',$clean);
			$clean = preg_replace('~Make cheque payable(.*)$~i','',$clean);
			$clean = str_ireplace(
			array('CardCC','CC Type','MasterCardCC','MasterCC',': MC',': Visa','Payment Visa','Payment MC','CCAmex','AmexCC','Type: Amex','CC Exp.','CC (Sage Pay)CC'),
			array('CC','CC, Type','MC','MC',' MC',' Visa','Visa','MC','Amex','Amex','Amex','Exp.','(Sage Pay)'),$clean);
			$clean = preg_replace('~:$~','',$clean);

			if($trim_card === true)
			{
				preg_match('~\b(?:\d[ -]*?){13,16}\b~',$clean,$cc_matches);
				if(isset($cc_matches[0]))
				{
					$replacement_cc = str_pad(substr($cc_matches[0], -4), 8, '*', STR_PAD_LEFT);
					$clean = str_replace($cc_matches[0],$replacement_cc,$clean);
				}
			}

			$clean = trim($clean);

			// if same name has been configured as title and method, this will trim out the repetition
			$payment_method_length = strlen($clean);
			$p_a = trim(substr($clean,0,(round($payment_method_length/2))));
			$p_b = trim(substr($clean,(round($payment_method_length/2)),$payment_method_length));

			if($p_a == $p_b && (strlen($p_a) > 4)) $clean = $p_a;

			// CC Visa #****2889
			$clean = preg_replace('~^(.*)Visa~i','Visa',$clean);


			return $clean;
	}	
	
	private function clean_amazon_billing_notice($dirty) {		
			$clean = trim(str_ireplace($this->_clean_shipping_address_dirty_array, $this->_clean_shipping_address_replace_array, $dirty));
			return $clean;
	}

	private function clean_text($dirty) {
			$dirty = $this->clean_object($dirty);
			$clean = str_replace(array("\n","\r",'<br/>','<br>','<br />'),'~',$dirty);//,'\n','\r'
			$clean = strip_tags($clean);
			$clean = $this->to_utf8($clean);
			$clean = str_replace('~',"\n",$clean);
			return $clean;
	}
	
	private function clean_pdf($dirty) {
			$dirty = $this->clean_object($dirty);
			$clean = str_replace(array("\n","\r","<br/>","<br>","<br />","*","(",')','~'),'',$dirty);
			$clean = strip_tags($clean);
			$clean = str_replace('~',"\n",$clean);
			$clean = $this->clean_amazon_billing_notice($clean);
			return $clean;
	}  
	
	private function clean_pdf_more($dirty) {
			$clean = $this->clean_pdf($dirty);
			$clean = preg_replace('/[^\x20-\x7E]/','', $clean);
			//$clean = iconv("UTF-8","ISO-8859-1//TRANSLIT",$clean);
			//$clean = preg_replace('/[x00-x08x10x0Bx0Cx0E-x19x7F]|[x00-x7F][x80-xBF]+|([xC0xC1]|[xF0-xFF])[x80-xBF]*|[xC2-xDF]((?![x80-xBF])|[x80-xBF]{2,})|[xE0-xEF](([x80-xBF](?![x80-xBF]))|(?![x80-xBF]{2})|[x80-xBF]{3,})/S','', $clean);
			
			//$clean = utf8_encode($clean);
			//$clean = iconv("ISO-8859-1","UTF-8//IGNORE",$clean);
			//$clean = iconv("ASCII","UTF-8//IGNORE",$clean);
			$clean = $this->to_utf8($clean);
			return $clean;
	}
	
	
	public function clean_method($dirty,$method='text') {
	
		switch ($method) {
			case 'shipping':
				$clean = $this->clean_shipping($dirty);
				break;

			case 'payment':
			    $clean = $this->clean_payment($dirty,true);
				break;	

			case 'payment-full':
				$clean = $this->clean_payment($dirty,false);
				break;						

			case 'text':
				$clean = $this->clean_text($dirty);
				break;	
				
			case 'pdf':
				$clean = $this->clean_pdf($dirty);
				break;		
				
			case 'pdf_more':
				$clean = $this->clean_pdf_more($dirty);
				break;		

			default:
				// currently only order notes comes through here
				$clean = str_replace('M2E Pro Notes:','',$dirty);
				break;
		}	
		return $clean;
	}
	public function getVariable($variable){
		$result = '';
		$variable = trim($variable);
		switch ($variable) {
			case "{{store_name}}":
				$result = Mage::getStoreConfig("general/store_information/name");
				break;
			case '{{store_vat_number}}':
				$result = Mage::getStoreConfig("general/store_information/merchant_vat_number");
				break;
			case '{{store_return_address}}':
				$result = Mage::getStoreConfig("general/store_information/address");
				$result = explode("\n", $result);
				break;
			case '{{store_email}}':
				$result = Mage::getStoreConfig("trans_email/ident_general/email");
				break;
			case '{{store_phone}}':
				$result = Mage::getStoreConfig("general/store_information/phone");
				break;
			default:
				$result = $variable;
				break;
		}
		return $result;
	}
	public function setLocale($store_id, $date_format){
		$default_locale = mb_substr(Mage::app()->getStore($store_id)->getConfig('general/locale/code'),0,2);
		$date_format_strftime = false;
		if(strpos($date_format, '%') !== false)
		{ 
			$date_format_strftime = true;
			switch ($default_locale) {
				case 'en':
					setlocale(LC_TIME, 'en_US.UTF-8');
					break;
				case 'fr':
					setlocale(LC_TIME, 'fr_FR.UTF-8');
					break;
				default:
					setlocale(LC_TIME, 'en_US.UTF-8');
					break;	
			}
		}
		return $date_format_strftime;
	}	
	public function createOrderDateByFormat($order, $date_format_strftime, $date_format){
		$order_date = 'n/a';
		if ($order->getCreatedAtStoreDate()) {

			//$dated                = $order->getCreatedAtStoreDate();
			$dated = $order->getCreatedAt();
			$dated_timestamp = strtotime($dated);

			if ($dated != '') // eg Packing Sheet from Invoices page = no date
			{
				$dated_timestamp = strtotime($dated);
				//$order_date       = date($date_format,$dated_timestamp);
				if ($dated_timestamp != false) {
					if($date_format_strftime !== true) $order_date = Mage::getModel('core/date')->date($date_format, $dated_timestamp);
					else { $order_date = strftime($date_format, $dated_timestamp); }
				} else {
					$locale_timestamp = Mage::getModel('core/date')->timestamp(strtotime($order->getCreatedAt()));
					if ($locale_timestamp != false) 
					{
						if($date_format_strftime !== true) $order_date = Mage::getModel('core/date')->date($date_format, $locale_timestamp);
						else { $order_date = strftime($date_format, $locale_timestamp); }
					}
				}
			}
		}
		return $order_date ;
	}
	public function createShipmentDateByFormat($order, $date_format_strftime, $date_format){
		$shipment_date_title = '';
		$shipment_date = 'n/a';
		$_shipments_title = $order->getShipmentsCollection();

		foreach ($_shipments_title as $_shipment_title) {
			$dated_shipment_title = $_shipment_title->getCreatedAt();
			if ($dated_shipment_title != '') // eg Packing Sheet from Invoices page = no date
			{
				if ($dated_shipment_title != false) {
					if($date_format_strftime !== true) $shipment_date = Mage::getModel('core/date')->date($date_format, $dated_shipment_title);
					else { $shipment_date = strftime($date_format, $dated_shipment_title); }
				} else {
					$locale_timestamp = Mage::getModel('core/date')->timestamp(strtotime($dated_shipment_title));
					if ($locale_timestamp != false) 
					{
						if($date_format_strftime !== true) $shipment_date = Mage::getModel('core/date')->date($date_format, $locale_timestamp);
						else { $shipment_date = strftime($date_format, $locale_timestamp); }
					}
				}
			}
			break;
		}
		return $shipment_date;
	}
	public function createInvoiceDateByFormat($order, $date_format_strftime, $date_format){
		$invoice_date = 'n/a';
		$_invoices_title = $order->getInvoiceCollection();
		foreach ($_invoices_title as $_invoice_title) {
			$invoiceIncrementId_title = $_invoice_title->getIncrementId();
			$invoice_title_2 = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceIncrementId_title);
			$dated_invoice_title = $invoice_title_2->getCreatedAt();
			if ($dated_invoice_title != '') // eg Packing Sheet from Invoices page = no date
			{
				if ($dated_invoice_title != false) {
					if($date_format_strftime !== true) $invoice_date = Mage::getModel('core/date')->date($date_format, $dated_invoice_title);
					else { $invoice_date = strftime($date_format, $dated_invoice_title); }
				} else {
					$locale_timestamp = Mage::getModel('core/date')->timestamp(strtotime($dated_invoice_title));
					if ($locale_timestamp != false) 
					{
						if($date_format_strftime !== true) $invoice_date = Mage::getModel('core/date')->date($date_format, $locale_timestamp);
						else { $invoice_date = strftime($date_format, $locale_timestamp); }
					}
				}
			}
			break;
		}
		return $invoice_date;
	}

    // This function is fix for ucwords() which is didn't work with non-romance character like sweden
    public function ucwords_specific ($string, $delimiters = '', $encoding = NULL)
    {
        if ($encoding === NULL) { $encoding = mb_internal_encoding();}

        if (is_string($delimiters))
        {
            $delimiters =  str_split( str_replace(' ', '', $delimiters));
        }

        $delimiters_pattern1 = array();
        $delimiters_replace1 = array();
        $delimiters_pattern2 = array();
        $delimiters_replace2 = array();
        foreach ($delimiters as $delimiter)
        {
            $uniqid = uniqid();
            $delimiters_pattern1[]   = '/'. preg_quote($delimiter) .'/';
            $delimiters_replace1[]   = $delimiter.$uniqid.' ';
            $delimiters_pattern2[]   = '/'. preg_quote($delimiter.$uniqid.' ') .'/';
            $delimiters_replace2[]   = $delimiter;
        }

        // $return_string = mb_strtolower($string, $encoding);
        $return_string = $string;
        $return_string = preg_replace($delimiters_pattern1, $delimiters_replace1, $return_string);

        $words = explode(' ', $return_string);

        foreach ($words as $index => $word)
        {
            $words[$index] = mb_strtoupper(mb_substr($word, 0, 1, $encoding), $encoding).mb_substr($word, 1, mb_strlen($word, $encoding), $encoding);
        }

        $return_string = implode(' ', $words);

        $return_string = preg_replace($delimiters_pattern2, $delimiters_replace2, $return_string);

        return $return_string;
    }
}
