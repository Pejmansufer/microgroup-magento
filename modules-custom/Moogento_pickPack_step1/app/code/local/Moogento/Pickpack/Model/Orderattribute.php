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
* File        Shippingbackground.php
* @category   Moogento
* @package    pickPack
* @copyright  Copyright (c) 2014 Moogento <info@moogento.com> / All rights reserved.
* @license    https://www.moogento.com/License.html
*/ 


class Moogento_Pickpack_Model_Orderattribute
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $return_array = array();

        $return_array[] = array('value' => 'shipping_method', 'label' => Mage::helper('pickpack')->__('Shipping Method (Default)'));

        if(Mage::helper('pickpack')->isInstalled("Moogento_CourierRules")){
            $return_array[] = array('value' => 'custom_attribute', 'label' => Mage::helper('pickpack')->__('Custom Order Attribute'));
            $return_array[] = array('value' => 'custom_courierrules_method', 'label' => Mage::helper('pickpack')->__('courierRules Shipping Method'));
        }

        return $return_array;
    }

}
