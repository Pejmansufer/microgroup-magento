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
* File        Status.php
* @category   Moogento
* @package    pickPack
* @copyright  Copyright (c) 2014 Moogento <info@moogento.com> / All rights reserved.
* @license    https://www.moogento.com/License.html
*/ 


class Moogento_Pickpack_Model_Adminhtml_System_Config_Source_Condition_Productattribute
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'no', 'label' => Mage::helper('pickpack')->__("No (default)")),
            array('value' => 'yes_current_date', 'label' => Mage::helper('pickpack')->__("Yes (Custom product attribute containing today's date)")),
//             array('value' => 'yes_specific_date', 'label' => Mage::helper('pickpack')->__('Yes (item custom attribute with specific date)')),
            array('value' => 'product_attribute', 'label' => Mage::helper('pickpack')->__("Yes (Custom product attribute containing specific text)")),
        );
    }
}
